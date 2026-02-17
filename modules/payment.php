<?php
require_once 'config/payment_processor.php';

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);
$processor = new PaymentProcessor($conn);

// Get booking ID
$booking_id = $_GET['booking_id'] ?? null;

if (!$booking_id) {
    header('Location: index.php?page=my_bookings');
    exit;
}

// Get booking details
$stmt = $conn->prepare("SELECT cr.*, c.full_name, c.email, c.phone, car.image 
    FROM car_rentals cr 
    JOIN customers c ON cr.customer_id = c.id 
    LEFT JOIN cars car ON cr.car_model = car.name 
    WHERE cr.id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking || $booking['payment_status'] == 'Paid') {
    header('Location: index.php?page=my_bookings');
    exit;
}

// Process payment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['process_payment'])) {
    $payment_data = [
        'amount' => $booking['total_amount'],
        'payment_method' => $_POST['payment_method'],
        'transaction_id' => $_POST['transaction_id'] ?? null,
        'account_number' => $_POST['account_number'] ?? null,
        'account_name' => $_POST['account_name'] ?? null
    ];
    
    $result = $processor->processPayment($booking_id, $payment_data);
    
    if ($result['success']) {
        $_SESSION['payment_success'] = $result;
        header('Location: index.php?page=payment_success&ref=' . $result['payment_reference']);
        exit;
    } else {
        $error_message = $result['message'];
    }
}

$payment_methods = $processor->getPaymentMethods();
?>

<div class="pagetitle">
    <h1>Payment</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php?page=my_bookings">My Bookings</a></li>
            <li class="breadcrumb-item active">Payment</li>
        </ol>
    </nav>
</div>

<section class="section">
    <?php if (isset($error_message)): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Select Payment Method</h5>
                </div>
                <div class="card-body">
                    <form method="POST" id="paymentForm">
                        <div class="row g-3 mb-4">
                            <?php foreach ($payment_methods as $key => $method): ?>
                            <?php if ($method['enabled']): ?>
                            <div class="col-md-6">
                                <div class="payment-method-card" data-method="<?php echo $key; ?>">
                                    <input type="radio" name="payment_method" value="<?php echo $key; ?>" id="method_<?php echo $key; ?>" required>
                                    <label for="method_<?php echo $key; ?>">
                                        <i class="bi <?php echo $method['icon']; ?> fs-2"></i>
                                        <span><?php echo $method['name']; ?></span>
                                    </label>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <!-- Payment Details Forms -->
                        <div id="payment-details">
                            <!-- GCash -->
                            <div class="payment-details-form" data-method="gcash" style="display: none;">
                                <h6 class="mb-3">GCash Payment Details</h6>
                                <div class="alert alert-info">
                                    <strong>Send payment to:</strong><br>
                                    GCash Number: <strong>0917-123-4567</strong><br>
                                    Account Name: <strong>Car Rental Services</strong>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Reference Number</label>
                                    <input type="text" class="form-control" name="transaction_id" placeholder="Enter GCash reference number">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Your GCash Number</label>
                                    <input type="text" class="form-control" name="account_number" placeholder="09XX-XXX-XXXX">
                                </div>
                            </div>

                            <!-- PayMaya -->
                            <div class="payment-details-form" data-method="paymaya" style="display: none;">
                                <h6 class="mb-3">PayMaya Payment Details</h6>
                                <div class="alert alert-info">
                                    <strong>Send payment to:</strong><br>
                                    PayMaya Number: <strong>0917-987-6543</strong><br>
                                    Account Name: <strong>Car Rental Services</strong>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Reference Number</label>
                                    <input type="text" class="form-control" name="transaction_id" placeholder="Enter PayMaya reference number">
                                </div>
                            </div>

                            <!-- Bank Transfer -->
                            <div class="payment-details-form" data-method="bank_transfer" style="display: none;">
                                <h6 class="mb-3">Bank Transfer Details</h6>
                                <div class="alert alert-info">
                                    <strong>Bank Details:</strong><br>
                                    Bank: <strong>BDO</strong><br>
                                    Account Number: <strong>1234-5678-9012</strong><br>
                                    Account Name: <strong>Car Rental Services Inc.</strong>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Reference Number</label>
                                    <input type="text" class="form-control" name="transaction_id" placeholder="Enter bank reference number">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Sender Name</label>
                                    <input type="text" class="form-control" name="account_name" placeholder="Name on bank account">
                                </div>
                            </div>

                            <!-- Credit Card -->
                            <div class="payment-details-form" data-method="credit_card" style="display: none;">
                                <h6 class="mb-3">Credit/Debit Card Details</h6>
                                <div class="mb-3">
                                    <label class="form-label">Card Number</label>
                                    <input type="text" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Expiry Date</label>
                                        <input type="text" class="form-control" placeholder="MM/YY" maxlength="5">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">CVV</label>
                                        <input type="text" class="form-control" placeholder="123" maxlength="3">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Cardholder Name</label>
                                    <input type="text" class="form-control" name="account_name" placeholder="Name on card">
                                </div>
                            </div>

                            <!-- Cash -->
                            <div class="payment-details-form" data-method="cash" style="display: none;">
                                <div class="alert alert-success">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Cash Payment</strong><br>
                                    You will pay in cash when you pick up the vehicle. Please bring the exact amount.
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#">terms and conditions</a> and <a href="#">cancellation policy</a>
                            </label>
                        </div>

                        <button type="submit" name="process_payment" class="btn btn-lg w-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <i class="bi bi-lock-fill me-2"></i>Complete Payment - ₱<?php echo number_format($booking['total_amount'], 2); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header" style="background-color: #f8f9fa;">
                    <h6 class="mb-0">Booking Summary</h6>
                </div>
                <div class="card-body">
                    <?php if ($booking['image']): ?>
                    <img src="<?php echo $booking['image']; ?>" class="img-fluid rounded mb-3" alt="Car">
                    <?php endif; ?>
                    
                    <h6 class="fw-bold"><?php echo htmlspecialchars($booking['car_model']); ?></h6>
                    
                    <hr>
                    
                    <div class="mb-2">
                        <small class="text-muted">Booking ID</small>
                        <div class="fw-bold"><?php echo $booking['booking_id']; ?></div>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-muted">Pickup Date</small>
                        <div><?php echo date('M d, Y', strtotime($booking['pickup_date'])); ?></div>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-muted">Return Date</small>
                        <div><?php echo date('M d, Y', strtotime($booking['return_date'])); ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Rental Days</small>
                        <div><?php echo $booking['rental_days']; ?> days</div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>₱<?php echo number_format($booking['total_amount'], 2); ?></span>
                    </div>
                    
                    <?php if ($booking['discount_amount'] > 0): ?>
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Discount</span>
                        <span>-₱<?php echo number_format($booking['discount_amount'], 2); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <strong>Total Amount</strong>
                        <strong class="text-primary fs-5">₱<?php echo number_format($booking['total_amount'], 2); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.payment-method-card {
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    position: relative;
}

.payment-method-card:hover {
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
}

.payment-method-card input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.payment-method-card input[type="radio"]:checked + label {
    color: #667eea;
}

.payment-method-card input[type="radio"]:checked ~ label::before {
    content: '✓';
    position: absolute;
    top: 10px;
    right: 10px;
    background: #667eea;
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.payment-method-card.selected {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
}

.payment-method-card label {
    cursor: pointer;
    margin: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    position: relative;
}

.payment-details-form {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    margin-top: 20px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentCards = document.querySelectorAll('.payment-method-card');
    const detailsForms = document.querySelectorAll('.payment-details-form');
    
    paymentCards.forEach(card => {
        card.addEventListener('click', function() {
            const method = this.dataset.method;
            const radio = this.querySelector('input[type="radio"]');
            
            // Update UI
            paymentCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            radio.checked = true;
            
            // Show relevant form
            detailsForms.forEach(form => {
                form.style.display = form.dataset.method === method ? 'block' : 'none';
            });
        });
    });
});
</script>

<?php $conn->close(); ?>
