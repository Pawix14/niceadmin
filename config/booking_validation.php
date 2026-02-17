<?php
// Booking Validation Helper Functions

class BookingValidator {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    // ============ CRITICAL VALIDATIONS ============
    
    /**
     * 1. Check if car is available for the requested dates
     * Prevents double booking
     */
    public function isCarAvailable($car_id, $pickup_date, $return_date, $exclude_booking_id = null) {
        $sql = "SELECT COUNT(*) as count FROM car_rentals 
                WHERE car_model = (SELECT name FROM cars WHERE id = ?) 
                AND status IN ('Confirmed', 'Active', 'Pending')
                AND (
                    (pickup_date <= ? AND return_date >= ?) OR
                    (pickup_date <= ? AND return_date >= ?) OR
                    (pickup_date >= ? AND return_date <= ?)
                )";
        
        if ($exclude_booking_id) {
            $sql .= " AND id != ?";
        }
        
        $stmt = $this->conn->prepare($sql);
        if ($exclude_booking_id) {
            $stmt->bind_param("isssssi", $car_id, $return_date, $pickup_date, $pickup_date, $pickup_date, $pickup_date, $return_date, $exclude_booking_id);
        } else {
            $stmt->bind_param("isssss", $car_id, $return_date, $pickup_date, $pickup_date, $pickup_date, $pickup_date, $return_date);
        }
        
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] == 0;
    }
    
    /**
     * 2. Check if car status is Active
     */
    public function isCarActive($car_id) {
        $stmt = $this->conn->prepare("SELECT status FROM cars WHERE id = ?");
        $stmt->bind_param("i", $car_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result && $result['status'] == 'Active';
    }
    
    /**
     * 3. Check customer age requirement (18+)
     */
    public function isCustomerAgeValid($customer_id, $min_age = 18) {
        $stmt = $this->conn->prepare("SELECT birthdate FROM customers WHERE id = ?");
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if (!$result || !$result['birthdate']) {
            return ['valid' => false, 'message' => 'Birthdate not found'];
        }
        
        $age = date_diff(date_create($result['birthdate']), date_create('today'))->y;
        
        if ($age < $min_age) {
            return ['valid' => false, 'message' => "You must be at least $min_age years old to rent a car"];
        }
        
        return ['valid' => true];
    }
    
    /**
     * 4. Check customer account status
     */
    public function isCustomerAccountValid($customer_id) {
        $stmt = $this->conn->prepare("SELECT status, account_verified FROM customers WHERE id = ?");
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if (!$result) {
            return ['valid' => false, 'message' => 'Customer account not found'];
        }
        
        if ($result['status'] != 'Active') {
            return ['valid' => false, 'message' => 'Your account is not active. Please contact support.'];
        }
        
        return ['valid' => true];
    }
    
    /**
     * 5. Check for outstanding balance
     */
    public function hasOutstandingBalance($customer_id) {
        $stmt = $this->conn->prepare("SELECT SUM(total_amount) as unpaid FROM car_rentals 
                                      WHERE customer_id = ? AND payment_status = 'Unpaid'");
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        $unpaid = $result['unpaid'] ?? 0;
        
        if ($unpaid > 0) {
            return ['has_balance' => true, 'amount' => $unpaid, 
                    'message' => 'You have an outstanding balance of ₱' . number_format($unpaid, 2)];
        }
        
        return ['has_balance' => false];
    }
    
    /**
     * 6. Check active booking limit (max 3 active bookings)
     */
    public function checkActiveBookingLimit($customer_id, $max_bookings = 3) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM car_rentals 
                                      WHERE customer_id = ? AND status IN ('Confirmed', 'Active')");
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result['count'] >= $max_bookings) {
            return ['valid' => false, 'message' => "You have reached the maximum of $max_bookings active bookings"];
        }
        
        return ['valid' => true];
    }
    
    // ============ DATE & TIME VALIDATIONS ============
    
    /**
     * 7. Validate dates are not in the past
     */
    public function validateFutureDates($pickup_date) {
        $now = new DateTime();
        $pickup = new DateTime($pickup_date);
        
        if ($pickup < $now) {
            return ['valid' => false, 'message' => 'Pickup date cannot be in the past'];
        }
        
        return ['valid' => true];
    }
    
    /**
     * 8. Validate return date is after pickup date
     */
    public function validateDateOrder($pickup_date, $return_date) {
        $pickup = new DateTime($pickup_date);
        $return = new DateTime($return_date);
        
        if ($return <= $pickup) {
            return ['valid' => false, 'message' => 'Return date must be after pickup date'];
        }
        
        return ['valid' => true];
    }
    
    /**
     * 9. Validate minimum rental days (1 day)
     */
    public function validateMinimumDays($pickup_date, $return_date, $min_days = 1) {
        $pickup = new DateTime($pickup_date);
        $return = new DateTime($return_date);
        $days = $pickup->diff($return)->days;
        
        if ($days < $min_days) {
            return ['valid' => false, 'message' => "Minimum rental period is $min_days day(s)"];
        }
        
        return ['valid' => true, 'days' => $days];
    }
    
    /**
     * 10. Validate maximum rental days (30 days)
     */
    public function validateMaximumDays($pickup_date, $return_date, $max_days = 30) {
        $pickup = new DateTime($pickup_date);
        $return = new DateTime($return_date);
        $days = $pickup->diff($return)->days;
        
        if ($days > $max_days) {
            return ['valid' => false, 'message' => "Maximum rental period is $max_days days"];
        }
        
        return ['valid' => true, 'days' => $days];
    }
    
    /**
     * 11. Validate advance booking limit (max 90 days in advance)
     */
    public function validateAdvanceBooking($pickup_date, $max_advance_days = 90) {
        $now = new DateTime();
        $pickup = new DateTime($pickup_date);
        $days_advance = $now->diff($pickup)->days;
        
        if ($days_advance > $max_advance_days) {
            return ['valid' => false, 'message' => "You can only book up to $max_advance_days days in advance"];
        }
        
        return ['valid' => true];
    }
    
    /**
     * 12. Check business hours (8 AM - 6 PM)
     */
    public function validateBusinessHours($datetime) {
        $time = new DateTime($datetime);
        $hour = (int)$time->format('H');
        
        if ($hour < 8 || $hour >= 18) {
            return ['valid' => false, 'message' => 'Pickup/return must be between 8:00 AM and 6:00 PM'];
        }
        
        return ['valid' => true];
    }
    
    // ============ PAYMENT VALIDATIONS ============
    
    /**
     * 13. Validate promo code
     */
    public function validatePromoCode($code, $total_amount, $customer_id = null) {
        $today = date('Y-m-d');
        $stmt = $this->conn->prepare("SELECT * FROM promo_codes 
                                      WHERE code = ? AND status = 'Active' 
                                      AND valid_from <= ? AND valid_until >= ?");
        $stmt->bind_param("sss", $code, $today, $today);
        $stmt->execute();
        $promo = $stmt->get_result()->fetch_assoc();
        
        if (!$promo) {
            return ['valid' => false, 'message' => 'Invalid or expired promo code'];
        }
        
        // Check minimum order amount
        if ($promo['min_order_amount'] && $total_amount < $promo['min_order_amount']) {
            return ['valid' => false, 'message' => 'Minimum order amount of ₱' . number_format($promo['min_order_amount'], 2) . ' required'];
        }
        
        // Check usage limit
        if ($promo['usage_limit']) {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as used FROM car_rentals WHERE promo_code = ?");
            $stmt->bind_param("s", $code);
            $stmt->execute();
            $usage = $stmt->get_result()->fetch_assoc();
            
            if ($usage['used'] >= $promo['usage_limit']) {
                return ['valid' => false, 'message' => 'Promo code usage limit reached'];
            }
        }
        
        // Check first-time customer requirement
        if ($promo['for_first_time_only'] && $customer_id) {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as bookings FROM car_rentals WHERE customer_id = ?");
            $stmt->bind_param("i", $customer_id);
            $stmt->execute();
            $bookings = $stmt->get_result()->fetch_assoc();
            
            if ($bookings['bookings'] > 0) {
                return ['valid' => false, 'message' => 'This promo code is for first-time customers only'];
            }
        }
        
        return ['valid' => true, 'promo' => $promo];
    }
    
    /**
     * 14. Calculate discount
     */
    public function calculateDiscount($promo, $total_amount) {
        if ($promo['discount_type'] == 'percentage') {
            $discount = ($total_amount * $promo['discount_value']) / 100;
            if ($promo['max_discount_amount'] && $discount > $promo['max_discount_amount']) {
                $discount = $promo['max_discount_amount'];
            }
        } else {
            $discount = $promo['discount_value'];
        }
        
        return $discount;
    }
    
    // ============ MODIFICATION & CANCELLATION VALIDATIONS ============
    
    /**
     * 15. Check if booking can be modified (24 hours before pickup)
     */
    public function canModifyBooking($booking_id, $hours_before = 24) {
        $stmt = $this->conn->prepare("SELECT pickup_date, status FROM car_rentals WHERE id = ?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $booking = $stmt->get_result()->fetch_assoc();
        
        if (!$booking) {
            return ['can_modify' => false, 'message' => 'Booking not found'];
        }
        
        if (!in_array($booking['status'], ['Confirmed', 'Pending'])) {
            return ['can_modify' => false, 'message' => 'Only confirmed or pending bookings can be modified'];
        }
        
        $now = new DateTime();
        $pickup = new DateTime($booking['pickup_date']);
        $hours_diff = ($pickup->getTimestamp() - $now->getTimestamp()) / 3600;
        
        if ($hours_diff < $hours_before) {
            return ['can_modify' => false, 'message' => "Modifications must be made at least $hours_before hours before pickup"];
        }
        
        return ['can_modify' => true];
    }
    
    /**
     * 16. Check if booking can be cancelled
     */
    public function canCancelBooking($booking_id) {
        $stmt = $this->conn->prepare("SELECT pickup_date, status, total_amount FROM car_rentals WHERE id = ?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $booking = $stmt->get_result()->fetch_assoc();
        
        if (!$booking) {
            return ['can_cancel' => false, 'message' => 'Booking not found'];
        }
        
        if ($booking['status'] == 'Cancelled') {
            return ['can_cancel' => false, 'message' => 'Booking is already cancelled'];
        }
        
        if ($booking['status'] == 'Completed') {
            return ['can_cancel' => false, 'message' => 'Completed bookings cannot be cancelled'];
        }
        
        $now = new DateTime();
        $pickup = new DateTime($booking['pickup_date']);
        $hours_diff = ($pickup->getTimestamp() - $now->getTimestamp()) / 3600;
        
        // Calculate cancellation fee
        $cancellation_fee = 0;
        if ($hours_diff < 24) {
            $cancellation_fee = $booking['total_amount'] * 0.5; // 50% fee
        } elseif ($hours_diff < 48) {
            $cancellation_fee = $booking['total_amount'] * 0.25; // 25% fee
        }
        
        $refund = $booking['total_amount'] - $cancellation_fee;
        
        return [
            'can_cancel' => true, 
            'cancellation_fee' => $cancellation_fee,
            'refund_amount' => $refund,
            'hours_before_pickup' => $hours_diff
        ];
    }
    
    /**
     * 17. Validate booking extension
     */
    public function canExtendBooking($booking_id, $new_return_date) {
        $stmt = $this->conn->prepare("SELECT * FROM car_rentals WHERE id = ?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $booking = $stmt->get_result()->fetch_assoc();
        
        if (!$booking) {
            return ['can_extend' => false, 'message' => 'Booking not found'];
        }
        
        if ($booking['status'] != 'Active') {
            return ['can_extend' => false, 'message' => 'Only active bookings can be extended'];
        }
        
        // Check if car is available for extended period
        $stmt = $this->conn->prepare("SELECT id FROM cars WHERE name = ?");
        $stmt->bind_param("s", $booking['car_model']);
        $stmt->execute();
        $car = $stmt->get_result()->fetch_assoc();
        
        if (!$this->isCarAvailable($car['id'], $booking['return_date'], $new_return_date, $booking_id)) {
            return ['can_extend' => false, 'message' => 'Car is not available for the extended period'];
        }
        
        return ['can_extend' => true];
    }
    
    // ============ COMPREHENSIVE VALIDATION ============
    
    /**
     * Run all validations for a new booking
     */
    public function validateNewBooking($data) {
        $errors = [];
        
        // Date validations
        $future_check = $this->validateFutureDates($data['pickup_date']);
        if (!$future_check['valid']) $errors[] = $future_check['message'];
        
        $order_check = $this->validateDateOrder($data['pickup_date'], $data['return_date']);
        if (!$order_check['valid']) $errors[] = $order_check['message'];
        
        $min_days = $this->validateMinimumDays($data['pickup_date'], $data['return_date']);
        if (!$min_days['valid']) $errors[] = $min_days['message'];
        
        $max_days = $this->validateMaximumDays($data['pickup_date'], $data['return_date']);
        if (!$max_days['valid']) $errors[] = $max_days['message'];
        
        $advance_check = $this->validateAdvanceBooking($data['pickup_date']);
        if (!$advance_check['valid']) $errors[] = $advance_check['message'];
        
        // Car validations
        if (!$this->isCarActive($data['car_id'])) {
            $errors[] = 'Selected car is not available for booking';
        }
        
        if (!$this->isCarAvailable($data['car_id'], $data['pickup_date'], $data['return_date'])) {
            $errors[] = 'Car is already booked for the selected dates';
        }
        
        // Customer validations
        if (isset($data['customer_id'])) {
            $age_check = $this->isCustomerAgeValid($data['customer_id']);
            if (!$age_check['valid']) $errors[] = $age_check['message'];
            
            $account_check = $this->isCustomerAccountValid($data['customer_id']);
            if (!$account_check['valid']) $errors[] = $account_check['message'];
            
            $balance_check = $this->hasOutstandingBalance($data['customer_id']);
            if ($balance_check['has_balance']) $errors[] = $balance_check['message'];
            
            $limit_check = $this->checkActiveBookingLimit($data['customer_id']);
            if (!$limit_check['valid']) $errors[] = $limit_check['message'];
        }
        
        // Promo code validation
        if (isset($data['promo_code']) && !empty($data['promo_code'])) {
            $promo_check = $this->validatePromoCode($data['promo_code'], $data['total_amount'], $data['customer_id'] ?? null);
            if (!$promo_check['valid']) $errors[] = $promo_check['message'];
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'days' => $min_days['days'] ?? 0
        ];
    }
}
?>
