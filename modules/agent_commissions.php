<?php
// modules/agent_commissions.php

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle commission status update
if (isset($_POST['update_status'])) {
    $commission_id = $conn->real_escape_string($_POST['commission_id']);
    $new_status = $conn->real_escape_string($_POST['status']);
    
    $sql = "UPDATE agent_commissions SET status = '$new_status' WHERE commission_id = '$commission_id'";
    
    if ($conn->query($sql)) {
        $message = "✅ Commission status updated successfully!";
        $message_type = "success";
    } else {
        $message = "❌ Error updating commission: " . $conn->error;
        $message_type = "error";
    }
}
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">💰 Agent Commissions</h5>
            
            <?php if (isset($message)): ?>
            <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <!-- Commission Summary -->
            <div class="row mb-4">
                <?php
                $summary_result = $conn->query("
                    SELECT 
                        COUNT(*) as total_commissions,
                        SUM(commission_amount) as total_amount,
                        SUM(CASE WHEN status = 'Paid' THEN commission_amount ELSE 0 END) as paid_amount,
                        SUM(CASE WHEN status = 'Pending' THEN commission_amount ELSE 0 END) as pending_amount
                    FROM agent_commissions
                ");
                $summary = $summary_result->fetch_assoc();
                ?>
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h4 class="card-title">₱<?php echo number_format($summary['total_amount'] ?? 0, 2); ?></h4>
                            <p class="card-text">Total Commissions</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h4 class="card-title">₱<?php echo number_format($summary['paid_amount'] ?? 0, 2); ?></h4>
                            <p class="card-text">Paid Commissions</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h4 class="card-title">₱<?php echo number_format($summary['pending_amount'] ?? 0, 2); ?></h4>
                            <p class="card-text">Pending Commissions</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h4 class="card-title"><?php echo $summary['total_commissions'] ?? 0; ?></h4>
                            <p class="card-text">Total Records</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Commissions Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Commission ID</th>
                            <th>Agent</th>
                            <th>Booking ID</th>
                            <th>Booking Type</th>
                            <th>Booking Amount</th>
                            <th>Commission Rate</th>
                            <th>Commission Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $commissions_result = $conn->query("
                            SELECT ac.*, ta.agent_name 
                            FROM agent_commissions ac 
                            LEFT JOIN travel_agents ta ON ac.agent_id = ta.agent_id 
                            ORDER BY ac.commission_date DESC LIMIT 20
                        ");
                        while($commission = $commissions_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $commission['commission_id']; ?></td>
                            <td><?php echo htmlspecialchars($commission['agent_name']); ?></td>
                            <td><?php echo $commission['booking_id']; ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                switch($commission['booking_type']) {
                                    case 'Travel': echo 'primary'; break;
                                    case 'Hotel': echo 'success'; break;
                                    case 'Tour': echo 'warning'; break;
                                    case 'Flight': echo 'info'; break;
                                }
                                ?>"><?php echo $commission['booking_type']; ?></span>
                            </td>
                            <td>₱<?php echo number_format($commission['booking_amount'], 2); ?></td>
                            <td><?php echo $commission['commission_rate']; ?>%</td>
                            <td><strong>₱<?php echo number_format($commission['commission_amount'], 2); ?></strong></td>
                            <td>
                                <span class="badge bg-<?php 
                                switch($commission['status']) {
                                    case 'Paid': echo 'success'; break;
                                    case 'Pending': echo 'warning'; break;
                                    case 'Cancelled': echo 'danger'; break;
                                    default: echo 'secondary';
                                }
                                ?>"><?php echo $commission['status']; ?></span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($commission['commission_date'])); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="commission_id" value="<?php echo $commission['commission_id']; ?>">
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="Pending" <?php echo $commission['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Paid" <?php echo $commission['status'] == 'Paid' ? 'selected' : ''; ?>>Paid</option>
                                        <option value="Cancelled" <?php echo $commission['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Top Agents -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">🏆 Top Performing Agents</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Agent</th>
                                    <th>Total Bookings</th>
                                    <th>Total Commission</th>
                                    <th>Pending Commission</th>
                                    <th>Paid Commission</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $top_agents_result = $conn->query("
                                    SELECT 
                                        ta.agent_name,
                                        ta.total_bookings,
                                        SUM(ac.commission_amount) as total_commission,
                                        SUM(CASE WHEN ac.status = 'Pending' THEN ac.commission_amount ELSE 0 END) as pending_commission,
                                        SUM(CASE WHEN ac.status = 'Paid' THEN ac.commission_amount ELSE 0 END) as paid_commission
                                    FROM travel_agents ta
                                    LEFT JOIN agent_commissions ac ON ta.agent_id = ac.agent_id
                                    GROUP BY ta.agent_id
                                    ORDER BY total_commission DESC
                                    LIMIT 5
                                ");
                                while($agent = $top_agents_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($agent['agent_name']); ?></td>
                                    <td><?php echo $agent['total_bookings']; ?></td>
                                    <td><strong>₱<?php echo number_format($agent['total_commission'] ?? 0, 2); ?></strong></td>
                                    <td>₱<?php echo number_format($agent['pending_commission'] ?? 0, 2); ?></td>
                                    <td>₱<?php echo number_format($agent['paid_commission'] ?? 0, 2); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $conn->close(); ?>