<?php
session_start();
header('Content-Type: application/json');

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get customer email from session
$customer_email = null;
if (isset($_SESSION['username'])) {
    $username = $conn->real_escape_string($_SESSION['username']);
    $result = $conn->query("SELECT email FROM customers WHERE username='$username'");
    if ($result && $result->num_rows > 0) {
        $customer_email = $result->fetch_assoc()['email'];
    }
}

if (!$customer_email) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $car_model = $conn->real_escape_string($_POST['car_model']);
    $car_type = $conn->real_escape_string($_POST['car_type']);
    $car_image = $conn->real_escape_string($_POST['car_image']);
    $daily_rate = (float)$_POST['daily_rate'];
    
    $sql = "INSERT INTO favorite_cars (customer_email, car_model, car_type, car_image, daily_rate) 
            VALUES ('$customer_email', '$car_model', '$car_type', '$car_image', $daily_rate)
            ON DUPLICATE KEY UPDATE car_type='$car_type', car_image='$car_image', daily_rate=$daily_rate";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Added to favorites']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add favorite']);
    }
} elseif ($action === 'remove') {
    $car_model = $conn->real_escape_string($_POST['car_model']);
    
    $sql = "DELETE FROM favorite_cars WHERE customer_email='$customer_email' AND car_model='$car_model'";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Removed from favorites']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove favorite']);
    }
} elseif ($action === 'check') {
    $car_model = $conn->real_escape_string($_POST['car_model']);
    
    $result = $conn->query("SELECT id FROM favorite_cars WHERE customer_email='$customer_email' AND car_model='$car_model'");
    
    echo json_encode(['success' => true, 'is_favorite' => $result->num_rows > 0]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

$conn->close();
?>
