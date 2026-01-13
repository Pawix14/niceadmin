<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if flight_bookings table exists and add missing columns
$check_table = $conn->query("SHOW TABLES LIKE 'flight_bookings'");
if ($check_table->num_rows == 0) {
    // Create table if it doesn't exist
    $conn->query("CREATE TABLE flight_bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id VARCHAR(50) UNIQUE NOT NULL,
        passenger_name VARCHAR(100) NOT NULL,
        airline VARCHAR(100) NOT NULL,
        flight_number VARCHAR(20) NOT NULL,
        departure_airport VARCHAR(10) NOT NULL,
        arrival_airport VARCHAR(10) NOT NULL,
        departure_date DATE NOT NULL,
        arrival_date DATE,
        departure_time TIME,
        arrival_time TIME,
        seat_class VARCHAR(50) NOT NULL,
        passengers INT NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        status VARCHAR(50) DEFAULT 'Pending',
        booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} else {
    // Add missing columns if table exists
    $columns = $conn->query("SHOW COLUMNS FROM flight_bookings LIKE 'departure_time'");
    if ($columns->num_rows == 0) {
        $conn->query("ALTER TABLE flight_bookings ADD COLUMN departure_time TIME AFTER arrival_date");
    }
    $columns = $conn->query("SHOW COLUMNS FROM flight_bookings LIKE 'arrival_time'");
    if ($columns->num_rows == 0) {
        $conn->query("ALTER TABLE flight_bookings ADD COLUMN arrival_time TIME AFTER departure_time");
    }
}
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['search_flights'])) {
        // Search flights
        $from_airport = $conn->real_escape_string($_POST['from_airport']);
        $to_airport = $conn->real_escape_string($_POST['to_airport']);
        $departure_date = $conn->real_escape_string($_POST['departure_date']);
        $return_date = isset($_POST['return_date']) ? $conn->real_escape_string($_POST['return_date']) : '';
        $travelers = (int)$_POST['travelers'];
        $seat_class = $conn->real_escape_string($_POST['seat_class']);
        
        // Store search in session
        $_SESSION['flight_search'] = [
            'from' => $from_airport,
            'to' => $to_airport,
            'departure' => $departure_date,
            'return' => $return_date,
            'travelers' => $travelers,
            'class' => $seat_class
        ];
        
        // Don't redirect to avoid losing search results
        
    } elseif (isset($_POST['book_flight'])) {
        // Book flight
        $passenger_name = $conn->real_escape_string($_POST['passenger_name']);
        $email = $conn->real_escape_string($_POST['email']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $airline = $conn->real_escape_string($_POST['airline']);
        $flight_number = $conn->real_escape_string($_POST['flight_number']);
        $departure_airport = $conn->real_escape_string($_POST['departure_airport']);
        $arrival_airport = $conn->real_escape_string($_POST['arrival_airport']);
        $departure_date = $conn->real_escape_string($_POST['departure_date']);
        $departure_time = isset($_POST['departure_time']) ? $conn->real_escape_string($_POST['departure_time']) : '';
        $arrival_time = isset($_POST['arrival_time']) ? $conn->real_escape_string($_POST['arrival_time']) : '';
        $arrival_date = isset($_POST['arrival_date']) ? $conn->real_escape_string($_POST['arrival_date']) : $departure_date;
        $seat_class = $conn->real_escape_string($_POST['seat_class']);
        $passengers = (int)$_POST['passengers'];
        $total_fare = (float)$_POST['total_fare'];
        
        $booking_id = 'FLT-' . date('YmdHis') . rand(100, 999);
        
        $sql = "INSERT INTO flight_bookings (booking_id, passenger_name, airline, flight_number, departure_airport, arrival_airport, departure_date, arrival_date, departure_time, arrival_time, seat_class, passengers, total_amount, status)
                VALUES ('$booking_id', '$passenger_name', '$airline', '$flight_number', '$departure_airport', '$arrival_airport', '$departure_date', '$arrival_date', '$departure_time', '$arrival_time', '$seat_class', $passengers, $total_fare, 'Confirmed')";
        
        if ($conn->query($sql)) {
            $message = "✅ Flight booked successfully! Booking ID: <strong>$booking_id</strong>";
            $message_type = "success";
            
            // Clear the search session after booking
            unset($_SESSION['flight_search']);
        } else {
            $message = "❌ Error booking flight: " . $conn->error;
            $message_type = "error";
        }
    }
}

// Airport data with available airlines
$airports = [
    'MNL' => [
        'name' => 'Manila Ninoy Aquino Intl',
        'city' => 'Manila',
        'country' => 'Philippines',
        'airlines' => ['Philippine Airlines', 'Cebu Pacific', 'AirAsia', 'Qatar Airways', 'Singapore Airlines']
    ],
    'SIN' => [
        'name' => 'Singapore Changi',
        'city' => 'Singapore',
        'country' => 'Singapore',
        'airlines' => ['Singapore Airlines', 'Scoot', 'Emirates', 'Qatar Airways', 'Cathay Pacific']
    ],
    'HKG' => [
        'name' => 'Hong Kong International',
        'city' => 'Hong Kong',
        'country' => 'China',
        'airlines' => ['Cathay Pacific', 'Hong Kong Airlines', 'Emirates', 'Qatar Airways', 'Singapore Airlines']
    ],
    'NRT' => [
        'name' => 'Tokyo Narita',
        'city' => 'Tokyo',
        'country' => 'Japan',
        'airlines' => ['Japan Airlines', 'ANA', 'Qatar Airways', 'Singapore Airlines', 'Cathay Pacific']
    ],
    'BKK' => [
        'name' => 'Bangkok Suvarnabhumi',
        'city' => 'Bangkok',
        'country' => 'Thailand',
        'airlines' => ['Thai Airways', 'Bangkok Airways', 'Qatar Airways', 'Emirates', 'Singapore Airlines']
    ],
    'DXB' => [
        'name' => 'Dubai International',
        'city' => 'Dubai',
        'country' => 'UAE',
        'airlines' => ['Emirates', 'Qatar Airways', 'Etihad Airways', 'Flydubai', 'Singapore Airlines']
    ],
    'AMS' => [
        'name' => 'Amsterdam Schiphol',
        'city' => 'Amsterdam',
        'country' => 'Netherlands',
        'airlines' => ['KLM', 'Qatar Airways', 'Emirates', 'Singapore Airlines', 'British Airways']
    ],
    'LAX' => [
        'name' => 'Los Angeles International',
        'city' => 'Los Angeles',
        'country' => 'USA',
        'airlines' => ['Delta Airlines', 'American Airlines', 'United Airlines', 'Qatar Airways', 'Singapore Airlines']
    ]
];

// Airline logo mapping - CORRECTED BASED ON YOUR FILES
function getAirlineLogo($airline) {
    $logos = [
        'Philippine Airlines' => 'philippines-airline.png',
        'Cebu Pacific' => 'philippines-airline.png',
        'AirAsia' => 'philippines-airline.png',
        'Singapore Airlines' => 'singapore-airline.png', // Fixed: was 'singapore-airlines.png'
        'Qatar Airways' => 'qatar-airlines.png',
        'Cathay Pacific' => 'cathay-airlines.png',
        'Japan Airlines' => 'japan-airline.png',
        'ANA' => 'japan-airline.png',
        'Emirates' => 'emirates-airline.png',
        'Scoot' => 'singapore-airline.png',
        'Thai Airways' => 'philippines-airline.png',
        'Bangkok Airways' => 'philippines-airline.png',
        'Etihad Airways' => 'emirates-airline.png',
        'Flydubai' => 'emirates-airline.png',
        'KLM' => 'philippines-airline.png',
        'British Airways' => 'philippines-airline.png',
        'Delta Airlines' => 'philippines-airline.png',
        'American Airlines' => 'philippines-airline.png',
        'United Airlines' => 'philippines-airline.png',
        'Hong Kong Airlines' => 'cathay-airlines.png'
    ];

    return isset($logos[$airline]) ? $logos[$airline] : 'airplane.png';
}

// Helper function to check if image exists
function airlineLogoExists($airline) {
    $logoFile = getAirlineLogo($airline);
    $imagePath = 'assets/img/airlines/' . $logoFile;
    
    // Check if file exists in the directory
    if (file_exists($imagePath)) {
        return $logoFile;
    }
    
    // Fallback to default
    return 'airplane.png';
}

// Sample flight data based on route
function getFlightsForRoute($from, $to) {
    $flights = [
        'MNL-SIN' => [
            ['airline' => 'Philippine Airlines', 'flight_no' => 'PR 507', 'departure' => '08:00', 'arrival' => '11:30', 'duration' => '3h 30m', 'price' => 250, 'stops' => 0],
            ['airline' => 'Singapore Airlines', 'flight_no' => 'SQ 916', 'departure' => '14:20', 'arrival' => '17:50', 'duration' => '3h 30m', 'price' => 280, 'stops' => 0],
            ['airline' => 'Cebu Pacific', 'flight_no' => '5J 803', 'departure' => '22:15', 'arrival' => '01:45', 'duration' => '3h 30m', 'price' => 180, 'stops' => 0],
            ['airline' => 'Qatar Airways', 'flight_no' => 'QR 931', 'departure' => '13:45', 'arrival' => '20:15', 'duration' => '6h 30m', 'price' => 350, 'stops' => 1],
        ],
        'MNL-HKG' => [
            ['airline' => 'Cathay Pacific', 'flight_no' => 'CX 902', 'departure' => '09:30', 'arrival' => '12:00', 'duration' => '2h 30m', 'price' => 220, 'stops' => 0],
            ['airline' => 'Philippine Airlines', 'flight_no' => 'PR 300', 'departure' => '16:45', 'arrival' => '19:15', 'duration' => '2h 30m', 'price' => 200, 'stops' => 0],
            ['airline' => 'AirAsia', 'flight_no' => 'Z2 272', 'departure' => '21:00', 'arrival' => '23:30', 'duration' => '2h 30m', 'price' => 150, 'stops' => 0],
        ],
        'MNL-NRT' => [
            ['airline' => 'Japan Airlines', 'flight_no' => 'JL 740', 'departure' => '10:15', 'arrival' => '15:30', 'duration' => '4h 15m', 'price' => 320, 'stops' => 0],
            ['airline' => 'Philippine Airlines', 'flight_no' => 'PR 422', 'departure' => '23:30', 'arrival' => '04:45', 'duration' => '4h 15m', 'price' => 300, 'stops' => 0],
            ['airline' => 'ANA', 'flight_no' => 'NH 822', 'departure' => '14:00', 'arrival' => '19:15', 'duration' => '4h 15m', 'price' => 340, 'stops' => 0],
        ],
        'SIN-MNL' => [
            ['airline' => 'Singapore Airlines', 'flight_no' => 'SQ 915', 'departure' => '07:30', 'arrival' => '11:00', 'duration' => '3h 30m', 'price' => 280, 'stops' => 0],
            ['airline' => 'Scoot', 'flight_no' => 'TR 384', 'departure' => '13:45', 'arrival' => '17:15', 'duration' => '3h 30m', 'price' => 160, 'stops' => 0],
            ['airline' => 'Philippine Airlines', 'flight_no' => 'PR 508', 'departure' => '20:00', 'arrival' => '23:30', 'duration' => '3h 30m', 'price' => 250, 'stops' => 0],
        ],
        'default' => [
            ['airline' => 'Qatar Airways', 'flight_no' => 'QR 933', 'departure' => '18:35', 'arrival' => '20:45', 'duration' => '2h 10m', 'price' => 400, 'stops' => 0],
            ['airline' => 'Emirates', 'flight_no' => 'EK 332', 'departure' => '14:20', 'arrival' => '16:30', 'duration' => '2h 10m', 'price' => 420, 'stops' => 0],
            ['airline' => 'Singapore Airlines', 'flight_no' => 'SQ 112', 'departure' => '09:15', 'arrival' => '11:25', 'duration' => '2h 10m', 'price' => 380, 'stops' => 0],
            ['airline' => 'Cathay Pacific', 'flight_no' => 'CX 710', 'departure' => '22:00', 'arrival' => '00:10', 'duration' => '2h 10m', 'price' => 350, 'stops' => 0],
        ]
    ];

    $route = $from . '-' . $to;
    return isset($flights[$route]) ? $flights[$route] : $flights['default'];
}

// Get current search or set defaults
$from = isset($_SESSION['flight_search']['from']) ? $_SESSION['flight_search']['from'] : 'MNL';
$to = isset($_SESSION['flight_search']['to']) ? $_SESSION['flight_search']['to'] : 'SIN';
$departure = isset($_SESSION['flight_search']['departure']) ? $_SESSION['flight_search']['departure'] : date('Y-m-d', strtotime('+3 days'));
$return = isset($_SESSION['flight_search']['return']) ? $_SESSION['flight_search']['return'] : date('Y-m-d', strtotime('+10 days'));
$travelers = isset($_SESSION['flight_search']['travelers']) ? $_SESSION['flight_search']['travelers'] : 1;
$class = isset($_SESSION['flight_search']['class']) ? $_SESSION['flight_search']['class'] : 'Economy';

$available_flights = getFlightsForRoute($from, $to);
?>

<style>
/* Minimal Design */
.flight-search-container {
    background: #f8f9fa;
    color: #333;
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 30px;
    border: 1px solid #e0e0e0;
}

.flight-search-container h1 {
    color: #333;
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.flight-search-container .subtitle {
    color: #666;
    font-size: 1.1rem;
    margin-bottom: 20px;
}

.search-tabs {
    display: flex;
    background: white;
    border-radius: 8px;
    padding: 5px;
    margin-bottom: 20px;
    border: 1px solid #e0e0e0;
}

.search-tab {
    flex: 1;
    padding: 12px;
    background: transparent;
    border: none;
    color: #666;
    font-weight: 500;
    cursor: pointer;
    border-radius: 6px;
    transition: all 0.3s;
}

.search-tab.active {
    background: #666;
    color: white;
}

.search-form {
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border: 1px solid #e0e0e0;
}

.form-row {
    display: flex;
    gap: 15px;
    align-items: flex-end;
}

.form-group {
    flex: 1;
}

.form-label {
    display: block;
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 5px;
    font-weight: 500;
}

.form-control-flight {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    font-size: 1rem;
    transition: all 0.3s;
}

.form-control-flight:focus {
    border-color: #666;
    box-shadow: 0 0 0 3px rgba(102, 102, 102, 0.1);
    outline: none;
}

.swap-btn {
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #666;
    transition: all 0.3s;
}

.swap-btn:hover {
    background: #666;
    border-color: #666;
    color: white;
}

.search-btn {
    background: #666;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 12px 30px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    height: 45px;
}

.search-btn:hover {
    background: #555;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 102, 102, 0.3);
}

/* Flight Results */
.flight-results-header {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: 1px solid #e0e0e0;
}

.flight-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
    transition: all 0.3s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.flight-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #666;
}

.airline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
}

.airline-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.airline-logo-small {
    width: 40px;
    height: 40px;
    background: #f8f9fa;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border: 1px solid #e0e0e0;
}

.airline-logo-small img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.airline-details h4 {
    margin: 0;
    color: #333;
    font-size: 1.1rem;
}

.airline-details p {
    margin: 5px 0 0;
    color: #666;
    font-size: 0.9rem;
}

.flight-price {
    text-align: right;
}

.flight-price h3 {
    margin: 0;
    color: #666;
    font-size: 1.8rem;
}

.flight-price p {
    margin: 5px 0 0;
    color: #666;
    font-size: 0.9rem;
}

.flight-timeline {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    position: relative;
}

.time-block {
    text-align: center;
    flex: 1;
}

.time {
    font-size: 1.5rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.airport-code {
    font-size: 1.1rem;
    font-weight: 500;
    color: #666;
    margin-bottom: 5px;
}

.airport-name {
    font-size: 0.9rem;
    color: #666;
}

.timeline-center {
    flex: 2;
    text-align: center;
    position: relative;
}

.duration {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 10px;
}

.timeline-line {
    height: 2px;
    background: #666;
    position: relative;
    margin: 0 20px;
}

.timeline-line::before,
.timeline-line::after {
    content: '';
    position: absolute;
    width: 10px;
    height: 10px;
    background: #666;
    border-radius: 50%;
    top: -4px;
}

.timeline-line::before {
    left: 0;
}

.timeline-line::after {
    right: 0;
}

.flight-features {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.feature-badge {
    background: #f8f9fa;
    color: #666;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    border: 1px solid #e0e0e0;
}

.flight-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #f0f0f0;
}

.flight-details {
    display: flex;
    gap: 15px;
    font-size: 0.9rem;
    color: #666;
}

.select-btn {
    background: #666;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 10px 25px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.select-btn:hover {
    background: #555;
}

/* Manual Booking Form */
.manual-booking-form {
    background: white;
    border-radius: 8px;
    padding: 25px;
    margin-top: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: 1px solid #e0e0e0;
}

.manual-booking-form h3 {
    color: #333;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
}

/* Airport Airlines Display */
.airport-airlines {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-top: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: 1px solid #e0e0e0;
}

.airline-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.airline-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    transition: all 0.3s;
}

.airline-item:hover {
    border-color: #666;
    background: #f8f9fa;
}

.airline-icon {
    width: 30px;
    height: 30px;
    background: #f8f9fa;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666;
    font-weight: bold;
    font-size: 0.8rem;
    border: 1px solid #e0e0e0;
}

.btn-primary {
    background-color: #666;
    border-color: #666;
}

.btn-primary:hover {
    background-color: #555;
    border-color: #555;
}

/* Responsive */
@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
    }
    
    .swap-btn {
        align-self: center;
        margin: 10px 0;
    }
    
    .flight-timeline {
        flex-direction: column;
        gap: 15px;
    }
    
    .timeline-center {
        order: 3;
        width: 100%;
    }
}
</style>

<div class="container-fluid">
    <!-- Flight Search Header -->
    <div class="flight-search-container">
        <h1>✈️ Discover the best flight deals</h1>
        <p class="subtitle">Your next take-off awaits</p>
        
        <div class="search-tabs">
            <button type="button" class="search-tab active" data-tab="round-trip">Round-trip</button>
            <button type="button" class="search-tab" data-tab="one-way">One-way</button>
            <button type="button" class="search-tab" data-tab="multi-city">Multi-city</button>
        </div>
        
        <form method="POST" action="" class="search-form" id="flightSearchForm">
            <input type="hidden" name="search_flights" value="1">
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Leaving from</label>
                    <select name="from_airport" class="form-control-flight" required>
                        <?php foreach($airports as $code => $data): ?>
                            <option value="<?php echo $code; ?>" <?php echo $from == $code ? 'selected' : ''; ?>>
                                <?php echo $code; ?> - <?php echo $data['city']; ?>, <?php echo $data['country']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="button" class="swap-btn" onclick="swapAirports()" title="Swap airports">
                        <i class="bi bi-arrow-left-right"></i>
                    </button>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Going to</label>
                    <select name="to_airport" class="form-control-flight" required>
                        <?php foreach($airports as $code => $data): ?>
                            <option value="<?php echo $code; ?>" <?php echo $to == $code ? 'selected' : ''; ?>>
                                <?php echo $code; ?> - <?php echo $data['city']; ?>, <?php echo $data['country']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Departure</label>
                    <input type="date" name="departure_date" class="form-control-flight" value="<?php echo $departure; ?>" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="form-group" id="returnDateGroup">
                    <label class="form-label">Return (Optional)</label>
                    <input type="date" name="return_date" class="form-control-flight" value="<?php echo $return; ?>" min="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            
            <div class="form-row mt-3">
                <div class="form-group">
                    <label class="form-label">Travelers</label>
                    <select name="travelers" class="form-control-flight">
                        <?php for($i = 1; $i <= 9; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo $travelers == $i ? 'selected' : ''; ?>>
                                <?php echo $i; ?> <?php echo $i == 1 ? 'adult' : 'adults'; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Seat Class</label>
                    <select name="seat_class" class="form-control-flight">
                        <option value="Economy" <?php echo $class == 'Economy' ? 'selected' : ''; ?>>Economy</option>
                        <option value="Premium Economy" <?php echo $class == 'Premium Economy' ? 'selected' : ''; ?>>Premium Economy</option>
                        <option value="Business" <?php echo $class == 'Business' ? 'selected' : ''; ?>>Business Class</option>
                        <option value="First Class" <?php echo $class == 'First Class' ? 'selected' : ''; ?>>First Class</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="search-btn w-100">
                        <i class="bi bi-search me-2"></i>Search Flights
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <!-- Available Airlines for Selected Airports -->
    <div class="airport-airlines">
        <h3>✈️ Airlines operating from <?php echo $airports[$from]['city']; ?> to <?php echo $airports[$to]['city']; ?></h3>
        <p class="text-muted">Select an airline to see available flights</p>
        
        <div class="airline-grid">
            <?php 
            // Get airlines that operate from both airports
            $from_airlines = $airports[$from]['airlines'];
            $to_airlines = $airports[$to]['airlines'];
            $common_airlines = array_intersect($from_airlines, $to_airlines);
            
            foreach($common_airlines as $airline): 
                $airline_code = substr(strtoupper(preg_replace('/[^A-Z]/', '', $airline)), 0, 2);
            ?>
            <div class="airline-item">
                <div class="airline-icon"><?php echo $airline_code; ?></div>
                <div>
                    <strong><?php echo $airline; ?></strong>
                    <div class="text-muted" style="font-size: 0.8rem;">Operates daily flights</div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Flight Results -->
    <?php if (isset($_SESSION['flight_search']) || isset($_POST['search_flights'])): ?>
    <div class="flight-results-header">
        <h3>Available Flights from <?php echo $airports[$from]['city']; ?> to <?php echo $airports[$to]['city']; ?></h3>
        <p class="text-muted">Departure: <?php echo date('F d, Y', strtotime($departure)); ?></p>
    </div>
    
    <?php foreach($available_flights as $index => $flight): 
        $logoFile = airlineLogoExists($flight['airline']);
    ?>
    <div class="flight-card">
        <div class="airline-header">
            <div class="airline-info">
                <div class="airline-logo-small">
                    <img src="assets/img/airlines/<?php echo $logoFile; ?>" alt="<?php echo $flight['airline']; ?>" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <span style="display: none; font-size: 0.7rem; color: #666; font-weight: bold;">
                        <?php echo strtoupper(substr($flight['airline'], 0, 2)); ?>
                    </span>
                </div>
                <div class="airline-details">
                    <h4><?php echo $flight['airline']; ?></h4>
                    <p><?php echo $flight['flight_no']; ?> • <?php echo $flight['stops']; ?> stop<?php echo $flight['stops'] != 1 ? 's' : ''; ?></p>
                </div>
            </div>
            <div class="flight-price">
                <h3>₱<?php echo number_format($flight['price'] * $travelers, 2); ?></h3>
                <p>Total for <?php echo $travelers; ?> traveler<?php echo $travelers != 1 ? 's' : ''; ?></p>
            </div>
        </div>
        
        <div class="flight-timeline">
            <div class="time-block">
                <div class="time"><?php echo $flight['departure']; ?></div>
                <div class="airport-code"><?php echo $from; ?></div>
                <div class="airport-name"><?php echo $airports[$from]['city']; ?></div>
            </div>
            
            <div class="timeline-center">
                <div class="duration"><?php echo $flight['duration']; ?></div>
                <div class="timeline-line"></div>
                <div class="text-muted mt-2" style="font-size: 0.9rem;">
                    <i class="bi bi-airplane"></i> 
                    <?php echo $flight['stops'] == 0 ? 'Direct flight' : $flight['stops'] . ' stop(s)'; ?>
                </div>
            </div>
            
            <div class="time-block">
                <div class="time"><?php echo $flight['arrival']; ?></div>
                <div class="airport-code"><?php echo $to; ?></div>
                <div class="airport-name"><?php echo $airports[$to]['city']; ?></div>
            </div>
        </div>
        
        <div class="flight-features">
            <span class="feature-badge"><i class="bi bi-bag-check"></i> 20kg Baggage</span>
            <span class="feature-badge"><i class="bi bi-utensils"></i> Meals included</span>
            <span class="feature-badge"><i class="bi bi-wifi"></i> Free WiFi</span>
            <span class="feature-badge"><i class="bi bi-tv"></i> In-flight entertainment</span>
        </div>
        
        <div class="flight-actions">
            <div class="flight-details">
                <span><i class="bi bi-clock"></i> Duration: <?php echo $flight['duration']; ?></span>
                <span><i class="bi bi-calendar"></i> Date: <?php echo date('M d, Y', strtotime($departure)); ?></span>
                <span><i class="bi bi-people"></i> Seats available: 12</span>
            </div>
            <button class="select-btn" onclick="bookFlight(<?php echo $index; ?>)">
                <i class="bi bi-check-circle me-2"></i>Select Flight
            </button>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Manual Booking Form -->
    <div class="manual-booking-form">
        <h3>📝 Flight Booking</h3>
        <p class="text-muted">Can't find your flight? Book it manually here.</p>
        
        <form method="POST" action="" id="manualBookingForm">
            <input type="hidden" name="book_flight" value="1">
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Passenger Name *</label>
                    <input type="text" name="passenger_name" class="form-control" placeholder="Enter full name" required>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Phone *</label>
                    <input type="tel" name="phone" class="form-control" placeholder="+63 912 345 6789" required>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Airline *</label>
                    <select name="airline" class="form-select" required>
                        <option value="">Select airline...</option>
                        <?php 
                        $all_airlines = [];
                        foreach($airports as $data) {
                            $all_airlines = array_merge($all_airlines, $data['airlines']);
                        }
                        $all_airlines = array_unique($all_airlines);
                        sort($all_airlines);
                        foreach($all_airlines as $airline): ?>
                            <option value="<?php echo $airline; ?>"><?php echo $airline; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Flight Number *</label>
                    <input type="text" name="flight_number" class="form-control" placeholder="PR 101" required>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Departure Airport *</label>
                    <select name="departure_airport" class="form-select" required>
                        <?php foreach($airports as $code => $data): ?>
                            <option value="<?php echo $code; ?>"><?php echo $code; ?> - <?php echo $data['city']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Arrival Airport *</label>
                    <select name="arrival_airport" class="form-select" required>
                        <?php foreach($airports as $code => $data): ?>
                            <option value="<?php echo $code; ?>"><?php echo $code; ?> - <?php echo $data['city']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Departure Date *</label>
                    <input type="date" name="departure_date" class="form-control" required>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Departure Time *</label>
                    <input type="time" name="departure_time" class="form-control" required>
                    <small class="text-muted">Flight's scheduled departure time</small>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Arrival Date</label>
                    <input type="date" name="arrival_date" class="form-control" readonly style="background-color: #f8f9fa;">
                    <small class="text-muted">Auto-calculated based on departure</small>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Arrival Time</label>
                    <input type="time" name="arrival_time" class="form-control" readonly style="background-color: #f8f9fa;">
                    <small class="text-muted">Flight's scheduled arrival time</small>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Flight Duration</label>
                    <input type="text" name="flight_duration" class="form-control" readonly style="background-color: #f8f9fa;" placeholder="e.g., 3h 30m">
                    <small class="text-muted">Total flight time</small>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Seat Class *</label>
                    <select name="seat_class" class="form-select" required>
                        <option value="Economy">Economy</option>
                        <option value="Premium Economy">Premium Economy</option>
                        <option value="Business">Business Class</option>
                        <option value="First Class">First Class</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Passengers *</label>
                    <input type="number" name="passengers" class="form-control" min="1" max="10" value="1" required>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Seat Number (Optional)</label>
                    <input type="text" name="seat_number" class="form-control" placeholder="e.g., 12A" readonly>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="showSeatMap()">Choose Seat</button>
                    <small class="text-muted d-block">Click "Choose Seat" to select from visual seat map</small>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Total Fare (₱) *</label>
                    <div class="input-group">
                        <span class="input-group-text">₱</span>
                        <input type="number" name="total_fare" class="form-control" step="0.01" min="0" value="5000.00" required>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="confirmBooking" required>
                        <label class="form-check-label" for="confirmBooking">
                            I confirm that all information provided is accurate
                        </label>
                    </div>
                </div>
                <div class="col-12 text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-check-circle me-2"></i>Book Flight Now
                    </button>
                </div>

<script>
function swapAirports() {
    const fromSelect = document.querySelector('select[name="from_airport"]');
    const toSelect = document.querySelector('select[name="to_airport"]');
    
    const tempValue = fromSelect.value;
    fromSelect.value = toSelect.value;
    toSelect.value = tempValue;
}

function bookFlight(flightIndex) {
    const flights = <?php echo json_encode($available_flights); ?>;
    const flight = flights[flightIndex];
    const fromAirport = '<?php echo $from; ?>';
    const toAirport = '<?php echo $to; ?>';
    const travelers = <?php echo $travelers; ?>;
    const seatClass = '<?php echo $class; ?>';
    const departureDate = '<?php echo $departure; ?>';
    
    // Fill manual form with flight details
    document.querySelector('select[name="airline"]').value = flight.airline;
    document.querySelector('input[name="flight_number"]').value = flight.flight_no;
    document.querySelector('select[name="departure_airport"]').value = fromAirport;
    document.querySelector('select[name="arrival_airport"]').value = toAirport;
    document.querySelector('select[name="seat_class"]').value = seatClass;
    document.querySelector('input[name="passengers"]').value = travelers;
    document.querySelector('input[name="total_fare"]').value = (flight.price * travelers).toFixed(2);
    
    // Set departure date and time separately
    document.querySelector('input[name="departure_date"]').value = departureDate;
    document.querySelector('input[name="departure_time"]').value = flight.departure;
    
    // Calculate and set arrival date and time
    const arrivalTime = flight.arrival;
    let arrivalDate = departureDate;
    
    // If arrival time is earlier than departure time, it's next day
    if (arrivalTime < flight.departure) {
        const nextDay = new Date(departureDate);
        nextDay.setDate(nextDay.getDate() + 1);
        arrivalDate = nextDay.toISOString().split('T')[0];
    }
    
    document.querySelector('input[name="arrival_date"]').value = arrivalDate;
    document.querySelector('input[name="arrival_time"]').value = arrivalTime;
    document.querySelector('input[name="flight_duration"]').value = flight.duration;
    
    // Fill passenger name if available from session
    <?php if (isset($_SESSION['user_name'])): ?>
    document.querySelector('input[name="passenger_name"]').value = '<?php echo $_SESSION['user_name']; ?>';
    <?php endif; ?>
    
    // Scroll to manual form
    document.getElementById('manualBookingForm').scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });
    
    // Highlight the form
    const form = document.querySelector('.manual-booking-form');
    form.style.boxShadow = '0 0 0 3px rgba(24, 144, 255, 0.3)';
    form.style.transition = 'box-shadow 0.3s';
    
    setTimeout(() => {
        form.style.boxShadow = '';
    }, 2000);
    
    // Show success message without alert
    const successMsg = document.createElement('div');
    successMsg.className = 'alert alert-info alert-dismissible fade show';
    successMsg.innerHTML = `
        <i class="bi bi-info-circle me-2"></i>Flight details auto-filled! Please review and complete the booking.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.querySelector('.container-fluid').insertBefore(successMsg, document.querySelector('.airport-airlines'));
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        if (successMsg.parentNode) {
            successMsg.remove();
        }
    }, 5000);
}

// Tab switching
document.querySelectorAll('.search-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.search-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        
        // Show/hide return date based on tab
        const returnDateGroup = document.getElementById('returnDateGroup');
        const returnDateInput = document.querySelector('input[name="return_date"]');
        
        if (this.getAttribute('data-tab') === 'one-way') {
            returnDateGroup.style.display = 'none';
            returnDateInput.removeAttribute('required');
        } else {
            returnDateGroup.style.display = 'block';
            returnDateInput.setAttribute('required', 'required');
        }
    });
});

// Set default dates for manual booking
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    // Set default departure to tomorrow
    const departureDefault = new Date(tomorrow);
    document.querySelector('input[name="departure_date"]').value = departureDefault.toISOString().slice(0, 10);
    document.querySelector('input[name="departure_time"]').value = '14:00';
    
    // Set min dates for date inputs
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        input.min = today.toISOString().slice(0, 10);
    });
    
    // Initialize tab state
    document.querySelector('.search-tab.active').click();
});

// Form validation
document.getElementById('manualBookingForm').addEventListener('submit', function(e) {
    const totalFare = document.querySelector('input[name="total_fare"]').value;
    if (totalFare <= 0) {
        e.preventDefault();
        alert('Total fare must be greater than 0.');
        return false;
    }
    
    const departureDate = document.querySelector('input[name="departure_date"]').value;
    const departureTime = document.querySelector('input[name="departure_time"]').value;
    
    if (!departureDate || !departureTime) {
        e.preventDefault();
        alert('Please fill in both departure date and time.');
        return false;
    }
    
    return true;
});
</script>

<?php $conn->close(); ?>
<!-- Seat Selection Modal -->
<div id="seatModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div class="modal-content" style="background: white; margin: 50px auto; padding: 30px; border-radius: 10px; max-width: 800px; position: relative;">
        <span class="close" onclick="closeSeatModal()" style="position: absolute; right: 20px; top: 20px; font-size: 24px; cursor: pointer;">&times;</span>
        <h3>✈️ Select Your Seat</h3>
        <p class="text-muted">Choose your preferred seat based on your class</p>
        
        <div class="seat-class-info mb-3">
            <strong>Selected Class: <span id="selectedClass">Economy</span></strong>
        </div>
        
        <div class="seat-map" id="seatMap">
            <!-- Seat map will be generated by JavaScript -->
        </div>
        
        <div class="selected-seat-display mt-3" id="selectedSeatDisplay" style="background: #f0f9ff; padding: 15px; border-radius: 8px; text-align: center; border: 2px solid #1890ff;">
            <h5>No seat selected</h5>
            <p class="text-muted">Click on an available seat to select it</p>
        </div>
        
        <div class="seat-legend mt-3" style="display: flex; justify-content: center; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 5px;">
                <div style="width: 20px; height: 20px; background: #e8f5e8; border: 2px solid #4CAF50; border-radius: 3px;"></div>
                <span>Available</span>
            </div>
            <div style="display: flex; align-items: center; gap: 5px;">
                <div style="width: 20px; height: 20px; background: #4CAF50; border: 2px solid #2e7d32; border-radius: 3px;"></div>
                <span>Selected</span>
            </div>
            <div style="display: flex; align-items: center; gap: 5px;">
                <div style="width: 20px; height: 20px; background: #f5f5f5; border: 2px solid #ddd; border-radius: 3px;"></div>
                <span>Occupied</span>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <button class="btn btn-secondary me-3" onclick="closeSeatModal()">Cancel</button>
            <button class="btn btn-primary" onclick="confirmSeatSelection()">Confirm Selection</button>
        </div>
    </div>
</div>

<style>
.seat-map {
    margin: 20px 0;
    max-height: 400px;
    overflow-y: auto;
}

.seat-row {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 10px;
}

.row-label {
    width: 30px;
    text-align: center;
    font-weight: bold;
    margin-right: 10px;
}

.seat {
    width: 35px;
    height: 35px;
    margin: 2px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.8rem;
    font-weight: bold;
}

.seat.available {
    background: #e8f5e8;
    border: 2px solid #4CAF50;
    color: #2e7d32;
}

.seat.selected {
    background: #4CAF50;
    color: white;
    border: 2px solid #2e7d32;
}

.seat.occupied {
    background: #f5f5f5;
    border: 2px solid #ddd;
    color: #999;
    cursor: not-allowed;
}

.aisle {
    width: 40px;
}
</style>

<script>
let selectedSeat = '';
let currentSeatClass = 'Economy';

function showSeatMap() {
    currentSeatClass = document.querySelector('select[name="seat_class"]').value;
    document.getElementById('selectedClass').textContent = currentSeatClass;
    document.getElementById('seatModal').style.display = 'block';
    generateSeatMap();
}

function closeSeatModal() {
    document.getElementById('seatModal').style.display = 'none';
    selectedSeat = '';
    updateSelectedSeatDisplay();
}

function generateSeatMap() {
    const seatMap = document.getElementById('seatMap');
    seatMap.innerHTML = '';
    
    let startRow, endRow, letters;
    
    if (currentSeatClass === 'First Class') {
        startRow = 1;
        endRow = 4;
        letters = ['A', 'B', '', 'C', 'D'];
    } else if (currentSeatClass === 'Business') {
        startRow = 5;
        endRow = 10;
        letters = ['A', 'B', '', 'C', 'D'];
    } else {
        startRow = 11;
        endRow = 30;
        letters = ['A', 'B', 'C', '', 'D', 'E', 'F'];
    }
    
    for (let row = startRow; row <= endRow; row++) {
        const seatRow = document.createElement('div');
        seatRow.className = 'seat-row';
        
        const rowLabel = document.createElement('div');
        rowLabel.className = 'row-label';
        rowLabel.textContent = row;
        seatRow.appendChild(rowLabel);
        
        letters.forEach((letter) => {
            if (letter === '') {
                const aisle = document.createElement('div');
                aisle.className = 'aisle';
                seatRow.appendChild(aisle);
            } else {
                const seat = document.createElement('div');
                seat.className = 'seat available';
                seat.textContent = row + letter;
                seat.dataset.seat = row + letter;
                
                // Randomly occupy some seats
                if (Math.random() < 0.3) {
                    seat.className = 'seat occupied';
                } else {
                    seat.onclick = function() {
                        selectSeat(this);
                    };
                }
                
                seatRow.appendChild(seat);
            }
        });
        
        seatMap.appendChild(seatRow);
    }
}

function selectSeat(seatElement) {
    // Clear previous selection
    document.querySelectorAll('.seat.selected').forEach(seat => {
        seat.classList.remove('selected');
        seat.classList.add('available');
    });
    
    // Select new seat
    seatElement.classList.remove('available');
    seatElement.classList.add('selected');
    selectedSeat = seatElement.dataset.seat;
    
    updateSelectedSeatDisplay();
}

function updateSelectedSeatDisplay() {
    const display = document.getElementById('selectedSeatDisplay');
    
    if (selectedSeat) {
        display.innerHTML = `<h5>Selected Seat: ${selectedSeat}</h5><p class="text-muted">${currentSeatClass} Class</p>`;
    } else {
        display.innerHTML = `<h5>No seat selected</h5><p class="text-muted">Click on an available seat to select it</p>`;
    }
}

function confirmSeatSelection() {
    if (selectedSeat) {
        document.querySelector('input[name="seat_number"]').value = selectedSeat;
        closeSeatModal();
        alert(`Seat ${selectedSeat} selected successfully!`);
    } else {
        alert('Please select a seat first.');
    }
}
</script>