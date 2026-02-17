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

// Get customer info
$customer_username = $_SESSION['username'];
$customer_info = $conn->query("SELECT * FROM customers WHERE username='$customer_username'")->fetch_assoc();

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $_FILES['profile_picture']['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (in_array($ext, $allowed)) {
        $upload_dir = 'uploads/profiles/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $new_filename = 'profile_' . $customer_info['customer_id'] . '_' . time() . '.' . $ext;
        $upload_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
            // Delete old profile picture if exists
            if ($customer_info['profile_picture'] && file_exists($customer_info['profile_picture'])) {
                unlink($customer_info['profile_picture']);
            }
            
            $conn->query("UPDATE customers SET profile_picture='$upload_path' WHERE username='$customer_username'");
            $message = '✅ Profile picture updated successfully!';
            $message_type = 'success';
            $customer_info = $conn->query("SELECT * FROM customers WHERE username='$customer_username'")->fetch_assoc();
        } else {
            $message = '❌ Error uploading file';
            $message_type = 'error';
        }
    } else {
        $message = '❌ Invalid file type. Only JPG, PNG, and GIF allowed';
        $message_type = 'error';
    }
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);
    
    $sql = "UPDATE customers SET full_name='$full_name', email='$email', phone='$phone', address='$address' WHERE username='$customer_username'";
    
    if ($conn->query($sql)) {
        $_SESSION['user_name'] = $full_name;
        $message = '✅ Profile updated successfully!';
        $message_type = 'success';
        $customer_info = $conn->query("SELECT * FROM customers WHERE username='$customer_username'")->fetch_assoc();
    } else {
        $message = '❌ Error updating profile';
        $message_type = 'error';
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (password_verify($current_password, $customer_info['password'])) {
        if ($new_password === $confirm_password) {
            if (strlen($new_password) >= 6) {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $conn->query("UPDATE customers SET password='$hashed' WHERE username='$customer_username'");
                $message = '✅ Password changed successfully!';
                $message_type = 'success';
            } else {
                $message = '❌ Password must be at least 6 characters';
                $message_type = 'error';
            }
        } else {
            $message = '❌ New passwords do not match';
            $message_type = 'error';
        }
    } else {
        $message = '❌ Current password is incorrect';
        $message_type = 'error';
    }
}

$conn->close();
?>

<div class="pagetitle">
  <h1>My Profile</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">My Profile</li>
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
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header bg-light">
          <h6 class="mb-0"><i class="bi bi-person-circle me-2"></i>Profile Information</h6>
        </div>
        <div class="card-body">
          <form method="POST">
            <input type="hidden" name="update_profile" value="1">
            
            <div class="mb-3">
              <label class="form-label">Full Name *</label>
              <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($customer_info['full_name']); ?>" required>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Email *</label>
              <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($customer_info['email']); ?>" required>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Phone</label>
              <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($customer_info['phone'] ?: ''); ?>">
            </div>
            
            <div class="mb-3">
              <label class="form-label">Address</label>
              <textarea class="form-control" name="address" rows="3"><?php echo htmlspecialchars($customer_info['address'] ?: ''); ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-check-circle me-2"></i>Update Profile
            </button>
          </form>
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-header bg-light">
          <h6 class="mb-0"><i class="bi bi-key me-2"></i>Change Password</h6>
        </div>
        <div class="card-body">
          <form method="POST">
            <input type="hidden" name="change_password" value="1">
            
            <div class="mb-3">
              <label class="form-label">Current Password *</label>
              <input type="password" class="form-control" name="current_password" required>
            </div>
            
            <div class="mb-3">
              <label class="form-label">New Password *</label>
              <input type="password" class="form-control" name="new_password" required minlength="6">
              <small class="text-muted">Minimum 6 characters</small>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Confirm New Password *</label>
              <input type="password" class="form-control" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn btn-warning">
              <i class="bi bi-shield-lock me-2"></i>Change Password
            </button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-header bg-light">
          <h6 class="mb-0"><i class="bi bi-camera me-2"></i>Profile Picture</h6>
        </div>
        <div class="card-body text-center">
          <div class="mb-3">
            <div style="width: 150px; height: 150px; margin: 0 auto; border-radius: 50%; overflow: hidden; border: 3px solid #e0e0e0; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
              <?php if ($customer_info['profile_picture'] && file_exists($customer_info['profile_picture'])): ?>
                <img src="<?php echo htmlspecialchars($customer_info['profile_picture']); ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
              <?php else: ?>
                <i class="bi bi-person-circle" style="font-size: 100px; color: #ccc;"></i>
              <?php endif; ?>
            </div>
          </div>
          <form method="POST" enctype="multipart/form-data">
            <input type="file" class="form-control mb-2" name="profile_picture" accept="image/*" required>
            <button type="submit" class="btn btn-sm btn-secondary w-100">
              <i class="bi bi-upload me-1"></i>Upload Picture
            </button>
          </form>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-header bg-light">
          <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Account Details</h6>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <small class="text-muted">Customer ID</small>
            <p class="mb-0"><span class="badge bg-secondary"><?php echo $customer_info['customer_id']; ?></span></p>
          </div>
          <div class="mb-3">
            <small class="text-muted">Username</small>
            <p class="mb-0 fw-bold"><?php echo htmlspecialchars($customer_info['username']); ?></p>
          </div>
          <div class="mb-3">
            <small class="text-muted">Account Status</small>
            <p class="mb-0"><span class="badge bg-success"><?php echo $customer_info['status']; ?></span></p>
          </div>
          <div class="mb-3">
            <small class="text-muted">Member Since</small>
            <p class="mb-0"><?php echo date('F d, Y', strtotime($customer_info['created_at'])); ?></p>
          </div>
          <div>
            <small class="text-muted">Last Login</small>
            <p class="mb-0"><?php echo $customer_info['last_login'] ? date('M d, Y h:i A', strtotime($customer_info['last_login'])) : 'N/A'; ?></p>
          </div>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-header bg-light">
          <h6 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Quick Upload Documents</h6>
        </div>
        <div class="card-body">
          <p class="small text-muted mb-3">Upload your verification documents</p>
          <a href="index.php?page=documents" class="btn btn-sm btn-primary w-100">
            <i class="bi bi-upload me-2"></i>Upload License/ID
          </a>
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
