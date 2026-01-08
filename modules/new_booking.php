<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$booking_type = isset($_GET['type']) ? $_GET['type'] : 'travel';
$booking_type = in_array($booking_type, ['travel', 'hotel', 'tour', 'flight']) ? $booking_type : 'travel';

// Set allowed specializations based on booking type
switch($booking_type) {
    case 'travel':
        $allowed_specializations = ['All', 'Travel'];
        break;
    case 'hotel':
        $allowed_specializations = ['All', 'Hotels'];
        break;
    case 'tour':
        $allowed_specializations = ['All', 'Tours'];
        break;
    case 'flight':
        $allowed_specializations = ['All', 'Flights'];
        break;
    default:
        $allowed_specializations = ['All'];
        break;
}

$specializations_str = "'" . implode("','", $allowed_specializations) . "'";
$agents_result = $conn->query("SELECT agent_id, agent_name, commission_rate FROM travel_agents WHERE status = 'Active' AND specialization IN ($specializations_str) ORDER BY agent_name");

$countries_result = $conn->query("SELECT * FROM countries ORDER BY country_name");
$cities_result = $conn->query("SELECT c.country_code, ci.city_name FROM cities ci INNER JOIN countries c ON ci.country_code = c.country_code ORDER BY c.country_name, ci.city_name");

$cities_by_country = [];
while ($row = $cities_result->fetch_assoc()) {
    $cities_by_country[$row['country_code']][] = $row['city_name'];
}

$airports = [
    'MNL' => ['name' => 'Manila (MNL)', 'airlines' => ['Philippine Airlines', 'Cebu Pacific', 'PAL Express']],
    'CEB' => ['name' => 'Cebu (CEB)', 'airlines' => ['Philippine Airlines', 'Cebu Pacific']],
    'DVO' => ['name' => 'Davao (DVO)', 'airlines' => ['Philippine Airlines', 'Cebu Pacific']],
    'NRT' => ['name' => 'Tokyo (NRT)', 'airlines' => ['Japan Airlines', 'All Nippon Airways']],
    'HKG' => ['name' => 'Hong Kong (HKG)', 'airlines' => ['Cathay Pacific', 'Hong Kong Airlines']],
    'SIN' => ['name' => 'Singapore (SIN)', 'airlines' => ['Singapore Airlines', 'Scoot']],
    'BKK' => ['name' => 'Bangkok (BKK)', 'airlines' => ['Thai Airways', 'AirAsia']],
    'ICN' => ['name' => 'Seoul (ICN)', 'airlines' => ['Korean Air', 'Asiana Airlines']],
    'KUL' => ['name' => 'Kuala Lumpur (KUL)', 'airlines' => ['Malaysia Airlines', 'AirAsia']],
    'CGK' => ['name' => 'Jakarta (CGK)', 'airlines' => ['Garuda Indonesia', 'Lion Air']]
];

$hotels = [
    'AUS' => ['Sydney Harbour Marriott', 'The Langham Sydney', 'Park Hyatt Sydney', 'Shangri-La Sydney', 'The Rocks Hotel Sydney'],
    'CAN' => ['Fairmont Pacific Rim', 'Rosewood Hotel Georgia', 'The Ritz-Carlton Toronto', 'Four Seasons Hotel Toronto', 'Hotel Arts Barcelona (Vancouver)'],
    'FRA' => ['Hotel Plaza Athenee', 'Le Meurice', 'The Ritz Paris', 'George V', 'Shangri-La Paris'],
    'GBR' => ['The Savoy', 'Claridge\'s', 'The Ritz London', 'The Dorchester', 'The Langham London'],
    'GER' => ['Hotel Adlon Kempinski', 'The Ritz-Carlton Berlin', 'Mandarin Oriental Munich', 'Hotel Vier Jahreszeiten Kempinski', 'Steigenberger Hotel Hamburg'],
    'JPN' => ['Tokyo Station Hotel', 'Park Hyatt Tokyo', 'The Ritz-Carlton Tokyo', 'Conrad Tokyo', 'Grand Hyatt Tokyo'],
    'PHL' => ['Shangri-La Makati', 'Red Planet Makati', 'Sofitel Philippine Plaza', 'The Fort Bonifacio', 'Makati Shangri-La'],
    'SGP' => ['Marina Bay Sands', 'Raffles Hotel Singapore', 'Shangri-La Hotel Singapore', 'The Fullerton Bay Hotel', 'Park Royal on Pickering'],
    'UAE' => ['Burj Al Arab Jumeirah', 'Armani Hotel Dubai', 'Atlantis The Palm', 'Jumeirah Beach Hotel', 'Palace Downtown Dubai'],
    'USA' => ['The Plaza Hotel', 'The Ritz-Carlton New York', 'Four Seasons Hotel New York', 'Mandarin Oriental New York', 'Waldorf Astoria New York']
];

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_type = $conn->real_escape_string($_POST['booking_type']);
    $agent_id = $conn->real_escape_string($_POST['agent_id']);
    $status = $conn->real_escape_string($_POST['status']);

    $agent_commission = 0;

    switch($booking_type) {
        case 'travel':
            $traveler_name = $conn->real_escape_string($_POST['traveler_name']);
            $travel_type = $conn->real_escape_string($_POST['travel_type']);
            $from_country = $conn->real_escape_string($_POST['from_country']);
            $from_city = $conn->real_escape_string($_POST['from_city']);
            $to_country = $conn->real_escape_string($_POST['to_country']);
            $to_city = $conn->real_escape_string($_POST['to_city']);
            $total_amount = (float)$_POST['booking_amount'];
            
            $booking_id = 'TRV-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            
            $agent_commission = 0;
            if (!empty($agent_id) && $total_amount > 0) {
                $agent_data = $conn->query("SELECT commission_rate FROM travel_agents WHERE agent_id = '$agent_id'")->fetch_assoc();
                $commission_rate = $agent_data['commission_rate'];
                $agent_commission = ($total_amount * $commission_rate) / 100;
            }
            
            $sql = "INSERT INTO travel_bookings (booking_id, traveler_name, travel_type, from_country, from_city, to_country, to_city, total_amount, agent_id, agent_commission, status) 
                    VALUES ('$booking_id', '$traveler_name', '$travel_type', '$from_country', '$from_city', '$to_country', '$to_city', $total_amount, '$agent_id', $agent_commission, '$status')";
            break;
            
        case 'hotel':
            $guest_name = $conn->real_escape_string($_POST['guest_name']);
            $hotel_name = $conn->real_escape_string($_POST['hotel_name']);
            $country = $conn->real_escape_string($_POST['country']);
            $city = $conn->real_escape_string($_POST['city']);
            $room_type = $conn->real_escape_string($_POST['room_type']);
            $check_in = $conn->real_escape_string($_POST['check_in']);
            $check_out = $conn->real_escape_string($_POST['check_out']);
            $num_guests = (int)$_POST['num_guests'];
            $total_amount = (float)$_POST['total_price'];
            
            $booking_id = 'HOT-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            
            $agent_commission = 0;
            if (!empty($agent_id) && $total_amount > 0) {
                $agent_data = $conn->query("SELECT commission_rate FROM travel_agents WHERE agent_id = '$agent_id'")->fetch_assoc();
                $commission_rate = $agent_data['commission_rate'];
                $agent_commission = ($total_amount * $commission_rate) / 100;
            }
            
            $sql = "INSERT INTO hotel_bookings (booking_id, guest_name, hotel_name, country, city, room_type, check_in, check_out, guests, total_amount, agent_id, agent_commission, status)
                    VALUES ('$booking_id', '$guest_name', '$hotel_name', '$country', '$city', '$room_type', '$check_in', '$check_out', $num_guests, $total_amount, '$agent_id', $agent_commission, '$status')";
            break;
            
        case 'tour':
            $participant_name = $conn->real_escape_string($_POST['tour_name']);
            $country = $conn->real_escape_string($_POST['country']);
            $city = $conn->real_escape_string($_POST['city']);
            $activity_type = $conn->real_escape_string($_POST['activity_type']);
            $duration_hours = (int)$_POST['duration_hours'];
            $price_per_person = (float)$_POST['price_per_person'];
            $participants = (int)$_POST['participants'];
            $tour_date = $conn->real_escape_string($_POST['tour_date']);

            $total_amount = $price_per_person * $participants;

            $booking_id = 'TOUR-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

            $agent_commission = 0;
            if (!empty($agent_id) && $total_amount > 0) {
                $agent_data = $conn->query("SELECT commission_rate FROM travel_agents WHERE agent_id = '$agent_id'")->fetch_assoc();
                $commission_rate = $agent_data['commission_rate'];
                $agent_commission = ($total_amount * $commission_rate) / 100;
            }

            $sql = "INSERT INTO tour_bookings (booking_id, participant_name, tour_name, country, city, tour_type, duration_hours, participants, price_per_person, total_amount, tour_date, agent_id, agent_commission, status)
                    VALUES ('$booking_id', '$participant_name', '$participant_name', '$country', '$city', '$activity_type', $duration_hours, $participants, $price_per_person, $total_amount, '$tour_date', '$agent_id', $agent_commission, '$status')";
            break;
            
        case 'flight':
            $passenger_name = $conn->real_escape_string($_POST['passenger_name']);
            $airline = $conn->real_escape_string($_POST['airline']);
            $flight_number = $conn->real_escape_string($_POST['flight_number']);
            $departure_airport = $conn->real_escape_string($_POST['departure_airport']);
            $arrival_airport = $conn->real_escape_string($_POST['arrival_airport']);
            $departure_date = $conn->real_escape_string($_POST['departure_date']);
            $arrival_date = $conn->real_escape_string($_POST['arrival_date']);
            $seat_class = $conn->real_escape_string($_POST['seat_class']);
            $total_amount = (float)$_POST['fare'];
            
            $booking_id = 'FLT-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            
            $agent_commission = 0;
            if (!empty($agent_id) && $total_amount > 0) {
                $agent_data = $conn->query("SELECT commission_rate FROM travel_agents WHERE agent_id = '$agent_id'")->fetch_assoc();
                $commission_rate = $agent_data['commission_rate'];
                $agent_commission = ($total_amount * $commission_rate) / 100;
            }
            
            $sql = "INSERT INTO flight_bookings (booking_id, passenger_name, airline, flight_number, departure_airport, arrival_airport, departure_date, arrival_date, seat_class, total_amount, agent_id, agent_commission, status) 
                    VALUES ('$booking_id', '$passenger_name', '$airline', '$flight_number', '$departure_airport', '$arrival_airport', '$departure_date', '$arrival_date', '$seat_class', $total_amount, '$agent_id', $agent_commission, '$status')";
            break;
    }
    
    if (isset($sql) && $conn->query($sql)) {
        if (!empty($agent_id) && in_array($booking_type, ['travel', 'hotel', 'flight'])) {
            $conn->query("UPDATE travel_agents SET total_bookings = total_bookings + 1 WHERE agent_id = '$agent_id'");
        }
        
        $message = "✅ " . ucfirst($booking_type) . " booking created successfully! ";
        $message .= "ID: <strong>" . $booking_id . "</strong>";
        
        if (!empty($agent_id) && $agent_commission > 0) {
            $agent_data = $conn->query("SELECT commission_rate FROM travel_agents WHERE agent_id = '$agent_id'")->fetch_assoc();
            $commission_rate = $agent_data['commission_rate'];
            
            $message .= "<br>📊 Agent Commission: <strong>₱" . number_format($agent_commission, 2) . "</strong> (" . $commission_rate . "% of ₱" . number_format($total_amount, 2) . ")";
        }
        
        $message_type = "success";
        
    } else {
        $message = "❌ Error creating booking: " . $conn->error;
        $message_type = "error";
    }
}
?>

<div class="pagetitle">
  <h1>New Booking</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">New Booking</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          
          <?php if ($message): ?>
          <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          <?php endif; ?>
          
          <ul class="nav nav-tabs nav-tabs-bordered mb-4" id="bookingTabs">
            <li class="nav-item">
              <button class="nav-link <?php echo $booking_type == 'travel' ? 'active' : ''; ?>" data-bs-toggle="tab" data-bs-target="#travel-tab">
                <i class="bi bi-airplane me-1"></i> Travel
              </button>
            </li>
            <li class="nav-item">
              <button class="nav-link <?php echo $booking_type == 'hotel' ? 'active' : ''; ?>" data-bs-toggle="tab" data-bs-target="#hotel-tab">
                <i class="bi bi-building me-1"></i> Hotel
              </button>
            </li>
            <li class="nav-item">
              <button class="nav-link <?php echo $booking_type == 'tour' ? 'active' : ''; ?>" data-bs-toggle="tab" data-bs-target="#tour-tab">
                <i class="bi bi-bus-front me-1"></i> Tour
              </button>
            </li>
            <li class="nav-item">
              <button class="nav-link <?php echo $booking_type == 'flight' ? 'active' : ''; ?>" data-bs-toggle="tab" data-bs-target="#flight-tab">
                <i class="bi bi-airplane-engines me-1"></i> Flight
              </button>
            </li>
          </ul>
          
          <div class="tab-content pt-2">
            
            <div class="tab-pane fade <?php echo $booking_type == 'travel' ? 'show active' : ''; ?>" id="travel-tab">
              <form method="POST" action="">
                <input type="hidden" name="booking_type" value="travel">
                
                <h5 class="card-title text-primary mb-4"><i class="bi bi-airplane"></i> Travel Booking</h5>
                
                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="traveler_name" class="form-label">Traveler Name *</label>
                    <input type="text" class="form-control" id="traveler_name" name="traveler_name" required placeholder="Enter traveler full name">
                  </div>
                  
                  <div class="col-md-6">
                    <label for="travel_type" class="form-label">Travel Type *</label>
                    <select class="form-select" id="travel_type" name="travel_type" required>
                      <option value="">Select travel type...</option>
                      <option value="Airplane">✈️ Airplane</option>
                      <option value="Ship">🛳️ Ship/Cruise</option>
                      <option value="Bus">🚌 Bus</option>
                      <option value="Train">🚂 Train</option>
                      <option value="Car">🚗 Car/Rental</option>
                    </select>
                  </div>
                  
                  <div class="col-md-6">
                    <label for="from_country" class="form-label">From Country *</label>
                    <select class="form-select" id="from_country" name="from_country" required onchange="updateCities('from')">
                      <option value="">Select country...</option>
                      <?php
                      $countries_result->data_seek(0);
                      while($country = $countries_result->fetch_assoc()): ?>
                      <option value="<?php echo $country['country_name']; ?>" data-code="<?php echo $country['country_code']; ?>">
                        <?php echo htmlspecialchars($country['country_name']); ?>
                      </option>
                      <?php endwhile; ?>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label for="from_city" class="form-label">From City *</label>
                    <select class="form-select" id="from_city" name="from_city" required disabled>
                      <option value="">Select country first</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label for="to_country" class="form-label">To Country *</label>
                    <select class="form-select" id="to_country" name="to_country" required onchange="updateCities('to')">
                      <option value="">Select country...</option>
                      <?php
                      $countries_result->data_seek(0);
                      while($country = $countries_result->fetch_assoc()): ?>
                      <option value="<?php echo $country['country_name']; ?>" data-code="<?php echo $country['country_code']; ?>">
                        <?php echo htmlspecialchars($country['country_name']); ?>
                      </option>
                      <?php endwhile; ?>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label for="to_city" class="form-label">To City *</label>
                    <select class="form-select" id="to_city" name="to_city" required disabled>
                      <option value="">Select country first</option>
                    </select>
                  </div>
                  
                  <div class="col-md-6">
                    <label for="booking_amount" class="form-label">Booking Amount (₱) *</label>
                    <div class="input-group">
                      <span class="input-group-text">₱</span>
                      <input type="number" class="form-control" id="booking_amount" name="booking_amount" step="0.01" min="0" required oninput="updateCommission('travel')">
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <label for="agent_id" class="form-label">Travel Agent (Optional)</label>
                    <select class="form-select" id="agent_id" name="agent_id" onchange="updateCommission('travel')">
                      <option value="">No agent / Direct booking</option>
                      <?php
                      $agents_result->data_seek(0);
                      while($agent = $agents_result->fetch_assoc()): ?>
                      <option value="<?php echo $agent['agent_id']; ?>" data-rate="<?php echo $agent['commission_rate']; ?>">
                        <?php echo htmlspecialchars($agent['agent_name']); ?> (<?php echo $agent['commission_rate']; ?>%)
                      </option>
                      <?php endwhile; ?>
                    </select>
                  </div>
                  
                  <div class="col-12">
                    <div class="alert alert-info" id="commissionInfoTravel" style="display: none;">
                      <i class="bi bi-info-circle"></i> 
                      Agent Commission: <span id="commissionAmountTravel">₱0.00</span> 
                      (<span id="commissionRateTravel">0</span>% of total amount)
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <label for="status" class="form-label">Status *</label>
                    <select class="form-select" id="status" name="status" required>
                      <option value="Pending">⏳ Pending</option>
                      <option value="Confirmed" selected>✅ Confirmed</option>
                      <option value="Completed">🏁 Completed</option>
                      <option value="Cancelled">❌ Cancelled</option>
                    </select>
                  </div>
                  
                  <div class="col-12">
                    <div class="text-center mt-4">
                      <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-circle"></i> Create Travel Booking
                      </button>
                      <button type="reset" class="btn btn-secondary">Clear Form</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            
            <div class="tab-pane fade <?php echo $booking_type == 'hotel' ? 'show active' : ''; ?>" id="hotel-tab">
              <form method="POST" action="">
                <input type="hidden" name="booking_type" value="hotel">
                
                <h5 class="card-title text-success mb-4"><i class="bi bi-building"></i> Hotel Booking</h5>
                
                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="guest_name" class="form-label">Guest Name *</label>
                    <input type="text" class="form-control" id="guest_name" name="guest_name" required placeholder="Enter guest full name">
                  </div>
                  
                  <div class="col-md-6">
                    <label for="country" class="form-label">Country *</label>
                    <select class="form-select" id="country" name="country" required onchange="updateCities('hotel'); updateHotels();">
                      <option value="">Select country...</option>
                      <?php
                      $countries_result->data_seek(0);
                      while($country = $countries_result->fetch_assoc()): ?>
                      <option value="<?php echo $country['country_name']; ?>" data-code="<?php echo $country['country_code']; ?>">
                        <?php echo htmlspecialchars($country['country_name']); ?>
                      </option>
                      <?php endwhile; ?>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label for="hotel_name" class="form-label">Hotel Name *</label>
                    <select class="form-select" id="hotel_name" name="hotel_name" required>
                      <option value="">Select country first</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label for="city" class="form-label">City *</label>
                    <select class="form-select" id="city" name="city" required disabled>
                      <option value="">Select country first</option>
                    </select>
                  </div>
                  
                  <div class="col-md-4">
                    <label for="room_type" class="form-label">Room Type *</label>
                    <select class="form-select" id="room_type" name="room_type" required>
                      <option value="">Select room type...</option>
                      <option value="Single">🛏️ Single Room</option>
                      <option value="Double">🛏️🛏️ Double Room</option>
                      <option value="Suite">🏨 Suite</option>
                      <option value="Deluxe">⭐ Deluxe Room</option>
                      <option value="Family">👨‍👩‍👧‍👦 Family Room</option>
                    </select>
                  </div>
                  
                  <div class="col-md-4">
                    <label for="check_in" class="form-label">Check-in Date *</label>
                    <input type="date" class="form-control" id="check_in" name="check_in" required min="<?php echo date('Y-m-d'); ?>">
                  </div>
                  
                  <div class="col-md-4">
                    <label for="check_out" class="form-label">Check-out Date *</label>
                    <input type="date" class="form-control" id="check_out" name="check_out" required>
                  </div>
                  
                  <div class="col-md-4">
                    <label for="num_guests" class="form-label">Number of Guests *</label>
                    <input type="number" class="form-control" id="num_guests" name="num_guests" min="1" max="10" value="1" required>
                  </div>
                  
                  <div class="col-md-4">
                    <label for="total_price" class="form-label">Total Price (₱) *</label>
                    <div class="input-group">
                      <span class="input-group-text">₱</span>
                      <input type="number" class="form-control" id="total_price" name="total_price" step="0.01" min="0" required oninput="updateCommission('hotel')">
                    </div>
                  </div>
                  
                  <div class="col-md-4">
                    <label for="hotel_agent_id" class="form-label">Travel Agent (Optional)</label>
                    <select class="form-select" id="hotel_agent_id" name="agent_id" onchange="updateCommission('hotel')">
                      <option value="">No agent / Direct booking</option>
                      <?php 
                      $agents_result->data_seek(0);
                      while($agent = $agents_result->fetch_assoc()): ?>
                      <option value="<?php echo $agent['agent_id']; ?>" data-rate="<?php echo $agent['commission_rate']; ?>">
                        <?php echo htmlspecialchars($agent['agent_name']); ?> (<?php echo $agent['commission_rate']; ?>%)
                      </option>
                      <?php endwhile; ?>
                    </select>
                  </div>
                  
                  <div class="col-12">
                    <div class="alert alert-info" id="commissionInfoHotel" style="display: none;">
                      <i class="bi bi-info-circle"></i> 
                      Agent Commission: <span id="commissionAmountHotel">₱0.00</span> 
                      (<span id="commissionRateHotel">0</span>% of total amount)
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <label for="hotel_status" class="form-label">Status *</label>
                    <select class="form-select" id="hotel_status" name="status" required>
                      <option value="Pending">⏳ Pending</option>
                      <option value="Confirmed" selected>✅ Confirmed</option>
                      <option value="Checked-in">🏨 Checked-in</option>
                      <option value="Checked-out">👋 Checked-out</option>
                      <option value="Cancelled">❌ Cancelled</option>
                    </select>
                  </div>
                  
                  <div class="col-12">
                    <div class="text-center mt-4">
                      <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-check-circle"></i> Create Hotel Booking
                      </button>
                      <button type="reset" class="btn btn-secondary">Clear Form</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            
            <div class="tab-pane fade <?php echo $booking_type == 'tour' ? 'show active' : ''; ?>" id="tour-tab">
              <form method="POST" action="">
                <input type="hidden" name="booking_type" value="tour">
                
                <h5 class="card-title text-warning mb-4"><i class="bi bi-bus-front"></i> Tour Activity</h5>
                
                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="tour_name" class="form-label">Tour Name *</label>
                    <input type="text" class="form-control" id="tour_name" name="tour_name" required placeholder="Enter tour name">
                  </div>
                  
                  <div class="col-md-6">
                    <label for="activity_type" class="form-label">Activity Type *</label>
                    <select class="form-select" id="activity_type" name="activity_type" required>
                      <option value="">Select activity type...</option>
                      <option value="Sightseeing">🏛️ Sightseeing</option>
                      <option value="Adventure">🧗 Adventure</option>
                      <option value="Cultural">🎎 Cultural</option>
                      <option value="Food">🍜 Food Tour</option>
                      <option value="Nature">🌳 Nature</option>
                    </select>
                  </div>
                  
                  <div class="col-md-6">
                    <label for="tour_country" class="form-label">Country *</label>
                    <select class="form-select" id="tour_country" name="country" required onchange="updateCities('tour')">
                      <option value="">Select country...</option>
                      <?php
                      $countries_result->data_seek(0);
                      while($country = $countries_result->fetch_assoc()): ?>
                      <option value="<?php echo $country['country_name']; ?>" data-code="<?php echo $country['country_code']; ?>">
                        <?php echo htmlspecialchars($country['country_name']); ?>
                      </option>
                      <?php endwhile; ?>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label for="tour_city" class="form-label">City *</label>
                    <select class="form-select" id="tour_city" name="city" required disabled>
                      <option value="">Select country first</option>
                    </select>
                  </div>
                  
                  <div class="col-md-4">
                    <label for="tour_date" class="form-label">Tour Date *</label>
                    <input type="date" class="form-control" id="tour_date" name="tour_date" required min="<?php echo date('Y-m-d'); ?>">
                  </div>
                  
                  <div class="col-md-4">
                    <label for="duration_hours" class="form-label">Duration (Hours) *</label>
                    <input type="number" class="form-control" id="duration_hours" name="duration_hours" min="1" max="24" value="4" required>
                  </div>
                  
                  <div class="col-md-4">
                    <label for="price_per_person" class="form-label">Price per Person (₱) *</label>
                    <div class="input-group">
                      <span class="input-group-text">₱</span>
                      <input type="number" class="form-control" id="price_per_person" name="price_per_person" step="0.01" min="0" required oninput="updateCommission('tour')">
                    </div>
                  </div>

                  <div class="col-md-4">
                    <label for="participants" class="form-label">Number of Participants *</label>
                    <input type="number" class="form-control" id="participants" name="participants" min="1" max="100" value="1" required oninput="updateCommission('tour')">
                  </div>

                  <div class="col-md-4">
                    <label for="total_amount" class="form-label">Total Amount (₱)</label>
                    <div class="input-group">
                      <span class="input-group-text">₱</span>
                      <input type="number" class="form-control" id="total_amount" name="total_amount" step="0.01" readonly>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label for="tour_agent_id" class="form-label">Travel Agent (Optional)</label>
                    <select class="form-select" id="tour_agent_id" name="agent_id" onchange="updateCommission('tour')">
                      <option value="">No agent / Direct booking</option>
                      <?php
                      $agents_result->data_seek(0);
                      while($agent = $agents_result->fetch_assoc()): ?>
                      <option value="<?php echo $agent['agent_id']; ?>" data-rate="<?php echo $agent['commission_rate']; ?>">
                        <?php echo htmlspecialchars($agent['agent_name']); ?> (<?php echo $agent['commission_rate']; ?>%)
                      </option>
                      <?php endwhile; ?>
                    </select>
                  </div>

                  <div class="col-12">
                    <div class="alert alert-info" id="commissionInfoTour" style="display: none;">
                      <i class="bi bi-info-circle"></i>
                      Agent Commission: <span id="commissionAmountTour">₱0.00</span>
                      (<span id="commissionRateTour">0</span>% of total amount)
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label for="tour_status" class="form-label">Status *</label>
                    <select class="form-select" id="tour_status" name="status" required>
                      <option value="Available" selected>✅ Available</option>
                      <option value="Pending">⏳ Pending</option>
                      <option value="Fully Booked">🚫 Fully Booked</option>
                      <option value="Cancelled">❌ Cancelled</option>
                      <option value="Completed">🏁 Completed</option>
                    </select>
                  </div>
                  
                  <div class="col-12">
                    <div class="text-center mt-4">
                      <button type="submit" class="btn btn-warning btn-lg">
                        <i class="bi bi-check-circle"></i> Create Tour Activity
                      </button>
                      <button type="reset" class="btn btn-secondary">Clear Form</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            
            <div class="tab-pane fade <?php echo $booking_type == 'flight' ? 'show active' : ''; ?>" id="flight-tab">
              <form method="POST" action="">
                <input type="hidden" name="booking_type" value="flight">
                
                <h5 class="card-title text-info mb-4"><i class="bi bi-airplane-engines"></i> Flight Booking</h5>
                
                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="passenger_name" class="form-label">Passenger Name *</label>
                    <input type="text" class="form-control" id="passenger_name" name="passenger_name" required placeholder="Enter passenger name">
                  </div>
                  
                  <div class="col-md-6">
                    <label for="airline" class="form-label">Airline *</label>
                    <select class="form-select" id="airline" name="airline" required>
                      <option value="">Select airline...</option>
                    </select>
                  </div>
                  
                  <div class="col-md-6">
                    <label for="flight_number" class="form-label">Flight Number *</label>
                    <input type="text" class="form-control" id="flight_number" name="flight_number" required placeholder="Ex: PR 101">
                  </div>
                  
                  <div class="col-md-6">
                    <label for="seat_class" class="form-label">Seat Class *</label>
                    <select class="form-select" id="seat_class" name="seat_class" required>
                      <option value="">Select class...</option>
                      <option value="Economy">✈️ Economy</option>
                      <option value="Premium Economy">⭐ Premium Economy</option>
                      <option value="Business">💼 Business Class</option>
                      <option value="First Class">👑 First Class</option>
                    </select>
                  </div>
                  
                  <div class="col-md-6">
                    <label for="departure_airport" class="form-label">Departure Airport *</label>
                    <select class="form-select" id="departure_airport" name="departure_airport" required onchange="updateAirlines()">
                      <option value="">Select departure airport...</option>
                      <?php foreach($airports as $code => $data): ?>
                      <option value="<?php echo $data['name']; ?>"><?php echo $data['name']; ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label for="arrival_airport" class="form-label">Arrival Airport *</label>
                    <input type="text" class="form-control" id="arrival_airport" name="arrival_airport" required placeholder="Ex: NRT - Tokyo">
                  </div>
                  
                  <div class="col-md-6">
                    <label for="departure_date" class="form-label">Departure Date & Time *</label>
                    <input type="datetime-local" class="form-control" id="departure_date" name="departure_date" required>
                  </div>
                  
                  <div class="col-md-6">
                    <label for="arrival_date" class="form-label">Arrival Date & Time *</label>
                    <input type="datetime-local" class="form-control" id="arrival_date" name="arrival_date" required>
                  </div>
                  
                  <div class="col-md-6">
                    <label for="fare" class="form-label">Fare (₱) *</label>
                    <div class="input-group">
                      <span class="input-group-text">₱</span>
                      <input type="number" class="form-control" id="fare" name="fare" step="0.01" min="0" required oninput="updateCommission('flight')">
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <label for="flight_agent_id" class="form-label">Travel Agent (Optional)</label>
                    <select class="form-select" id="flight_agent_id" name="agent_id" onchange="updateCommission('flight')">
                      <option value="">No agent / Direct booking</option>
                      <?php 
                      $agents_result->data_seek(0);
                      while($agent = $agents_result->fetch_assoc()): ?>
                      <option value="<?php echo $agent['agent_id']; ?>" data-rate="<?php echo $agent['commission_rate']; ?>">
                        <?php echo htmlspecialchars($agent['agent_name']); ?> (<?php echo $agent['commission_rate']; ?>%)
                      </option>
                      <?php endwhile; ?>
                    </select>
                  </div>
                  
                  <div class="col-12">
                    <div class="alert alert-info" id="commissionInfoFlight" style="display: none;">
                      <i class="bi bi-info-circle"></i> 
                      Agent Commission: <span id="commissionAmountFlight">₱0.00</span> 
                      (<span id="commissionRateFlight">0</span>% of total amount)
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <label for="flight_status" class="form-label">Status *</label>
                    <select class="form-select" id="flight_status" name="status" required>
                      <option value="Pending">⏳ Pending</option>
                      <option value="Confirmed" selected>✅ Confirmed</option>
                      <option value="Boarded">✈️ Boarded</option>
                      <option value="Completed">🏁 Completed</option>
                      <option value="Cancelled">❌ Cancelled</option>
                    </select>
                  </div>
                  
                  <div class="col-12">
                    <div class="text-center mt-4">
                      <button type="submit" class="btn btn-info btn-lg">
                        <i class="bi bi-check-circle"></i> Create Flight Booking
                      </button>
                      <button type="reset" class="btn btn-secondary">Clear Form</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
function updateCommission(type) {
    let agentSelect, amountInput, commissionInfo, commissionAmount, commissionRate;

    switch(type) {
        case 'travel':
            agentSelect = document.getElementById('agent_id');
            amountInput = document.getElementById('booking_amount');
            commissionInfo = document.getElementById('commissionInfoTravel');
            commissionAmount = document.getElementById('commissionAmountTravel');
            commissionRate = document.getElementById('commissionRateTravel');
            break;
        case 'hotel':
            agentSelect = document.getElementById('hotel_agent_id');
            amountInput = document.getElementById('total_price');
            commissionInfo = document.getElementById('commissionInfoHotel');
            commissionAmount = document.getElementById('commissionAmountHotel');
            commissionRate = document.getElementById('commissionRateHotel');
            break;
        case 'tour':
            agentSelect = document.getElementById('tour_agent_id');
            amountInput = document.getElementById('total_amount');
            commissionInfo = document.getElementById('commissionInfoTour');
            commissionAmount = document.getElementById('commissionAmountTour');
            commissionRate = document.getElementById('commissionRateTour');
            break;
        case 'flight':
            agentSelect = document.getElementById('flight_agent_id');
            amountInput = document.getElementById('fare');
            commissionInfo = document.getElementById('commissionInfoFlight');
            commissionAmount = document.getElementById('commissionAmountFlight');
            commissionRate = document.getElementById('commissionRateFlight');
            break;
    }

    const selectedOption = agentSelect.options[agentSelect.selectedIndex];
    const commissionRateValue = selectedOption.getAttribute('data-rate');
    const amountValue = parseFloat(amountInput.value) || 0;

    if (commissionRateValue && amountValue > 0) {
        const commission = (amountValue * commissionRateValue) / 100;
        commissionAmount.textContent = '₱' + commission.toFixed(2);
        commissionRate.textContent = commissionRateValue;
        commissionInfo.style.display = 'block';
    } else {
        commissionInfo.style.display = 'none';
    }
}

function calculateTotalAmount() {
    const pricePerPerson = parseFloat(document.getElementById('price_per_person').value) || 0;
    const participants = parseInt(document.getElementById('participants').value) || 0;
    const totalAmount = pricePerPerson * participants;
    document.getElementById('total_amount').value = totalAmount.toFixed(2);
    updateCommission('tour');
}

document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    document.getElementById('check_in').value = today.toISOString().split('T')[0];
    document.getElementById('check_out').value = tomorrow.toISOString().split('T')[0];
    
    document.getElementById('tour_date').value = today.toISOString().split('T')[0];
    
    tomorrow.setHours(14, 0, 0, 0);
    const flightDeparture = tomorrow.toISOString().slice(0, 16);
    const flightArrival = new Date(tomorrow);
    flightArrival.setHours(flightArrival.getHours() + 5);
    
    document.getElementById('departure_date').value = flightDeparture;
    document.getElementById('arrival_date').value = flightArrival.toISOString().slice(0, 16);
    
    const urlParams = new URLSearchParams(window.location.search);
    const type = urlParams.get('type');
    if (type) {
        const tabButton = document.querySelector(`[data-bs-target="#${type}-tab"]`);
        if (tabButton) {
            const tab = new bootstrap.Tab(tabButton);
            tab.show();
        }
    }
});

document.getElementById('check_in').addEventListener('change', function() {
    const checkOutField = document.getElementById('check_out');
    const checkInDate = new Date(this.value);
    const minDate = new Date(checkInDate);
    minDate.setDate(minDate.getDate() + 1);
    
    checkOutField.min = minDate.toISOString().split('T')[0];
    
    if (new Date(checkOutField.value) < minDate) {
        checkOutField.value = minDate.toISOString().split('T')[0];
    }
});

document.getElementById('departure_date').addEventListener('change', function() {
    const arrivalField = document.getElementById('arrival_date');
    const departureDate = new Date(this.value);
    const minDate = new Date(departureDate);
    minDate.setHours(minDate.getHours() + 1);

    arrivalField.min = minDate.toISOString().slice(0, 16);

    if (new Date(arrivalField.value) < minDate) {
        arrivalField.value = minDate.toISOString().slice(0, 16);
    }
});

const citiesByCountry = <?php echo json_encode($cities_by_country); ?>;
console.log('Cities by Country:', citiesByCountry);

const airports = <?php echo json_encode($airports); ?>;
console.log('Airports:', airports);

const hotels = <?php echo json_encode($hotels); ?>;
console.log('Hotels:', hotels);

function updateCities(type) {
    console.log('updateCities called with type:', type);
    let countrySelect, citySelect;

    if (type === 'from') {
        countrySelect = document.getElementById('from_country');
        citySelect = document.getElementById('from_city');
    } else if (type === 'to') {
        countrySelect = document.getElementById('to_country');
        citySelect = document.getElementById('to_city');
    } else if (type === 'hotel') {
        countrySelect = document.getElementById('country');
        citySelect = document.getElementById('city');
    } else if (type === 'tour') {
        countrySelect = document.getElementById('tour_country');
        citySelect = document.getElementById('tour_city');
    }

    const selectedOption = countrySelect.options[countrySelect.selectedIndex];
    const countryCode = selectedOption.getAttribute('data-code');
    console.log('Selected country code:', countryCode);
    console.log('Available cities for this country:', citiesByCountry[countryCode]);

    citySelect.innerHTML = '<option value="">Select city...</option>';

    if (countryCode && citiesByCountry[countryCode]) {
        citiesByCountry[countryCode].forEach(city => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            citySelect.appendChild(option);
        });
        console.log('Cities populated successfully');
    } else {
        console.log('No cities found for country code:', countryCode);
    }

    citySelect.disabled = !countryCode;
}

function updateAirlines() {
    const departureSelect = document.getElementById('departure_airport');
    const airlineSelect = document.getElementById('airline');
    const selectedAirportName = departureSelect.value;

    // Clear existing options
    airlineSelect.innerHTML = '<option value="">Select airline...</option>';

    if (!selectedAirportName) {
        return;
    }

    // Find the airport code from the selected name
    let airportCode = null;
    for (const code in airports) {
        if (airports[code].name === selectedAirportName) {
            airportCode = code;
            break;
        }
    }

    if (airportCode && airports[airportCode].airlines) {
        airports[airportCode].airlines.forEach(airline => {
            const option = document.createElement('option');
            option.value = airline;
            option.textContent = airline;
            airlineSelect.appendChild(option);
        });
    }
}

function updateHotels() {
    const countrySelect = document.getElementById('country');
    const hotelSelect = document.getElementById('hotel_name');
    const selectedOption = countrySelect.options[countrySelect.selectedIndex];
    const countryCode = selectedOption.getAttribute('data-code');

    console.log('updateHotels called');
    console.log('Selected country code:', countryCode);
    console.log('Hotels for this country:', hotels[countryCode]);

    // Clear existing options
    hotelSelect.innerHTML = '<option value="">Select hotel...</option>';

    if (countryCode && hotels[countryCode]) {
        hotels[countryCode].forEach(hotel => {
            const option = document.createElement('option');
            option.value = hotel;
            option.textContent = hotel;
            hotelSelect.appendChild(option);
        });
        console.log('Hotels populated successfully');
    } else {
        console.log('No hotels found for country code:', countryCode);
    }

    hotelSelect.disabled = !countryCode;
}
</script>

<?php $conn->close(); ?>