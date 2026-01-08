<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$check_table = $conn->query("SHOW TABLES LIKE 'cruises'");
if ($check_table->num_rows == 0) {
    $create_table_sql = "CREATE TABLE cruises (
        id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id VARCHAR(50) UNIQUE NOT NULL,
        cruise_line VARCHAR(100) NOT NULL,
        ship_name VARCHAR(100) NOT NULL,
        itinerary_name VARCHAR(200) NOT NULL,
        departure_port VARCHAR(100) NOT NULL,
        arrival_port VARCHAR(100) NOT NULL,
        departure_date DATE NOT NULL,
        arrival_date DATE NOT NULL,
        duration_nights INT NOT NULL,
        ports_of_call TEXT,
        cabin_type VARCHAR(50) NOT NULL,
        deck_number VARCHAR(20),
        cabin_number VARCHAR(20),
        guest_name VARCHAR(100) NOT NULL,
        guest_email VARCHAR(100) NOT NULL,
        guest_phone VARCHAR(20) NOT NULL,
        guest_count INT NOT NULL DEFAULT 1,
        room_count INT NOT NULL DEFAULT 1,
        base_price DECIMAL(10,2) NOT NULL,
        port_fees DECIMAL(10,2) DEFAULT 0,
        taxes DECIMAL(10,2) DEFAULT 0,
        gratuities DECIMAL(10,2) DEFAULT 0,
        total_amount DECIMAL(10,2) NOT NULL,
        promo_code VARCHAR(50),
        discount_amount DECIMAL(10,2) DEFAULT 0,
        agent_id VARCHAR(50),
        agent_commission DECIMAL(10,2) DEFAULT 0,
        status VARCHAR(50) DEFAULT 'Pending',
        payment_method VARCHAR(50),
        payment_status VARCHAR(50) DEFAULT 'Pending',
        special_requests TEXT,
        passport_number VARCHAR(50),
        passport_expiry DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (agent_id) REFERENCES travel_agents(agent_id) ON DELETE SET NULL
    )";
    
    if ($conn->query($create_table_sql) === FALSE) {
        echo "Error creating table: " . $conn->error;
    }
}
$cruise_lines = [
    'dream' => [
        'name' => 'Dream Cruises',
        'ships' => [
            [
                'id' => 'genting-dream',
                'name' => 'Genting Dream',
                'image' => 'assets/img/cruises/genting-dream.jpg',
                'tonnage' => '150,695 GT',
                'guest_capacity' => '3,350',
                'year_built' => '2016',
                'decks' => 18
            ]
        ]
    ],
    'royal' => [
        'name' => 'Royal Caribbean International',
        'ships' => [
            [
                'id' => 'symphony-seas',
                'name' => 'Symphony of the Seas',
                'image' => 'assets/img/cruises/royal-caribbean.jpg',
                'tonnage' => '228,081 GT',
                'guest_capacity' => '5,518',
                'year_built' => '2018',
                'decks' => 18
            ]
        ]
    ],
    'norwegian' => [
        'name' => 'Norwegian Cruise Line',
        'ships' => [
            [
                'id' => 'norwegian-joy',
                'name' => 'Norwegian Joy',
                'image' => 'assets/img/cruises/norwegian-cruise.jpg',
                'tonnage' => '167,725 GT',
                'guest_capacity' => '3,804',
                'year_built' => '2017',
                'decks' => 20
            ]
        ]
    ],
    'carnival' => [
        'name' => 'Carnival Cruise Line',
        'ships' => [
            [
                'id' => 'carnival-vista',
                'name' => 'Carnival Vista',
                'image' => 'assets/img/cruises/carnival-cruise.jpg',
                'tonnage' => '133,500 GT',
                'guest_capacity' => '3,934',
                'year_built' => '2016',
                'decks' => 15
            ]
        ]
    ],
    'msc' => [
        'name' => 'MSC Cruises',
        'ships' => [
            [
                'id' => 'msc-meraviglia',
                'name' => 'MSC Meraviglia',
                'image' => 'assets/img/cruises/msc-cruise.jpg',
                'tonnage' => '171,598 GT',
                'guest_capacity' => '4,475',
                'year_built' => '2017',
                'decks' => 19
            ]
        ]
    ],
    'disney' => [
        'name' => 'Disney Cruise Line',
        'ships' => [
            [
                'id' => 'disney-wish',
                'name' => 'Disney Wish',
                'image' => 'assets/img/cruises/disney-cruise.jpg',
                'tonnage' => '144,000 GT',
                'guest_capacity' => '2,500',
                'year_built' => '2022',
                'decks' => 15
            ]
        ]
    ]
];
$itineraries = [
    [
        'id' => 'southeast-asia-3',
        'name' => '3-Night Southeast Asia Cruise: Singapore - Melaka - Singapore',
        'cruise_line' => 'dream',
        'ship' => 'genting-dream',
        'departure_port' => 'Singapore (Marina Bay Cruise Centre)',
        'ports_of_call' => ['Singapore', 'Melaka, Malaysia', 'Singapore'],
        'duration_nights' => 2,
        'description' => 'Experience the best of Southeast Asia with this short getaway cruise'
    ],
    [
        'id' => 'southeast-asia-4',
        'name' => '4-Night Southeast Asia Cruise: Singapore - Phuket - Singapore',
        'cruise_line' => 'dream',
        'ship' => 'genting-dream',
        'departure_port' => 'Singapore (Marina Bay Cruise Centre)',
        'ports_of_call' => ['Singapore', 'Phuket, Thailand', 'Singapore'],
        'duration_nights' => 3,
        'description' => 'Explore the beautiful beaches of Phuket on this relaxing cruise'
    ],
    [
        'id' => 'caribbean-7',
        'name' => '7-Night Caribbean Cruise',
        'cruise_line' => 'royal',
        'ship' => 'symphony-seas',
        'departure_port' => 'Miami, Florida',
        'ports_of_call' => ['Miami', 'Nassau, Bahamas', 'CocoCay', 'St. Thomas', 'St. Maarten'],
        'duration_nights' => 7,
        'description' => 'Ultimate Caribbean adventure with Royal Caribbean'
    ],
    [
        'id' => 'mediterranean-10',
        'name' => '10-Night Mediterranean Cruise',
        'cruise_line' => 'msc',
        'ship' => 'msc-meraviglia',
        'departure_port' => 'Genoa, Italy',
        'ports_of_call' => ['Genoa', 'Marseille, France', 'Barcelona, Spain', 'Palma de Mallorca', 'Naples, Italy', 'Rome'],
        'duration_nights' => 10,
        'description' => 'Explore the beautiful Mediterranean coastline'
    ],
    [
        'id' => 'alaska-7',
        'name' => '7-Night Alaska Glacier Cruise',
        'cruise_line' => 'norwegian',
        'ship' => 'norwegian-joy',
        'departure_port' => 'Seattle, Washington',
        'ports_of_call' => ['Seattle', 'Juneau', 'Skagway', 'Glacier Bay', 'Ketchikan', 'Victoria, BC'],
        'duration_nights' => 7,
        'description' => 'Witness the majestic glaciers of Alaska'
    ]
];

// REALISTIC CRUISE PRICES IN PHILIPPINE PESOS (₱)
// Based on actual cruise pricing converted to PHP (1 USD ≈ 56 PHP)
$cabin_types = [
    'interior' => [
        'name' => 'Interior Stateroom',
        'description' => 'Comfortable interior cabin with all essential amenities',
        'base_price_per_night' => 5600,      // ~$100 USD per night
        'features' => ['Queen bed or twin beds', 'Private bathroom', 'TV', '24-hour room service']
    ],
    'oceanview' => [
        'name' => 'Oceanview Stateroom',
        'description' => 'Cabin with a window or porthole with ocean views',
        'base_price_per_night' => 8400,      // ~$150 USD per night
        'features' => ['Ocean view window', 'Queen bed or twin beds', 'Private bathroom', 'Sitting area']
    ],
    'balcony' => [
        'name' => 'Balcony Stateroom',
        'description' => 'Private balcony with stunning sea views',
        'base_price_per_night' => 11200,     // ~$200 USD per night
        'features' => ['Private balcony', 'Ocean views', 'Queen bed', 'Sitting area', 'Mini-bar']
    ],
    'suite' => [
        'name' => 'Suite',
        'description' => 'Luxurious suite with extra space and amenities',
        'base_price_per_night' => 22400,     // ~$400 USD per night
        'features' => ['Large living area', 'Private balcony', 'Priority boarding', 'Butler service', 'Concierge']
    ],
    'villa' => [
        'name' => 'Villa Suite',
        'description' => 'Ultimate luxury with exclusive amenities',
        'base_price_per_night' => 33600,     // ~$600 USD per night
        'features' => ['Multiple bedrooms', 'Private terrace', 'Butler service', 'VIP access', 'Luxury bathroom']
    ]
];

// Realistic port fees in PHP
$port_fees = [
    'Southeast Asia' => 2800,      // ~$50 USD
    'Caribbean' => 3360,           // ~$60 USD
    'Mediterranean' => 4480,       // ~$80 USD
    'Alaska' => 5600,              // ~$100 USD
    'Europe' => 3920               // ~$70 USD
];

// Updated promo codes with realistic PHP amounts
$promo_codes = [
    'CRUISE20' => ['discount' => 20, 'type' => 'percentage', 'min_nights' => 5],
    'SEA15' => ['discount' => 15, 'type' => 'percentage', 'min_amount' => 50000],  // ~$893 USD
    'SAIL100' => ['discount' => 500, 'type' => 'fixed'],  // ~$9 USD
    'FAMILY10' => ['discount' => 10, 'type' => 'percentage', 'min_guests' => 3]
];

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_cruise'])) {
    $required_fields = [
        'guest_name', 'guest_email', 'guest_phone',
        'cruise_line', 'ship_name', 'itinerary_name',
        'departure_port', 'arrival_port', 'departure_date', 'arrival_date',
        'cabin_type', 'guest_count', 'room_count',
        'base_price', 'total_amount', 'payment_method'
    ];
    
    $missing_fields = [];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }
    if (!empty($missing_fields)) {
        $message = "❌ Missing required fields: " . implode(', ', $missing_fields);
        $message_type = "error";
    } else {
        $guest_name = $conn->real_escape_string($_POST['guest_name']);
        $guest_email = $conn->real_escape_string($_POST['guest_email']);
        $guest_phone = $conn->real_escape_string($_POST['guest_phone']);
        
        $cruise_line = $conn->real_escape_string($_POST['cruise_line']);
        $ship_name = $conn->real_escape_string($_POST['ship_name']);
        $itinerary_name = $conn->real_escape_string($_POST['itinerary_name']);
        
        $departure_port = $conn->real_escape_string($_POST['departure_port']);
        $arrival_port = $conn->real_escape_string($_POST['arrival_port']);
        $departure_date = $conn->real_escape_string($_POST['departure_date']);
        $arrival_date = $conn->real_escape_string($_POST['arrival_date']);
        
        $duration_nights = isset($_POST['duration_nights']) ? (int)$_POST['duration_nights'] : 0;
        $ports_of_call = isset($_POST['ports_of_call']) ? $conn->real_escape_string($_POST['ports_of_call']) : '';
        
        $cabin_type = $conn->real_escape_string($_POST['cabin_type']);
        $deck_number = isset($_POST['deck_number']) ? $conn->real_escape_string($_POST['deck_number']) : '';
        $cabin_number = isset($_POST['cabin_number']) ? $conn->real_escape_string($_POST['cabin_number']) : '';
        
        $guest_count = (int)$_POST['guest_count'];
        $room_count = (int)$_POST['room_count'];
        
        $base_price = (float)$_POST['base_price'];
        $port_fee = isset($_POST['port_fee']) ? (float)$_POST['port_fee'] : 0;
        $taxes = isset($_POST['taxes']) ? (float)$_POST['taxes'] : 0;
        $gratuities = isset($_POST['gratuities']) ? (float)$_POST['gratuities'] : 0;
        
        $promo_code = isset($_POST['promo_code']) ? $conn->real_escape_string($_POST['promo_code']) : '';
        $discount_amount = isset($_POST['discount_amount']) ? (float)$_POST['discount_amount'] : 0;
        
        $total_amount = (float)$_POST['total_amount'];
        
        $agent_id = isset($_POST['agent_id']) && !empty($_POST['agent_id']) ? $conn->real_escape_string($_POST['agent_id']) : NULL;
        $agent_commission = 0;
        
        if (!empty($agent_id) && $agent_id !== 'NULL') {
            $agent_check = $conn->query("SELECT agent_id, commission_rate FROM travel_agents WHERE agent_id = '$agent_id'");
            if ($agent_check && $agent_check->num_rows > 0) {
                $agent_data = $agent_check->fetch_assoc();
                $commission_rate = $agent_data['commission_rate'];
                $agent_commission = ($total_amount * $commission_rate) / 100;
            } else {
                $agent_id = NULL;
            }
        } else {
            $agent_id = NULL;
        }
        
        $payment_method = $conn->real_escape_string($_POST['payment_method']);
        $status = 'Confirmed';
        
        $special_requests = isset($_POST['special_requests']) ? $conn->real_escape_string($_POST['special_requests']) : '';
        $passport_number = isset($_POST['passport_number']) ? $conn->real_escape_string($_POST['passport_number']) : '';
        $passport_expiry = isset($_POST['passport_expiry']) ? $conn->real_escape_string($_POST['passport_expiry']) : NULL;
        
        // Generate booking ID
        $booking_id = 'CRU-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        
        // Insert into database
        $sql = "INSERT INTO cruises (
            booking_id, cruise_line, ship_name, itinerary_name,
            departure_port, arrival_port, departure_date, arrival_date,
            duration_nights, ports_of_call, cabin_type, deck_number, cabin_number,
            guest_name, guest_email, guest_phone, guest_count, room_count,
            base_price, port_fees, taxes, gratuities, total_amount,
            promo_code, discount_amount, agent_id, agent_commission,
            status, payment_method, special_requests,
            passport_number, passport_expiry
        ) VALUES (
            '$booking_id', '$cruise_line', '$ship_name', '$itinerary_name',
            '$departure_port', '$arrival_port', '$departure_date', '$arrival_date',
            $duration_nights, '$ports_of_call', '$cabin_type', " . 
            (!empty($deck_number) ? "'$deck_number'" : "NULL") . ", " .
            (!empty($cabin_number) ? "'$cabin_number'" : "NULL") . ",
            '$guest_name', '$guest_email', '$guest_phone', $guest_count, $room_count,
            $base_price, $port_fee, $taxes, $gratuities, $total_amount,
            " . (!empty($promo_code) ? "'$promo_code'" : "NULL") . ", $discount_amount,
            " . ($agent_id ? "'$agent_id'" : "NULL") . ", $agent_commission,
            '$status', '$payment_method', '$special_requests',
            " . (!empty($passport_number) ? "'$passport_number'" : "NULL") . ", " .
            (!empty($passport_expiry) ? "'$passport_expiry'" : "NULL") . "
        )";
        
        if ($conn->query($sql)) {
            // Update agent's total bookings if applicable
            if (!empty($agent_id) && $agent_id !== 'NULL') {
                $conn->query("UPDATE travel_agents SET total_bookings = total_bookings + 1 WHERE agent_id = '$agent_id'");
            }
            
            $message = "✅ Cruise booked successfully! ";
            $message .= "Booking ID: <strong>" . $booking_id . "</strong><br>";
            $message .= "Total Amount: <strong>₱" . number_format($total_amount, 2) . "</strong>";
            
            if ($agent_commission > 0) {
                $message .= "<br>📊 Agent Commission: <strong>₱" . number_format($agent_commission, 2) . "</strong>";
            }
            
            $message_type = "success";
            
            // Redirect after 3 seconds
            echo '<script>setTimeout(function() { window.location.href = "index.php?page=cruises"; }, 3000);</script>';
            
        } else {
            $message = "❌ Error creating booking: " . $conn->error;
            $message_type = "error";
        }
    }
}

// Get active agents for dropdown
$agents_result = $conn->query("SELECT agent_id, agent_name, commission_rate FROM travel_agents WHERE status = 'Active' AND (specialization = 'All' OR specialization = 'Cruises') ORDER BY agent_name");
?>

<div class="pagetitle">
  <h1>Cruises</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">Cruises</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      
      <?php if ($message): ?>
      <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php endif; ?>
      
      <div class="card">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-ship me-2"></i>Cruise Booking</h5>
          
          <!-- Navigation Tabs -->
          <ul class="nav nav-tabs nav-tabs-bordered mb-4" id="cruiseTabs">
            <li class="nav-item">
              <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#browse-tab">
                <i class="bi bi-compass me-1"></i> Browse Cruises
              </button>
            </li>
            <li class="nav-item">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#book-tab">
                <i class="bi bi-calendar-plus me-1"></i> Book Cruise
              </button>
            </li>
          </ul>
          
          <div class="tab-content pt-2">
            
            <!-- Tab 1: Browse Cruises -->
            <div class="tab-pane fade show active" id="browse-tab">
              <!-- Featured Cruises -->
              <div class="row mb-5">
                <div class="col-12">
                  <h4 class="mb-4">⭐ Featured Cruises</h4>
                  <div class="row g-4">
                    <?php foreach(array_slice($itineraries, 0, 3) as $itinerary): 
                      $cruise_line = $cruise_lines[$itinerary['cruise_line']];
                      $ship = null;
                      foreach($cruise_line['ships'] as $s) {
                        if ($s['id'] == $itinerary['ship']) {
                          $ship = $s;
                          break;
                        }
                      }
                    ?>
                    <div class="col-md-4">
                      <div class="card h-100 border">
                        <div class="position-relative">
                          <img src="<?php echo $ship['image']; ?>" class="card-img-top" alt="<?php echo $ship['name']; ?>" style="height: 200px; object-fit: cover;">
                          <span class="position-absolute top-0 end-0 badge bg-primary m-2"><?php echo $cruise_line['name']; ?></span>
                        </div>
                        <div class="card-body">
                          <h5 class="card-title"><?php echo $itinerary['name']; ?></h5>
                          <p class="text-muted small mb-3"><?php echo $itinerary['description']; ?></p>
                          
                          <div class="mb-3">
                            <div><i class="bi bi-geo-alt"></i> <strong>Departure:</strong> <?php echo $itinerary['departure_port']; ?></div>
                            <div><i class="bi bi-calendar"></i> <strong>Duration:</strong> <?php echo $itinerary['duration_nights']; ?> nights</div>
                            <div><i class="bi bi-ship"></i> <strong>Ship:</strong> <?php echo $ship['name']; ?></div>
                          </div>
                          
                          <div class="d-flex justify-content-between align-items-center">
                            <div>
                              <?php 
                                // Calculate price in PHP (2 guests in interior cabin)
                                $price = $cabin_types['interior']['base_price_per_night'] * $itinerary['duration_nights'] * 2;
                              ?>
                              <h4 class="text-primary mb-0">₱<?php echo number_format($price, 0); ?></h4>
                              <small class="text-muted">starting from, per cabin</small>
                            </div>
                            <button class="btn btn-primary select-itinerary" 
                                    data-itinerary='<?php echo json_encode($itinerary); ?>'
                                    data-cruiseline='<?php echo json_encode($cruise_line); ?>'
                                    data-ship='<?php echo json_encode($ship); ?>'>
                              <i class="bi bi-calendar-plus"></i> Select
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
              
              <!-- All Cruise Lines -->
              <div class="row">
                <div class="col-12">
                  <h4 class="mb-4">🚢 Cruise Lines</h4>
                  <div class="row g-4">
                    <?php foreach($cruise_lines as $line_key => $line): 
                      $ship = $line['ships'][0];
                    ?>
                    <div class="col-md-4 col-lg-3">
                      <div class="card h-100 border">
                        <img src="<?php echo $ship['image']; ?>" class="card-img-top" alt="<?php echo $line['name']; ?>" style="height: 150px; object-fit: cover;">
                        <div class="card-body">
                          <h6 class="card-title"><?php echo $line['name']; ?></h6>
                          <p class="small text-muted mb-2"><?php echo $ship['name']; ?></p>
                          <div class="small text-muted">
                            <div><i class="bi bi-people"></i> Capacity: <?php echo $ship['guest_capacity']; ?></div>
                            <div><i class="bi bi-layers"></i> Decks: <?php echo $ship['decks']; ?></div>
                            <div><i class="bi bi-calendar"></i> Built: <?php echo $ship['year_built']; ?></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Tab 2: Book Cruise -->
            <div class="tab-pane fade" id="book-tab">
              <form id="cruiseBookingForm" method="POST" action="">
                <input type="hidden" name="book_cruise" value="1">
                
                <div class="row">
                  <!-- Left Column: Cruise Details -->
                  <div class="col-lg-8">
                    
                    <!-- Selected Cruise Details -->
                    <div class="card mb-4">
                      <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-ship me-2"></i>Selected Cruise Details</h6>
                      </div>
                      <div class="card-body">
                        <div id="noCruiseSelected" class="text-center text-muted py-4">
                          <i class="bi bi-ship display-1"></i>
                          <p class="mt-3">Please select a cruise from the Browse tab</p>
                          <button type="button" class="btn btn-primary" onclick="switchToBrowseTab()">
                            <i class="bi bi-search"></i> Browse Cruises
                          </button>
                        </div>
                        
                        <div id="cruiseDetails" style="display: none;">
                          <div class="row">
                            <div class="col-md-4">
                              <img id="cruiseShipImage" src="" class="img-fluid rounded mb-3" alt="Cruise Ship" style="max-height: 150px;">
                            </div>
                            <div class="col-md-8">
                              <h4 id="cruiseItineraryName"></h4>
                              <div class="row">
                                <div class="col-6">
                                  <div><strong>Cruise Line:</strong> <span id="cruiseLineName"></span></div>
                                  <div><strong>Ship:</strong> <span id="cruiseShipName"></span></div>
                                  <div><strong>Duration:</strong> <span id="cruiseDuration"></span> nights</div>
                                </div>
                                <div class="col-6">
                                  <div><strong>Departure:</strong> <span id="cruiseDeparturePort"></span></div>
                                  <div><strong>Ports of Call:</strong> <span id="cruisePorts"></span></div>
                                </div>
                              </div>
                            </div>
                          </div>
                          
                          <!-- Hidden fields for form submission -->
                          <input type="hidden" id="form_cruise_line" name="cruise_line">
                          <input type="hidden" id="form_ship_name" name="ship_name">
                          <input type="hidden" id="form_itinerary_name" name="itinerary_name">
                          <input type="hidden" id="form_departure_port" name="departure_port">
                          <input type="hidden" id="form_arrival_port" name="arrival_port">
                          <input type="hidden" id="form_departure_date" name="departure_date">
                          <input type="hidden" id="form_arrival_date" name="arrival_date">
                          <input type="hidden" id="form_duration_nights" name="duration_nights">
                          <input type="hidden" id="form_ports_of_call" name="ports_of_call">
                        </div>
                      </div>
                    </div>
                    
                    <!-- Cabin Selection -->
                    <div class="card mb-4">
                      <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-door-closed me-2"></i>Cabin Selection</h6>
                      </div>
                      <div class="card-body">
                        <div class="row g-3" id="cabinOptions">
                          <?php foreach($cabin_types as $type_key => $cabin): ?>
                          <div class="col-md-6">
                            <div class="card border cabin-card <?php echo $type_key == 'interior' ? 'border-primary' : ''; ?>" data-type="<?php echo $type_key; ?>">
                              <div class="card-body">
                                <div class="form-check">
                                  <input class="form-check-input cabin-radio" type="radio" name="cabin_type" id="cabin_<?php echo $type_key; ?>" value="<?php echo $type_key; ?>" <?php echo $type_key == 'interior' ? 'checked' : ''; ?>>
                                  <label class="form-check-label" for="cabin_<?php echo $type_key; ?>">
                                    <h6 class="mb-1"><?php echo $cabin['name']; ?></h6>
                                  </label>
                                </div>
                                <p class="small text-muted mb-2"><?php echo $cabin['description']; ?></p>
                                <div class="mb-2">
                                  <?php foreach($cabin['features'] as $feature): ?>
                                  <span class="badge bg-light text-dark me-1 mb-1 small"><?php echo $feature; ?></span>
                                  <?php endforeach; ?>
                                </div>
                                <h5 class="text-primary mb-0">₱<span class="cabin-price" data-base="<?php echo $cabin['base_price_per_night']; ?>"><?php echo number_format($cabin['base_price_per_night'], 0); ?></span>/night</h5>
                                <input type="hidden" class="cabin-features" value="<?php echo htmlspecialchars(json_encode($cabin['features'])); ?>">
                              </div>
                            </div>
                          </div>
                          <?php endforeach; ?>
                        </div>
                        
                        <!-- Cabin Details -->
                        <div class="row g-3 mt-3">
                          <div class="col-md-4">
                            <label class="form-label">Deck Number</label>
                            <select class="form-select" id="deck_number" name="deck_number">
                              <option value="">Select deck</option>
                              <?php for($i = 1; $i <= 20; $i++): ?>
                              <option value="<?php echo $i; ?>">Deck <?php echo $i; ?></option>
                              <?php endfor; ?>
                            </select>
                          </div>
                          <div class="col-md-4">
                            <label class="form-label">Cabin Number (Optional)</label>
                            <input type="text" class="form-control" id="cabin_number" name="cabin_number" placeholder="E.g., 7123">
                          </div>
                          <div class="col-md-4">
                            <label class="form-label">Room Count</label>
                            <select class="form-select" id="room_count" name="room_count">
                              <?php for($i = 1; $i <= 5; $i++): ?>
                              <option value="<?php echo $i; ?>" <?php echo $i == 1 ? 'selected' : ''; ?>><?php echo $i; ?> room<?php echo $i > 1 ? 's' : ''; ?></option>
                              <?php endfor; ?>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Guest Information -->
                    <div class="card mb-4">
                      <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-people me-2"></i>Guest Information</h6>
                      </div>
                      <div class="card-body">
                        <div class="row g-3">
                          <div class="col-md-4">
                            <label class="form-label">Number of Guests *</label>
                            <select class="form-select" id="guest_count" name="guest_count">
                              <?php for($i = 1; $i <= 8; $i++): ?>
                              <option value="<?php echo $i; ?>" <?php echo $i == 2 ? 'selected' : ''; ?>><?php echo $i; ?> guest<?php echo $i > 1 ? 's' : ''; ?></option>
                              <?php endfor; ?>
                            </select>
                          </div>
                          <div class="col-md-8">
                            <div class="alert alert-info small">
                              <i class="bi bi-info-circle"></i> Most cabins accommodate 2 guests maximum. Additional guests may require multiple cabins.
                            </div>
                          </div>
                          
                          <div class="col-md-6">
                            <label class="form-label">Guest Name *</label>
                            <input type="text" class="form-control" name="guest_name" required value="<?php echo isset($_POST['guest_name']) ? htmlspecialchars($_POST['guest_name']) : ''; ?>">
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Email Address *</label>
                            <input type="email" class="form-control" name="guest_email" required value="<?php echo isset($_POST['guest_email']) ? htmlspecialchars($_POST['guest_email']) : ''; ?>">
                          </div>
                          
                          <div class="col-md-6">
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" class="form-control" name="guest_phone" required value="<?php echo isset($_POST['guest_phone']) ? htmlspecialchars($_POST['guest_phone']) : ''; ?>">
                          </div>
                          
                          <div class="col-md-6">
                            <label class="form-label">Passport Number (Optional)</label>
                            <input type="text" class="form-control" name="passport_number" value="<?php echo isset($_POST['passport_number']) ? htmlspecialchars($_POST['passport_number']) : ''; ?>">
                          </div>
                          
                          <div class="col-md-6">
                            <label class="form-label">Passport Expiry Date (Optional)</label>
                            <input type="date" class="form-control" name="passport_expiry" value="<?php echo isset($_POST['passport_expiry']) ? htmlspecialchars($_POST['passport_expiry']) : ''; ?>">
                          </div>
                          
                          <div class="col-12">
                            <label class="form-label">Special Requests</label>
                            <textarea class="form-control" name="special_requests" rows="3" placeholder="Dietary requirements, accessibility needs, celebration details..."><?php echo isset($_POST['special_requests']) ? htmlspecialchars($_POST['special_requests']) : ''; ?></textarea>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Right Column: Booking Summary -->
                  <div class="col-lg-4">
                    <!-- Price Summary -->
                    <div class="card mb-4">
                      <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bi bi-receipt me-2"></i>Price Summary</h6>
                      </div>
                      <div class="card-body">
                        <table class="table table-sm">
                          <tbody>
                            <tr>
                              <td>Cabin Type:</td>
                              <td class="text-end" id="summary_cabin_type">Interior</td>
                            </tr>
                            <tr>
                              <td>Cabin Rate:</td>
                              <td class="text-end" id="summary_cabin_rate">₱0/night</td>
                            </tr>
                            <tr>
                              <td>Duration:</td>
                              <td class="text-end" id="summary_duration">0 nights</td>
                            </tr>
                            <tr>
                              <td>Base Price:</td>
                              <td class="text-end" id="summary_base_price">₱0.00</td>
                            </tr>
                            <tr>
                              <td>Port Fees:</td>
                              <td class="text-end" id="summary_port_fees">₱0.00</td>
                            </tr>
                            <tr>
                              <td>Taxes & Fees:</td>
                              <td class="text-end" id="summary_taxes">₱0.00</td>
                            </tr>
                            <tr>
                              <td>Gratuities:</td>
                              <td class="text-end" id="summary_gratuities">₱0.00</td>
                            </tr>
                            <tr id="summary_discount_row" style="display: none;">
                              <td>Discount:</td>
                              <td class="text-end text-danger" id="summary_discount">-₱0.00</td>
                            </tr>
                            <tr class="table-primary">
                              <th>Total Amount:</th>
                              <th class="text-end" id="summary_total">₱0.00</th>
                            </tr>
                          </tbody>
                        </table>
                        
                        <!-- Hidden fields for pricing -->
                        <input type="hidden" id="form_base_price" name="base_price">
                        <input type="hidden" id="form_port_fee" name="port_fee">
                        <input type="hidden" id="form_taxes" name="taxes">
                        <input type="hidden" id="form_gratuities" name="gratuities">
                        <input type="hidden" id="form_total_amount" name="total_amount">
                        <input type="hidden" id="form_promo_code" name="promo_code">
                        <input type="hidden" id="form_discount_amount" name="discount_amount">
                        
                        <!-- Promo Code -->
                        <div class="mt-3">
                          <label class="form-label">Promo Code</label>
                          <div class="input-group">
                            <input type="text" class="form-control" id="promo_code_input" placeholder="Enter promo code">
                            <button class="btn btn-outline-primary" type="button" id="apply_promo">Apply</button>
                          </div>
                          <div id="promo_message" class="mt-2 small"></div>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Travel Agent -->
                    <div class="card mb-4">
                      <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-person-badge me-2"></i>Travel Agent</h6>
                      </div>
                      <div class="card-body">
                        <select class="form-select" name="agent_id" id="cruise_agent_id">
                          <option value="">No agent / Direct booking</option>
                          <?php while($agent = $agents_result->fetch_assoc()): ?>
                          <option value="<?php echo $agent['agent_id']; ?>" data-rate="<?php echo $agent['commission_rate']; ?>"
                                  <?php echo (isset($_POST['agent_id']) && $_POST['agent_id'] == $agent['agent_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($agent['agent_name']); ?> (<?php echo $agent['commission_rate']; ?>%)
                          </option>
                          <?php endwhile; ?>
                        </select>
                        <div class="alert alert-info mt-3 small" id="agentCommissionInfo" style="display: none;">
                          <i class="bi bi-info-circle"></i> 
                          Agent Commission: <span id="commissionAmount">₱0.00</span>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Payment & Booking -->
                    <div class="card mb-4">
                      <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-credit-card me-2"></i>Payment & Booking</h6>
                      </div>
                      <div class="card-body">
                        <div class="mb-3">
                          <label class="form-label">Payment Method *</label>
                          <select class="form-select" name="payment_method" required>
                            <option value="">Select payment method...</option>
                            <option value="Credit Card" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'Credit Card') ? 'selected' : ''; ?>>💳 Credit Card</option>
                            <option value="Debit Card" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'Debit Card') ? 'selected' : ''; ?>>💳 Debit Card</option>
                            <option value="PayPal" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'PayPal') ? 'selected' : ''; ?>>📱 PayPal</option>
                            <option value="Bank Transfer" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'Bank Transfer') ? 'selected' : ''; ?>>🏦 Bank Transfer</option>
                            <option value="Pay Later" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'Pay Later') ? 'selected' : ''; ?>>⏰ Pay Later</option>
                          </select>
                        </div>
                        
                        <div class="form-check mb-3">
                          <input class="form-check-input" type="checkbox" id="cruise_terms" required>
                          <label class="form-check-label" for="cruise_terms">
                            I agree to the <a href="#" class="text-primary">Cruise Terms & Conditions</a> and understand the cancellation policy *
                          </label>
                        </div>
                        
                        <div class="alert alert-warning small">
                          <i class="bi bi-exclamation-triangle"></i> 
                          <strong>Passport Requirement:</strong> Please ensure your passport is valid for at least 6 months beyond your cruise end date.
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 btn-lg" id="bookCruiseBtn" disabled>
                          <i class="bi bi-check-circle me-2"></i>Book Cruise Now
                        </button>
                      </div>
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
// Global variables
let selectedItinerary = null;
let selectedCruiseLine = null;
let selectedShip = null;
let selectedCabin = 'interior';
let cabinNightRate = <?php echo $cabin_types['interior']['base_price_per_night']; ?>;
let durationNights = 0;
let portFee = 0;
let taxes = 0;
let gratuities = 0;
let discount = 0;
let appliedPromo = null;

// Tab switching
function switchToBrowseTab() {
    const browseTab = document.querySelector('[data-bs-target="#browse-tab"]');
    const tab = new bootstrap.Tab(browseTab);
    tab.show();
}

// Select itinerary from browse tab
document.querySelectorAll('.select-itinerary').forEach(button => {
    button.addEventListener('click', function() {
        selectedItinerary = JSON.parse(this.dataset.itinerary);
        selectedCruiseLine = JSON.parse(this.dataset.cruiseline);
        selectedShip = JSON.parse(this.dataset.ship);
        
        // Update UI
        document.getElementById('noCruiseSelected').style.display = 'none';
        const detailsDiv = document.getElementById('cruiseDetails');
        detailsDiv.style.display = 'block';
        
        document.getElementById('cruiseShipImage').src = selectedShip.image;
        document.getElementById('cruiseItineraryName').textContent = selectedItinerary.name;
        document.getElementById('cruiseLineName').textContent = selectedCruiseLine.name;
        document.getElementById('cruiseShipName').textContent = selectedShip.name;
        document.getElementById('cruiseDuration').textContent = selectedItinerary.duration_nights;
        document.getElementById('cruiseDeparturePort').textContent = selectedItinerary.departure_port;
        document.getElementById('cruisePorts').textContent = selectedItinerary.ports_of_call.join(' → ');
        
        // Update form hidden fields
        document.getElementById('form_cruise_line').value = selectedCruiseLine.name;
        document.getElementById('form_ship_name').value = selectedShip.name;
        document.getElementById('form_itinerary_name').value = selectedItinerary.name;
        document.getElementById('form_departure_port').value = selectedItinerary.departure_port;
        document.getElementById('form_arrival_port').value = selectedItinerary.ports_of_call[selectedItinerary.ports_of_call.length - 1];
        document.getElementById('form_departure_date').value = getFutureDate(30); // 30 days from now
        document.getElementById('form_arrival_date').value = getFutureDate(30 + selectedItinerary.duration_nights);
        document.getElementById('form_duration_nights').value = selectedItinerary.duration_nights;
        document.getElementById('form_ports_of_call').value = selectedItinerary.ports_of_call.join(', ');
        
        // Set duration and update price
        durationNights = selectedItinerary.duration_nights;
        
        // Set port fee based on region (using PHP amounts)
        if (selectedItinerary.name.includes('Southeast Asia')) {
            portFee = 2800;      // ₱2,800
        } else if (selectedItinerary.name.includes('Caribbean')) {
            portFee = 3360;      // ₱3,360
        } else if (selectedItinerary.name.includes('Mediterranean')) {
            portFee = 4480;      // ₱4,480
        } else if (selectedItinerary.name.includes('Alaska')) {
            portFee = 5600;      // ₱5,600
        } else {
            portFee = 3920;      // ₱3,920
        }
        
        // Calculate taxes and gratuities (10% tax, 15% gratuities)
        taxes = (cabinNightRate * durationNights * 0.10);
        gratuities = (cabinNightRate * durationNights * 0.15);
        
        updatePrice();
        updateBookButton();
        
        // Switch to book tab
        const bookTab = document.querySelector('[data-bs-target="#book-tab"]');
        const tab = new bootstrap.Tab(bookTab);
        tab.show();
    });
});

// Cabin selection
document.querySelectorAll('.cabin-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        if (this.checked) {
            selectedCabin = this.value;
            const cabinCard = this.closest('.cabin-card');
            
            // Update visual selection
            document.querySelectorAll('.cabin-card').forEach(card => {
                card.classList.remove('border-primary');
                card.classList.add('border');
            });
            cabinCard.classList.remove('border');
            cabinCard.classList.add('border-primary');
            
            // Update cabin rate
            const priceElement = cabinCard.querySelector('.cabin-price');
            cabinNightRate = parseFloat(priceElement.dataset.base);
            
            updatePrice();
        }
    });
});

// Update price calculation
function updatePrice() {
    if (!selectedItinerary) return;
    
    const guestCount = parseInt(document.getElementById('guest_count').value) || 2;
    const roomCount = parseInt(document.getElementById('room_count').value) || 1;
    
    // Base price calculation
    const basePrice = cabinNightRate * durationNights * roomCount;
    
    // Update taxes and gratuities based on new base price
    taxes = (basePrice * 0.10); // 10% tax
    gratuities = (basePrice * 0.15); // 15% gratuities
    
    // Apply discount if any
    let discountAmount = 0;
    if (appliedPromo) {
        const promoData = <?php echo json_encode($promo_codes); ?>;
        const promo = promoData[appliedPromo];
        const totalBeforeDiscount = basePrice + portFee + taxes + gratuities;
        
        if (promo.type === 'percentage') {
            discountAmount = (totalBeforeDiscount * promo.discount) / 100;
        } else if (promo.type === 'fixed') {
            discountAmount = promo.discount;
        }
    }
    
    discount = discountAmount;
    const totalAmount = basePrice + portFee + taxes + gratuities - discount;
    
    // Update form hidden fields
    document.getElementById('form_base_price').value = basePrice;
    document.getElementById('form_port_fee').value = portFee;
    document.getElementById('form_taxes').value = taxes;
    document.getElementById('form_gratuities').value = gratuities;
    document.getElementById('form_total_amount').value = totalAmount;
    document.getElementById('form_promo_code').value = appliedPromo || '';
    document.getElementById('form_discount_amount').value = discount;
    
    // Update UI
    const cabinTypeName = document.querySelector(`#cabin_${selectedCabin}`).nextElementSibling.querySelector('h6').textContent;
    document.getElementById('summary_cabin_type').textContent = cabinTypeName;
    document.getElementById('summary_cabin_rate').textContent = '₱' + cabinNightRate.toFixed(2) + '/night';
    document.getElementById('summary_duration').textContent = durationNights + ' night' + (durationNights !== 1 ? 's' : '');
    document.getElementById('summary_base_price').textContent = '₱' + basePrice.toFixed(2);
    document.getElementById('summary_port_fees').textContent = '₱' + portFee.toFixed(2);
    document.getElementById('summary_taxes').textContent = '₱' + taxes.toFixed(2);
    document.getElementById('summary_gratuities').textContent = '₱' + gratuities.toFixed(2);
    
    if (discount > 0) {
        document.getElementById('summary_discount_row').style.display = 'table-row';
        document.getElementById('summary_discount').textContent = '-₱' + discount.toFixed(2);
    } else {
        document.getElementById('summary_discount_row').style.display = 'none';
    }
    
    document.getElementById('summary_total').textContent = '₱' + totalAmount.toFixed(2);
    
    // Update agent commission
    updateAgentCommission(totalAmount);
}

// Update agent commission
function updateAgentCommission(totalAmount) {
    const agentSelect = document.getElementById('cruise_agent_id');
    const selectedOption = agentSelect.options[agentSelect.selectedIndex];
    const commissionRate = selectedOption.getAttribute('data-rate');
    
    if (commissionRate && totalAmount > 0) {
        const commission = (totalAmount * commissionRate) / 100;
        document.getElementById('commissionAmount').textContent = '₱' + commission.toFixed(2);
        document.getElementById('agentCommissionInfo').style.display = 'block';
    } else {
        document.getElementById('agentCommissionInfo').style.display = 'none';
    }
}

// Update book button state
function updateBookButton() {
    const hasCruise = selectedItinerary !== null;
    const termsChecked = document.getElementById('cruise_terms').checked;
    
    document.getElementById('bookCruiseBtn').disabled = !(hasCruise && termsChecked);
}

// Event listeners for dynamic updates
document.getElementById('guest_count').addEventListener('change', updatePrice);
document.getElementById('room_count').addEventListener('change', updatePrice);
document.getElementById('cruise_agent_id').addEventListener('change', function() {
    updateAgentCommission(parseFloat(document.getElementById('form_total_amount').value) || 0);
});
document.getElementById('cruise_terms').addEventListener('change', updateBookButton);

// Promo code application
document.getElementById('apply_promo').addEventListener('click', function() {
    const promoCode = document.getElementById('promo_code_input').value.toUpperCase();
    const promoMessage = document.getElementById('promo_message');
    const promoData = <?php echo json_encode($promo_codes); ?>;
    
    if (promoData[promoCode]) {
        const promo = promoData[promoCode];
        let valid = true;
        let message = '';
        
        // Check conditions
        const guestCount = parseInt(document.getElementById('guest_count').value) || 2;
        
        if (promo.min_nights && durationNights < promo.min_nights) {
            valid = false;
            message = `Minimum ${promo.min_nights} nights required for this promo.`;
        }
        
        const totalBeforeDiscount = (cabinNightRate * durationNights) + portFee + taxes + gratuities;
        if (promo.min_amount && totalBeforeDiscount < promo.min_amount) {
            valid = false;
            message = `Minimum amount ₱${promo.min_amount.toLocaleString()} required for this promo.`;
        }
        
        if (promo.min_guests && guestCount < promo.min_guests) {
            valid = false;
            message = `Minimum ${promo.min_guests} guests required for this promo.`;
        }
        
        if (valid) {
            appliedPromo = promoCode;
            promoMessage.innerHTML = `<span class="text-success">✅ Promo code applied! ${promo.discount}${promo.type === 'percentage' ? '%' : '₱'} discount.</span>`;
        } else {
            appliedPromo = null;
            promoMessage.innerHTML = `<span class="text-danger">❌ ${message}</span>`;
        }
    } else {
        appliedPromo = null;
        promoMessage.innerHTML = '<span class="text-danger">❌ Invalid promo code.</span>';
    }
    
    updatePrice();
});

// Helper function to get future date
function getFutureDate(days) {
    const date = new Date();
    date.setDate(date.getDate() + days);
    return date.toISOString().split('T')[0];
}

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tabs
    const triggerTabList = [].slice.call(document.querySelectorAll('#cruiseTabs button'));
    triggerTabList.forEach(function (triggerEl) {
        const tabTrigger = new bootstrap.Tab(triggerEl);
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            tabTrigger.show();
        });
    });
    
    // Initialize price for default cabin
    updatePrice();
});
</script>

<style>
.cabin-card {
    cursor: pointer;
    transition: all 0.2s;
}

.cabin-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.cabin-card.border-primary {
    border-width: 2px !important;
}

.select-itinerary {
    transition: all 0.2s;
}

.select-itinerary:hover {
    transform: scale(1.05);
}

.card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
}

.card-header {
    border-bottom: 1px solid #e0e0e0;
}

.badge {
    font-weight: 500;
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

.nav-tabs .nav-link.active {
    color: #666;
    border-color: #666 #666 #fff;
}

.nav-tabs .nav-link {
    color: #666;
}

.nav-tabs .nav-link:hover {
    color: #333;
    border-color: #e0e0e0 #e0e0e0 #666;
}
</style>

<?php $conn->close(); ?>    