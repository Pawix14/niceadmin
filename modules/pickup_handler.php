<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $booking_id = $conn->real_escape_string($_POST['booking_id']);
    
    if ($action == 'confirm_pickup') {
        // Check if booking is confirmed first
        $booking = $conn->query("SELECT * FROM car_rentals WHERE booking_id='$booking_id'")->fetch_assoc();
        
        if ($booking['status'] !== 'Confirmed') {
            echo json_encode(['success' => false, 'message' => 'Booking must be confirmed by staff before pickup!']);
            exit;
        }
        
        // Customer confirms they picked up the car
        $conn->query("UPDATE car_rentals SET pickup_status='Picked Up', pickup_confirmed_at=NOW(), status='Active' WHERE booking_id='$booking_id'");
        
        // Notify staff
        $conn->query("INSERT INTO notifications (user_type, title, message, booking_id, category, priority, is_read) 
            VALUES ('staff', 'Car Picked Up', 'Customer has picked up car for booking $booking_id at {$booking['pickup_location']}', '$booking_id', 'booking', 'normal', 0)");
        
        echo json_encode(['success' => true, 'message' => 'Pickup confirmed! Rental is now active.']);
    }
    
    if ($action == 'confirm_return_pickup') {
        // Staff confirms they picked up the returned car
        $conn->query("UPDATE car_rentals SET return_pickup_status='Picked Up', return_pickup_confirmed_at=NOW(), status='Completed' WHERE booking_id='$booking_id'");
        
        echo json_encode(['success' => true, 'message' => 'Return pickup confirmed! Booking completed.']);
    }
    
    if ($action == 'notify_return_pickup') {
        // CRITICAL: Check if fully paid before allowing return
        $booking = $conn->query("SELECT * FROM car_rentals WHERE booking_id='$booking_id'")->fetch_assoc();
        
        if ($booking['remaining_balance'] > 0) {
            echo json_encode([
                'success' => false, 
                'message' => 'Payment Required! You must pay the remaining balance of ₱' . number_format($booking['remaining_balance'], 2) . ' before returning the car.',
                'balance' => $booking['remaining_balance']
            ]);
            exit;
        }
        
        $return_location = $booking['dropoff_location'];
        
        $conn->query("UPDATE car_rentals SET return_pickup_status='Ready' WHERE booking_id='$booking_id'");
        $conn->query("INSERT INTO notifications (user_type, title, message, booking_id, category, priority, is_read) 
            VALUES ('staff', 'Car Ready for Return Pickup', 'Customer returned car for booking $booking_id. Pick up at: $return_location', '$booking_id', 'booking', 'important', 0)");
        
        echo json_encode(['success' => true, 'message' => 'Staff notified to pick up car at ' . $return_location]);
    }
}

$conn->close();
?>
