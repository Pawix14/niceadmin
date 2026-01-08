<?php
// modules/travel_booking_form.php - UPDATED VERSION

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch countries from database
$countries_result = $conn->query("SELECT * FROM countries ORDER BY country_name");
$countries = [];

// Fetch cities for each country
$cities_result = $conn->query("SELECT c.country_code, ci.city_name FROM cities ci 
                               INNER JOIN countries c ON ci.country_code = c.country_code 
                               ORDER BY c.country_name, ci.city_name");

$cities_by_country = [];
while ($row = $cities_result->fetch_assoc()) {
    $cities_by_country[$row['country_code']][] = $row['city_name'];
}

// Fetch active travel agents
$agents_result = $conn->query("SELECT agent_id, agent_name, commission_rate FROM travel_agents WHERE status = 'Active' ORDER BY agent_name");

// Handle form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $traveler_name = $conn->real_escape_string($_POST['traveler_name']);
    $travel_type = $conn->real_escape_string($_POST['travel_type']);
    $from_country = $conn->real_escape_string($_POST['from_country']);
    $from_city = $conn->real_escape_string($_POST['from_city']);
    $to_country = $conn->real_escape_string($_POST['to_country']);
    $to_city = $conn->real_escape_string($_POST['to_city']);
    $status = $conn->real_escape_string($_POST['status']);
    $agent_id = $conn->real_escape_string($_POST['agent_id']);
    $booking_amount = (float)$_POST['booking_amount'];
    
    // Get country names from country codes
    $from_country_name = $conn->query("SELECT country_name FROM countries WHERE country_code = '$from_country'")->fetch_assoc()['country_name'];
    $to_country_name = $conn->query("SELECT country_name FROM countries WHERE country_code = '$to_country'")->fetch_assoc()['country_name'];
    
    // Generate booking ID
    $booking_id = 'TRV-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    
    $sql = "INSERT INTO travel_bookings (booking_id, traveler_name, travel_type, from_country, from_city, to_country, to_city, status, agent_id, booking_amount) 
            VALUES ('$booking_id', '$traveler_name', '$travel_type', '$from_country_name', '$from_city', '$to_country_name', '$to_city', '$status', '$agent_id', $booking_amount)";
    
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
                          VALUES ('$commission_id', '$agent_id', '$booking_id', 'Travel', $booking_amount, $commission_rate, $commission_amount, 'Pending')");
            
            // Update agent's total bookings
            $conn->query("UPDATE travel_agents SET total_bookings = total_bookings + 1 WHERE agent_id = '$agent_id'");
            
            $message = "✅ Travel booking created successfully! Booking ID: <strong>$booking_id</strong><br>";
            $message .= "📊 Agent Commission: <strong>₱" . number_format($commission_amount, 2) . "</strong> (" . $commission_rate . "% of ₱" . number_format($booking_amount, 2) . ")";
        } else {
            $message = "✅ Travel booking created successfully! Booking ID: <strong>$booking_id</strong>";
        }
        
        $message_type = "success";
        
        // Clear form fields after successful submission
        echo '<script>document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("bookingForm").reset();
            document.getElementById("from_city").disabled = true;
            document.getElementById("to_city").disabled = true;
        });</script>';
    } else {
        $message = "❌ Error creating booking: " . $conn->error;
        $message_type = "error";
    }
}
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">✈️ Travel Booking Form</h5>
            
            <!-- Display success/error message -->
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <!-- Travel Booking Form -->
            <form class="row g-3" method="POST" action="" id="bookingForm">
                <div class="col-md-6">
                    <label for="traveler_name" class="form-label">Traveler Name *</label>
                    <input type="text" class="form-control" id="traveler_name" name="traveler_name" required placeholder="Ex. Gabriel Paolo">
                </div>
                
                <div class="col-md-6">
                    <label for="travel_type" class="form-label">Travel Type *</label>
                    <select class="form-select" id="travel_type" name="travel_type" required>
                        <option value="">Select travel type...</option>
                        <option value="Airplane">✈️ Airplane</option>
                        <option value="Ship">🛳️ Ship/Cruise</option>
                        <option value="Bus">🚌 Bus</option>
                        <option value="Train">🚂 Train</option>
                        <option value="Car">🚗 Car/Rental</option>
                    </select>
                </div>
                
                <!-- FROM LOCATION -->
                <div class="col-md-6">
                    <label for="from_country" class="form-label">From Country *</label>
                    <select class="form-select" id="from_country" name="from_country" required onchange="updateCities('from')">
                        <option value="">Select a country...</option>
                        <?php 
                        $countries_result->data_seek(0);
                        while($country = $countries_result->fetch_assoc()): ?>
                        <option value="<?php echo $country['country_code']; ?>">
                            <?php echo htmlspecialchars($country['country_name']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="from_city" class="form-label">From City *</label>
                    <select class="form-select" id="from_city" name="from_city" required disabled>
                        <option value="">First select a country</option>
                    </select>
                </div>
                
                <!-- TO LOCATION -->
                <div class="col-md-6">
                    <label for="to_country" class="form-label">To Country *</label>
                    <select class="form-select" id="to_country" name="to_country" required onchange="updateCities('to')">
                        <option value="">Select a country...</option>
                        <?php 
                        $countries_result->data_seek(0);
                        while($country = $countries_result->fetch_assoc()): 
                        ?>
                        <option value="<?php echo $country['country_code']; ?>">
                            <?php echo htmlspecialchars($country['country_name']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="to_city" class="form-label">To City *</label>
                    <select class="form-select" id="to_city" name="to_city" required disabled>
                        <option value="">First select a country</option>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="booking_amount" class="form-label">Booking Amount (₱) *</label>
                    <div class="input-group">
                        <span class="input-group-text">₱</span>
                        <input type="number" class="form-control" id="booking_amount" name="booking_amount" step="0.01" min="0" required>
                    </div>
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
                
                <div class="col-md-12">
                    <label for="status" class="form-label">Booking Status *</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="Booked">✅ Booked (Active)</option>
                        <option value="Cancelled">❌ Cancelled (Rejected)</option>
                        <option value="Completed">🏁 Completed (Finished)</option>
                    </select>
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">Submit Booking</button>
                    <button type="reset" class="btn btn-secondary" onclick="resetForm()">Clear Form</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Define cities data from PHP to JavaScript
const citiesData = <?php echo json_encode($cities_by_country); ?>;

// Function to update city dropdown based on selected country
function updateCities(direction) {
    const countrySelect = document.getElementById(direction + '_country');
    const citySelect = document.getElementById(direction + '_city');
    const selectedCountry = countrySelect.value;
    
    // Clear current city options
    citySelect.innerHTML = '<option value="">Select a city...</option>';
    
    if (selectedCountry && citiesData[selectedCountry]) {
        // Add cities for selected country
        citiesData[selectedCountry].forEach(city => {
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

// Calculate commission when booking amount changes
document.getElementById('booking_amount').addEventListener('input', showCommissionInfo);

// Reset form function
function resetForm() {
    document.getElementById('from_city').innerHTML = '<option value="">First select a country</option>';
    document.getElementById('to_city').innerHTML = '<option value="">First select a country</option>';
    document.getElementById('from_city').disabled = true;
    document.getElementById('to_city').disabled = true;
    document.getElementById('commissionInfo').style.display = 'none';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize city dropdowns
    document.getElementById('from_city').disabled = true;
    document.getElementById('to_city').disabled = true;
    
    // Re-populate cities if there are values (form was submitted with errors)
    const fromCountry = document.getElementById('from_country').value;
    const toCountry = document.getElementById('to_country').value;
    
    if (fromCountry) {
        updateCities('from');
        document.getElementById('from_city').value = "<?php echo isset($_POST['from_city']) ? $_POST['from_city'] : ''; ?>";
    }
    
    if (toCountry) {
        updateCities('to');
        document.getElementById('to_city').value = "<?php echo isset($_POST['to_city']) ? $_POST['to_city'] : ''; ?>";
    }
});
</script>

<?php
$conn->close();
?>