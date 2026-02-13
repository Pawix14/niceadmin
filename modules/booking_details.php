<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$message_type = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $booking_id = $conn->real_escape_string($_POST['booking_id']);
    $status = $conn->real_escape_string($_POST['status']);
    $admin_notes = $conn->real_escape_string($_POST['admin_notes']);
    
    $update_result = $conn->query("UPDATE car_rentals SET status='$status', admin_notes='$admin_notes' WHERE booking_id='$booking_id'");
    
    if (!$update_result) {
        $message = "❌ Error updating booking: " . $conn->error;
        $message_type = 'danger';
    } else {
        // Get customer email
        $booking_data = $conn->query("SELECT customer_email, customer_name FROM car_rentals WHERE booking_id='$booking_id'")->fetch_assoc();
        
        if ($booking_data) {
            // Create notification for customer
            $title = $conn->real_escape_string("Booking $status");
            $notif_message = $conn->real_escape_string("Your booking $booking_id has been $status by admin.");
            if ($admin_notes) {
                $notif_message .= $conn->real_escape_string(" Note: $admin_notes");
            }
            
            $customer_email = $conn->real_escape_string($booking_data['customer_email']);
            $sql = "INSERT INTO notifications (user_type, user_id, title, message, booking_id, is_read, created_at) VALUES ('customer', '$customer_email', '$title', '$notif_message', '$booking_id', 0, NOW())";
            $notif_result = $conn->query($sql);
            
            if (!$notif_result) {
                $message = "⚠️ Booking updated but notification failed: " . $conn->error . " SQL: " . $sql;
                $message_type = 'warning';
            } else {
                $message = "✅ Booking updated and customer notified!";
                $message_type = 'success';
            }
        } else {
            $message = "⚠️ Booking updated but customer not found!";
            $message_type = 'warning';
        }
    }
}

// Get booking details
$booking_id = isset($_GET['id']) ? $conn->real_escape_string($_GET['id']) : '';
$booking = $conn->query("SELECT * FROM car_rentals WHERE booking_id='$booking_id'")->fetch_assoc();

if (!$booking) {
    echo '<div class="alert alert-danger">Booking not found</div>';
    exit();
}

$conn->close();
?>

<div class="pagetitle">
  <h1>📋 Booking Details</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php?page=all_bookings">Bookings</a></li>
      <li class="breadcrumb-item active"><?php echo $booking['booking_id']; ?></li>
    </ol>
  </nav>
</div>

<section class="section">
  <?php if ($message): ?>
  <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <div class="row">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header bg-light">
          <h6 class="mb-0">🚗 Booking Information</h6>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <strong>Booking ID:</strong>
              <p><?php echo $booking['booking_id']; ?></p>
            </div>
            <div class="col-md-6">
              <strong>Status:</strong>
              <p><span class="badge bg-<?php echo $booking['status'] == 'Confirmed' ? 'success' : ($booking['status'] == 'Pending' ? 'warning' : 'secondary'); ?>">
                <?php echo $booking['status']; ?>
              </span></p>
            </div>
          </div>
          
          <div class="row mb-3">
            <div class="col-md-6">
              <strong>Car Model:</strong>
              <p><?php echo htmlspecialchars($booking['car_model']); ?></p>
            </div>
            <div class="col-md-6">
              <strong>Car Type:</strong>
              <p><?php echo htmlspecialchars($booking['car_type']); ?></p>
            </div>
          </div>
          
          <div class="row mb-3">
            <div class="col-md-6">
              <strong>Pickup Date:</strong>
              <p><?php echo date('M d, Y', strtotime($booking['pickup_date'])); ?> at <?php echo date('h:i A', strtotime($booking['pickup_time'])); ?></p>
            </div>
            <div class="col-md-6">
              <strong>Dropoff Date:</strong>
              <p><?php echo date('M d, Y', strtotime($booking['dropoff_date'])); ?> at <?php echo date('h:i A', strtotime($booking['dropoff_time'])); ?></p>
            </div>
          </div>
          
          <div class="row mb-3">
            <div class="col-md-6">
              <strong>Pickup Location:</strong>
              <p><?php echo htmlspecialchars($booking['pickup_location']); ?></p>
            </div>
            <div class="col-md-6">
              <strong>Dropoff Location:</strong>
              <p><?php echo htmlspecialchars($booking['dropoff_location']); ?></p>
            </div>
          </div>
          
          <div class="row mb-3">
            <div class="col-md-6">
              <strong>Rental Days:</strong>
              <p><?php echo $booking['rental_days']; ?> days</p>
            </div>
            <div class="col-md-6">
              <strong>Total Amount:</strong>
              <p class="text-success fs-5"><strong>₱<?php echo number_format($booking['total_amount'], 2); ?></strong></p>
            </div>
          </div>
        </div>
      </div>
      
      <div class="card mt-3">
        <div class="card-header bg-light">
          <h6 class="mb-0">👤 Customer Information</h6>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <strong>Name:</strong>
              <p><?php echo htmlspecialchars($booking['customer_name']); ?></p>
            </div>
            <div class="col-md-6">
              <strong>Email:</strong>
              <p><?php echo htmlspecialchars($booking['customer_email']); ?></p>
            </div>
          </div>
          
          <div class="row mb-3">
            <div class="col-md-6">
              <strong>Phone:</strong>
              <p><?php echo htmlspecialchars($booking['customer_phone']); ?></p>
            </div>
            <div class="col-md-6">
              <strong>Age:</strong>
              <p><?php echo $booking['customer_age']; ?> years old</p>
            </div>
          </div>
          
          <div class="row mb-3">
            <div class="col-md-6">
              <strong>License Number:</strong>
              <p><?php echo htmlspecialchars($booking['license_number']); ?></p>
            </div>
            <div class="col-md-6">
              <strong>License Expiry:</strong>
              <p><?php echo date('M d, Y', strtotime($booking['license_expiry'])); ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header" style="background-color: #666; color: white;">
          <h6 class="mb-0">⚙️ Admin Actions</h6>
        </div>
        <div class="card-body">
          <form method="POST">
            <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
            
            <div class="mb-3">
              <label class="form-label">Update Status</label>
              <select class="form-select" name="status" required>
                <option value="Pending" <?php echo $booking['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="Confirmed" <?php echo $booking['status'] == 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                <option value="Active" <?php echo $booking['status'] == 'Active' ? 'selected' : ''; ?>>Active</option>
                <option value="Completed" <?php echo $booking['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                <option value="Cancelled" <?php echo $booking['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
              </select>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Message to Customer</label>
              <textarea class="form-control" name="admin_notes" rows="4" placeholder="e.g., Your car is ready for pickup..."><?php echo htmlspecialchars($booking['admin_notes'] ?? ''); ?></textarea>
            </div>
            
            <button type="submit" name="update_status" class="btn w-100" style="background-color: #666; color: white;">
              <i class="bi bi-check-circle"></i> Update & Notify Customer
            </button>
          </form>
        </div>
      </div>
      
      <div class="card mt-3">
        <div class="card-header bg-light">
          <h6 class="mb-0">💰 Payment Details</h6>
        </div>
        <div class="card-body">
          <p><strong>Payment Method:</strong><br><?php echo htmlspecialchars($booking['payment_method']); ?></p>
          <p><strong>Payment Status:</strong><br>
            <span class="badge bg-<?php echo $booking['payment_status'] == 'Paid' ? 'success' : 'warning'; ?>">
              <?php echo $booking['payment_status']; ?>
            </span>
          </p>
        </div>
      </div>
    </div>
  </div>
</section>
