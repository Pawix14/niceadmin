<?php
// cron/check_rentals.php - Run this every hour via cron/task scheduler
require_once '../config/database.php';

$conn = getConnection();

// Add reminder_sent column if not exists
$conn->query("ALTER TABLE car_rental_bookings ADD COLUMN IF NOT EXISTS reminder_sent TINYINT(1) DEFAULT 0");

// Update car availability based on active rentals
$conn->query("UPDATE cars c 
    LEFT JOIN car_rental_bookings b ON c.name = b.car_model 
    AND b.status IN ('Confirmed', 'Pending') 
    AND CURDATE() BETWEEN b.pickup_date AND b.return_date
    SET c.status = IF(b.id IS NULL, 'Active', 'Rented')");

// Check for rentals ending in 1 day and send reminders
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$result = $conn->query("SELECT * FROM car_rental_bookings 
    WHERE return_date = '$tomorrow' 
    AND status = 'Confirmed' 
    AND reminder_sent = 0");

while($booking = $result->fetch_assoc()) {
    // Insert notification
    $conn->query("INSERT INTO notifications (user_email, message, type, created_at) 
        VALUES ('{$booking['customer_email']}', 
        'Reminder: Your rental for {$booking['car_model']} ends tomorrow ({$booking['return_date']}). Please return the car on time.', 
        'rental_reminder', NOW())");
    
    // Mark reminder as sent
    $conn->query("UPDATE car_rental_bookings SET reminder_sent = 1 WHERE id = {$booking['id']}");
}

$conn->close();
?>
