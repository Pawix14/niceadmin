<?php
// Database cleanup script - removes unused tables
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>CarGo Database Cleanup</h2>";
echo "<p>Checking for unused tables...</p>";

// Get all current tables
$result = $conn->query("SHOW TABLES");
$all_tables = [];
while($row = $result->fetch_array()) {
    $all_tables[] = $row[0];
}

echo "<h3>Current Tables (" . count($all_tables) . "):</h3>";
echo "<ul>";
foreach($all_tables as $table) {
    echo "<li>$table</li>";
}
echo "</ul>";

// Define tables that ARE being used in the system
$used_tables = [
    'admins',
    'cars',
    'car_rentals',
    'car_sales',
    'car_maintenance',
    'car_reviews',
    'car_blocked_dates',
    'customers',
    'customer_documents',
    'notifications',
    'promo_codes',
    'travel_agents',
    'seasonal_pricing',
    'travel_bookings',
    'car_rental_bookings'
];

// Find unused tables
$unused_tables = array_diff($all_tables, $used_tables);

if(empty($unused_tables)) {
    echo "<p style='color: green;'><strong>✅ No unused tables found. Database is clean!</strong></p>";
} else {
    echo "<h3 style='color: red;'>Unused Tables Found (" . count($unused_tables) . "):</h3>";
    echo "<ul>";
    foreach($unused_tables as $table) {
        echo "<li style='color: red;'>$table</li>";
    }
    echo "</ul>";
    
    echo "<h3>Removing unused tables...</h3>";
    
    foreach($unused_tables as $table) {
        $sql = "DROP TABLE IF EXISTS `$table`";
        if($conn->query($sql)) {
            echo "<p style='color: green;'>✅ Dropped table: <strong>$table</strong></p>";
        } else {
            echo "<p style='color: red;'>❌ Error dropping table $table: " . $conn->error . "</p>";
        }
    }
    
    echo "<h3 style='color: green;'>✅ Database cleanup completed!</h3>";
}

// Show final table count
$result = $conn->query("SHOW TABLES");
$final_count = $result->num_rows;
echo "<p><strong>Final table count: $final_count</strong></p>";

echo "<h3>Remaining Tables:</h3>";
echo "<ul>";
$result->data_seek(0);
while($row = $result->fetch_array()) {
    echo "<li style='color: green;'>$row[0]</li>";
}
echo "</ul>";

$conn->close();

echo "<hr>";
echo "<p><a href='index.php'>← Back to Dashboard</a></p>";
?>
