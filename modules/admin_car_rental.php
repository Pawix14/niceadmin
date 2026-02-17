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

// Send reminder
if(isset($_GET['remind'])) {
    $booking_id = intval($_GET['remind']);
    $booking = $conn->query("SELECT * FROM car_rentals WHERE id=$booking_id")->fetch_assoc();
    if($booking) {
        $insert = $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id, is_read, created_at) 
            VALUES ('customer', '{$booking['customer_email']}', 'Return Reminder', 
            'Reminder: Your rental for {$booking['car_model']} is ending on {$booking['dropoff_date']}. Please return the car on time.', 
            '{$booking['booking_id']}', 0, NOW())");
        if($insert) {
            $message = '✅ Reminder sent to customer!';
            $message_type = 'success';
        } else {
            $message = '❌ Error: ' . $conn->error;
            $message_type = 'danger';
        }
    }
}

// Get all cars with booking count
$cars = [];
$result = $conn->query("SELECT c.*, 
    (SELECT COUNT(*) FROM car_rentals cr WHERE cr.car_model = c.name AND cr.status IN ('Confirmed', 'Pending')) as active_bookings
    FROM cars c 
    ORDER BY c.type, c.name");

if ($result) {
    while($row = $result->fetch_assoc()) {
        $cars[] = $row;
    }
}

// Get active sales
$active_sales = [];
$sales_result = $conn->query("SELECT car_model, sale_price, discount_percentage FROM car_sales WHERE status='Active' AND CURDATE() BETWEEN sale_start AND sale_end");
if ($sales_result) {
    while($sale = $sales_result->fetch_assoc()) {
        $active_sales[$sale['car_model']] = $sale;
    }
}

// Get active bookings
$bookings = $conn->query("SELECT * FROM car_rentals WHERE status IN ('Confirmed', 'Pending') ORDER BY dropoff_date ASC")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<div class="pagetitle">
  <h1>🚗 Car Rental Overview</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">Car Rental</li>
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

  <div class="card mb-4">
    <div class="card-header" style="background-color: #0a2540; color: white;">
      <h6 class="mb-0">📋 Active Rentals</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Booking ID</th>
              <th>Customer</th>
              <th>Car</th>
              <th>Return Date</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($bookings as $booking): 
              $days_left = (strtotime($booking['dropoff_date']) - time()) / 86400;
            ?>
            <tr>
              <td><?php echo $booking['booking_id']; ?></td>
              <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
              <td><?php echo htmlspecialchars($booking['car_model']); ?></td>
              <td>
                <?php echo date('M d, Y', strtotime($booking['dropoff_date'])); ?>
                <?php if($days_left <= 2 && $days_left >= 0): ?>
                <span class="badge bg-warning text-dark">Due Soon</span>
                <?php endif; ?>
              </td>
              <td><span class="badge bg-<?php echo $booking['status']=='Confirmed'?'success':'warning'; ?>"><?php echo $booking['status']; ?></span></td>
              <td>
                <a href="?page=admin_car_rental&remind=<?php echo $booking['id']; ?>" class="btn btn-sm btn-warning" onclick="return confirm('Send reminder to customer?')"><i class="bi bi-bell"></i> Remind</a>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($bookings)): ?>
            <tr><td colspan="6" class="text-center text-muted">No active rentals</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header bg-light">
      <h6 class="mb-0">🚙 All Available Cars</h6>
    </div>
    <div class="card-body">
      <div class="row g-4">
        <?php foreach($cars as $car): ?>
        <div class="col-md-6 col-lg-4">
          <div class="card h-100 border">
            <div class="position-relative">
              <?php if ($car['image'] && file_exists($car['image'])): ?>
              <img src="<?php echo $car['image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($car['name']); ?>" style="height: 200px; object-fit: cover;">
              <?php else: ?>
              <div style="height: 200px; background: #ddd; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-car-front" style="font-size: 48px; color: #999;"></i>
              </div>
              <?php endif; ?>
              
              <span class="position-absolute top-0 start-0 badge bg-secondary m-2"><?php echo $car['type']; ?></span>
              
              <?php if (isset($active_sales[$car['name']])): ?>
              <span class="position-absolute top-0 end-0 badge bg-danger m-2">
                <?php echo $active_sales[$car['name']]['discount_percentage']; ?>% OFF
              </span>
              <?php endif; ?>
            </div>
            
            <div class="card-body">
              <h6 class="card-title"><?php echo htmlspecialchars($car['name']); ?></h6>
              
              <?php if ($car['features']): ?>
              <div class="mb-3">
                <?php 
                $features = explode(',', $car['features']);
                foreach($features as $feature): 
                ?>
                <span class="badge bg-light text-dark me-1 mb-1"><?php echo trim($feature); ?></span>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>
              
              <div class="mb-3">
                <?php if (isset($active_sales[$car['name']])): ?>
                <div>
                  <small class="text-muted"><del>₱<?php echo number_format($car['daily_rate'], 2); ?></del></small>
                  <h5 class="text-danger mb-0">₱<?php echo number_format($active_sales[$car['name']]['sale_price'], 2); ?><small class="text-muted">/day</small></h5>
                </div>
                <?php else: ?>
                <h5 class="text-primary mb-0">₱<?php echo number_format($car['daily_rate'], 2); ?><small class="text-muted">/day</small></h5>
                <?php endif; ?>
              </div>
              
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <?php if ($car['status'] == 'Active'): ?>
                    <?php if ($car['active_bookings'] > 0): ?>
                    <span class="badge bg-warning text-dark">
                      <i class="bi bi-calendar-check"></i> <?php echo $car['active_bookings']; ?> Booking<?php echo $car['active_bookings'] > 1 ? 's' : ''; ?>
                    </span>
                    <?php else: ?>
                    <span class="badge bg-success">
                      <i class="bi bi-check-circle"></i> Available
                    </span>
                    <?php endif; ?>
                    <?php if ($car['is_featured']): ?>
                    <span class="badge bg-warning text-dark ms-1">
                      <i class="bi bi-star-fill"></i> Featured
                    </span>
                    <?php endif; ?>
                  <?php else: ?>
                  <span class="badge bg-secondary">
                    <i class="bi bi-x-circle"></i> Inactive
                  </span>
                  <?php endif; ?>
                </div>
                
                <a href="index.php?page=cars&edit_car=<?php echo $car['id']; ?>" class="btn btn-sm" style="background-color: #666; color: white;">
                  <i class="bi bi-pencil"></i> Edit
                </a>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        
        <?php if (empty($cars)): ?>
        <div class="col-12">
          <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No cars available. <a href="index.php?page=cars">Add a car</a> to get started.
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<style>
.card {
  transition: transform 0.2s;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
