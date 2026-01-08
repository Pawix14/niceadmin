<?php
// modules/hotels.php - UPDATED VERSION

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch active travel agents
$agents_result = $conn->query("SELECT agent_id, agent_name, commission_rate FROM travel_agents WHERE status = 'Active' ORDER BY agent_name");

// Fetch countries and cities from database (same as travel_booking_form)
$countries_result = $conn->query("SELECT * FROM countries ORDER BY country_name");
$cities_result = $conn->query("SELECT c.country_code, ci.city_name FROM cities ci 
                               INNER JOIN countries c ON ci.country_code = c.country_code 
                               ORDER BY c.country_name, ci.city_name");

$cities_by_country = [];
while ($row = $cities_result->fetch_assoc()) {
    $cities_by_country[$row['country_code']][] = $row['city_name'];
}

// Handle form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guest_name = $conn->real_escape_string($_POST['guest_name']);
    $hotel_name = $conn->real_escape_string($_POST['hotel_name']);
    $country = $conn->real_escape_string($_POST['country']);
    $city = $conn->real_escape_string($_POST['city']);
    $room_type = $conn->real_escape_string($_POST['room_type']);
    $check_in = $conn->real_escape_string($_POST['check_in']);
    $check_out = $conn->real_escape_string($_POST['check_out']);
    $num_guests = (int)$_POST['num_guests'];
    $booking_amount = (float)$_POST['booking_amount'];
    $status = $conn->real_escape_string($_POST['status']);
    $agent_id = $conn->real_escape_string($_POST['agent_id']);
    
    // Calculate nights
    $check_in_date = new DateTime($check_in);
    $check_out_date = new DateTime($check_out);
    $nights = $check_in_date->diff($check_out_date)->days;
    
    // Generate reservation ID
    $reservation_id = 'HOT-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    
    $sql = "INSERT INTO hotel_reservations (reservation_id, guest_name, hotel_name, country, city, room_type, check_in, check_out, num_guests, booking_amount, status, agent_id) 
            VALUES ('$reservation_id', '$guest_name', '$hotel_name', '$country', '$city', '$room_type', '$check_in', '$check_out', $num_guests, $booking_amount, '$status', '$agent_id')";
    
    if ($conn->query($sql)) {
        // Update agent's total bookings and calculate commission if agent was selected
        if (!empty($agent_id) && $booking_amount > 0) {
            // Get agent commission rate
            $agent_data = $conn->query("SELECT commission_rate FROM travel_agents WHERE agent_id = '$agent_id'")->fetch_assoc();
            $commission_rate = $agent_data['commission_rate'];
            $commission_amount = ($booking_amount * $commission_rate) / 100;
            
            // Generate commission ID
            $commission_id = 'COM-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            
            // Record commission
            $conn->query("INSERT INTO agent_commissions (commission_id, agent_id, booking_id, booking_type, booking_amount, commission_rate, commission_amount, status) 
                          VALUES ('$commission_id', '$agent_id', '$reservation_id', 'Hotel', $booking_amount, $commission_rate, $commission_amount, 'Pending')");
            
            // Update agent's total bookings
            $conn->query("UPDATE travel_agents SET total_bookings = total_bookings + 1 WHERE agent_id = '$agent_id'");
            
            $message = "✅ Hotel reservation created successfully! Reservation ID: <strong>$reservation_id</strong><br>";
            $message .= "📊 Agent Commission: <strong>₱" . number_format($commission_amount, 2) . "</strong> (" . $commission_rate . "% of ₱" . number_format($booking_amount, 2) . ")";
        } else {
            $message = "✅ Hotel reservation created successfully! Reservation ID: <strong>$reservation_id</strong><br>Check-in: $check_in | Check-out: $check_out | Nights: $nights";
        }
        $message_type = "success";
    } else {
        $message = "❌ Error creating reservation: " . $conn->error;
        $message_type = "error";
    }
}
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">🏨 Hotel Reservations</h5>
            
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <form class="row g-3" method="POST" action="">
                <div class="col-md-6">
                    <label for="guest_name" class="form-label">Guest Name *</label>
                    <input type="text" class="form-control" id="guest_name" name="guest_name" required placeholder="Ex. John Smith">
                </div>
                
                <div class="col-md-6">
                    <label for="hotel_name" class="form-label">Hotel Name *</label>
                    <input type="text" class="form-control" id="hotel_name" name="hotel_name" required placeholder="Ex. Grand Hotel">
                </div>
                
                <div class="col-md-6">
                    <label for="country" class="form-label">Country *</label>
                    <select class="form-select" id="country" name="country" required onchange="updateHotelCities()">
                        <option value="">Select country...</option>
                        <?php 
                        $countries_result->data_seek(0);
                        while($country = $countries_result->fetch_assoc()): ?>
                        <option value="<?php echo $country['country_name']; ?>" data-code="<?php echo $country['country_code']; ?>">
                            <?php echo htmlspecialchars($country['country_name']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="city" class="form-label">City *</label>
                    <select class="form-select" id="city" name="city" required disabled>
                        <option value="">First select a country</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="room_type" class="form-label">Room Type *</label>
                    <select class="form-select" id="room_type" name="room_type" required>
                        <option value="Single">🛏️ Single</option>
                        <option value="Double">🛏️🛏️ Double</option>
                        <option value="Suite">🏨 Suite</option>
                        <option value="Deluxe">⭐ Deluxe</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="num_guests" class="form-label">Number of Guests *</label>
                    <input type="number" class="form-control" id="num_guests" name="num_guests" min="1" max="10" required>
                </div>
                
                <div class="col-md-4">
                    <label for="booking_amount" class="form-label">Booking Amount (₱) *</label>
                    <div class="input-group">
                        <span class="input-group-text">₱</span>
                        <input type="number" class="form-control" id="booking_amount" name="booking_amount" step="0.01" min="0" required oninput="showCommissionInfo()">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label for="check_in" class="form-label">Check-in Date *</label>
                    <input type="date" class="form-control" id="check_in" name="check_in" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="col-md-6">
                    <label for="check_out" class="form-label">Check-out Date *</label>
                    <input type="date" class="form-control" id="check_out" name="check_out" required>
                </div>
                
                <div class="col-md-6">
                    <label for="agent_id" class="form-label">Travel Agent (Optional)</label>
                    <select class="form-select" id="agent_id" name="agent_id" onchange="showCommissionInfo()">
                        <option value="">No agent / Self-booking</option>
                        <?php 
                        $agents_result->data_seek(0);
                        while($agent = $agents_result->fetch_assoc()): ?>
                        <option value="<?php echo $agent['agent_id']; ?>" data-rate="<?php echo $agent['commission_rate']; ?>">
                            <?php echo htmlspecialchars($agent['agent_name']); ?> (<?php echo $agent['commission_rate']; ?>% commission)
                        </option>
                        <?php endwhile; ?>
                    </select>
                    <div id="commissionInfo" class="form-text" style="display: none;">
                        Commission: <span id="commissionAmount">₱0.00</span>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label for="status" class="form-label">Reservation Status *</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="Confirmed">✅ Confirmed</option>
                        <option value="Pending">⏳ Pending</option>
                        <option value="Cancelled">❌ Cancelled</option>
                        <option value="Checked-in">🏨 Checked-in</option>
                        <option value="Checked-out">👋 Checked-out</option>
                    </select>
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">Book Hotel</button>
                    <button type="reset" class="btn btn-secondary" onclick="resetHotelForm()">Clear Form</button>
                </div>
            </form>
            
            <!-- Display existing reservations -->
            <div class="mt-5">
                <h5 class="card-title">📋 Recent Hotel Reservations</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Reservation ID</th>
                                <th>Guest</th>
                                <th>Hotel</th>
                                <th>Location</th>
                                <th>Check-in/out</th>
                                <th>Room Type</th>
                                <th>Amount</th>
                                <th>Agent</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $reservations_result = $conn->query("
                                SELECT hr.*, ta.agent_name 
                                FROM hotel_reservations hr 
                                LEFT JOIN travel_agents ta ON hr.agent_id = ta.agent_id 
                                ORDER BY hr.booking_date DESC LIMIT 10
                            ");
                            while($res = $reservations_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $res['reservation_id']; ?></td>
                                <td><?php echo htmlspecialchars($res['guest_name']); ?></td>
                                <td><?php echo htmlspecialchars($res['hotel_name']); ?></td>
                                <td><?php echo htmlspecialchars($res['city']) . ', ' . htmlspecialchars($res['country']); ?></td>
                                <td><?php echo date('M d', strtotime($res['check_in'])) . ' - ' . date('M d', strtotime($res['check_out'])); ?></td>
                                <td><?php echo $res['room_type']; ?></td>
                                <td>₱<?php echo number_format($res['booking_amount'], 2); ?></td>
                                <td>
                                    <?php if($res['agent_name']): ?>
                                    <span class="badge bg-info"><?php echo $res['agent_name']; ?></span>
                                    <?php else: ?>
                                    <span class="text-muted">Direct</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php 
                                    switch($res['status']) {
                                        case 'Confirmed': echo 'success'; break;
                                        case 'Pending': echo 'warning'; break;
                                        case 'Cancelled': echo 'danger'; break;
                                        case 'Checked-in': echo 'info'; break;
                                        case 'Checked-out': echo 'secondary'; break;
                                        default: echo 'primary';
                                    }
                                    ?>"><?php echo $res['status']; ?></span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Define cities data from PHP to JavaScript
const hotelCitiesData = <?php echo json_encode($cities_by_country); ?>;

// Function to update city dropdown based on selected country
function updateHotelCities() {
    const countrySelect = document.getElementById('country');
    const citySelect = document.getElementById('city');
    const selectedOption = countrySelect.options[countrySelect.selectedIndex];
    const countryCode = selectedOption.getAttribute('data-code');
    
    // Clear current city options
    citySelect.innerHTML = '<option value="">Select a city...</option>';
    
    if (countryCode && hotelCitiesData[countryCode]) {
        // Add cities for selected country
        hotelCitiesData[countryCode].forEach(city => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            citySelect.appendChild(option);
        });
        
        // Enable city select
        citySelect.disabled = false;
    } else {
        citySelect.innerHTML = '<option value="">First select a country</option>';
        citySelect.disabled = true;
    }
}

// Function to show commission info
function showCommissionInfo() {
    const agentSelect = document.getElementById('agent_id');
    const bookingAmount = document.getElementById('booking_amount').value;
    const commissionInfo = document.getElementById('commissionInfo');
    const commissionAmount = document.getElementById('commissionAmount');
    
    const selectedOption = agentSelect.options[agentSelect.selectedIndex];
    const commissionRate = selectedOption.getAttribute('data-rate');
    
    if (commissionRate && bookingAmount > 0) {
        const commission = (bookingAmount * commissionRate) / 100;
        commissionAmount.textContent = '₱' + commission.toFixed(2) + ' (' + commissionRate + '%)';
        commissionInfo.style.display = 'block';
    } else {
        commissionInfo.style.display = 'none';
    }
}

// Reset form function
function resetHotelForm() {
    document.getElementById('city').innerHTML = '<option value="">First select a country</option>';
    document.getElementById('city').disabled = true;
    document.getElementById('commissionInfo').style.display = 'none';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize city dropdown
    document.getElementById('city').disabled = true;
    
    // Set minimum check-out date based on check-in date
    document.getElementById('check_in').addEventListener('change', function() {
        var checkInDate = new Date(this.value);
        var checkOutField = document.getElementById('check_out');
        
        // Set min date for check-out (day after check-in)
        var minCheckOut = new Date(checkInDate);
        minCheckOut.setDate(minCheckOut.getDate() + 1);
        
        checkOutField.min = minCheckOut.toISOString().split('T')[0];
        
        // If current check-out is before min date, clear it
        if (checkOutField.value && new Date(checkOutField.value) < minCheckOut) {
            checkOutField.value = '';
        }
    });
    
    // Re-populate city if there are values
    const country = document.getElementById('country').value;
    if (country) {
        updateHotelCities();
        document.getElementById('city').value = "<?php echo isset($_POST['city']) ? $_POST['city'] : ''; ?>";
    }
});
</script>

<?php $conn->close(); ?>