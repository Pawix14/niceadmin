<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get customer info
$customer_username = $_SESSION['username'];
$customer_info = $conn->query("SELECT * FROM customers WHERE username='$customer_username'")->fetch_assoc();
$customer_name = $customer_info['full_name'];
$customer_email = $customer_info['email'];

// Get all customer bookings
$my_bookings = [];
$result = $conn->query("SELECT * FROM car_rentals WHERE customer_email='$customer_email' OR customer_name='$customer_name' ORDER BY created_at DESC");
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
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
          <h6 class="mb-0"><i class="bi bi-journal-text me-2"></i>My Booking History</h6>
          <a href="index.php?page=car_rental" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle me-2"></i>New Booking
          </a>
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
                  <td><strong class="text-success">₱<?php echo number_format($booking['total_amount'], 2); ?></strong></td>
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
