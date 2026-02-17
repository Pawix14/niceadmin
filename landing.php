<?php
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');
$featured_cars = $conn->query("SELECT * FROM cars WHERE is_featured=1 AND status='Active' LIMIT 3")->fetch_all(MYSQLI_ASSOC);
$all_cars = $conn->query("SELECT * FROM cars WHERE status='Active' LIMIT 8")->fetch_all(MYSQLI_ASSOC);
$sales = $conn->query("SELECT c.*, s.sale_price, s.discount_percentage FROM cars c JOIN car_sales s ON c.name=s.car_model WHERE s.status='Active' AND CURDATE() BETWEEN s.sale_start AND s.sale_end LIMIT 4")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CarGo - Premium Car Rental Service</title>
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; }
    .navbar { background: rgba(10, 37, 64, 0.95); backdrop-filter: blur(10px); box-shadow: 0 2px 15px rgba(0,0,0,0.3); }
    .navbar-brand { font-size: 1.5rem; font-weight: 700; color: white !important; }
    .btn-login { background: linear-gradient(135deg, #666 0%, #555 100%); color: white; border: none; padding: 10px 30px; border-radius: 8px; font-weight: 600; }
    .btn-login:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.3); }
    .hero { background: linear-gradient(135deg, #0a2540 0%, #2c3e50 100%); padding: 120px 0 80px; color: white; }
    .hero h1 { font-size: 3.5rem; font-weight: 700; margin-bottom: 20px; }
    .hero-features { display: flex; gap: 30px; justify-content: center; margin-top: 40px; flex-wrap: wrap; }
    .hero-feature { background: rgba(255,255,255,0.1); padding: 20px 30px; border-radius: 12px; backdrop-filter: blur(10px); }
    .section { padding: 80px 0; }
    .section-title { font-size: 2.5rem; font-weight: 700; color: #0a2540; margin-bottom: 15px; text-align: center; }
    .section-subtitle { text-align: center; color: #6c757d; font-size: 1.1rem; margin-bottom: 50px; max-width: 700px; margin-left: auto; margin-right: auto; }
    .car-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s; margin-bottom: 30px; height: 100%; }
    .car-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
    .car-card img { width: 100%; height: 220px; object-fit: cover; }
    .car-card-body { padding: 25px; }
    .car-specs { display: flex; gap: 15px; margin: 15px 0; flex-wrap: wrap; }
    .car-spec { background: #f8f9fa; padding: 8px 12px; border-radius: 6px; font-size: 0.85rem; }
    .car-price { font-size: 1.8rem; font-weight: 700; color: #0a2540; }
    .sale-badge { position: absolute; top: 15px; right: 15px; background: #dc3545; color: white; padding: 8px 15px; border-radius: 8px; font-weight: 700; z-index: 1; }
    .feature-box { background: white; padding: 40px 30px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); text-align: center; height: 100%; transition: all 0.3s; }
    .feature-box:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.12); }
    .feature-icon { font-size: 3rem; color: #0a2540; margin-bottom: 20px; }
    .process-step { background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); text-align: center; position: relative; }
    .step-number { width: 60px; height: 60px; background: #0a2540; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; margin: 0 auto 20px; }
    .testimonial { background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); margin-bottom: 20px; }
    .testimonial-rating { color: #ffc107; margin-bottom: 10px; }
    .login-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); display: none; align-items: center; justify-content: center; z-index: 9999; }
    .login-prompt { background: white; padding: 40px; border-radius: 12px; text-align: center; max-width: 400px; }
    .stats { background: #0a2540; color: white; padding: 60px 0; }
    .stat-item { text-align: center; }
    .stat-number { font-size: 3rem; font-weight: 700; }
    .info-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px; border-radius: 16px; margin-bottom: 30px; }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
      <a class="navbar-brand" href="landing.php">🚗 CarGo</a>
      <a href="login.php" class="btn btn-login">Login</a>
    </div>
  </nav>

  <div class="hero">
    <div class="container text-center">
      <h1>Premium Car Rental Service</h1>
      <p class="lead fs-4">Experience luxury and comfort with our premium fleet of vehicles</p>
      <p class="mb-4">Flexible rentals • Competitive prices • 24/7 support • Verified vehicles</p>
      <button class="btn btn-light btn-lg mt-3" onclick="showLoginPrompt()"><i class="bi bi-box-arrow-in-right me-2"></i>Get Started</button>
      <div class="hero-features">
        <div class="hero-feature"><i class="bi bi-check-circle me-2"></i>No Hidden Fees</div>
        <div class="hero-feature"><i class="bi bi-check-circle me-2"></i>Free Cancellation</div>
        <div class="hero-feature"><i class="bi bi-check-circle me-2"></i>Instant Booking</div>
      </div>
    </div>
  </div>

  <div class="stats">
    <div class="container">
      <div class="row">
        <div class="col-md-3 stat-item"><div class="stat-number"><?php echo count($all_cars); ?>+</div><div>Available Cars</div></div>
        <div class="col-md-3 stat-item"><div class="stat-number">500+</div><div>Happy Customers</div></div>
        <div class="col-md-3 stat-item"><div class="stat-number">24/7</div><div>Customer Support</div></div>
        <div class="col-md-3 stat-item"><div class="stat-number">100%</div><div>Satisfaction</div></div>
      </div>
    </div>
  </div>

  <div class="section" style="background: white;">
    <div class="container">
      <h2 class="section-title">How It Works</h2>
      <p class="section-subtitle">Rent a car in 4 simple steps</p>
      <div class="row g-4">
        <div class="col-md-3">
          <div class="process-step">
            <div class="step-number">1</div>
            <i class="bi bi-search feature-icon" style="font-size: 2.5rem;"></i>
            <h5>Browse Cars</h5>
            <p class="text-muted">Choose from our wide selection of vehicles</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="process-step">
            <div class="step-number">2</div>
            <i class="bi bi-calendar-check feature-icon" style="font-size: 2.5rem;"></i>
            <h5>Select Dates</h5>
            <p class="text-muted">Pick your rental period and location</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="process-step">
            <div class="step-number">3</div>
            <i class="bi bi-credit-card feature-icon" style="font-size: 2.5rem;"></i>
            <h5>Make Payment</h5>
            <p class="text-muted">Secure payment with multiple options</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="process-step">
            <div class="step-number">4</div>
            <i class="bi bi-car-front feature-icon" style="font-size: 2.5rem;"></i>
            <h5>Drive Away</h5>
            <p class="text-muted">Pick up your car and enjoy the ride</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php if(!empty($featured_cars)): ?>
  <div class="section">
    <div class="container">
      <h2 class="section-title">⭐ Featured Cars</h2>
      <p class="section-subtitle">Our most popular and highly-rated vehicles</p>
      <div class="row">
        <?php foreach($featured_cars as $car): ?>
        <div class="col-md-4">
          <div class="car-card">
            <img src="<?php echo $car['image'] ?: 'assets/img/default-car.jpg'; ?>" alt="<?php echo htmlspecialchars($car['name']); ?>">
            <div class="car-card-body">
              <h5 class="fw-bold"><?php echo htmlspecialchars($car['name']); ?></h5>
              <div class="car-specs">
                <span class="car-spec"><i class="bi bi-fuel-pump me-1"></i><?php echo $car['fuel_type']; ?></span>
                <span class="car-spec"><i class="bi bi-gear me-1"></i><?php echo $car['transmission']; ?></span>
                <span class="car-spec"><i class="bi bi-people me-1"></i><?php echo $car['seating_capacity']; ?> seats</span>
              </div>
              <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="car-price">₱<?php echo number_format($car['daily_rate'], 2); ?><small class="text-muted">/day</small></span>
                <button class="btn btn-primary" onclick="showLoginPrompt()"><i class="bi bi-calendar-check me-2"></i>Book Now</button>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php if(!empty($sales)): ?>
  <div class="section" style="background: #f1f5f9;">
    <div class="container">
      <h2 class="section-title">🔥 Special Offers</h2>
      <p class="section-subtitle">Limited time deals - Save up to 50% on selected vehicles</p>
      <div class="row">
        <?php foreach($sales as $car): ?>
        <div class="col-md-3">
          <div class="car-card position-relative">
            <span class="sale-badge">-<?php echo $car['discount_percentage']; ?>%</span>
            <img src="<?php echo $car['image'] ?: 'assets/img/default-car.jpg'; ?>" alt="<?php echo htmlspecialchars($car['name']); ?>">
            <div class="car-card-body">
              <h6 class="fw-bold"><?php echo htmlspecialchars($car['name']); ?></h6>
              <div><span class="text-decoration-line-through text-muted">₱<?php echo number_format($car['daily_rate'], 2); ?></span> <span class="badge bg-danger">SAVE ₱<?php echo number_format($car['daily_rate'] - $car['sale_price'], 2); ?></span></div>
              <div class="car-price">₱<?php echo number_format($car['sale_price'], 2); ?><small class="text-muted">/day</small></div>
              <button class="btn btn-danger btn-sm w-100 mt-2" onclick="showLoginPrompt()"><i class="bi bi-lightning-fill me-2"></i>Grab Deal</button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <div class="section">
    <div class="container">
      <h2 class="section-title">Our Complete Fleet</h2>
      <p class="section-subtitle">From economy to luxury - find the perfect car for your journey</p>
      <div class="row">
        <?php foreach($all_cars as $car): ?>
        <div class="col-md-3">
          <div class="car-card">
            <img src="<?php echo $car['image'] ?: 'assets/img/default-car.jpg'; ?>" alt="<?php echo htmlspecialchars($car['name']); ?>">
            <div class="car-card-body">
              <h6 class="fw-bold"><?php echo htmlspecialchars($car['name']); ?></h6>
              <p class="text-muted small mb-2"><?php echo $car['type']; ?> • <?php echo $car['transmission']; ?></p>
              <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold text-primary">₱<?php echo number_format($car['daily_rate'], 2); ?><small class="text-muted">/day</small></span>
                <button class="btn btn-sm btn-outline-primary" onclick="showLoginPrompt()">View Details</button>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="section" style="background: white;">
    <div class="container">
      <h2 class="section-title">Why Choose CarGo?</h2>
      <p class="section-subtitle">We provide the best car rental experience with unmatched service quality</p>
      <div class="row g-4">
        <div class="col-md-3">
          <div class="feature-box">
            <i class="bi bi-shield-check feature-icon"></i>
            <h5>Verified Cars</h5>
            <p class="text-muted">All vehicles inspected, certified & regularly maintained for your safety</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="feature-box">
            <i class="bi bi-cash-coin feature-icon"></i>
            <h5>Best Prices</h5>
            <p class="text-muted">Competitive rates with no hidden fees. Price match guarantee available</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="feature-box">
            <i class="bi bi-headset feature-icon"></i>
            <h5>24/7 Support</h5>
            <p class="text-muted">Round-the-clock customer service ready to assist you anytime</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="feature-box">
            <i class="bi bi-geo-alt feature-icon"></i>
            <h5>Multiple Locations</h5>
            <p class="text-muted">Convenient pick-up and drop-off points across the city</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="feature-box">
            <i class="bi bi-clock-history feature-icon"></i>
            <h5>Flexible Rentals</h5>
            <p class="text-muted">Hourly, daily, weekly or monthly rental options available</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="feature-box">
            <i class="bi bi-file-earmark-check feature-icon"></i>
            <h5>Easy Booking</h5>
            <p class="text-muted">Simple online booking process with instant confirmation</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="feature-box">
            <i class="bi bi-arrow-repeat feature-icon"></i>
            <h5>Free Cancellation</h5>
            <p class="text-muted">Cancel or modify your booking free of charge up to 24 hours</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="feature-box">
            <i class="bi bi-award feature-icon"></i>
            <h5>Quality Service</h5>
            <p class="text-muted">Award-winning service with 98% customer satisfaction rate</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="section" style="background: #f8f9fa;">
    <div class="container">
      <h2 class="section-title">What Our Customers Say</h2>
      <p class="section-subtitle">Real reviews from real customers</p>
      <div class="row">
        <div class="col-md-4">
          <div class="testimonial">
            <div class="testimonial-rating">★★★★★</div>
            <p>"Excellent service! The car was clean, well-maintained, and the booking process was seamless. Highly recommend CarGo!"</p>
            <div class="d-flex align-items-center mt-3">
              <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-weight: 700;">JD</div>
              <div class="ms-3">
                <div class="fw-bold">John Doe</div>
                <small class="text-muted">Business Traveler</small>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="testimonial">
            <div class="testimonial-rating">★★★★★</div>
            <p>"Best car rental experience ever! Great prices, friendly staff, and the car exceeded my expectations. Will definitely rent again."</p>
            <div class="d-flex align-items-center mt-3">
              <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-weight: 700;">MS</div>
              <div class="ms-3">
                <div class="fw-bold">Maria Santos</div>
                <small class="text-muted">Family Vacation</small>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="testimonial">
            <div class="testimonial-rating">★★★★★</div>
            <p>"Professional service from start to finish. The 24/7 support was very helpful when I needed to extend my rental. Five stars!"</p>
            <div class="d-flex align-items-center mt-3">
              <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-weight: 700;">RC</div>
              <div class="ms-3">
                <div class="fw-bold">Robert Cruz</div>
                <small class="text-muted">Weekend Getaway</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="section" style="background: linear-gradient(135deg, #0a2540 0%, #2c3e50 100%); color: white;">
    <div class="container text-center">
      <h2 class="section-title text-white">Ready to Hit the Road?</h2>
      <p class="lead mb-4">Join thousands of satisfied customers and book your perfect car today</p>
      <button class="btn btn-light btn-lg" onclick="showLoginPrompt()"><i class="bi bi-box-arrow-in-right me-2"></i>Start Your Journey</button>
    </div>
  </div>

  <footer class="text-center py-4" style="background: #0a2540; color: white;">
    <p class="mb-0">&copy; 2024 CarGo - Premium Car Rental Service</p>
  </footer>

  <div class="login-overlay" id="loginOverlay">
    <div class="login-prompt">
      <i class="bi bi-lock-fill" style="font-size: 3rem; color: #0a2540;"></i>
      <h4 class="mt-3">Login Required</h4>
      <p>Please login to book a car and access all features.</p>
      <a href="login.php" class="btn btn-login">Go to Login</a>
      <button class="btn btn-secondary mt-2" onclick="closeLoginPrompt()">Cancel</button>
    </div>
  </div>

  <script>
    function showLoginPrompt() { document.getElementById('loginOverlay').style.display = 'flex'; }
    function closeLoginPrompt() { document.getElementById('loginOverlay').style.display = 'none'; }
  </script>
</body>
</html>
