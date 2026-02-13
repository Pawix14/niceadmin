<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create maintenance table
$conn->query("CREATE TABLE IF NOT EXISTS car_maintenance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_id INT NOT NULL,
    maintenance_type VARCHAR(50) NOT NULL,
    description TEXT,
    cost DECIMAL(10,2),
    maintenance_date DATE NOT NULL,
    next_service_date DATE,
    performed_by VARCHAR(100),
    status VARCHAR(20) DEFAULT 'Completed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
)");

$message = '';
$message_type = '';

// Handle Add Maintenance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_maintenance'])) {
    $car_id = intval($_POST['car_id']);
    $maintenance_type = $conn->real_escape_string($_POST['maintenance_type']);
    $description = $conn->real_escape_string($_POST['description']);
    $cost = floatval($_POST['cost']);
    $maintenance_date = $conn->real_escape_string($_POST['maintenance_date']);
    $next_service_date = !empty($_POST['next_service_date']) ? "'".$conn->real_escape_string($_POST['next_service_date'])."'" : 'NULL';
    $performed_by = $conn->real_escape_string($_POST['performed_by']);
    
    $sql = "INSERT INTO car_maintenance (car_id, maintenance_type, description, cost, maintenance_date, next_service_date, performed_by) 
            VALUES ($car_id, '$maintenance_type', '$description', $cost, '$maintenance_date', $next_service_date, '$performed_by')";
    
    if ($conn->query($sql)) {
        $message = '✅ Maintenance record added successfully!';
        $message_type = 'success';
    } else {
        $message = '❌ Error: ' . $conn->error;
        $message_type = 'error';
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM car_maintenance WHERE id=$id");
    $message = '✅ Record deleted successfully!';
    $message_type = 'success';
}

// Get all cars
$cars = [];
$result = $conn->query("SELECT id, name, license_plate FROM cars ORDER BY name");
while($row = $result->fetch_assoc()) {
    $cars[] = $row;
}

// Get maintenance records
$maintenance_records = [];
$result = $conn->query("SELECT cm.*, c.name as car_name, c.license_plate 
                        FROM car_maintenance cm 
                        JOIN cars c ON cm.car_id = c.id 
                        ORDER BY cm.maintenance_date DESC");
while($row = $result->fetch_assoc()) {
    $maintenance_records[] = $row;
}

$conn->close();
?>

<style>
.premium-dashboard { max-width: 1400px; margin: 0 auto; }
.section-card { background: white; border-radius: 16px; padding: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border: 1px solid #f0f0f0; margin-bottom: 24px; overflow: hidden; }
.section-header { background: #0a2540; color: white; padding: 16px 24px; font-weight: 600; font-size: 1rem; }
.maintenance-card { background: white; border-radius: 12px; padding: 20px; border: 1px solid #e8e8e8; margin-bottom: 16px; transition: all 0.3s; }
.maintenance-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-color: #0a2540; }
.type-badge { padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; }
.type-service { background: rgba(10, 37, 64, 0.1); color: #0a2540; }
.type-repair { background: rgba(220, 53, 69, 0.1); color: #dc3545; }
.type-cleaning { background: rgba(25, 135, 84, 0.1); color: #198754; }
.type-inspection { background: rgba(255, 193, 7, 0.1); color: #ffc107; }
</style>

<div class="premium-dashboard">
  <div class="pagetitle mb-4">
    <h1 style="color: #0a2540; font-weight: 700;">🔧 Car Maintenance</h1>
    <p class="text-muted">Track service history, repairs, and cleaning status</p>
  </div>

  <?php if ($message): ?>
  <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <div class="row g-3">
    <div class="col-lg-4">
      <div class="section-card">
        <div class="section-header"><i class="bi bi-plus-circle me-2"></i>Add Maintenance Record</div>
        <div class="p-4">
          <form method="POST">
            <div class="mb-3">
              <label class="form-label">Car *</label>
              <select class="form-select" name="car_id" required>
                <option value="">Select car...</option>
                <?php foreach($cars as $car): ?>
                <option value="<?php echo $car['id']; ?>"><?php echo htmlspecialchars($car['name']); ?> <?php echo $car['license_plate'] ? '('.$car['license_plate'].')' : ''; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Type *</label>
              <select class="form-select" name="maintenance_type" required>
                <option value="Service">Regular Service</option>
                <option value="Repair">Repair</option>
                <option value="Cleaning">Cleaning</option>
                <option value="Inspection">Inspection</option>
                <option value="Tire Change">Tire Change</option>
                <option value="Oil Change">Oil Change</option>
                <option value="Damage Report">Damage Report</option>
              </select>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Description *</label>
              <textarea class="form-control" name="description" rows="3" required placeholder="Details of maintenance work..."></textarea>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Cost (₱) *</label>
              <input type="number" step="0.01" class="form-control" name="cost" required>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Maintenance Date *</label>
              <input type="date" class="form-control" name="maintenance_date" required value="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div class="mb-3">
              <label class="form-label">Next Service Date</label>
              <input type="date" class="form-control" name="next_service_date">
            </div>
            
            <div class="mb-3">
              <label class="form-label">Performed By *</label>
              <input type="text" class="form-control" name="performed_by" required placeholder="Mechanic/Service center name">
            </div>
            
            <button type="submit" name="add_maintenance" class="btn w-100" style="background: #0a2540; color: white; border-radius: 10px; padding: 12px; font-weight: 600;">
              <i class="bi bi-plus-circle me-2"></i>Add Record
            </button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="section-card">
        <div class="section-header"><i class="bi bi-clock-history me-2"></i>Maintenance History</div>
        <div class="p-4">
          <?php if (empty($maintenance_records)): ?>
          <div class="text-center py-5">
            <i class="bi bi-tools display-1 text-muted"></i>
            <h5 class="mt-3">No maintenance records yet</h5>
            <p class="text-muted">Add your first maintenance record</p>
          </div>
          <?php else: ?>
          <?php foreach($maintenance_records as $record): ?>
          <div class="maintenance-card">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <h6 class="mb-1" style="font-weight: 700; color: #0a2540;"><?php echo htmlspecialchars($record['car_name']); ?></h6>
                <small class="text-muted"><?php echo $record['license_plate'] ? $record['license_plate'] : 'No plate'; ?></small>
              </div>
              <span class="type-badge type-<?php echo strtolower($record['maintenance_type']); ?>"><?php echo $record['maintenance_type']; ?></span>
            </div>
            <p class="mb-2" style="font-size: 0.9rem;"><?php echo htmlspecialchars($record['description']); ?></p>
            <div class="row g-2 text-muted" style="font-size: 0.85rem;">
              <div class="col-md-4"><i class="bi bi-calendar3"></i> <?php echo date('M d, Y', strtotime($record['maintenance_date'])); ?></div>
              <div class="col-md-4"><i class="bi bi-cash"></i> ₱<?php echo number_format($record['cost'], 2); ?></div>
              <div class="col-md-4"><i class="bi bi-person"></i> <?php echo htmlspecialchars($record['performed_by']); ?></div>
              <?php if ($record['next_service_date']): ?>
              <div class="col-12"><i class="bi bi-arrow-repeat"></i> Next: <?php echo date('M d, Y', strtotime($record['next_service_date'])); ?></div>
              <?php endif; ?>
            </div>
            <div class="mt-2">
              <a href="?page=car_maintenance&delete=<?php echo $record['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this record?')">
                <i class="bi bi-trash"></i> Delete
              </a>
            </div>
          </div>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
