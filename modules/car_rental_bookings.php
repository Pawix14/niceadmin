<?php
// modules/car_rental_bookings.php
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_car'])) {
    $customer_name = $conn->real_escape_string($_POST['customer_name']);
    $customer_email = $conn->real_escape_string($_POST['customer_email']);
    $customer_phone = $conn->real_escape_string($_POST['customer_phone']);
    $car_model = $conn->real_escape_string($_POST['car_model']);
    $car_type = $conn->real_escape_string($_POST['car_type']);
    $pickup_date = $conn->real_escape_string($_POST['pickup_date']);
    $return_date = $conn->real_escape_string($_POST['return_date']);
    $pickup_location = $conn->real_escape_string($_POST['pickup_location']);
    $daily_rate = (float)$_POST['daily_rate'];
    $rental_days = (int)$_POST['rental_days'];
    $total_amount = (float)$_POST['total_amount'];
    $booking_date = date('Y-m-d');
    $status = 'Pending';
    
    // Get agent ID if available
    $agent_id = isset($_POST['agent_id']) ? $conn->real_escape_string($_POST['agent_id']) : null;
    
    // Calculate commission if agent is assigned
    $agent_commission = 0;
    if ($agent_id) {
        $agent_result = $conn->query("SELECT commission_rate FROM travel_agents WHERE agent_id = '$agent_id'");
        if ($agent_result && $agent_row = $agent_result->fetch_assoc()) {
            $commission_rate = $agent_row['commission_rate'];
            $agent_commission = ($total_amount * $commission_rate) / 100;
        }
    }
    
    // Generate booking ID
    $booking_id = 'CAR-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    
    $sql = "INSERT INTO car_rental_bookings (booking_id, customer_name, customer_email, customer_phone, 
            car_model, car_type, pickup_date, return_date, pickup_location, daily_rate, rental_days, 
            total_amount, agent_id, agent_commission, booking_date, status) 
            VALUES ('$booking_id', '$customer_name', '$customer_email', '$customer_phone', 
            '$car_model', '$car_type', '$pickup_date', '$return_date', '$pickup_location', 
            $daily_rate, $rental_days, $total_amount, " . ($agent_id ? "'$agent_id'" : "NULL") . ", 
            $agent_commission, '$booking_date', '$status')";
    
    if ($conn->query($sql)) {
        $message = "✅ Car rental booked successfully! Booking ID: <strong>$booking_id</strong>";
        $message_type = "success";
    } else {
        $message = "❌ Error booking car rental: " . $conn->error;
        $message_type = "error";
    }
}

// Fetch travel agents
$agents_result = $conn->query("SELECT agent_id, agent_name FROM travel_agents WHERE status = 'Active' ORDER BY agent_name");

$conn->close();
?>

<div class="pagetitle">
  <h1>Car Rental Booking</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">Car Rental</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          
          <?php if ($message): ?>
          <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          <?php endif; ?>
          
          <h5 class="card-title">Rent a Car</h5>
          
          <form method="POST" action="">
            <input type="hidden" name="book_car" value="1">
            
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="customer_name" class="form-label">Customer Name *</label>
                  <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="agent_id" class="form-label">Travel Agent (Optional)</label>
                  <select class="form-select" id="agent_id" name="agent_id">
                    <option value="">No agent</option>
                    <?php while($agent = $agents_result->fetch_assoc()): ?>
                    <option value="<?php echo $agent['agent_id']; ?>">
                      <?php echo htmlspecialchars($agent['agent_name']); ?> (<?php echo $agent['agent_id']; ?>)
                    </option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="customer_email" class="form-label">Email *</label>
                  <input type="email" class="form-control" id="customer_email" name="customer_email" required>
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="customer_phone" class="form-label">Phone Number *</label>
                  <input type="tel" class="form-control" id="customer_phone" name="customer_phone" required>
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="pickup_location" class="form-label">Pickup Location *</label>
                  <input type="text" class="form-control" id="pickup_location" name="pickup_location" required>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="car_model" class="form-label">Car Model *</label>
                  <input type="text" class="form-control" id="car_model" name="car_model" required placeholder="Ex. Toyota Corolla">
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="car_type" class="form-label">Car Type *</label>
                  <select class="form-select" id="car_type" name="car_type" required>
                    <option value="Sedan">Sedan</option>
                    <option value="SUV">SUV</option>
                    <option value="MPV">MPV</option>
                    <option value="Van">Van</option>
                    <option value="Pickup">Pickup</option>
                    <option value="Luxury">Luxury</option>
                  </select>
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="daily_rate" class="form-label">Daily Rate (₱) *</label>
                  <div class="input-group">
                    <span class="input-group-text">₱</span>
                    <input type="number" class="form-control" id="daily_rate" name="daily_rate" step="0.01" min="0" required onchange="calculateTotal()">
                  </div>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="pickup_date" class="form-label">Pickup Date *</label>
                  <input type="date" class="form-control" id="pickup_date" name="pickup_date" required min="<?php echo date('Y-m-d'); ?>" onchange="calculateDays()">
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="return_date" class="form-label">Return Date *</label>
                  <input type="date" class="form-control" id="return_date" name="return_date" required min="<?php echo date('Y-m-d'); ?>" onchange="calculateDays()">
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">Rental Days</label>
                  <div class="form-control bg-light" id="rental_days_display">0 days</div>
                  <input type="hidden" id="rental_days" name="rental_days" value="0">
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="alert alert-info">
                  <i class="bi bi-info-circle"></i> 
                  <strong>Car Details:</strong> <span id="car_details">Not selected</span>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="alert alert-success">
                  <i class="bi bi-cash-coin"></i> 
                  <strong>Total Amount:</strong> ₱<span id="total_amount_display">0.00</span>
                  <input type="hidden" id="total_amount" name="total_amount" value="0">
                </div>
              </div>
            </div>
            
            <div class="text-center mt-4">
              <button type="submit" class="btn btn-primary btn-lg px-5">
                <i class="bi bi-check-circle"></i> Confirm Booking
              </button>
              <button type="reset" class="btn btn-secondary btn-lg px-5">
                <i class="bi bi-x-circle"></i> Reset
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
function calculateDays() {
    const pickupDate = new Date(document.getElementById('pickup_date').value);
    const returnDate = new Date(document.getElementById('return_date').value);
    
    if (pickupDate && returnDate && returnDate > pickupDate) {
        const timeDiff = returnDate.getTime() - pickupDate.getTime();
        const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));
        
        document.getElementById('rental_days_display').textContent = daysDiff + ' days';
        document.getElementById('rental_days').value = daysDiff;
        
        calculateTotal();
    } else {
        document.getElementById('rental_days_display').textContent = '0 days';
        document.getElementById('rental_days').value = 0;
        calculateTotal();
    }
}

function calculateTotal() {
    const dailyRate = parseFloat(document.getElementById('daily_rate').value) || 0;
    const rentalDays = parseInt(document.getElementById('rental_days').value) || 0;
    const carModel = document.getElementById('car_model').value || 'Not selected';
    const carType = document.getElementById('car_type').value;
    
    const totalAmount = dailyRate * rentalDays;
    
    document.getElementById('total_amount_display').textContent = totalAmount.toFixed(2);
    document.getElementById('total_amount').value = totalAmount;
    document.getElementById('car_details').textContent = carModel + ' (' + carType + ')';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateDays();
    calculateTotal();
});
</script>