<?php
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');
$message = '';

// Add payment columns
$conn->query("ALTER TABLE car_rentals ADD COLUMN IF NOT EXISTS payment_proof VARCHAR(255)");
$conn->query("ALTER TABLE car_rentals ADD COLUMN IF NOT EXISTS invoice_number VARCHAR(50)");
$conn->query("ALTER TABLE car_rentals ADD COLUMN IF NOT EXISTS receipt_file VARCHAR(255)");
$conn->query("ALTER TABLE car_rentals ADD COLUMN IF NOT EXISTS payment_verified_by VARCHAR(50)");
$conn->query("ALTER TABLE car_rentals ADD COLUMN IF NOT EXISTS payment_verified_at DATETIME");

if(isset($_POST['update_payment'])) {
    $booking_id = intval($_POST['booking_id']);
    $action = $_POST['action'];
    
    if($action == 'approve') {
        $invoice_num = 'INV-'.date('Ymd').'-'.str_pad($booking_id, 4, '0', STR_PAD_LEFT);
        $staff_name = $_SESSION['user_name'];
        $conn->query("UPDATE car_rentals SET payment_status='Paid', invoice_number='$invoice_num', payment_verified_by='$staff_name', payment_verified_at=NOW() WHERE id=$booking_id");
        
        $booking = $conn->query("SELECT * FROM car_rentals WHERE id=$booking_id")->fetch_assoc();
        $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id, is_read) VALUES ('customer', '{$booking['customer_email']}', 'Payment Approved', 'Your payment has been approved. Invoice: $invoice_num', '{$booking['booking_id']}', 0)");
        $message = '✅ Payment approved and invoice generated!';
    } elseif($action == 'reject') {
        $reason = $conn->real_escape_string($_POST['reject_reason']);
        $booking = $conn->query("SELECT * FROM car_rentals WHERE id=$booking_id")->fetch_assoc();
        $conn->query("UPDATE car_rentals SET payment_status='Failed' WHERE id=$booking_id");
        $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id, is_read) VALUES ('customer', '{$booking['customer_email']}', 'Payment Issue', 'Payment verification failed: $reason. Please resubmit.', '{$booking['booking_id']}', 0)");
        $message = '❌ Payment rejected and customer notified!';
    }
}

if(isset($_POST['generate_receipt'])) {
    $booking_id = intval($_POST['booking_id']);
    $receipt_num = 'REC-'.date('Ymd').'-'.str_pad($booking_id, 4, '0', STR_PAD_LEFT);
    $conn->query("UPDATE car_rentals SET receipt_file='$receipt_num' WHERE id=$booking_id");
    
    $booking = $conn->query("SELECT * FROM car_rentals WHERE id=$booking_id")->fetch_assoc();
    $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id, is_read) VALUES ('customer', '{$booking['customer_email']}', 'Receipt Ready', 'Your receipt $receipt_num is ready for download.', '{$booking['booking_id']}', 0)");
    $message = '✅ Receipt generated and sent to customer!';
}

$bookings = $conn->query("SELECT * FROM car_rentals ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<div class="pagetitle">
  <h1>💳 Payment Management</h1>
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
            <th>Customer</th>
            <th>Amount</th>
            <th>Payment Method</th>
            <th>Proof</th>
            <th>Status</th>
            <th>Invoice</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($bookings as $b): ?>
          <tr>
            <td><?php echo $b['booking_id']; ?></td>
            <td><?php echo htmlspecialchars($b['customer_name']); ?><br><small><?php echo $b['customer_email']; ?></small></td>
            <td><strong>₱<?php echo number_format($b['total_amount'], 2); ?></strong></td>
            <td><?php echo $b['payment_method']; ?></td>
            <td>
              <?php if($b['payment_proof']): ?>
              <a href="<?php echo $b['payment_proof']; ?>" target="_blank" class="btn btn-sm btn-info">View Proof</a>
              <?php else: ?>
              <span class="text-muted">No proof</span>
              <?php endif; ?>
            </td>
            <td>
              <span class="badge bg-<?php echo $b['payment_status']=='Paid'?'success':($b['payment_status']=='Failed'?'danger':'warning'); ?>">
                <?php echo $b['payment_status']; ?>
              </span>
            </td>
            <td>
              <?php if($b['invoice_number']): ?>
              <strong><?php echo $b['invoice_number']; ?></strong>
              <?php if($b['receipt_file']): ?>
              <br><small class="text-success">Receipt: <?php echo $b['receipt_file']; ?></small>
              <?php endif; ?>
              <?php else: ?>
              <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if($b['payment_status']=='Pending' && $b['payment_proof']): ?>
              <form method="POST" class="d-inline">
                <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                <input type="hidden" name="action" value="approve">
                <button type="submit" name="update_payment" class="btn btn-sm btn-success" onclick="return confirm('Approve payment?')">Approve</button>
              </form>
              <button class="btn btn-sm btn-danger" onclick="rejectPayment(<?php echo $b['id']; ?>)">Reject</button>
              <?php elseif($b['payment_status']=='Paid' && !$b['receipt_file']): ?>
              <form method="POST" class="d-inline">
                <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                <button type="submit" name="generate_receipt" class="btn btn-sm btn-primary">Generate Receipt</button>
              </form>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<div id="rejectModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:white; padding:30px; border-radius:12px; max-width:500px; width:90%;">
    <h5>Reject Payment</h5>
    <form method="POST">
      <input type="hidden" name="booking_id" id="reject_booking_id">
      <input type="hidden" name="action" value="reject">
      <div class="mb-3">
        <label>Reason for rejection:</label>
        <textarea class="form-control" name="reject_reason" rows="3" required></textarea>
      </div>
      <button type="submit" name="update_payment" class="btn btn-danger">Reject Payment</button>
      <button type="button" class="btn btn-secondary" onclick="document.getElementById('rejectModal').style.display='none'">Cancel</button>
    </form>
  </div>
</div>

<script>
function rejectPayment(id) {
  document.getElementById('reject_booking_id').value = id;
  document.getElementById('rejectModal').style.display = 'flex';
}
</script>
