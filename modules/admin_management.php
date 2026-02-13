<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create admins table if it doesn't exist
$check_table = $conn->query("SHOW TABLES LIKE 'admins'");
if ($check_table->num_rows == 0) {
    $create_sql = "CREATE TABLE admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        admin_id VARCHAR(20) UNIQUE NOT NULL,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        phone VARCHAR(20),
        role VARCHAR(50) DEFAULT 'Admin',
        status VARCHAR(20) DEFAULT 'Active',
        last_login DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $conn->query($create_sql);
    
    // Create default admin
    $default_password = password_hash('admin123', PASSWORD_DEFAULT);
    $conn->query("INSERT INTO admins (admin_id, username, password, full_name, email, role, status) 
                  VALUES ('ADM001', 'admin', '$default_password', 'System Administrator', 'admin@paradise.com', 'Super Admin', 'Active')");
}

$message = '';
$message_type = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_admin'])) {
        $username = $conn->real_escape_string($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $full_name = $conn->real_escape_string($_POST['full_name']);
        $email = $conn->real_escape_string($_POST['email']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $role = $conn->real_escape_string($_POST['role']);
        
        $admin_id = 'ADM' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        
        $sql = "INSERT INTO admins (admin_id, username, password, full_name, email, phone, role) 
                VALUES ('$admin_id', '$username', '$password', '$full_name', '$email', '$phone', '$role')";
        
        if ($conn->query($sql)) {
            $message = "✅ Admin added successfully! Admin ID: <strong>$admin_id</strong>";
            $message_type = "success";
        } else {
            $message = "❌ Error: " . $conn->error;
            $message_type = "error";
        }
    } elseif (isset($_POST['update_admin'])) {
        $id = (int)$_POST['admin_id'];
        $full_name = $conn->real_escape_string($_POST['full_name']);
        $email = $conn->real_escape_string($_POST['email']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $role = $conn->real_escape_string($_POST['role']);
        $status = $conn->real_escape_string($_POST['status']);
        
        $sql = "UPDATE admins SET full_name='$full_name', email='$email', phone='$phone', role='$role', status='$status' WHERE id=$id";
        
        if ($conn->query($sql)) {
            $message = "✅ Admin updated successfully!";
            $message_type = "success";
        } else {
            $message = "❌ Error: " . $conn->error;
            $message_type = "error";
        }
    } elseif (isset($_POST['change_password'])) {
        $id = (int)$_POST['admin_id'];
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        
        $sql = "UPDATE admins SET password='$new_password' WHERE id=$id";
        
        if ($conn->query($sql)) {
            $message = "✅ Password changed successfully!";
            $message_type = "success";
        } else {
            $message = "❌ Error: " . $conn->error;
            $message_type = "error";
        }
    }
}

// Handle delete
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($conn->query("DELETE FROM admins WHERE id=$id")) {
        $message = "✅ Admin deleted successfully!";
        $message_type = "success";
    }
}

// Get all admins
$admins = $conn->query("SELECT * FROM admins ORDER BY created_at DESC");
$total_admins = $admins->num_rows;
$active_admins = $conn->query("SELECT COUNT(*) as count FROM admins WHERE status='Active'")->fetch_assoc()['count'];
?>

<div class="pagetitle">
  <h1>Admin Management</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">Admin Management</li>
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

  <!-- Statistics -->
  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card stat-card">
        <div class="card-body text-center py-4">
          <h2 class="display-6 fw-bold text-muted"><?php echo $total_admins; ?></h2>
          <p class="mb-0 text-muted">Total Admins</p>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card stat-card">
        <div class="card-body text-center py-4">
          <h2 class="display-6 fw-bold text-success"><?php echo $active_admins; ?></h2>
          <p class="mb-0 text-muted">Active Admins</p>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
          <h6 class="mb-0"><i class="bi bi-people me-2"></i>Admin List</h6>
          <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
            <i class="bi bi-plus-circle"></i> Add Admin
          </button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Admin ID</th>
                  <th>Name</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php while($admin = $admins->fetch_assoc()): ?>
                <tr>
                  <td><strong><?php echo $admin['admin_id']; ?></strong></td>
                  <td><?php echo htmlspecialchars($admin['full_name']); ?></td>
                  <td><?php echo htmlspecialchars($admin['username']); ?></td>
                  <td><?php echo htmlspecialchars($admin['email']); ?></td>
                  <td><span class="badge bg-primary"><?php echo $admin['role']; ?></span></td>
                  <td>
                    <span class="badge bg-<?php echo $admin['status'] == 'Active' ? 'success' : 'secondary'; ?>">
                      <?php echo $admin['status']; ?>
                    </span>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="editAdmin(<?php echo htmlspecialchars(json_encode($admin)); ?>)">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="changePassword(<?php echo $admin['id']; ?>, '<?php echo htmlspecialchars($admin['full_name']); ?>')">
                      <i class="bi bi-key"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteAdmin(<?php echo $admin['id']; ?>, '<?php echo htmlspecialchars($admin['username']); ?>')">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-header bg-light">
          <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Admin Roles</h6>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <h6>Super Admin</h6>
            <p class="small text-muted">Full system access and control</p>
          </div>
          <div class="mb-3">
            <h6>Admin</h6>
            <p class="small text-muted">Manage bookings and agents</p>
          </div>
          <div class="mb-3">
            <h6>Manager</h6>
            <p class="small text-muted">View reports and statistics</p>
          </div>
          <div>
            <h6>Staff</h6>
            <p class="small text-muted">Basic booking operations</p>
          </div>
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-header bg-light">
          <h6 class="mb-0"><i class="bi bi-shield-check me-2"></i>Default Login</h6>
        </div>
        <div class="card-body">
          <p class="small"><strong>Username:</strong> admin</p>
          <p class="small"><strong>Password:</strong> admin123</p>
          <div class="alert alert-warning small mb-0">
            <i class="bi bi-exclamation-triangle"></i> Please change the default password after first login!
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Add Admin Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Admin</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <div class="modal-body">
          <input type="hidden" name="add_admin" value="1">
          <div class="mb-3">
            <label class="form-label">Full Name *</label>
            <input type="text" class="form-control" name="full_name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Username *</label>
            <input type="text" class="form-control" name="username" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password *</label>
            <input type="password" class="form-control" name="password" required minlength="6">
          </div>
          <div class="mb-3">
            <label class="form-label">Email *</label>
            <input type="email" class="form-control" name="email" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="tel" class="form-control" name="phone">
          </div>
          <div class="mb-3">
            <label class="form-label">Role *</label>
            <select class="form-select" name="role" required>
              <option value="Admin">Admin</option>
              <option value="Super Admin">Super Admin</option>
              <option value="Manager">Manager</option>
              <option value="Staff">Staff</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Admin</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Admin Modal -->
<div class="modal fade" id="editAdminModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Admin</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <div class="modal-body">
          <input type="hidden" name="update_admin" value="1">
          <input type="hidden" name="admin_id" id="edit_admin_id">
          <div class="mb-3">
            <label class="form-label">Full Name *</label>
            <input type="text" class="form-control" name="full_name" id="edit_full_name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email *</label>
            <input type="email" class="form-control" name="email" id="edit_email" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="tel" class="form-control" name="phone" id="edit_phone">
          </div>
          <div class="mb-3">
            <label class="form-label">Role *</label>
            <select class="form-select" name="role" id="edit_role" required>
              <option value="Admin">Admin</option>
              <option value="Super Admin">Super Admin</option>
              <option value="Manager">Manager</option>
              <option value="Staff">Staff</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Status *</label>
            <select class="form-select" name="status" id="edit_status" required>
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Admin</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Change Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <div class="modal-body">
          <input type="hidden" name="change_password" value="1">
          <input type="hidden" name="admin_id" id="pwd_admin_id">
          <p>Changing password for: <strong id="pwd_admin_name"></strong></p>
          <div class="mb-3">
            <label class="form-label">New Password *</label>
            <input type="password" class="form-control" name="new_password" required minlength="6">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning">Change Password</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function editAdmin(admin) {
    document.getElementById('edit_admin_id').value = admin.id;
    document.getElementById('edit_full_name').value = admin.full_name;
    document.getElementById('edit_email').value = admin.email;
    document.getElementById('edit_phone').value = admin.phone || '';
    document.getElementById('edit_role').value = admin.role;
    document.getElementById('edit_status').value = admin.status;
    new bootstrap.Modal(document.getElementById('editAdminModal')).show();
}

function changePassword(id, name) {
    document.getElementById('pwd_admin_id').value = id;
    document.getElementById('pwd_admin_name').textContent = name;
    new bootstrap.Modal(document.getElementById('changePasswordModal')).show();
}

function deleteAdmin(id, username) {
    if (confirm('Are you sure you want to delete admin: ' + username + '?')) {
        window.location.href = 'index.php?page=admin_management&delete=1&id=' + id;
    }
}
</script>

<style>
.stat-card {
  border: 1px solid #e0e0e0;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  transition: transform 0.3s;
}

.stat-card:hover {
  transform: translateY(-5px);
}

.btn-primary {
  background-color: #666;
  border-color: #666;
}

.btn-primary:hover {
  background-color: #555;
  border-color: #555;
}

.btn-outline-primary {
  color: #666;
  border-color: #666;
}

.btn-outline-primary:hover {
  background-color: #666;
  border-color: #666;
  color: white;
}
</style>

<?php $conn->close(); ?>
