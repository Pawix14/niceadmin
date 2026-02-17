<?php
require_once 'config/payment_processor.php';

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);
$processor = new PaymentProcessor($conn);

// Handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['verify_payment'])) {
        $result = $processor->verifyPayment($_POST['payment_id'], $_SESSION['admin_id']);
        $message = $result['message'];
        $message_type = $result['success'] ? 'success' : 'danger';
    }
    
    if (isset($_POST['process_refund'])) {
        $result = $processor->processRefund(
            $_POST['payment_id'],
            $_POST['refund_amount'],
            $_POST['refund_reason']
        );
        $message = $result['message'];
        $message_type = $result['success'] ? 'success' : 'danger';
    }
}

// Get date range
$date_from = $_GET['date_from'] ?? date('Y-m-01');
$date_to = $_GET['date_to'] ?? date('Y-m-d');

// Get payment statistics
$stats = $processor->getPaymentStats($date_from, $date_to);

// Calculate totals
$total_revenue = 0;
$total_transactions = 0;
foreach ($stats as $stat) {
    $total_revenue += $stat['total_revenue'];
    $total_transactions += $stat['method_count'];
}

// Get recent payments
$payments_query = "SELECT p.*, cr.booking_id, cr.car_model, c.full_name, c.email
    FROM payments p
    JOIN car_rentals cr ON p.booking_id = cr.id
    JOIN customers c ON p.customer_id = c.id
    WHERE DATE(p.payment_date) BETWEEN '$date_from' AND '$date_to'
    ORDER BY p.payment_date DESC";

$payments = $conn->query($payments_query);

// Get pending verifications
$pending_query = "SELECT p.*, cr.booking_id, cr.car_model, c.full_name
    FROM payments p
    JOIN car_rentals cr ON p.booking_id = cr.id
    JOIN customers c ON p.customer_id = c.id
    WHERE p.payment_status = 'Completed' AND p.verified_by IS NULL
    ORDER BY p.payment_date DESC";

$pending_payments = $conn->query($pending_query);
?>

<div class="pagetitle">
    <h1>Payment Management</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active">Payments</li>
        </ol>
    </nav>
</div>

<section class="section">
    <?php if (isset($message)): ?>
    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card" style="border-left: 4px solid #4caf50;">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Revenue</h6>
                    <h3 class="mb-0" style="color: #4caf50;">₱<?php echo number_format($total_revenue, 2); ?></h3>
                    <small class="text-muted"><?php echo $date_from; ?> to <?php echo $date_to; ?></small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card" style="border-left: 4px solid #2196f3;">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Transactions</h6>
                    <h3 class="mb-0" style="color: #2196f3;"><?php echo $total_transactions; ?></h3>
                    <small class="text-muted">Completed payments</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card" style="border-left: 4px solid #ff9800;">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Pending Verification</h6>
                    <h3 class="mb-0" style="color: #ff9800;"><?php echo $pending_payments->num_rows; ?></h3>
                    <small class="text-muted">Awaiting approval</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card" style="border-left: 4px solid #9c27b0;">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Average Transaction</h6>
                    <h3 class="mb-0" style="color: #9c27b0;">
                        ₱<?php echo $total_transactions > 0 ? number_format($total_revenue / $total_transactions, 2) : '0.00'; ?>
                    </h3>
                    <small class="text-muted">Per booking</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods Breakdown -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Payment Methods Breakdown</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Method</th>
                                <th class="text-end">Transactions</th>
                                <th class="text-end">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats as $stat): ?>
                            <tr>
                                <td><?php echo ucwords(str_replace('_', ' ', $stat['payment_method'])); ?></td>
                                <td class="text-end"><?php echo $stat['method_count']; ?></td>
                                <td class="text-end">₱<?php echo number_format($stat['total_revenue'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-calendar-range me-2"></i>Date Filter</h6>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <input type="hidden" name="page" value="admin_payments">
                        <div class="col-md-5">
                            <label class="form-label">From Date</label>
                            <input type="date" class="form-control" name="date_from" value="<?php echo $date_from; ?>">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">To Date</label>
                            <input type="date" class="form-control" name="date_to" value="<?php echo $date_to; ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Verifications -->
    <?php if ($pending_payments->num_rows > 0): ?>
    <div class="card mb-4">
        <div class="card-header" style="background-color: #fff3cd;">
            <h6 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Pending Payment Verifications</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Customer</th>
                            <th>Booking</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($payment = $pending_payments->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo $payment['payment_reference']; ?></strong></td>
                            <td><?php echo htmlspecialchars($payment['full_name']); ?></td>
                            <td><?php echo $payment['booking_id']; ?></td>
                            <td><strong>₱<?php echo number_format($payment['amount'], 2); ?></strong></td>
                            <td><?php echo ucwords(str_replace('_', ' ', $payment['payment_method'])); ?></td>
                            <td><?php echo date('M d, Y', strtotime($payment['payment_date'])); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                                    <button type="submit" name="verify_payment" class="btn btn-sm btn-success" 
                                            onclick="return confirm('Verify this payment?')">
                                        <i class="bi bi-check-circle"></i> Verify
                                    </button>
                                </form>
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                        data-bs-target="#detailsModal<?php echo $payment['id']; ?>">
                                    <i class="bi bi-eye"></i> Details
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- All Payments -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>All Payments</h6>
            <button class="btn btn-sm btn-success" onclick="exportToExcel()">
                <i class="bi bi-file-excel"></i> Export to Excel
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="paymentsTable">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Customer</th>
                            <th>Booking</th>
                            <th>Car</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($payment = $payments->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo $payment['payment_reference']; ?></strong></td>
                            <td>
                                <?php echo htmlspecialchars($payment['full_name']); ?><br>
                                <small class="text-muted"><?php echo $payment['email']; ?></small>
                            </td>
                            <td><?php echo $payment['booking_id']; ?></td>
                            <td><?php echo htmlspecialchars($payment['car_model']); ?></td>
                            <td><strong class="text-success">₱<?php echo number_format($payment['amount'], 2); ?></strong></td>
                            <td>
                                <span class="badge bg-secondary">
                                    <?php echo ucwords(str_replace('_', ' ', $payment['payment_method'])); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $payment['payment_status'] == 'Completed' ? 'success' : 
                                        ($payment['payment_status'] == 'Verified' ? 'primary' : 
                                        ($payment['payment_status'] == 'Refunded' ? 'warning' : 'secondary')); 
                                ?>">
                                    <?php echo $payment['payment_status']; ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y h:i A', strtotime($payment['payment_date'])); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=invoice&booking_id=<?php echo $payment['booking_id']; ?>" 
                                       class="btn btn-outline-primary" target="_blank">
                                        <i class="bi bi-receipt"></i>
                                    </a>
                                    <?php if ($payment['payment_status'] != 'Refunded'): ?>
                                    <button class="btn btn-outline-danger" data-bs-toggle="modal" 
                                            data-bs-target="#refundModal<?php echo $payment['id']; ?>">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Refund Modal -->
                        <div class="modal fade" id="refundModal<?php echo $payment['id']; ?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Process Refund</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Refund Amount</label>
                                                <input type="number" class="form-control" name="refund_amount" 
                                                       max="<?php echo $payment['amount']; ?>" 
                                                       value="<?php echo $payment['amount']; ?>" 
                                                       step="0.01" required>
                                                <small class="text-muted">Maximum: ₱<?php echo number_format($payment['amount'], 2); ?></small>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Refund Reason</label>
                                                <textarea class="form-control" name="refund_reason" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" name="process_refund" class="btn btn-danger">Process Refund</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
function exportToExcel() {
    const table = document.getElementById('paymentsTable');
    let html = table.outerHTML;
    const url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'payments_' + new Date().toISOString().slice(0,10) + '.xls';
    link.click();
}
</script>

<?php $conn->close(); ?>
