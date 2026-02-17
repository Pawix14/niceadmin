<?php
/**
 * EXAMPLE USAGE OF BOOKING VALIDATION SYSTEM
 * 
 * This file demonstrates how to implement the validation system
 * in your car rental booking process
 */

// Include the validation helper
require_once 'config/booking_validation.php';
require_once 'config/database.php';

// Initialize validator
$validator = new BookingValidator($conn);

// ============================================
// EXAMPLE 1: Validate a New Booking
// ============================================

if (isset($_POST['submit_booking'])) {
    $booking_data = [
        'car_id' => $_POST['car_id'],
        'customer_id' => $_SESSION['customer_id'],
        'pickup_date' => $_POST['pickup_date'],
        'return_date' => $_POST['return_date'],
        'total_amount' => $_POST['total_amount'],
        'promo_code' => $_POST['promo_code'] ?? null
    ];
    
    // Run comprehensive validation
    $validation = $validator->validateNewBooking($booking_data);
    
    if (!$validation['valid']) {
        // Show errors to user
        foreach ($validation['errors'] as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    } else {
        // Proceed with booking
        $days = $validation['days'];
        
        // Calculate final price with promo code if applicable
        $final_amount = $booking_data['total_amount'];
        
        if (!empty($booking_data['promo_code'])) {
            $promo_check = $validator->validatePromoCode(
                $booking_data['promo_code'], 
                $booking_data['total_amount'], 
                $booking_data['customer_id']
            );
            
            if ($promo_check['valid']) {
                $discount = $validator->calculateDiscount($promo_check['promo'], $booking_data['total_amount']);
                $final_amount -= $discount;
            }
        }
        
        // Insert booking into database
        $stmt = $conn->prepare("INSERT INTO car_rentals 
            (customer_id, car_model, pickup_date, return_date, rental_days, total_amount, promo_code, status) 
            VALUES (?, (SELECT name FROM cars WHERE id = ?), ?, ?, ?, ?, ?, 'Confirmed')");
        
        $stmt->bind_param("iissids", 
            $booking_data['customer_id'],
            $booking_data['car_id'],
            $booking_data['pickup_date'],
            $booking_data['return_date'],
            $days,
            $final_amount,
            $booking_data['promo_code']
        );
        
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Booking confirmed successfully!</div>";
        }
    }
}

// ============================================
// EXAMPLE 2: Check Car Availability (AJAX)
// ============================================

if (isset($_GET['check_availability'])) {
    header('Content-Type: application/json');
    
    $car_id = $_GET['car_id'];
    $pickup_date = $_GET['pickup_date'];
    $return_date = $_GET['return_date'];
    
    $is_available = $validator->isCarAvailable($car_id, $pickup_date, $return_date);
    
    echo json_encode([
        'available' => $is_available,
        'message' => $is_available ? 'Car is available' : 'Car is already booked for these dates'
    ]);
    exit;
}

// ============================================
// EXAMPLE 3: Modify Booking
// ============================================

if (isset($_POST['modify_booking'])) {
    $booking_id = $_POST['booking_id'];
    $new_return_date = $_POST['new_return_date'];
    
    // Check if modification is allowed
    $can_modify = $validator->canModifyBooking($booking_id);
    
    if (!$can_modify['can_modify']) {
        echo "<div class='alert alert-danger'>{$can_modify['message']}</div>";
    } else {
        // Get booking details
        $stmt = $conn->prepare("SELECT * FROM car_rentals WHERE id = ?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $booking = $stmt->get_result()->fetch_assoc();
        
        // Validate new dates
        $date_check = $validator->validateDateOrder($booking['pickup_date'], $new_return_date);
        
        if (!$date_check['valid']) {
            echo "<div class='alert alert-danger'>{$date_check['message']}</div>";
        } else {
            // Check car availability for new dates
            $stmt = $conn->prepare("SELECT id FROM cars WHERE name = ?");
            $stmt->bind_param("s", $booking['car_model']);
            $stmt->execute();
            $car = $stmt->get_result()->fetch_assoc();
            
            if (!$validator->isCarAvailable($car['id'], $booking['pickup_date'], $new_return_date, $booking_id)) {
                echo "<div class='alert alert-danger'>Car is not available for the new dates</div>";
            } else {
                // Update booking
                $stmt = $conn->prepare("UPDATE car_rentals SET return_date = ? WHERE id = ?");
                $stmt->bind_param("si", $new_return_date, $booking_id);
                $stmt->execute();
                
                echo "<div class='alert alert-success'>Booking modified successfully!</div>";
            }
        }
    }
}

// ============================================
// EXAMPLE 4: Cancel Booking
// ============================================

if (isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];
    
    $can_cancel = $validator->canCancelBooking($booking_id);
    
    if (!$can_cancel['can_cancel']) {
        echo "<div class='alert alert-danger'>{$can_cancel['message']}</div>";
    } else {
        // Show cancellation details
        echo "<div class='alert alert-warning'>";
        echo "Cancellation Fee: ₱" . number_format($can_cancel['cancellation_fee'], 2) . "<br>";
        echo "Refund Amount: ₱" . number_format($can_cancel['refund_amount'], 2) . "<br>";
        echo "Hours before pickup: " . round($can_cancel['hours_before_pickup'], 1) . " hours";
        echo "</div>";
        
        // Confirm cancellation
        if (isset($_POST['confirm_cancel'])) {
            $stmt = $conn->prepare("UPDATE car_rentals 
                SET status = 'Cancelled', 
                    cancellation_fee = ?,
                    refund_amount = ?,
                    cancelled_at = NOW() 
                WHERE id = ?");
            
            $stmt->bind_param("ddi", 
                $can_cancel['cancellation_fee'],
                $can_cancel['refund_amount'],
                $booking_id
            );
            
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Booking cancelled. Refund will be processed.</div>";
            }
        }
    }
}

// ============================================
// EXAMPLE 5: Extend Booking
// ============================================

if (isset($_POST['extend_booking'])) {
    $booking_id = $_POST['booking_id'];
    $new_return_date = $_POST['new_return_date'];
    
    $can_extend = $validator->canExtendBooking($booking_id, $new_return_date);
    
    if (!$can_extend['can_extend']) {
        echo "<div class='alert alert-danger'>{$can_extend['message']}</div>";
    } else {
        // Calculate additional charges
        $stmt = $conn->prepare("SELECT * FROM car_rentals WHERE id = ?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $booking = $stmt->get_result()->fetch_assoc();
        
        $original_days = (new DateTime($booking['return_date']))->diff(new DateTime($booking['pickup_date']))->days;
        $new_days = (new DateTime($new_return_date))->diff(new DateTime($booking['pickup_date']))->days;
        $additional_days = $new_days - $original_days;
        
        $daily_rate = $booking['total_amount'] / $original_days;
        $additional_charge = $daily_rate * $additional_days;
        
        echo "<div class='alert alert-info'>";
        echo "Additional Days: $additional_days<br>";
        echo "Additional Charge: ₱" . number_format($additional_charge, 2);
        echo "</div>";
        
        // Confirm extension
        if (isset($_POST['confirm_extend'])) {
            $new_total = $booking['total_amount'] + $additional_charge;
            
            $stmt = $conn->prepare("UPDATE car_rentals 
                SET return_date = ?, 
                    rental_days = ?,
                    total_amount = ? 
                WHERE id = ?");
            
            $stmt->bind_param("sidi", $new_return_date, $new_days, $new_total, $booking_id);
            
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Booking extended successfully!</div>";
            }
        }
    }
}

// ============================================
// EXAMPLE 6: Real-time Validation (JavaScript)
// ============================================
?>

<script>
// Real-time car availability check
function checkCarAvailability() {
    const carId = document.getElementById('car_id').value;
    const pickupDate = document.getElementById('pickup_date').value;
    const returnDate = document.getElementById('return_date').value;
    
    if (carId && pickupDate && returnDate) {
        fetch(`?check_availability&car_id=${carId}&pickup_date=${pickupDate}&return_date=${returnDate}`)
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('availability_message');
                if (data.available) {
                    messageDiv.className = 'alert alert-success';
                    messageDiv.textContent = '✓ ' + data.message;
                } else {
                    messageDiv.className = 'alert alert-danger';
                    messageDiv.textContent = '✗ ' + data.message;
                }
                messageDiv.style.display = 'block';
            });
    }
}

// Attach to date inputs
document.getElementById('pickup_date').addEventListener('change', checkCarAvailability);
document.getElementById('return_date').addEventListener('change', checkCarAvailability);
</script>

<?php
// ============================================
// EXAMPLE 7: Admin Override (Optional)
// ============================================

if (isset($_POST['admin_override']) && $_SESSION['role'] == 'admin') {
    // Admins can bypass certain validations
    // But still log the override for audit purposes
    
    $booking_data = [
        'car_id' => $_POST['car_id'],
        'customer_id' => $_POST['customer_id'],
        'pickup_date' => $_POST['pickup_date'],
        'return_date' => $_POST['return_date'],
        'total_amount' => $_POST['total_amount'],
        'override_reason' => $_POST['override_reason']
    ];
    
    // Log the override
    $stmt = $conn->prepare("INSERT INTO booking_overrides 
        (admin_id, booking_data, reason, created_at) 
        VALUES (?, ?, ?, NOW())");
    
    $booking_json = json_encode($booking_data);
    $stmt->bind_param("iss", 
        $_SESSION['admin_id'],
        $booking_json,
        $booking_data['override_reason']
    );
    
    $stmt->execute();
    
    echo "<div class='alert alert-warning'>Booking created with admin override</div>";
}
?>
