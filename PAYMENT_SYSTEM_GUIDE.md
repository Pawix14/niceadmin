# Payment System Documentation

## Overview
Professional payment processing system for car rental bookings with admin management, invoice generation, and refund handling.

---

## 📁 Files Created

1. **`config/payment_processor.php`** - Core payment processing class
2. **`modules/payment.php`** - Customer payment page
3. **`modules/payment_success.php`** - Payment confirmation page
4. **`modules/invoice.php`** - Invoice/receipt generator
5. **`modules/admin_payments.php`** - Admin payment dashboard
6. **`payment_system_schema.sql`** - Database schema

---

## 🚀 Features

### Customer Features
- ✅ Multiple payment methods (GCash, PayMaya, Bank Transfer, Credit Card, Cash)
- ✅ Secure payment processing
- ✅ Real-time payment confirmation
- ✅ Professional invoice generation
- ✅ Receipt download & print
- ✅ Payment history tracking

### Admin Features
- ✅ Payment verification system
- ✅ Revenue analytics & statistics
- ✅ Payment method breakdown
- ✅ Refund processing
- ✅ Transaction management
- ✅ Export to Excel
- ✅ Audit trail logging

---

## 💳 Supported Payment Methods

### 1. GCash
- Customer sends payment to business GCash number
- Enters reference number for verification
- Admin verifies payment

### 2. PayMaya
- Similar to GCash process
- Reference number required

### 3. Bank Transfer
- Customer transfers to business bank account
- Provides reference number and sender name
- Admin verification required

### 4. Credit/Debit Card
- Card details collected securely
- Can integrate with payment gateway (Stripe, PayPal, etc.)

### 5. Cash on Pickup
- Customer pays when picking up vehicle
- No upfront payment required

---

## 📊 Database Schema

### Tables Created

#### 1. payments
```sql
- id (Primary Key)
- booking_id (Foreign Key)
- customer_id (Foreign Key)
- amount
- payment_method
- payment_reference (Unique)
- transaction_id
- payment_status (Pending/Completed/Verified/Failed/Refunded)
- payment_date
- payment_details (JSON)
- verified_by (Admin ID)
- verified_at
- refund_amount
```

#### 2. invoices
```sql
- id (Primary Key)
- invoice_number (Unique)
- booking_id (Foreign Key)
- payment_id (Foreign Key)
- invoice_date
- due_date
- subtotal
- tax_amount
- discount_amount
- total_amount
- status (Draft/Sent/Paid/Overdue/Cancelled)
```

#### 3. refunds
```sql
- id (Primary Key)
- payment_id (Foreign Key)
- booking_id (Foreign Key)
- refund_amount
- refund_reason
- refund_reference (Unique)
- refund_status (Pending/Processing/Completed/Failed)
- refund_date
- processed_by (Admin ID)
```

#### 4. payment_logs
```sql
- id (Primary Key)
- payment_id (Foreign Key)
- activity
- user_id
- ip_address
- user_agent
- created_at
```

---

## 🔧 Installation

### Step 1: Run SQL Schema
```bash
mysql -u root -p travel_db_improved < payment_system_schema.sql
```

### Step 2: Configure Payment Methods
Edit `config/payment_processor.php` to update:
- GCash number
- PayMaya number
- Bank account details
- Payment gateway credentials (if using)

### Step 3: Add Navigation Links
Add to your navigation menu:
```php
// For customers
<a href="index.php?page=payment&booking_id=<?php echo $booking_id; ?>">Make Payment</a>

// For admins
<a href="index.php?page=admin_payments">Payment Management</a>
```

---

## 💻 Usage Examples

### Customer Payment Flow

#### 1. Initiate Payment
```php
// Redirect to payment page
header('Location: index.php?page=payment&booking_id=' . $booking_id);
```

#### 2. Process Payment
```php
require_once 'config/payment_processor.php';
$processor = new PaymentProcessor($conn);

$payment_data = [
    'amount' => $total_amount,
    'payment_method' => 'gcash',
    'transaction_id' => 'GCASH123456',
    'account_number' => '09171234567'
];

$result = $processor->processPayment($booking_id, $payment_data);

if ($result['success']) {
    // Redirect to success page
    header('Location: index.php?page=payment_success&ref=' . $result['payment_reference']);
}
```

#### 3. View Invoice
```php
// Direct link to invoice
<a href="index.php?page=invoice&booking_id=<?php echo $booking_id; ?>" target="_blank">
    View Invoice
</a>
```

### Admin Operations

#### 1. Verify Payment
```php
$result = $processor->verifyPayment($payment_id, $admin_id);
```

#### 2. Process Refund
```php
$result = $processor->processRefund(
    $payment_id,
    $refund_amount,
    'Customer cancellation'
);
```

#### 3. Get Payment Statistics
```php
$stats = $processor->getPaymentStats('2025-01-01', '2025-01-31');
```

---

## 🎨 Customization

### Change Payment Methods
Edit `getPaymentMethods()` in `payment_processor.php`:
```php
public function getPaymentMethods() {
    return [
        'gcash' => ['name' => 'GCash', 'icon' => 'bi-phone', 'enabled' => true],
        'paymaya' => ['name' => 'PayMaya', 'icon' => 'bi-wallet2', 'enabled' => false],
        // Add more methods
    ];
}
```

### Customize Invoice Design
Edit `modules/invoice.php`:
- Change company logo
- Update company information
- Modify colors and styling
- Add terms and conditions

### Configure Email Notifications
Add email sending after payment:
```php
// After successful payment
$to = $customer_email;
$subject = 'Payment Confirmation - ' . $payment_reference;
$message = 'Your payment has been received...';
mail($to, $subject, $message);
```

---

## 🔒 Security Features

1. **Transaction Integrity**
   - Database transactions for atomic operations
   - Rollback on failure

2. **Payment Verification**
   - Admin verification required for manual payments
   - Audit trail for all activities

3. **Unique References**
   - Auto-generated payment references
   - Prevents duplicate payments

4. **Data Validation**
   - Amount verification
   - Status checks
   - User authentication

---

## 📈 Admin Dashboard Features

### Revenue Statistics
- Total revenue by date range
- Average transaction value
- Payment method breakdown
- Pending verifications count

### Payment Management
- View all transactions
- Filter by date, status, method
- Verify pending payments
- Process refunds
- Export to Excel

### Audit Trail
- All payment activities logged
- User tracking
- IP address recording
- Timestamp tracking

---

## 🧪 Testing

### Test Payment Flow
1. Create a booking
2. Navigate to payment page
3. Select payment method
4. Enter payment details
5. Submit payment
6. Verify success page
7. Check invoice generation
8. Admin verifies payment

### Test Refund Flow
1. Admin opens payment details
2. Click refund button
3. Enter refund amount and reason
4. Process refund
5. Verify refund record created
6. Check booking status updated

---

## 🔗 Integration with Booking System

### Update Booking Status
Payment automatically updates:
- `payment_status` to 'Paid'
- `status` to 'Confirmed'
- `payment_method` and `payment_reference`
- `paid_at` timestamp

### Link to Validation System
```php
// Before payment, validate booking
require_once 'config/booking_validation.php';
$validator = new BookingValidator($conn);

$validation = $validator->validateNewBooking($booking_data);
if ($validation['valid']) {
    // Proceed to payment
}
```

---

## 📱 Mobile Responsive
All payment pages are fully responsive:
- Mobile-friendly payment forms
- Touch-optimized buttons
- Responsive invoice layout
- Print-friendly receipts

---

## 🌐 Payment Gateway Integration (Optional)

### Stripe Integration Example
```php
require_once 'vendor/autoload.php';
\Stripe\Stripe::setApiKey('your_secret_key');

$charge = \Stripe\Charge::create([
    'amount' => $amount * 100, // in cents
    'currency' => 'php',
    'source' => $token,
    'description' => 'Car Rental Booking #' . $booking_id
]);
```

### PayPal Integration Example
```php
// Use PayPal SDK
$payment = new Payment();
$payment->setIntent('sale')
    ->setPayer($payer)
    ->setTransactions([$transaction]);
```

---

## 📧 Email Templates

### Payment Confirmation Email
```html
Subject: Payment Confirmation - [Payment Reference]

Dear [Customer Name],

Your payment of ₱[Amount] has been successfully processed.

Payment Details:
- Reference: [Payment Reference]
- Method: [Payment Method]
- Date: [Payment Date]

Booking Details:
- Car: [Car Model]
- Pickup: [Pickup Date]
- Return: [Return Date]

View Invoice: [Invoice Link]

Thank you for choosing our service!
```

---

## 🐛 Troubleshooting

### Payment Not Processing
- Check database connection
- Verify booking exists
- Ensure amount matches
- Check payment method is enabled

### Invoice Not Generating
- Verify invoice table exists
- Check booking has payment record
- Ensure invoice number is unique

### Admin Can't Verify Payment
- Check admin permissions
- Verify admin_id in session
- Ensure payment status is 'Completed'

---

## 📊 Reports Available

1. **Daily Revenue Report**
2. **Payment Method Analysis**
3. **Pending Verifications**
4. **Refund Summary**
5. **Customer Payment History**

---

## 🔄 Future Enhancements

- [ ] Automated payment gateway integration
- [ ] Recurring payments for subscriptions
- [ ] Split payments (deposit + balance)
- [ ] Payment reminders
- [ ] SMS notifications
- [ ] QR code payments
- [ ] Cryptocurrency support
- [ ] Installment plans

---

## 📞 Support

For issues or questions:
- Email: support@carrental.com
- Phone: +63 917 123 4567

---

## ✅ Checklist

- [ ] Run SQL schema
- [ ] Configure payment methods
- [ ] Test payment flow
- [ ] Test refund process
- [ ] Customize invoice design
- [ ] Set up email notifications
- [ ] Train admin staff
- [ ] Go live!

---

**System Status:** ✅ Production Ready
**Last Updated:** January 2025
**Version:** 1.0.0
