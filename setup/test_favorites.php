<?php
session_start();
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

echo "<h2>Favorite System Debug</h2>";

// Check table exists
$result = $conn->query("SHOW TABLES LIKE 'favorite_cars'");
if ($result->num_rows > 0) {
    echo "✅ favorite_cars table exists<br>";
    
    // Show table structure
    $structure = $conn->query("DESCRIBE favorite_cars");
    echo "<h3>Table Structure:</h3><pre>";
    while($row = $structure->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
} else {
    echo "❌ favorite_cars table does NOT exist<br>";
    echo "<br><strong>Creating table now...</strong><br>";
    
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
        echo "✅ Table created successfully!<br>";
    } else {
        echo "❌ Error: " . $conn->error . "<br>";
    }
}

// Check session
echo "<h3>Session Info:</h3>";
echo "Session ID: " . session_id() . "<br>";
echo "Username in session: " . (isset($_SESSION['username']) ? $_SESSION['username'] : 'NOT SET') . "<br>";

if (isset($_SESSION['username'])) {
    $username = $conn->real_escape_string($_SESSION['username']);
    $result = $conn->query("SELECT email FROM customers WHERE username='$username'");
    if ($result && $result->num_rows > 0) {
        $email = $result->fetch_assoc()['email'];
        echo "Customer email: " . $email . "<br>";
    } else {
        echo "❌ Customer not found in database<br>";
    }
}

// Show all favorites
echo "<h3>All Favorites:</h3>";
$favorites = $conn->query("SELECT * FROM favorite_cars");
if ($favorites && $favorites->num_rows > 0) {
    echo "<table border='1'><tr><th>Email</th><th>Car Model</th><th>Created</th></tr>";
    while($fav = $favorites->fetch_assoc()) {
        echo "<tr><td>{$fav['customer_email']}</td><td>{$fav['car_model']}</td><td>{$fav['created_at']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "No favorites yet<br>";
}

$conn->close();
?>
<br><br>
<a href="../index.php?page=car_rental">Go to Car Rental</a>
