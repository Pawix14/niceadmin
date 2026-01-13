<?php
// modules/hotels.php - Available Hotels for Booking

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

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_hotel'])) {
    $booking_id = 'HTL-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    $hotel_id = $conn->real_escape_string($_POST['hotel_id']);
    $guest_name = $conn->real_escape_string($_POST['guest_name']);
    $check_in = $conn->real_escape_string($_POST['check_in']);
    $check_out = $conn->real_escape_string($_POST['check_out']);
    $guests = (int)$_POST['guests'];
    $room_type = $conn->real_escape_string($_POST['room_type']);
    
    // Get hotel details
    $hotel_result = $conn->query("SELECT * FROM available_hotels WHERE id = '$hotel_id'");
    $hotel = $hotel_result->fetch_assoc();
    
    if ($hotel) {
        $nights = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
        $total_amount = $hotel['price_per_night'] * $nights;
        
        $sql = "INSERT INTO hotel_bookings (booking_id, customer_id, guest_name, hotel_name, country, city, room_type, check_in, check_out, guests, nights, total_amount, status) 
                VALUES ('$booking_id', 'CUST-NEW', '$guest_name', '{$hotel['hotel_name']}', '{$hotel['country']}', '{$hotel['city']}', '$room_type', '$check_in', '$check_out', $guests, $nights, $total_amount, 'Confirmed')";
        
        if ($conn->query($sql)) {
            $message = "✅ Hotel booked successfully! Booking ID: $booking_id";
            $message_type = "success";
        } else {
            $message = "❌ Error booking hotel: " . $conn->error;
            $message_type = "error";
        }
    }
}

// Get filters
$price_filter = isset($_GET['price']) ? $_GET['price'] : '';
$city_filter = isset($_GET['city']) ? $_GET['city'] : '';
$rating_filter = isset($_GET['rating']) ? $_GET['rating'] : '';

// Build query with filters
$where = ["1=1"];

if ($price_filter) {
    switch($price_filter) {
        case 'budget': $where[] = "price_per_night <= 3000"; break;
        case 'mid': $where[] = "price_per_night BETWEEN 3001 AND 8000"; break;
        case 'luxury': $where[] = "price_per_night > 8000"; break;
    }
}
if ($city_filter) $where[] = "city LIKE '%$city_filter%'";
if ($rating_filter) $where[] = "rating >= $rating_filter";

// Create available_hotels table if not exists and add sample data
$conn->query("CREATE TABLE IF NOT EXISTS available_hotels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hotel_name VARCHAR(200) NOT NULL,
    city VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    rating DECIMAL(2,1) DEFAULT 5.0,
    price_per_night DECIMAL(10,2) NOT NULL,
    amenities TEXT,
    description TEXT,
    available_rooms INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$check_hotels = $conn->query("SELECT COUNT(*) as count FROM available_hotels");
if ($check_hotels && $check_hotels->fetch_assoc()['count'] < 20) {
    $conn->query("DELETE FROM available_hotels");
    
    $conn->query("INSERT INTO available_hotels (hotel_name, city, country, rating, price_per_night, amenities, description, available_rooms) VALUES 
    ('Grand Hyatt Tokyo', 'Tokyo', 'Japan', 4.8, 6500.00, 'Free WiFi, Pool, Spa, Restaurant', 'Luxury hotel in the heart of Tokyo with stunning city views', 15),
    ('Marina Bay Sands', 'Singapore', 'Singapore', 4.9, 8500.00, 'Infinity Pool, Casino, Shopping, Spa', 'Iconic hotel with rooftop infinity pool overlooking the city', 12),
    ('The Ritz London', 'London', 'United Kingdom', 4.7, 12000.00, 'Afternoon Tea, Spa, Fine Dining, Concierge', 'Historic luxury hotel in Piccadilly with royal heritage', 8),
    ('Hotel Plaza Athénée', 'Paris', 'France', 4.8, 9500.00, 'Michelin Restaurant, Spa, Fashion Boutiques', 'Elegant Parisian palace hotel on Avenue Montaigne', 10),
    ('Burj Al Arab', 'Dubai', 'UAE', 5.0, 15000.00, 'Private Beach, Helicopter Pad, Butler Service', 'World\'s most luxurious hotel shaped like a sail', 6),
    ('The Peninsula Hong Kong', 'Hong Kong', 'Hong Kong', 4.6, 7500.00, 'Harbor Views, Spa, Rooftop Bar', 'Classic luxury hotel with Victoria Harbor views', 14),
    ('Four Seasons Bali', 'Bali', 'Indonesia', 4.9, 5500.00, 'Private Villas, Beach Access, Spa', 'Tropical paradise resort with traditional Balinese design', 20),
    ('The St. Regis New York', 'New York', 'USA', 4.7, 11000.00, 'Central Park Views, Butler Service, Fine Dining', 'Timeless luxury in the heart of Manhattan', 9),
    ('Mandarin Oriental Bangkok', 'Bangkok', 'Thailand', 4.8, 4500.00, 'River Views, Thai Spa, Cultural Tours', 'Riverside luxury with authentic Thai hospitality', 18),
    ('The Westin Sydney', 'Sydney', 'Australia', 4.5, 6000.00, 'Harbor Bridge Views, Pool, Fitness Center', 'Modern hotel with iconic Sydney Harbor views', 16),
    ('Hotel Danieli Venice', 'Venice', 'Italy', 4.6, 8800.00, 'Canal Views, Historic Palace, Fine Dining', 'Venetian palace hotel on the Grand Canal', 7),
    ('The Oberoi Mumbai', 'Mumbai', 'India', 4.7, 4800.00, 'Ocean Views, Spa, Business Center', 'Contemporary luxury overlooking the Arabian Sea', 13),
    ('Shangri-La Hotel Paris', 'Paris', 'France', 4.5, 7200.00, 'Eiffel Tower Views, Asian Spa, Gourmet Restaurant', 'Asian hospitality meets Parisian elegance', 11),
    ('The Langham London', 'London', 'United Kingdom', 4.4, 5800.00, 'Traditional Afternoon Tea, Spa, Historic Charm', 'Victorian grandeur in the heart of London', 12),
    ('Park Hyatt Tokyo', 'Tokyo', 'Japan', 4.9, 9800.00, 'Mount Fuji Views, Minimalist Design, Spa', 'Serene luxury with Japanese aesthetics', 8),
    ('The St. Regis Maldives', 'Male', 'Maldives', 5.0, 18000.00, 'Overwater Villas, Private Beach, Water Sports', 'Ultimate tropical luxury in crystal clear waters', 5),
    ('Raffles Hotel Singapore', 'Singapore', 'Singapore', 4.8, 14000.00, 'Colonial Heritage, Long Bar, Tropical Gardens', 'Legendary colonial hotel with timeless elegance', 6),
    ('The Savoy London', 'London', 'United Kingdom', 4.6, 10500.00, 'Thames Views, Art Deco Style, Famous Bar', 'Iconic luxury hotel on the Strand', 9),
    ('Aman Tokyo', 'Tokyo', 'Japan', 4.9, 16000.00, 'Zen Design, City Views, Holistic Spa', 'Urban sanctuary with traditional Japanese elements', 4),
    ('Conrad Maldives', 'Male', 'Maldives', 4.8, 22000.00, 'Underwater Restaurant, Dolphin Lagoon, Spa', 'Luxury resort on pristine coral atoll', 3)");
}

$hotels = $conn->query("SELECT * FROM available_hotels WHERE " . implode(' AND ', $where) . " ORDER BY rating DESC, price_per_night ASC");
$cities = $conn->query("SELECT DISTINCT city FROM available_hotels ORDER BY city");

function getHotelImage($city, $country) {
    $city_lower = strtolower($city);
    
    $hotel_images = [
        'tokyo' => 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=400&h=300&fit=crop',
        'singapore' => 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=400&h=300&fit=crop',
        'london' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=400&h=300&fit=crop',
        'paris' => 'https://images.unsplash.com/photo-1502602898536-47ad22581b52?w=400&h=300&fit=crop',
        'dubai' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=400&h=300&fit=crop',
        'hong kong' => 'https://images.unsplash.com/photo-1536431311719-398b6704d4cc?w=400&h=300&fit=crop',
        'bali' => 'https://images.unsplash.com/photo-1537953773345-d172ccf13cf1?w=400&h=300&fit=crop',
        'new york' => 'https://images.unsplash.com/photo-1496417263034-38ec4f0b665a?w=400&h=300&fit=crop',
        'bangkok' => 'https://images.unsplash.com/photo-1563492065-1a5a6e0d8a8b?w=400&h=300&fit=crop',
        'sydney' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&h=300&fit=crop',
        'venice' => 'https://images.unsplash.com/photo-1523906834658-6e24ef2386f9?w=400&h=300&fit=crop',
        'mumbai' => 'https://images.unsplash.com/photo-1570168007204-dfb528c6958f?w=400&h=300&fit=crop',
        'male' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&h=300&fit=crop'
    ];
    
    return isset($hotel_images[$city_lower]) ? $hotel_images[$city_lower] : 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400&h=300&fit=crop';
}
?>

<style>
    .hotel-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s;
        height: 100%;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        cursor: pointer;
    }
    .hotel-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .hotel-image {
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
        position: relative;
    }
    .price-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255,255,255,0.95);
        color: #666;
        padding: 8px 15px;
        border-radius: 25px;
        font-weight: bold;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .rating-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: rgba(255,255,255,0.95);
        color: #666;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.9rem;
        font-weight: bold;
    }
    .filter-card {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 30px;
        border: 1px solid #e0e0e0;
    }
    .stats-card {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .btn-primary {
        background-color: #666;
        border-color: #666;
    }
    .btn-primary:hover {
        background-color: #555;
        border-color: #555;
    }
    .btn-outline-primary {
        color: #666;
        border-color: #666;
    }
    .btn-outline-primary:hover {
        background-color: #666;
        border-color: #666;
        color: white;
    }
    .form-select, .form-control {
        border-radius: 10px;
        border: 1px solid #e0e0e0;
    }
    .available-badge {
        background: #f8f9fa;
        color: #666;
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 0.8rem;
        border: 1px solid #e0e0e0;
    }
    .card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
    }
    .card-header {
        border-bottom: 1px solid #e0e0e0;
        background: #f8f9fa;
    }
    .text-primary {
        color: #666 !important;
    }
</style>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-5 fw-bold">🏨 Book Luxury Hotels</h1>
            <p class="text-muted">Discover and book amazing hotels worldwide</p>
        </div>
    </div>

    <?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3><?php echo $hotels ? $hotels->num_rows : 0; ?></h3>
                        <p class="mb-0">Available Hotels</p>
                    </div>
                    <i class="bi bi-building display-6"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <?php 
                        $cities_count = $conn->query("SELECT COUNT(DISTINCT city) as count FROM available_hotels");
                        $total_cities = $cities_count ? $cities_count->fetch_assoc()['count'] : 0;
                        ?>
                        <h3><?php echo $total_cities; ?></h3>
                        <p class="mb-0">Cities</p>
                    </div>
                    <i class="bi bi-geo-alt display-6"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <?php 
                        $avg_price = $conn->query("SELECT AVG(price_per_night) as avg FROM available_hotels");
                        $average = $avg_price ? $avg_price->fetch_assoc()['avg'] : 0;
                        ?>
                        <h3>₱<?php echo number_format($average, 0); ?></h3>
                        <p class="mb-0">Avg. Price/Night</p>
                    </div>
                    <i class="bi bi-cash-coin display-6"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <?php 
                        $luxury_count = $conn->query("SELECT COUNT(*) as count FROM available_hotels WHERE price_per_night > 8000");
                        $luxury = $luxury_count ? $luxury_count->fetch_assoc()['count'] : 0;
                        ?>
                        <h3><?php echo $luxury; ?></h3>
                        <p class="mb-0">Luxury Hotels</p>
                    </div>
                    <i class="bi bi-star display-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <h5 class="mb-3">🔍 Find Your Perfect Hotel</h5>
        <form method="GET" action="">
            <input type="hidden" name="page" value="hotels">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Price Range</label>
                    <select name="price" class="form-select">
                        <option value="">All Prices</option>
                        <option value="budget" <?php echo $price_filter === 'budget' ? 'selected' : ''; ?>>Budget (≤₱3,000/night)</option>
                        <option value="mid" <?php echo $price_filter === 'mid' ? 'selected' : ''; ?>>Mid-range (₱3,001-8,000/night)</option>
                        <option value="luxury" <?php echo $price_filter === 'luxury' ? 'selected' : ''; ?>>Luxury (>₱8,000/night)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">City</label>
                    <select name="city" class="form-select">
                        <option value="">All Cities</option>
                        <?php if ($cities): ?>
                            <?php while ($city = $cities->fetch_assoc()): ?>
                                <option value="<?php echo $city['city']; ?>" <?php echo $city_filter === $city['city'] ? 'selected' : ''; ?>>
                                    <?php echo $city['city']; ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Minimum Rating</label>
                    <select name="rating" class="form-select">
                        <option value="">Any Rating</option>
                        <option value="4.5" <?php echo $rating_filter === '4.5' ? 'selected' : ''; ?>>4.5+ Stars</option>
                        <option value="4.7" <?php echo $rating_filter === '4.7' ? 'selected' : ''; ?>>4.7+ Stars</option>
                        <option value="4.9" <?php echo $rating_filter === '4.9' ? 'selected' : ''; ?>>4.9+ Stars</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">🔍 Search Hotels</button>
                    <a href="?page=hotels" class="btn btn-outline-secondary">Clear Filters</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Hotels Grid -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Available Hotels (<?php echo $hotels ? $hotels->num_rows : 0; ?> found)</h4>
            </div>
            
            <?php if ($hotels && $hotels->num_rows > 0): ?>
            <div class="row g-4">
                <?php while($hotel = $hotels->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="hotel-card" onclick="bookHotel(<?php echo $hotel['id']; ?>, '<?php echo addslashes($hotel['hotel_name']); ?>', '<?php echo $hotel['city']; ?>', <?php echo $hotel['price_per_night']; ?>)">
                        <?php $image_url = getHotelImage($hotel['city'], $hotel['country']); ?>
                        <div class="hotel-image" style="background-image: url('<?php echo $image_url; ?>')">
                            <div class="rating-badge">
                                <i class="bi bi-star-fill"></i> <?php echo $hotel['rating']; ?>
                            </div>
                            <div class="price-badge">₱<?php echo number_format($hotel['price_per_night']); ?>/night</div>
                        </div>
                        <div class="p-4">
                            <h5 class="mb-2"><?php echo htmlspecialchars($hotel['hotel_name']); ?></h5>
                            <p class="text-muted mb-2">
                                <i class="bi bi-geo-alt"></i> <?php echo $hotel['city']; ?>, <?php echo $hotel['country']; ?>
                            </p>
                            
                            <p class="text-muted small mb-3"><?php echo htmlspecialchars($hotel['description']); ?></p>
                            
                            <div class="mb-3">
                                <small class="text-muted">Amenities:</small>
                                <div class="small"><?php echo htmlspecialchars($hotel['amenities']); ?></div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="available-badge">
                                    <?php echo $hotel['available_rooms']; ?> rooms available
                                </span>
                                <button class="btn btn-primary btn-sm" onclick="event.stopPropagation(); bookHotel(<?php echo $hotel['id']; ?>, '<?php echo addslashes($hotel['hotel_name']); ?>', '<?php echo $hotel['city']; ?>', <?php echo $hotel['price_per_night']; ?>)">
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
                <i class="bi bi-building display-1 text-muted"></i>
                <h4 class="mt-3">No hotels found</h4>
                <p class="text-muted">Try adjusting your filters to find available hotels</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Book Hotel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="book_hotel" value="1">
                    <input type="hidden" name="hotel_id" id="hotel_id">
                    
                    <div class="mb-3">
                        <h6 id="hotel_name_display"></h6>
                        <p class="text-muted" id="hotel_location"></p>
                        <p class="text-primary fw-bold" id="hotel_price"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Guest Name *</label>
                        <input type="text" name="guest_name" class="form-control" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Check-in Date *</label>
                            <input type="date" name="check_in" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Check-out Date *</label>
                            <input type="date" name="check_out" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Number of Guests *</label>
                            <select name="guests" class="form-select" required>
                                <option value="1">1 Guest</option>
                                <option value="2" selected>2 Guests</option>
                                <option value="3">3 Guests</option>
                                <option value="4">4 Guests</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Room Type *</label>
                            <select name="room_type" class="form-select" required>
                                <option value="Single">Single Room</option>
                                <option value="Double" selected>Double Room</option>
                                <option value="Suite">Suite</option>
                                <option value="Deluxe">Deluxe Room</option>
                                <option value="Family">Family Room</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function bookHotel(hotelId, hotelName, city, pricePerNight) {
    document.getElementById('hotel_id').value = hotelId;
    document.getElementById('hotel_name_display').textContent = hotelName;
    document.getElementById('hotel_location').textContent = city;
    document.getElementById('hotel_price').textContent = '₱' + pricePerNight.toLocaleString() + ' per night';
    
    const modal = new bootstrap.Modal(document.getElementById('bookingModal'));
    modal.show();
}

// Auto-calculate check-out date
document.addEventListener('DOMContentLoaded', function() {
    const checkinInput = document.querySelector('input[name="check_in"]');
    const checkoutInput = document.querySelector('input[name="check_out"]');
    
    if (checkinInput && checkoutInput) {
        checkinInput.addEventListener('change', function() {
            const checkinDate = new Date(this.value);
            checkinDate.setDate(checkinDate.getDate() + 2); // Default 2 nights
            checkoutInput.value = checkinDate.toISOString().split('T')[0];
        });
    }
});
</script>

<?php $conn->close(); ?>