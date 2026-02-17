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

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'delete' && isset($_GET['type']) && isset($_GET['id'])) {
        $type = $_GET['type'];
        $id = $conn->real_escape_string($_GET['id']);
        
        switch($type) {
            case 'travel':
                $conn->query("DELETE FROM travel_bookings WHERE booking_id = '$id'");
                break;
            case 'car':
                // Delete related records first (in order)
                $conn->query("DELETE FROM car_reviews WHERE booking_id = '$id'");
                $conn->query("DELETE FROM customer_documents WHERE booking_id = '$id'");
                // Then delete the booking
                $conn->query("DELETE FROM car_rentals WHERE booking_id = '$id'");
                break;
        }
        
        $message = "✅ Booking deleted successfully";
        $message_type = "success";
    } elseif ($_GET['action'] == 'update_status' && isset($_GET['type']) && isset($_GET['id']) && isset($_GET['status'])) {
        $type = $_GET['type'];
        $id = $conn->real_escape_string($_GET['id']);
        $status = $conn->real_escape_string($_GET['status']);
        
        switch($type) {
            case 'travel':
                $conn->query("UPDATE travel_bookings SET status = '$status' WHERE booking_id = '$id'");
                break;
            case 'car':
                $conn->query("UPDATE car_rentals SET status = '$status' WHERE booking_id = '$id'");
                break;
        }
        
        // No additional processing needed for remaining booking types
        
        $message = "✅ Booking status updated";
        $message_type = "success";
    }
}

// Get all bookings for main table
$all_bookings = [];

// Car Rental Bookings only
$car_table_exists = $conn->query("SHOW TABLES LIKE 'car_rentals'");
if ($car_table_exists && $car_table_exists->num_rows > 0) {
    $result = $conn->query("
        SELECT 'car' as type, booking_id as id, customer_name, 'Car Rental' as booking_type, 
               CONCAT('Car: ', car_model, ' (', car_type, ')') as details, 
               COALESCE(total_amount, 0) as amount, 
               COALESCE(agent_commission, 0) as commission,
               status, created_at as booking_date, car_model, car_type
        FROM car_rentals 
        ORDER BY created_at DESC
    ");
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $all_bookings[] = $row;
        }
    }
}

usort($all_bookings, function($a, $b) {
    return strtotime($b['booking_date']) - strtotime($a['booking_date']);
});

// Get RECENT Car Rentals (last 5)

// Calculate statistics
$total_bookings = count($all_bookings);
$total_revenue = array_sum(array_column($all_bookings, 'amount'));
$total_commission = array_sum(array_column($all_bookings, 'commission'));
$active_bookings = count(array_filter($all_bookings, function($b) { 
    return in_array($b['status'], ['Confirmed', 'Available', 'Active', 'Checked-in', 'Boarded']); 
}));

$conn->close();
?>

<div class="pagetitle">
  <h1>All Bookings</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">All Bookings</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    
    <div class="col-lg-12">
      <div class="row mb-4">
        <div class="col-md-4">
          <div class="card stat-card">
            <div class="card-body text-center py-4">
              <h2 class="display-6 fw-bold text-muted"><?php echo $total_bookings; ?></h2>
              <p class="mb-0 text-muted">Total Bookings</p>
            </div>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="card stat-card">
            <div class="card-body text-center py-4">
              <h2 class="display-6 fw-bold text-muted">₱<?php echo number_format($total_commission, 2); ?></h2>
              <p class="mb-0 text-muted">Total Commission</p>
            </div>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="card stat-card">
            <div class="card-body text-center py-4">
              <h2 class="display-6 fw-bold text-muted"><?php echo $active_bookings; ?></h2>
              <p class="mb-0 text-muted">Active Bookings</p>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          
          <?php if ($message): ?>
          <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          <?php endif; ?>
          
          <!-- Action Buttons -->
          <div class="row mb-4">
            <div class="col-md-12">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Car Rental Bookings</h5>
                <div class="btn-group">
                  <a href="index.php?page=car_rental" class="btn btn-info">
                    <i class="bi bi-car-front"></i> Rent a Car
                  </a>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Filters -->
          <div class="row mb-4">
            <div class="col-md-3">
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-filter"></i></span>
                <select class="form-select" id="filterType">
                  <option value="">All Types</option>
                  <option value="Car Rental" selected>Car Rental</option>
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                <select class="form-select" id="filterStatus">
                  <option value="">All Status</option>
                  <option value="Pending">Pending</option>
                  <option value="Confirmed">Confirmed</option>
                  <option value="Completed">Completed</option>
                  <option value="Cancelled">Cancelled</option>
                  <option value="Active">Active</option>
                  <option value="Returned">Returned</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="searchInput" placeholder="Search by booking ID, customer name, or details...">
                <button class="btn btn-outline-secondary" type="button" onclick="searchBookings()">
                  Search
                </button>
              </div>
            </div>
          </div>
          
          <!-- Bookings Table -->
          <div class="table-responsive">
            <table class="table table-hover" id="bookingsTable">
              <thead class="table-light">
                <tr>
                  <th>Booking ID</th>
                  <th>Customer/Tour</th>
                  <th>Type</th>
                  <th>Details</th>
                  <th>Amount</th>
                  <th>Commission</th>
                  <th>Status</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($all_bookings as $booking): 
                  $type_badge = '';
                  $type_icon = '';
                  switch($booking['booking_type']) {
                    case 'Car Rental': 
                      $type_badge = 'dark'; 
                      $type_icon = 'bi-car-front';
                      break;
                    default:
                      $type_badge = 'secondary';
                      $type_icon = 'bi-question';
                  }
                  
                  $status_badge = '';
                  $status_icon = '';
                  switch($booking['status']) {
                    case 'Confirmed': 
                    case 'Available': 
                    case 'Active': 
                      $status_badge = 'success'; 
                      $status_icon = 'bi-check-circle';
                      break;
                    case 'Pending': 
                      $status_badge = 'warning'; 
                      $status_icon = 'bi-clock';
                      break;
                    case 'Completed': 
                    case 'Returned': 
                    case 'Checked-out': 
                      $status_badge = 'info'; 
                      $status_icon = 'bi-check-all';
                      break;
                    case 'Cancelled': 
                      $status_badge = 'danger'; 
                      $status_icon = 'bi-x-circle';
                      break;
                    case 'Checked-in': 
                      $status_badge = 'primary'; 
                      $status_icon = 'bi-door-open';
                      break;
                    case 'Boarded': 
                      $status_badge = 'info'; 
                      $status_icon = 'bi-airplane-engines';
                      break;
                    case 'Fully Booked': 
                      $status_badge = 'danger'; 
                      $status_icon = 'bi-exclamation-circle';
                      break;
                    default: 
                      $status_badge = 'secondary'; 
                      $status_icon = 'bi-question-circle';
                  }
                ?>
                <tr data-type="<?php echo $booking['booking_type']; ?>" data-status="<?php echo $booking['status']; ?>" data-id="<?php echo $booking['id']; ?>" data-customer="<?php echo htmlspecialchars($booking['customer_name']); ?>" data-details="<?php echo htmlspecialchars($booking['details']); ?>">
                  <td>
                    <div class="d-flex align-items-center">
                      <i class="bi bi-ticket-perforated text-primary me-2"></i>
                      <strong><?php echo $booking['id']; ?></strong>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <i class="bi bi-person-circle text-muted me-2"></i>
                      <?php echo htmlspecialchars($booking['customer_name']); ?>
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-<?php echo $type_badge; ?>">
                      <i class="bi <?php echo $type_icon; ?> me-1"></i>
                      <?php echo $booking['booking_type']; ?>
                    </span>
                  </td>
                  <td>
                    <small><?php echo $booking['details']; ?></small>
                  </td>
                  <td>
                    <strong class="text-success">₱<?php echo number_format($booking['amount'], 2); ?></strong>
                  </td>
                  <td>
                    <?php if ($booking['commission'] > 0): ?>
                    <span class="badge bg-success">
                      <i class="bi bi-cash-coin me-1"></i>
                      ₱<?php echo number_format($booking['commission'], 2); ?>
                    </span>
                    <?php else: ?>
                    <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="badge bg-<?php echo $status_badge; ?>">
                      <i class="bi <?php echo $status_icon; ?> me-1"></i>
                      <?php echo $booking['status']; ?>
                    </span>
                  </td>
                  <td>
                    <small class="text-muted">
                      <i class="bi bi-calendar-event me-1"></i>
                      <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?>
                    </small>
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <button type="button" class="btn btn-outline-primary" onclick="showStatusModal('<?php echo $booking['type']; ?>', '<?php echo $booking['id']; ?>', '<?php echo $booking['booking_type']; ?>', '<?php echo htmlspecialchars($booking['customer_name'], ENT_QUOTES); ?>')" title="Update Status">
                        <i class="bi bi-arrow-repeat"></i>
                      </button>
                      <button type="button" class="btn btn-outline-info" onclick="viewBooking('<?php echo $booking['type']; ?>', '<?php echo $booking['id']; ?>')" title="View Details">
                        <i class="bi bi-eye"></i>
                      </button>
                      <button type="button" class="btn btn-outline-danger" onclick="deleteBooking('<?php echo $booking['type']; ?>', '<?php echo $booking['id']; ?>')" title="Delete">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          
          <?php if (empty($all_bookings)): ?>
          <div class="text-center py-5">
            <i class="bi bi-calendar-x display-1 text-muted"></i>
            <h5 class="mt-3">No bookings found</h5>
            <p class="text-muted">Start by creating your first booking.</p>
            <a href="index.php?page=new_booking" class="btn btn-primary mt-2">
              <i class="bi bi-plus-circle"></i> Create New Booking
            </a>
          </div>
          <?php endif; ?>
          
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Booking Status</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-3">Update status for: <strong id="bookingCustomer"></strong></p>
        <div class="list-group" id="statusOptions">
          <!-- Status options will be loaded here by JavaScript -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- View Booking Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Booking Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="bookingDetails">
        <!-- Booking details will be loaded here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
// Modal-based status update function
function showStatusModal(type, id, bookingType, customerName) {
    // Set customer name in modal
    document.getElementById('bookingCustomer').textContent = customerName;
    
    // Clear previous options
    const statusOptions = document.getElementById('statusOptions');
    statusOptions.innerHTML = '';
    
    // Define status options based on booking type
    let statusList = ['Pending', 'Confirmed', 'Active', 'Returned', 'Cancelled'];
    
    // Add status options to modal
    statusList.forEach(status => {
        const link = document.createElement('a');
        link.href = 'index.php?page=all_bookings&action=update_status&type=' + 
                   type + 
                   '&id=' + id + 
                   '&status=' + status;
        link.className = 'list-group-item list-group-item-action';
        link.textContent = status;
        statusOptions.appendChild(link);
    });
    
    // Show the modal using Bootstrap
    const modalElement = document.getElementById('statusModal');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

// Filter functionality
function filterBookings() {
    const typeFilter = document.getElementById('filterType').value;
    const statusFilter = document.getElementById('filterStatus').value;
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#bookingsTable tbody tr');
    
    rows.forEach(row => {
        const rowType = row.getAttribute('data-type');
        const rowStatus = row.getAttribute('data-status');
        const rowId = row.getAttribute('data-id').toLowerCase();
        const rowCustomer = row.getAttribute('data-customer').toLowerCase();
        const rowDetails = row.getAttribute('data-details').toLowerCase();
        
        const typeMatch = !typeFilter || rowType === typeFilter;
        const statusMatch = !statusFilter || rowStatus === statusFilter;
        const searchMatch = !searchTerm || 
                           rowId.includes(searchTerm) || 
                           rowCustomer.includes(searchTerm) || 
                           rowDetails.includes(searchTerm);
        
        row.style.display = (typeMatch && statusMatch && searchMatch) ? '' : 'none';
    });
}

// Search function
function searchBookings() {
    filterBookings();
}

// Initialize filters when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Setup filter event listeners
    document.getElementById('filterType').addEventListener('change', filterBookings);
    document.getElementById('filterStatus').addEventListener('change', filterBookings);
    document.getElementById('searchInput').addEventListener('keyup', filterBookings);
    
    // Apply initial filter
    filterBookings();
});

// Delete booking confirmation
function deleteBooking(type, id) {
    if (confirm('Are you sure you want to delete this booking?')) {
        window.location.href = 'index.php?page=all_bookings&action=delete&type=' + type + '&id=' + id;
    }
}

// View booking details
function viewBooking(type, id) {
    // In a real application, you would fetch booking details via AJAX
    // For now, show a simple alert
    alert('Viewing booking details for: ' + id + '\nType: ' + type);
    
    // Example of what you could do with AJAX:
    /*
    fetch('get_booking_details.php?type=' + type + '&id=' + id)
        .then(response => response.json())
        .then(data => {
            document.getElementById('bookingDetails').innerHTML = data.html;
            const modal = new bootstrap.Modal(document.getElementById('viewModal'));
            modal.show();
        });
    */
}
</script>

<style>
.stat-card {
  border: 1px solid #e0e0e0;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  margin-bottom: 20px;
  transition: transform 0.3s;
  background: white;
}

.stat-card:hover {
  transform: translateY(-5px);
}

.stat-card .card-body {
  padding: 20px;
}

.card h5.card-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #333;
}

.list-group-item {
  border-left: none;
  border-right: none;
  border-radius: 0;
}

.list-group-item:first-child {
  border-top: none;
}

.list-group-item:last-child {
  border-bottom: none;
}
</style>