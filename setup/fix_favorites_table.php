<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Fixing favorite_cars table...</h2>";

// Drop the old table
$conn->query("DROP TABLE IF EXISTS favorite_cars");
echo "✅ Dropped old table<br>";

// Create new table with correct structure
$sql = "CREATE TABLE favorite_cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_email VARCHAR(100) NOT NULL,
    car_model VARCHAR(100) NOT NULL,
    car_type VARCHAR(50) NOT NULL,
    car_image VARCHAR(255),
    daily_rate DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_favorite (customer_email, car_model)
)";

if ($conn->query($sql)) {
    echo "✅ Created new table with correct structure!<br>";
} else {
    echo "❌ Error: " . $conn->error . "<br>";
}

// Verify structure
echo "<h3>New Table Structure:</h3><pre>";
$result = $conn->query("DESCRIBE favorite_cars");
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
echo "</pre>";

$conn->close();

echo "<br><a href='../index.php?page=car_rental'>Go to Car Rental</a>";
?>
