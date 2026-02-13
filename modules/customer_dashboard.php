<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$customer_username = $_SESSION['username'];
$customer_info = $conn->query("SELECT * FROM customers WHERE username='$customer_username'")->fetch_assoc();
$customer_name = $customer_info['full_name'];
$customer_email = $customer_info['email'];
$my_bookings = [];
$result = $conn->query("SELECT * FROM car_rentals WHERE customer_email='$customer_email' OR customer_name='$customer_name' ORDER BY created_at DESC LIMIT 5");
while($row = $result->fetch_assoc()) {
    $my_bookings[] = $row;
}
$is_first_time = (count($my_bookings) == 0);
$car_sales = [];
$today = date('Y-m-d');
$result = $conn->query("SELECT cs.*, c.image FROM car_sales cs LEFT JOIN cars c ON cs.car_model = c.name WHERE cs.status='Active' AND cs.sale_start <= '$today' AND cs.sale_end >= '$today' ORDER BY cs.discount_percentage DESC");
while($row = $result->fetch_assoc()) {
    $car_sales[] = $row;
}
$featured_cars = [];
$result = $conn->query("SELECT * FROM cars WHERE status='Active' AND is_featured=1");
while($row = $result->fetch_assoc()) {
    $featured_cars[] = $row;
}
$first_time_promo = null;
if ($is_first_time) {
    $result = $conn->query("SELECT * FROM promo_codes WHERE status='Active' AND for_first_time_only=1 AND valid_from <= '$today' AND valid_until >= '$today' LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $first_time_promo = $result->fetch_assoc();
    }
}
$total_bookings = count($my_bookings);
$active_bookings = count(array_filter($my_bookings, function($b) { return in_array($b['status'], ['Confirmed', 'Active']); }));
$total_spent = array_sum(array_column($my_bookings, 'total_amount'));

$conn->close();
?>

<style>
.premium-dashboard { max-width: 1400px; margin: 0 auto; }
.premium-header { background: linear-gradient(135deg, #0a2540 0%, #1a3a5a 100%); color: white; padding: 24px; border-radius: 16px; margin-bottom: 24px; box-shadow: 0 4px 12px rgba(10, 37, 64, 0.15); }
.stat-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s; border: 1px solid #f0f0f0; }
.stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
.stat-icon { width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 16px; }
.section-card { background: white; border-radius: 16px; padding: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border: 1px solid #f0f0f0; margin-bottom: 24px; overflow: hidden; }
.section-header { background: #0a2540; color: white; padding: 16px 24px; font-weight: 600; font-size: 1rem; }
.car-card-horizontal { background: white; border-radius: 12px; border: 1px solid #e8e8e8; width: 270px; flex-shrink: 0; transition: all 0.3s; overflow: hidden; }
.car-card-horizontal:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,0.12); border-color: #0a2540; }
.car-img { width: 100%; height: 160px; object-fit: cover; }
.discount-badge { position: absolute; top: 12px; right: 12px; background: #dc3545; color: white; padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 0.85rem; box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3); }
.promo-banner { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25); }
.booking-card { background: white; border-radius: 12px; padding: 20px; border: 1px solid #e8e8e8; margin-bottom: 16px; transition: all 0.3s; }
.booking-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-color: #0a2540; }
.btn-premium { background: #0a2540; color: white; border: none; padding: 12px 24px; border-radius: 10px; font-weight: 600; transition: all 0.3s; }
.btn-premium:hover { background: #1a3a5a; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(10, 37, 64, 0.3); color: white; }
.btn-outline-premium { background: white; color: #0a2540; border: 2px solid #0a2540; padding: 10px 20px; border-radius: 10px; font-weight: 600; transition: all 0.3s; }
.btn-outline-premium:hover { background: #0a2540; color: white; }
</style>

<div class="premium-dashboard">
  <div class="premium-header">
    <h1 class="mb-2" style="font-size: 1.75rem; font-weight: 700;">Welcome back, <?php echo htmlspecialchars($customer_info['full_name']); ?>! 👋</h1>
    <p class="mb-0" style="opacity: 0.9;">Manage your bookings and discover amazing car rental deals</p>
  </div>

  <?php if ($first_time_promo): ?>
  <div class="promo-banner">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <h4 class="mb-2" style="font-weight: 700;">🎉 Welcome Bonus!</h4>
        <p class="mb-2">Use code <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 8px; font-weight: 700; font-size: 1.25rem;"><?php echo $first_time_promo['code']; ?></span></p>
        <p class="mb-0" style="opacity: 0.95;">Get <?php echo $first_time_promo['discount_type'] == 'percentage' ? $first_time_promo['discount_value'] . '% OFF' : '₱' . number_format($first_time_promo['discount_value'], 2) . ' OFF'; ?> on your first rental!</p>
      </div>
      <a href="index.php?page=car_rental" class="btn btn-light btn-lg" style="font-weight: 600;">Book Now</a>
    </div>
  </div>
  <?php endif; ?>

  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="stat-card">
        <div class="stat-icon" style="background: rgba(10, 37, 64, 0.1); color: #0a2540;"><i class="bi bi-calendar-check"></i></div>
        <h3 class="mb-1" style="font-weight: 700; color: #0a2540;"><?php echo $total_bookings; ?></h3>
        <p class="mb-0 text-muted">Total Bookings</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-card">
        <div class="stat-icon" style="background: rgba(25, 135, 84, 0.1); color: #198754;"><i class="bi bi-check-circle"></i></div>
        <h3 class="mb-1" style="font-weight: 700; color: #198754;"><?php echo $active_bookings; ?></h3>
        <p class="mb-0 text-muted">Active Bookings</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-card">
        <div class="stat-icon" style="background: rgba(102, 126, 234, 0.1); color: #667eea;"><i class="bi bi-wallet2"></i></div>
        <h3 class="mb-1" style="font-weight: 700; color: #667eea;">₱<?php echo number_format($total_spent, 2); ?></h3>
        <p class="mb-0 text-muted">Total Spent</p>
      </div>
    </div>
  </div>

  <?php if (!empty($car_sales)): ?>
  <div class="section-card">
    <div class="section-header"><i class="bi bi-tag-fill me-2"></i>Special Car Sales</div>
    <div class="p-4">
      <div class="d-flex gap-3 overflow-auto pb-2">
        <?php foreach($car_sales as $sale): ?>
        <div class="car-card-horizontal">
          <div class="position-relative">
            <div class="discount-badge"><?php echo $sale['discount_percentage']; ?>% OFF</div>
            <?php if ($sale['image'] && file_exists($sale['image'])): ?>
            <img src="<?php echo $sale['image']; ?>" class="car-img" alt="<?php echo htmlspecialchars($sale['car_model']); ?>">
            <?php else: ?>
            <div class="car-img" style="background: #f5f5f5; display: flex; align-items: center; justify-content: center;"><i class="bi bi-car-front" style="font-size: 48px; color: #ccc;"></i></div>
            <?php endif; ?>
          </div>
          <div class="p-3">
            <h6 class="mb-2" style="font-weight: 700; color: #0a2540;"><?php echo htmlspecialchars($sale['car_model']); ?></h6>
            <div class="mb-3">
              <span class="text-muted" style="text-decoration: line-through; font-size: 0.9rem;">₱<?php echo number_format($sale['original_price'], 2); ?></span>
              <h4 class="mb-0" style="color: #dc3545; font-weight: 700;">₱<?php echo number_format($sale['sale_price'], 2); ?><small class="text-muted" style="font-size: 0.85rem;">/day</small></h4>
            </div>
            <p class="mb-3 text-muted" style="font-size: 0.85rem;"><i class="bi bi-clock"></i> Ends <?php echo date('M d, Y', strtotime($sale['sale_end'])); ?></p>
            <a href="index.php?page=car_rental&car=<?php echo urlencode($sale['car_model']); ?>" class="btn btn-premium w-100"><i class="bi bi-cart-plus me-2"></i>Book Now</a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php if (!empty($featured_cars)): ?>
  <div class="section-card">
    <div class="section-header"><i class="bi bi-star-fill me-2"></i>Featured Cars</div>
    <div class="p-4">
      <div class="d-flex gap-3 overflow-auto pb-2">
        <?php foreach($featured_cars as $car): ?>
        <div class="car-card-horizontal">
          <?php if ($car['image'] && file_exists($car['image'])): ?>
          <img src="<?php echo $car['image']; ?>" class="car-img" alt="<?php echo htmlspecialchars($car['name']); ?>">
          <?php else: ?>
          <div class="car-img" style="background: #f5f5f5; display: flex; align-items: center; justify-content: center;"><i class="bi bi-car-front" style="font-size: 48px; color: #ccc;"></i></div>
          <?php endif; ?>
          <div class="p-3">
            <span class="badge" style="background: rgba(10, 37, 64, 0.1); color: #0a2540; font-size: 0.75rem; margin-bottom: 8px;"><?php echo $car['type']; ?></span>
            <h6 class="mb-2" style="font-weight: 700; color: #0a2540;"><?php echo htmlspecialchars($car['name']); ?></h6>
            <?php if ($car['features']): ?>
            <div class="mb-3">
              <?php foreach(array_slice(explode(',', $car['features']), 0, 2) as $feature): ?>
              <span class="badge bg-light text-dark me-1 mb-1" style="font-size: 0.75rem;"><?php echo trim($feature); ?></span>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <h4 class="mb-3" style="color: #0a2540; font-weight: 700;">₱<?php echo number_format($car['daily_rate'], 2); ?><small class="text-muted" style="font-size: 0.85rem;">/day</small></h4>
            <a href="index.php?page=car_rental&car=<?php echo urlencode($car['name']); ?>" class="btn btn-outline-premium w-100"><i class="bi bi-arrow-right me-2"></i>View Details</a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="section-card">
        <div class="section-header d-flex justify-content-between align-items-center">
          <span><i class="bi bi-clock-history me-2"></i>Recent Bookings</span>
          <a href="index.php?page=my_bookings" class="btn btn-sm btn-light">View All</a>
        </div>
        <div class="p-4">
          <?php if (empty($my_bookings)): ?>
          <div class="text-center py-5">
            <i class="bi bi-inbox display-1 text-muted"></i>
            <h5 class="mt-3">No bookings yet</h5>
            <p class="text-muted">Start by booking your first car rental!</p>
            <a href="index.php?page=car_rental" class="btn btn-premium mt-2"><i class="bi bi-car-front me-2"></i>Book Now</a>
          </div>
          <?php else: ?>
          <?php foreach($my_bookings as $booking): ?>
          <div class="booking-card">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <h6 class="mb-1" style="font-weight: 700; color: #0a2540;"><?php echo htmlspecialchars($booking['car_model']); ?></h6>
                <small class="text-muted"><?php echo $booking['booking_id']; ?></small>
              </div>
              <span class="badge status-<?php echo strtolower($booking['status']); ?>"><?php echo $booking['status']; ?></span>
            </div>
            <div class="row g-2 text-muted" style="font-size: 0.9rem;">
              <div class="col-6"><i class="bi bi-calendar3"></i> <?php echo date('M d, Y', strtotime($booking['pickup_date'])); ?></div>
              <div class="col-6"><i class="bi bi-clock"></i> <?php echo $booking['rental_days']; ?> days</div>
              <div class="col-12"><strong style="color: #0a2540; font-size: 1.1rem;">₱<?php echo number_format($booking['total_amount'], 2); ?></strong></div>
            </div>
          </div>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="section-card mb-3">
        <div class="section-header"><i class="bi bi-lightning-charge me-2"></i>Quick Actions</div>
        <div class="p-3">
          <a href="index.php?page=car_rental" class="btn btn-premium w-100 mb-2"><i class="bi bi-car-front me-2"></i>Book a Car</a>
          <a href="index.php?page=my_bookings" class="btn btn-outline-premium w-100 mb-2"><i class="bi bi-journal-text me-2"></i>My Bookings</a>
          <a href="index.php?page=my_profile" class="btn btn-outline-premium w-100"><i class="bi bi-person-circle me-2"></i>My Profile</a>
        </div>
      </div>
    </div>
  </div>
</div>
