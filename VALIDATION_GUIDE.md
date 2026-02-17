# Car Rental Booking Validation System

## Complete Implementation Guide

### Overview
This validation system ensures data integrity, prevents double bookings, and enforces business rules for your car rental system.

---

## 1. CRITICAL VALIDATIONS (Prevent Double Booking)

### 1.1 Car Availability Check
**Purpose:** Prevent multiple customers from booking the same car for overlapping dates

**Function:** `isCarAvailable($car_id, $pickup_date, $return_date, $exclude_booking_id = null)`

**Usage:**
```php
if (!$validator->isCarAvailable($car_id, $pickup_date, $return_date)) {
    echo "Car is already booked for these dates";
}
```

**Business Logic:**
- Checks for date overlaps in existing bookings
- Only considers bookings with status: Confirmed, Active, Pending
- Can exclude a specific booking (useful for modifications)

---

### 1.2 Car Status Validation
**Purpose:** Only allow booking of active cars

**Function:** `isCarActive($car_id)`

**Usage:**
```php
if (!$validator->isCarActive($car_id)) {
    echo "This car is not available for booking";
}
```

---

### 1.3 Customer Age Validation
**Purpose:** Ensure customer meets minimum age requirement (18+)

**Function:** `isCustomerAgeValid($customer_id, $min_age = 18)`

**Usage:**
```php
$age_check = $validator->isCustomerAgeValid($customer_id);
if (!$age_check['valid']) {
    echo $age_check['message'];
}
```

**Requirements:**
- Customer must have birthdate in database
- Default minimum age: 18 years
- Can be adjusted for luxury cars (21+)

---

### 1.4 Customer Account Status
**Purpose:** Verify customer account is active and verified

**Function:** `isCustomerAccountValid($customer_id)`

**Usage:**
```php
$account_check = $validator->isCustomerAccountValid($customer_id);
if (!$account_check['valid']) {
    echo $account_check['message'];
}
```

---

### 1.5 Outstanding Balance Check
**Purpose:** Prevent bookings if customer has unpaid invoices

**Function:** `hasOutstandingBalance($customer_id)`

**Usage:**
```php
$balance_check = $validator->hasOutstandingBalance($customer_id);
if ($balance_check['has_balance']) {
    echo "You have unpaid balance: ₱" . number_format($balance_check['amount'], 2);
}
```

---

### 1.6 Active Booking Limit
**Purpose:** Limit maximum concurrent active bookings per customer

**Function:** `checkActiveBookingLimit($customer_id, $max_bookings = 3)`

**Usage:**
```php
$limit_check = $validator->checkActiveBookingLimit($customer_id);
if (!$limit_check['valid']) {
    echo $limit_check['message'];
}
```

---

## 2. DATE & TIME VALIDATIONS

### 2.1 Future Date Validation
**Purpose:** Prevent booking dates in the past

**Function:** `validateFutureDates($pickup_date)`

---

### 2.2 Date Order Validation
**Purpose:** Ensure return date is after pickup date

**Function:** `validateDateOrder($pickup_date, $return_date)`

---

### 2.3 Minimum Rental Days
**Purpose:** Enforce minimum rental period (default: 1 day)

**Function:** `validateMinimumDays($pickup_date, $return_date, $min_days = 1)`

---

### 2.4 Maximum Rental Days
**Purpose:** Limit maximum rental period (default: 30 days)

**Function:** `validateMaximumDays($pickup_date, $return_date, $max_days = 30)`

---

### 2.5 Advance Booking Limit
**Purpose:** Prevent bookings too far in advance (default: 90 days)

**Function:** `validateAdvanceBooking($pickup_date, $max_advance_days = 90)`

---

### 2.6 Business Hours Check
**Purpose:** Ensure pickup/return during business hours (8 AM - 6 PM)

**Function:** `validateBusinessHours($datetime)`

---

## 3. PAYMENT VALIDATIONS

### 3.1 Promo Code Validation
**Purpose:** Validate promo code eligibility and restrictions

**Function:** `validatePromoCode($code, $total_amount, $customer_id = null)`

**Checks:**
- Code is active and not expired
- Minimum order amount requirement
- Usage limit not exceeded
- First-time customer requirement (if applicable)

**Usage:**
```php
$promo_check = $validator->validatePromoCode($code, $total_amount, $customer_id);
if ($promo_check['valid']) {
    $discount = $validator->calculateDiscount($promo_check['promo'], $total_amount);
}
```

---

### 3.2 Discount Calculation
**Purpose:** Calculate discount amount with maximum cap

**Function:** `calculateDiscount($promo, $total_amount)`

**Supports:**
- Percentage discounts
- Fixed amount discounts
- Maximum discount caps

---

## 4. MODIFICATION & CANCELLATION VALIDATIONS

### 4.1 Modification Deadline
**Purpose:** Allow modifications only 24+ hours before pickup

**Function:** `canModifyBooking($booking_id, $hours_before = 24)`

**Usage:**
```php
$can_modify = $validator->canModifyBooking($booking_id);
if ($can_modify['can_modify']) {
    // Allow modification
} else {
    echo $can_modify['message'];
}
```

---

### 4.2 Cancellation Policy
**Purpose:** Calculate cancellation fees based on timing

**Function:** `canCancelBooking($booking_id)`

**Fee Structure:**
- Less than 24 hours: 50% cancellation fee
- 24-48 hours: 25% cancellation fee
- More than 48 hours: No fee

**Usage:**
```php
$can_cancel = $validator->canCancelBooking($booking_id);
if ($can_cancel['can_cancel']) {
    echo "Cancellation Fee: ₱" . number_format($can_cancel['cancellation_fee'], 2);
    echo "Refund: ₱" . number_format($can_cancel['refund_amount'], 2);
}
```

---

### 4.3 Booking Extension
**Purpose:** Validate and process booking extensions

**Function:** `canExtendBooking($booking_id, $new_return_date)`

**Checks:**
- Booking is currently active
- Car is available for extended period
- No conflicting bookings

---

## 5. COMPREHENSIVE VALIDATION

### 5.1 All-in-One Validation
**Purpose:** Run all validations for a new booking

**Function:** `validateNewBooking($data)`

**Usage:**
```php
$booking_data = [
    'car_id' => $car_id,
    'customer_id' => $customer_id,
    'pickup_date' => $pickup_date,
    'return_date' => $return_date,
    'total_amount' => $total_amount,
    'promo_code' => $promo_code
];

$validation = $validator->validateNewBooking($booking_data);

if (!$validation['valid']) {
    foreach ($validation['errors'] as $error) {
        echo "<div class='alert alert-danger'>$error</div>";
    }
} else {
    // Proceed with booking
    $rental_days = $validation['days'];
}
```

---

## 6. DATABASE REQUIREMENTS

### Required Tables & Columns

**customers table:**
- id
- birthdate (for age validation)
- status (Active/Inactive)
- account_verified

**cars table:**
- id
- name
- status (Active/Inactive/Rented)

**car_rentals table:**
- id
- customer_id
- car_model
- pickup_date
- return_date
- rental_days
- total_amount
- promo_code
- status (Pending/Confirmed/Active/Completed/Cancelled)
- payment_status (Paid/Unpaid)
- cancellation_fee
- refund_amount
- cancelled_at

**promo_codes table:**
- code
- status (Active/Inactive)
- discount_type (percentage/fixed)
- discount_value
- max_discount_amount
- min_order_amount
- usage_limit
- for_first_time_only
- valid_from
- valid_until

---

## 7. IMPLEMENTATION CHECKLIST

### Step 1: Setup
- [ ] Create `config/booking_validation.php`
- [ ] Include in your booking pages
- [ ] Initialize validator with database connection

### Step 2: New Bookings
- [ ] Add validation before inserting booking
- [ ] Display validation errors to user
- [ ] Log validation failures for analysis

### Step 3: Modifications
- [ ] Check modification deadline
- [ ] Validate new dates
- [ ] Check car availability for new dates

### Step 4: Cancellations
- [ ] Calculate cancellation fees
- [ ] Show refund amount
- [ ] Update booking status
- [ ] Process refund

### Step 5: Real-time Checks
- [ ] Add AJAX availability check
- [ ] Show availability calendar
- [ ] Disable unavailable dates

---

## 8. AJAX IMPLEMENTATION

### Real-time Availability Check
```javascript
function checkAvailability(carId, pickupDate, returnDate) {
    fetch(`?check_availability&car_id=${carId}&pickup_date=${pickupDate}&return_date=${returnDate}`)
        .then(response => response.json())
        .then(data => {
            if (data.available) {
                showSuccess('Car is available!');
            } else {
                showError('Car is already booked');
            }
        });
}
```

---

## 9. ERROR HANDLING

### User-Friendly Messages
```php
$error_messages = [
    'car_unavailable' => 'Sorry, this car is already booked for your selected dates.',
    'past_date' => 'Please select a future date for your booking.',
    'age_requirement' => 'You must be at least 18 years old to rent a car.',
    'outstanding_balance' => 'Please clear your outstanding balance before making a new booking.',
    'booking_limit' => 'You have reached the maximum number of active bookings.',
];
```

---

## 10. TESTING SCENARIOS

### Test Cases
1. **Double Booking Prevention**
   - Try booking same car for overlapping dates
   - Expected: Error message

2. **Past Date Prevention**
   - Try booking with past pickup date
   - Expected: Error message

3. **Age Validation**
   - Customer under 18 tries to book
   - Expected: Error message

4. **Promo Code**
   - Use expired promo code
   - Use first-time promo as returning customer
   - Expected: Error messages

5. **Cancellation Fees**
   - Cancel 1 hour before pickup (50% fee)
   - Cancel 3 days before pickup (no fee)
   - Expected: Correct fee calculation

---

## 11. SECURITY CONSIDERATIONS

- Always use prepared statements
- Sanitize all user inputs
- Validate on both client and server side
- Log all validation failures
- Implement rate limiting for booking attempts
- Use CSRF tokens on forms

---

## 12. PERFORMANCE OPTIMIZATION

- Cache car availability for popular dates
- Index database columns (pickup_date, return_date, status)
- Use database transactions for bookings
- Implement queue system for high traffic

---

## Support & Customization

All validation parameters can be customized:
- Minimum/maximum rental days
- Advance booking limit
- Business hours
- Cancellation fee structure
- Active booking limit

Adjust these values in the validation functions to match your business requirements.
