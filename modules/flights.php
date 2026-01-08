<?php
// modules/flights.php

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passenger_name = $conn->real_escape_string($_POST['passenger_name']);
    $airline = $conn->real_escape_string($_POST['airline']);
    $flight_number = $conn->real_escape_string($_POST['flight_number']);
    $departure_airport = $conn->real_escape_string($_POST['departure_airport']);
    $arrival_airport = $conn->real_escape_string($_POST['arrival_airport']);
    $departure_date = $conn->real_escape_string($_POST['departure_date']);
    $arrival_date = $conn->real_escape_string($_POST['arrival_date']);
    $seat_class = $conn->real_escape_string($_POST['seat_class']);
    $seat_number = $conn->real_escape_string($_POST['seat_number']);
    $fare = (float)$_POST['fare'];
    $status = $conn->real_escape_string($_POST['status']);
    
    // Generate flight booking ID
    $flight_id = 'FLT-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    
    $sql = "INSERT INTO flight_bookings (flight_id, passenger_name, airline, flight_number, departure_airport, arrival_airport, departure_date, arrival_date, seat_class, seat_number, fare, status) 
            VALUES ('$flight_id', '$passenger_name', '$airline', '$flight_number', '$departure_airport', '$arrival_airport', '$departure_date', '$arrival_date', '$seat_class', '$seat_number', $fare, '$status')";
    
    if ($conn->query($sql)) {
        $message = "✅ Flight booking created successfully! Booking ID: <strong>$flight_id</strong>";
        $message_type = "success";
    } else {
        $message = "❌ Error creating flight booking: " . $conn->error;
        $message_type = "error";
    }
}

// Popular airlines and airports
$airlines = ['Japan Airlines', 'Philippine Airlines', 'Singapore Airlines', 'Emirates', 'Qatar Airways', 'Cathay Pacific', 'ANA', 'KLM', 'British Airways', 'Delta Airlines'];
$airports = [
    'NRT' => 'Narita Intl (Tokyo)',
    'HND' => 'Haneda (Tokyo)',
    'MNL' => 'Ninoy Aquino (Manila)',
    'SIN' => 'Changi (Singapore)',
    'DXB' => 'Dubai Intl',
    'LAX' => 'Los Angeles',
    'JFK' => 'John F Kennedy (NYC)',
    'LHR' => 'Heathrow (London)',
    'CDG' => 'Charles de Gaulle (Paris)',
    'FRA' => 'Frankfurt'
];
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">✈️ Flight Bookings Management</h5>
            
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <form class="row g-3" method="POST" action="">
                <div class="col-md-6">
                    <label for="passenger_name" class="form-label">Passenger Name *</label>
                    <input type="text" class="form-control" id="passenger_name" name="passenger_name" required placeholder="Ex. John Smith">
                </div>
                
                <div class="col-md-6">
                    <label for="airline" class="form-label">Airline *</label>
                    <select class="form-select" id="airline" name="airline" required>
                        <option value="">Select airline...</option>
                        <?php foreach($airlines as $airline): ?>
                        <option value="<?php echo $airline; ?>"><?php echo $airline; ?></option>
                        <?php endforeach; ?>
                        <option value="Other">Other Airline</option>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="flight_number" class="form-label">Flight Number *</label>
                    <input type="text" class="form-control" id="flight_number" name="flight_number" required placeholder="Ex. PR101">
                </div>
                
                <div class="col-md-6">
                    <label for="seat_class" class="form-label">Seat Class *</label>
                    <select class="form-select" id="seat_class" name="seat_class" required>
                        <option value="Economy">✈️ Economy</option>
                        <option value="Premium Economy">⭐ Premium Economy</option>
                        <option value="Business">💼 Business Class</option>
                        <option value="First Class">👑 First Class</option>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="departure_airport" class="form-label">Departure Airport *</label>
                    <select class="form-select" id="departure_airport" name="departure_airport" required>
                        <option value="">Select airport...</option>
                        <?php foreach($airports as $code => $name): ?>
                        <option value="<?php echo $code; ?>"><?php echo $code; ?> - <?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="arrival_airport" class="form-label">Arrival Airport *</label>
                    <select class="form-select" id="arrival_airport" name="arrival_airport" required>
                        <option value="">Select airport...</option>
                        <?php foreach($airports as $code => $name): ?>
                        <option value="<?php echo $code; ?>"><?php echo $code; ?> - <?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="departure_date" class="form-label">Departure Date & Time *</label>
                    <input type="datetime-local" class="form-control" id="departure_date" name="departure_date" required>
                </div>
                
                <div class="col-md-6">
                    <label for="arrival_date" class="form-label">Arrival Date & Time *</label>
                    <input type="datetime-local" class="form-control" id="arrival_date" name="arrival_date" required>
                </div>
                
                <div class="col-md-4">
                    <label for="seat_number" class="form-label">Seat Number</label>
                    <input type="text" class="form-control" id="seat_number" name="seat_number" placeholder="Ex. 14A">
                </div>
                
                <div class="col-md-4">
                    <label for="fare" class="form-label">Fare (Php) *</label>
                    <div class="input-group">
                        <span class="input-group-text">₱</span>
                        <input type="number" class="form-control" id="fare" name="fare" step="0.01" min="0" required>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label for="status" class="form-label">Status *</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="Confirmed">✅ Confirmed</option>
                        <option value="Pending">⏳ Pending</option>
                        <option value="Cancelled">❌ Cancelled</option>
                        <option value="Boarded">✈️ Boarded</option>
                        <option value="Completed">🏁 Completed</option>
                    </select>
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">Book Flight</button>
                    <button type="reset" class="btn btn-secondary">Clear Form</button>
                </div>
            </form>
            
            <!-- Display existing flight bookings -->
            <div class="mt-5">
                <h5 class="card-title">📋 Recent Flight Bookings</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Passenger</th>
                                <th>Flight</th>
                                <th>Route</th>
                                <th>Departure</th>
                                <th>Class</th>
                                <th>Fare</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $flights_result = $conn->query("SELECT * FROM flight_bookings ORDER BY departure_date DESC LIMIT 10");
                            while($flight = $flights_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $flight['flight_id']; ?></td>
                                <td><?php echo htmlspecialchars($flight['passenger_name']); ?></td>
                                <td><?php echo $flight['airline'] . ' ' . $flight['flight_number']; ?></td>
                                <td><?php echo $flight['departure_airport'] . ' → ' . $flight['arrival_airport']; ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($flight['departure_date'])); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                    switch($flight['seat_class']) {
                                        case 'Economy': echo 'secondary'; break;
                                        case 'Premium Economy': echo 'info'; break;
                                        case 'Business': echo 'warning'; break;
                                        case 'First Class': echo 'success'; break;
                                        default: echo 'primary';
                                    }
                                    ?>"><?php echo $flight['seat_class']; ?></span>
                                </td>
                                <td>$<?php echo number_format($flight['fare'], 2); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                    switch($flight['status']) {
                                        case 'Confirmed': echo 'success'; break;
                                        case 'Pending': echo 'warning'; break;
                                        case 'Cancelled': echo 'danger'; break;
                                        case 'Boarded': echo 'info'; break;
                                        case 'Completed': echo 'secondary'; break;
                                        default: echo 'primary';
                                    }
                                    ?>"><?php echo $flight['status']; ?></span>
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
// Set arrival date based on departure date
document.getElementById('departure_date').addEventListener('change', function() {
    var departureDate = new Date(this.value);
    var arrivalField = document.getElementById('arrival_date');
    
    // Set min arrival time (1 hour after departure)
    var minArrival = new Date(departureDate);
    minArrival.setHours(minArrival.getHours() + 1);
    
    // Format for datetime-local input
    var minArrivalStr = minArrival.toISOString().slice(0, 16);
    arrivalField.min = minArrivalStr;
    
    // If current arrival is before min, clear it
    if (arrivalField.value && new Date(arrivalField.value) < minArrival) {
        arrivalField.value = '';
    }
});

// Set default departure date to tomorrow
window.addEventListener('DOMContentLoaded', function() {
    var tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    tomorrow.setHours(14, 0, 0, 0); // Set to 2 PM
    
    var departureField = document.getElementById('departure_date');
    departureField.value = tomorrow.toISOString().slice(0, 16);
    departureField.min = new Date().toISOString().slice(0, 16);
    
    // Trigger change event to set arrival min
    departureField.dispatchEvent(new Event('change'));
});
</script>

<?php $conn->close(); ?>