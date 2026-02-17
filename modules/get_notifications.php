<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');

$user_type = $_SESSION['user_type'];
$user_id = '';

if($user_type == 'customer') {
    $username = $_SESSION['username'];
    $customer = $conn->query("SELECT email FROM customers WHERE username='$username'")->fetch_assoc();
    $user_id = $customer['email'];
}

$where = $user_type == 'customer' ? "user_type='customer' AND user_id='$user_id'" : "user_type='$user_type'";

$unread = $conn->query("SELECT COUNT(*) as count FROM notifications WHERE $where AND is_read=0")->fetch_assoc()['count'];

$response = ['unread_count' => $unread];

if(isset($_GET['full'])) {
    $notifications = $conn->query("SELECT *, 
        CASE 
            WHEN TIMESTAMPDIFF(MINUTE, created_at, NOW()) < 60 THEN CONCAT(TIMESTAMPDIFF(MINUTE, created_at, NOW()), 'm ago')
            WHEN TIMESTAMPDIFF(HOUR, created_at, NOW()) < 24 THEN CONCAT(TIMESTAMPDIFF(HOUR, created_at, NOW()), 'h ago')
            ELSE CONCAT(TIMESTAMPDIFF(DAY, created_at, NOW()), 'd ago')
        END as time_ago
        FROM notifications WHERE $where ORDER BY created_at DESC LIMIT 20")->fetch_all(MYSQLI_ASSOC);
    $response['notifications'] = $notifications;
}

header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>
