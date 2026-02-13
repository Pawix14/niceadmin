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

// Handle Add/Edit Promo Code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_promo'])) {
    $code = strtoupper($conn->real_escape_string($_POST['code']));
    $discount_type = $_POST['discount_type'];
    $discount_value = floatval($_POST['discount_value']);
    $description = $conn->real_escape_string($_POST['description']);
    $valid_from = $_POST['valid_from'];
    $valid_until = $_POST['valid_until'];
    $usage_limit = !empty($_POST['usage_limit']) ? intval($_POST['usage_limit']) : 'NULL';
    $for_first_time_only = isset($_POST['for_first_time_only']) ? 1 : 0;
    $status = $_POST['status'];
    
    if (isset($_POST['promo_id']) && !empty($_POST['promo_id'])) {
        $promo_id = intval($_POST['promo_id']);
        $sql = "UPDATE promo_codes SET code='$code', discount_type='$discount_type', discount_value=$discount_value, 
                description='$description', valid_from='$valid_from', valid_until='$valid_until', 
                usage_limit=$usage_limit, for_first_time_only=$for_first_time_only, status='$status' 
                WHERE id=$promo_id";
        $message = '✅ Promo code updated successfully!';
    } else {
        $sql = "INSERT INTO promo_codes (code, discount_type, discount_value, description, valid_from, valid_until, usage_limit, for_first_time_only, status) 
                VALUES ('$code', '$discount_type', $discount_value, '$description', '$valid_from', '$valid_until', $usage_limit, $for_first_time_only, '$status')";
        $message = '✅ Promo code added successfully!';
    }
    
    if ($conn->query($sql)) {
        $message_type = 'success';
    } else {
        $message = '❌ Error: ' . $conn->error;
        $message_type = 'error';
    }
}

// Handle Delete
if (isset($_GET['delete_promo'])) {
    $id = intval($_GET['delete_promo']);
    $conn->query("DELETE FROM promo_codes WHERE id=$id");
    $message = '✅ Promo code deleted successfully!';
    $message_type = 'success';
}

// Get all promo codes
$promos = [];
$result = $conn->query("SELECT * FROM promo_codes ORDER BY created_at DESC");
while($row = $result->fetch_assoc()) {
    $promos[] = $row;
}

// Get promo for editing
$edit_promo = null;
if (isset($_GET['edit_promo'])) {
    $id = intval($_GET['edit_promo']);
    $edit_promo = $conn->query("SELECT * FROM promo_codes WHERE id=$id")->fetch_assoc();
}

$conn->close();
?>

<div class="pagetitle">
  <h1>🎟️ Promo Codes Management</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">Promo Codes</li>
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
        <div class="card-header" style="background-color: #666; color: white;">
          <h6 class="mb-0"><?php echo $edit_promo ? '✏️ Edit Promo Code' : '➕ Add Promo Code'; ?></h6>
        </div>
        <div class="card-body">
          <form method="POST">
            <?php if ($edit_promo): ?>
            <input type="hidden" name="promo_id" value="<?php echo $edit_promo['id']; ?>">
            <?php endif; ?>
            
            <div class="mb-3">
              <label class="form-label">Promo Code *</label>
              <input type="text" class="form-control" name="code" value="<?php echo $edit_promo ? htmlspecialchars($edit_promo['code']) : ''; ?>" required style="text-transform: uppercase;">
            </div>
            
            <div class="mb-3">
              <label class="form-label">Discount Type *</label>
              <select class="form-select" name="discount_type" required>
                <option value="percentage" <?php echo ($edit_promo && $edit_promo['discount_type'] == 'percentage') ? 'selected' : ''; ?>>Percentage (%)</option>
                <option value="fixed" <?php echo ($edit_promo && $edit_promo['discount_type'] == 'fixed') ? 'selected' : ''; ?>>Fixed Amount (₱)</option>
              </select>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Discount Value *</label>
              <input type="number" step="0.01" class="form-control" name="discount_value" value="<?php echo $edit_promo ? $edit_promo['discount_value'] : ''; ?>" required>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea class="form-control" name="description" rows="2"><?php echo $edit_promo ? htmlspecialchars($edit_promo['description']) : ''; ?></textarea>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Valid From *</label>
              <input type="date" class="form-control" name="valid_from" value="<?php echo $edit_promo ? $edit_promo['valid_from'] : date('Y-m-d'); ?>" required>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Valid Until *</label>
              <input type="date" class="form-control" name="valid_until" value="<?php echo $edit_promo ? $edit_promo['valid_until'] : ''; ?>" required>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Usage Limit</label>
              <input type="number" class="form-control" name="usage_limit" value="<?php echo $edit_promo ? $edit_promo['usage_limit'] : ''; ?>" placeholder="Leave empty for unlimited">
            </div>
            
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" name="for_first_time_only" id="firstTimeOnly" <?php echo ($edit_promo && $edit_promo['for_first_time_only']) ? 'checked' : ''; ?>>
              <label class="form-check-label" for="firstTimeOnly">
                For first-time customers only
              </label>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Status *</label>
              <select class="form-select" name="status" required>
                <option value="Active" <?php echo ($edit_promo && $edit_promo['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                <option value="Inactive" <?php echo ($edit_promo && $edit_promo['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
              </select>
            </div>
            
            <button type="submit" name="save_promo" class="btn w-100" style="background-color: #666; color: white;">
              <i class="bi bi-check-circle me-2"></i><?php echo $edit_promo ? 'Update Promo' : 'Add Promo'; ?>
            </button>
            <?php if ($edit_promo): ?>
            <a href="index.php?page=promo_codes" class="btn btn-secondary w-100 mt-2">Cancel</a>
            <?php endif; ?>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card">
        <div class="card-header bg-light">
          <h6 class="mb-0">🎫 Active Promo Codes</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Code</th>
                  <th>Discount</th>
                  <th>Valid Until</th>
                  <th>Usage</th>
                  <th>First-Time Only</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($promos as $promo): ?>
                <tr>
                  <td><strong class="text-primary"><?php echo htmlspecialchars($promo['code']); ?></strong></td>
                  <td>
                    <?php 
                    if ($promo['discount_type'] == 'percentage') {
                        echo $promo['discount_value'] . '%';
                    } else {
                        echo '₱' . number_format($promo['discount_value'], 2);
                    }
                    ?>
                  </td>
                  <td><?php echo date('M d, Y', strtotime($promo['valid_until'])); ?></td>
                  <td>
                    <?php 
                    if ($promo['usage_limit']) {
                        echo $promo['times_used'] . '/' . $promo['usage_limit'];
                    } else {
                        echo $promo['times_used'] . '/∞';
                    }
                    ?>
                  </td>
                  <td>
                    <?php if ($promo['for_first_time_only']): ?>
                      <span class="badge bg-info">Yes</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">No</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="badge bg-<?php echo $promo['status'] == 'Active' ? 'success' : 'secondary'; ?>">
                      <?php echo $promo['status']; ?>
                    </span>
                  </td>
                  <td>
                    <a href="?page=promo_codes&edit_promo=<?php echo $promo['id']; ?>" class="btn btn-sm btn-warning">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <a href="?page=promo_codes&delete_promo=<?php echo $promo['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this promo code?')">
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
