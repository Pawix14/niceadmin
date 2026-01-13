<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$all_commissions = [];
$result = $conn->query("
    SELECT 'travel' as type, b.booking_id as id, b.traveler_name as customer_name,
           'Travel' as booking_type, b.total_amount as amount,
           b.agent_commission as commission, b.agent_id, a.agent_name,
           a.commission_rate, b.status, b.booking_date
    FROM travel_bookings b
    LEFT JOIN travel_agents a ON b.agent_id = a.agent_id
    WHERE b.agent_id IS NOT NULL AND b.agent_commission > 0
");
while($row = $result->fetch_assoc()) {
    $all_commissions[] = $row;
}
$result = $conn->query("
    SELECT 'hotel' as type, b.booking_id as id, b.guest_name as customer_name,
           'Hotel' as booking_type, b.total_amount as amount,
           b.agent_commission as commission, b.agent_id, a.agent_name,
           a.commission_rate, b.status, b.booking_date
    FROM hotel_bookings b
    LEFT JOIN travel_agents a ON b.agent_id = a.agent_id
    WHERE b.agent_id IS NOT NULL AND b.agent_commission > 0
");
while($row = $result->fetch_assoc()) {
    $all_commissions[] = $row;
}

// Get flight commissions
$result = $conn->query("
    SELECT 'flight' as type, b.booking_id as id, b.passenger_name as customer_name,
           'Flight' as booking_type, b.total_amount as amount,
           b.agent_commission as commission, b.agent_id, a.agent_name,
           a.commission_rate, b.status, b.booking_date
    FROM flight_bookings b
    LEFT JOIN travel_agents a ON b.agent_id = a.agent_id
    WHERE b.agent_id IS NOT NULL AND b.agent_commission > 0
");
while($row = $result->fetch_assoc()) {
    $all_commissions[] = $row;
}

// Get TOUR commissions
$result = $conn->query("
    SELECT 'tour' as type, b.booking_id as id, b.participant_name as customer_name,
           'Tour' as booking_type, b.total_amount as amount,
           b.agent_commission as commission, b.agent_id, a.agent_name,
           a.commission_rate, b.status, b.booking_date
    FROM tour_bookings b
    LEFT JOIN travel_agents a ON b.agent_id = a.agent_id
    WHERE b.agent_id IS NOT NULL AND b.agent_commission > 0
");
while($row = $result->fetch_assoc()) {
    $all_commissions[] = $row;
}

// Get CAR RENTAL commissions (FIXED)
$result = $conn->query("
    SELECT 'car' as type, b.booking_id as id, b.customer_name,
           'Car Rental' as booking_type, b.total_amount as amount,
           b.agent_commission as commission, b.agent_id, a.agent_name,
           a.commission_rate, b.status, b.created_at as booking_date
    FROM car_rentals b
    LEFT JOIN travel_agents a ON b.agent_id = a.agent_id
    WHERE b.agent_id IS NOT NULL AND b.agent_commission > 0
");
while($row = $result->fetch_assoc()) {
    $all_commissions[] = $row;
}

// Sort by date
usort($all_commissions, function($a, $b) {
    return strtotime($b['booking_date']) - strtotime($a['booking_date']);
});

// Calculate totals
$total_commission = array_sum(array_column($all_commissions, 'commission'));
$pending_commission = 0;
$paid_commission = 0;

// Group by type for statistics
$type_totals = [
    'Travel' => 0,
    'Hotel' => 0,
    'Flight' => 0,
    'Tour' => 0,
    'Car Rental' => 0
];

foreach($all_commissions as $c) {
    // Update type totals
    if (isset($type_totals[$c['booking_type']])) {
        $type_totals[$c['booking_type']] += $c['commission'];
    }
    
    // Update pending/paid totals
    if (in_array($c['status'], ['Confirmed', 'Pending', 'Checked-in', 'Boarded', 'Active'])) {
        $pending_commission += $c['commission'];
    } elseif (in_array($c['status'], ['Completed', 'Checked-out', 'Returned'])) {
        $paid_commission += $c['commission'];
    }
}

$conn->close();
?>

<div class="pagetitle">
  <h1>Commissions</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">Commissions</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    
    <!-- Commission Stats -->
    <div class="col-lg-4">
      <div class="card stat-card bg-primary text-white">
        <div class="card-body text-center">
          <h5>Total Commission</h5>
          <h2>₱<?php echo number_format($total_commission, 2); ?></h2>
        </div>
      </div>
    </div>
    
    <div class="col-lg-4">
      <div class="card stat-card bg-warning text-dark">
        <div class="card-body text-center">
          <h5>Pending Commission</h5>
          <h2>₱<?php echo number_format($pending_commission, 2); ?></h2>
        </div>
      </div>
    </div>
    
    <div class="col-lg-4">
      <div class="card stat-card bg-success text-white">
        <div class="card-body text-center">
          <h5>Paid Commission</h5>
          <h2>₱<?php echo number_format($paid_commission, 2); ?></h2>
        </div>
      </div>
    </div>
    
    <!-- Commission Breakdown by Type -->
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Commission Breakdown by Type</h5>
          <div class="row text-center">
            <div class="col-md-2">
              <div class="p-3 border rounded bg-light">
                <h6 class="text-primary">Travel</h6>
                <h5>₱<?php echo number_format($type_totals['Travel'], 2); ?></h5>
              </div>
            </div>
            <div class="col-md-2">
              <div class="p-3 border rounded bg-light">
                <h6 class="text-success">Hotel</h6>
                <h5>₱<?php echo number_format($type_totals['Hotel'], 2); ?></h5>
              </div>
            </div>
            <div class="col-md-2">
              <div class="p-3 border rounded bg-light">
                <h6 class="text-info">Flight</h6>
                <h5>₱<?php echo number_format($type_totals['Flight'], 2); ?></h5>
              </div>
            </div>
            <div class="col-md-2">
              <div class="p-3 border rounded bg-light">
                <h6 class="text-warning">Tour</h6>
                <h5>₱<?php echo number_format($type_totals['Tour'], 2); ?></h5>
              </div>
            </div>
            <div class="col-md-2">
              <div class="p-3 border rounded bg-light">
                <h6 class="text-dark">Car Rental</h6>
                <h5>₱<?php echo number_format($type_totals['Car Rental'], 2); ?></h5>
              </div>
            </div>
            <div class="col-md-2">
              <div class="p-3 border rounded bg-light">
                <h6 class="text-secondary">Total</h6>
                <h5>₱<?php echo number_format($total_commission, 2); ?></h5>
              </div>
            </div>
          </div>
          
          <!-- Commission Chart -->
          <div class="mt-4">
            <canvas id="commissionChart" height="100"></canvas>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Commissions Table -->
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title">Commission Details</h5>
            <div class="btn-group">
              <button class="btn btn-sm btn-outline-primary" onclick="filterCommissions('all')">All</button>
              <button class="btn btn-sm btn-outline-success" onclick="filterCommissions('Travel')">Travel</button>
              <button class="btn btn-sm btn-outline-success" onclick="filterCommissions('Hotel')">Hotel</button>
              <button class="btn btn-sm btn-outline-info" onclick="filterCommissions('Flight')">Flight</button>
              <button class="btn btn-sm btn-outline-warning" onclick="filterCommissions('Tour')">Tour</button>
              <button class="btn btn-sm btn-outline-dark" onclick="filterCommissions('Car Rental')">Car</button>
            </div>
          </div>
          
          <?php if (!empty($all_commissions)): ?>
          <div class="table-responsive">
            <table class="table table-hover" id="commissionsTable">
              <thead>
                <tr>
                  <th>Booking ID</th>
                  <th>Customer</th>
                  <th>Agent</th>
                  <th>Booking Type</th>
                  <th>Booking Amount</th>
                  <th>Commission Rate</th>
                  <th>Commission Amount</th>
                  <th>Status</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($all_commissions as $commission): ?>
                <tr data-type="<?php echo $commission['booking_type']; ?>">
                  <td><strong><?php echo $commission['id']; ?></strong></td>
                  <td><?php echo htmlspecialchars($commission['customer_name']); ?></td>
                  <td>
                    <div>
                      <strong><?php echo $commission['agent_name']; ?></strong><br>
                      <small class="text-muted"><?php echo $commission['agent_id']; ?></small>
                    </div>
                  </td>
                  <td>
                    <?php 
                    $type_badge = '';
                    $type_icon = '';
                    switch($commission['booking_type']) {
                      case 'Travel': 
                        $type_badge = 'primary'; 
                        $type_icon = 'bi-bus-front';
                        break;
                      case 'Hotel': 
                        $type_badge = 'success'; 
                        $type_icon = 'bi-building';
                        break;
                      case 'Flight': 
                        $type_badge = 'info'; 
                        $type_icon = 'bi-airplane';
                        break;
                      case 'Tour': 
                        $type_badge = 'warning'; 
                        $type_icon = 'bi-compass';
                        break;
                      case 'Car Rental': 
                        $type_badge = 'dark'; 
                        $type_icon = 'bi-car-front';
                        break;
                    }
                    ?>
                    <span class="badge bg-<?php echo $type_badge; ?>">
                      <i class="bi <?php echo $type_icon; ?>"></i>
                      <?php echo $commission['booking_type']; ?>
                    </span>
                  </td>
                  <td>₱<?php echo number_format($commission['amount'], 2); ?></td>
                  <td><?php echo $commission['commission_rate']; ?>%</td>
                  <td>
                    <strong class="text-success">₱<?php echo number_format($commission['commission'], 2); ?></strong>
                  </td>
                  <td>
                    <?php 
                    $status_badge = '';
                    switch($commission['status']) {
                      case 'Confirmed': 
                      case 'Checked-in': 
                      case 'Boarded': 
                      case 'Active': 
                        $status_badge = 'warning'; break;
                      case 'Completed': 
                      case 'Checked-out': 
                      case 'Returned': 
                        $status_badge = 'success'; break;
                      case 'Cancelled': $status_badge = 'danger'; break;
                      default: $status_badge = 'secondary';
                    }
                    ?>
                    <span class="badge bg-<?php echo $status_badge; ?>">
                      <?php echo $commission['status']; ?>
                    </span>
                  </td>
                  <td><?php echo date('M d, Y', strtotime($commission['booking_date'])); ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php else: ?>
          <div class="text-center py-5">
            <i class="bi bi-cash-coin display-1 text-muted"></i>
            <h5 class="mt-3">No commissions yet</h5>
            <p class="text-muted">Commissions will appear here when bookings are made with agents.</p>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize commission chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('commissionChart').getContext('2d');
    
    // Get commission data from PHP
    const commissionData = {
        labels: ['Travel', 'Hotel', 'Flight', 'Tour', 'Car Rental'],
        datasets: [{
            label: 'Commission Amount (₱)',
            data: [
                <?php echo $type_totals['Travel']; ?>,
                <?php echo $type_totals['Hotel']; ?>,
                <?php echo $type_totals['Flight']; ?>,
                <?php echo $type_totals['Tour']; ?>,
                <?php echo $type_totals['Car Rental']; ?>
            ],
            backgroundColor: [
                '#007bff', // Travel - blue
                '#28a745', // Hotel - green
                '#17a2b8', // Flight - cyan
                '#ffc107', // Tour - yellow
                '#343a40'  // Car - dark
            ],
            borderWidth: 1
        }]
    };
    
    const commissionChart = new Chart(ctx, {
        type: 'bar',
        data: commissionData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.raw.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toFixed(2);
                        }
                    }
                }
            }
        }
    });
    
    // Setup commission filtering
    window.filterCommissions = function(type) {
        const rows = document.querySelectorAll('#commissionsTable tbody tr');
        
        rows.forEach(row => {
            const rowType = row.getAttribute('data-type');
            
            if (type === 'all' || rowType === type) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    };
});
</script>

<style>
.stat-card {
  border: none;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  margin-bottom: 20px;
  transition: transform 0.3s;
}

.stat-card:hover {
  transform: translateY(-5px);
}

.stat-card .card-body {
  padding: 25px;
}

.stat-card.bg-primary { background: linear-gradient(135deg, #007bff, #0056b3); }
.stat-card.bg-warning { background: linear-gradient(135deg, #ffc107, #e0a800); }
.stat-card.bg-success { background: linear-gradient(135deg, #28a745, #1e7e34); }
</style>