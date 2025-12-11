<?php
// modules/get_booking_details.php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "travel_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$id = $_GET['id'];
$sql = "SELECT * FROM travel_bookings WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if ($booking): ?>
    <div class="booking-details">
        <p><strong>Booking ID:</strong> <?php echo htmlspecialchars($booking['booking_id']); ?></p>
        <p><strong>Traveler Name:</strong> <?php echo htmlspecialchars($booking['traveler_name']); ?></p>
        <p><strong>Travel Type:</strong> <?php echo $booking['travel_type']; ?></p>
        <p><strong>From:</strong> <?php echo htmlspecialchars($booking['from_city']); ?>, <?php echo htmlspecialchars($booking['from_country']); ?></p>
        <p><strong>To:</strong> <?php echo htmlspecialchars($booking['to_city']); ?>, <?php echo htmlspecialchars($booking['to_country']); ?></p>
        <p><strong>Status:</strong> <?php echo $booking['status']; ?></p>
        <p><strong>Booking Date:</strong> <?php echo date('Y-m-d H:i', strtotime($booking['booking_date'])); ?></p>
    </div>
<?php else: ?>
    <p>Booking not found.</p>
<?php endif;

$stmt->close();
$conn->close();
?>