<?php
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');
$customer_username = $_SESSION['username'];
$customer_info = $conn->query("SELECT * FROM customers WHERE username='$customer_username'")->fetch_assoc();
$customer_email = $customer_info['email'];

$message = '';

if(isset($_POST['submit_payment'])) {
    $booking_id = intval($_POST['booking_id']);
    $payment_amount = floatval($_POST['payment_amount']);
    
    if(isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] == 0) {
        $upload_dir = 'uploads/payments/';
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION);
        $file_name = 'payment_'.$booking_id.'_'.time().'.'.$file_ext;
        $file_path = $upload_dir.$file_name;
        
        if(move_uploaded_file($_FILES['payment_proof']['tmp_name'], $file_path)) {
            // Get current booking details
            $booking = $conn->query("SELECT * FROM car_rentals WHERE id=$booking_id")->fetch_assoc();
            $new_amount_paid = $booking['amount_paid'] + $payment_amount;
            $new_remaining = $booking['remaining_balance'] - $payment_amount;
            
            // Update payment info
            $conn->query("UPDATE car_rentals SET 
                payment_proof='$file_path', 
                payment_status='Pending',
                amount_paid=$new_amount_paid,
                remaining_balance=$new_remaining
                WHERE id=$booking_id");
            
            // Notify staff/admin
            $conn->query("INSERT INTO notifications (user_type, title, message, booking_id, is_read) 
                VALUES ('staff', 'Payment Submitted', 
                'Customer submitted payment proof for booking {$booking['booking_id']}. Amount: ₱" . number_format($payment_amount, 2) . "', 
                '{$booking['booking_id']}', 0)");
            
            $message = '✅ Payment proof submitted! Amount: ₱' . number_format($payment_amount, 2) . '. Waiting for staff verification.';
        }
    }
}

$bookings = $conn->query("SELECT * FROM car_rentals WHERE customer_email='$customer_email' ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<div class="pagetitle">
  <h1>💳 My Payments</h1>
</div>

<section class="section">
  <?php if($message): ?>
  <div class="alert alert-success" style="background-color: #d1fae5; border: 2px solid #10b981; color: #065f46; font-weight: 600; font-size: 16px;"><?php echo $message; ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <table class="table">
        <thead>
          <tr>
            <th>Booking ID</th>
            <th>Car</th>
            <th>Total Amount</th>
            <th>Amount Paid</th>
            <th>Remaining Balance</th>
            <th>Payment Method</th>
            <th>Status</th>
            <th>Invoice/Receipt</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($bookings as $b): 
            $amount_paid = $b['amount_paid'] ?? 0;
            $remaining = $b['remaining_balance'] ?? $b['total_amount'];
          ?>
          <tr>
            <td><?php echo $b['booking_id']; ?></td>
            <td><?php echo htmlspecialchars($b['car_model']); ?></td>
            <td><strong>₱<?php echo number_format($b['total_amount'], 2); ?></strong></td>
            <td><strong class="text-success">₱<?php echo number_format($amount_paid, 2); ?></strong></td>
            <td>
              <?php if($remaining > 0): ?>
              <strong class="text-danger">₱<?php echo number_format($remaining, 2); ?></strong>
              <?php else: ?>
              <span class="text-success">✅ Fully Paid</span>
              <?php endif; ?>
            </td>
            <td><?php echo $b['payment_method']; ?></td>
            <td>
              <?php 
              // Show Cancelled if booking is cancelled, otherwise show payment status
              $display_status = ($b['status'] == 'Cancelled') ? 'Cancelled' : $b['payment_status'];
              $badge_color = ($display_status == 'Cancelled') ? 'secondary' : ($b['payment_status']=='Paid'?'success':($b['payment_status']=='Failed'?'danger':'warning'));
              ?>
              <span class="badge bg-<?php echo $badge_color; ?>">
                <?php echo $display_status; ?>
              </span>
              <?php if($b['payment_verified_by']): ?>
              <br><small class="text-muted">Verified by: <?php echo $b['payment_verified_by']; ?></small>
              <?php endif; ?>
            </td>
            <td>
              <?php if($b['invoice_number']): ?>
              <strong><?php echo $b['invoice_number']; ?></strong>
              <?php endif; ?>
              <?php if($b['receipt_file']): ?>
              <br><button class="btn btn-sm btn-success" onclick="viewReceipt('<?php echo $b['booking_id']; ?>', '<?php echo $b['receipt_file']; ?>', '<?php echo $b['invoice_number']; ?>', <?php echo $b['total_amount']; ?>, '<?php echo addslashes($b['customer_name']); ?>', '<?php echo addslashes($b['car_model']); ?>', '<?php echo $b['pickup_date']; ?>', '<?php echo $b['dropoff_date']; ?>', <?php echo $b['rental_days']; ?>, <?php echo $b['daily_rate']; ?>, <?php echo $b['insurance_fee']; ?>, <?php echo $amount_paid; ?>, <?php echo $remaining; ?>)">View Receipt</button>
              <?php endif; ?>
            </td>
            <td>
              <?php if($b['status'] != 'Cancelled' && $remaining > 0): ?>
              <button class="btn btn-sm btn-primary" onclick="showPaymentForm(<?php echo $b['id']; ?>, '<?php echo $b['booking_id']; ?>', <?php echo $b['total_amount']; ?>, <?php echo $remaining; ?>, '<?php echo $b['payment_type'] ?? 'Full Payment'; ?>')">
                <i class="bi bi-credit-card"></i> Pay Balance
              </button>
              <?php elseif($b['status'] == 'Cancelled'): ?>
              <small class="text-muted">Booking cancelled</small>
              <?php elseif($remaining <= 0): ?>
              <span class="badge bg-success">✓ Fully Paid</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<div id="paymentModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:white; padding:30px; border-radius:12px; max-width:600px; width:90%;">
    <h5><i class="bi bi-credit-card"></i> Submit Payment</h5>
    <div class="alert alert-info mb-3">
      <p class="mb-1"><strong>Booking:</strong> <span id="modal_booking_id"></span></p>
      <p class="mb-1"><strong>Payment Type:</strong> <span id="modal_payment_type"></span></p>
      <p class="mb-1"><strong>Total Amount:</strong> <span id="modal_total"></span></p>
      <p class="mb-0"><strong>Remaining Balance:</strong> <span id="modal_remaining" class="text-danger"></span></p>
    </div>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="booking_id" id="form_booking_id">
      
      <div class="mb-3">
        <label class="form-label">Payment Amount *</label>
        <div class="input-group">
          <span class="input-group-text">₱</span>
          <input type="number" class="form-control" name="payment_amount" id="payment_amount" step="0.01" min="0.01" required>
        </div>
        <div class="form-text">
          <button type="button" class="btn btn-sm btn-outline-primary" onclick="setFullAmount()">Pay Full Balance</button>
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setHalfAmount()">Pay 50%</button>
        </div>
      </div>
      
      <div class="mb-3">
        <label class="form-label">Upload Payment Proof (Screenshot/Receipt) *</label>
        <input type="file" class="form-control" name="payment_proof" accept="image/*,.pdf" required>
        <small class="text-muted">Accepted: JPG, PNG, PDF</small>
      </div>
      
      <div class="alert alert-warning small">
        <i class="bi bi-info-circle"></i> <strong>Payment Instructions:</strong><br>
        1. Transfer to our bank account or GCash<br>
        2. Take a screenshot of the payment confirmation<br>
        3. Upload the proof here<br>
        4. Staff will verify within 24 hours
      </div>
      
      <button type="submit" name="submit_payment" class="btn btn-primary">
        <i class="bi bi-check-circle"></i> Submit Payment
      </button>
      <button type="button" class="btn btn-secondary" onclick="document.getElementById('paymentModal').style.display='none'">Cancel</button>
    </form>
  </div>
</div>

<script>
let currentRemaining = 0;

function showPaymentForm(id, bookingId, totalAmount, remaining, paymentType) {
  currentRemaining = remaining;
  document.getElementById('form_booking_id').value = id;
  document.getElementById('modal_booking_id').textContent = bookingId;
  document.getElementById('modal_payment_type').textContent = paymentType;
  document.getElementById('modal_total').textContent = '₱' + totalAmount.toFixed(2);
  document.getElementById('modal_remaining').textContent = '₱' + remaining.toFixed(2);
  document.getElementById('payment_amount').value = '';
  document.getElementById('payment_amount').max = remaining;
  document.getElementById('paymentModal').style.display = 'flex';
}

function setFullAmount() {
  document.getElementById('payment_amount').value = currentRemaining.toFixed(2);
}

function setHalfAmount() {
  document.getElementById('payment_amount').value = (currentRemaining / 2).toFixed(2);
}

function viewReceipt(bookingId, receiptNum, invoiceNum, totalAmount, customerName, carModel, pickupDate, dropoffDate, rentalDays, dailyRate, insuranceFee, amountPaid, remaining) {
  const receiptWindow = window.open('', '_blank', 'width=900,height=800');
  const subtotal = dailyRate * rentalDays;
  receiptWindow.document.write(`
    <html>
    <head><title>Receipt - ${receiptNum}</title>
    <style>
      body{font-family:Arial;padding:40px;max-width:900px;margin:0 auto;background:#f5f5f5;}
      .receipt{background:white;padding:40px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
      .header{text-align:center;border-bottom:3px solid #2c3e50;padding-bottom:20px;margin-bottom:30px;}
      .header h1{color:#2c3e50;margin:0;font-size:36px;}
      .header h3{color:#666;margin:10px 0 0 0;font-weight:normal;}
      .section{margin:25px 0;padding:20px;background:#f8f9fa;border-radius:8px;}
      .section-title{font-size:18px;font-weight:bold;color:#2c3e50;margin-bottom:15px;border-bottom:2px solid #dee2e6;padding-bottom:8px;}
      .info-row{display:flex;justify-content:space-between;padding:8px 0;}
      .info-label{font-weight:600;color:#666;}
      .breakdown-row{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #dee2e6;}
      .breakdown-label{color:#666;}
      .breakdown-value{font-weight:600;color:#2c3e50;}
      .total-section{background:#2c3e50;color:white;padding:20px;border-radius:8px;margin:30px 0;}
      .total-row{display:flex;justify-content:space-between;padding:8px 0;font-size:18px;}
      .grand-total{font-size:28px;font-weight:bold;text-align:center;padding:15px 0;border-top:2px solid rgba(255,255,255,0.3);margin-top:10px;}
      .footer{text-align:center;margin-top:40px;padding-top:20px;border-top:2px solid #dee2e6;color:#666;}
      .print-btn{margin-top:20px;padding:12px 30px;background:#2c3e50;color:white;border:none;border-radius:8px;cursor:pointer;font-size:16px;}
      .print-btn:hover{background:#1a252f;}
      @media print{.print-btn{display:none;} body{background:white;}}
    </style>
    </head>
    <body>
    <div class="receipt">
      <div class="header">
        <h1>🚗 CarGo Car Rental</h1>
        <h3>Official Payment Receipt</h3>
      </div>
      
      <div class="section">
        <div class="section-title">Receipt Information</div>
        <div class="info-row"><span class="info-label">Receipt Number:</span><span>${receiptNum}</span></div>
        <div class="info-row"><span class="info-label">Invoice Number:</span><span>${invoiceNum}</span></div>
        <div class="info-row"><span class="info-label">Booking ID:</span><span>${bookingId}</span></div>
        <div class="info-row"><span class="info-label">Date Issued:</span><span>${new Date().toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'})}</span></div>
        <div class="info-row"><span class="info-label">Time:</span><span>${new Date().toLocaleTimeString('en-US')}</span></div>
      </div>
      
      <div class="section">
        <div class="section-title">Customer Information</div>
        <div class="info-row"><span class="info-label">Customer Name:</span><span>${customerName}</span></div>
      </div>
      
      <div class="section">
        <div class="section-title">Rental Details</div>
        <div class="info-row"><span class="info-label">Vehicle:</span><span>${carModel}</span></div>
        <div class="info-row"><span class="info-label">Pickup Date:</span><span>${new Date(pickupDate).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'})}</span></div>
        <div class="info-row"><span class="info-label">Drop-off Date:</span><span>${new Date(dropoffDate).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'})}</span></div>
        <div class="info-row"><span class="info-label">Rental Duration:</span><span>${rentalDays} day(s)</span></div>
      </div>
      
      <div class="section">
        <div class="section-title">Payment Breakdown</div>
        <div class="breakdown-row">
          <span class="breakdown-label">Daily Rate (₱${dailyRate.toLocaleString('en-US', {minimumFractionDigits: 2})} × ${rentalDays} days)</span>
          <span class="breakdown-value">₱${subtotal.toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
        </div>
        <div class="breakdown-row">
          <span class="breakdown-label">Insurance Fee</span>
          <span class="breakdown-value">₱${insuranceFee.toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
        </div>
        <div class="breakdown-row" style="border-bottom:2px solid #2c3e50;font-weight:bold;">
          <span class="breakdown-label">Total Amount</span>
          <span class="breakdown-value">₱${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
        </div>
      </div>
      
      <div class="total-section">
        <div class="total-row">
          <span>Amount Paid:</span>
          <span>₱${amountPaid.toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
        </div>
        <div class="total-row">
          <span>Remaining Balance:</span>
          <span>₱${remaining.toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
        </div>
        <div class="grand-total">
          ${remaining > 0 ? 'PARTIAL PAYMENT' : 'FULLY PAID'}
        </div>
      </div>
      
      <div class="footer">
        <p style="margin:5px 0;"><strong>Thank you for choosing CarGo!</strong></p>
        <p style="margin:5px 0;font-size:14px;">For inquiries, contact us at support@cargo.com | +63 123 456 7890</p>
        <p style="margin:5px 0;font-size:12px;color:#999;">This is a computer-generated receipt and does not require a signature.</p>
      </div>
      
      <center><button onclick="window.print()" class="print-btn">🖨️ Print Receipt</button></center>
    </div>
    </body>
    </html>
  `);
}
</script>
