<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get current step
$step = isset($_GET['step']) ? (int)$_GET['step'] : 3;
$booking_data = $_SESSION['car_booking'] ?? [];

// Options data
$options = [
    'popular' => [
        'extra_driver' => ['name' => 'Extra Driver', 'price' => 945, 'per_day' => true],
        '4g_wifi' => ['name' => '4G Wifi', 'price' => 1323, 'per_day' => true],
        'gps' => ['name' => 'GPS', 'price' => 1015, 'per_day' => true]
    ],
    'additional' => [
        'road_map' => ['name' => 'Philippines Road Map', 'price' => 3206, 'per_day' => false],
        'young_driver' => ['name' => 'Young Driver Fee', 'price' => 2030, 'per_day' => true],
        'child_seat_large' => ['name' => 'Child Seat 15-36 kg', 'price' => 686, 'per_day' => true],
        'child_seat_small' => ['name' => 'Child Seat 0-18 kg', 'price' => 686, 'per_day' => true],
        'booster_seat' => ['name' => 'Booster Seat', 'price' => 336, 'per_day' => true],
        'toll_fee' => ['name' => 'SLEX/NLEX Toll Fee', 'price' => 1750, 'per_day' => false]
    ],
    'included' => [
        'fuel_discount' => ['name' => 'Cheaper fuel and discounts', 'price' => 0],
        'unlimited_mileage' => ['name' => 'Unlimited Mileage', 'price' => 0],
        'winter_tires' => ['name' => 'All-weather tires', 'price' => 0],
        'free_cancellation' => ['name' => 'FREE cancellation', 'price' => 0]
    ]
];

// Sample booking data
$sample_booking = [
    'car' => 'Toyota Vios (manual) or similar',
    'pickup_location' => 'Manila Ninoy Aquino International Airport',
    'pickup_date' => '2025-02-13 09:00',
    'dropoff_location' => 'Manila Ninoy Aquino International Airport', 
    'dropoff_date' => '2025-02-17 09:00',
    'days' => 4,
    'base_price' => 13698
];

// Process form submission
if ($_POST) {
    $_SESSION['car_booking_options'] = $_POST;
    if (isset($_POST['continue'])) {
        header('Location: ?step=' . ($step + 1));
        exit;
    }
}

$selected_options = $_SESSION['car_booking_options'] ?? [];
?>

<div class="pagetitle">
    <h1>Car Rental Booking</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active">Car Rental</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-8">
            <!-- Progress Steps -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="step-item <?php echo $step >= 1 ? 'completed' : ''; ?>">
                            <div class="step-number">1</div>
                            <div class="step-label">Car</div>
                        </div>
                        <div class="step-item <?php echo $step >= 2 ? 'completed' : ''; ?>">
                            <div class="step-number">2</div>
                            <div class="step-label">Insurance</div>
                        </div>
                        <div class="step-item <?php echo $step >= 3 ? 'active' : ''; ?>">
                            <div class="step-number">3</div>
                            <div class="step-label">Options</div>
                        </div>
                        <div class="step-item">
                            <div class="step-number">4</div>
                            <div class="step-label">Details</div>
                        </div>
                        <div class="step-item">
                            <div class="step-number">5</div>
                            <div class="step-label">Confirmation</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Options Selection -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Select your options</h5>
                    
                    <form method="POST">
                        <!-- Popular Options -->
                        <div class="mb-5">
                            <h6 class="mb-3 text-primary">Popular options</h6>
                            <?php foreach($options['popular'] as $key => $option): ?>
                            <div class="option-item border rounded p-3 mb-3">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <strong><?php echo $option['name']; ?></strong>
                                            <i class="bi bi-info-circle ms-2 text-muted" data-bs-toggle="tooltip" title="Additional information"></i>
                                        </div>
                                        <div class="text-muted">₱<?php echo number_format($option['price']); ?><?php echo $option['per_day'] ? ' / per day' : ''; ?></div>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <div class="d-flex align-items-center justify-content-end">
                                            <button type="button" class="btn btn-outline-secondary btn-sm me-2 quantity-btn" data-action="decrease" data-target="<?php echo $key; ?>">-</button>
                                            <input type="number" name="<?php echo $key; ?>" id="<?php echo $key; ?>" class="form-control text-center quantity-input" style="width: 60px;" min="0" max="5" value="<?php echo $selected_options[$key] ?? 0; ?>">
                                            <button type="button" class="btn btn-outline-secondary btn-sm ms-2 quantity-btn" data-action="increase" data-target="<?php echo $key; ?>">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Additional Options -->
                        <div class="mb-5">
                            <h6 class="mb-3 text-primary">Additional options</h6>
                            <?php foreach($options['additional'] as $key => $option): ?>
                            <div class="option-item border rounded p-3 mb-3">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <strong><?php echo $option['name']; ?></strong>
                                            <i class="bi bi-info-circle ms-2 text-muted" data-bs-toggle="tooltip" title="Additional information"></i>
                                        </div>
                                        <div class="text-muted">₱<?php echo number_format($option['price']); ?><?php echo $option['per_day'] ? ' / per day' : ''; ?></div>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <div class="d-flex align-items-center justify-content-end">
                                            <button type="button" class="btn btn-outline-secondary btn-sm me-2 quantity-btn" data-action="decrease" data-target="<?php echo $key; ?>">-</button>
                                            <input type="number" name="<?php echo $key; ?>" id="<?php echo $key; ?>" class="form-control text-center quantity-input" style="width: 60px;" min="0" max="5" value="<?php echo $selected_options[$key] ?? 0; ?>">
                                            <button type="button" class="btn btn-outline-secondary btn-sm ms-2 quantity-btn" data-action="increase" data-target="<?php echo $key; ?>">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Included Options -->
                        <div class="mb-5">
                            <?php foreach($options['included'] as $key => $option): ?>
                            <div class="option-item border rounded p-3 mb-3 bg-light">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-center">
                                            <strong><?php echo $option['name']; ?> (Included)</strong>
                                            <i class="bi bi-info-circle ms-2 text-muted" data-bs-toggle="tooltip" title="This is included in your rental"></i>
                                        </div>
                                        <div class="text-muted">₱<?php echo number_format($option['price']); ?></div>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <span class="badge bg-success">Included</span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pickup Options -->
                        <div class="mb-4">
                            <h6 class="mb-3 text-primary">Pickup Options</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card pickup-option">
                                        <div class="card-body text-center">
                                            <h6>At counter</h6>
                                            <p class="text-muted small">For those who prefer the traditional approach</p>
                                            <input type="radio" name="pickup_option" value="counter" class="form-check-input">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card pickup-option selected">
                                        <div class="card-body text-center">
                                            <h6>Self-service</h6>
                                            <p class="text-muted small">Introducing our new Car Rental Self-Service experience! Say goodbye to waiting in line at the counter. With our automated KeyBox system, you can swiftly and conveniently collect your rental car keys on your own terms.</p>
                                            <input type="radio" name="pickup_option" value="self_service" class="form-check-input" checked>
                                            <div class="mt-2">
                                                <span class="badge bg-primary">Selected</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info mt-3">
                                <p class="mb-2">In order to utilize our self-service pick-up option, you'll need to have purchased our Platinum Insurance. Would you like to add Platinum Insurance to your reservation?</p>
                                <button type="button" class="btn btn-primary btn-sm">Add Platinum Insurance</button>
                            </div>
                        </div>

                        <div class="alert alert-light">
                            <small>All our cars come fully serviced, with unlimited mileage, all-weather tires, and free cancellation</small>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" onclick="history.back()">Go back</button>
                            <button type="submit" name="continue" class="btn btn-primary">Continue</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="col-lg-4">
            <div class="card sticky-top">
                <div class="card-body">
                    <h6 class="card-title"><?php echo $sample_booking['car']; ?></h6>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Pick up</span>
                        </div>
                        <div><strong><?php echo $sample_booking['pickup_location']; ?></strong></div>
                        <div class="text-muted"><?php echo date('d.m.Y H:i', strtotime($sample_booking['pickup_date'])); ?></div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Drop off</span>
                        </div>
                        <div><strong><?php echo $sample_booking['dropoff_location']; ?></strong></div>
                        <div class="text-muted"><?php echo date('d.m.Y H:i', strtotime($sample_booking['dropoff_date'])); ?></div>
                    </div>

                    <hr>

                    <h6>PRICE SUMMARY FOR <?php echo $sample_booking['days']; ?> DAYS RENTAL</h6>
                    
                    <div class="price-breakdown">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Vehicle</span>
                            <span>₱<?php echo number_format($sample_booking['base_price']); ?></span>
                        </div>
                        
                        <?php foreach($options['included'] as $key => $option): ?>
                        <div class="d-flex justify-content-between mb-2 text-muted">
                            <span><?php echo $option['name']; ?></span>
                            <span>₱<?php echo number_format($option['price']); ?></span>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="d-flex justify-content-between mb-2 text-warning">
                            <span>Decline additional insurances (Deposit)</span>
                            <span>₱350,000</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total</span>
                            <span id="total-price">₱<?php echo number_format($sample_booking['base_price']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 8px;
}

.step-item.completed .step-number {
    background: #28a745;
    color: white;
}

.step-item.active .step-number {
    background: #007bff;
    color: white;
}

.step-label {
    font-size: 12px;
    text-align: center;
}

.option-item {
    transition: all 0.2s;
}

.option-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.quantity-input {
    border: 1px solid #dee2e6;
}

.pickup-option {
    cursor: pointer;
    transition: all 0.2s;
}

.pickup-option:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.pickup-option.selected {
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

.sticky-top {
    top: 20px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity buttons
    document.querySelectorAll('.quantity-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.dataset.action;
            const target = this.dataset.target;
            const input = document.getElementById(target);
            let value = parseInt(input.value) || 0;
            
            if (action === 'increase' && value < 5) {
                value++;
            } else if (action === 'decrease' && value > 0) {
                value--;
            }
            
            input.value = value;
            updatePricing();
        });
    });

    // Pickup option selection
    document.querySelectorAll('.pickup-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.pickup-option').forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
        });
    });

    function updatePricing() {
        // Add pricing calculation logic here
        console.log('Updating pricing...');
    }
});
</script>

<?php $conn->close(); ?>