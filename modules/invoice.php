<?php
require_once 'config/payment_processor.php';

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

$invoice_id = $_GET['invoice_id'] ?? null;
$booking_id = $_GET['booking_id'] ?? null;

// Get invoice details
if ($invoice_id) {
    $stmt = $conn->prepare("SELECT i.*, cr.*, c.full_name, c.email, c.phone, c.address,
        p.payment_method, p.payment_reference, p.payment_date, p.transaction_id
        FROM invoices i
        JOIN car_rentals cr ON i.booking_id = cr.id
        JOIN customers c ON cr.customer_id = c.id
        LEFT JOIN payments p ON i.payment_id = p.id
        WHERE i.id = ?");
    $stmt->bind_param("i", $invoice_id);
} else if ($booking_id) {
    $stmt = $conn->prepare("SELECT i.*, cr.*, c.full_name, c.email, c.phone, c.address,
        p.payment_method, p.payment_reference, p.payment_date, p.transaction_id
        FROM car_rentals cr
        JOIN customers c ON cr.customer_id = c.id
        LEFT JOIN invoices i ON cr.id = i.booking_id
        LEFT JOIN payments p ON i.payment_id = p.id
        WHERE cr.id = ?");
    $stmt->bind_param("i", $booking_id);
}

$stmt->execute();
$invoice = $stmt->get_result()->fetch_assoc();

if (!$invoice) {
    echo "Invoice not found";
    exit;
}

// Calculate amounts
$subtotal = $invoice['total_amount'];
$discount = $invoice['discount_amount'] ?? 0;
$tax = 0; // Add tax calculation if needed
$total = $subtotal - $discount + $tax;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice - <?php echo $invoice['invoice_number']; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #333; line-height: 1.6; }
        .invoice-container { max-width: 800px; margin: 20px auto; padding: 40px; background: white; }
        .invoice-header { display: flex; justify-content: space-between; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 3px solid #667eea; }
        .company-info h1 { color: #667eea; font-size: 28px; margin-bottom: 5px; }
        .company-info p { color: #666; font-size: 14px; }
        .invoice-title { text-align: right; }
        .invoice-title h2 { color: #333; font-size: 32px; margin-bottom: 10px; }
        .invoice-number { background: #667eea; color: white; padding: 8px 15px; border-radius: 5px; display: inline-block; }
        .invoice-details { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px; }
        .detail-box { background: #f8f9fa; padding: 20px; border-radius: 8px; }
        .detail-box h3 { color: #667eea; font-size: 14px; margin-bottom: 15px; text-transform: uppercase; }
        .detail-box p { margin-bottom: 8px; font-size: 14px; }
        .detail-box strong { color: #333; }
        .invoice-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .invoice-table thead { background: #667eea; color: white; }
        .invoice-table th { padding: 12px; text-align: left; font-weight: 600; }
        .invoice-table td { padding: 12px; border-bottom: 1px solid #e0e0e0; }
        .invoice-table tbody tr:hover { background: #f8f9fa; }
        .totals-section { margin-left: auto; width: 300px; }
        .total-row { display: flex; justify-content: space-between; padding: 10px 0; font-size: 14px; }
        .total-row.subtotal { border-top: 1px solid #e0e0e0; }
        .total-row.final { background: #667eea; color: white; padding: 15px; margin-top: 10px; border-radius: 5px; font-size: 18px; font-weight: bold; }
        .payment-info { background: #e8f5e9; padding: 20px; border-radius: 8px; margin-top: 30px; border-left: 4px solid #4caf50; }
        .payment-info h3 { color: #2e7d32; margin-bottom: 10px; }
        .footer { margin-top: 50px; padding-top: 20px; border-top: 2px solid #e0e0e0; text-align: center; color: #666; font-size: 12px; }
        .status-badge { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-paid { background: #4caf50; color: white; }
        .status-pending { background: #ff9800; color: white; }
        .print-button { background: #667eea; color: white; border: none; padding: 12px 30px; border-radius: 5px; cursor: pointer; font-size: 16px; margin-bottom: 20px; }
        .print-button:hover { background: #5568d3; }
        @media print {
            .print-button, .no-print { display: none; }
            .invoice-container { padding: 0; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <button class="print-button no-print" onclick="window.print()">
            <i class="bi bi-printer"></i> Print Invoice
        </button>
        
        <div class="invoice-header">
            <div class="company-info">
                <h1>🚗 Car Rental Services</h1>
                <p>123 Main Street, Manila, Philippines</p>
                <p>Phone: +63 917 123 4567</p>
                <p>Email: info@carrental.com</p>
                <p>Website: www.carrental.com</p>
            </div>
            <div class="invoice-title">
                <h2>INVOICE</h2>
                <div class="invoice-number"><?php echo $invoice['invoice_number']; ?></div>
                <p style="margin-top: 10px; color: #666;">
                    Date: <?php echo date('F d, Y', strtotime($invoice['invoice_date'])); ?>
                </p>
                <span class="status-badge status-<?php echo strtolower($invoice['status']); ?>">
                    <?php echo strtoupper($invoice['status']); ?>
                </span>
            </div>
        </div>

        <div class="invoice-details">
            <div class="detail-box">
                <h3>Bill To</h3>
                <p><strong><?php echo htmlspecialchars($invoice['full_name']); ?></strong></p>
                <p><?php echo htmlspecialchars($invoice['email']); ?></p>
                <p><?php echo htmlspecialchars($invoice['phone']); ?></p>
                <?php if ($invoice['address']): ?>
                <p><?php echo htmlspecialchars($invoice['address']); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="detail-box">
                <h3>Booking Details</h3>
                <p><strong>Booking ID:</strong> <?php echo $invoice['booking_id']; ?></p>
                <p><strong>Pickup Date:</strong> <?php echo date('M d, Y', strtotime($invoice['pickup_date'])); ?></p>
                <p><strong>Return Date:</strong> <?php echo date('M d, Y', strtotime($invoice['return_date'])); ?></p>
                <p><strong>Rental Days:</strong> <?php echo $invoice['rental_days']; ?> days</p>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: center;">Quantity</th>
                    <th style="text-align: right;">Unit Price</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($invoice['car_model']); ?></strong><br>
                        <small style="color: #666;">Car Rental Service</small>
                    </td>
                    <td style="text-align: center;"><?php echo $invoice['rental_days']; ?> days</td>
                    <td style="text-align: right;">₱<?php echo number_format($subtotal / $invoice['rental_days'], 2); ?></td>
                    <td style="text-align: right;">₱<?php echo number_format($subtotal, 2); ?></td>
                </tr>
                
                <?php if ($invoice['promo_code']): ?>
                <tr>
                    <td colspan="3" style="text-align: right; color: #4caf50;">
                        <strong>Promo Code Applied: <?php echo $invoice['promo_code']; ?></strong>
                    </td>
                    <td style="text-align: right; color: #4caf50;">
                        -₱<?php echo number_format($discount, 2); ?>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="totals-section">
            <div class="total-row subtotal">
                <span>Subtotal:</span>
                <span>₱<?php echo number_format($subtotal, 2); ?></span>
            </div>
            
            <?php if ($discount > 0): ?>
            <div class="total-row" style="color: #4caf50;">
                <span>Discount:</span>
                <span>-₱<?php echo number_format($discount, 2); ?></span>
            </div>
            <?php endif; ?>
            
            <div class="total-row">
                <span>Tax (0%):</span>
                <span>₱<?php echo number_format($tax, 2); ?></span>
            </div>
            
            <div class="total-row final">
                <span>TOTAL:</span>
                <span>₱<?php echo number_format($total, 2); ?></span>
            </div>
        </div>

        <?php if ($invoice['payment_status'] == 'Paid'): ?>
        <div class="payment-info">
            <h3>✓ Payment Received</h3>
            <p><strong>Payment Method:</strong> <?php echo ucwords(str_replace('_', ' ', $invoice['payment_method'])); ?></p>
            <p><strong>Payment Reference:</strong> <?php echo $invoice['payment_reference']; ?></p>
            <?php if ($invoice['transaction_id']): ?>
            <p><strong>Transaction ID:</strong> <?php echo $invoice['transaction_id']; ?></p>
            <?php endif; ?>
            <p><strong>Payment Date:</strong> <?php echo date('F d, Y h:i A', strtotime($invoice['payment_date'])); ?></p>
        </div>
        <?php endif; ?>

        <div class="footer">
            <p><strong>Terms & Conditions</strong></p>
            <p>Payment is due within 7 days. Late payments may incur additional charges.</p>
            <p>Please bring this invoice when picking up your vehicle.</p>
            <p style="margin-top: 20px;">Thank you for choosing our car rental service!</p>
            <p style="margin-top: 10px; font-size: 10px;">This is a computer-generated invoice and does not require a signature.</p>
        </div>
    </div>

    <script>
        // Auto-print option
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('print') === 'true') {
            window.onload = function() {
                window.print();
            };
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>
