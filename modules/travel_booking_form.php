<?php
// modules/travel_booking_form.php

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
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
    
    // Get country names from country codes
    $from_country_name = $conn->query("SELECT country_name FROM countries WHERE country_code = '$from_country'")->fetch_assoc()['country_name'];
    $to_country_name = $conn->query("SELECT country_name FROM countries WHERE country_code = '$to_country'")->fetch_assoc()['country_name'];
    
    // Generate booking ID
    $booking_id = 'TRV-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    
    $sql = "INSERT INTO travel_bookings (booking_id, traveler_name, travel_type, from_country, from_city, to_country, to_city, status) 
            VALUES ('$booking_id', '$traveler_name', '$travel_type', '$from_country_name', '$from_city', '$to_country_name', '$to_city', '$status')";
    
    if ($conn->query($sql)) {
        $message = "✅ Travel booking created successfully! Booking ID: <strong>$booking_id</strong>";
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
                        <?php while($country = $countries_result->fetch_assoc()): ?>
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
                        // Reset pointer for countries result
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

// Reset form function
function resetForm() {
    document.getElementById('from_city').innerHTML = '<option value="">First select a country</option>';
    document.getElementById('to_city').innerHTML = '<option value="">First select a country</option>';
    document.getElementById('from_city').disabled = true;
    document.getElementById('to_city').disabled = true;
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