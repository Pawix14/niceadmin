<?php
// modules/tours_improved.php

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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_tour'])) {
    $tour_name = $conn->real_escape_string($_POST['tour_name']);
    $country = $conn->real_escape_string($_POST['country']);
    $city = $conn->real_escape_string($_POST['city']);
    $tour_type = $conn->real_escape_string($_POST['tour_type']);
    $duration_days = (int)$_POST['duration_days'];
    $duration_nights = (int)$_POST['duration_nights'];
    $max_participants = (int)$_POST['max_participants'];
    $available_slots = (int)$_POST['available_slots'];
    $price_per_person = (float)$_POST['price_per_person'];
    $tour_date = $conn->real_escape_string($_POST['tour_date']);
    $highlights = $conn->real_escape_string($_POST['highlights']);
    $included = $conn->real_escape_string($_POST['included']);
    
    $tour_id = 'TOUR-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    
    $sql = "INSERT INTO tour_activities (tour_id, tour_name, country, city, tour_type, duration_days, duration_nights, max_participants, available_slots, price_per_person, tour_date, status, highlights, included) 
            VALUES ('$tour_id', '$tour_name', '$country', '$city', '$tour_type', $duration_days, $duration_nights, $max_participants, $available_slots, $price_per_person, '$tour_date', 'Available', '$highlights', '$included')";
    
    if ($conn->query($sql)) {
        $message = "✅ Tour created successfully!";
        $message_type = "success";
    } else {
        $message = "❌ Error creating tour: " . $conn->error;
        $message_type = "error";
    }
}

// Get filters
$price_filter = isset($_GET['price']) ? $_GET['price'] : '';
$type_filter = isset($_GET['type']) ? $_GET['type'] : '';
$duration_filter = isset($_GET['duration']) ? $_GET['duration'] : '';
$country_filter = isset($_GET['country']) ? $_GET['country'] : '';

// Build query with filters
$where_conditions = ["(status = 'Available' OR status = 'Active')", "available_slots > 0"];

if ($price_filter) {
    switch($price_filter) {
        case 'budget': $where_conditions[] = "price_per_person <= 2000"; break;
        case 'mid': $where_conditions[] = "price_per_person BETWEEN 2001 AND 5000"; break;
        case 'luxury': $where_conditions[] = "price_per_person > 5000"; break;
    }
}

if ($type_filter) {
    $where_conditions[] = "tour_type = '$type_filter'";
}

if ($duration_filter) {
    switch($duration_filter) {
        case 'day': $where_conditions[] = "duration_days = 1"; break;
        case 'weekend': $where_conditions[] = "duration_days BETWEEN 2 AND 3"; break;
        case 'week': $where_conditions[] = "duration_days >= 4"; break;
    }
}

if ($country_filter) {
    $where_conditions[] = "country LIKE '%$country_filter%'";
}

$where_clause = implode(' AND ', $where_conditions);
$tours_result = $conn->query("SELECT * FROM tour_activities WHERE $where_clause ORDER BY tour_date ASC");

// Get countries from database
$countries_result = $conn->query("SELECT DISTINCT country FROM tour_activities ORDER BY country");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Activities</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .tour-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s;
            height: 100%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .tour-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .tour-image {
            height: 200px;
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .price-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255,255,255,0.95);
            color: #333;
            padding: 8px 15px;
            border-radius: 25px;
            font-weight: bold;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .filter-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .btn-filter {
            margin: 5px;
            border-radius: 25px;
        }
        .btn-filter.active {
            background: #0d6efd;
            color: white;
        }
        .create-tour-form {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-5 fw-bold">🌍 Discover Amazing Tours</h1>
            <p class="text-muted">Find your perfect adventure from our curated collection</p>
        </div>
    </div>

    <?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Advanced Filters -->
    <div class="filter-card">
        <h5 class="mb-3">🔍 Find Your Perfect Tour</h5>
        <form method="GET" action="">
            <input type="hidden" name="page" value="tours">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Price Range</label>
                    <select name="price" class="form-select">
                        <option value="">Any Price</option>
                        <option value="budget" <?php echo $price_filter == 'budget' ? 'selected' : ''; ?>>Budget (≤₱2,000)</option>
                        <option value="mid" <?php echo $price_filter == 'mid' ? 'selected' : ''; ?>>Mid-range (₱2,001-5,000)</option>
                        <option value="luxury" <?php echo $price_filter == 'luxury' ? 'selected' : ''; ?>>Luxury (>₱5,000)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tour Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="Adventure" <?php echo $type_filter == 'Adventure' ? 'selected' : ''; ?>>🏔️ Adventure</option>
                        <option value="Cultural" <?php echo $type_filter == 'Cultural' ? 'selected' : ''; ?>>🏛️ Cultural</option>
                        <option value="Food" <?php echo $type_filter == 'Food' ? 'selected' : ''; ?>>🍜 Food Tour</option>
                        <option value="Nature" <?php echo $type_filter == 'Nature' ? 'selected' : ''; ?>>🌿 Nature</option>
                        <option value="Historical" <?php echo $type_filter == 'Historical' ? 'selected' : ''; ?>>📜 Historical</option>
                        <option value="Beach" <?php echo $type_filter == 'Beach' ? 'selected' : ''; ?>>🏖️ Beach</option>
                        <option value="City" <?php echo $type_filter == 'City' ? 'selected' : ''; ?>>🏙️ City Tour</option>
                        <option value="Wildlife" <?php echo $type_filter == 'Wildlife' ? 'selected' : ''; ?>>🦁 Wildlife</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Duration</label>
                    <select name="duration" class="form-select">
                        <option value="">Any Duration</option>
                        <option value="day" <?php echo $duration_filter == 'day' ? 'selected' : ''; ?>>Day Trip</option>
                        <option value="weekend" <?php echo $duration_filter == 'weekend' ? 'selected' : ''; ?>>Weekend (2-3 days)</option>
                        <option value="week" <?php echo $duration_filter == 'week' ? 'selected' : ''; ?>>Extended (4+ days)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Country</label>
                    <select name="country" class="form-select">
                        <option value="">All Countries</option>
                        <?php if ($countries_result): ?>
                            <?php while($country = $countries_result->fetch_assoc()): ?>
                                <option value="<?php echo $country['country']; ?>" <?php echo $country_filter == $country['country'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($country['country']); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">🔍 Search Tours</button>
                    <a href="?page=tours" class="btn btn-outline-secondary">Clear Filters</a>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createTourModal">
                        ➕ Create New Tour
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tours Grid -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Available Tours (<?php echo $tours_result ? $tours_result->num_rows : 0; ?> found)</h4>
            </div>
            
            <?php if ($tours_result && $tours_result->num_rows > 0): ?>
            <div class="row g-4">
                <?php while($tour = $tours_result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="tour-card">
                        <div class="tour-image position-relative">
                            <?php echo htmlspecialchars($tour['city']); ?>, <?php echo htmlspecialchars($tour['country']); ?>
                            <div class="price-badge">₱<?php echo number_format($tour['price_per_person'], 0); ?></div>
                        </div>
                        <div class="p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-info"><?php echo $tour['tour_type']; ?></span>
                                <small class="text-muted"><?php echo $tour['available_slots']; ?> slots left</small>
                            </div>
                            
                            <h5 class="mb-2"><?php echo htmlspecialchars($tour['tour_name']); ?></h5>
                            
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> <?php echo date('M d, Y', strtotime($tour['tour_date'])); ?>
                                    <br>
                                    <i class="bi bi-clock"></i> <?php echo $tour['duration_days']; ?> day(s), <?php echo $tour['duration_nights']; ?> night(s)
                                </small>
                            </div>
                            
                            <?php if ($tour['highlights']): ?>
                            <p class="text-muted small mb-3"><?php echo htmlspecialchars(substr($tour['highlights'], 0, 100)); ?>...</p>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-primary">₱<?php echo number_format($tour['price_per_person'], 2); ?></strong>
                                    <small class="text-muted d-block">per person</small>
                                </div>
                                <button class="btn btn-primary" onclick="bookTour('<?php echo $tour['tour_id']; ?>')">
                                    Book Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-search display-1 text-muted"></i>
                <h4 class="mt-3">No tours found</h4>
                <p class="text-muted">Try adjusting your filters or create a new tour</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Create Tour Modal -->
<div class="modal fade" id="createTourModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Tour</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="create_tour" value="1">
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Tour Name *</label>
                            <input type="text" name="tour_name" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Country *</label>
                            <input type="text" name="country" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">City *</label>
                            <input type="text" name="city" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Tour Type *</label>
                            <select name="tour_type" class="form-select" required>
                                <option value="Adventure">🏔️ Adventure</option>
                                <option value="Cultural">🏛️ Cultural</option>
                                <option value="Food">🍜 Food Tour</option>
                                <option value="Nature">🌿 Nature</option>
                                <option value="Historical">📜 Historical</option>
                                <option value="Beach">🏖️ Beach</option>
                                <option value="City">🏙️ City Tour</option>
                                <option value="Wildlife">🦁 Wildlife</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Tour Date *</label>
                            <input type="date" name="tour_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Days *</label>
                            <input type="number" name="duration_days" class="form-control" min="1" value="1" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Nights</label>
                            <input type="number" name="duration_nights" class="form-control" min="0" value="0">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Price (₱) *</label>
                            <input type="number" name="price_per_person" class="form-control" step="0.01" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Max Participants *</label>
                            <input type="number" name="max_participants" class="form-control" min="1" value="10" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Available Slots *</label>
                            <input type="number" name="available_slots" class="form-control" min="0" value="10" required>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Highlights</label>
                            <textarea name="highlights" class="form-control" rows="2"></textarea>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">What's Included</label>
                            <textarea name="included" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Tour</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function bookTour(tourId) {
    window.location.href = '?page=tour_booking&tour_id=' + tourId;
}
</script>

</body>
</html>

<?php $conn->close(); ?>