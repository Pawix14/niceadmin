<?php
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');
$message = '';

if(isset($_POST['add_customer'])) {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $conn->query("INSERT INTO customers (full_name, email, phone, username, password, status) VALUES ('$full_name', '$email', '$phone', '$username', '$password', 'Active')");
    $message = '✅ Customer added successfully!';
}

if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM customers WHERE id=$id");
    $message = '✅ Customer deleted!';
}

$customers = $conn->query("SELECT * FROM customers ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<div class="pagetitle">
  <h1>👥 Customer Management</h1>
</div>

<section class="section">
  <?php if($message): ?>
  <div class="alert alert-success"><?php echo $message; ?></div>
  <?php endif; ?>

  <div class="card mb-4">
    <div class="card-header" style="background: #0a2540; color: white;">
      <h6 class="mb-0">Add New Customer</h6>
    </div>
    <div class="card-body">
      <form method="POST" class="row g-3">
        <div class="col-md-3">
          <input type="text" class="form-control" name="full_name" placeholder="Full Name" required>
        </div>
        <div class="col-md-3">
          <input type="email" class="form-control" name="email" placeholder="Email" required>
        </div>
        <div class="col-md-2">
          <input type="text" class="form-control" name="phone" placeholder="Phone" required>
        </div>
        <div class="col-md-2">
          <input type="text" class="form-control" name="username" placeholder="Username" required>
        </div>
        <div class="col-md-2">
          <button type="submit" name="add_customer" class="btn btn-primary w-100">Add</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <table class="table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Username</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($customers as $c): ?>
          <tr>
            <td><?php echo htmlspecialchars($c['full_name']); ?></td>
            <td><?php echo htmlspecialchars($c['email']); ?></td>
            <td><?php echo htmlspecialchars($c['phone']); ?></td>
            <td><?php echo htmlspecialchars($c['username']); ?></td>
            <td><span class="badge bg-<?php echo $c['status']=='Active'?'success':'secondary'; ?>"><?php echo $c['status']; ?></span></td>
            <td>
              <a href="?page=customers&delete=<?php echo $c['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>
