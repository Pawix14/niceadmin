<?php
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');

// Add payment type and balance columns
$conn->query("ALTER TABLE car_rentals ADD COLUMN IF NOT EXISTS payment_type VARCHAR(20) DEFAULT 'Full Payment'");
$conn->query("ALTER TABLE car_rentals ADD COLUMN IF NOT EXISTS amount_paid DECIMAL(10,2) DEFAULT 0");
$conn->query("ALTER TABLE car_rentals ADD COLUMN IF NOT EXISTS remaining_balance DECIMAL(10,2) DEFAULT 0");

echo "✅ Payment columns added successfully!";
$conn->close();
?>
