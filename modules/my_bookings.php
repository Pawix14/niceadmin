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

// Add pickup columns if not exists
$conn->query("ALTER TABLE car_rentals ADD COLUMN IF NOT EXISTS pickup_status VARCHAR(20) DEFAULT 'Pending'");
$conn->query("ALTER TABLE car_rentals ADD COLUMN IF NOT EXISTS pickup_confirmed_at DATETIME");
$conn->query("ALTER TABLE car_rentals ADD COLUMN IF NOT EXISTS return_pickup_status VARCHAR(20) DEFAULT 'Pending'");
$conn->query("ALTER TABLE car_rentals ADD COLUMN IF NOT EXISTS return_pickup_confirmed_at DATETIME");

// Add actual_return_date column if not exists
$conn->query("ALTER TABLE car_rentals ADD COLUMN IF NOT EXISTS actual_return_date DATETIME");
$conn->query("ALTER TABLE car_rentals ADD COLUMN IF NOT EXISTS refund_amount DECIMAL(10,2) DEFAULT 0");
$conn->query("ALTER TABLE car_rentals ADD COLUMN IF NOT EXISTS actual_rental_days INT DEFAULT 0");

// Return car with refund calculation
if(isset($_GET['return'])) {
    $booking_id = intval($_GET['return']);
    
    // Get booking details
    $booking = $conn->query("SELECT * FROM car_rentals WHERE id=$booking_id")->fetch_assoc();
    
    if($booking) {
        $pickup_date = new DateTime($booking['pickup_date']);
        $dropoff_date = new DateTime($booking['dropoff_date']);
        $actual_return = new DateTime();
        
        // Calculate actual days used
        $actual_days = $pickup_date->diff($actual_return)->days + 1; // +1 to include pickup day
        $booked_days = $booking['rental_days'];
        
        $refund_amount = 0;
        
        // If returned early, calculate refund
        if($actual_days < $booked_days) {
            $unused_days = $booked_days - $actual_days;
            $daily_rate = $booking['daily_rate'];
            $insurance_per_day = $booking['insurance_fee'] / $booked_days;
            
            // Refund for unused days (daily rate + insurance per day)
            $refund_amount = $unused_days * ($daily_rate + $insurance_per_day);
            
            $message = "✅ Car returned successfully!<br>";
            $message .= "<strong>Actual days used:</strong> $actual_days days (Booked: $booked_days days)<br>";
            $message .= "<strong>Early return refund:</strong> ₱" . number_format($refund_amount, 2);
        } else {
            $message = "✅ Car returned successfully!<br>";
            $message .= "<strong>Actual days used:</strong> $actual_days days<br>";
            $message .= "No refund (returned on or after scheduled date)";
        }
        
        // Update booking and notify staff for pickup
        $conn->query("UPDATE car_rentals SET 
            status='Completed', 
            actual_return_date=NOW(), 
            actual_rental_days=$actual_days,
            refund_amount=$refund_amount,
            return_pickup_status='Ready'
            WHERE id=$booking_id");
        
        // Notify staff to pick up car
        $return_location = $booking['dropoff_location'];
        $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id, is_read) 
            VALUES ('staff', 'admin', 'Car Ready for Return Pickup', 'Customer returned car for booking {$booking['booking_id']}. Pick up at: $return_location', '{$booking['booking_id']}', 0)");
        
        $message .= "<br><small class='text-muted'>Staff has been notified to pick up the car at $return_location</small>";
        $message_type = 'success';
    }
}

// Quick rebook
if(isset($_GET['rebook'])) {
    $booking_id = intval($_GET['rebook']);
    $booking = $conn->query("SELECT * FROM car_rentals WHERE id=$booking_id")->fetch_assoc();
    if($booking) {
        $_SESSION['rebook_data'] = [
            'car_model' => $booking['car_model'],
            'car_type' => $booking['car_type'],
            'daily_rate' => $booking['daily_rate']
        ];
        header("Location: index.php?page=car_rental");
        exit();
    }
}

// Get customer info
$customer_username = $_SESSION['username'];
$customer_info = $conn->query("SELECT * FROM customers WHERE username='$customer_username'")->fetch_assoc();
$customer_name = $customer_info['full_name'];
$customer_email = $customer_info['email'];

// Filter handling
$filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : 'all';
$filter_query = "SELECT * FROM car_rentals WHERE (customer_email='$customer_email' OR customer_name='$customer_name')";

if($filter_status != 'all') {
    $filter_query .= " AND status='$filter_status'";
}
$filter_query .= " ORDER BY created_at DESC";

// Get all customer bookings
$my_bookings = [];
$result = $conn->query($filter_query);
while($row = $result->fetch_assoc()) {
    $my_bookings[] = $row;
}

$conn->close();
?>

<div class="pagetitle">
  <h1>My Bookings</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">My Bookings</li>
    </ol>
  </nav>
</div>

<section class="section">
  <?php if($message): ?>
  <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
          <h6 class="mb-0"><i class="bi bi-journal-text me-2"></i>My Booking History</h6>
          <div class="d-flex gap-2">
            <select class="form-select form-select-sm" onchange="window.location.href='?page=my_bookings&filter_status='+this.value" style="width:150px;">
              <option value="all" <?php echo $filter_status=='all'?'selected':''; ?>>All Status</option>
              <option value="Pending" <?php echo $filter_status=='Pending'?'selected':''; ?>>Pending</option>
              <option value="Confirmed" <?php echo $filter_status=='Confirmed'?'selected':''; ?>>Confirmed</option>
              <option value="Completed" <?php echo $filter_status=='Completed'?'selected':''; ?>>Completed</option>
              <option value="Cancelled" <?php echo $filter_status=='Cancelled'?'selected':''; ?>>Cancelled</option>
            </select>
            <a href="index.php?page=car_rental" class="btn btn-sm btn-primary">
              <i class="bi bi-plus-circle me-2"></i>New Booking
            </a>
          </div>
        </div>
        <div class="card-body">
          <?php if (empty($my_bookings)): ?>
          <div class="text-center py-5">
            <i class="bi bi-inbox display-1 text-muted"></i>
            <h5 class="mt-3">No bookings found</h5>
            <p class="text-muted">You haven't made any bookings yet. Start by booking a car!</p>
            <a href="index.php?page=car_rental" class="btn btn-primary mt-3">
              <i class="bi bi-car-front me-2"></i>Book a Car Now
            </a>
          </div>
          <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Booking ID</th>
                  <th>Car Details</th>
                  <th>Pickup</th>
                  <th>Drop-off</th>
                  <th>Duration</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th>Date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($my_bookings as $booking): ?>
                <tr>
                  <td><strong><?php echo $booking['booking_id']; ?></strong></td>
                  <td>
                    <strong><?php echo htmlspecialchars($booking['car_model']); ?></strong><br>
                    <small class="text-muted"><?php echo $booking['car_type']; ?></small>
                  </td>
                  <td>
                    <small>
                      <?php echo date('M d, Y', strtotime($booking['pickup_date'])); ?><br>
                      <?php echo date('h:i A', strtotime($booking['pickup_time'])); ?>
                    </small>
                  </td>
                  <td>
                    <small>
                      <?php echo date('M d, Y', strtotime($booking['dropoff_date'])); ?><br>
                      <?php echo date('h:i A', strtotime($booking['dropoff_time'])); ?>
                    </small>
                  </td>
                  <td><?php echo $booking['rental_days']; ?> days</td>
                  <td><strong class="text-success">₱<?php echo number_format($booking['total_amount'], 2); ?></strong>
                    <?php if($booking['refund_amount'] > 0): ?>
                    <br><small class="text-info">Refund: ₱<?php echo number_format($booking['refund_amount'], 2); ?></small>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="badge bg-<?php 
                      echo $booking['status'] == 'Confirmed' ? 'success' : 
                           ($booking['status'] == 'Active' ? 'primary' : 
                           ($booking['status'] == 'Pending' ? 'warning' : 
                           ($booking['status'] == 'Completed' ? 'info' : 'secondary'))); 
                    ?>">
                      <?php echo $booking['status']; ?>
                    </span>
                    <?php if ($booking['status'] == 'Pending'): ?>
                    <br><small class="text-muted">Awaiting admin review</small>
                    <?php endif; ?>
                  </td>
                  <td><small><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></small></td>
                  <td>
                    <?php if($booking['status'] == 'Confirmed' && $booking['pickup_status'] == 'Ready'): ?>
                    <button class="btn btn-sm btn-primary mb-1" onclick="confirmPickup('<?php echo $booking['booking_id']; ?>')">
                      <i class="bi bi-box-arrow-up-right"></i> Confirm Pickup
                    </button><br>
                    <?php endif; ?>
                    <?php if($booking['status'] == 'Confirmed' || $booking['status'] == 'Active'): ?>
                    <a href="index.php?page=extend_rental&booking_id=<?php echo $booking['booking_id']; ?>" class="btn btn-sm btn-warning mb-1">
                      <i class="bi bi-calendar-plus"></i> Extend
                    </a><br>
                    <?php endif; ?>
                    <?php if($booking['status'] == 'Active'): ?>
                    <a href="?page=my_bookings&return=<?php echo $booking['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Confirm car return?')"><i class="bi bi-check-circle"></i> Return Car</a>
                    <?php elseif($booking['status'] == 'Completed'): ?>
                    <a href="?page=car_reviews&booking_id=<?php echo $booking['booking_id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-star"></i> Review</a>
                    <a href="?page=my_bookings&rebook=<?php echo $booking['id']; ?>" class="btn btn-sm btn-primary"><i class="bi bi-arrow-repeat"></i> Rebook</a>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
function confirmPickup(bookingId) {
  if(confirm('Confirm that you have picked up the car?')) {
    fetch('modules/pickup_handler.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'action=confirm_pickup&booking_id=' + bookingId
    })
    .then(res => res.json())
    .then(data => {
      if(data.success) {
        alert('✅ ' + data.message);
        location.reload();
      }
    });
  }
}
</script>

<style>
.btn-primary {
  background-color: #666;
  border-color: #666;
}

.btn-primary:hover {
  background-color: #555;
  border-color: #555;
}
</style>
