<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create favorite_cars table
$sql = "CREATE TABLE IF NOT EXISTS favorite_cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_email VARCHAR(100) NOT NULL,
    car_model VARCHAR(100) NOT NULL,
    car_type VARCHAR(50) NOT NULL,
    car_image VARCHAR(255),
    daily_rate DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_favorite (customer_email, car_model)
)";

if ($conn->query($sql) === TRUE) {
    echo "✅ favorite_cars table created successfully!<br>";
} else {
    echo "❌ Error creating table: " . $conn->error . "<br>";
}

$conn->close();

echo "<br><a href='../index.php'>Go to Home</a>";
?>
