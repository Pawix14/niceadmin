<?php
// File cleanup script - removes unused module files
echo "<h2>CarGo File Cleanup</h2>";
echo "<p>Checking for unused module files...</p>";

$modules_dir = 'modules/';

// Get all PHP files in modules directory
$all_files = glob($modules_dir . '*.php');

echo "<h3>All Module Files (" . count($all_files) . "):</h3>";
echo "<ul>";
foreach($all_files as $file) {
    echo "<li>" . basename($file) . "</li>";
}
echo "</ul>";

// Define files that ARE being used (referenced in index.php navigation)
$used_files = [
    // Admin navigation
    'admin_dashboard.php',
    'dashboard.php',
    'cars.php',
    'car_sales.php',
    'promo_codes.php',
    'agents.php',
    'commissions.php',
    'admin_management.php',
    'booking_calendar.php',
    'car_availability.php',
    'car_reviews.php',
    'documents.php',
    'notifications.php',
    
    // Staff navigation
    'staff_dashboard.php',
    'staff_booking_review.php',
    'all_bookings.php',
    'admin_car_rental.php',
    'car_maintenance.php',
    'customers.php',
    'payments.php',
    
    // Customer navigation
    'customer_dashboard.php',
    'my_bookings.php',
    'my_payments.php',
    'car_rental.php',
    'favorite_cars.php',
    'my_profile.php',
    
    // Helper/Handler files
    'favorite_car_handler.php',
    'get_notifications.php',
    'mark_notification_read.php',
    'invoice.php',
    
    // Backend handlers & secondary pages
    'payment_success.php',
    'pickup_handler.php',
    'pickup_management.php',
    'send_balance_reminder.php',
    'staff_extend_rental.php',
    'extend_rental.php'
];

// Find unused files
$unused_files = [];
foreach($all_files as $file) {
    $basename = basename($file);
    if(!in_array($basename, $used_files)) {
        $unused_files[] = $file;
    }
}

if(empty($unused_files)) {
    echo "<p style='color: green;'><strong>✅ No unused files found. All files are in use!</strong></p>";
} else {
    echo "<h3 style='color: red;'>Unused Files Found (" . count($unused_files) . "):</h3>";
    echo "<ul>";
    foreach($unused_files as $file) {
        echo "<li style='color: red;'>" . basename($file) . "</li>";
    }
    echo "</ul>";
    
    echo "<form method='POST'>";
    echo "<p><strong>⚠️ WARNING:</strong> This will permanently delete the files listed above!</p>";
    echo "<button type='submit' name='confirm_delete' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete these files? This cannot be undone!\")'>Delete Unused Files</button>";
    echo "</form>";
    
    if(isset($_POST['confirm_delete'])) {
        echo "<h3>Deleting unused files...</h3>";
        
        foreach($unused_files as $file) {
            if(unlink($file)) {
                echo "<p style='color: green;'>✅ Deleted: <strong>" . basename($file) . "</strong></p>";
            } else {
                echo "<p style='color: red;'>❌ Error deleting: <strong>" . basename($file) . "</strong></p>";
            }
        }
        
        echo "<h3 style='color: green;'>✅ File cleanup completed!</h3>";
        echo "<p><a href='cleanup_files.php'>Refresh Page</a></p>";
    }
}

// Show final file count
$final_files = glob($modules_dir . '*.php');
echo "<p><strong>Final file count: " . count($final_files) . "</strong></p>";

echo "<h3>Remaining Files:</h3>";
echo "<ul>";
foreach($final_files as $file) {
    echo "<li style='color: green;'>" . basename($file) . "</li>";
}
echo "</ul>";

echo "<hr>";
echo "<p><a href='index.php'>← Back to Dashboard</a></p>";
?>

<style>
body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
h2, h3 { color: #333; }
ul { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.btn { padding: 10px 20px; font-size: 16px; border: none; border-radius: 5px; cursor: pointer; }
.btn-danger { background: #dc3545; color: white; }
.btn-danger:hover { background: #c82333; }
</style>
