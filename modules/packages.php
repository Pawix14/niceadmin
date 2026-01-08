<?php
// modules/packages.php

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $package_name = $conn->real_escape_string($_POST['package_name']);
    $destination_country = $conn->real_escape_string($_POST['destination_country']);
    $destination_city = $conn->real_escape_string($_POST['destination_city']);
    $duration_days = (int)$_POST['duration_days'];
    $price = (float)$_POST['price'];
    $inclusions = $conn->real_escape_string($_POST['inclusions']);
    $status = $conn->real_escape_string($_POST['status']);
    
    // Generate package ID
    $package_id = 'PKG-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    
    $sql = "INSERT INTO travel_packages (package_id, package_name, destination_country, destination_city, duration_days, price, inclusions, status) 
            VALUES ('$package_id', '$package_name', '$destination_country', '$destination_city', $duration_days, $price, '$inclusions', '$status')";
    
    if ($conn->query($sql)) {
        $message = "✅ Travel package created successfully! Package ID: <strong>$package_id</strong>";
        $message_type = "success";
    } else {
        $message = "❌ Error creating package: " . $conn->error;
        $message_type = "error";
    }
}

// Fetch countries for dropdown
$countries_result = $conn->query("SELECT country_name FROM countries ORDER BY country_name");
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">📦 Travel Packages Management</h5>
            
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <form class="row g-3" method="POST" action="">
                <div class="col-md-6">
                    <label for="package_name" class="form-label">Package Name *</label>
                    <input type="text" class="form-control" id="package_name" name="package_name" required placeholder="Ex. Japan Golden Route">
                </div>
                
                <div class="col-md-6">
                    <label for="destination_country" class="form-label">Destination Country *</label>
                    <select class="form-select" id="destination_country" name="destination_country" required>
                        <option value="">Select country...</option>
                        <?php while($country = $countries_result->fetch_assoc()): ?>
                        <option value="<?php echo $country['country_name']; ?>">
                            <?php echo htmlspecialchars($country['country_name']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="destination_city" class="form-label">Destination City *</label>
                    <input type="text" class="form-control" id="destination_city" name="destination_city" required placeholder="Ex. Tokyo">
                </div>
                
                <div class="col-md-6">
                    <label for="duration_days" class="form-label">Duration (Days) *</label>
                    <input type="number" class="form-control" id="duration_days" name="duration_days" min="1" max="30" required>
                </div>
                
                <div class="col-md-6">
                    <label for="price" class="form-label">Price (Php) *</label>
                    <div class="input-group">
                        <span class="input-group-text">₱</span>
                        <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label for="status" class="form-label">Status *</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="Active">✅ Active</option>
                        <option value="Inactive">⏸️ Inactive</option>
                        <option value="Fully Booked">🚫 Fully Booked</option>
                    </select>
                </div>
                
                <div class="col-12">
                    <label for="inclusions" class="form-label">Package Inclusions</label>
                    <textarea class="form-control" id="inclusions" name="inclusions" rows="3" placeholder="What's included in this package..."></textarea>
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">Create Package</button>
                    <button type="reset" class="btn btn-secondary">Clear Form</button>
                </div>
            </form>
            
            <!-- Display existing packages -->
            <div class="mt-5">
                <h5 class="card-title">📋 Existing Packages</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Package ID</th>
                                <th>Package Name</th>
                                <th>Destination</th>
                                <th>Duration</th>
                                <th>Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $packages_result = $conn->query("SELECT * FROM travel_packages ORDER BY created_date DESC LIMIT 10");
                            while($package = $packages_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $package['package_id']; ?></td>
                                <td><?php echo htmlspecialchars($package['package_name']); ?></td>
                                <td><?php echo htmlspecialchars($package['destination_city']) . ', ' . htmlspecialchars($package['destination_country']); ?></td>
                                <td><?php echo $package['duration_days']; ?> days</td>
                                <td>$<?php echo number_format($package['price'], 2); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                    switch($package['status']) {
                                        case 'Active': echo 'success'; break;
                                        case 'Inactive': echo 'secondary'; break;
                                        case 'Fully Booked': echo 'danger'; break;
                                        default: echo 'primary';
                                    }
                                    ?>"><?php echo $package['status']; ?></span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $conn->close(); ?>