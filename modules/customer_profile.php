<?php
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');

// Create preferences table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS customer_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_email VARCHAR(100) UNIQUE NOT NULL,
    default_pickup_location VARCHAR(200),
    default_insurance VARCHAR(50) DEFAULT 'none',
    favorite_cars TEXT,
    profile_completion INT DEFAULT 0,
    is_verified TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

$customer_username = $_SESSION['username'];
$customer_info = $conn->query("SELECT * FROM customers WHERE username='$customer_username'")->fetch_assoc();
$customer_email = $customer_info['email'];

// Get or create preferences
$prefs = $conn->query("SELECT * FROM customer_preferences WHERE customer_email='$customer_email'")->fetch_assoc();
if(!$prefs) {
    $conn->query("INSERT INTO customer_preferences (customer_email) VALUES ('$customer_email')");
    $prefs = $conn->query("SELECT * FROM customer_preferences WHERE customer_email='$customer_email'")->fetch_assoc();
}

// Update preferences
if(isset($_POST['save_preferences'])) {
    $pickup = $conn->real_escape_string($_POST['default_pickup_location']);
    $insurance = $conn->real_escape_string($_POST['default_insurance']);
    
    $conn->query("UPDATE customer_preferences SET default_pickup_location='$pickup', default_insurance='$insurance' WHERE customer_email='$customer_email'");
    $message = "✅ Preferences saved successfully!";
}

// Calculate profile completion
$completion = 0;
if(!empty($customer_info['full_name'])) $completion += 20;
if(!empty($customer_info['email'])) $completion += 20;
if(!empty($customer_info['phone'])) $completion += 20;

$docs = $conn->query("SELECT COUNT(*) as count FROM customer_documents WHERE customer_email='$customer_email'")->fetch_assoc();
if($docs['count'] >= 4) $completion += 20;

if(!empty($prefs['default_pickup_location'])) $completion += 10;
if(!empty($prefs['default_insurance'])) $completion += 10;

// Check if verified (all 4 documents uploaded)
$is_verified = $docs['count'] >= 4 ? 1 : 0;
$conn->query("UPDATE customer_preferences SET profile_completion=$completion, is_verified=$is_verified WHERE customer_email='$customer_email'");

$conn->close();
?>

<div class="pagetitle">
  <h1>👤 My Profile</h1>
</div>

<section class="section">
  <?php if(isset($message)): ?>
  <div class="alert alert-success"><?php echo $message; ?></div>
  <?php endif; ?>

  <div class="row">
    <!-- Profile Completion Card -->
    <div class="col-lg-4 mb-4">
      <div class="card">
        <div class="card-body text-center pt-4">
          <div class="position-relative d-inline-block mb-3">
            <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 48px; font-weight: bold;">
              <?php echo strtoupper(substr($customer_info['full_name'], 0, 1)); ?>
            </div>
            <?php if($is_verified): ?>
            <span class="position-absolute bottom-0 end-0 badge bg-success rounded-circle p-2" title="Verified Customer" style="width: 35px; height: 35px;">
              <i class="bi bi-check-lg"></i>
            </span>
            <?php endif; ?>
          </div>
          
          <h5><?php echo htmlspecialchars($customer_info['full_name']); ?></h5>
          <p class="text-muted"><?php echo htmlspecialchars($customer_info['email']); ?></p>
          
          <?php if($is_verified): ?>
          <span class="badge bg-success mb-3"><i class="bi bi-shield-check"></i> Verified Customer</span>
          <?php else: ?>
          <span class="badge bg-warning mb-3"><i class="bi bi-exclamation-circle"></i> Not Verified</span>
          <?php endif; ?>
          
          <div class="mb-2">
            <small class="text-muted">Profile Completion</small>
            <h4 class="mb-0" style="color: <?php echo $completion >= 80 ? '#10b981' : ($completion >= 50 ? '#f59e0b' : '#ef4444'); ?>;">
              <?php echo $completion; ?>%
            </h4>
          </div>
          
          <div class="progress" style="height: 10px;">
            <div class="progress-bar" role="progressbar" style="width: <?php echo $completion; ?>%; background-color: <?php echo $completion >= 80 ? '#10b981' : ($completion >= 50 ? '#f59e0b' : '#ef4444'); ?>;">
            </div>
          </div>
          
          <?php if($completion < 100): ?>
          <div class="alert alert-info mt-3 small text-start">
            <strong>Complete your profile:</strong>
            <ul class="mb-0 mt-2">
              <?php if(empty($customer_info['phone'])): ?>
              <li>Add phone number</li>
              <?php endif; ?>
              <?php if($docs['count'] < 4): ?>
              <li>Upload all documents (<?php echo $docs['count']; ?>/4)</li>
              <?php endif; ?>
              <?php if(empty($prefs['default_pickup_location'])): ?>
              <li>Set default pickup location</li>
              <?php endif; ?>
            </ul>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Booking Preferences -->
    <div class="col-lg-8">
      <div class="card mb-4">
        <div class="card-header" style="background-color: #0a2540; color: white;">
          <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Booking Preferences</h6>
        </div>
        <div class="card-body">
          <form method="POST">
            <div class="mb-3">
              <label class="form-label fw-bold">Default Pickup Location</label>
              <select class="form-select" name="default_pickup_location">
                <option value="">Select default location...</option>
                <option value="MNL - Manila Ninoy Aquino International Airport" <?php echo $prefs['default_pickup_location']=='MNL - Manila Ninoy Aquino International Airport'?'selected':''; ?>>MNL - Manila Airport</option>
                <option value="Makati City Center" <?php echo $prefs['default_pickup_location']=='Makati City Center'?'selected':''; ?>>Makati City Center</option>
                <option value="Bonifacio Global City (BGC)" <?php echo $prefs['default_pickup_location']=='Bonifacio Global City (BGC)'?'selected':''; ?>>BGC</option>
                <option value="Quezon City - Cubao" <?php echo $prefs['default_pickup_location']=='Quezon City - Cubao'?'selected':''; ?>>Quezon City - Cubao</option>
              </select>
              <small class="text-muted">This will be pre-selected when booking</small>
            </div>
            
            <div class="mb-3">
              <label class="form-label fw-bold">Preferred Insurance</label>
              <select class="form-select" name="default_insurance">
                <option value="none" <?php echo $prefs['default_insurance']=='none'?'selected':''; ?>>No Insurance</option>
                <option value="basic" <?php echo $prefs['default_insurance']=='basic'?'selected':''; ?>>Basic Insurance (₱300/day)</option>
                <option value="premium" <?php echo $prefs['default_insurance']=='premium'?'selected':''; ?>>Premium Insurance (₱600/day)</option>
                <option value="full" <?php echo $prefs['default_insurance']=='full'?'selected':''; ?>>Full Coverage (₱900/day)</option>
              </select>
              <small class="text-muted">This will be pre-selected when booking</small>
            </div>
            
            <button type="submit" name="save_preferences" class="btn btn-primary">
              <i class="bi bi-save me-2"></i>Save Preferences
            </button>
          </form>
        </div>
      </div>

      <!-- Account Information -->
      <div class="card">
        <div class="card-header" style="background-color: #f8fafc;">
          <h6 class="mb-0"><i class="bi bi-person-circle me-2"></i>Account Information</h6>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="text-muted small">Full Name</label>
              <p class="fw-bold mb-0"><?php echo htmlspecialchars($customer_info['full_name']); ?></p>
            </div>
            <div class="col-md-6 mb-3">
              <label class="text-muted small">Email</label>
              <p class="fw-bold mb-0"><?php echo htmlspecialchars($customer_info['email']); ?></p>
            </div>
            <div class="col-md-6 mb-3">
              <label class="text-muted small">Phone</label>
              <p class="fw-bold mb-0"><?php echo !empty($customer_info['phone']) ? htmlspecialchars($customer_info['phone']) : '<span class="text-danger">Not provided</span>'; ?></p>
            </div>
            <div class="col-md-6 mb-3">
              <label class="text-muted small">Member Since</label>
              <p class="fw-bold mb-0"><?php echo date('M d, Y', strtotime($customer_info['created_at'])); ?></p>
            </div>
          </div>
          <a href="?page=documents" class="btn btn-outline-primary">
            <i class="bi bi-file-earmark-text me-2"></i>Manage Documents
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
