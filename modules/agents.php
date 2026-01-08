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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_agent'])) {
    $agent_name = $conn->real_escape_string($_POST['agent_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $agency_name = $conn->real_escape_string($_POST['agency_name']);
    $specialization = $conn->real_escape_string($_POST['specialization']);
    $commission_rate = (float)$_POST['commission_rate'];
    
    $agent_id = 'AGT' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    $sql = "INSERT INTO travel_agents (agent_id, agent_name, email, phone, agency_name, specialization, commission_rate) 
            VALUES ('$agent_id', '$agent_name', '$email', '$phone', '$agency_name', '$specialization', $commission_rate)";
    
    if ($conn->query($sql)) {
        $message = "✅ Travel agent added successfully! Agent ID: <strong>$agent_id</strong>";
        $message_type = "success";
    } else {
        $message = "❌ Error adding agent: " . $conn->error;
        $message_type = "error";
    }
}

$agents = [];
$result = $conn->query("SELECT * FROM travel_agents ORDER BY agent_name");

while($row = $result->fetch_assoc()) {
    $agents[] = $row;
}

$agent_stats = [];
foreach($agents as $agent) {
    $result = $conn->query("SELECT COUNT(*) as bookings FROM travel_bookings WHERE agent_id = '{$agent['agent_id']}'");
    $travel_bookings = $result->fetch_assoc()['bookings'];
    
    $result = $conn->query("SELECT COUNT(*) as bookings FROM hotel_bookings WHERE agent_id = '{$agent['agent_id']}'");
    $hotel_bookings = $result->fetch_assoc()['bookings'];
    
    $result = $conn->query("SELECT COUNT(*) as bookings FROM tour_bookings WHERE agent_id = '{$agent['agent_id']}'");
    $tour_bookings = $result->fetch_assoc()['bookings'];
    
    $result = $conn->query("SELECT COUNT(*) as bookings FROM flight_bookings WHERE agent_id = '{$agent['agent_id']}'");
    $flight_bookings = $result->fetch_assoc()['bookings'];
    
    $total_bookings = $travel_bookings + $hotel_bookings + $tour_bookings + $flight_bookings;
    
    $result = $conn->query("SELECT COALESCE(SUM(agent_commission), 0) as commission FROM travel_bookings WHERE agent_id = '{$agent['agent_id']}'");
    $travel_commission = $result->fetch_assoc()['commission'];
    
    $result = $conn->query("SELECT COALESCE(SUM(agent_commission), 0) as commission FROM hotel_bookings WHERE agent_id = '{$agent['agent_id']}'");
    $hotel_commission = $result->fetch_assoc()['commission'];
    
    $result = $conn->query("SELECT COALESCE(SUM(agent_commission), 0) as commission FROM flight_bookings WHERE agent_id = '{$agent['agent_id']}'");
    $flight_commission = $result->fetch_assoc()['commission'];
    
    $total_commission = $travel_commission + $hotel_commission + $flight_commission;
    
    $agent_stats[$agent['agent_id']] = [
        'total_bookings' => $total_bookings,
        'total_commission' => $total_commission
    ];
}

$conn->close();
?>

<div class="pagetitle">
  <h1>Travel Agents</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">Travel Agents</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Add New Agent</h5>
          
          <?php if ($message): ?>
          <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          <?php endif; ?>
          
          <form method="POST" action="">
            <input type="hidden" name="add_agent" value="1">
            
            <div class="mb-3">
              <label for="agent_name" class="form-label">Agent Name *</label>
              <input type="text" class="form-control" id="agent_name" name="agent_name" required>
            </div>
            
            <div class="mb-3">
              <label for="email" class="form-label">Email *</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <div class="mb-3">
              <label for="phone" class="form-label">Phone *</label>
              <input type="tel" class="form-control" id="phone" name="phone" required>
            </div>
            
            <div class="mb-3">
              <label for="agency_name" class="form-label">Agency Name</label>
              <input type="text" class="form-control" id="agency_name" name="agency_name">
            </div>
            
            <div class="mb-3">
              <label for="specialization" class="form-label">Specialization</label>
              <select class="form-select" id="specialization" name="specialization">
                <option value="All">All Services</option>
                <option value="Flights">Flights</option>
                <option value="Hotels">Hotels</option>
                <option value="Tours">Tours</option>
                <option value="Travel">Travel</option>
              </select>
            </div>
            
            <div class="mb-3">
              <label for="commission_rate" class="form-label">Commission Rate (%) *</label>
              <input type="number" class="form-control" id="commission_rate" name="commission_rate" step="0.01" min="0" max="50" value="10.00" required>
            </div>
            
            <div class="text-center">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Add Agent
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">All Agents</h5>
          
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Agent ID</th>
                  <th>Agent Details</th>
                  <th>Specialization</th>
                  <th>Commission</th>
                  <th>Performance</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($agents as $agent): 
                  $stats = $agent_stats[$agent['agent_id']] ?? ['total_bookings' => 0, 'total_commission' => 0];
                ?>
                <tr>
                  <td><strong><?php echo $agent['agent_id']; ?></strong></td>
                  <td>
                    <div>
                      <h6 class="mb-0"><?php echo htmlspecialchars($agent['agent_name']); ?></h6>
                      <small class="text-muted"><?php echo $agent['email']; ?></small><br>
                      <small class="text-muted"><?php echo $agent['phone']; ?></small>
                      <?php if($agent['agency_name']): ?>
                      <br><small><i class="bi bi-building"></i> <?php echo $agent['agency_name']; ?></small>
                      <?php endif; ?>
                    </div>
                  </td>
                  <td>
                    <?php
                    $specialization_badge = '';
                    switch($agent['specialization']) {
                      case 'Flights': $specialization_badge = 'info'; break;
                      case 'Hotels': $specialization_badge = 'success'; break;
                      case 'Tours': $specialization_badge = 'warning'; break;
                      case 'Travel': $specialization_badge = 'danger'; break;
                      default: $specialization_badge = 'primary';
                    }
                    ?>
                    <span class="badge bg-<?php echo $specialization_badge; ?>">
                      <?php echo $agent['specialization']; ?>
                    </span>
                  </td>
                  <td>
                    <div><strong><?php echo $agent['commission_rate']; ?>%</strong></div>
                    <small class="text-muted">Rate</small>
                  </td>
                  <td>
                    <div class="mb-1">
                      <small><strong><?php echo $stats['total_bookings']; ?></strong> bookings</small>
                    </div>
                    <div>
                      <small class="text-success">₱<?php echo number_format($stats['total_commission'], 2); ?> earned</small>
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-<?php echo $agent['status'] == 'Active' ? 'success' : 'secondary'; ?>">
                      <?php echo $agent['status']; ?>
                    </span>
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