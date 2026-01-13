<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if we need to create the car_rentals table
$check_table = $conn->query("SHOW TABLES LIKE 'car_rentals'");
if ($check_table->num_rows == 0) {
    // Create car rentals table
    $create_table_sql = "CREATE TABLE car_rentals (
        id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id VARCHAR(50) UNIQUE NOT NULL,
        customer_name VARCHAR(100) NOT NULL,
        customer_email VARCHAR(100) NOT NULL,
        customer_phone VARCHAR(20) NOT NULL,
        customer_age INT NOT NULL,
        pickup_date DATE NOT NULL,
        dropoff_date DATE NOT NULL,
        pickup_time TIME NOT NULL,
        dropoff_time TIME NOT NULL,
        pickup_location VARCHAR(200) NOT NULL,
        dropoff_location VARCHAR(200) NOT NULL,
        car_type VARCHAR(50) NOT NULL,
        car_model VARCHAR(100) NOT NULL,
        car_image VARCHAR(255),
        rental_days INT NOT NULL,
        daily_rate DECIMAL(10,2) NOT NULL,
        subtotal DECIMAL(10,2) NOT NULL,
        insurance_fee DECIMAL(10,2) DEFAULT 0,
        additional_fees DECIMAL(10,2) DEFAULT 0,
        total_amount DECIMAL(10,2) NOT NULL,
        promo_code VARCHAR(50),
        discount_amount DECIMAL(10,2) DEFAULT 0,
        agent_id VARCHAR(50),
        agent_commission DECIMAL(10,2) DEFAULT 0,
        status VARCHAR(50) DEFAULT 'Pending',
        payment_method VARCHAR(50),
        payment_status VARCHAR(50) DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (agent_id) REFERENCES travel_agents(agent_id) ON DELETE SET NULL
    )";
    
    if ($conn->query($create_table_sql) === FALSE) {
        echo "Error creating table: " . $conn->error;
    }
}

// Available cars data
$available_cars = [
    'economy' => [
        'name' => 'Economy Car',
        'models' => [
            [
                'id' => 'kia-rio',
                'name' => 'Kia Rio or Similar',
                'image' => 'assets/img/cars/kia-rio.jpg',
                'daily_rate' => 2850,
                'features' => ['4-5 seats', 'Air Conditioning', 'Automatic', 'Fuel Efficient'],
                'type' => 'Economy'
            ],
            [
                'id' => 'toyota-corolla',
                'name' => 'Toyota Corolla or Similar',
                'image' => 'assets/img/cars/toyota-corolla.jpg',
                'daily_rate' => 3150,
                'features' => ['5 seats', 'Air Conditioning', 'Automatic', 'Spacious Trunk'],
                'type' => 'Economy'
            ]
        ]
    ],
    'compact' => [
        'name' => 'Compact Car',
        'models' => [
            [
                'id' => 'honda-civic',
                'name' => 'Honda Civic or Similar',
                'image' => 'assets/img/cars/honda-civic.jpg',
                'daily_rate' => 3650,
                'features' => ['5 seats', 'Premium Sound', 'Automatic', 'GPS Navigation'],
                'type' => 'Compact'
            ]
        ]
    ],
    'suv' => [
        'name' => 'SUV',
        'models' => [
            [
                'id' => 'ford-mustang',
                'name' => 'Ford Mustang or Similar',
                'image' => 'assets/img/cars/ford-mustang.jpg',
                'daily_rate' => 5108,
                'features' => ['4 seats', 'Sports Car', 'Automatic', 'Premium Features'],
                'type' => 'SUV'
            ]
        ]
    ],
    'luxury' => [
        'name' => 'Luxury SUV',
        'models' => [
            [
                'id' => 'bmw-x7',
                'name' => 'BMW X7 or Similar',
                'image' => 'assets/img/cars/bmw-x7.jpg',
                'daily_rate' => 5878,
                'features' => ['7 seats', 'Leather Seats', 'Automatic', 'Premium Package'],
                'type' => 'Luxury'
            ]
        ]
    ],
    'electric' => [
        'name' => 'Electric Car',
        'models' => [
            [
                'id' => 'tesla-model3',
                'name' => 'Tesla Model 3 or Similar',
                'image' => 'assets/img/cars/tesla-model3.jpg',
                'daily_rate' => 4250,
                'features' => ['5 seats', 'Electric', 'Autopilot', 'Premium Interior'],
                'type' => 'Electric'
            ]
        ]
    ]
];

// Available pickup/dropoff locations
$locations = [
    'airport' => [
        'name' => 'Airport',
        'options' => [
            'MNL - Manila Ninoy Aquino International Airport',
            'CEB - Mactan-Cebu International Airport',
            'DVO - Francisco Bangoy International Airport',
            'CRK - Clark International Airport'
        ]
    ],
    'city' => [
        'name' => 'City Center',
        'options' => [
            'Makati City Center',
            'Bonifacio Global City (BGC)',
            'Quezon City - Cubao',
            'Pasay City - Mall of Asia',
            'Mandaluyong City - Ortigas Center'
        ]
    ],
    'station' => [
        'name' => 'Train Station',
        'options' => [
            'MRT Ayala Station',
            'LRT Buendia Station',
            'MRT Taft Station',
            'PITX - Parañaque Integrated Terminal Exchange'
        ]
    ],
    'hotel' => [
        'name' => 'Hotel',
        'options' => [
            'Any Hotel in Metro Manila (Hotel Delivery)',
            'Shangri-La Hotel',
            'Okada Manila',
            'Solaire Resort & Casino'
        ]
    ]
];

// Promo codes
$promo_codes = [
    'WELCOME20' => ['discount' => 20, 'type' => 'percentage', 'min_days' => 3],
    'SUMMER15' => ['discount' => 15, 'type' => 'percentage', 'min_amount' => 10000],
    'CAR50' => ['discount' => 50, 'type' => 'fixed'],
    'WEEKLY10' => ['discount' => 10, 'type' => 'percentage', 'min_days' => 7]
];

// Insurance options
$insurance_options = [
    'basic' => ['name' => 'Basic Insurance', 'daily_rate' => 300, 'coverage' => 'Collision Damage Waiver'],
    'premium' => ['name' => 'Premium Insurance', 'daily_rate' => 600, 'coverage' => 'Full Coverage + Roadside Assistance'],
    'full' => ['name' => 'Full Coverage', 'daily_rate' => 900, 'coverage' => 'Complete Protection Package']
];

// Process booking
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_car'])) {
    // Validate all required fields are present
    $required_fields = [
        'customer_name', 'customer_email', 'customer_phone', 'customer_age',
        'pickup_date', 'dropoff_date', 'pickup_time', 'dropoff_time',
        'pickup_location', 'dropoff_location', 'car_model', 'car_type',
        'daily_rate', 'rental_days', 'subtotal', 'total_amount',
        'payment_method'
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
        // Get form data with proper validation
        $customer_name = $conn->real_escape_string($_POST['customer_name']);
        $customer_email = $conn->real_escape_string($_POST['customer_email']);
        $customer_phone = $conn->real_escape_string($_POST['customer_phone']);
        $customer_age = (int)$_POST['customer_age'];
        
        $pickup_date = $conn->real_escape_string($_POST['pickup_date']);
        $dropoff_date = $conn->real_escape_string($_POST['dropoff_date']);
        $pickup_time = $conn->real_escape_string($_POST['pickup_time']);
        $dropoff_time = $conn->real_escape_string($_POST['dropoff_time']);
        $pickup_location = $conn->real_escape_string($_POST['pickup_location']);
        $dropoff_location = $conn->real_escape_string($_POST['dropoff_location']);
        
        $car_type = $conn->real_escape_string($_POST['car_type']);
        $car_model = $conn->real_escape_string($_POST['car_model']);
        $car_image = isset($_POST['car_image']) ? $conn->real_escape_string($_POST['car_image']) : '';
        
        $rental_days = (int)$_POST['rental_days'];
        $daily_rate = (float)$_POST['daily_rate'];
        $subtotal = (float)$_POST['subtotal'];
        
        // Get insurance and extras with proper default values
        $insurance = isset($_POST['insurance']) ? $conn->real_escape_string($_POST['insurance']) : 'none';
        $insurance_fee = isset($_POST['insurance_fee']) ? (float)$_POST['insurance_fee'] : 0;
        
        $additional_driver = isset($_POST['additional_driver']) ? 1 : 0;
        $gps = isset($_POST['gps']) ? 1 : 0;
        $child_seat = isset($_POST['child_seat']) ? 1 : 0;
        
        $additional_fees = isset($_POST['additional_fees']) ? (float)$_POST['additional_fees'] : 0;
        
        $promo_code = isset($_POST['promo_code']) ? $conn->real_escape_string($_POST['promo_code']) : '';
        $discount_amount = isset($_POST['discount_amount']) ? (float)$_POST['discount_amount'] : 0;
        
        $total_amount = (float)$_POST['total_amount'];
        
        $agent_id_raw = isset($_POST['agent_id']) ? trim($_POST['agent_id']) : '';
        $agent_id = (!empty($agent_id_raw)) ? $conn->real_escape_string($agent_id_raw) : NULL;
        $agent_commission = 0;

        if ($agent_id !== NULL) {
            // Validate that the agent_id exists in travel_agents table
            $agent_check = $conn->query("SELECT agent_id, commission_rate FROM travel_agents WHERE agent_id = '$agent_id' AND status = 'Active'");
            if ($agent_check && $agent_check->num_rows > 0) {
                $agent_data = $agent_check->fetch_assoc();
                $commission_rate = $agent_data['commission_rate'];
                $agent_commission = ($total_amount * $commission_rate) / 100;
            } else {
                // Agent doesn't exist or is inactive, set agent_id to NULL to avoid foreign key constraint
                $agent_id = NULL;
                $message = "⚠️ Selected travel agent is not available. Booking will proceed without agent commission.";
                $message_type = "warning";
            }
        }
        
        $payment_method = $conn->real_escape_string($_POST['payment_method']);
        $status = 'Confirmed';

        // Generate booking ID
        $booking_id = 'CAR-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

        // Insert into database - handle NULL agent_id properly
        $sql = "INSERT INTO car_rentals (
            booking_id, customer_name, customer_email, customer_phone, customer_age,
            pickup_date, dropoff_date, pickup_time, dropoff_time,
            pickup_location, dropoff_location, car_type, car_model, car_image,
            rental_days, daily_rate, subtotal, insurance_fee, additional_fees,
            total_amount, promo_code, discount_amount, agent_id, agent_commission,
            status, payment_method
        ) VALUES (
            '$booking_id', '$customer_name', '$customer_email', '$customer_phone', $customer_age,
            '$pickup_date', '$dropoff_date', '$pickup_time', '$dropoff_time',
            '$pickup_location', '$dropoff_location', '$car_type', '$car_model', '$car_image',
            $rental_days, $daily_rate, $subtotal, $insurance_fee, $additional_fees,
            $total_amount, '$promo_code', $discount_amount, " . ($agent_id === NULL ? "NULL" : "'$agent_id'") . ", $agent_commission,
            '$status', '$payment_method'
        )";
        
        if ($conn->query($sql)) {
            // Update agent's total bookings if applicable
            if (!empty($agent_id)) {
                $conn->query("UPDATE travel_agents SET total_bookings = total_bookings + 1 WHERE agent_id = '$agent_id'");
            }
            
            $message = "✅ Car rental booked successfully! ";
            $message .= "Booking ID: <strong>" . $booking_id . "</strong><br>";
            $message .= "Total Amount: <strong>₱" . number_format($total_amount, 2) . "</strong>";
            
            if ($agent_commission > 0) {
                $message .= "<br>📊 Agent Commission: <strong>₱" . number_format($agent_commission, 2) . "</strong>";
            }
            
            $message_type = "success";
            
            // Clear form data after successful submission
            $_POST = [];
        } else {
            $message = "❌ Error creating booking: " . $conn->error;
            $message_type = "error";
        }
    }
}

// Get active agents for dropdown
$agents_result = $conn->query("SELECT agent_id, agent_name, commission_rate FROM travel_agents WHERE status = 'Active' AND (specialization = 'All' OR specialization = 'Car Rental') ORDER BY agent_name");
?>

<div class="pagetitle">
  <h1>Car Rental</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">Car Rental</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      
      <?php if ($message): ?>
      <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : ($message_type === 'warning' ? 'warning' : 'danger'); ?> alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php endif; ?>
      
      <div class="card">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-car-front me-2"></i>Rent a Car</h5>
          
          <div class="row">
            <!-- Left Column: Car Selection -->
            <div class="col-lg-8">
              <div class="card mb-4">
                <div class="card-header bg-light">
                  <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filters & Car Selection</h6>
                </div>
                <div class="card-body">
                  
                  <!-- Car Type Filters -->
                  <div class="mb-4">
                    <h6 class="mb-3">Car Type</h6>
                    <div class="d-flex flex-wrap gap-2">
                      <button class="btn btn-sm btn-outline-primary car-filter active" data-type="all">All</button>
                      <?php foreach($available_cars as $type => $car_data): ?>
                      <button class="btn btn-sm btn-outline-primary car-filter" data-type="<?php echo $type; ?>">
                        <?php echo $car_data['name']; ?>
                      </button>
                      <?php endforeach; ?>
                    </div>
                  </div>
                  
                  <!-- Car Selection Grid -->
                  <div class="row" id="car-grid">
                    <?php foreach($available_cars as $type => $car_data): ?>
                      <?php foreach($car_data['models'] as $car): ?>
                      <div class="col-md-6 mb-4 car-item" data-type="<?php echo $type; ?>">
                        <div class="card h-100 border">
                          <div class="position-relative">
                            <img src="<?php echo $car['image']; ?>" class="card-img-top" alt="<?php echo $car['name']; ?>" style="height: 200px; object-fit: cover;">
                            <span class="position-absolute top-0 start-0 badge bg-primary m-2"><?php echo $car['type']; ?></span>
                          </div>
                          <div class="card-body">
                            <h6 class="card-title"><?php echo $car['name']; ?></h6>
                            <div class="mb-3">
                              <?php foreach($car['features'] as $feature): ?>
                              <span class="badge bg-light text-dark me-1 mb-1"><?php echo $feature; ?></span>
                              <?php endforeach; ?>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                              <div>
                                <h5 class="text-primary mb-0">₱<?php echo number_format($car['daily_rate'], 2); ?><small class="text-muted">/day</small></h5>
                              </div>
                              <button class="btn btn-primary btn-sm select-car" 
                                      data-id="<?php echo $car['id']; ?>"
                                      data-name="<?php echo $car['name']; ?>"
                                      data-type="<?php echo $car['type']; ?>"
                                      data-image="<?php echo $car['image']; ?>"
                                      data-rate="<?php echo $car['daily_rate']; ?>">
                                <i class="bi bi-check-lg"></i> Select
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <?php endforeach; ?>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
              
              <!-- Rental Details -->
              <div class="card mb-4">
                <div class="card-header bg-light">
                  <h6 class="mb-0"><i class="bi bi-calendar-range me-2"></i>Rental Details</h6>
                </div>
                <div class="card-body">
                  <form id="rentalForm">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label class="form-label">Pick-up Date *</label>
                        <input type="date" class="form-control" id="pickup_date" name="pickup_date" required min="<?php echo date('Y-m-d'); ?>" value="<?php echo isset($_POST['pickup_date']) ? htmlspecialchars($_POST['pickup_date']) : ''; ?>">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Pick-up Time *</label>
                        <input type="time" class="form-control" id="pickup_time" name="pickup_time" required value="<?php echo isset($_POST['pickup_time']) ? htmlspecialchars($_POST['pickup_time']) : '10:00'; ?>">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Drop-off Date *</label>
                        <input type="date" class="form-control" id="dropoff_date" name="dropoff_date" required value="<?php echo isset($_POST['dropoff_date']) ? htmlspecialchars($_POST['dropoff_date']) : ''; ?>">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Drop-off Time *</label>
                        <input type="time" class="form-control" id="dropoff_time" name="dropoff_time" required value="<?php echo isset($_POST['dropoff_time']) ? htmlspecialchars($_POST['dropoff_time']) : '10:00'; ?>">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Pick-up Location *</label>
                        <select class="form-select" id="pickup_location" name="pickup_location" required>
                          <option value="">Select location...</option>
                          <?php foreach($locations as $loc_type => $loc_data): ?>
                            <optgroup label="<?php echo $loc_data['name']; ?>">
                              <?php foreach($loc_data['options'] as $option): ?>
                              <option value="<?php echo $option; ?>" <?php echo (isset($_POST['pickup_location']) && $_POST['pickup_location'] == $option) ? 'selected' : ''; ?>>
                                <?php echo $option; ?>
                              </option>
                              <?php endforeach; ?>
                            </optgroup>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Drop-off Location *</label>
                        <select class="form-select" id="dropoff_location" name="dropoff_location" required>
                          <option value="">Select location...</option>
                          <?php foreach($locations as $loc_type => $loc_data): ?>
                            <optgroup label="<?php echo $loc_data['name']; ?>">
                              <?php foreach($loc_data['options'] as $option): ?>
                              <option value="<?php echo $option; ?>" <?php echo (isset($_POST['dropoff_location']) && $_POST['dropoff_location'] == $option) ? 'selected' : ''; ?>>
                                <?php echo $option; ?>
                              </option>
                              <?php endforeach; ?>
                            </optgroup>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
              
              <!-- Extras -->
              <div class="card mb-4">
                <div class="card-header bg-light">
                  <h6 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Extras & Insurance</h6>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <h6>Insurance Options</h6>
                      <?php foreach($insurance_options as $key => $insurance): ?>
                      <div class="form-check mb-2">
                        <input class="form-check-input insurance-option" type="radio" name="insurance" id="insurance_<?php echo $key; ?>" value="<?php echo $key; ?>" 
                               <?php echo (isset($_POST['insurance']) && $_POST['insurance'] == $key) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="insurance_<?php echo $key; ?>">
                          <strong><?php echo $insurance['name']; ?></strong><br>
                          <small class="text-muted">₱<?php echo $insurance['daily_rate']; ?>/day - <?php echo $insurance['coverage']; ?></small>
                        </label>
                      </div>
                      <?php endforeach; ?>
                      <div class="form-check">
                        <input class="form-check-input insurance-option" type="radio" name="insurance" id="insurance_none" value="none" 
                               <?php echo (!isset($_POST['insurance']) || $_POST['insurance'] == 'none') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="insurance_none">
                          <strong>No Insurance</strong><br>
                          <small class="text-muted">Decline insurance coverage</small>
                        </label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <h6>Additional Options</h6>
                      <div class="form-check mb-3">
                        <input class="form-check-input extra-option" type="checkbox" id="additional_driver" name="additional_driver"
                               <?php echo (isset($_POST['additional_driver']) && $_POST['additional_driver']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="additional_driver">
                          <strong>Additional Driver</strong><br>
                          <small class="text-muted">₱500/day - Add another driver</small>
                        </label>
                      </div>
                      <div class="form-check mb-3">
                        <input class="form-check-input extra-option" type="checkbox" id="gps" name="gps"
                               <?php echo (isset($_POST['gps']) && $_POST['gps']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="gps">
                          <strong>GPS Navigation</strong><br>
                          <small class="text-muted">₱300/day - GPS rental</small>
                        </label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input extra-option" type="checkbox" id="child_seat" name="child_seat"
                               <?php echo (isset($_POST['child_seat']) && $_POST['child_seat']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="child_seat">
                          <strong>Child Safety Seat</strong><br>
                          <small class="text-muted">₱200/day - For children</small>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                  <h6 class="mb-0"><i class="bi bi-car-front me-2"></i>Selected Car</h6>
                </div>
                <div class="card-body text-center">
                  <div id="selectedCarPlaceholder" class="text-muted">
                    <i class="bi bi-car-front display-1 text-muted"></i>
                    <p class="mt-3">No car selected yet</p>
                  </div>
                  <div id="selectedCarDetails" style="display: none;">
                    <img id="selectedCarImage" src="" class="img-fluid rounded mb-3" alt="Selected Car" style="max-height: 150px;">
                    <h5 id="selectedCarName" class="text-primary"></h5>
                    <p id="selectedCarType" class="text-muted"></p>
                    <h4 id="selectedCarRate" class="text-success"></h4>
                    <input type="hidden" id="selected_car_id" name="car_id">
                    <input type="hidden" id="selected_car_name" name="car_name">
                    <input type="hidden" id="selected_car_type" name="car_type">
                    <input type="hidden" id="selected_car_image" name="car_image">
                    <input type="hidden" id="selected_car_rate" name="daily_rate">
                  </div>
                </div>
              </div>
              <div class="card mb-4">
                <div class="card-header bg-light">
                  <h6 class="mb-0"><i class="bi bi-receipt me-2"></i>Price Breakdown</h6>
                </div>
                <div class="card-body">
                  <table class="table table-sm">
                    <tbody>
                      <tr>
                        <td>Daily Rate:</td>
                        <td class="text-end" id="price_daily">₱0.00</td>
                      </tr>
                      <tr>
                        <td>Rental Days:</td>
                        <td class="text-end" id="price_days">0 days</td>
                      </tr>
                      <tr>
                        <td>Subtotal:</td>
                        <td class="text-end" id="price_subtotal">₱0.00</td>
                      </tr>
                      <tr>
                        <td>Insurance:</td>
                        <td class="text-end" id="price_insurance">₱0.00</td>
                      </tr>
                      <tr>
                        <td>Additional Fees:</td>
                        <td class="text-end" id="price_extras">₱0.00</td>
                      </tr>
                      <tr id="discount_row" style="display: none;">
                        <td>Discount:</td>
                        <td class="text-end text-danger" id="price_discount">-₱0.00</td>
                      </tr>
                      <tr class="table-primary">
                        <th>Total Amount:</th>
                        <th class="text-end" id="price_total">₱0.00</th>
                      </tr>
                    </tbody>
                  </table>
                  
                  <!-- Promo Code -->
                  <div class="mt-3">
                    <label class="form-label">Promo Code</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="promo_code" name="promo_code" placeholder="Enter promo code" value="<?php echo isset($_POST['promo_code']) ? htmlspecialchars($_POST['promo_code']) : ''; ?>">
                      <button class="btn btn-outline-primary" type="button" id="apply_promo">Apply</button>
                    </div>
                    <div id="promo_message" class="mt-2 small"></div>
                  </div>
                </div>
              </div>
              
              <!-- Customer Information -->
              <div class="card mb-4">
                <div class="card-header bg-light">
                  <h6 class="mb-0"><i class="bi bi-person-circle me-2"></i>Customer Information</h6>
                </div>
                <div class="card-body">
                  <form id="customerForm" method="POST" action="">
                    <input type="hidden" name="book_car" value="1">
                    
                    <!-- Car Details Hidden Fields -->
                    <input type="hidden" id="form_car_model" name="car_model" value="<?php echo isset($_POST['car_model']) ? htmlspecialchars($_POST['car_model']) : ''; ?>">
                    <input type="hidden" id="form_car_type" name="car_type" value="<?php echo isset($_POST['car_type']) ? htmlspecialchars($_POST['car_type']) : ''; ?>">
                    <input type="hidden" id="form_car_image" name="car_image" value="<?php echo isset($_POST['car_image']) ? htmlspecialchars($_POST['car_image']) : ''; ?>">
                    <input type="hidden" id="form_daily_rate" name="daily_rate" value="<?php echo isset($_POST['daily_rate']) ? htmlspecialchars($_POST['daily_rate']) : ''; ?>">
                    <input type="hidden" id="form_rental_days" name="rental_days" value="<?php echo isset($_POST['rental_days']) ? htmlspecialchars($_POST['rental_days']) : ''; ?>">
                    <input type="hidden" id="form_subtotal" name="subtotal" value="<?php echo isset($_POST['subtotal']) ? htmlspecialchars($_POST['subtotal']) : ''; ?>">
                    
                    <!-- Rental Details Hidden Fields -->
                    <input type="hidden" id="form_pickup_date" name="pickup_date" value="<?php echo isset($_POST['pickup_date']) ? htmlspecialchars($_POST['pickup_date']) : ''; ?>">
                    <input type="hidden" id="form_dropoff_date" name="dropoff_date" value="<?php echo isset($_POST['dropoff_date']) ? htmlspecialchars($_POST['dropoff_date']) : ''; ?>">
                    <input type="hidden" id="form_pickup_time" name="pickup_time" value="<?php echo isset($_POST['pickup_time']) ? htmlspecialchars($_POST['pickup_time']) : ''; ?>">
                    <input type="hidden" id="form_dropoff_time" name="dropoff_time" value="<?php echo isset($_POST['dropoff_time']) ? htmlspecialchars($_POST['dropoff_time']) : ''; ?>">
                    <input type="hidden" id="form_pickup_location" name="pickup_location" value="<?php echo isset($_POST['pickup_location']) ? htmlspecialchars($_POST['pickup_location']) : ''; ?>">
                    <input type="hidden" id="form_dropoff_location" name="dropoff_location" value="<?php echo isset($_POST['dropoff_location']) ? htmlspecialchars($_POST['dropoff_location']) : ''; ?>">
                    
                    <!-- Extras Hidden Fields -->
                    <input type="hidden" id="form_insurance" name="insurance" value="<?php echo isset($_POST['insurance']) ? htmlspecialchars($_POST['insurance']) : 'none'; ?>">
                    <input type="hidden" id="form_insurance_fee" name="insurance_fee" value="<?php echo isset($_POST['insurance_fee']) ? htmlspecialchars($_POST['insurance_fee']) : '0'; ?>">
                    <input type="hidden" id="form_additional_driver" name="additional_driver" value="<?php echo isset($_POST['additional_driver']) ? htmlspecialchars($_POST['additional_driver']) : '0'; ?>">
                    <input type="hidden" id="form_gps" name="gps" value="<?php echo isset($_POST['gps']) ? htmlspecialchars($_POST['gps']) : '0'; ?>">
                    <input type="hidden" id="form_child_seat" name="child_seat" value="<?php echo isset($_POST['child_seat']) ? htmlspecialchars($_POST['child_seat']) : '0'; ?>">
                    <input type="hidden" id="form_additional_fees" name="additional_fees" value="<?php echo isset($_POST['additional_fees']) ? htmlspecialchars($_POST['additional_fees']) : '0'; ?>">
                    
                    <!-- Promo Hidden Fields -->
                    <input type="hidden" id="form_promo_code" name="promo_code" value="<?php echo isset($_POST['promo_code']) ? htmlspecialchars($_POST['promo_code']) : ''; ?>">
                    <input type="hidden" id="form_discount_amount" name="discount_amount" value="<?php echo isset($_POST['discount_amount']) ? htmlspecialchars($_POST['discount_amount']) : '0'; ?>">
                    
                    <!-- Total Amount -->
                    <input type="hidden" id="form_total_amount" name="total_amount" value="<?php echo isset($_POST['total_amount']) ? htmlspecialchars($_POST['total_amount']) : '0'; ?>">
                    
                    <div class="mb-3">
                      <label class="form-label">Full Name *</label>
                      <input type="text" class="form-control" name="customer_name" required value="<?php echo isset($_POST['customer_name']) ? htmlspecialchars($_POST['customer_name']) : ''; ?>">
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Email Address *</label>
                      <input type="email" class="form-control" name="customer_email" required value="<?php echo isset($_POST['customer_email']) ? htmlspecialchars($_POST['customer_email']) : ''; ?>">
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Phone Number *</label>
                      <input type="tel" class="form-control" name="customer_phone" required value="<?php echo isset($_POST['customer_phone']) ? htmlspecialchars($_POST['customer_phone']) : ''; ?>">
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Age *</label>
                      <input type="number" class="form-control" name="customer_age" min="18" max="99" required value="<?php echo isset($_POST['customer_age']) ? htmlspecialchars($_POST['customer_age']) : ''; ?>">
                    </div>
                    
                    <!-- Travel Agent -->
                    <div class="mb-3">
                      <label class="form-label">Travel Agent (Optional)</label>
                      <select class="form-select" name="agent_id" id="car_agent_id">
                        <option value="">No agent / Direct booking</option>
                        <?php 
                        $agents_result->data_seek(0);
                        while($agent = $agents_result->fetch_assoc()): 
                        ?>
                        <option value="<?php echo $agent['agent_id']; ?>" data-rate="<?php echo $agent['commission_rate']; ?>"
                                <?php echo (isset($_POST['agent_id']) && $_POST['agent_id'] == $agent['agent_id']) ? 'selected' : ''; ?>>
                          <?php echo htmlspecialchars($agent['agent_name']); ?> (<?php echo $agent['commission_rate']; ?>%)
                        </option>
                        <?php endwhile; ?>
                      </select>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="mb-3">
                      <label class="form-label">Payment Method *</label>
                      <select class="form-select" name="payment_method" required>
                        <option value="">Select payment method...</option>
                        <option value="Credit Card" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'Credit Card') ? 'selected' : ''; ?>>💳 Credit Card</option>
                        <option value="Debit Card" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'Debit Card') ? 'selected' : ''; ?>>💳 Debit Card</option>
                        <option value="PayPal" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'PayPal') ? 'selected' : ''; ?>>📱 PayPal</option>
                        <option value="GCash" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'GCash') ? 'selected' : ''; ?>>📱 GCash</option>
                        <option value="Pay at Pickup" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'Pay at Pickup') ? 'selected' : ''; ?>>💵 Pay at Pickup</option>
                      </select>
                    </div>
                    
                    <!-- Terms & Conditions -->
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="checkbox" id="terms" required <?php echo (isset($_POST['terms']) && $_POST['terms']) ? 'checked' : ''; ?>>
                      <label class="form-check-label" for="terms">
                        I agree to the <a href="#" class="text-primary">Terms & Conditions</a> and <a href="#" class="text-primary">Rental Agreement</a> *
                      </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100" id="bookNowBtn" disabled>
                      <i class="bi bi-check-circle me-2"></i>Book Now
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
// Car filtering
document.querySelectorAll('.car-filter').forEach(button => {
    button.addEventListener('click', function() {
        // Update active button
        document.querySelectorAll('.car-filter').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        
        const filterType = this.dataset.type;
        const carItems = document.querySelectorAll('.car-item');
        
        carItems.forEach(item => {
            if (filterType === 'all' || item.dataset.type === filterType) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});

// Car selection
let selectedCar = null;
let rentalDays = 0;
let insuranceFee = 0;
let additionalFees = 0;
let discount = 0;
let appliedPromo = null;

document.querySelectorAll('.select-car').forEach(button => {
    button.addEventListener('click', function() {
        selectedCar = {
            id: this.dataset.id,
            name: this.dataset.name,
            type: this.dataset.type,
            image: this.dataset.image,
            rate: parseFloat(this.dataset.rate)
        };
        
        // Update UI
        document.getElementById('selectedCarPlaceholder').style.display = 'none';
        const detailsDiv = document.getElementById('selectedCarDetails');
        detailsDiv.style.display = 'block';
        
        document.getElementById('selectedCarImage').src = selectedCar.image;
        document.getElementById('selectedCarName').textContent = selectedCar.name;
        document.getElementById('selectedCarType').textContent = selectedCar.type + ' Car';
        document.getElementById('selectedCarRate').textContent = '₱' + selectedCar.rate.toFixed(2) + '/day';
        
        // Update form hidden fields
        document.getElementById('form_car_model').value = selectedCar.name;
        document.getElementById('form_car_type').value = selectedCar.type;
        document.getElementById('form_car_image').value = selectedCar.image;
        document.getElementById('form_daily_rate').value = selectedCar.rate;
        
        updatePrice();
        updateBookNowButton();
    });
});

// Calculate rental days
function calculateRentalDays() {
    const pickupDate = new Date(document.getElementById('pickup_date').value);
    const dropoffDate = new Date(document.getElementById('dropoff_date').value);
    
    if (pickupDate && dropoffDate && dropoffDate > pickupDate) {
        const diffTime = Math.abs(dropoffDate - pickupDate);
        rentalDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        return rentalDays;
    }
    return 0;
}

// Update price calculation
function updatePrice() {
    if (!selectedCar) return;
    
    const days = calculateRentalDays();
    const subtotal = selectedCar.rate * days;
    
    // Update hidden form fields
    document.getElementById('form_rental_days').value = days;
    document.getElementById('form_subtotal').value = subtotal;
    
    // Update rental details hidden fields
    document.getElementById('form_pickup_date').value = document.getElementById('pickup_date').value;
    document.getElementById('form_dropoff_date').value = document.getElementById('dropoff_date').value;
    document.getElementById('form_pickup_time').value = document.getElementById('pickup_time').value;
    document.getElementById('form_dropoff_time').value = document.getElementById('dropoff_time').value;
    document.getElementById('form_pickup_location').value = document.getElementById('pickup_location').value;
    document.getElementById('form_dropoff_location').value = document.getElementById('dropoff_location').value;
    
    // Calculate extras
    additionalFees = 0;
    const additionalDriver = document.getElementById('additional_driver').checked;
    const gps = document.getElementById('gps').checked;
    const childSeat = document.getElementById('child_seat').checked;
    
    if (additionalDriver) additionalFees += 500 * days;
    if (gps) additionalFees += 300 * days;
    if (childSeat) additionalFees += 200 * days;
    
    // Update extras hidden fields
    document.getElementById('form_additional_driver').value = additionalDriver ? '1' : '0';
    document.getElementById('form_gps').value = gps ? '1' : '0';
    document.getElementById('form_child_seat').value = childSeat ? '1' : '0';
    document.getElementById('form_additional_fees').value = additionalFees;
    
    // Calculate insurance
    const selectedInsurance = document.querySelector('input[name="insurance"]:checked');
    insuranceFee = 0;
    let insuranceValue = 'none';
    
    if (selectedInsurance && selectedInsurance.value !== 'none') {
        const insuranceData = <?php echo json_encode($insurance_options); ?>;
        insuranceFee = insuranceData[selectedInsurance.value].daily_rate * days;
        insuranceValue = selectedInsurance.value;
    }
    
    // Update insurance hidden fields
    document.getElementById('form_insurance').value = insuranceValue;
    document.getElementById('form_insurance_fee').value = insuranceFee;
    
    // Apply discount
    let discountAmount = 0;
    if (appliedPromo) {
        const promo = <?php echo json_encode($promo_codes); ?>[appliedPromo];
        const totalBeforeDiscount = subtotal + insuranceFee + additionalFees;
        
        if (promo.type === 'percentage') {
            discountAmount = (totalBeforeDiscount * promo.discount) / 100;
        } else if (promo.type === 'fixed') {
            discountAmount = promo.discount;
        }
    }
    
    discount = discountAmount;
    const totalAmount = subtotal + insuranceFee + additionalFees - discount;
    
    // Update promo hidden fields
    document.getElementById('form_promo_code').value = appliedPromo || '';
    document.getElementById('form_discount_amount').value = discount;
    
    // Update total amount hidden field
    document.getElementById('form_total_amount').value = totalAmount;
    
    // Update UI
    document.getElementById('price_daily').textContent = '₱' + selectedCar.rate.toFixed(2);
    document.getElementById('price_days').textContent = days + ' day' + (days !== 1 ? 's' : '');
    document.getElementById('price_subtotal').textContent = '₱' + subtotal.toFixed(2);
    document.getElementById('price_insurance').textContent = '₱' + insuranceFee.toFixed(2);
    document.getElementById('price_extras').textContent = '₱' + additionalFees.toFixed(2);
    
    if (discount > 0) {
        document.getElementById('discount_row').style.display = 'table-row';
        document.getElementById('price_discount').textContent = '-₱' + discount.toFixed(2);
    } else {
        document.getElementById('discount_row').style.display = 'none';
    }
    
    document.getElementById('price_total').textContent = '₱' + totalAmount.toFixed(2);
}

// Update book now button state
function updateBookNowButton() {
    const hasCar = selectedCar !== null;
    const hasDates = calculateRentalDays() > 0;
    const hasPickup = document.getElementById('pickup_location').value !== '';
    const hasDropoff = document.getElementById('dropoff_location').value !== '';
    const termsChecked = document.getElementById('terms').checked;
    
    document.getElementById('bookNowBtn').disabled = !(hasCar && hasDates && hasPickup && hasDropoff && termsChecked);
}

// Event listeners for price updates
document.getElementById('pickup_date').addEventListener('change', function() {
    const dropoffDate = document.getElementById('dropoff_date');
    const minDate = new Date(this.value);
    minDate.setDate(minDate.getDate() + 1);
    dropoffDate.min = minDate.toISOString().split('T')[0];
    
    // If dropoff date is before new minimum, adjust it
    if (new Date(dropoffDate.value) < minDate) {
        dropoffDate.value = minDate.toISOString().split('T')[0];
    }
    
    updatePrice();
    updateBookNowButton();
});

document.getElementById('dropoff_date').addEventListener('change', function() {
    updatePrice();
    updateBookNowButton();
});

document.getElementById('pickup_time').addEventListener('change', updatePrice);
document.getElementById('dropoff_time').addEventListener('change', updatePrice);

document.getElementById('pickup_location').addEventListener('change', function() {
    updatePrice();
    updateBookNowButton();
});

document.getElementById('dropoff_location').addEventListener('change', function() {
    updatePrice();
    updateBookNowButton();
});

// Insurance and extras change
document.querySelectorAll('.insurance-option, .extra-option').forEach(element => {
    element.addEventListener('change', updatePrice);
});
document.getElementById('apply_promo').addEventListener('click', function() {
    const promoCode = document.getElementById('promo_code').value.toUpperCase();
    const promoMessage = document.getElementById('promo_message');
    const promoData = <?php echo json_encode($promo_codes); ?>;   
    if (promoData[promoCode]) {
        const promo = promoData[promoCode];
        let valid = true;
        let message = '';
        if (promo.min_days && rentalDays < promo.min_days) {
            valid = false;
            message = `Minimum ${promo.min_days} days required for this promo.`;
        }
        const totalBeforeDiscount = (selectedCar ? selectedCar.rate * rentalDays : 0) + insuranceFee + additionalFees;
        if (promo.min_amount && totalBeforeDiscount < promo.min_amount) {
            valid = false;
            message = `Minimum amount ₱${promo.min_amount} required for this promo.`;
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
document.getElementById('customerForm').addEventListener('submit', function(e) {
    if (!selectedCar) {
        e.preventDefault();
        alert('Please select a car first.');
        return;
    }    
    if (calculateRentalDays() === 0) {
        e.preventDefault();
        alert('Please select valid rental dates.');
        return;
    }
    if (!document.getElementById('form_pickup_date').value || 
        !document.getElementById('form_dropoff_date').value ||
        !document.getElementById('form_pickup_location').value ||
        !document.getElementById('form_dropoff_location').value) {
        e.preventDefault();
        alert('Please complete all rental details.');
        return;
    }
});
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    if (!document.getElementById('pickup_date').value) {
        document.getElementById('pickup_date').value = today.toISOString().split('T')[0];
    }
    
    if (!document.getElementById('dropoff_date').value) {
        document.getElementById('dropoff_date').value = tomorrow.toISOString().split('T')[0];
    }
    const pickupDate = new Date(document.getElementById('pickup_date').value);
    const minDropoff = new Date(pickupDate);
    minDropoff.setDate(minDropoff.getDate() + 1);
    document.getElementById('dropoff_date').min = minDropoff.toISOString().split('T')[0];
    document.getElementById('terms').addEventListener('change', updateBookNowButton);
    if (document.getElementById('form_car_model').value) {
        selectedCar = {
            id: document.getElementById('form_car_model').value.toLowerCase().replace(/ /g, '-'),
            name: document.getElementById('form_car_model').value,
            type: document.getElementById('form_car_type').value,
            image: document.getElementById('form_car_image').value,
            rate: parseFloat(document.getElementById('form_daily_rate').value)
        };
        document.getElementById('selectedCarPlaceholder').style.display = 'none';
        const detailsDiv = document.getElementById('selectedCarDetails');
        detailsDiv.style.display = 'block';
        document.getElementById('selectedCarImage').src = selectedCar.image;
        document.getElementById('selectedCarName').textContent = selectedCar.name;
        document.getElementById('selectedCarType').textContent = selectedCar.type + ' Car';
        document.getElementById('selectedCarRate').textContent = '₱' + selectedCar.rate.toFixed(2) + '/day';
        updatePrice();
        updateBookNowButton();
    }
});
</script>

<style>
.car-item {
    transition: transform 0.2s;
}

.car-item:hover {
    transform: translateY(-5px);
}

.select-car {
    transition: all 0.2s;
}

.select-car:hover {
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

.form-check-input:checked {
    background-color: #666;
    border-color: #666;
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

.btn-outline-primary:hover,
.btn-outline-primary.active {
    background-color: #666;
    border-color: #666;
    color: white;
}
</style>

<?php $conn->close(); ?>