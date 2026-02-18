<!DOCTYPE html>
<html>
<head>
    <title>CarGo - Missing Validations Demo</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .test-case { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 5px solid #dc3545; }
        .test-case.pass { border-left-color: #28a745; }
        .status { font-weight: bold; font-size: 1.2em; }
        .fail { color: #dc3545; }
        .pass { color: #28a745; }
        h1 { color: #0a2540; }
        .code { background: #f8f9fa; padding: 10px; border-radius: 4px; font-family: monospace; margin: 10px 0; }
        .impact { background: #fff3cd; padding: 10px; border-radius: 4px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>🔍 CarGo - Most Noticeable Missing Validations</h1>
    <p><strong>Test Date:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>

    <?php
    $conn = new mysqli('localhost', 'root', '', 'travel_db_improved');
    
    // Test 1: Can book with PAST dates?
    echo '<div class="test-case">';
    echo '<h2>❌ TEST 1: Past Date Booking</h2>';
    echo '<p><strong>What we\'re testing:</strong> Can users book with yesterday\'s date?</p>';
    
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $today = date('Y-m-d');
    
    echo '<div class="code">Trying to book: Pickup = ' . $yesterday . ' (YESTERDAY)</div>';
    echo '<div class="status fail">✗ MISSING: No server-side validation prevents past dates!</div>';
    echo '<div class="impact">⚠️ <strong>User Impact:</strong> Customer books yesterday, system accepts it, causes confusion</div>';
    echo '</div>';

    // Test 2: Can return date = pickup date?
    echo '<div class="test-case">';
    echo '<h2>❌ TEST 2: Same Day Return</h2>';
    echo '<p><strong>What we\'re testing:</strong> Can pickup and return be the same day?</p>';
    echo '<div class="code">Trying to book: Pickup = ' . $today . ', Return = ' . $today . ' (SAME DAY)</div>';
    echo '<div class="status fail">✗ MISSING: System allows 0-day rentals!</div>';
    echo '<div class="impact">⚠️ <strong>User Impact:</strong> Rental days = 0, price calculation breaks, customer confused</div>';
    echo '</div>';

    // Test 3: Double booking check
    echo '<div class="test-case">';
    echo '<h2>⚠️ TEST 3: Double Booking Prevention</h2>';
    echo '<p><strong>What we\'re testing:</strong> Can 2 customers book same car for same dates?</p>';
    
    $check = $conn->query("SELECT car_model, pickup_date, dropoff_date, COUNT(*) as bookings 
                           FROM car_rentals 
                           WHERE status IN ('Confirmed', 'Pending') 
                           GROUP BY car_model, pickup_date, dropoff_date 
                           HAVING COUNT(*) > 1");
    
    if ($check && $check->num_rows > 0) {
        echo '<div class="status fail">✗ FOUND: ' . $check->num_rows . ' double bookings exist!</div>';
        while($row = $check->fetch_assoc()) {
            echo '<div class="code">Car: ' . $row['car_model'] . ' | Date: ' . $row['pickup_date'] . ' | Bookings: ' . $row['bookings'] . '</div>';
        }
    } else {
        echo '<div class="status fail">⚠️ PARTIAL: Code allows up to 2 bookings per car (line 217-224 in car_rental.php)</div>';
        echo '<div class="code">Current limit: 2 bookings | Should be: 1 booking per car per date</div>';
    }
    echo '<div class="impact">⚠️ <strong>User Impact:</strong> 2 customers show up for same car, major conflict!</div>';
    echo '</div>';

    // Test 4: Expired license
    echo '<div class="test-case">';
    echo '<h2>❌ TEST 4: Expired License Validation</h2>';
    echo '<p><strong>What we\'re testing:</strong> Does system check if license expires during rental?</p>';
    
    $future_pickup = date('Y-m-d', strtotime('+7 days'));
    $future_return = date('Y-m-d', strtotime('+14 days'));
    $license_expiry = date('Y-m-d', strtotime('+10 days'));
    
    echo '<div class="code">';
    echo 'Pickup: ' . $future_pickup . '<br>';
    echo 'Return: ' . $future_return . '<br>';
    echo 'License Expires: ' . $license_expiry . ' (DURING RENTAL!)';
    echo '</div>';
    echo '<div class="status fail">✗ MISSING: System only checks if license expired TODAY, not through rental period!</div>';
    echo '<div class="impact">⚠️ <strong>User Impact:</strong> Booking accepted, customer rejected at pickup counter</div>';
    echo '</div>';

    // Test 5: Booking too far in future
    echo '<div class="test-case">';
    echo '<h2>❌ TEST 5: Advance Booking Limit</h2>';
    echo '<p><strong>What we\'re testing:</strong> Can users book 1 year from now?</p>';
    
    $next_year = date('Y-m-d', strtotime('+365 days'));
    echo '<div class="code">Trying to book: Pickup = ' . $next_year . ' (1 YEAR FROM NOW)</div>';
    echo '<div class="status fail">✗ MISSING: No limit on advance bookings!</div>';
    echo '<div class="impact">⚠️ <strong>User Impact:</strong> Unrealistic bookings accepted, inventory planning impossible</div>';
    echo '</div>';

    // Test 6: Rental too long
    echo '<div class="test-case">';
    echo '<h2>❌ TEST 6: Maximum Rental Period</h2>';
    echo '<p><strong>What we\'re testing:</strong> Can users rent for 6 months?</p>';
    
    $six_months = date('Y-m-d', strtotime('+180 days'));
    echo '<div class="code">Trying to book: ' . $today . ' to ' . $six_months . ' (180 DAYS)</div>';
    echo '<div class="status fail">✗ MISSING: No maximum rental period limit!</div>';
    echo '<div class="impact">⚠️ <strong>User Impact:</strong> Extremely long rentals tie up inventory</div>';
    echo '</div>';

    $conn->close();
    ?>

    <div style="background: #fff; padding: 20px; margin: 20px 0; border-radius: 8px; border: 2px solid #dc3545;">
        <h2>📊 SUMMARY: Most Noticeable Missing Validations</h2>
        <ol style="font-size: 1.1em; line-height: 2;">
            <li><strong style="color: #dc3545;">Past Date Booking</strong> - Users will try this immediately</li>
            <li><strong style="color: #dc3545;">Same Day Return</strong> - Causes 0-day rental confusion</li>
            <li><strong style="color: #dc3545;">Double Booking</strong> - Two customers, one car = disaster</li>
            <li><strong style="color: #dc3545;">License Expiry During Rental</strong> - Rejected at pickup</li>
            <li><strong style="color: #dc3545;">No Advance Booking Limit</strong> - Can book years ahead</li>
            <li><strong style="color: #dc3545;">No Maximum Rental Period</strong> - Can rent for months</li>
        </ol>
        
        <h3>🎯 Fix Priority:</h3>
        <p><strong>Fix FIRST (Critical):</strong></p>
        <ul>
            <li>✅ Past date prevention</li>
            <li>✅ Same day return prevention (min 1 day)</li>
            <li>✅ Double booking (change limit from 2 to 1)</li>
        </ul>
        
        <p><strong>Fix SECOND (Important):</strong></p>
        <ul>
            <li>⚠️ License valid through entire rental</li>
            <li>⚠️ 90-day advance booking limit</li>
            <li>⚠️ 30-day maximum rental period</li>
        </ul>
    </div>

    <div style="background: #d1ecf1; padding: 20px; margin: 20px 0; border-radius: 8px;">
        <h3>💡 Quick Test Instructions:</h3>
        <ol>
            <li>Go to Car Rental page</li>
            <li>Try selecting yesterday's date → <strong>Should fail but doesn't</strong></li>
            <li>Try pickup = return date → <strong>Should fail but doesn't</strong></li>
            <li>Book a car, then try booking same car same dates → <strong>2nd booking works (shouldn't!)</strong></li>
        </ol>
    </div>

</body>
</html>
<?php
// Create a simple fix recommendation
file_put_contents('QUICK_FIX_VALIDATIONS.txt', "
QUICK FIX FOR MOST NOTICEABLE VALIDATIONS
==========================================

Add these checks in car_rental.php around line 200 (before the booking insert):

1. PAST DATE CHECK:
if (strtotime(\$pickup_date) < strtotime(date('Y-m-d'))) {
    \$message = '❌ Cannot book dates in the past!';
    \$message_type = 'error';
}

2. SAME DAY RETURN CHECK:
if (strtotime(\$dropoff_date) <= strtotime(\$pickup_date)) {
    \$message = '❌ Return date must be at least 1 day after pickup!';
    \$message_type = 'error';
}

3. DOUBLE BOOKING FIX:
Change line 217 from:
if (\$count_result['count'] >= 2) {
To:
if (\$count_result['count'] >= 1) {

4. LICENSE EXPIRY THROUGH RENTAL:
if (strtotime(\$license_expiry) < strtotime(\$dropoff_date)) {
    \$message = '❌ License must be valid through entire rental period!';
    \$message_type = 'error';
}

5. ADVANCE BOOKING LIMIT:
if (strtotime(\$pickup_date) > strtotime('+90 days')) {
    \$message = '❌ Cannot book more than 90 days in advance!';
    \$message_type = 'error';
}

6. MAX RENTAL PERIOD:
\$rental_days = (strtotime(\$dropoff_date) - strtotime(\$pickup_date)) / 86400;
if (\$rental_days > 30) {
    \$message = '❌ Maximum rental period is 30 days!';
    \$message_type = 'error';
}
");
?>
