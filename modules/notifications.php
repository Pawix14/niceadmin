<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Mark notification as read
if (isset($_GET['mark_read'])) {
    $id = intval($_GET['mark_read']);
    $conn->query("UPDATE notifications SET is_read=1 WHERE id=$id");
    header("Location: index.php?page=notifications");
    exit();
}

// Handle customer message to admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $message_text = $conn->real_escape_string($_POST['message']);
    $customer_name = $conn->real_escape_string($_POST['customer_name']);
    $customer_email = $conn->real_escape_string($_POST['customer_email']);
    
    $title = "Message from Customer: $customer_name";
    $conn->query("INSERT INTO notifications (user_type, title, message, is_read) VALUES ('admin', '$title', '$message_text - From: $customer_email', 0)");
    
    $success_message = "✅ Message sent to admin successfully!";
}

// Handle admin message to customer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_admin_message'])) {
    $message_text = $conn->real_escape_string($_POST['message']);
    $customer_email = $conn->real_escape_string($_POST['customer_email']);
    
    $title = "Message from Admin";
    $conn->query("INSERT INTO notifications (user_type, user_id, title, message, is_read) VALUES ('customer', '$customer_email', '$title', '$message_text', 0)");
    
    $success_message = "✅ Message sent to customer successfully!";
}

// Get user type and email
$is_admin = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
$is_staff = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'staff';

// For customers, get email from customers table
$user_email = '';
if (!$is_admin && !$is_staff && isset($_SESSION['username'])) {
    $username = $conn->real_escape_string($_SESSION['username']);
    $customer_result = $conn->query("SELECT email FROM customers WHERE username='$username'");
    if ($customer_result && $customer_row = $customer_result->fetch_assoc()) {
        $user_email = $customer_row['email'];
    }
}

// Get notifications based on user type
$notifications = [];
if ($is_admin) {
    $result = $conn->query("SELECT * FROM notifications WHERE user_type='admin' ORDER BY created_at DESC");
} elseif ($is_staff) {
    $result = $conn->query("SELECT * FROM notifications WHERE user_type='staff' ORDER BY created_at DESC");
} else {
    $result = $conn->query("SELECT * FROM notifications WHERE user_type='customer' AND user_id='$user_email' ORDER BY created_at DESC");
}
while($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

$unread_count = count(array_filter($notifications, function($n) { return !$n['is_read']; }));

$conn->close();
?>

<div class="pagetitle">
  <h1>🔔 Notifications</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">Notifications</li>
    </ol>
  </nav>
</div>

<section class="section">
  <?php if (isset($success_message)): ?>
  <div class="alert alert-success alert-dismissible fade show">
    <?php echo $success_message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>
  
  <?php if (!$is_admin): ?>
  <div class="card mb-4">
    <div class="card-header" style="background-color: #666; color: white;">
      <h6 class="mb-0">💬 Send Message to Admin</h6>
    </div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Customer'); ?>">
        <input type="hidden" name="customer_email" value="<?php echo htmlspecialchars($user_email); ?>">
        <div class="mb-3">
          <label class="form-label">Your Message</label>
          <textarea class="form-control" name="message" rows="4" placeholder="Type your question or message here..." required></textarea>
        </div>
        <button type="submit" name="send_message" class="btn" style="background-color: #666; color: white;">
          <i class="bi bi-send"></i> Send Message
        </button>
      </form>
    </div>
  </div>
  <?php else: ?>
  <div class="card mb-4">
    <div class="card-header" style="background-color: #666; color: white;">
      <h6 class="mb-0">📧 Send Message to Customer</h6>
    </div>
    <div class="card-body">
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Select Customer</label>
          <select class="form-select" name="customer_email" required>
            <option value="">Choose customer...</option>
            <?php
            $conn_temp = new mysqli('localhost', 'root', '', 'travel_db_improved');
            $customers = $conn_temp->query("SELECT DISTINCT email, full_name FROM customers ORDER BY full_name");
            while($customer = $customers->fetch_assoc()) {
                echo '<option value="'.htmlspecialchars($customer['email']).'">'.htmlspecialchars($customer['full_name']).' ('.htmlspecialchars($customer['email']).')</option>';
            }
            $conn_temp->close();
            ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Message</label>
          <textarea class="form-control" name="message" rows="4" placeholder="Type your message here..." required></textarea>
        </div>
        <button type="submit" name="send_admin_message" class="btn" style="background-color: #666; color: white;">
          <i class="bi bi-send"></i> Send Message
        </button>
      </form>
    </div>
  </div>
  <?php endif; ?>
  
  <div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
      <h6 class="mb-0">📬 All Notifications (<?php echo $unread_count; ?> unread)</h6>
    </div>
    <div class="card-body">
      <?php if (empty($notifications)): ?>
      <div class="text-center py-5">
        <i class="bi bi-bell-slash display-1 text-muted"></i>
        <p class="mt-3 text-muted">No notifications yet</p>
      </div>
      <?php else: ?>
      <div class="list-group">
        <?php foreach($notifications as $notif): ?>
        <div class="list-group-item <?php echo !$notif['is_read'] ? 'list-group-item-warning' : ''; ?>">
          <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
              <h6 class="mb-1">
                <?php if (!$notif['is_read']): ?>
                <span class="badge bg-danger me-2">NEW</span>
                <?php endif; ?>
                <?php echo htmlspecialchars($notif['title']); ?>
              </h6>
              <p class="mb-1"><?php echo htmlspecialchars($notif['message']); ?></p>
              <?php if ($notif['booking_id'] && $is_admin): ?>
              <a href="index.php?page=booking_details&id=<?php echo $notif['booking_id']; ?>" class="btn btn-sm btn-primary mt-2">
                <i class="bi bi-eye"></i> View Booking
              </a>
              <?php elseif ($notif['booking_id']): ?>
              <a href="index.php?page=my_bookings" class="btn btn-sm btn-primary mt-2">
                <i class="bi bi-eye"></i> View My Bookings
              </a>
              <?php endif; ?>
              <small class="text-muted d-block mt-2">
                <i class="bi bi-clock"></i> <?php echo date('M d, Y h:i A', strtotime($notif['created_at'])); ?>
              </small>
            </div>
            <?php if (!$notif['is_read']): ?>
            <a href="?page=notifications&mark_read=<?php echo $notif['id']; ?>" class="btn btn-sm btn-outline-secondary">
              <i class="bi bi-check"></i> Mark Read
            </a>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>
