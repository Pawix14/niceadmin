<?php
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');

$booking_id = $conn->real_escape_string($_POST['booking_id']);
$customer_email = $conn->real_escape_string($_POST['customer_email']);
$balance = floatval($_POST['balance']);

$message = "Reminder: You have a remaining balance of ₱" . number_format($balance, 2) . " for booking $booking_id. Please settle the balance before pickup.";

$conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id, is_read) 
    VALUES ('customer', '$customer_email', 'Payment Reminder', '$message', '$booking_id', 0)");

echo json_encode(['success' => true]);
$conn->close();
?>
