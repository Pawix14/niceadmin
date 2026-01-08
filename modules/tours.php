<?php
// modules/tours.php

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get tour image
function getTourImage($city, $country, $tour_type = '') {
    $city_lower = strtolower($city);
    $country_lower = strtolower($country);
    
    // Map specific cities to images
    $city_images = [
        'tokyo' => 'tokyo-japan-tour.jpg',
        'london' => 'london-uk-tour.jpg',
        'manila' => 'manila-tour.jpg',
        'bangkok' => 'bangkok-tour.jpg',
        'singapore' => 'singapore-tour.jpg',
        'bali' => 'bali-tour.jpg',
        'paris' => 'paris-tour.jpg',
        'new york' => 'newyork-tour.jpg',
        'rome' => 'rome-tour.jpg',
        'sydney' => 'sydney-tour.jpg',
        'dubai' => 'dubai-tour.jpg',
        'hong kong' => 'hongkong-tour.jpg',
    ];
    
    // Map countries to images
    $country_images = [
        'japan' => 'japan-tour.jpg',
        'united kingdom' => 'uk-tour.jpg',
        'philippines' => 'philippines-tour.jpg',
        'thailand' => 'thailand-tour.jpg',
        'malaysia' => 'malaysia-tour.jpg',
        'vietnam' => 'vietnam-tour.jpg',
        'indonesia' => 'indonesia-tour.jpg',
    ];
    
    // Check by city first
    foreach ($city_images as $city_key => $image) {
        if (strpos($city_lower, $city_key) !== false) {
            $image_name = $image;
            break;
        }
    }
    
    // If no city match, check by country
    if (!isset($image_name)) {
        foreach ($country_images as $country_key => $image) {
            if (strpos($country_lower, $country_key) !== false) {
                $image_name = $image;
                break;
            }
        }
    }
    
    // If still no match, use tour type based images
    if (!isset($image_name)) {
        $tour_type_images = [
            'adventure' => 'adventure-tour.jpg',
            'cultural' => 'cultural-tour.jpg',
            'beach' => 'beach-tour.jpg',
            'mountain' => 'mountain-tour.jpg',
            'food' => 'food-tour.jpg',
            'historical' => 'historical-tour.jpg',
        ];
        
        $tour_type_lower = strtolower($tour_type);
        foreach ($tour_type_images as $type_key => $image) {
            if (strpos($tour_type_lower, $type_key) !== false) {
                $image_name = $image;
                break;
            }
        }
    }
    
    // Final fallback
    if (!isset($image_name)) {
        $default_images = ['tour-1.jpg', 'tour-2.jpg', 'tour-3.jpg', 'tour-4.jpg', 'tour-5.jpg'];
        $image_name = $default_images[array_rand($default_images)];
    }
    
    $image_path = 'assets/img/tours/' . $image_name;
    $full_path = $_SERVER['DOCUMENT_ROOT'] . '/niceadmin/' . $image_path;
    
    return file_exists($full_path) ? $image_path : false;
}

// Function to get country image
function getCountryImage($country_name) {
    $country_lower = strtolower($country_name);
    
    $country_images = [
        'philippines' => 'philippines.jpg',
        'japan' => 'japan.jpg',
        'thailand' => 'thailand.jpg',
        'singapore' => 'singapore.jpg',
        'malaysia' => 'malaysia.jpg',
        'vietnam' => 'vietnam.jpg',
        'indonesia' => 'indonesia.jpg',
        'united kingdom' => 'uk.jpg',
        'united states' => 'usa.jpg',
        'australia' => 'australia.jpg',
        'france' => 'france.jpg',
        'italy' => 'italy.jpg',
    ];
    
    foreach ($country_images as $country_key => $image) {
        if (strpos($country_lower, $country_key) !== false) {
            $image_name = $image;
            break;
        }
    }
    
    if (!isset($image_name)) {
        $image_name = 'default-country.jpg';
    }
    
    $image_path = 'assets/img/countries/' . $image_name;
    $full_path = $_SERVER['DOCUMENT_ROOT'] . '/niceadmin/' . $image_path;
    
    return file_exists($full_path) ? $image_path : false;
}

// Handle form submission - Create new tour in tour_activities table
$message = '';
$message_type = '';
$selected_tour_id = isset($_GET['select_tour']) ? $_GET['select_tour'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_tour'])) {
        $tour_name = $conn->real_escape_string($_POST['tour_name']);
        $country = $conn->real_escape_string($_POST['country']);
        $city = $conn->real_escape_string($_POST['city']);
        $tour_type = $conn->real_escape_string($_POST['tour_type']);
        $duration_days = (int)$_POST['duration_days'];
        $duration_nights = (int)$_POST['duration_nights'];
        $duration_hours = (int)$_POST['duration_hours'];
        $max_participants = (int)$_POST['max_participants'];
        $available_slots = (int)$_POST['available_slots'];
        $price_per_person = (float)$_POST['price_per_person'];
        $tour_date = $conn->real_escape_string($_POST['tour_date']);
        $status = $conn->real_escape_string($_POST['status']);
        $description = $conn->real_escape_string($_POST['description']);
        $highlights = $conn->real_escape_string($_POST['highlights']);
        $included = $conn->real_escape_string($_POST['included']);
        
        // Generate tour ID
        $tour_id = 'TOUR-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        
        $sql = "INSERT INTO tour_activities (tour_id, tour_name, country, city, tour_type, duration_days, duration_nights, duration_hours, max_participants, available_slots, price_per_person, tour_date, status, description, highlights, included) 
                VALUES ('$tour_id', '$tour_name', '$country', '$city', '$tour_type', $duration_days, $duration_nights, $duration_hours, $max_participants, $available_slots, $price_per_person, '$tour_date', '$status', '$description', '$highlights', '$included')";
        
        if ($conn->query($sql)) {
            $message = "✅ Tour created successfully! Tour ID: <strong>$tour_id</strong>";
            $message_type = "success";
        } else {
            $message = "❌ Error creating tour: " . $conn->error;
            $message_type = "error";
        }
    }
}

// Handle tour selection
if ($selected_tour_id) {
    $selected_tour_sql = "SELECT * FROM tour_activities WHERE tour_id = '$selected_tour_id'";
    $selected_tour_result = $conn->query($selected_tour_sql);
    if ($selected_tour_result && $selected_tour_result->num_rows > 0) {
        $selected_tour = $selected_tour_result->fetch_assoc();
    }
}

// Fetch countries for dropdown
$countries_result = $conn->query("SELECT country_name FROM countries ORDER BY country_name");

// Fetch available tours for display
$tours_result = $conn->query("SELECT * FROM tour_activities WHERE (status = 'Available' OR status = 'Active') AND available_slots > 0 ORDER BY tour_date ASC");

// Fetch all tours for management
$all_tours_result = $conn->query("SELECT * FROM tour_activities ORDER BY tour_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tours Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .tour-card {
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            cursor: pointer;
        }

        .tour-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .tour-card.selected {
            border: 3px solid #ff6b35;
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
        }

        .tour-image {
            height: 200px;
            width: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .tour-card:hover .tour-image {
            transform: scale(1.05);
        }

        .tour-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            z-index: 2;
        }

        .tour-price {
            color: #ff6b35;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .tour-duration {
            color: #666;
            font-size: 0.9rem;
        }

        .tour-location {
            color: #333;
            font-weight: 500;
        }

        .country-card {
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            cursor: pointer;
            transition: transform 0.3s;
            height: 180px;
        }

        .country-card:hover {
            transform: scale(1.03);
        }

        .country-image {
            height: 100%;
            width: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .country-card:hover .country-image {
            transform: scale(1.1);
        }

        .country-name {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: white;
            padding: 20px 15px 10px;
            font-weight: bold;
            z-index: 1;
        }

        .tour-tabs {
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .tour-tab {
            background: none;
            border: none;
            padding: 10px 20px;
            font-weight: 500;
            color: #666;
            border-bottom: 3px solid transparent;
            cursor: pointer;
        }

        .tour-tab.active {
            color: #ff6b35;
            border-bottom: 3px solid #ff6b35;
        }

        .tour-tab:hover {
            color: #ff6b35;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .selected-tour-sidebar {
            position: sticky;
            top: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            border-left: 4px solid #ff6b35;
            animation: fadeIn 0.5s ease;
        }

        .selection-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ff6b35;
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            z-index: 2;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .image-placeholder {
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 14px;
        }

        .tour-image-container {
            position: relative;
            overflow: hidden;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .price-badge {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(255, 107, 53, 0.9);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
            z-index: 2;
        }
    </style>
</head>
<body>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-5 fw-bold">🌏 Tour Activities</h1>
            <p class="text-muted">Discover amazing tours and create unforgettable experiences</p>
        </div>
    </div>

    <?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- Selected Tour Sidebar (if any) -->
    <?php if (isset($selected_tour)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="selected-tour-sidebar">
                <div class="row">
                    <div class="col-md-2">
                        <?php $selected_tour_image = getTourImage($selected_tour['city'], $selected_tour['country'], $selected_tour['tour_type']); ?>
                        <?php if ($selected_tour_image): ?>
                            <img src="<?php echo $selected_tour_image; ?>" class="img-fluid rounded" style="height: 120px; width: 100%; object-fit: cover;" alt="<?php echo htmlspecialchars($selected_tour['tour_name']); ?>">
                        <?php else: ?>
                            <div class="image-placeholder rounded" style="height: 120px; width: 100%;">
                                <?php echo substr($selected_tour['city'], 0, 10); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5><i class="bi bi-check-circle-fill text-success"></i> Selected Tour</h5>
                                <h4 class="fw-bold"><?php echo htmlspecialchars($selected_tour['tour_name']); ?></h4>
                                <p class="text-muted mb-2">
                                    <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($selected_tour['city']); ?>, <?php echo htmlspecialchars($selected_tour['country']); ?>
                                </p>
                                <p class="mb-0">
                                    <span class="badge bg-info"><?php echo htmlspecialchars($selected_tour['tour_type']); ?></span>
                                    <span class="badge bg-primary"><?php echo date('M d, Y', strtotime($selected_tour['tour_date'])); ?></span>
                                    <span class="badge bg-success">₱<?php echo number_format($selected_tour['price_per_person'], 2); ?> per person</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 text-end">
                        <a href="?page=tours" class="btn btn-outline-secondary btn-sm mb-2">Change Selection</a>
                        <button class="btn btn-primary btn-lg" onclick="proceedToBooking('<?php echo $selected_tour['tour_id']; ?>')">
                            <i class="bi bi-calendar-check"></i> Book Now
                        </button>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong><i class="bi bi-clock"></i> Duration:</strong> <?php echo $selected_tour['duration_days']; ?> days, <?php echo $selected_tour['duration_nights']; ?> nights</p>
                        <p><strong><i class="bi bi-people"></i> Available Slots:</strong> <?php echo $selected_tour['available_slots']; ?> / <?php echo $selected_tour['max_participants']; ?></p>
                    </div>
                    <div class="col-md-6">
                        <?php if ($selected_tour['highlights']): ?>
                            <p><strong><i class="bi bi-stars"></i> Highlights:</strong> <?php echo htmlspecialchars(substr($selected_tour['highlights'], 0, 100)); ?>...</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Navigation Tabs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="tour-tabs">
                <button class="tour-tab active" onclick="showTab('browse')">Browse Tours</button>
                <button class="tour-tab" onclick="showTab('create')">Create Tour</button>
                <button class="tour-tab" onclick="showTab('manage')">Manage Tours</button>
            </div>
        </div>
    </div>

    <!-- Tab 1: Browse Tours -->
    <div class="tab-content active" id="browseTab">
        <!-- Search Bar -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control form-control-lg" id="searchTours" placeholder="Where do you want to visit? Enter destination, theme, or keyword">
                    <button class="btn btn-primary btn-lg" onclick="searchTours()">Search</button>
                </div>
            </div>
        </div>

        <!-- Customize Your Trip -->
        <div class="row mb-5">
            <div class="col-12">
                <h4 class="mb-3">✨ Customize your perfect trip</h4>
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5>Travel easy & Customize freely</h5>
                                <p class="text-muted">Design your own itinerary with our local experts</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-primary btn-lg">Customize my trip</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommended Countries -->
        <div class="row mb-5">
            <div class="col-12">
                <h4 class="mb-4">📍 Recommended Destinations</h4>
                <div class="row g-4">
                    <?php
                    $countries = [
                        ['name' => 'Philippines', 'code' => 'philippines'],
                        ['name' => 'Japan', 'code' => 'japan'],
                        ['name' => 'Thailand', 'code' => 'thailand'],
                        ['name' => 'Singapore', 'code' => 'singapore'],
                        ['name' => 'Malaysia', 'code' => 'malaysia'],
                        ['name' => 'Vietnam', 'code' => 'vietnam'],
                    ];
                    foreach ($countries as $country): 
                        $country_image = getCountryImage($country['name']);
                    ?>
                    <div class="col-md-4 col-lg-2">
                        <div class="country-card" onclick="filterByCountry('<?php echo $country['name']; ?>')">
                            <?php if ($country_image): ?>
                                <img src="<?php echo $country_image; ?>" class="country-image" alt="<?php echo $country['name']; ?>">
                            <?php else: ?>
                                <div class="country-image image-placeholder">
                                    <?php echo $country['name']; ?>
                                </div>
                            <?php endif; ?>
                            <div class="country-name"><?php echo $country['name']; ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Available Tours -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>🎯 Popular Tours</h4>
                    <div class="btn-group">
                        <button class="btn btn-outline-secondary btn-sm" onclick="filterTours('all')">All</button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="filterTours('available')">Available</button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="filterTours('adventure')">Adventure</button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="filterTours('cultural')">Cultural</button>
                    </div>
                </div>
                
                <?php if ($tours_result && $tours_result->num_rows > 0): ?>
                <div class="row g-4" id="tourGrid">
                    <?php while($tour = $tours_result->fetch_assoc()): 
                        $status_badge = '';
                        $badge_class = '';
                        $is_selected = isset($selected_tour) && $selected_tour['tour_id'] == $tour['tour_id'];
                        $tour_image = getTourImage($tour['city'], $tour['country'], $tour['tour_type']);
                        
                        switch($tour['status']) {
                            case 'Available': 
                            case 'Active':
                                $status_badge = 'Available'; 
                                $badge_class = 'bg-success';
                                break;
                            case 'Fully Booked': 
                                $status_badge = 'Fully Booked'; 
                                $badge_class = 'bg-danger';
                                break;
                            case 'Cancelled': 
                                $status_badge = 'Cancelled'; 
                                $badge_class = 'bg-secondary';
                                break;
                            case 'Completed': 
                                $status_badge = 'Completed'; 
                                $badge_class = 'bg-info';
                                break;
                            default:
                                $status_badge = $tour['status'];
                                $badge_class = 'bg-primary';
                        }
                    ?>
                    <div class="col-md-6 col-lg-4 col-xl-3 tour-item" data-type="<?php echo strtolower($tour['tour_type']); ?>" data-status="<?php echo strtolower($tour['status']); ?>">
                        <div class="tour-card <?php echo $is_selected ? 'selected' : ''; ?>" onclick="selectTour('<?php echo $tour['tour_id']; ?>')">
                            <?php if ($is_selected): ?>
                                <div class="selection-indicator">
                                    <i class="bi bi-check"></i>
                                </div>
                            <?php endif; ?>
                            <div class="tour-image-container">
                                <?php if ($tour_image): ?>
                                    <img src="<?php echo $tour_image; ?>" class="tour-image" alt="<?php echo htmlspecialchars($tour['tour_name']); ?>">
                                <?php else: ?>
                                    <div class="tour-image image-placeholder">
                                        <?php echo htmlspecialchars(substr($tour['city'], 0, 15)); ?>
                                    </div>
                                <?php endif; ?>
                                <span class="tour-badge <?php echo $badge_class; ?>"><?php echo $status_badge; ?></span>
                                <span class="price-badge">₱<?php echo number_format($tour['price_per_person'], 0); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="tour-location mb-1">
                                            <i class="bi bi-geo-alt"></i> 
                                            <?php echo htmlspecialchars($tour['city']); ?>, <?php echo htmlspecialchars($tour['country']); ?>
                                        </h6>
                                        <h5 class="mb-1" style="font-size: 1rem; min-height: 40px;"><?php echo htmlspecialchars($tour['tour_name']); ?></h5>
                                    </div>
                                </div>
                                
                                <div class="tour-duration mb-2">
                                    <i class="bi bi-clock"></i> 
                                    <?php echo isset($tour['duration_days']) ? $tour['duration_days'] : '1'; ?> day<?php echo (isset($tour['duration_days']) && $tour['duration_days'] > 1) ? 's' : ''; ?> 
                                    <?php echo isset($tour['duration_nights']) ? $tour['duration_nights'] : '0'; ?> night<?php echo (isset($tour['duration_nights']) && $tour['duration_nights'] > 1) ? 's' : ''; ?>
                                    • <?php echo isset($tour['tour_type']) ? $tour['tour_type'] : 'Private'; ?> tour
                                </div>
                                
                                <div class="tour-duration mb-3">
                                    <i class="bi bi-calendar"></i> 
                                    <?php echo date('M d, Y', strtotime($tour['tour_date'])); ?>
                                    <br>
                                    <i class="bi bi-people"></i> 
                                    <?php echo isset($tour['available_slots']) ? $tour['available_slots'] : (isset($tour['max_participants']) ? $tour['max_participants'] : '0'); ?> slots available
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="tour-price">
                                        ₱<?php echo number_format($tour['price_per_person'], 2); ?>
                                        <small class="text-muted d-block">per person</small>
                                    </div>
                                    <button class="btn <?php echo $is_selected ? 'btn-success' : 'btn-primary'; ?> btn-sm" onclick="event.stopPropagation(); selectTour('<?php echo $tour['tour_id']; ?>')">
                                        <?php if ($is_selected): ?>
                                            <i class="bi bi-check-circle"></i> Selected
                                        <?php else: ?>
                                            <i class="bi bi-plus-circle"></i> Select
                                        <?php endif; ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No tours available at the moment. Please check back later or create a new tour.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tab 2: Create Tour Form -->
    <div class="tab-content" id="createTab">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">✏️ Create New Tour Activity</h5>
                
                <form class="row g-3" method="POST" action="">
                    <input type="hidden" name="create_tour" value="1">
                    
                    <div class="col-md-12">
                        <label for="tour_name" class="form-label">Tour Name *</label>
                        <input type="text" class="form-control" id="tour_name" name="tour_name" required placeholder="Ex. Private Tour - Manila City Highlights">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="country" class="form-label">Country *</label>
                        <select class="form-select" id="country" name="country" required>
                            <option value="">Select country...</option>
                            <?php 
                            if ($countries_result) {
                                $countries_result->data_seek(0); // Reset pointer
                                while($country = $countries_result->fetch_assoc()): ?>
                                <option value="<?php echo $country['country_name']; ?>">
                                    <?php echo htmlspecialchars($country['country_name']); ?>
                                </option>
                                <?php endwhile; 
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="city" class="form-label">City *</label>
                        <input type="text" class="form-control" id="city" name="city" required placeholder="Ex. Manila">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="tour_type" class="form-label">Tour Type *</label>
                        <select class="form-select" id="tour_type" name="tour_type" required>
                            <option value="Sightseeing">🏛️ Sightseeing</option>
                            <option value="Adventure">🧗 Adventure</option>
                            <option value="Cultural">🎎 Cultural</option>
                            <option value="Food">🍜 Food Tour</option>
                            <option value="Nature">🌳 Nature</option>
                            <option value="Historical">📜 Historical</option>
                            <option value="Private">👤 Private Tour</option>
                            <option value="Group">👥 Group Tour</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="duration_days" class="form-label">Duration (Days) *</label>
                        <input type="number" class="form-control" id="duration_days" name="duration_days" min="1" max="30" value="1" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="duration_nights" class="form-label">Duration (Nights)</label>
                        <input type="number" class="form-control" id="duration_nights" name="duration_nights" min="0" max="29" value="0">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="duration_hours" class="form-label">Duration (Hours) *</label>
                        <input type="number" class="form-control" id="duration_hours" name="duration_hours" min="1" max="24" value="4" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="max_participants" class="form-label">Max Participants *</label>
                        <input type="number" class="form-control" id="max_participants" name="max_participants" min="1" max="100" value="10" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="available_slots" class="form-label">Available Slots *</label>
                        <input type="number" class="form-control" id="available_slots" name="available_slots" min="0" value="10" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="price_per_person" class="form-label">Price per Person (₱) *</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" id="price_per_person" name="price_per_person" step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="tour_date" class="form-label">Tour Date *</label>
                        <input type="date" class="form-control" id="tour_date" name="tour_date" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Available">✅ Available</option>
                            <option value="Active">🟢 Active</option>
                            <option value="Fully Booked">🚫 Fully Booked</option>
                            <option value="Cancelled">❌ Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <label for="highlights" class="form-label">Tour Highlights</label>
                        <textarea class="form-control" id="highlights" name="highlights" rows="3" placeholder="Key attractions and experiences..."></textarea>
                    </div>
                    
                    <div class="col-12">
                        <label for="included" class="form-label">What's Included</label>
                        <textarea class="form-control" id="included" name="included" rows="2" placeholder="Transportation, meals, tickets, etc..."></textarea>
                    </div>
                    
                    <div class="col-12">
                        <label for="description" class="form-label">Tour Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Detailed description of the tour..."></textarea>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-5">Create Tour</button>
                        <button type="reset" class="btn btn-secondary btn-lg px-5">Clear Form</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tab 3: Manage Tours -->
    <div class="tab-content" id="manageTab">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">📊 Tour Management Dashboard</h5>
                
                <?php if ($all_tours_result && $all_tours_result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tour ID</th>
                                <th>Tour Name</th>
                                <th>Location</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Duration</th>
                                <th>Price</th>
                                <th>Slots</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($tour = $all_tours_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo isset($tour['tour_id']) ? $tour['tour_id'] : $tour['id']; ?></td>
                                <td><?php echo htmlspecialchars($tour['tour_name']); ?></td>
                                <td><?php echo htmlspecialchars($tour['city']) . ', ' . htmlspecialchars($tour['country']); ?></td>
                                <td>
                                    <span class="badge bg-info">
                                    <?php echo isset($tour['tour_type']) ? $tour['tour_type'] : 'Private'; ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($tour['tour_date'])); ?></td>
                                <td>
                                    <?php echo isset($tour['duration_days']) ? $tour['duration_days'] : '1'; ?>D
                                    <?php echo isset($tour['duration_nights']) ? $tour['duration_nights'] : '0'; ?>N
                                </td>
                                <td>₱<?php echo number_format($tour['price_per_person'], 2); ?></td>
                                <td>
                                    <?php echo isset($tour['available_slots']) ? $tour['available_slots'] : '0'; ?> / <?php echo $tour['max_participants']; ?>
                                    <?php if (isset($tour['available_slots']) && $tour['available_slots'] < 5 && $tour['available_slots'] > 0): ?>
                                    <span class="badge bg-warning text-dark">Few left!</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php 
                                    switch($tour['status']) {
                                        case 'Available': 
                                        case 'Active': 
                                            echo 'success'; break;
                                        case 'Fully Booked': echo 'danger'; break;
                                        case 'Cancelled': echo 'secondary'; break;
                                        case 'Completed': echo 'info'; break;
                                        default: echo 'primary';
                                    }
                                    ?>"><?php echo $tour['status']; ?></span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editTour('<?php echo isset($tour['tour_id']) ? $tour['tour_id'] : $tour['id']; ?>')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteTour('<?php echo isset($tour['tour_id']) ? $tour['tour_id'] : $tour['id']; ?>')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No tours found. Please create a tour first.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tour-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName + 'Tab').classList.add('active');
    
    // Add active class to clicked tab button
    event.target.classList.add('active');
}

function selectTour(tourId) {
    // Scroll to top before redirecting
    window.scrollTo({ top: 0, behavior: 'smooth' });
    // Redirect with tour selection parameter
    window.location.href = '?page=tours&select_tour=' + tourId;
}

function filterByCountry(country) {
    const searchInput = document.getElementById('searchTours');
    searchInput.value = country;
    searchTours();
}

function searchTours() {
    const searchTerm = document.getElementById('searchTours').value.toLowerCase();
    const tourItems = document.querySelectorAll('.tour-item');
    
    tourItems.forEach(item => {
        const tourName = item.querySelector('h5').textContent.toLowerCase();
        const location = item.querySelector('.tour-location').textContent.toLowerCase();
        
        if (tourName.includes(searchTerm) || location.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function filterTours(filterType) {
    const tourItems = document.querySelectorAll('.tour-item');
    
    tourItems.forEach(item => {
        const type = item.getAttribute('data-type');
        const status = item.getAttribute('data-status');
        
        if (filterType === 'all') {
            item.style.display = 'block';
        } else if (filterType === 'available' && status === 'available') {
            item.style.display = 'block';
        } else if (filterType === 'adventure' && type.includes('adventure')) {
            item.style.display = 'block';
        } else if (filterType === 'cultural' && type.includes('cultural')) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function proceedToBooking(tourId) {
    alert('Proceeding to booking for tour: ' + tourId);
    // You can redirect to booking page
    // window.location.href = '?page=booking&tour_id=' + tourId;
}

function editTour(tourId) {
    alert('Edit tour: ' + tourId);
}

function deleteTour(tourId) {
    if (confirm('Are you sure you want to delete this tour?')) {
        window.location.href = 'index.php?page=delete_tour&id=' + tourId;
    }
}

// Initialize first tab as active on page load
document.addEventListener('DOMContentLoaded', function() {
    showTab('browse');
    
    // Add search functionality
    const searchInput = document.getElementById('searchTours');
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchTours();
        }
    });
    
    // Scroll to selected tour if exists
    <?php if (isset($selected_tour)): ?>
    const selectedCard = document.querySelector('.tour-card.selected');
    if (selectedCard) {
        selectedCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    <?php endif; ?>
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php 
if ($conn) {
    $conn->close();
}
?>