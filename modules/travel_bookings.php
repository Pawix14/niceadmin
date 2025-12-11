<?php
// modules/travel_bookings.php

// Database connection
$servername = "127.0.0.1";
$username = "root"; // Change as needed
$password = ""; // Change as needed
$dbname = "travel_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle search and filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$travel_type_filter = isset($_GET['travel_type']) ? $_GET['travel_type'] : '';
$country_filter = isset($_GET['country']) ? $_GET['country'] : '';

// Build the query
$query = "SELECT * FROM travel_bookings WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (traveler_name LIKE ? OR booking_id LIKE ? OR from_city LIKE ? OR to_city LIKE ? OR from_country LIKE ? OR to_country LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "ssssss";
}

if (!empty($status_filter)) {
    $query .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if (!empty($travel_type_filter)) {
    $query .= " AND travel_type = ?";
    $params[] = $travel_type_filter;
    $types .= "s";
}

if (!empty($country_filter)) {
    $query .= " AND (from_country = ? OR to_country = ?)";
    $params[] = $country_filter;
    $params[] = $country_filter;
    $types .= "ss";
}

$query .= " ORDER BY booking_date DESC";

// Prepare and execute the query
$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Get unique values for filters
$statuses_result = $conn->query("SELECT DISTINCT status FROM travel_bookings ORDER BY status");
$travel_types_result = $conn->query("SELECT DISTINCT travel_type FROM travel_bookings ORDER BY travel_type");
$countries_result = $conn->query("SELECT DISTINCT from_country FROM travel_bookings UNION SELECT DISTINCT to_country FROM travel_bookings ORDER BY from_country");
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Travel Bookings Management</h5>
        </div>
        <div class="card-body">
            <!-- Search and Filter Form -->
            <div class="row mb-4">
                <div class="col-12">
                    <!-- Add hidden field to preserve page parameter -->
                    <form method="GET" action="" class="row g-3">
                        <input type="hidden" name="page" value="travel_bookings">
                        
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="search" placeholder="Search bookings..." 
                                   value="<?php echo htmlspecialchars($search); ?>" id="searchInput">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status" id="statusFilter">
                                <option value="">All Statuses</option>
                                <?php while($row = $statuses_result->fetch_assoc()): ?>
                                    <option value="<?php echo $row['status']; ?>" 
                                            <?php echo ($status_filter == $row['status']) ? 'selected' : ''; ?>>
                                        <?php echo $row['status']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="travel_type" id="travelTypeFilter">
                                <option value="">All Travel Types</option>
                                <?php while($row = $travel_types_result->fetch_assoc()): ?>
                                    <option value="<?php echo $row['travel_type']; ?>" 
                                            <?php echo ($travel_type_filter == $row['travel_type']) ? 'selected' : ''; ?>>
                                        <?php echo $row['travel_type']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="country" id="countryFilter">
                                <option value="">All Countries</option>
                                <?php while($row = $countries_result->fetch_assoc()): ?>
                                    <option value="<?php echo htmlspecialchars($row['from_country']); ?>" 
                                            <?php echo ($country_filter == $row['from_country']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($row['from_country']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                        <div class="col-md-1">
                            <a href="?page=travel_bookings" class="btn btn-secondary w-100">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Data Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="travelTable">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Traveler Name</th>
                            <th>Travel Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
                            <th>Booking Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['booking_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['traveler_name']); ?></td>
                                    <td>
                                        <span class="badge 
                                            <?php 
                                            switch($row['travel_type']) {
                                                case 'Airplane': echo 'bg-info'; break;
                                                case 'Ship': echo 'bg-primary'; break;
                                                case 'Train': echo 'bg-warning'; break;
                                                case 'Bus': echo 'bg-secondary'; break;
                                                case 'Car': echo 'bg-success'; break;
                                                default: echo 'bg-light text-dark';
                                            }
                                            ?>">
                                            <?php echo $row['travel_type']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($row['from_city']); ?>, 
                                        <small class="text-muted"><?php echo htmlspecialchars($row['from_country']); ?></small>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($row['to_city']); ?>, 
                                        <small class="text-muted"><?php echo htmlspecialchars($row['to_country']); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            <?php 
                                            switch($row['status']) {
                                                case 'Booked': echo 'bg-success'; break;
                                                case 'Cancelled': echo 'bg-danger'; break;
                                                case 'Completed': echo 'bg-primary'; break;
                                                default: echo 'bg-secondary';
                                            }
                                            ?>">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($row['booking_date'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary view-booking" 
                                                    data-bs-toggle="modal" data-bs-target="#viewModal"
                                                    data-id="<?php echo $row['id']; ?>">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning edit-booking"
                                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                                    data-id="<?php echo $row['id']; ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger delete-booking"
                                                    data-id="<?php echo $row['id']; ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No bookings found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination (if needed) -->
            <div class="row mt-3">
                <div class="col-md-6">
                    <p>Showing <?php echo $result->num_rows; ?> bookings</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <!-- Details will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="editModalBody">
                <!-- Edit form will be loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
// JavaScript for handling modal actions and live search
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const travelTypeFilter = document.getElementById('travelTypeFilter');
    const countryFilter = document.getElementById('countryFilter');
    const tableBody = document.getElementById('tableBody');
    const form = document.querySelector('form[method="GET"]');
    
    // Live search function
    function performLiveSearch() {
        // Create URL with current filter values
        const params = new URLSearchParams();
        params.append('page', 'travel_bookings');
        
        if (searchInput.value.trim()) {
            params.append('search', searchInput.value);
        }
        if (statusFilter.value) {
            params.append('status', statusFilter.value);
        }
        if (travelTypeFilter.value) {
            params.append('travel_type', travelTypeFilter.value);
        }
        if (countryFilter.value) {
            params.append('country', countryFilter.value);
        }
        
        // Use Fetch API to get filtered results without page reload
        fetch(`index.php?${params.toString()}`)
            .then(response => response.text())
            .then(data => {
                // Parse the response to extract just the table rows
                const parser = new DOMParser();
                const doc = parser.parseFromString(data, 'text/html');
                const newTableBody = doc.querySelector('#tableBody');
                
                if (newTableBody) {
                    tableBody.innerHTML = newTableBody.innerHTML;
                    
                    // Re-attach event listeners to new buttons
                    attachModalListeners();
                    
                    // Update booking count if present
                    const countElement = document.querySelector('.row.mt-3 .col-md-6 p');
                    const newCountElement = doc.querySelector('.row.mt-3 .col-md-6 p');
                    if (countElement && newCountElement) {
                        countElement.textContent = newCountElement.textContent;
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    }
    
    // Attach event listeners for live search
    searchInput.addEventListener('input', debounce(performLiveSearch, 300));
    statusFilter.addEventListener('change', performLiveSearch);
    travelTypeFilter.addEventListener('change', performLiveSearch);
    countryFilter.addEventListener('change', performLiveSearch);
    
    // Prevent form submission for live search
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        performLiveSearch();
    });
    
    // Debounce function to limit API calls
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Re-attach modal listeners after content updates
    function attachModalListeners() {
        // View booking details
        document.querySelectorAll('.view-booking').forEach(button => {
            button.addEventListener('click', function() {
                const bookingId = this.getAttribute('data-id');
                fetch(`modules/get_booking_details.php?id=${bookingId}`)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('viewModalBody').innerHTML = data;
                    });
            });
        });

        // Edit booking
        document.querySelectorAll('.edit-booking').forEach(button => {
            button.addEventListener('click', function() {
                const bookingId = this.getAttribute('data-id');
                fetch(`modules/get_booking_edit.php?id=${bookingId}`)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('editModalBody').innerHTML = data;
                    });
            });
        });

        // Delete booking
        document.querySelectorAll('.delete-booking').forEach(button => {
            button.addEventListener('click', function() {
                const bookingId = this.getAttribute('data-id');
                if (confirm('Are you sure you want to delete this booking?')) {
                    fetch(`modules/delete_booking.php?id=${bookingId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Refresh the table after deletion
                                performLiveSearch();
                            } else {
                                alert('Error deleting booking: ' + data.message);
                            }
                        });
                }
            });
        });
    }
    
    // Initial attachment of modal listeners
    attachModalListeners();
});
</script>

<?php
$stmt->close();
$conn->close();
?>