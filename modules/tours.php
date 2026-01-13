<?php
// modules/tours.php - Improved version

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

// Add sample tours if none exist
$check_tours = $conn->query("SELECT COUNT(*) as count FROM tour_activities");
if ($check_tours && $check_tours->fetch_assoc()['count'] < 30) {
    // Clear existing tours first
    $conn->query("DELETE FROM tour_activities");
    
    // Insert comprehensive tour list
    $conn->query("INSERT INTO tour_activities (tour_id, tour_name, country, city, tour_type, duration_days, duration_nights, max_participants, available_slots, price_per_person, tour_date, status, highlights, included) VALUES 
    -- Adventure Tours
    ('TOUR-ADV-001', 'Tokyo Adventure Quest', 'Japan', 'Tokyo', 'Adventure', 3, 2, 8, 8, 6500.00, '2024-12-18', 'Available', 'Shibuya crossing, robot restaurant, Mount Fuji day trip', 'Accommodation, breakfast, guide'),
    ('TOUR-ADV-002', 'Nepal Himalaya Trek', 'Nepal', 'Kathmandu', 'Adventure', 7, 6, 6, 6, 12000.00, '2024-12-25', 'Available', 'Everest base camp, mountain climbing, sherpa guides', 'Equipment, meals, guide'),
    ('TOUR-ADV-003', 'New Zealand Bungee Jump', 'New Zealand', 'Queenstown', 'Adventure', 4, 3, 10, 10, 8500.00, '2025-01-05', 'Available', 'Bungee jumping, skydiving, white water rafting', 'Accommodation, activities, safety gear'),
    ('TOUR-ADV-004', 'Amazon Jungle Expedition', 'Brazil', 'Manaus', 'Adventure', 5, 4, 8, 8, 9500.00, '2025-01-12', 'Available', 'Jungle trekking, wildlife spotting, river adventures', 'Accommodation, meals, guide'),
    -- Cultural Tours
    ('TOUR-CUL-001', 'Cultural Heritage India', 'India', 'Delhi', 'Cultural', 7, 6, 10, 10, 9500.00, '2025-01-10', 'Available', 'Taj Mahal, Red Fort, traditional dance shows', 'Accommodation, meals, guide'),
    ('TOUR-CUL-002', 'Ancient Egypt Discovery', 'Egypt', 'Cairo', 'Cultural', 6, 5, 12, 12, 11000.00, '2024-12-20', 'Available', 'Pyramids, Sphinx, Nile cruise, museums', 'Accommodation, meals, entry tickets'),
    ('TOUR-CUL-003', 'Moroccan Heritage Tour', 'Morocco', 'Marrakech', 'Cultural', 5, 4, 8, 8, 7500.00, '2024-12-28', 'Available', 'Medina tours, traditional crafts, desert camp', 'Accommodation, meals, activities'),
    ('TOUR-CUL-004', 'Chinese Culture Immersion', 'China', 'Beijing', 'Cultural', 8, 7, 15, 15, 8500.00, '2025-01-15', 'Available', 'Great Wall, Forbidden City, tea ceremony', 'Accommodation, meals, guide'),
    -- Food Tours
    ('TOUR-FOO-001', 'Food Tour Bangkok', 'Thailand', 'Bangkok', 'Food', 2, 1, 10, 10, 4500.00, '2025-01-20', 'Available', 'Street food markets, cooking classes, temple visits', 'Accommodation, meals, guide'),
    ('TOUR-FOO-002', 'Italian Culinary Journey', 'Italy', 'Rome', 'Food', 4, 3, 8, 8, 9500.00, '2024-12-22', 'Available', 'Pasta making, wine tasting, local markets', 'Accommodation, meals, cooking classes'),
    ('TOUR-FOO-003', 'French Gastronomy Tour', 'France', 'Lyon', 'Food', 3, 2, 6, 6, 12000.00, '2025-01-08', 'Available', 'Michelin restaurants, cheese tasting, bakery visits', 'Accommodation, meals, tastings'),
    ('TOUR-FOO-004', 'Japanese Sushi Experience', 'Japan', 'Osaka', 'Food', 2, 1, 8, 8, 7500.00, '2024-12-30', 'Available', 'Sushi making, sake tasting, fish market tour', 'Accommodation, meals, classes'),
    -- Nature Tours
    ('TOUR-NAT-001', 'Amazon Rainforest', 'Peru', 'Iquitos', 'Nature', 6, 5, 8, 8, 10500.00, '2025-01-18', 'Available', 'Wildlife observation, canopy walks, river cruises', 'Accommodation, meals, guide'),
    ('TOUR-NAT-002', 'African Safari', 'Tanzania', 'Serengeti', 'Nature', 7, 6, 6, 6, 15000.00, '2024-12-26', 'Available', 'Big Five game drives, Maasai village visit', 'Accommodation, meals, safari vehicle'),
    ('TOUR-NAT-003', 'Costa Rica Eco Adventure', 'Costa Rica', 'San Jose', 'Nature', 5, 4, 10, 10, 8500.00, '2025-01-12', 'Available', 'Rainforest hikes, volcano tours, wildlife spotting', 'Accommodation, meals, activities'),
    ('TOUR-NAT-004', 'Norwegian Fjords', 'Norway', 'Bergen', 'Nature', 4, 3, 12, 12, 11500.00, '2025-01-25', 'Available', 'Fjord cruises, northern lights, glacier walks', 'Accommodation, meals, cruise'),
    -- Historical Tours
    ('TOUR-HIS-001', 'London Historical Walk', 'United Kingdom', 'London', 'Historical', 1, 0, 15, 15, 3500.00, '2024-12-22', 'Available', 'Tower of London, Westminster Abbey, British Museum', 'Entry tickets, guide, lunch'),
    ('TOUR-HIS-002', 'Ancient Rome Discovery', 'Italy', 'Rome', 'Historical', 3, 2, 12, 12, 6500.00, '2024-12-28', 'Available', 'Colosseum, Roman Forum, Vatican City', 'Accommodation, entry tickets, guide'),
    ('TOUR-HIS-003', 'Greek Mythology Tour', 'Greece', 'Athens', 'Historical', 4, 3, 10, 10, 7500.00, '2025-01-05', 'Available', 'Acropolis, Parthenon, ancient Agora', 'Accommodation, meals, guide'),
    ('TOUR-HIS-004', 'Medieval Castles Tour', 'Germany', 'Munich', 'Historical', 5, 4, 8, 8, 8500.00, '2025-01-15', 'Available', 'Neuschwanstein Castle, medieval towns', 'Accommodation, meals, transportation'),
    -- Beach Tours
    ('TOUR-BEA-001', 'Bali Beach Paradise', 'Indonesia', 'Bali', 'Beach', 4, 3, 6, 6, 7500.00, '2024-12-25', 'Available', 'Surfing lessons, beach hopping, temple visits', 'Accommodation, meals, activities'),
    ('TOUR-BEA-002', 'Maldives Island Hopping', 'Maldives', 'Male', 'Beach', 5, 4, 4, 4, 18000.00, '2025-01-10', 'Available', 'Private beaches, snorkeling, water sports', 'Accommodation, meals, activities'),
    ('TOUR-BEA-003', 'Caribbean Paradise', 'Barbados', 'Bridgetown', 'Beach', 6, 5, 8, 8, 12000.00, '2024-12-30', 'Available', 'Beach relaxation, diving, island tours', 'Accommodation, meals, activities'),
    ('TOUR-BEA-004', 'Hawaiian Island Adventure', 'USA', 'Honolulu', 'Beach', 7, 6, 10, 10, 14000.00, '2025-01-20', 'Available', 'Volcano tours, beach activities, luau dinner', 'Accommodation, meals, activities'),
    -- City Tours
    ('TOUR-CIT-001', 'New York City Explorer', 'USA', 'New York', 'City', 3, 2, 15, 15, 8500.00, '2024-12-20', 'Available', 'Times Square, Central Park, Broadway shows', 'Accommodation, tickets, guide'),
    ('TOUR-CIT-002', 'Paris City of Lights', 'France', 'Paris', 'City', 4, 3, 12, 12, 9500.00, '2024-12-28', 'Available', 'Eiffel Tower, Louvre, Seine cruise', 'Accommodation, meals, guide'),
    ('TOUR-CIT-003', 'Dubai Modern Marvels', 'UAE', 'Dubai', 'City', 3, 2, 10, 10, 11000.00, '2025-01-08', 'Available', 'Burj Khalifa, desert safari, shopping malls', 'Accommodation, meals, activities'),
    ('TOUR-CIT-004', 'Singapore Urban Discovery', 'Singapore', 'Singapore', 'City', 2, 1, 12, 12, 6500.00, '2025-01-15', 'Available', 'Marina Bay, Gardens by the Bay, Chinatown', 'Accommodation, meals, guide'),
    -- Wildlife Tours
    ('TOUR-WIL-001', 'Wildlife Safari Kenya', 'Kenya', 'Nairobi', 'Wildlife', 6, 5, 8, 8, 18000.00, '2025-01-15', 'Available', 'Masai Mara game drives, Big Five spotting', 'Accommodation, meals, safari vehicle'),
    ('TOUR-WIL-002', 'Galapagos Wildlife Tour', 'Ecuador', 'Quito', 'Wildlife', 8, 7, 6, 6, 22000.00, '2025-01-22', 'Available', 'Unique species, snorkeling, nature walks', 'Accommodation, meals, guide'),
    ('TOUR-WIL-003', 'Borneo Orangutan Safari', 'Malaysia', 'Kota Kinabalu', 'Wildlife', 5, 4, 8, 8, 12000.00, '2024-12-28', 'Available', 'Orangutan sanctuary, jungle trekking', 'Accommodation, meals, guide'),
    ('TOUR-WIL-004', 'Antarctic Wildlife Expedition', 'Antarctica', 'Ushuaia', 'Wildlife', 10, 9, 12, 12, 35000.00, '2025-02-01', 'Available', 'Penguins, whales, ice formations', 'Accommodation, meals, expedition'),
    -- Sightseeing Tours
    ('TOUR-SIG-001', 'Grand Canyon Spectacular', 'USA', 'Las Vegas', 'Sightseeing', 2, 1, 20, 20, 5500.00, '2024-12-25', 'Available', 'Helicopter tours, sunset viewing, hiking trails', 'Accommodation, transportation, guide'),
    ('TOUR-SIG-002', 'Swiss Alps Panorama', 'Switzerland', 'Zurich', 'Sightseeing', 4, 3, 15, 15, 12000.00, '2025-01-10', 'Available', 'Mountain railways, scenic views, cable cars', 'Accommodation, transportation, guide'),
    ('TOUR-SIG-003', 'Niagara Falls Wonder', 'Canada', 'Toronto', 'Sightseeing', 2, 1, 25, 25, 4500.00, '2024-12-30', 'Available', 'Waterfall views, boat rides, observation decks', 'Accommodation, activities, guide'),
    ('TOUR-SIG-004', 'Northern Lights Iceland', 'Iceland', 'Reykjavik', 'Sightseeing', 3, 2, 12, 12, 9500.00, '2025-01-18', 'Available', 'Aurora viewing, hot springs, glacier tours', 'Accommodation, transportation, guide'),
    -- Romantic Tours
    ('TOUR-ROM-001', 'Romantic Paris Evening', 'France', 'Paris', 'Romantic', 1, 0, 2, 2, 8500.00, '2024-12-15', 'Available', 'Candlelit dinner cruise on Seine River, Eiffel Tower visit', 'Dinner, transportation, guide'),
    ('TOUR-ROM-002', 'Romantic Santorini Sunset', 'Greece', 'Santorini', 'Romantic', 2, 1, 2, 2, 12000.00, '2024-12-20', 'Available', 'Private sunset viewing, wine tasting, couples spa', 'Accommodation, meals, spa'),
    ('TOUR-ROM-003', 'Venice Gondola Romance', 'Italy', 'Venice', 'Romantic', 2, 1, 2, 2, 9500.00, '2024-12-28', 'Available', 'Gondola rides, romantic dinners, St. Marks Square', 'Accommodation, meals, activities'),
    ('TOUR-ROM-004', 'Bali Romantic Retreat', 'Indonesia', 'Ubud', 'Romantic', 3, 2, 2, 2, 11000.00, '2025-01-05', 'Available', 'Couples massage, private villa, sunset dinner', 'Accommodation, meals, spa'),
    -- Family Tours
    ('TOUR-FAM-001', 'Family Fun Singapore', 'Singapore', 'Singapore', 'Family', 3, 2, 12, 12, 5500.00, '2024-12-28', 'Available', 'Universal Studios, Night Safari, Gardens by the Bay', 'Accommodation, tickets, meals'),
    ('TOUR-FAM-002', 'Disney World Orlando', 'USA', 'Orlando', 'Family', 5, 4, 15, 15, 12000.00, '2025-01-08', 'Available', 'Theme parks, character meets, water parks', 'Accommodation, tickets, meals'),
    ('TOUR-FAM-003', 'London Family Adventure', 'United Kingdom', 'London', 'Family', 4, 3, 10, 10, 8500.00, '2024-12-22', 'Available', 'Harry Potter studios, London Eye, museums', 'Accommodation, tickets, guide'),
    ('TOUR-FAM-004', 'Tokyo Family Discovery', 'Japan', 'Tokyo', 'Family', 4, 3, 8, 8, 9500.00, '2025-01-12', 'Available', 'Disneyland, robot shows, anime districts', 'Accommodation, tickets, guide'),
    -- Photography Tours
    ('TOUR-PHO-001', 'Photography Tour Iceland', 'Iceland', 'Reykjavik', 'Photography', 5, 4, 8, 8, 15000.00, '2025-01-05', 'Available', 'Northern Lights, waterfalls, glaciers, volcanic landscapes', 'Accommodation, equipment, guide'),
    ('TOUR-PHO-002', 'African Photo Safari', 'South Africa', 'Cape Town', 'Photography', 7, 6, 6, 6, 18000.00, '2025-01-20', 'Available', 'Wildlife photography, landscape shots, sunset captures', 'Accommodation, equipment, guide'),
    ('TOUR-PHO-003', 'Cherry Blossom Japan', 'Japan', 'Kyoto', 'Photography', 4, 3, 10, 10, 11000.00, '2025-03-15', 'Available', 'Sakura photography, temple shots, traditional gardens', 'Accommodation, equipment, guide'),
    ('TOUR-PHO-004', 'Patagonia Landscape Photo', 'Argentina', 'Buenos Aires', 'Photography', 8, 7, 8, 8, 16000.00, '2025-02-10', 'Available', 'Mountain photography, glacier shots, wildlife captures', 'Accommodation, equipment, guide')");
}

// Function to get tour image from local assets
function getTourImage($city, $country, $tour_type, $tour_id) {
    $city_lower = strtolower($city);
    $country_lower = strtolower($country);
    $type_lower = strtolower($tour_type);
    
    // Check for specific city images first
    $possible_images = [
        $city_lower . '.jpg',
        $country_lower . '.jpg',
        $type_lower . '.jpg'
    ];
    
    foreach ($possible_images as $image) {
        $image_path = 'assets/img/tours/' . $image;
        if (file_exists($image_path)) {
            return $image_path;
        }
    }
    
    // Fallback to any available image
    $tour_images = glob('assets/img/tours/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    if (!empty($tour_images)) {
        return $tour_images[0];
    }
    
    return 'assets/img/tours/default-tour.jpg';
}
?>

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
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.1rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
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
</style>

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
                        <option value="Sightseeing" <?php echo $type_filter == 'Sightseeing' ? 'selected' : ''; ?>>👁️ Sightseeing</option>
                        <option value="Romantic" <?php echo $type_filter == 'Romantic' ? 'selected' : ''; ?>>💕 Romantic</option>
                        <option value="Family" <?php echo $type_filter == 'Family' ? 'selected' : ''; ?>>👨‍👩‍👧‍👦 Family</option>
                        <option value="Photography" <?php echo $type_filter == 'Photography' ? 'selected' : ''; ?>>📸 Photography</option>
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
                        <?php 
                        $image_url = getTourImage($tour['city'], $tour['country'], $tour['tour_type'], $tour['tour_id']);
                        ?>
                        <div class="tour-image position-relative" style="background-image: url('<?php echo $image_url; ?>')">
                            <div style="background: rgba(0,0,0,0.3); padding: 10px; border-radius: 5px;">
                                <?php echo htmlspecialchars($tour['city']); ?>, <?php echo htmlspecialchars($tour['country']); ?>
                            </div>
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

<script>
function bookTour(tourId) {
    window.location.href = '?page=tour_booking&tour_id=' + tourId;
}
</script>

<?php $conn->close(); ?>