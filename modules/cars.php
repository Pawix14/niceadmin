<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update car availability based on active rentals
$conn->query("UPDATE cars c 
    LEFT JOIN car_rental_bookings b ON c.name = b.car_model 
    AND b.status IN ('Confirmed', 'Pending') 
    AND CURDATE() BETWEEN b.pickup_date AND b.return_date
    SET c.status = IF(b.id IS NULL, 'Active', 'Rented')");

// Create cars table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL,
    daily_rate DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    features TEXT,
    fuel_type VARCHAR(30) DEFAULT 'Gasoline',
    transmission VARCHAR(20) DEFAULT 'Automatic',
    seating_capacity INT DEFAULT 5,
    mileage_limit INT DEFAULT 200,
    car_year INT,
    license_plate VARCHAR(20),
    vin VARCHAR(50),
    color VARCHAR(30),
    status VARCHAR(20) DEFAULT 'Active',
    is_featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Add new columns if they don't exist
$new_columns = [
    'fuel_type' => "VARCHAR(30) DEFAULT 'Gasoline' AFTER features",
    'transmission' => "VARCHAR(20) DEFAULT 'Automatic' AFTER fuel_type",
    'seating_capacity' => "INT DEFAULT 5 AFTER transmission",
    'mileage_limit' => "INT DEFAULT 200 AFTER seating_capacity",
    'car_year' => "INT AFTER mileage_limit",
    'license_plate' => "VARCHAR(20) AFTER car_year",
    'vin' => "VARCHAR(50) AFTER license_plate",
    'color' => "VARCHAR(30) AFTER vin"
];

foreach ($new_columns as $column => $definition) {
    $check = $conn->query("SHOW COLUMNS FROM cars LIKE '$column'");
    if ($check->num_rows == 0) {
        $conn->query("ALTER TABLE cars ADD COLUMN $column $definition");
    }
}

$check_column = $conn->query("SHOW COLUMNS FROM cars LIKE 'is_featured'");
if ($check_column->num_rows == 0) {
    $conn->query("ALTER TABLE cars ADD COLUMN is_featured TINYINT(1) DEFAULT 0 AFTER status");
}

$message = '';
$message_type = '';

// Handle Add/Edit Car
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_car'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $type = $conn->real_escape_string($_POST['type']);
    $daily_rate = floatval($_POST['daily_rate']);
    $features = $conn->real_escape_string($_POST['features']);
    $fuel_type = $conn->real_escape_string($_POST['fuel_type']);
    $transmission = $conn->real_escape_string($_POST['transmission']);
    $seating_capacity = intval($_POST['seating_capacity']);
    $mileage_limit = intval($_POST['mileage_limit']);
    $car_year = !empty($_POST['car_year']) ? intval($_POST['car_year']) : 'NULL';
    $license_plate = $conn->real_escape_string($_POST['license_plate']);
    $vin = $conn->real_escape_string($_POST['vin']);
    $color = $conn->real_escape_string($_POST['color']);
    $status = $_POST['status'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = 'uploads/cars/' . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_path = $upload_path;
            }
        }
    }
    
    if (isset($_POST['car_id']) && !empty($_POST['car_id'])) {
        $car_id = intval($_POST['car_id']);
        if ($image_path) {
            $sql = "UPDATE cars SET name='$name', type='$type', daily_rate=$daily_rate, image='$image_path', features='$features', fuel_type='$fuel_type', transmission='$transmission', seating_capacity=$seating_capacity, mileage_limit=$mileage_limit, car_year=$car_year, license_plate='$license_plate', vin='$vin', color='$color', status='$status', is_featured=$is_featured WHERE id=$car_id";
        } else {
            $sql = "UPDATE cars SET name='$name', type='$type', daily_rate=$daily_rate, features='$features', fuel_type='$fuel_type', transmission='$transmission', seating_capacity=$seating_capacity, mileage_limit=$mileage_limit, car_year=$car_year, license_plate='$license_plate', vin='$vin', color='$color', status='$status', is_featured=$is_featured WHERE id=$car_id";
        }
        $message = '✅ Car updated successfully!';
    } else {
        $sql = "INSERT INTO cars (name, type, daily_rate, image, features, fuel_type, transmission, seating_capacity, mileage_limit, car_year, license_plate, vin, color, status, is_featured) VALUES ('$name', '$type', $daily_rate, '$image_path', '$features', '$fuel_type', '$transmission', $seating_capacity, $mileage_limit, $car_year, '$license_plate', '$vin', '$color', '$status', $is_featured)";
        $message = '✅ Car added successfully!';
    }
    
    if ($conn->query($sql)) {
        $message_type = 'success';
    } else {
        $message = '❌ Error: ' . $conn->error;
        $message_type = 'error';
    }
}

// Handle Delete
if (isset($_GET['delete_car'])) {
    $id = intval($_GET['delete_car']);
    $conn->query("DELETE FROM cars WHERE id=$id");
    $message = '✅ Car deleted successfully!';
    $message_type = 'success';
}

// Get all cars
$cars = [];
$result = $conn->query("SELECT * FROM cars ORDER BY created_at DESC");
while($row = $result->fetch_assoc()) {
    $cars[] = $row;
}

// Get car for editing
$edit_car = null;
if (isset($_GET['edit_car'])) {
    $id = intval($_GET['edit_car']);
    $edit_car = $conn->query("SELECT * FROM cars WHERE id=$id")->fetch_assoc();
}

$conn->close();
?>

<div class="pagetitle">
  <h1>🚗 Car Management</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">Cars</li>
    </ol>
  </nav>
</div>

<section class="section">
  <?php if ($message): ?>
  <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <div class="row">
    <!-- Left Column: Add/Edit Car Form -->
    <div class="col-lg-5">
      <div class="card">
        <div class="card-header" style="background-color: #0a2540; color: white;">
          <h6 class="mb-0"><?php echo $edit_car ? '✏️ Edit Car' : '➕ Add New Car'; ?></h6>
        </div>
        <div class="card-body" style="max-height: 800px; overflow-y: auto;">
          <form method="POST" enctype="multipart/form-data">
            <?php if ($edit_car): ?>
            <input type="hidden" name="car_id" value="<?php echo $edit_car['id']; ?>">
            <?php endif; ?>
            
            <div class="mb-3">
              <label class="form-label fw-bold">Car Name *</label>
              <input type="text" class="form-control" name="name" value="<?php echo $edit_car ? htmlspecialchars($edit_car['name']) : ''; ?>" required>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Car Type *</label>
                <select class="form-select" name="type" required>
                  <option value="">Select type...</option>
                  <option value="Economy" <?php echo ($edit_car && $edit_car['type'] == 'Economy') ? 'selected' : ''; ?>>Economy</option>
                  <option value="Compact" <?php echo ($edit_car && $edit_car['type'] == 'Compact') ? 'selected' : ''; ?>>Compact</option>
                  <option value="SUV" <?php echo ($edit_car && $edit_car['type'] == 'SUV') ? 'selected' : ''; ?>>SUV</option>
                  <option value="Luxury" <?php echo ($edit_car && $edit_car['type'] == 'Luxury') ? 'selected' : ''; ?>>Luxury</option>
                  <option value="Electric" <?php echo ($edit_car && $edit_car['type'] == 'Electric') ? 'selected' : ''; ?>>Electric</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Daily Rate (₱) *</label>
                <input type="number" step="0.01" class="form-control" name="daily_rate" value="<?php echo $edit_car ? $edit_car['daily_rate'] : ''; ?>" required>
              </div>
            </div>
            
            <div class="mb-3">
              <label class="form-label fw-bold">Car Image</label>
              <?php if ($edit_car && $edit_car['image']): ?>
              <div class="mb-2">
                <img src="<?php echo $edit_car['image']; ?>" style="max-height: 80px; border-radius: 8px;">
              </div>
              <?php endif; ?>
              <input type="file" class="form-control" name="image" accept="image/*">
              <small class="text-muted">JPG, PNG, GIF (Max 5MB)</small>
            </div>
            
            <div class="mb-3">
              <label class="form-label fw-bold">Features</label>
              <textarea class="form-control" name="features" rows="2" placeholder="e.g., 5 seats, Air Conditioning, GPS"><?php echo $edit_car ? htmlspecialchars($edit_car['features']) : ''; ?></textarea>
            </div>
            
            <div class="row">
              <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Fuel Type</label>
                <select class="form-select" name="fuel_type">
                  <option value="Gasoline" <?php echo ($edit_car && $edit_car['fuel_type'] == 'Gasoline') ? 'selected' : ''; ?>>Gasoline</option>
                  <option value="Diesel" <?php echo ($edit_car && $edit_car['fuel_type'] == 'Diesel') ? 'selected' : ''; ?>>Diesel</option>
                  <option value="Electric" <?php echo ($edit_car && $edit_car['fuel_type'] == 'Electric') ? 'selected' : ''; ?>>Electric</option>
                  <option value="Hybrid" <?php echo ($edit_car && $edit_car['fuel_type'] == 'Hybrid') ? 'selected' : ''; ?>>Hybrid</option>
                </select>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Transmission</label>
                <select class="form-select" name="transmission">
                  <option value="Automatic" <?php echo ($edit_car && $edit_car['transmission'] == 'Automatic') ? 'selected' : ''; ?>>Automatic</option>
                  <option value="Manual" <?php echo ($edit_car && $edit_car['transmission'] == 'Manual') ? 'selected' : ''; ?>>Manual</option>
                </select>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Seats</label>
                <input type="number" class="form-control" name="seating_capacity" value="<?php echo $edit_car ? $edit_car['seating_capacity'] : '5'; ?>" min="2" max="15">
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Car Year</label>
                <input type="number" class="form-control" name="car_year" value="<?php echo $edit_car ? $edit_car['car_year'] : ''; ?>" placeholder="2024">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Color</label>
                <input type="text" class="form-control" name="color" value="<?php echo $edit_car ? htmlspecialchars($edit_car['color']) : ''; ?>" placeholder="White, Black">
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">License Plate</label>
                <input type="text" class="form-control" name="license_plate" value="<?php echo $edit_car ? htmlspecialchars($edit_car['license_plate']) : ''; ?>" placeholder="ABC-1234">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Mileage Limit</label>
                <input type="number" class="form-control" name="mileage_limit" value="<?php echo $edit_car ? $edit_car['mileage_limit'] : '200'; ?>" placeholder="km/day">
              </div>
            </div>
            
            <div class="mb-3">
              <label class="form-label fw-bold">VIN</label>
              <input type="text" class="form-control" name="vin" value="<?php echo $edit_car ? htmlspecialchars($edit_car['vin']) : ''; ?>" placeholder="17-character VIN">
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Status</label>
                <select class="form-select" name="status">
                  <option value="Active" <?php echo ($edit_car && $edit_car['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                  <option value="Inactive" <?php echo ($edit_car && $edit_car['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
              </div>
              <div class="col-md-6 mb-3 d-flex align-items-end">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" <?php echo ($edit_car && $edit_car['is_featured']) ? 'checked' : ''; ?>>
                  <label class="form-check-label fw-bold" for="is_featured">
                    ⭐ Feature this car
                  </label>
                </div>
              </div>
            </div>
            
            <div class="d-grid gap-2">
              <button type="submit" name="save_car" class="btn" style="background-color: #0a2540; color: white; padding: 12px;">
                <i class="bi bi-check-circle me-2"></i><?php echo $edit_car ? 'Update Car' : 'Add Car'; ?>
              </button>
              <?php if ($edit_car): ?>
              <a href="index.php?page=cars" class="btn btn-outline-secondary">Cancel</a>
              <?php endif; ?>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Right Column: All Cars Table -->
    <div class="col-lg-7">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #f8fafc;">
          <h6 class="mb-0 fw-bold" style="color: #0a2540;">
            <i class="bi bi-grid me-2"></i>All Cars (<?php echo count($cars); ?>)
          </h6>
          <div>
            <input type="text" class="form-control form-control-sm" id="searchCar" placeholder="🔍 Search cars..." style="width: 200px;">
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive" style="max-height: 750px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0" id="carsTable">
              <thead style="background-color: #f1f5f9; position: sticky; top: 0; z-index: 10;">
                <tr>
                  <th style="width: 60px;">Image</th>
                  <th>Car Details</th>
                  <th>Type</th>
                  <th>Rate</th>
                  <th>Status</th>
                  <th style="width: 100px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($cars as $car): ?>
                <tr>
                  <td>
                    <?php if ($car['image'] && file_exists($car['image'])): ?>
                    <img src="<?php echo $car['image']; ?>" style="width: 60px; height: 45px; object-fit: cover; border-radius: 8px;">
                    <?php else: ?>
                    <div style="width: 60px; height: 45px; background: linear-gradient(135deg, #f1f5f9, #e2e8f0); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                      <i class="bi bi-car-front" style="font-size: 20px; color: #64748b;"></i>
                    </div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="fw-bold" style="color: #0a2540;"><?php echo htmlspecialchars($car['name']); ?></div>
                    <div class="small text-muted">
                      <?php echo $car['fuel_type']; ?> • <?php echo $car['transmission']; ?> • <?php echo $car['seating_capacity']; ?> seats
                    </div>
                    <?php if ($car['is_featured']): ?>
                    <span class="badge bg-warning text-dark mt-1" style="font-size: 10px;">⭐ Featured</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="badge" style="background-color: #e2e8f0; color: #334155; padding: 6px 12px;">
                      <?php echo $car['type']; ?>
                    </span>
                  </td>
                  <td>
                    <span class="fw-bold" style="color: #0a2540;">₱<?php echo number_format($car['daily_rate'], 2); ?></span>
                    <span class="text-muted">/day</span>
                  </td>
                  <td>
                    <span class="badge bg-<?php echo $car['status'] == 'Active' ? 'success' : ($car['status'] == 'Rented' ? 'danger' : 'secondary'); ?>" style="padding: 6px 12px;">
                      <?php echo $car['status']; ?>
                    </span>
                  </td>
                  <td>
                    <div class="d-flex gap-1">
                      <a href="?page=cars&edit_car=<?php echo $car['id']; ?>" class="btn btn-sm" style="background-color: #f1f5f9; color: #475569;" title="Edit">
                        <i class="bi bi-pencil"></i>
                      </a>
                      <a href="?page=cars&delete_car=<?php echo $car['id']; ?>" class="btn btn-sm" style="background-color: #fee2e2; color: #b91c1c;" onclick="return confirm('Delete this car?')" title="Delete">
                        <i class="bi bi-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($cars)): ?>
                <tr>
                  <td colspan="6" class="text-center py-5">
                    <i class="bi bi-car-front display-4 text-muted"></i>
                    <h6 class="mt-3">No cars found</h6>
                    <p class="text-muted">Add your first car to get started</p>
                  </td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
// Simple client-side search
document.getElementById('searchCar').addEventListener('keyup', function() {
    let searchValue = this.value.toLowerCase();
    let tableRows = document.querySelectorAll('#carsTable tbody tr');
    
    tableRows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});
</script>

<style>
.card {
    border: 1px solid #edf2f7;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
}

.card-header {
    border-bottom: 1px solid #edf2f7;
    padding: 16px 20px;
}

.table {
    font-size: 14px;
}

.table td, .table th {
    padding: 16px 20px;
}

.btn-sm {
    padding: 6px 10px;
    border-radius: 8px;
}

.btn-sm i {
    font-size: 14px;
}

.form-label {
    font-size: 13px;
    margin-bottom: 4px;
    color: #334155;
}

.form-control, .form-select {
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 12px;
    font-size: 14px;
}

.form-control:focus, .form-select:focus {
    border-color: #0a2540;
    box-shadow: 0 0 0 3px rgba(10, 37, 64, 0.1);
}

/* Custom scrollbar */
.table-responsive::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #94a3b8;
    border-radius: 20px;
}
</style>