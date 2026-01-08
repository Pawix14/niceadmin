<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$cities_result = $conn->query("SELECT c.country_code, ci.city_name FROM cities ci INNER JOIN countries c ON ci.country_code = c.country_code ORDER BY c.country_name, ci.city_name");

$cities_by_country = [];
while ($row = $cities_result->fetch_assoc()) {
    $cities_by_country[$row['country_code']][] = $row['city_name'];
}

echo "<h1>Cities by Country Debug</h1>";
echo "<pre>";
print_r($cities_by_country);
echo "</pre>";

$conn->close();
?>
