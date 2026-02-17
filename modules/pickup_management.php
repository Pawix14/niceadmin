<?php
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');

// Add columns if not exist
$conn->query("ALTER TABLE car_rentals ADD COLUMN IF NOT EXISTS pickup_status VARCHAR(20) DEFAULT 'Pending'");
$conn->query("ALTER TABLE car_rentals ADD COLUMN IF NOT EXISTS return_pickup_status VARCHAR(20) DEFAULT 'Pending'");

// Get bookings that need pickup action
$pending_pickups = $conn->query("SELECT * FROM car_rentals WHERE status='Confirmed' AND pickup_status='Ready' ORDER BY pickup_date ASC");
$pending_returns = $conn->query("SELECT * FROM car_rentals WHERE status='Completed' AND return_pickup_status='Ready' ORDER BY actual_return_date DESC");

$conn->close();
?>

<div class="pagetitle">
  <h1>🚗 Pickup Management</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">Pickup Management</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <!-- Pending Customer Pickups -->
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h6 class="mb-0"><i class="bi bi-box-arrow-up-right me-2"></i>Cars Ready for Customer Pickup</h6>
        </div>
        <div class="card-body">
          <?php if($pending_pickups->num_rows == 0): ?>
          <div class="text-center py-4 text-muted">
            <i class="bi bi-check-circle display-4"></i>
            <p class="mt-2">No pending customer pickups</p>
          </div>
          <?php else: ?>
          <?php while($booking = $pending_pickups->fetch_assoc()): ?>
          <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <strong><?php echo $booking['booking_id']; ?></strong>
                <br><small class="text-muted"><?php echo $booking['customer_name']; ?></small>
              </div>
              <span class="badge bg-warning">Waiting Pickup</span>
            </div>
            <p class="mb-2">
              <strong><?php echo $booking['car_model']; ?></strong> (<?php echo $booking['car_type']; ?>)
            </p>
            <p class="mb-2">
              <i class="bi bi-geo-alt text-danger"></i> 
              <strong>Pickup Location:</strong> <?php echo htmlspecialchars($booking['pickup_location']); ?>
            </p>
            <p class="mb-2">
              <i class="bi bi-calendar"></i> 
              <?php echo date('M d, Y h:i A', strtotime($booking['pickup_date'] . ' ' . $booking['pickup_time'])); ?>
            </p>
            <small class="text-muted">Customer will confirm when they pick up the car</small>
          </div>
          <?php endwhile; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Pending Return Pickups (Staff Action) -->
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header bg-success text-white">
          <h6 class="mb-0"><i class="bi bi-box-arrow-in-down me-2"></i>Cars Ready for Staff Pickup (Returns)</h6>
        </div>
        <div class="card-body">
          <?php if($pending_returns->num_rows == 0): ?>
          <div class="text-center py-4 text-muted">
            <i class="bi bi-check-circle display-4"></i>
            <p class="mt-2">No pending return pickups</p>
          </div>
          <?php else: ?>
          <?php while($booking = $pending_returns->fetch_assoc()): ?>
          <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <strong><?php echo $booking['booking_id']; ?></strong>
                <br><small class="text-muted"><?php echo $booking['customer_name']; ?></small>
              </div>
              <span class="badge bg-info">Ready for Pickup</span>
            </div>
            <p class="mb-2">
              <strong><?php echo $booking['car_model']; ?></strong> (<?php echo $booking['car_type']; ?>)
            </p>
            <p class="mb-2">
              <i class="bi bi-geo-alt text-success"></i> 
              <strong>Return Location:</strong> <?php echo htmlspecialchars($booking['dropoff_location']); ?>
            </p>
            <p class="mb-2">
              <i class="bi bi-calendar"></i> 
              Returned: <?php echo date('M d, Y h:i A', strtotime($booking['actual_return_date'])); ?>
            </p>
            <button class="btn btn-success btn-sm w-100" onclick="confirmReturnPickup('<?php echo $booking['booking_id']; ?>')">
              <i class="bi bi-check-circle"></i> Confirm Pickup Complete
            </button>
          </div>
          <?php endwhile; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
function confirmReturnPickup(bookingId) {
  if(confirm('Confirm that you have picked up this returned car?')) {
    fetch('modules/pickup_handler.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'action=confirm_return_pickup&booking_id=' + bookingId
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
