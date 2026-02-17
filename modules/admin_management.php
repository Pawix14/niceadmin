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
        </div>
        <div class="card-body">
          <div class="mb-3">
            <button class="btn btn-primary" onclick="document.getElementById('addAdminForm').style.display='block'"><i class="bi bi-plus-circle"></i> Add Admin</button>
          </div>
          
          <div id="addAdminForm" style="display:none;" class="mb-4 p-3 border rounded bg-light">
            <h6>Add New Admin</h6>
            <form method="POST">
              <input type="hidden" name="add_admin" value="1">
              <div class="row g-2">
                <div class="col-md-6">
                  <input type="text" class="form-control" name="full_name" placeholder="Full Name" required>
                </div>
                <div class="col-md-6">
                  <input type="text" class="form-control" name="username" placeholder="Username" required>
                </div>
                <div class="col-md-6">
                  <input type="password" class="form-control" name="password" placeholder="Password" required minlength="6">
                </div>
                <div class="col-md-6">
                  <input type="email" class="form-control" name="email" placeholder="Email" required>
                </div>
                <div class="col-md-6">
                  <input type="tel" class="form-control" name="phone" placeholder="Phone">
                </div>
                <div class="col-md-6">
                  <select class="form-select" name="role" required>
                    <option value="">Select Role</option>
                    <option value="Admin">Admin</option>
                    <option value="Super Admin">Super Admin</option>
                    <option value="Manager">Manager</option>
                    <option value="Staff">Staff</option>
                  </select>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-success">Add Admin</button>
                  <button type="button" class="btn btn-secondary" onclick="document.getElementById('addAdminForm').style.display='none'">Cancel</button>
                </div>
              </div>
            </form>
          </div>
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

<script>
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

.modal-backdrop {
  z-index: 1040 !important;
}

.modal {
  z-index: 1050 !important;
}

.modal-dialog {
  z-index: 1060 !important;
}
</style>

<?php $conn->close(); ?>
