<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$car_model = isset($_GET['car_model']) ? $conn->real_escape_string($_GET['car_model']) : '';
$pickup_date = isset($_GET['pickup_date']) ? $conn->real_escape_string($_GET['pickup_date']) : '';
$dropoff_date = isset($_GET['dropoff_date']) ? $conn->real_escape_string($_GET['dropoff_date']) : '';

if (empty($car_model) || empty($pickup_date) || empty($dropoff_date)) {
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

// Check for existing bookings
$query = "SELECT cr.*, cr.customer_name, cr.booking_id, cr.status
          FROM car_rentals cr
          WHERE cr.car_model = '$car_model'
          AND cr.status IN ('Pending', 'Confirmed', 'Active')
          AND ((cr.pickup_date <= '$dropoff_date' AND cr.dropoff_date >= '$pickup_date'))
          ORDER BY cr.pickup_date ASC";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = [
            'booking_id' => $row['booking_id'],
            'customer_name' => substr($row['customer_name'], 0, 1) . '***', // Privacy
            'pickup_date' => $row['pickup_date'],
            'dropoff_date' => $row['dropoff_date'],
            'status' => $row['status']
        ];
    }
    
    echo json_encode([
        'available' => false,
        'message' => 'Car is already booked for these dates',
        'bookings' => $bookings
    ]);
} else {
    echo json_encode([
        'available' => true,
        'message' => 'Car is available for these dates'
    ]);
}

$conn->close();
?>
