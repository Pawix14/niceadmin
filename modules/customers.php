<?php
// modules/customers.php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle actions
$message = '';
$message_type = '';

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'delete' && isset($_GET['id'])) {
        $customer_id = $conn->real_escape_string($_GET['id']);
        $conn->query("UPDATE customers SET status = 'Inactive' WHERE customer_id = '$customer_id'");
        $message = "✅ Customer marked as inactive";
        $message_type = "success";
    }
}
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">👥 Customer Management</h5>
            
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <a href="index.php?page=customer_form" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add New Customer
                    </a>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary">Export</button>
                    <button type="button" class="btn btn-outline-secondary">Print</button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Customer ID</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Country</th>
                            <th>Loyalty Points</th>
                            <th>Bookings</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("
                            SELECT c.*, 
                            (SELECT COUNT(*) FROM travel_bookings WHERE customer_id = c.customer_id) + 
                            (SELECT COUNT(*) FROM hotel_bookings WHERE customer_id = c.customer_id) + 
                            (SELECT COUNT(*) FROM tour_bookings WHERE customer_id = c.customer_id) + 
                            (SELECT COUNT(*) FROM flight_bookings WHERE customer_id = c.customer_id) as total_bookings
                            FROM customers c 
                            ORDER BY registration_date DESC
                        ");
                        
                        while($customer = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo $customer['customer_id']; ?></strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                                        <?php echo strtoupper(substr($customer['full_name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($customer['full_name']); ?></h6>
                                        <small class="text-muted"><?php echo $customer['email']; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo $customer['phone']; ?></td>
                            <td><?php echo $customer['country']; ?></td>
                            <td>
                                <span class="badge bg-warning"><?php echo $customer['loyalty_points']; ?> points</span>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo $customer['total_bookings']; ?> bookings</span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $customer['status'] == 'Active' ? 'success' : 'secondary'; ?>">
                                    <?php echo $customer['status']; ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=customer_view&id=<?php echo $customer['customer_id']; ?>" class="btn btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="index.php?page=customer_form&id=<?php echo $customer['customer_id']; ?>" class="btn btn-outline-success" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="index.php?page=customers&action=delete&id=<?php echo $customer['customer_id']; ?>" 
                                       class="btn btn-outline-danger" title="Delete"
                                       onclick="return confirm('Are you sure you want to mark this customer as inactive?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Customer Stats -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h3 class="text-primary">
                                <?php 
                                $result = $conn->query("SELECT COUNT(*) as total FROM customers WHERE status = 'Active'");
                                echo $result->fetch_assoc()['total'];
                                ?>
                            </h3>
                            <p class="mb-0">Active Customers</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h3 class="text-success">
                                <?php 
                                $result = $conn->query("SELECT SUM(loyalty_points) as total FROM customers");
                                echo number_format($result->fetch_assoc()['total']);
                                ?>
                            </h3>
                            <p class="mb-0">Total Loyalty Points</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h3 class="text-warning">
                                <?php 
                                $result = $conn->query("SELECT COUNT(DISTINCT country) as total FROM customers WHERE country IS NOT NULL");
                                echo $result->fetch_assoc()['total'];
                                ?>
                            </h3>
                            <p class="mb-0">Countries</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h3 class="text-info">
                                <?php 
                                $result = $conn->query("SELECT COUNT(*) as total FROM customers WHERE loyalty_points > 1000");
                                echo $result->fetch_assoc()['total'];
                                ?>
                            </h3>
                            <p class="mb-0">VIP Customers</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $conn->close(); ?>