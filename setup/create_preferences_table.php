<?php
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');

// Create customer_preferences table
$conn->query("CREATE TABLE IF NOT EXISTS customer_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_email VARCHAR(100) UNIQUE NOT NULL,
    default_pickup_location VARCHAR(200),
    default_insurance VARCHAR(50) DEFAULT 'none',
    favorite_cars TEXT,
    profile_completion INT DEFAULT 0,
    is_verified TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

echo "✅ Customer preferences table created successfully!";
$conn->close();
?>
