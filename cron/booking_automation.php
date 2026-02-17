<?php
// Booking Automation - Run this every hour via cron job or task scheduler
// Windows: schtasks /create /tn "BookingAutomation" /tr "php d:\xampp\htdocs\niceadmin\cron\booking_automation.php" /sc hourly

$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$log = [];
$now = date('Y-m-d H:i:s');
$today = date('Y-m-d');

// 1. AUTO-STATUS UPDATES
// Confirmed → Active (on pickup date)
$result = $conn->query("UPDATE car_rentals SET status='Active' 
    WHERE status='Confirmed' AND pickup_date <= '$today' AND dropoff_date >= '$today'");
if($result) $log[] = "✅ Updated " . $conn->affected_rows . " bookings to Active";

// Active → Completed (after dropoff date)
$result = $conn->query("UPDATE car_rentals SET status='Completed' 
    WHERE status='Active' AND dropoff_date < '$today'");
if($result) $log[] = "✅ Updated " . $conn->affected_rows . " bookings to Completed";

// 2. PICKUP REMINDERS (24 hours before)
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$reminders = $conn->query("SELECT booking_id, customer_email, customer_name, car_model, pickup_date, pickup_time, pickup_location 
    FROM car_rentals 
    WHERE status='Confirmed' AND pickup_date='$tomorrow' 
    AND NOT EXISTS (SELECT 1 FROM notifications WHERE booking_id=car_rentals.booking_id AND title='Pickup Reminder')");

$reminder_count = 0;
while($booking = $reminders->fetch_assoc()) {
    $message = "Reminder: Your rental for {$booking['car_model']} is tomorrow at {$booking['pickup_time']}. Pickup location: {$booking['pickup_location']}. Please bring your driver's license and booking confirmation.";
    
    $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id, is_read) 
        VALUES ('customer', '{$booking['customer_email']}', 'Pickup Reminder', '$message', '{$booking['booking_id']}', 0)");
    $reminder_count++;
}
$log[] = "📧 Sent $reminder_count pickup reminders";

// 3. LATE RETURN ALERTS
$late_returns = $conn->query("SELECT booking_id, customer_email, customer_name, car_model, dropoff_date 
    FROM car_rentals 
    WHERE status='Active' AND dropoff_date < '$today'");

$late_count = 0;
while($booking = $late_returns->fetch_assoc()) {
    $days_late = (strtotime($today) - strtotime($booking['dropoff_date'])) / 86400;
    $message = "ALERT: {$booking['car_model']} rental (Booking: {$booking['booking_id']}) is {$days_late} day(s) overdue. Customer: {$booking['customer_name']}";
    
    $conn->query("INSERT INTO notifications (user_type, title, message, booking_id, is_read) 
        VALUES ('staff', 'Late Return Alert', '$message', '{$booking['booking_id']}', 0)");
    $late_count++;
}
$log[] = "⚠️ Sent $late_count late return alerts";

// 4. DOCUMENT EXPIRY ALERTS (license expiring in 30 days)
$expiry_date = date('Y-m-d', strtotime('+30 days'));
$expiring = $conn->query("SELECT booking_id, customer_email, customer_name, license_expiry 
    FROM car_rentals 
    WHERE status IN ('Confirmed', 'Active') AND license_expiry <= '$expiry_date' AND license_expiry >= '$today'");

$expiry_count = 0;
while($booking = $expiring->fetch_assoc()) {
    $message = "Your driver's license expires on {$booking['license_expiry']}. Please update your license information before your rental period.";
    
    $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id, is_read) 
        VALUES ('customer', '{$booking['customer_email']}', 'License Expiry Warning', '$message', '{$booking['booking_id']}', 0)");
    $expiry_count++;
}
$log[] = "📅 Sent $expiry_count license expiry warnings";

// Log results
$log_text = implode("\n", $log);
file_put_contents(__DIR__ . '/automation_log.txt', date('Y-m-d H:i:s') . "\n" . $log_text . "\n\n", FILE_APPEND);

echo $log_text;
$conn->close();
?>
