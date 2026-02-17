<?php
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');

// Create car_availability table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS car_availability (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'Blocked',
    reason VARCHAR(255),
    price_multiplier DECIMAL(3,2) DEFAULT 1.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
)");

$car_name = isset($_GET['car_name']) ? $conn->real_escape_string($_GET['car_name']) : '';

if(!$car_name) {
    echo '<div class="alert alert-danger">Please select a car first</div>';
    exit();
}

// Get car details by name
$car = $conn->query("SELECT * FROM cars WHERE name='$car_name'")->fetch_assoc();

if(!$car) {
    echo '<div class="alert alert-danger">Car not found</div>';
    exit();
}

// Get blocked dates (maintenance) - from car_blocked_dates table
$blocked_dates = [];
$maintenance_result = $conn->query("SELECT block_start, block_end FROM car_blocked_dates WHERE car_model='$car_name' AND block_end >= CURDATE()");
if($maintenance_result) {
    while($row = $maintenance_result->fetch_assoc()) {
        $start = new DateTime($row['block_start']);
        $end = new DateTime($row['block_end']);
        $end->modify('+1 day');
        $period = new DatePeriod($start, new DateInterval('P1D'), $end);
        foreach($period as $date) {
            $blocked_dates[] = $date->format('Y-m-d');
        }
    }
}

// Get booked dates
$booked_dates = [];
$bookings_result = $conn->query("SELECT pickup_date, dropoff_date FROM car_rentals WHERE car_model='$car_name' AND status IN ('Confirmed', 'Pending')");
if($bookings_result) {
    while($row = $bookings_result->fetch_assoc()) {
        $start = new DateTime($row['pickup_date']);
        $end = new DateTime($row['dropoff_date']);
        $end->modify('+1 day');
        $period = new DatePeriod($start, new DateInterval('P1D'), $end);
        foreach($period as $date) {
            $booked_dates[] = $date->format('Y-m-d');
        }
    }
}

$conn->close();
?>

<div class="pagetitle">
  <h1>📅 Car Availability - <?php echo htmlspecialchars($car['name']); ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php?page=car_rental">Cars</a></li>
      <li class="breadcrumb-item active">Availability Calendar</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header" style="background-color: #0a2540; color: white;">
          <h6 class="mb-0"><i class="bi bi-calendar-week me-2"></i>Availability Calendar</h6>
        </div>
        <div class="card-body">
          <div id="calendar"></div>
          
          <div class="mt-4">
            <h6>Legend:</h6>
            <div class="d-flex gap-3 flex-wrap">
              <div><span class="badge" style="background-color: #10b981;">Available</span></div>
              <div><span class="badge" style="background-color: #ef4444;">Booked</span></div>
              <div><span class="badge" style="background-color: #6b7280;">Maintenance</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-header bg-light">
          <h6 class="mb-0"><i class="bi bi-car-front me-2"></i>Car Details</h6>
        </div>
        <div class="card-body">
          <?php if($car['image'] && file_exists($car['image'])): ?>
          <img src="<?php echo $car['image']; ?>" class="img-fluid rounded mb-3" style="width:100%; height:200px; object-fit:cover;">
          <?php endif; ?>
          
          <h5><?php echo htmlspecialchars($car['name']); ?></h5>
          <p class="text-muted"><?php echo $car['type']; ?> • <?php echo $car['transmission']; ?></p>
          <h4 class="text-primary">₱<?php echo number_format($car['daily_rate'], 2); ?>/day</h4>
          
          <hr>
          
          <div class="mb-2">
            <small class="text-muted">Fuel Type:</small>
            <p class="mb-0"><?php echo $car['fuel_type']; ?></p>
          </div>
          <div class="mb-2">
            <small class="text-muted">Seating Capacity:</small>
            <p class="mb-0"><?php echo $car['seating_capacity']; ?> seats</p>
          </div>
          <div class="mb-2">
            <small class="text-muted">Mileage Limit:</small>
            <p class="mb-0"><?php echo $car['mileage_limit']; ?> km/day</p>
          </div>
          
          <div class="d-grid gap-2 mt-4">
            <button type="button" class="btn btn-primary" onclick="selectDatesFromCalendar()">
              <i class="bi bi-calendar-check me-2"></i>Select Dates & Continue
            </button>
            <a href="index.php?page=car_rental" class="btn btn-outline-secondary">
              <i class="bi bi-arrow-left me-2"></i>Back to Cars
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
const blockedDates = <?php echo json_encode($blocked_dates); ?>;
const bookedDates = <?php echo json_encode($booked_dates); ?>;
let selectedPickup = null;
let selectedDropoff = null;

function generateCalendar() {
  const calendar = document.getElementById('calendar');
  const today = new Date();
  const currentMonth = today.getMonth();
  const currentYear = today.getFullYear();
  
  let html = '<div class="alert alert-info mb-3"><i class="bi bi-info-circle"></i> <strong>Click on an available date to select pickup, then click another date for return.</strong></div>';
  html += '<div class="row g-3">';
  
  // Show 3 months
  for(let m = 0; m < 3; m++) {
    const month = new Date(currentYear, currentMonth + m, 1);
    const monthName = month.toLocaleString('default', { month: 'long', year: 'numeric' });
    
    html += '<div class="col-md-4">';
    html += '<div class="card">';
    html += '<div class="card-header text-center bg-light"><strong>' + monthName + '</strong></div>';
    html += '<div class="card-body p-2">';
    html += '<table class="table table-sm table-borderless mb-0" style="font-size:12px;">';
    html += '<thead><tr><th>Su</th><th>Mo</th><th>Tu</th><th>We</th><th>Th</th><th>Fr</th><th>Sa</th></tr></thead>';
    html += '<tbody>';
    
    const firstDay = new Date(month.getFullYear(), month.getMonth(), 1).getDay();
    const daysInMonth = new Date(month.getFullYear(), month.getMonth() + 1, 0).getDate();
    
    let day = 1;
    for(let i = 0; i < 6; i++) {
      html += '<tr>';
      for(let j = 0; j < 7; j++) {
        if(i === 0 && j < firstDay) {
          html += '<td></td>';
        } else if(day > daysInMonth) {
          html += '<td></td>';
        } else {
          const dateStr = month.getFullYear() + '-' + 
                         String(month.getMonth() + 1).padStart(2, '0') + '-' + 
                         String(day).padStart(2, '0');
          
          let bgColor = '#10b981';
          let textColor = 'white';
          let clickable = true;
          let cursor = 'pointer';
          
          const isPast = new Date(dateStr) < new Date(today.toDateString());
          
          if(isPast) {
            bgColor = '#e5e7eb';
            textColor = '#9ca3af';
            clickable = false;
            cursor = 'not-allowed';
          } else if(blockedDates.includes(dateStr)) {
            bgColor = '#6b7280';
            clickable = false;
            cursor = 'not-allowed';
          } else if(bookedDates.includes(dateStr)) {
            bgColor = '#ef4444';
            clickable = false;
            cursor = 'not-allowed';
          }
          
          html += '<td><div class="calendar-day' + (clickable ? ' clickable-day' : '') + '" data-date="' + dateStr + '" style="background-color:' + bgColor + '; color:' + textColor + '; padding:5px; text-align:center; border-radius:4px; font-weight:600; cursor:' + cursor + ';">' + day + '</div></td>';
          day++;
        }
      }
      html += '</tr>';
      if(day > daysInMonth) break;
    }
    
    html += '</tbody></table>';
    html += '</div></div></div>';
  }
  
  html += '</div>';
  calendar.innerHTML = html;
  
  // Add click handlers
  document.querySelectorAll('.clickable-day').forEach(day => {
    day.addEventListener('click', function() {
      const date = this.dataset.date;
      
      if(!selectedPickup) {
        selectedPickup = date;
        this.style.backgroundColor = '#3b82f6';
        this.style.border = '2px solid #1e40af';
      } else if(!selectedDropoff && date > selectedPickup) {
        selectedDropoff = date;
        this.style.backgroundColor = '#3b82f6';
        this.style.border = '2px solid #1e40af';
        highlightRange();
      } else {
        // Reset
        selectedPickup = null;
        selectedDropoff = null;
        generateCalendar();
      }
    });
  });
}

function highlightRange() {
  if(!selectedPickup || !selectedDropoff) return;
  
  const start = new Date(selectedPickup);
  const end = new Date(selectedDropoff);
  
  document.querySelectorAll('.clickable-day').forEach(day => {
    const date = new Date(day.dataset.date);
    if(date >= start && date <= end) {
      day.style.backgroundColor = '#3b82f6';
      day.style.border = '2px solid #1e40af';
    }
  });
}

function selectDatesFromCalendar() {
  if(!selectedPickup || !selectedDropoff) {
    alert('Please select both pickup and return dates from the calendar.');
    return;
  }
  
  // Redirect back to car rental with dates and car parameter
  window.location.href = 'index.php?page=car_rental&car=<?php echo urlencode($car['name']); ?>&pickup=' + selectedPickup + '&dropoff=' + selectedDropoff;
}

generateCalendar();
</script>

<style>
.table th, .table td {
  padding: 2px !important;
  text-align: center;
}
</style>
