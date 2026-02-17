<?php
// Create favorite_cars table
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE TABLE IF NOT EXISTS `favorite_cars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_email` varchar(255) NOT NULL,
  `car_model` varchar(255) NOT NULL,
  `car_type` varchar(100) DEFAULT NULL,
  `car_image` varchar(500) DEFAULT NULL,
  `daily_rate` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_favorite` (`customer_email`, `car_model`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql) === TRUE) {
    echo "✅ Table 'favorite_cars' created successfully!<br>";
    echo "<a href='index.php'>Go to Dashboard</a>";
} else {
    echo "❌ Error creating table: " . $conn->error;
}

$conn->close();
?>
