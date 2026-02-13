<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$message_type = '';

// Get available car models from cars table
$car_models = [];
$cars_result = $conn->query("SELECT name, daily_rate FROM cars WHERE status='Active' ORDER BY name");
if ($cars_result) {
    while($car = $cars_result->fetch_assoc()) {
        $car_models[$car['name']] = $car['daily_rate'];
    }
}

// Handle Add/Edit Car Sale
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_sale'])) {
    $car_model = $conn->real_escape_string($_POST['car_model']);
    $original_price = floatval($_POST['original_price']);
    $sale_price = floatval($_POST['sale_price']);
    $discount_percentage = intval($_POST['discount_percentage']);
    $sale_start = $_POST['sale_start'];
    $sale_end = $_POST['sale_end'];
    $status = $_POST['status'];
    
    if (isset($_POST['sale_id']) && !empty($_POST['sale_id'])) {
        $sale_id = intval($_POST['sale_id']);
        $sql = "UPDATE car_sales SET car_model='$car_model', original_price=$original_price, sale_price=$sale_price, 
                discount_percentage=$discount_percentage, sale_start='$sale_start', sale_end='$sale_end', status='$status' 
                WHERE id=$sale_id";
        $message = '✅ Car sale updated successfully!';
    } else {
        $sql = "INSERT INTO car_sales (car_model, original_price, sale_price, discount_percentage, sale_start, sale_end, status) 
                VALUES ('$car_model', $original_price, $sale_price, $discount_percentage, '$sale_start', '$sale_end', '$status')";
        $message = '✅ Car sale added successfully!';
    }
    
    if ($conn->query($sql)) {
        $message_type = 'success';
    } else {
        $message = '❌ Error: ' . $conn->error;
        $message_type = 'error';
    }
}

// Handle Delete
if (isset($_GET['delete_sale'])) {
    $id = intval($_GET['delete_sale']);
    $conn->query("DELETE FROM car_sales WHERE id=$id");
    $message = '✅ Car sale deleted successfully!';
    $message_type = 'success';
}

// Get all car sales
$sales = [];
$result = $conn->query("SELECT * FROM car_sales ORDER BY created_at DESC");
while($row = $result->fetch_assoc()) {
    $sales[] = $row;
}

// Get sale for editing
$edit_sale = null;
if (isset($_GET['edit_sale'])) {
    $id = intval($_GET['edit_sale']);
    $edit_sale = $conn->query("SELECT * FROM car_sales WHERE id=$id")->fetch_assoc();
}

$conn->close();
?>

<div class="pagetitle">
  <h1>🚗 Car Sales Management</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">Car Sales</li>
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
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h6 class="mb-0"><?php echo $edit_sale ? '✏️ Edit Car Sale' : '➕ Add Car Sale'; ?></h6>
        </div>
        <div class="card-body">
          <form method="POST">
            <?php if ($edit_sale): ?>
            <input type="hidden" name="sale_id" value="<?php echo $edit_sale['id']; ?>">
            <?php endif; ?>
            
            <div class="mb-3">
              <label class="form-label">Car Model *</label>
              <select class="form-select" name="car_model" id="car_model" required>
                <option value="">Select car model...</option>
                <?php foreach($car_models as $model => $price): ?>
                <option value="<?php echo htmlspecialchars($model); ?>" data-price="<?php echo $price; ?>" <?php echo ($edit_sale && $edit_sale['car_model'] == $model) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($model); ?> - ₱<?php echo number_format($price, 2); ?>/day
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Original Price (₱) *</label>
              <input type="number" step="0.01" class="form-control" id="original_price" name="original_price" value="<?php echo $edit_sale ? $edit_sale['original_price'] : ''; ?>" required readonly>
              <small class="text-muted">Auto-filled from car model</small>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Discount (%) *</label>
              <input type="number" class="form-control" id="discount_percentage" name="discount_percentage" value="<?php echo $edit_sale ? $edit_sale['discount_percentage'] : ''; ?>" required min="1" max="99">
              <small class="text-muted">Enter discount percentage (1-99%)</small>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Sale Price (₱) *</label>
              <input type="number" step="0.01" class="form-control" id="sale_price" name="sale_price" value="<?php echo $edit_sale ? $edit_sale['sale_price'] : ''; ?>" required readonly>
              <small class="text-muted">Auto-calculated from discount</small>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Sale Start Date *</label>
              <input type="date" class="form-control" name="sale_start" value="<?php echo $edit_sale ? $edit_sale['sale_start'] : date('Y-m-d'); ?>" required>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Sale End Date *</label>
              <input type="date" class="form-control" name="sale_end" value="<?php echo $edit_sale ? $edit_sale['sale_end'] : ''; ?>" required>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Status *</label>
              <select class="form-select" name="status" required>
                <option value="Active" <?php echo ($edit_sale && $edit_sale['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                <option value="Inactive" <?php echo ($edit_sale && $edit_sale['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
              </select>
            </div>
            
            <button type="submit" name="save_sale" class="btn btn-primary w-100">
              <i class="bi bi-check-circle me-2"></i><?php echo $edit_sale ? 'Update Sale' : 'Add Sale'; ?>
            </button>
            <?php if ($edit_sale): ?>
            <a href="index.php?page=car_sales" class="btn btn-secondary w-100 mt-2">Cancel</a>
            <?php endif; ?>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card">
        <div class="card-header bg-light">
          <h6 class="mb-0">🏷️ Active Car Sales</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Car Model</th>
                  <th>Original Price</th>
                  <th>Sale Price</th>
                  <th>Discount</th>
                  <th>Valid Until</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($sales as $sale): ?>
                <tr>
                  <td><strong><?php echo htmlspecialchars($sale['car_model']); ?></strong></td>
                  <td><del class="text-muted">₱<?php echo number_format($sale['original_price'], 2); ?></del></td>
                  <td><strong class="text-success">₱<?php echo number_format($sale['sale_price'], 2); ?></strong></td>
                  <td><span class="badge bg-danger"><?php echo $sale['discount_percentage']; ?>% OFF</span></td>
                  <td><?php echo date('M d, Y', strtotime($sale['sale_end'])); ?></td>
                  <td>
                    <span class="badge bg-<?php echo $sale['status'] == 'Active' ? 'success' : 'secondary'; ?>">
                      <?php echo $sale['status']; ?>
                    </span>
                  </td>
                  <td>
                    <a href="?page=car_sales&edit_sale=<?php echo $sale['id']; ?>" class="btn btn-sm btn-warning">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <a href="?page=car_sales&delete_sale=<?php echo $sale['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this sale?')">
                      <i class="bi bi-trash"></i>
                    </a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
.btn-primary {
  background-color: #666;
  border-color: #666;
}

.btn-primary:hover {
  background-color: #555;
  border-color: #555;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const carModelSelect = document.getElementById('car_model');
    const originalPriceInput = document.getElementById('original_price');
    const discountInput = document.getElementById('discount_percentage');
    const salePriceInput = document.getElementById('sale_price');
    
    // Auto-fill original price when car model is selected
    carModelSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        
        if (price) {
            originalPriceInput.value = price;
            calculateSalePrice();
        } else {
            originalPriceInput.value = '';
            salePriceInput.value = '';
        }
    });
    
    // Auto-calculate sale price when discount changes
    discountInput.addEventListener('input', calculateSalePrice);
    
    function calculateSalePrice() {
        const originalPrice = parseFloat(originalPriceInput.value);
        const discount = parseFloat(discountInput.value);
        
        if (originalPrice && discount && discount > 0 && discount < 100) {
            const salePrice = originalPrice - (originalPrice * discount / 100);
            salePriceInput.value = salePrice.toFixed(2);
        } else {
            salePriceInput.value = '';
        }
    }
});
</script>
