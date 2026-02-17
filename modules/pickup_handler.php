<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $booking_id = $conn->real_escape_string($_POST['booking_id']);
    
    if ($action == 'confirm_pickup') {
        // Customer confirms they picked up the car
        $conn->query("UPDATE car_rentals SET pickup_status='Picked Up', pickup_confirmed_at=NOW(), status='Active' WHERE booking_id='$booking_id'");
        
        // Notify staff
        $booking = $conn->query("SELECT * FROM car_rentals WHERE booking_id='$booking_id'")->fetch_assoc();
        $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id, is_read) 
            VALUES ('staff', 'admin', 'Car Picked Up', 'Customer has picked up car for booking $booking_id at {$booking['pickup_location']}', '$booking_id', 0)");
        
        echo json_encode(['success' => true, 'message' => 'Pickup confirmed! Rental is now active.']);
    }
    
    if ($action == 'confirm_return_pickup') {
        // Staff confirms they picked up the returned car
        $conn->query("UPDATE car_rentals SET return_pickup_status='Picked Up', return_pickup_confirmed_at=NOW() WHERE booking_id='$booking_id'");
        
        echo json_encode(['success' => true, 'message' => 'Return pickup confirmed!']);
    }
    
    if ($action == 'notify_return_pickup') {
        // Customer returns car, notify staff to pick up
        $booking = $conn->query("SELECT * FROM car_rentals WHERE booking_id='$booking_id'")->fetch_assoc();
        $return_location = $booking['dropoff_location'];
        
        $conn->query("UPDATE car_rentals SET return_pickup_status='Ready' WHERE booking_id='$booking_id'");
        $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id, is_read) 
            VALUES ('staff', 'admin', 'Car Ready for Return Pickup', 'Customer returned car for booking $booking_id. Pick up at: $return_location', '$booking_id', 0)");
        
        echo json_encode(['success' => true, 'message' => 'Staff notified to pick up car at ' . $return_location]);
    }
}

$conn->close();
?>
