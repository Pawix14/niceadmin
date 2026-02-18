<?php
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');

$message = '';
$message_type = '';

// Handle status update and notifications
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['notify_customer'])) {
        $booking_id = $conn->real_escape_string($_POST['booking_id']);
        $customer_email = $conn->real_escape_string($_POST['customer_email']);
        $custom_message = $conn->real_escape_string($_POST['custom_message']);
        
        $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id, is_read) 
            VALUES ('customer', '$customer_email', 'Documents Required', '$custom_message', '$booking_id', 0)");
        
        $message = "✅ Customer notified successfully!";
        $message_type = "success";
    }
    
    if (isset($_POST['approve_booking'])) {
        $booking_id = $conn->real_escape_string($_POST['booking_id']);
        $customer_email = $conn->real_escape_string($_POST['customer_email']);
        $pickup_location = $conn->real_escape_string($_POST['pickup_location']);
        
        $conn->query("UPDATE car_rentals SET status='Confirmed', pickup_status='Ready' WHERE booking_id='$booking_id'");
        $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id, is_read) 
            VALUES ('customer', '$customer_email', 'Booking Approved - Ready for Pickup', 'Your booking $booking_id has been approved! Please pick up your car at: $pickup_location', '$booking_id', 0)");
        
        $message = "✅ Booking approved! Customer notified to pick up car at $pickup_location";
        $message_type = "success";
    }
    
    if (isset($_POST['reject_booking'])) {
        $booking_id = $conn->real_escape_string($_POST['booking_id']);
        $customer_email = $conn->real_escape_string($_POST['customer_email']);
        $reason = $conn->real_escape_string($_POST['reject_reason']);
        
        $conn->query("UPDATE car_rentals SET status='Cancelled' WHERE booking_id='$booking_id'");
        $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id, is_read) 
            VALUES ('customer', '$customer_email', 'Booking Rejected', 'Your booking $booking_id has been rejected. Reason: $reason', '$booking_id', 0)");
        
        $message = "❌ Booking rejected.";
        $message_type = "warning";
    }
}

// Get bookings with document status (Pending and Confirmed)
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'Pending';
$where_clause = $status_filter == 'All' ? "cr.status IN ('Pending', 'Confirmed')" : "cr.status = '$status_filter'";

$bookings = $conn->query("SELECT cr.*, 
    (SELECT file_path FROM customer_documents WHERE customer_email = cr.customer_email AND booking_id = cr.booking_id AND document_type = 'License_Front' LIMIT 1) as license_front_path,
    (SELECT file_path FROM customer_documents WHERE customer_email = cr.customer_email AND booking_id = cr.booking_id AND document_type = 'License_Back' LIMIT 1) as license_back_path,
    (SELECT file_path FROM customer_documents WHERE customer_email = cr.customer_email AND booking_id = cr.booking_id AND document_type = 'Valid_ID' LIMIT 1) as valid_id_path,
    (SELECT file_path FROM customer_documents WHERE customer_email = cr.customer_email AND booking_id = cr.booking_id AND document_type = 'Proof_of_Address' LIMIT 1) as proof_address_path
    FROM car_rentals cr 
    WHERE $where_clause
    ORDER BY cr.created_at DESC");

$conn->close();
?>

<div class="pagetitle">
  <h1>📋 Booking Review</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">Booking Review</li>
    </ol>
  </nav>
</div>

<section class="section">
  <?php if ($message): ?>
  <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <!-- Filter Tabs -->
  <div class="card mb-3">
    <div class="card-body">
      <div class="btn-group" role="group">
        <a href="?page=staff_booking_review&status=Pending" class="btn btn-<?php echo (!isset($_GET['status']) || $_GET['status']=='Pending') ? 'primary' : 'outline-primary'; ?>">
          <i class="bi bi-clock"></i> Pending
        </a>
        <a href="?page=staff_booking_review&status=Confirmed" class="btn btn-<?php echo (isset($_GET['status']) && $_GET['status']=='Confirmed') ? 'success' : 'outline-success'; ?>">
          <i class="bi bi-check-circle"></i> Confirmed
        </a>
        <a href="?page=staff_booking_review&status=All" class="btn btn-<?php echo (isset($_GET['status']) && $_GET['status']=='All') ? 'secondary' : 'outline-secondary'; ?>">
          <i class="bi bi-list"></i> All
        </a>
      </div>
    </div>
  </div>

  <div class="row">
    <?php while($booking = $bookings->fetch_assoc()): 
      $has_license_front = !empty($booking['license_front_path']);
      $has_license_back = !empty($booking['license_back_path']);
      $has_valid_id = !empty($booking['valid_id_path']);
      $has_proof_address = !empty($booking['proof_address_path']);
      $has_all_docs = $has_license_front && $has_license_back && $has_valid_id && $has_proof_address;
    ?>
    <div class="col-lg-6 mb-4">
      <div class="card">
        <div class="card-header" style="background-color: <?php echo $has_all_docs ? '#d1fae5' : '#fee2e2'; ?>;">
          <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-receipt"></i> <?php echo $booking['booking_id']; ?></h6>
            <span class="badge bg-<?php echo $booking['status']=='Confirmed'?'success':'warning'; ?>"><?php echo $booking['status']; ?></span>
          </div>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-6">
              <small class="text-muted">Customer</small>
              <p class="mb-0 fw-bold"><?php echo htmlspecialchars($booking['customer_name']); ?></p>
              <small><?php echo htmlspecialchars($booking['customer_email']); ?></small>
              <?php if(!empty($booking['customer_phone'])): ?>
              <br><small><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($booking['customer_phone']); ?></small>
              <?php endif; ?>
            </div>
            <div class="col-6">
              <small class="text-muted">Car</small>
              <p class="mb-0 fw-bold"><?php echo htmlspecialchars($booking['car_model']); ?></p>
              <small><?php echo $booking['rental_days']; ?> days • ₱<?php echo number_format($booking['total_amount'], 2); ?></small>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-6">
              <small class="text-muted">Pickup</small>
              <p class="mb-0"><i class="bi bi-calendar"></i> <?php echo date('M d, Y', strtotime($booking['pickup_date'])); ?></p>
              <small><i class="bi bi-clock"></i> <?php echo date('g:i A', strtotime($booking['pickup_time'])); ?></small>
              <?php if(!empty($booking['pickup_location'])): ?>
              <br><small><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($booking['pickup_location']); ?></small>
              <?php endif; ?>
            </div>
            <div class="col-6">
              <small class="text-muted">Drop-off</small>
              <p class="mb-0"><i class="bi bi-calendar"></i> <?php echo date('M d, Y', strtotime($booking['dropoff_date'])); ?></p>
              <small><i class="bi bi-clock"></i> <?php echo date('g:i A', strtotime($booking['dropoff_time'])); ?></small>
              <?php if(!empty($booking['dropoff_location'])): ?>
              <br><small><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($booking['dropoff_location']); ?></small>
              <?php endif; ?>
            </div>
          </div>

          <?php if(!empty($booking['license_number']) || !empty($booking['license_expiry'])): ?>
          <div class="mb-3 p-2" style="background-color: #f8fafc; border-radius: 8px;">
            <small class="text-muted fw-bold">Driver License Details</small>
            <?php if(!empty($booking['license_number'])): ?>
            <p class="mb-1 small"><i class="bi bi-card-text"></i> License #: <strong><?php echo htmlspecialchars($booking['license_number']); ?></strong></p>
            <?php endif; ?>
            <?php if(!empty($booking['license_expiry'])): ?>
            <p class="mb-1 small"><i class="bi bi-calendar-check"></i> Expiry: <strong><?php echo date('M d, Y', strtotime($booking['license_expiry'])); ?></strong></p>
            <?php endif; ?>
            <?php if(!empty($booking['insurance_fee']) && $booking['insurance_fee'] > 0): ?>
            <p class="mb-0 small"><i class="bi bi-shield-check"></i> Insurance: <strong>₱<?php echo number_format($booking['insurance_fee'], 2); ?></strong></p>
            <?php endif; ?>
          </div>
          <?php endif; ?>

          <!-- Payment Information -->
          <?php 
          $amount_paid = $booking['amount_paid'] ?? 0;
          $remaining_balance = $booking['remaining_balance'] ?? 0;
          $is_fully_paid = ($booking['payment_status'] == 'Paid') || ($amount_paid > 0 && $remaining_balance == 0);
          $pickup_status = $booking['pickup_status'] ?? 'Not Picked Up';
          ?>
          
          <!-- Pickup Status Indicator -->
          <?php if ($booking['status'] == 'Confirmed'): ?>
          <div class="mb-3 p-2" style="background-color: <?php echo ($pickup_status == 'Picked Up') ? '#d1fae5' : '#fef3c7'; ?>; border-radius: 8px; border-left: 4px solid <?php echo ($pickup_status == 'Picked Up') ? '#10b981' : '#f59e0b'; ?>;">
            <small class="text-muted fw-bold">🚗 Pickup Status</small>
            <?php if ($pickup_status == 'Picked Up'): ?>
            <p class="mb-1 small text-success"><i class="bi bi-check-circle-fill"></i> <strong>Car Picked Up</strong></p>
            <p class="mb-0 small"><i class="bi bi-clock"></i> Picked up on: <strong><?php echo date('M d, Y g:i A', strtotime($booking['pickup_confirmed_at'])); ?></strong></p>
            <?php else: ?>
            <p class="mb-1 small text-warning"><i class="bi bi-hourglass-split"></i> <strong>Waiting for Customer Pickup</strong></p>
            <p class="mb-0 small">Customer has not confirmed pickup yet</p>
            <?php endif; ?>
          </div>
          <?php endif; ?>
          
          <div class="mb-3 p-2" style="background-color: <?php echo ($remaining_balance > 0) ? '#fef3c7' : ($is_fully_paid ? '#d1fae5' : '#f3f4f6'); ?>; border-radius: 8px;">
            <small class="text-muted fw-bold">💳 Payment Information</small>
            <p class="mb-1 small"><i class="bi bi-cash"></i> Type: <strong><?php echo $booking['payment_type'] ?? 'Full Payment'; ?></strong></p>
            <p class="mb-1 small"><i class="bi bi-check-circle"></i> Paid: <strong>₱<?php echo number_format($amount_paid, 2); ?></strong></p>
            <?php if($remaining_balance > 0): ?>
            <p class="mb-0 small text-danger"><i class="bi bi-exclamation-circle"></i> Balance: <strong>₱<?php echo number_format($remaining_balance, 2); ?></strong></p>
            <?php elseif($is_fully_paid): ?>
            <p class="mb-0 small text-success"><i class="bi bi-check-circle-fill"></i> <strong>Fully Paid</strong></p>
            <?php else: ?>
            <p class="mb-0 small text-warning"><i class="bi bi-clock"></i> <strong>Payment Pending</strong></p>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <h6>📄 Document Status</h6>
            <div class="row g-2">
              <div class="col-6">
                <?php if ($has_license_front): ?>
                <a href="<?php echo $booking['license_front_path']; ?>" target="_blank" class="badge bg-success w-100 text-decoration-none">
                  <i class="bi bi-check-circle"></i> License (Front)
                </a>
                <?php else: ?>
                <span class="badge bg-danger w-100">
                  <i class="bi bi-x-circle"></i> License (Front)
                </span>
                <?php endif; ?>
              </div>
              <div class="col-6">
                <?php if ($has_license_back): ?>
                <a href="<?php echo $booking['license_back_path']; ?>" target="_blank" class="badge bg-success w-100 text-decoration-none">
                  <i class="bi bi-check-circle"></i> License (Back)
                </a>
                <?php else: ?>
                <span class="badge bg-danger w-100">
                  <i class="bi bi-x-circle"></i> License (Back)
                </span>
                <?php endif; ?>
              </div>
              <div class="col-6">
                <?php if ($has_valid_id): ?>
                <a href="<?php echo $booking['valid_id_path']; ?>" target="_blank" class="badge bg-success w-100 text-decoration-none">
                  <i class="bi bi-check-circle"></i> Valid ID
                </a>
                <?php else: ?>
                <span class="badge bg-danger w-100">
                  <i class="bi bi-x-circle"></i> Valid ID
                </span>
                <?php endif; ?>
              </div>
              <div class="col-6">
                <?php if ($has_proof_address): ?>
                <a href="<?php echo $booking['proof_address_path']; ?>" target="_blank" class="badge bg-success w-100 text-decoration-none">
                  <i class="bi bi-check-circle"></i> Proof of Address
                </a>
                <?php else: ?>
                <span class="badge bg-danger w-100">
                  <i class="bi bi-x-circle"></i> Proof of Address
                </span>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <?php if (!$has_all_docs): ?>
          <div class="alert alert-warning small mb-3">
            <i class="bi bi-exclamation-triangle"></i> Missing documents. Notify customer to submit required documents.
          </div>
          
          <form method="POST" class="mb-3">
            <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
            <input type="hidden" name="customer_email" value="<?php echo $booking['customer_email']; ?>">
            <textarea class="form-control form-control-sm mb-2" name="custom_message" rows="2" placeholder="Custom message (optional)">Please submit the following documents: <?php 
              $missing = [];
              if (!$has_license_front) $missing[] = 'Driver\'s License (Front)';
              if (!$has_license_back) $missing[] = 'Driver\'s License (Back)';
              if (!$has_valid_id) $missing[] = 'Valid ID';
              if (!$has_proof_address) $missing[] = 'Proof of Address';
              echo implode(', ', $missing);
            ?>. Upload at: My Profile > Documents</textarea>
            <button type="submit" name="notify_customer" class="btn btn-warning btn-sm w-100">
              <i class="bi bi-bell"></i> Notify Customer
            </button>
          </form>
          <?php endif; ?>

          <div class="d-flex gap-2">
            <?php 
            $has_payment = ($booking['payment_status'] == 'Paid') || ($booking['amount_paid'] > 0);
            if ($has_all_docs && $has_payment): 
            ?>
            <form method="POST" class="flex-fill">
              <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
              <input type="hidden" name="customer_email" value="<?php echo $booking['customer_email']; ?>">
              <input type="hidden" name="pickup_location" value="<?php echo htmlspecialchars($booking['pickup_location']); ?>">
              <button type="submit" name="approve_booking" class="btn btn-success w-100">
                <i class="bi bi-check-circle"></i> <?php echo $booking['status']=='Confirmed' ? 'Re-approve' : 'Approve'; ?>
              </button>
            </form>
            <?php if(!empty($booking['remaining_balance']) && $booking['remaining_balance'] > 0): ?>
            <button type="button" class="btn btn-warning" onclick="remindBalance('<?php echo $booking['booking_id']; ?>', '<?php echo $booking['customer_email']; ?>', <?php echo $booking['remaining_balance']; ?>)">
              <i class="bi bi-bell"></i> Remind Balance
            </button>
            <?php endif; ?>
            <?php elseif ($has_all_docs && !$has_payment): ?>
            <div class="alert alert-info small mb-0 flex-fill">
              <i class="bi bi-info-circle"></i> Waiting for payment confirmation before approval.
            </div>
            <?php endif; ?>
            
            <?php if ($booking['status'] != 'Cancelled'): ?>
            <button type="button" class="btn btn-danger <?php echo $has_all_docs ? '' : 'w-100'; ?>" onclick="openRejectModal('<?php echo $booking['booking_id']; ?>', '<?php echo $booking['customer_email']; ?>', '<?php echo $booking['id']; ?>')">
              <i class="bi bi-x-circle"></i> <?php echo $booking['status']=='Confirmed' ? 'Cancel' : 'Reject'; ?>
            </button>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
</section>

<!-- Single Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" data-bs-backdrop="false" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Reject Booking</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <div class="modal-body">
          <input type="hidden" name="booking_id" id="reject_booking_id">
          <input type="hidden" name="customer_email" id="reject_customer_email">
          <label class="form-label">Reason for rejection *</label>
          <textarea class="form-control" name="reject_reason" id="reject_reason" rows="3" required></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="reject_booking" class="btn btn-danger">Reject Booking</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div id="customBackdrop" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1040;"></div>

<script>
function openRejectModal(bookingId, customerEmail, id) {
  document.getElementById('reject_booking_id').value = bookingId;
  document.getElementById('reject_customer_email').value = customerEmail;
  document.getElementById('reject_reason').value = '';
  document.getElementById('customBackdrop').style.display = 'block';
  var modal = new bootstrap.Modal(document.getElementById('rejectModal'), {backdrop: false});
  modal.show();
}

function remindBalance(bookingId, customerEmail, balance) {
  if(confirm('Send balance reminder to customer?')) {
    fetch('modules/send_balance_reminder.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'booking_id=' + bookingId + '&customer_email=' + customerEmail + '&balance=' + balance
    }).then(() => {
      alert('✅ Balance reminder sent!');
      location.reload();
    });
  }
}

document.getElementById('rejectModal').addEventListener('hidden.bs.modal', function () {
  document.getElementById('customBackdrop').style.display = 'none';
});

document.getElementById('customBackdrop').addEventListener('click', function() {
  bootstrap.Modal.getInstance(document.getElementById('rejectModal')).hide();
});
</script>

<style>
#rejectModal {
  z-index: 1050 !important;
}
</style>
