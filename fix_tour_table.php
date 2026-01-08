<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if tour_type column exists
$result = $conn->query("SHOW COLUMNS FROM tour_activities LIKE 'tour_type'");
if ($result->num_rows == 0) {
    // Add the tour_type column
    $sql = "ALTER TABLE tour_activities ADD COLUMN tour_type ENUM('Sightseeing','Adventure','Cultural','Food','Nature','Historical','Private','Group') NOT NULL DEFAULT 'Private' AFTER city";
    if ($conn->query($sql)) {
        echo "✅ tour_type column added successfully!\n";
    } else {
        echo "❌ Error adding tour_type column: " . $conn->error . "\n";
    }
} else {
    echo "✅ tour_type column already exists.\n";
}

// Check the table structure
$result = $conn->query("DESCRIBE tour_activities");
if ($result) {
    echo "\nCurrent table structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
}

$conn->close();
?>
