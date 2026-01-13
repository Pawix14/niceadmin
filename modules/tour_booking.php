<?php
// modules/tour_booking.php

// Start session check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
$tour_id = isset($_GET['tour_id']) ? $_GET['tour_id'] : null;

// Get tour details
$tour = null;
if ($tour_id) {
    $tour_sql = "SELECT * FROM tour_activities WHERE tour_id = '$tour_id'";
    $tour_result = $conn->query($tour_sql);
    if ($tour_result && $tour_result->num_rows > 0) {
        $tour = $tour_result->fetch_assoc();
    }
}

// Get agents for dropdown
$agents_result = $conn->query("SELECT agent_id, agent_name, commission_rate FROM travel_agents ORDER BY agent_name");

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_tour'])) {
    $participant_name = $conn->real_escape_string($_POST['participant_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $participants = (int)$_POST['participants'];
    $tour_id = $conn->real_escape_string($_POST['tour_id']);
    $tour_name = $conn->real_escape_string($_POST['tour_name']);
    $city = $conn->real_escape_string($_POST['city']);
    $country = $conn->real_escape_string($_POST['country']);
    $price_per_person = (float)$_POST['price_per_person'];
    $agent_id = !empty($_POST['agent_id']) ? $conn->real_escape_string($_POST['agent_id']) : null;
    $special_requests = $conn->real_escape_string($_POST['special_requests']);
    
    // Calculate total amount
    $total_amount = $price_per_person * $participants;
    
    // Calculate agent commission if agent is selected
    $agent_commission = 0;
    if ($agent_id) {
        $agent_result = $conn->query("SELECT commission_rate FROM travel_agents WHERE agent_id = '$agent_id'");
        if ($agent_result && $agent_row = $agent_result->fetch_assoc()) {
            $commission_rate = $agent_row['commission_rate'];
            $agent_commission = ($total_amount * $commission_rate) / 100;
        }
    }
    
    // Generate booking ID
    $booking_id = 'TOUR-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    
    // Insert booking
    $sql = "INSERT INTO tour_bookings (booking_id, participant_name, email, phone, tour_name, country, city, tour_type, tour_date, duration_days, duration_nights, participants, price_per_person, total_amount, agent_id, agent_commission, special_requests, status) 
            VALUES ('$booking_id', '$participant_name', '$email', '$phone', '$tour_name', '$country', '$city', '{$tour['tour_type']}', '{$tour['tour_date']}', {$tour['duration_days']}, {$tour['duration_nights']}, $participants, $price_per_person, $total_amount, " . ($agent_id ? "'$agent_id'" : "NULL") . ", $agent_commission, '$special_requests', 'Confirmed')";
    
    if ($conn->query($sql)) {
        // Update available slots
        $conn->query("UPDATE tour_activities SET available_slots = available_slots - $participants WHERE tour_id = '$tour_id'");
        
        $message = "✅ Tour booked successfully! Booking ID: <strong>$booking_id</strong>";
        $message_type = "success";
        
        // Clear form data
        $_POST = array();
    } else {
        $message = "❌ Error booking tour: " . $conn->error;
        $message_type = "error";
    }
}

if (!$tour) {
    echo "<div class='alert alert-danger'>Tour not found. Please select a valid tour.</div>";
    echo "<a href='?page=tours' class='btn btn-primary'>Back to Tours</a>";
    exit;
}
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="display-6 fw-bold">🎫 Book Your Tour</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="?page=tours">Tours</a></li>
                    <li class="breadcrumb-item active">Book Tour</li>
                </ol>
            </nav>
        </div>
    </div>

    <?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- Tour Details (Left Column) -->
        <div class="col-lg-5">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Tour Details</h5>
                </div>
                <div class="card-body">
                    <h4><?php echo htmlspecialchars($tour['tour_name']); ?></h4>
                    <p class="text-muted mb-3">
                        <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($tour['city']); ?>, <?php echo htmlspecialchars($tour['country']); ?>
                    </p>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong><i class="bi bi-calendar"></i> Date:</strong><br>
                            <?php echo date('F d, Y', strtotime($tour['tour_date'])); ?>
                        </div>
                        <div class="col-6">
                            <strong><i class="bi bi-clock"></i> Duration:</strong><br>
                            <?php echo $tour['duration_days']; ?> day(s), <?php echo $tour['duration_nights']; ?> night(s)
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong><i class="bi bi-tag"></i> Type:</strong><br>
                            <span class="badge bg-info"><?php echo $tour['tour_type']; ?></span>
                        </div>
                        <div class="col-6">
                            <strong><i class="bi bi-people"></i> Available:</strong><br>
                            <?php echo $tour['available_slots']; ?> / <?php echo $tour['max_participants']; ?> slots
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <strong><i class="bi bi-cash"></i> Price per Person:</strong><br>
                        <span class="h4 text-success">₱<?php echo number_format($tour['price_per_person'], 2); ?></span>
                    </div>
                    
                    <?php if ($tour['highlights']): ?>
                    <div class="mb-3">
                        <strong><i class="bi bi-stars"></i> Highlights:</strong><br>
                        <p class="text-muted"><?php echo htmlspecialchars($tour['highlights']); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($tour['included']): ?>
                    <div class="mb-3">
                        <strong><i class="bi bi-check-circle"></i> What's Included:</strong><br>
                        <p class="text-muted"><?php echo htmlspecialchars($tour['included']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Booking Form (Right Column) -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-person-plus"></i> Booking Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="book_tour" value="1">
                        <input type="hidden" name="tour_id" value="<?php echo $tour['tour_id']; ?>">
                        <input type="hidden" name="tour_name" value="<?php echo htmlspecialchars($tour['tour_name']); ?>">
                        <input type="hidden" name="city" value="<?php echo htmlspecialchars($tour['city']); ?>">
                        <input type="hidden" name="country" value="<?php echo htmlspecialchars($tour['country']); ?>">
                        <input type="hidden" name="price_per_person" value="<?php echo $tour['price_per_person']; ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Participant Name *</label>
                                <input type="text" name="participant_name" class="form-control" required 
                                       placeholder="Enter full name" value="<?php echo isset($_POST['participant_name']) ? htmlspecialchars($_POST['participant_name']) : ''; ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" class="form-control" required 
                                       placeholder="your@email.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Phone Number *</label>
                                <input type="tel" name="phone" class="form-control" required 
                                       placeholder="+63 912 345 6789" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Number of Participants *</label>
                                <select name="participants" class="form-select" required onchange="calculateTotal()">
                                    <?php for($i = 1; $i <= min(10, $tour['available_slots']); $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo (isset($_POST['participants']) && $_POST['participants'] == $i) ? 'selected' : ''; ?>>
                                            <?php echo $i; ?> participant<?php echo $i > 1 ? 's' : ''; ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Travel Agent (Optional)</label>
                                <select name="agent_id" class="form-select" onchange="calculateTotal()">
                                    <option value="">No agent</option>
                                    <?php if ($agents_result): ?>
                                        <?php while($agent = $agents_result->fetch_assoc()): ?>
                                            <option value="<?php echo $agent['agent_id']; ?>" data-commission="<?php echo $agent['commission_rate']; ?>"
                                                    <?php echo (isset($_POST['agent_id']) && $_POST['agent_id'] == $agent['agent_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($agent['agent_name']); ?> (<?php echo $agent['commission_rate']; ?>% commission)
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Total Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="text" id="totalAmount" class="form-control" readonly value="<?php echo number_format($tour['price_per_person'], 2); ?>">
                                </div>
                                <small class="text-muted">Price will update based on number of participants</small>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Special Requests (Optional)</label>
                                <textarea name="special_requests" class="form-control" rows="3" 
                                          placeholder="Any special requirements, dietary restrictions, accessibility needs, etc."><?php echo isset($_POST['special_requests']) ? htmlspecialchars($_POST['special_requests']) : ''; ?></textarea>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                    <label class="form-check-label" for="agreeTerms">
                                        I agree to the <a href="#" target="_blank">terms and conditions</a> and <a href="#" target="_blank">cancellation policy</a>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-12 text-center mt-4">
                                <button type="submit" class="btn btn-success btn-lg px-5">
                                    <i class="bi bi-check-circle"></i> Confirm Booking
                                </button>
                                <a href="?page=tours" class="btn btn-secondary btn-lg px-5 ms-3">
                                    <i class="bi bi-arrow-left"></i> Back to Tours
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calculateTotal() {
    const participants = document.querySelector('select[name="participants"]').value;
    const pricePerPerson = <?php echo $tour['price_per_person']; ?>;
    const total = participants * pricePerPerson;
    
    document.getElementById('totalAmount').value = new Intl.NumberFormat('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(total);
}

// Calculate total on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotal();
});
</script>

<?php $conn->close(); ?>