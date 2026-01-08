<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$stats = [];
$result = $conn->query("SELECT COUNT(*) as total FROM travel_bookings");
$stats['travel_bookings'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM hotel_bookings");
$stats['hotel_bookings'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM tour_bookings");
$stats['tour_bookings'] = $result->fetch_assoc()['total'];
$result = $conn->query("SELECT COUNT(*) as total FROM flight_bookings");
$stats['flight_bookings'] = $result->fetch_assoc()['total'];
$stats['total_bookings'] = $stats['travel_bookings'] + $stats['hotel_bookings'] + $stats['tour_bookings'] + $stats['flight_bookings'];
$result = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM travel_bookings");
$stats['travel_revenue'] = $result->fetch_assoc()['total'];
$result = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM hotel_bookings");
$stats['hotel_revenue'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM tour_bookings");
$stats['tour_revenue'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM flight_bookings");
$stats['flight_revenue'] = $result->fetch_assoc()['total'];

$stats['total_revenue'] = $stats['travel_revenue'] + $stats['hotel_revenue'] + $stats['tour_revenue'] + $stats['flight_revenue'];
$today = date('Y-m-d');
$result = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM travel_bookings WHERE DATE(booking_date) = '$today'");
$stats['today_travel'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM hotel_bookings WHERE DATE(booking_date) = '$today'");
$stats['today_hotel'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM tour_bookings WHERE DATE(booking_date) = '$today'");
$stats['today_tour'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM flight_bookings WHERE DATE(booking_date) = '$today'");
$stats['today_flight'] = $result->fetch_assoc()['total'];

$stats['today_revenue'] = $stats['today_travel'] + $stats['today_hotel'] + $stats['today_tour'] + $stats['today_flight'];

// Recent bookings
$recent_bookings = [];

$query = "
    (SELECT booking_id, traveler_name as customer_name, 'Travel' as type, total_amount, status, booking_date
     FROM travel_bookings ORDER BY booking_date DESC LIMIT 2)
    UNION ALL
    (SELECT booking_id, guest_name as customer_name, 'Hotel' as type, total_amount, status, booking_date
     FROM hotel_bookings ORDER BY booking_date DESC LIMIT 2)
    UNION ALL
    (SELECT booking_id, participant_name as customer_name, 'Tour' as type, total_amount, status, booking_date
     FROM tour_bookings ORDER BY booking_date DESC LIMIT 2)
    UNION ALL
    (SELECT booking_id, passenger_name as customer_name, 'Flight' as type, total_amount, status, booking_date
     FROM flight_bookings ORDER BY booking_date DESC LIMIT 2)
    ORDER BY booking_date DESC LIMIT 8
";

$result = $conn->query($query);
while($row = $result->fetch_assoc()) {
    $recent_bookings[] = $row;
}

// Get pending bookings
$result = $conn->query("SELECT COUNT(*) as total FROM travel_bookings WHERE status = 'Pending'");
$pending_travel = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM hotel_bookings WHERE status = 'Pending'");
$pending_hotel = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM tour_bookings WHERE status = 'Pending'");
$pending_tour = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM flight_bookings WHERE status = 'Pending'");
$pending_flight = $result->fetch_assoc()['total'];

$stats['pending_bookings'] = $pending_travel + $pending_hotel + $pending_tour + $pending_flight;

// Top destinations
$top_destinations = [];
$result = $conn->query("
    SELECT to_country, COUNT(*) as bookings 
    FROM travel_bookings 
    WHERE to_country != '' 
    GROUP BY to_country 
    ORDER BY bookings DESC 
    LIMIT 4
");
while($row = $result->fetch_assoc()) {
    $top_destinations[] = $row;
}

$conn->close();
?>

<div class="pagetitle">
    <h1>🌴 Paradise Travel - Dashboard</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>

<section class="section">
    <!-- Paradise Travel Services - Professional Travel Theme -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card trip-module-card">
                <div class="card-body">
                    <h2 class="card-title mb-4">🌴 Paradise Travel Services</h2>
                    <div class="row g-4">
                        <div class="col-md-3 col-sm-6">
                            <div class="module-card hotel-module">
                                <div class="module-icon">
                                    <i class="bi bi-building"></i>
                                </div>
                                <h3>Hotels</h3>
                                <p class="module-description">Find perfect stays</p>
                                <div class="module-stats">
                                    <span class="badge"><?php echo $stats['hotel_bookings']; ?> Bookings</span>
                                </div>
                                <a href="index.php?page=new_booking&type=hotel" class="module-action">
                                    Book Now <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6">
                            <div class="module-card flight-module">
                                <div class="module-icon">
                                    <i class="bi bi-airplane"></i>
                                </div>
                                <h3>Flights</h3>
                                <p class="module-description">Book flights worldwide</p>
                                <div class="module-stats">
                                    <span class="badge"><?php echo $stats['flight_bookings']; ?> Bookings</span>
                                </div>
                                <a href="index.php?page=new_booking&type=flight" class="module-action">
                                    Book Now <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6">
                            <div class="module-card travel-module">
                                <div class="module-icon">
                                    <i class="bi bi-compass"></i>
                                </div>
                                <h3>Travel</h3>
                                <p class="module-description">All travel services</p>
                                <div class="module-stats">
                                    <span class="badge"><?php echo $stats['travel_bookings']; ?> Bookings</span>
                                </div>
                                <a href="index.php?page=new_booking&type=travel" class="module-action">
                                    Book Now <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6">
                            <div class="module-card tour-module">
                                <div class="module-icon">
                                    <i class="bi bi-bus-front"></i>
                                </div>
                                <h3>Tours</h3>
                                <p class="module-description">Discover experiences</p>
                                <div class="module-stats">
                                    <span class="badge"><?php echo $stats['tour_bookings']; ?> Bookings</span>
                                </div>
                                <a href="index.php?page=new_booking&type=tour" class="module-action">
                                    Book Now <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Your Trip Management Starts Here -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card trip-management-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="card-title mb-2">✈️ Your Trip Management Starts Here</h2>
                            <p class="card-subtitle">Manage all bookings in one place - Today is <?php echo date('F j, Y'); ?></p>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Dashboard - Professional Colors -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card ocean-blue">
                <div class="stat-header">
                    <i class="bi bi-journal-check"></i>
                    <h3>Total Bookings</h3>
                </div>
                <div class="stat-content">
                    <h2><?php echo $stats['total_bookings']; ?></h2>
                    <p>All booking types</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card palm-green">
                <div class="stat-header">
                    <i class="bi bi-currency-dollar"></i>
                    <h3>Total Revenue</h3>
                </div>
                <div class="stat-content">
                    <h2>₱<?php echo number_format($stats['total_revenue'], 2); ?></h2>
                    <p>All-time earnings</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card sunset-orange">
                <div class="stat-header">
                    <i class="bi bi-clock-history"></i>
                    <h3>Pending</h3>
                </div>
                <div class="stat-content">
                    <h2><?php echo $stats['pending_bookings']; ?></h2>
                    <p>Bookings pending</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card lagoon-teal">
                <div class="stat-header">
                    <i class="bi bi-cash-stack"></i>
                    <h3>Revenue Today</h3>
                </div>
                <div class="stat-content">
                    <h2>₱<?php echo number_format($stats['today_revenue'], 2); ?></h2>
                    <p>Today's earnings</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings & Activity -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">📋 Recent Bookings</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Customer</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recent_bookings as $booking): ?>
                                <tr>
                                    <td><strong><?php echo $booking['booking_id']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                    <td>
                                        <span class="badge type-badge <?php echo strtolower($booking['type']); ?>">
                                            <?php echo $booking['type']; ?>
                                        </span>
                                    </td>
                                    <td><strong>₱<?php echo number_format($booking['total_amount'], 2); ?></strong></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                            <?php echo $booking['status']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="index.php?page=all_bookings" class="btn btn-outline-primary">
                            View All Bookings <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Top Destinations -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">📍 Top Destinations</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach($top_destinations as $index => $destination): ?>
                        <div class="col-md-6 mb-3">
                            <div class="destination-card">
                                <div class="d-flex align-items-center">
                                    <div class="destination-rank"><?php echo $index + 1; ?></div>
                                    <div class="ms-3">
                                        <h6 class="mb-0"><?php echo $destination['to_country']; ?></h6>
                                        <small class="text-muted"><?php echo $destination['bookings']; ?> bookings</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">⚡ Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <a href="index.php?page=new_booking&type=travel" class="quick-action-item">
                            <i class="bi bi-plus-circle"></i>
                            <span>New Travel Booking</span>
                        </a>
                        <a href="index.php?page=new_booking&type=hotel" class="quick-action-item">
                            <i class="bi bi-building-add"></i>
                            <span>New Hotel Booking</span>
                        </a>
                        <a href="index.php?page=new_booking&type=flight" class="quick-action-item">
                            <i class="bi bi-airplane-engines"></i>
                            <span>New Flight Booking</span>
                        </a>
                        <a href="index.php?page=new_booking&type=tour" class="quick-action-item">
                            <i class="bi bi-bus-front"></i>
                            <span>New Tour Booking</span>
                        </a>
                        <a href="index.php?page=agents" class="quick-action-item">
                            <i class="bi bi-people"></i>
                            <span>Manage Agents</span>
                        </a>
                        <a href="index.php?page=commissions" class="quick-action-item">
                            <i class="bi bi-cash-coin"></i>
                            <span>View Commissions</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Booking Summary -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">📊 Booking Summary</h3>
                </div>
                <div class="card-body">
                    <div class="summary-item">
                        <div class="summary-icon travel-icon">
                            <i class="bi bi-airplane"></i>
                        </div>
                        <div class="summary-content">
                            <h4><?php echo $stats['travel_bookings']; ?></h4>
                            <p>Travel Bookings</p>
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-icon hotel-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="summary-content">
                            <h4><?php echo $stats['hotel_bookings']; ?></h4>
                            <p>Hotel Bookings</p>
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-icon tour-icon">
                            <i class="bi bi-bus-front"></i>
                        </div>
                        <div class="summary-content">
                            <h4><?php echo $stats['tour_bookings']; ?></h4>
                            <p>Tour Bookings</p>
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-icon flight-icon">
                            <i class="bi bi-airplane-engines"></i>
                        </div>
                        <div class="summary-content">
                            <h4><?php echo $stats['flight_bookings']; ?></h4>
                            <p>Flight Bookings</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Professional Travel Color Scheme */
:root {
    --ocean-blue: #2D9CDB;
    --palm-green: #27AE60;
    --sunset-orange: #F2994A;
    --lagoon-teal: #56CCF2;
    --sand-beige: #F2C94C;
    --coral-red: #EB5757;
    --deep-blue: #2F80ED;
    --light-blue: #56CCF2;
}

/* Trip.com Style Dashboard */
.trip-module-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.trip-module-card .card-title {
    color: var(--deep-blue);
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 15px;
}

.module-card {
    background: white;
    border-radius: 10px;
    padding: 25px;
    text-align: center;
    transition: all 0.3s ease;
    height: 100%;
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
}

.module-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.module-card.hotel-module:hover {
    border-color: var(--palm-green);
}

.module-card.flight-module:hover {
    border-color: var(--ocean-blue);
}

.module-card.travel-module:hover {
    border-color: var(--sunset-orange);
}

.module-card.tour-module:hover {
    border-color: var(--lagoon-teal);
}

.module-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 30px;
    color: white;
}

.hotel-module .module-icon {
    background: linear-gradient(135deg, var(--palm-green) 0%, #8BC34A 100%);
}

.flight-module .module-icon {
    background: linear-gradient(135deg, var(--ocean-blue) 0%, #56CCF2 100%);
}

.travel-module .module-icon {
    background: linear-gradient(135deg, var(--sunset-orange) 0%, #F2994A 100%);
}

.tour-module .module-icon {
    background: linear-gradient(135deg, var(--lagoon-teal) 0%, #2D9CDB 100%);
}

.module-card h3 {
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 10px;
    color: #333;
}

.module-description {
    font-size: 0.95rem;
    color: #666;
    margin-bottom: 15px;
    line-height: 1.5;
}

.module-stats .badge {
    font-size: 0.85rem;
    padding: 5px 12px;
    border-radius: 20px;
    background: #f8f9fa;
    color: #666;
    border: 1px solid #e0e0e0;
    font-weight: 500;
}

.module-action {
    display: inline-block;
    color: white;
    text-decoration: none;
    font-weight: 600;
    padding: 8px 20px;
    border-radius: 25px;
    transition: all 0.3s ease;
    margin-top: 10px;
    border: none;
}

.hotel-module .module-action {
    background: var(--palm-green);
}

.flight-module .module-action {
    background: var(--ocean-blue);
}

.travel-module .module-action {
    background: var(--sunset-orange);
}

.tour-module .module-action {
    background: var(--lagoon-teal);
}

.module-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    color: white;
    text-decoration: none;
}

/* Trip Management Card */
.trip-management-card {
    background: linear-gradient(135deg, var(--deep-blue) 0%, var(--light-blue) 100%);
    border-radius: 12px;
    border: none;
    color: white;
}

.trip-management-card .card-title {
    color: white;
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.trip-management-card .card-subtitle {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1rem;
    margin-bottom: 0;
}

.system-status {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 0.9rem;
    backdrop-filter: blur(10px);
}

.system-status i {
    font-size: 1.2rem;
    color: #4CAF50;
}

/* Stat Cards */
.stat-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    border: none;
    transition: transform 0.3s ease;
    height: 100%;
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
}

.stat-card.ocean-blue::before {
    background: var(--ocean-blue);
}

.stat-card.palm-green::before {
    background: var(--palm-green);
}

.stat-card.sunset-orange::before {
    background: var(--sunset-orange);
}

.stat-card.lagoon-teal::before {
    background: var(--lagoon-teal);
}

.stat-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

.stat-header i {
    font-size: 2rem;
}

.stat-card.ocean-blue .stat-header i {
    color: var(--ocean-blue);
}

.stat-card.palm-green .stat-header i {
    color: var(--palm-green);
}

.stat-card.sunset-orange .stat-header i {
    color: var(--sunset-orange);
}

.stat-card.lagoon-teal .stat-header i {
    color: var(--lagoon-teal);
}

.stat-header h3 {
    font-size: 1.1rem;
    color: #666;
    margin: 0;
    font-weight: 600;
}

.stat-content h2 {
    font-size: 2.2rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 5px;
}

.stat-content p {
    color: #888;
    font-size: 0.9rem;
    margin: 0;
}

/* Type Badges */
.type-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.type-badge.travel {
    background: #FFF3E0;
    color: #EF6C00;
    border: 1px solid #FFCC80;
}

.type-badge.hotel {
    background: #E8F5E9;
    color: #2E7D32;
    border: 1px solid #A5D6A7;
}

.type-badge.tour {
    background: #E0F7FA;
    color: #00838F;
    border: 1px solid #80DEEA;
}

.type-badge.flight {
    background: #E3F2FD;
    color: #1565C0;
    border: 1px solid #90CAF9;
}

/* Status Badges */
.status-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-pending {
    background: #FFF3CD;
    color: #856404;
    border: 1px solid #FFEAA7;
}

.status-confirmed {
    background: #D4EDDA;
    color: #155724;
    border: 1px solid #C3E6CB;
}

status-completed {
    background: #D1ECF1;
    color: #0C5460;
    border: 1px solid #BEE5EB;
}

.status-cancelled {
    background: #F8D7DA;
    color: #721C24;
    border: 1px solid #F5C6CB;
}

/* Quick Actions */
.quick-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.quick-action-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px 15px;
    background: #f8f9fa;
    border-radius: 8px;
    color: #333;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.quick-action-item:hover {
    background: var(--ocean-blue);
    color: white;
    text-decoration: none;
    transform: translateX(5px);
    border-color: var(--ocean-blue);
}

.quick-action-item i {
    font-size: 1.2rem;
    width: 24px;
}

.quick-action-item span {
    font-weight: 500;
}

/* Destination Cards */
.destination-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.destination-card:hover {
    background: #e9ecef;
    border-color: var(--ocean-blue);
}

.destination-rank {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--ocean-blue) 0%, var(--light-blue) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 18px;
}

/* Summary Items */
.summary-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.summary-item:last-child {
    border-bottom: none;
}

.summary-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.travel-icon {
    background: linear-gradient(135deg, var(--sunset-orange) 0%, #F2994A 100%);
}

.hotel-icon {
    background: linear-gradient(135deg, var(--palm-green) 0%, #8BC34A 100%);
}

.tour-icon {
    background: linear-gradient(135deg, var(--lagoon-teal) 0%, #2D9CDB 100%);
}

.flight-icon {
    background: linear-gradient(135deg, var(--ocean-blue) 0%, #56CCF2 100%);
}

.summary-content h4 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    color: #333;
}

.summary-content p {
    color: #666;
    margin: 0;
    font-size: 0.9rem;
}

/* Card Headers */
.card-header {
    background: white;
    border-bottom: 2px solid #f0f0f0;
    padding: 20px 25px;
}

.card-header .card-title {
    margin: 0;
    font-weight: 700;
    color: #333;
    font-size: 1.3rem;
}

/* Table Styling */
.table {
    margin-bottom: 0;
}

.table thead th {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #495057;
    padding: 15px;
}

.table tbody td {
    padding: 15px;
    vertical-align: middle;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

/* Responsive Design */
@media (max-width: 768px) {
    .trip-module-card .card-title {
        font-size: 1.5rem;
    }
    
    .module-card {
        padding: 20px 15px;
    }
    
    .module-icon {
        width: 60px;
        height: 60px;
        font-size: 24px;
    }
    
    .trip-management-card .card-title {
        font-size: 1.5rem;
    }
    
    .trip-management-card .card-subtitle {
        font-size: 0.9rem;
    }
    
    .system-status {
        margin-top: 15px;
        width: 100%;
        justify-content: center;
    }
    
    .stat-content h2 {
        font-size: 1.8rem;
    }
}

@media (max-width: 576px) {
    .trip-module-card .row {
        margin: -5px;
    }
    
    .trip-module-card .col-sm-6 {
        padding: 5px;
    }
    
    .module-card {
        padding: 15px 10px;
    }
    
    .module-card h3 {
        font-size: 1.2rem;
    }
    
    .module-action {
        padding: 6px 15px;
        font-size: 0.9rem;
    }
    
    .stat-card {
        padding: 20px 15px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate module cards on scroll
    const moduleCards = document.querySelectorAll('.module-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 100);
            }
        });
    }, {
        threshold: 0.1
    });

    moduleCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.5s ease';
        observer.observe(card);
    });

    // Animate stat numbers
    const statNumbers = document.querySelectorAll('.stat-content h2');
    
    statNumbers.forEach(stat => {
        const originalText = stat.textContent;
        const isCurrency = originalText.includes('₱');
        const numericValue = parseInt(originalText.replace(/[^0-9]/g, ''));
        
        if (!isNaN(numericValue)) {
            stat.textContent = isCurrency ? '₱0' : '0';
            
            let current = 0;
            const increment = numericValue / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= numericValue) {
                    stat.textContent = originalText;
                    clearInterval(timer);
                } else {
                    const displayValue = Math.floor(current);
                    stat.textContent = isCurrency ? 
                        '₱' + displayValue.toLocaleString() : 
                        displayValue.toLocaleString();
                }
            }, 30);
        }
    });

    // Add hover effects to quick actions
    const quickActions = document.querySelectorAll('.quick-action-item');
    
    quickActions.forEach(action => {
        action.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
        });
        
        action.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });

    // Update time in trip management card
    function updateTime() {
        const now = new Date();
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        };
        const dateString = now.toLocaleDateString('en-US', options);
        
        const dateElement = document.querySelector('.trip-management-card .card-subtitle');
        if (dateElement) {
            dateElement.textContent = `Manage all bookings in one place - Today is ${dateString}`;
        }
    }
    
    updateTime();

    // Add pulse animation to system status
    const systemStatus = document.querySelector('.system-status i');
    if (systemStatus) {
        setInterval(() => {
            systemStatus.style.transform = 'scale(1.1)';
            setTimeout(() => {
                systemStatus.style.transform = 'scale(1)';
            }, 300);
        }, 3000);
    }
});
</script>
