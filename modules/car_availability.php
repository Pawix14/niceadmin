<?php
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');

// Create tables
$conn->query("CREATE TABLE IF NOT EXISTS car_blocked_dates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_model VARCHAR(100) NOT NULL,
    block_start DATE NOT NULL,
    block_end DATE NOT NULL,
    reason VARCHAR(255) NOT NULL,
    created_by VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS seasonal_pricing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_model VARCHAR(100) NOT NULL,
    season_name VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    price_multiplier DECIMAL(3,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$message = '';

// Block dates
if(isset($_POST['block_dates'])) {
    $car_model = $conn->real_escape_string($_POST['car_model']);
    $start = $conn->real_escape_string($_POST['block_start']);
    $end = $conn->real_escape_string($_POST['block_end']);
    $reason = $conn->real_escape_string($_POST['reason']);
    $created_by = $_SESSION['username'];
    
    $conn->query("INSERT INTO car_blocked_dates (car_model, block_start, block_end, reason, created_by) 
                  VALUES ('$car_model', '$start', '$end', '$reason', '$created_by')");
    $message = '✅ Dates blocked successfully!';
}

// Add seasonal pricing
if(isset($_POST['add_seasonal'])) {
    $car_model = $conn->real_escape_string($_POST['car_model']);
    $season_name = $conn->real_escape_string($_POST['season_name']);
    $start = $conn->real_escape_string($_POST['season_start']);
    $end = $conn->real_escape_string($_POST['season_end']);
    $multiplier = floatval($_POST['price_multiplier']);
    
    $conn->query("INSERT INTO seasonal_pricing (car_model, season_name, start_date, end_date, price_multiplier) 
                  VALUES ('$car_model', '$season_name', '$start', '$end', $multiplier)");
    $message = '✅ Seasonal pricing added!';
}

// Delete block
if(isset($_GET['delete_block'])) {
    $id = intval($_GET['delete_block']);
    $conn->query("DELETE FROM car_blocked_dates WHERE id=$id");
    $message = '✅ Block removed!';
}

// Delete seasonal
if(isset($_GET['delete_seasonal'])) {
    $id = intval($_GET['delete_seasonal']);
    $conn->query("DELETE FROM seasonal_pricing WHERE id=$id");
    $message = '✅ Seasonal pricing removed!';
}

$cars = $conn->query("SELECT name FROM cars ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$blocked_dates = $conn->query("SELECT * FROM car_blocked_dates ORDER BY block_start DESC")->fetch_all(MYSQLI_ASSOC);
$seasonal_pricing = $conn->query("SELECT * FROM seasonal_pricing ORDER BY start_date DESC")->fetch_all(MYSQLI_ASSOC);

// Get calendar data
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$selected_car = isset($_GET['car']) ? $_GET['car'] : '';

if($selected_car) {
    $first_day = "$year-".str_pad($month, 2, '0', STR_PAD_LEFT)."-01";
    $last_day = date('Y-m-t', strtotime($first_day));
    
    $bookings = $conn->query("SELECT pickup_date, dropoff_date FROM car_rentals 
                              WHERE car_model='$selected_car' 
                              AND status IN ('Confirmed', 'Pending')
                              AND (pickup_date <= '$last_day' AND dropoff_date >= '$first_day')")->fetch_all(MYSQLI_ASSOC);
    
    $blocks = $conn->query("SELECT block_start, block_end, reason FROM car_blocked_dates 
                            WHERE car_model='$selected_car' 
                            AND (block_start <= '$last_day' AND block_end >= '$first_day')")->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>

<div class="pagetitle">
  <h1>📅 Car Availability Calendar</h1>
</div>

<section class="section">
  <?php if($message): ?>
  <div class="alert alert-success" style="background-color: #d1fae5; border: 2px solid #10b981; color: #065f46; font-weight: 600; font-size: 16px;"><?php echo $message; ?></div>
  <?php endif; ?>

  <div class="row">
    <!-- Left: Calendar View -->
    <div class="col-lg-8">
      <div class="card mb-4">
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label fw-bold">Select Car:</label>
            <select class="form-select" onchange="window.location.href='?page=car_availability&car='+this.value+'&month=<?php echo $month; ?>&year=<?php echo $year; ?>'">
              <option value="">Choose a car...</option>
              <?php foreach($cars as $car): ?>
              <option value="<?php echo htmlspecialchars($car['name']); ?>" <?php echo $selected_car==$car['name']?'selected':''; ?>>
                <?php echo htmlspecialchars($car['name']); ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <?php if($selected_car): ?>
          <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="?page=car_availability&car=<?php echo urlencode($selected_car); ?>&month=<?php echo $month==1?12:$month-1; ?>&year=<?php echo $month==1?$year-1:$year; ?>" class="btn btn-outline-primary">
              <i class="bi bi-chevron-left"></i>
            </a>
            <h5 style="color: #0a2540;"><?php echo date('F Y', strtotime("$year-$month-01")); ?></h5>
            <a href="?page=car_availability&car=<?php echo urlencode($selected_car); ?>&month=<?php echo $month==12?1:$month+1; ?>&year=<?php echo $month==12?$year+1:$year; ?>" class="btn btn-outline-primary">
              <i class="bi bi-chevron-right"></i>
            </a>
          </div>

          <div class="calendar-grid">
            <div class="calendar-header">Sun</div>
            <div class="calendar-header">Mon</div>
            <div class="calendar-header">Tue</div>
            <div class="calendar-header">Wed</div>
            <div class="calendar-header">Thu</div>
            <div class="calendar-header">Fri</div>
            <div class="calendar-header">Sat</div>
            
            <?php
            $first_day_of_month = date('w', strtotime("$year-$month-01"));
            $days_in_month = date('t', strtotime("$year-$month-01"));
            
            for($i = 0; $i < $first_day_of_month; $i++) {
                echo '<div class="calendar-day empty"></div>';
            }
            
            for($day = 1; $day <= $days_in_month; $day++) {
                $current_date = "$year-".str_pad($month, 2, '0', STR_PAD_LEFT)."-".str_pad($day, 2, '0', STR_PAD_LEFT);
                
                $is_booked = false;
                foreach($bookings as $b) {
                    if($current_date >= $b['pickup_date'] && $current_date <= $b['dropoff_date']) {
                        $is_booked = true;
                        break;
                    }
                }
                
                $is_blocked = false;
                $block_reason = '';
                foreach($blocks as $bl) {
                    if($current_date >= $bl['block_start'] && $current_date <= $bl['block_end']) {
                        $is_blocked = true;
                        $block_reason = $bl['reason'];
                        break;
                    }
                }
                
                $class = $is_blocked ? 'blocked' : ($is_booked ? 'booked' : 'available');
                $is_today = $current_date == date('Y-m-d');
                
                echo '<div class="calendar-day '.$class.($is_today?' today':'').'" title="'.($is_blocked?$block_reason:'').'">';
                echo '<div class="day-number">'.$day.'</div>';
                if($is_blocked) echo '<div class="status-label">Blocked</div>';
                else if($is_booked) echo '<div class="status-label">Booked</div>';
                else echo '<div class="status-label">Available</div>';
                echo '</div>';
            }
            ?>
          </div>

          <div class="mt-3">
            <span class="badge bg-success me-2">Available</span>
            <span class="badge bg-danger me-2">Booked</span>
            <span class="badge bg-secondary">Blocked</span>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Right: Actions -->
    <div class="col-lg-4">
      <!-- Block Dates -->
      <div class="card mb-3">
        <div class="card-header" style="background-color: #0a2540; color: white;">
          <h6 class="mb-0"><i class="bi bi-lock me-2"></i>Block Dates</h6>
        </div>
        <div class="card-body">
          <form method="POST">
            <div class="mb-3">
              <label class="form-label">Car:</label>
              <select class="form-select" name="car_model" required>
                <option value="">Select car...</option>
                <?php foreach($cars as $car): ?>
                <option value="<?php echo htmlspecialchars($car['name']); ?>"><?php echo htmlspecialchars($car['name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Start Date:</label>
              <input type="date" class="form-control" name="block_start" required>
            </div>
            <div class="mb-3">
              <label class="form-label">End Date:</label>
              <input type="date" class="form-control" name="block_end" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Reason:</label>
              <select class="form-select" name="reason" required>
                <option value="Maintenance">Maintenance</option>
                <option value="Repair">Repair</option>
                <option value="Inspection">Inspection</option>
                <option value="Reserved">Reserved</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <button type="submit" name="block_dates" class="btn btn-primary w-100">Block Dates</button>
          </form>
        </div>
      </div>

      <!-- Seasonal Pricing -->
      <div class="card">
        <div class="card-header" style="background-color: #0a2540; color: white;">
          <h6 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Seasonal Pricing</h6>
        </div>
        <div class="card-body">
          <form method="POST">
            <div class="mb-3">
              <label class="form-label">Car:</label>
              <select class="form-select" name="car_model" required>
                <option value="">Select car...</option>
                <?php foreach($cars as $car): ?>
                <option value="<?php echo htmlspecialchars($car['name']); ?>"><?php echo htmlspecialchars($car['name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Season Name:</label>
              <input type="text" class="form-control" name="season_name" placeholder="e.g., Summer Peak" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Start Date:</label>
              <input type="date" class="form-control" name="season_start" required>
            </div>
            <div class="mb-3">
              <label class="form-label">End Date:</label>
              <input type="date" class="form-control" name="season_end" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Price Multiplier:</label>
              <input type="number" class="form-control" name="price_multiplier" step="0.01" min="0.5" max="3" placeholder="1.5 = 150% of base price" required>
              <small class="text-muted">1.0 = normal, 1.5 = +50%, 0.8 = -20%</small>
            </div>
            <button type="submit" name="add_seasonal" class="btn btn-primary w-100">Add Seasonal Pricing</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Blocked Dates List -->
  <div class="card mb-3">
    <div class="card-header" style="background-color: #f8fafc;">
      <h6 class="mb-0"><i class="bi bi-list me-2"></i>Blocked Dates</h6>
    </div>
    <div class="card-body">
      <table class="table">
        <thead>
          <tr>
            <th>Car</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Reason</th>
            <th>Created By</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($blocked_dates as $block): ?>
          <tr>
            <td><strong><?php echo htmlspecialchars($block['car_model']); ?></strong></td>
            <td><?php echo date('M d, Y', strtotime($block['block_start'])); ?></td>
            <td><?php echo date('M d, Y', strtotime($block['block_end'])); ?></td>
            <td><span class="badge bg-secondary"><?php echo $block['reason']; ?></span></td>
            <td><?php echo htmlspecialchars($block['created_by']); ?></td>
            <td>
              <a href="?page=car_availability&delete_block=<?php echo $block['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Remove this block?')">
                <i class="bi bi-trash"></i>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Seasonal Pricing List -->
  <div class="card">
    <div class="card-header" style="background-color: #f8fafc;">
      <h6 class="mb-0"><i class="bi bi-list me-2"></i>Seasonal Pricing</h6>
    </div>
    <div class="card-body">
      <table class="table">
        <thead>
          <tr>
            <th>Car</th>
            <th>Season</th>
            <th>Period</th>
            <th>Multiplier</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($seasonal_pricing as $season): ?>
          <tr>
            <td><strong><?php echo htmlspecialchars($season['car_model']); ?></strong></td>
            <td><?php echo htmlspecialchars($season['season_name']); ?></td>
            <td><?php echo date('M d', strtotime($season['start_date'])); ?> - <?php echo date('M d, Y', strtotime($season['end_date'])); ?></td>
            <td><span class="badge bg-info"><?php echo $season['price_multiplier']; ?>x</span></td>
            <td>
              <a href="?page=car_availability&delete_seasonal=<?php echo $season['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Remove this seasonal pricing?')">
                <i class="bi bi-trash"></i>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<style>
.calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
.calendar-header { background: #0a2540; color: white; padding: 8px; text-align: center; font-weight: 600; border-radius: 6px; font-size: 0.9rem; }
.calendar-day { background: white; border: 2px solid #e2e8f0; padding: 8px; min-height: 80px; border-radius: 6px; position: relative; }
.calendar-day.available { border-color: #10b981; background: #f0fdf4; }
.calendar-day.booked { border-color: #dc3545; background: #fef2f2; }
.calendar-day.blocked { border-color: #6c757d; background: #f8f9fa; }
.calendar-day.today { box-shadow: 0 0 0 3px #0a2540; }
.calendar-day.empty { background: transparent; border: none; }
.day-number { font-weight: 700; color: #0a2540; font-size: 1rem; }
.status-label { font-size: 0.7rem; margin-top: 4px; padding: 2px 6px; border-radius: 4px; background: rgba(0,0,0,0.1); }
</style>
