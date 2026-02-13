<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$stats = [];
$result = $conn->query("SELECT COUNT(*) as total FROM car_rentals");
$stats['car_bookings'] = $result->fetch_assoc()['total'];
$stats['total_bookings'] = $stats['car_bookings'];

$result = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM car_rentals");
$stats['car_revenue'] = $result->fetch_assoc()['total'];
$stats['total_revenue'] = $stats['car_revenue'];

$today = date('Y-m-d');
$result = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM car_rentals WHERE DATE(created_at) = '$today'");
$stats['today_revenue'] = $result->fetch_assoc()['total'];

$recent_bookings = [];
$result = $conn->query("SELECT booking_id, customer_name, car_model, total_amount, status, created_at as booking_date FROM car_rentals ORDER BY created_at DESC LIMIT 5");
while($row = $result->fetch_assoc()) {
    $recent_bookings[] = $row;
}

$result = $conn->query("SELECT COUNT(*) as total FROM car_rentals WHERE status = 'Pending'");
$stats['pending_bookings'] = $result->fetch_assoc()['total'];

$car_sales = [];
$result = $conn->query("SELECT cs.*, c.image FROM car_sales cs LEFT JOIN cars c ON cs.car_model = c.name WHERE cs.status='Active' AND cs.sale_start <= '$today' AND cs.sale_end >= '$today' ORDER BY cs.discount_percentage DESC");
while($row = $result->fetch_assoc()) {
    $car_sales[] = $row;
}

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
.booking-card { background: white; border-radius: 12px; padding: 20px; border: 1px solid #e8e8e8; margin-bottom: 16px; transition: all 0.3s; }
.booking-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-color: #0a2540; }
.btn-premium { background: #0a2540; color: white; border: none; padding: 12px 24px; border-radius: 10px; font-weight: 600; transition: all 0.3s; }
.btn-premium:hover { background: #1a3a5a; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(10, 37, 64, 0.3); color: white; }
.btn-outline-premium { background: white; color: #0a2540; border: 2px solid #0a2540; padding: 10px 20px; border-radius: 10px; font-weight: 600; transition: all 0.3s; }
.btn-outline-premium:hover { background: #0a2540; color: white; }
.action-card { background: white; border-radius: 12px; padding: 16px; border: 1px solid #e8e8e8; transition: all 0.3s; text-decoration: none; display: block; }
.action-card:hover { border-color: #0a2540; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transform: translateX(4px); text-decoration: none; }
</style>

<div class="premium-dashboard">
  <div class="premium-header">
    <h1 class="mb-2" style="font-size: 1.75rem; font-weight: 700;">Admin Dashboard 🚗</h1>
    <p class="mb-0" style="opacity: 0.9;">Monitor bookings, manage sales, and oversee operations</p>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="stat-card">
        <div class="stat-icon" style="background: rgba(10, 37, 64, 0.1); color: #0a2540;"><i class="bi bi-journal-check"></i></div>
        <h3 class="mb-1" style="font-weight: 700; color: #0a2540;"><?php echo $stats['total_bookings']; ?></h3>
        <p class="mb-0 text-muted">Total Bookings</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat-card">
        <div class="stat-icon" style="background: rgba(25, 135, 84, 0.1); color: #198754;"><i class="bi bi-currency-dollar"></i></div>
        <h3 class="mb-1" style="font-weight: 700; color: #198754;">₱<?php echo number_format($stats['total_revenue'], 2); ?></h3>
        <p class="mb-0 text-muted">Total Revenue</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat-card">
        <div class="stat-icon" style="background: rgba(255, 193, 7, 0.1); color: #ffc107;"><i class="bi bi-clock-history"></i></div>
        <h3 class="mb-1" style="font-weight: 700; color: #ffc107;"><?php echo $stats['pending_bookings']; ?></h3>
        <p class="mb-0 text-muted">Pending Bookings</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat-card">
        <div class="stat-icon" style="background: rgba(102, 126, 234, 0.1); color: #667eea;"><i class="bi bi-cash-stack"></i></div>
        <h3 class="mb-1" style="font-weight: 700; color: #667eea;">₱<?php echo number_format($stats['today_revenue'], 2); ?></h3>
        <p class="mb-0 text-muted">Today's Revenue</p>
      </div>
    </div>
  </div>

  <?php if (!empty($car_sales)): ?>
  <div class="section-card">
    <div class="section-header"><i class="bi bi-tag-fill me-2"></i>Active Car Sales</div>
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
            <a href="index.php?page=car_sales&edit_sale=<?php echo $sale['id']; ?>" class="btn btn-outline-premium w-100"><i class="bi bi-pencil me-2"></i>Edit Sale</a>
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
          <a href="index.php?page=all_bookings" class="btn btn-sm btn-light">View All</a>
        </div>
        <div class="p-4">
          <?php if (empty($recent_bookings)): ?>
          <div class="text-center py-5">
            <i class="bi bi-inbox display-1 text-muted"></i>
            <h5 class="mt-3">No bookings yet</h5>
            <p class="text-muted">Bookings will appear here</p>
          </div>
          <?php else: ?>
          <?php foreach($recent_bookings as $booking): ?>
          <div class="booking-card">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <h6 class="mb-1" style="font-weight: 700; color: #0a2540;"><?php echo htmlspecialchars($booking['car_model']); ?></h6>
                <small class="text-muted"><?php echo $booking['booking_id']; ?> • <?php echo htmlspecialchars($booking['customer_name']); ?></small>
              </div>
              <span class="badge status-<?php echo strtolower($booking['status']); ?>"><?php echo $booking['status']; ?></span>
            </div>
            <div class="row g-2 text-muted" style="font-size: 0.9rem;">
              <div class="col-6"><i class="bi bi-calendar3"></i> <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></div>
              <div class="col-6"><strong style="color: #0a2540; font-size: 1.1rem;">₱<?php echo number_format($booking['total_amount'], 2); ?></strong></div>
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
          <a href="index.php?page=admin_car_rental" class="action-card mb-2">
            <div class="d-flex align-items-center gap-3">
              <div style="width: 40px; height: 40px; background: rgba(10, 37, 64, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-car-front" style="color: #0a2540; font-size: 1.2rem;"></i>
              </div>
              <div>
                <h6 class="mb-0" style="color: #0a2540; font-weight: 600;">New Booking</h6>
                <small class="text-muted">Create car rental</small>
              </div>
            </div>
          </a>
          <a href="index.php?page=car_sales" class="action-card mb-2">
            <div class="d-flex align-items-center gap-3">
              <div style="width: 40px; height: 40px; background: rgba(220, 53, 69, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-tag" style="color: #dc3545; font-size: 1.2rem;"></i>
              </div>
              <div>
                <h6 class="mb-0" style="color: #0a2540; font-weight: 600;">Car Sales</h6>
                <small class="text-muted">Manage promotions</small>
              </div>
            </div>
          </a>
          <a href="index.php?page=promo_codes" class="action-card mb-2">
            <div class="d-flex align-items-center gap-3">
              <div style="width: 40px; height: 40px; background: rgba(102, 126, 234, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-ticket-perforated" style="color: #667eea; font-size: 1.2rem;"></i>
              </div>
              <div>
                <h6 class="mb-0" style="color: #0a2540; font-weight: 600;">Promo Codes</h6>
                <small class="text-muted">Manage discounts</small>
              </div>
            </div>
          </a>
          <a href="index.php?page=agents" class="action-card mb-2">
            <div class="d-flex align-items-center gap-3">
              <div style="width: 40px; height: 40px; background: rgba(25, 135, 84, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-people" style="color: #198754; font-size: 1.2rem;"></i>
              </div>
              <div>
                <h6 class="mb-0" style="color: #0a2540; font-weight: 600;">Travel Agents</h6>
                <small class="text-muted">Manage agents</small>
              </div>
            </div>
          </a>
          <a href="index.php?page=commissions" class="action-card">
            <div class="d-flex align-items-center gap-3">
              <div style="width: 40px; height: 40px; background: rgba(255, 193, 7, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-cash-coin" style="color: #ffc107; font-size: 1.2rem;"></i>
              </div>
              <div>
                <h6 class="mb-0" style="color: #0a2540; font-weight: 600;">Commissions</h6>
                <small class="text-muted">View earnings</small>
              </div>
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
