<?php
header('Content-Type: application/json');
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db';
$conn = new mysqli($host, $user, $pass, $dbname);
if (isset($_GET['country'])) {
    $country = $conn->real_escape_string($_GET['country']);
    
    $country_query = "SELECT country_code FROM countries WHERE country_name = '$country'";
    $result = $conn->query($country_query);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $country_code = $row['country_code'];
        $city_query = "SELECT city_name FROM cities WHERE country_code = '$country_code' ORDER BY city_name";
        $city_result = $conn->query($city_query);
        
        $cities = [];
        while ($city = $city_result->fetch_assoc()) {
            $cities[] = $city['city_name'];
        }
        
        echo json_encode($cities);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
?>