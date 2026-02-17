<?php
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');

$stats = [];
$stats['total_bookings'] = $conn->query("SELECT COUNT(*) as count FROM car_rentals")->fetch_assoc()['count'];
$stats['pending_bookings'] = $conn->query("SELECT COUNT(*) as count FROM car_rentals WHERE status='Pending'")->fetch_assoc()['count'];
$stats['total_customers'] = $conn->query("SELECT COUNT(*) as count FROM customers")->fetch_assoc()['count'];
$stats['total_revenue'] = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM car_rentals WHERE payment_status='Paid'")->fetch_assoc()['total'];
$today = date('Y-m-d');
$stats['today_revenue'] = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM car_rentals WHERE DATE(created_at) = '$today'")->fetch_assoc()['total'];
$stats['pending_payments'] = $conn->query("SELECT COUNT(*) as count FROM car_rentals WHERE payment_status='Pending'")->fetch_assoc()['count'];

$recent_bookings = [];
$result = $conn->query("SELECT booking_id, customer_name, car_model, total_amount, status, created_at as booking_date FROM car_rentals ORDER BY created_at DESC LIMIT 5");
while($row = $result->fetch_assoc()) {
    $recent_bookings[] = $row;
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
.booking-card { background: white; border-radius: 12px; padding: 20px; border: 1px solid #e8e8e8; margin-bottom: 16px; transition: all 0.3s; }
.booking-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-color: #0a2540; }
.action-card { background: white; border-radius: 12px; padding: 16px; border: 1px solid #e8e8e8; transition: all 0.3s; text-decoration: none; display: block; }
.action-card:hover { border-color: #0a2540; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transform: translateX(4px); text-decoration: none; }
.status-pending { background: #ffc107; color: #000; padding: 4px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; }
.status-confirmed { background: #0dcaf0; color: #000; padding: 4px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; }
.status-completed { background: #198754; color: white; padding: 4px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; }
.status-cancelled { background: #dc3545; color: white; padding: 4px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; }
</style>

<div class="premium-dashboard">
  <div class="premium-header">
    <h1 class="mb-2" style="font-size: 1.75rem; font-weight: 700;">Staff Dashboard 🚗</h1>
    <p class="mb-0" style="opacity: 0.9;">Manage bookings, customers, and payments</p>
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
        <div class="stat-icon" style="background: rgba(255, 193, 7, 0.1); color: #ffc107;"><i class="bi bi-clock-history"></i></div>
        <h3 class="mb-1" style="font-weight: 700; color: #ffc107;"><?php echo $stats['pending_bookings']; ?></h3>
        <p class="mb-0 text-muted">Pending Bookings</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat-card">
        <div class="stat-icon" style="background: rgba(25, 135, 84, 0.1); color: #198754;"><i class="bi bi-people"></i></div>
        <h3 class="mb-1" style="font-weight: 700; color: #198754;"><?php echo $stats['total_customers']; ?></h3>
        <p class="mb-0 text-muted">Total Customers</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat-card">
        <div class="stat-icon" style="background: rgba(220, 53, 69, 0.1); color: #dc3545;"><i class="bi bi-credit-card"></i></div>
        <h3 class="mb-1" style="font-weight: 700; color: #dc3545;"><?php echo $stats['pending_payments']; ?></h3>
        <p class="mb-0 text-muted">Pending Payments</p>
      </div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="section-card">
        <div class="section-header d-flex justify-content-between align-items-center">
          <span><i class="bi bi-clock-history me-2"></i>Recent Bookings</span>
          <a href="index.php?page=bookings" class="btn btn-sm btn-light">View All</a>
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
          <a href="index.php?page=bookings" class="action-card mb-2">
            <div class="d-flex align-items-center gap-3">
              <div style="width: 40px; height: 40px; background: rgba(10, 37, 64, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-journal-check" style="color: #0a2540; font-size: 1.2rem;"></i>
              </div>
              <div>
                <h6 class="mb-0" style="color: #0a2540; font-weight: 600;">Manage Bookings</h6>
                <small class="text-muted">View all bookings</small>
              </div>
            </div>
          </a>
          <a href="index.php?page=admin_car_rental" class="action-card mb-2">
            <div class="d-flex align-items-center gap-3">
              <div style="width: 40px; height: 40px; background: rgba(220, 53, 69, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-car-front" style="color: #dc3545; font-size: 1.2rem;"></i>
              </div>
              <div>
                <h6 class="mb-0" style="color: #0a2540; font-weight: 600;">Car Rentals</h6>
                <small class="text-muted">Active rentals</small>
              </div>
            </div>
          </a>
          <a href="index.php?page=payments" class="action-card mb-2">
            <div class="d-flex align-items-center gap-3">
              <div style="width: 40px; height: 40px; background: rgba(102, 126, 234, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-credit-card" style="color: #667eea; font-size: 1.2rem;"></i>
              </div>
              <div>
                <h6 class="mb-0" style="color: #0a2540; font-weight: 600;">Payments</h6>
                <small class="text-muted">Process payments</small>
              </div>
            </div>
          </a>
          <a href="index.php?page=customers" class="action-card mb-2">
            <div class="d-flex align-items-center gap-3">
              <div style="width: 40px; height: 40px; background: rgba(25, 135, 84, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-people" style="color: #198754; font-size: 1.2rem;"></i>
              </div>
              <div>
                <h6 class="mb-0" style="color: #0a2540; font-weight: 600;">Customers</h6>
                <small class="text-muted">Manage customers</small>
              </div>
            </div>
          </a>
          <a href="index.php?page=maintenance" class="action-card">
            <div class="d-flex align-items-center gap-3">
              <div style="width: 40px; height: 40px; background: rgba(255, 193, 7, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-tools" style="color: #ffc107; font-size: 1.2rem;"></i>
              </div>
              <div>
                <h6 class="mb-0" style="color: #0a2540; font-weight: 600;">Maintenance</h6>
                <small class="text-muted">Car maintenance</small>
              </div>
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
