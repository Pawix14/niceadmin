<?php
$payment_ref = $_GET['ref'] ?? null;

if (!$payment_ref) {
    header('Location: index.php?page=my_bookings');
    exit;
}

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

// Get payment details
$stmt = $conn->prepare("SELECT p.*, cr.*, c.full_name, c.email, i.invoice_number
    FROM payments p
    JOIN car_rentals cr ON p.booking_id = cr.id
    JOIN customers c ON p.customer_id = c.id
    LEFT JOIN invoices i ON p.booking_id = i.booking_id
    WHERE p.payment_reference = ?");
$stmt->bind_param("s", $payment_ref);
$stmt->execute();
$payment = $stmt->get_result()->fetch_assoc();

if (!$payment) {
    header('Location: index.php?page=my_bookings');
    exit;
}
?>

<div class="pagetitle">
    <h1>Payment Successful</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php?page=my_bookings">My Bookings</a></li>
            <li class="breadcrumb-item active">Payment Success</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Message -->
            <div class="card border-0 shadow-lg mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-center text-white py-5">
                    <div class="success-checkmark mb-4">
                        <div class="check-icon">
                            <span class="icon-line line-tip"></span>
                            <span class="icon-line line-long"></span>
                            <div class="icon-circle"></div>
                            <div class="icon-fix"></div>
                        </div>
                    </div>
                    <h2 class="mb-3">Payment Successful!</h2>
                    <p class="mb-0 fs-5">Your booking has been confirmed</p>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Payment Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <small class="text-muted d-block mb-1">Payment Reference</small>
                                <strong class="fs-5"><?php echo $payment['payment_reference']; ?></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <small class="text-muted d-block mb-1">Amount Paid</small>
                                <strong class="fs-5 text-success">₱<?php echo number_format($payment['amount'], 2); ?></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <small class="text-muted d-block mb-1">Payment Method</small>
                                <strong><?php echo ucwords(str_replace('_', ' ', $payment['payment_method'])); ?></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <small class="text-muted d-block mb-1">Payment Date</small>
                                <strong><?php echo date('F d, Y h:i A', strtotime($payment['payment_date'])); ?></strong>
                            </div>
                        </div>
                        <?php if ($payment['invoice_number']): ?>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <small class="text-muted d-block mb-1">Invoice Number</small>
                                <strong><?php echo $payment['invoice_number']; ?></strong>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Booking Summary -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-car-front me-2"></i>Booking Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <h6 class="fw-bold"><?php echo htmlspecialchars($payment['car_model']); ?></h6>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Booking ID</small>
                            <strong><?php echo $payment['booking_id']; ?></strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge bg-success">Confirmed</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Pickup Date</small>
                            <strong><?php echo date('M d, Y', strtotime($payment['pickup_date'])); ?></strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Return Date</small>
                            <strong><?php echo date('M d, Y', strtotime($payment['return_date'])); ?></strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Rental Days</small>
                            <strong><?php echo $payment['rental_days']; ?> days</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="index.php?page=invoice&booking_id=<?php echo $payment['booking_id']; ?>" 
                               class="btn btn-primary w-100" target="_blank">
                                <i class="bi bi-file-earmark-pdf me-2"></i>Download Invoice
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="index.php?page=invoice&booking_id=<?php echo $payment['booking_id']; ?>&print=true" 
                               class="btn btn-outline-primary w-100" target="_blank">
                                <i class="bi bi-printer me-2"></i>Print Receipt
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="index.php?page=my_bookings" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-list-ul me-2"></i>View My Bookings
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="index.php" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-house me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Important Information -->
            <div class="alert alert-info mt-4">
                <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Important Information</h6>
                <ul class="mb-0">
                    <li>A confirmation email has been sent to <strong><?php echo $payment['email']; ?></strong></li>
                    <li>Please bring a valid ID and this invoice when picking up your vehicle</li>
                    <li>Arrive at least 15 minutes before your scheduled pickup time</li>
                    <li>For any questions, contact us at support@carrental.com</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<style>
.success-checkmark {
    width: 80px;
    height: 80px;
    margin: 0 auto;
}

.check-icon {
    width: 80px;
    height: 80px;
    position: relative;
    border-radius: 50%;
    box-sizing: content-box;
    border: 4px solid #4caf50;
    background-color: white;
}

.check-icon::before {
    top: 3px;
    left: -2px;
    width: 30px;
    transform-origin: 100% 50%;
    border-radius: 100px 0 0 100px;
}

.check-icon::after {
    top: 0;
    left: 30px;
    width: 60px;
    transform-origin: 0 50%;
    border-radius: 0 100px 100px 0;
    animation: rotate-circle 4.25s ease-in;
}

.icon-line {
    height: 5px;
    background-color: #4caf50;
    display: block;
    border-radius: 2px;
    position: absolute;
    z-index: 10;
}

.icon-line.line-tip {
    top: 46px;
    left: 14px;
    width: 25px;
    transform: rotate(45deg);
    animation: icon-line-tip 0.75s;
}

.icon-line.line-long {
    top: 38px;
    right: 8px;
    width: 47px;
    transform: rotate(-45deg);
    animation: icon-line-long 0.75s;
}

.icon-circle {
    top: -4px;
    left: -4px;
    z-index: 10;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    position: absolute;
    box-sizing: content-box;
    border: 4px solid rgba(76, 175, 80, .5);
}

.icon-fix {
    top: 8px;
    width: 5px;
    left: 26px;
    z-index: 1;
    height: 85px;
    position: absolute;
    transform: rotate(-45deg);
    background-color: white;
}

@keyframes icon-line-tip {
    0% { width: 0; left: 1px; top: 19px; }
    54% { width: 0; left: 1px; top: 19px; }
    70% { width: 50px; left: -8px; top: 37px; }
    84% { width: 17px; left: 21px; top: 48px; }
    100% { width: 25px; left: 14px; top: 45px; }
}

@keyframes icon-line-long {
    0% { width: 0; right: 46px; top: 54px; }
    65% { width: 0; right: 46px; top: 54px; }
    84% { width: 55px; right: 0px; top: 35px; }
    100% { width: 47px; right: 8px; top: 38px; }
}

.detail-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}
</style>

<?php $conn->close(); ?>
