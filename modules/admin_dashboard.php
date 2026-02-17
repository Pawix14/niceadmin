<?php
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');

// Get statistics
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM car_rentals")->fetch_assoc()['count'];
$active_rentals = $conn->query("SELECT COUNT(*) as count FROM car_rentals WHERE status='Active'")->fetch_assoc()['count'];
$pending_approvals = $conn->query("SELECT COUNT(*) as count FROM car_rentals WHERE status='Pending'")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(total_amount) as total FROM car_rentals WHERE status IN ('Confirmed', 'Active', 'Completed')")->fetch_assoc()['total'] ?? 0;

// Monthly revenue (last 6 months)
$monthly_data = [];
for($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $revenue = $conn->query("SELECT SUM(total_amount) as total FROM car_rentals WHERE DATE_FORMAT(created_at, '%Y-%m')='$month' AND status IN ('Confirmed', 'Active', 'Completed')")->fetch_assoc()['total'] ?? 0;
    $monthly_data[] = ['month' => date('M Y', strtotime("-$i months")), 'revenue' => $revenue];
}

// Popular cars
$popular_cars = $conn->query("SELECT car_model, COUNT(*) as bookings FROM car_rentals GROUP BY car_model ORDER BY bookings DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

// Recent bookings
$recent_bookings = $conn->query("SELECT * FROM car_rentals ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

// Booking status breakdown
$status_breakdown = $conn->query("SELECT status, COUNT(*) as count FROM car_rentals GROUP BY status")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<div class="pagetitle">
  <h1>📊 Dashboard</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">Dashboard</li>
    </ol>
  </nav>
</div>

<section class="section dashboard">
  <!-- Statistics Cards -->
  <div class="row">
    <div class="col-lg-3 col-md-6 mb-4">
      <div class="card" style="border-left: 4px solid #3b82f6;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="text-muted mb-1">Total Bookings</h6>
              <h3 class="mb-0"><?php echo number_format($total_bookings); ?></h3>
            </div>
            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
              <i class="bi bi-calendar-check" style="font-size: 28px; color: white;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
      <div class="card" style="border-left: 4px solid #10b981;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="text-muted mb-1">Active Rentals</h6>
              <h3 class="mb-0"><?php echo number_format($active_rentals); ?></h3>
            </div>
            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
              <i class="bi bi-car-front" style="font-size: 28px; color: white;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
      <div class="card" style="border-left: 4px solid #f59e0b;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="text-muted mb-1">Pending Approvals</h6>
              <h3 class="mb-0"><?php echo number_format($pending_approvals); ?></h3>
            </div>
            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
              <i class="bi bi-clock-history" style="font-size: 28px; color: white;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
      <div class="card" style="border-left: 4px solid #8b5cf6;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="text-muted mb-1">Total Revenue</h6>
              <h3 class="mb-0">₱<?php echo number_format($total_revenue, 0); ?></h3>
            </div>
            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
              <i class="bi bi-currency-dollar" style="font-size: 28px; color: white;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Revenue Chart -->
    <div class="col-lg-8 mb-4">
      <div class="card">
        <div class="card-header" style="background-color: #f8fafc;">
          <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Revenue Trend (Last 6 Months)</h6>
        </div>
        <div class="card-body">
          <canvas id="revenueChart" height="80"></canvas>
        </div>
      </div>
    </div>

    <!-- Booking Status Breakdown -->
    <div class="col-lg-4 mb-4">
      <div class="card">
        <div class="card-header" style="background-color: #f8fafc;">
          <h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Booking Status</h6>
        </div>
        <div class="card-body">
          <canvas id="statusChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Popular Cars -->
    <div class="col-lg-6 mb-4">
      <div class="card">
        <div class="card-header" style="background-color: #f8fafc;">
          <h6 class="mb-0"><i class="bi bi-star me-2"></i>Most Popular Cars</h6>
        </div>
        <div class="card-body">
          <?php foreach($popular_cars as $car): ?>
          <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
            <div>
              <h6 class="mb-0"><?php echo htmlspecialchars($car['car_model']); ?></h6>
              <small class="text-muted"><?php echo $car['bookings']; ?> bookings</small>
            </div>
            <div class="progress" style="width: 100px; height: 8px;">
              <div class="progress-bar bg-primary" style="width: <?php echo ($car['bookings']/$popular_cars[0]['bookings'])*100; ?>%;"></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Recent Bookings -->
    <div class="col-lg-6 mb-4">
      <div class="card">
        <div class="card-header" style="background-color: #f8fafc;">
          <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Bookings</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Booking ID</th>
                  <th>Customer</th>
                  <th>Car</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($recent_bookings as $booking): ?>
                <tr>
                  <td><small><?php echo $booking['booking_id']; ?></small></td>
                  <td><small><?php echo htmlspecialchars($booking['customer_name']); ?></small></td>
                  <td><small><?php echo htmlspecialchars($booking['car_model']); ?></small></td>
                  <td><span class="badge bg-<?php echo $booking['status']=='Confirmed'?'success':($booking['status']=='Pending'?'warning':'secondary'); ?>"><?php echo $booking['status']; ?></span></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($monthly_data, 'month')); ?>,
        datasets: [{
            label: 'Revenue (₱)',
            data: <?php echo json_encode(array_column($monthly_data, 'revenue')); ?>,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₱' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_column($status_breakdown, 'status')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($status_breakdown, 'count')); ?>,
            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>
