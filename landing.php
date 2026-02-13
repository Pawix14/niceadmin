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
    .hero { background: linear-gradient(135deg, #0a2540 0%, #2c3e50 100%); padding: 100px 0; color: white; text-align: center; }
    .hero h1 { font-size: 3.5rem; font-weight: 700; margin-bottom: 20px; }
    .section { padding: 80px 0; }
    .section-title { font-size: 2.5rem; font-weight: 700; color: #0a2540; margin-bottom: 50px; text-align: center; }
    .car-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s; margin-bottom: 30px; }
    .car-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
    .car-card img { width: 100%; height: 220px; object-fit: cover; }
    .car-card-body { padding: 25px; }
    .car-price { font-size: 1.8rem; font-weight: 700; color: #0a2540; }
    .sale-badge { position: absolute; top: 15px; right: 15px; background: #dc3545; color: white; padding: 8px 15px; border-radius: 8px; font-weight: 700; }
    .feature-icon { font-size: 3rem; color: #0a2540; margin-bottom: 20px; }
    .login-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); display: none; align-items: center; justify-content: center; z-index: 9999; }
    .login-prompt { background: white; padding: 40px; border-radius: 12px; text-align: center; max-width: 400px; }
    .stats { background: #0a2540; color: white; padding: 60px 0; }
    .stat-item { text-align: center; }
    .stat-number { font-size: 3rem; font-weight: 700; }
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
    <div class="container">
      <h1>Premium Car Rental Service</h1>
      <p class="lead fs-4">Experience luxury and comfort with our premium fleet</p>
      <button class="btn btn-light btn-lg mt-4" onclick="showLoginPrompt()">Get Started</button>
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

  <?php if(!empty($featured_cars)): ?>
  <div class="section">
    <div class="container">
      <h2 class="section-title">⭐ Featured Cars</h2>
      <div class="row">
        <?php foreach($featured_cars as $car): ?>
        <div class="col-md-4">
          <div class="car-card">
            <img src="<?php echo $car['image'] ?: 'assets/img/default-car.jpg'; ?>" alt="<?php echo htmlspecialchars($car['name']); ?>">
            <div class="car-card-body">
              <h5 class="fw-bold"><?php echo htmlspecialchars($car['name']); ?></h5>
              <p class="text-muted"><i class="bi bi-fuel-pump"></i> <?php echo $car['fuel_type']; ?> • <i class="bi bi-gear"></i> <?php echo $car['transmission']; ?> • <i class="bi bi-people"></i> <?php echo $car['seating_capacity']; ?> seats</p>
              <div class="d-flex justify-content-between align-items-center">
                <span class="car-price">₱<?php echo number_format($car['daily_rate'], 2); ?><small class="text-muted">/day</small></span>
                <button class="btn btn-primary" onclick="showLoginPrompt()">Book Now</button>
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
      <div class="row">
        <?php foreach($sales as $car): ?>
        <div class="col-md-3">
          <div class="car-card position-relative">
            <span class="sale-badge">-<?php echo $car['discount_percentage']; ?>%</span>
            <img src="<?php echo $car['image'] ?: 'assets/img/default-car.jpg'; ?>" alt="<?php echo htmlspecialchars($car['name']); ?>">
            <div class="car-card-body">
              <h6 class="fw-bold"><?php echo htmlspecialchars($car['name']); ?></h6>
              <div><span class="text-decoration-line-through text-muted">₱<?php echo number_format($car['daily_rate'], 2); ?></span></div>
              <div class="car-price">₱<?php echo number_format($car['sale_price'], 2); ?><small class="text-muted">/day</small></div>
              <button class="btn btn-danger btn-sm w-100 mt-2" onclick="showLoginPrompt()">Grab Deal</button>
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
      <h2 class="section-title">Our Fleet</h2>
      <div class="row">
        <?php foreach($all_cars as $car): ?>
        <div class="col-md-3">
          <div class="car-card">
            <img src="<?php echo $car['image'] ?: 'assets/img/default-car.jpg'; ?>" alt="<?php echo htmlspecialchars($car['name']); ?>">
            <div class="car-card-body">
              <h6 class="fw-bold"><?php echo htmlspecialchars($car['name']); ?></h6>
              <p class="text-muted small"><?php echo $car['type']; ?> • <?php echo $car['transmission']; ?></p>
              <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold text-primary">₱<?php echo number_format($car['daily_rate'], 2); ?></span>
                <button class="btn btn-sm btn-outline-primary" onclick="showLoginPrompt()">View</button>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="section" style="background: #0a2540; color: white;">
    <div class="container">
      <h2 class="section-title text-white">Why Choose CarGo?</h2>
      <div class="row text-center">
        <div class="col-md-3"><i class="bi bi-shield-check feature-icon" style="color: white;"></i><h5>Verified Cars</h5><p>All vehicles inspected & certified</p></div>
        <div class="col-md-3"><i class="bi bi-cash-coin feature-icon" style="color: white;"></i><h5>Best Prices</h5><p>Competitive rates guaranteed</p></div>
        <div class="col-md-3"><i class="bi bi-headset feature-icon" style="color: white;"></i><h5>24/7 Support</h5><p>Always here to help you</p></div>
        <div class="col-md-3"><i class="bi bi-geo-alt feature-icon" style="color: white;"></i><h5>Multiple Locations</h5><p>Pick up anywhere in the city</p></div>
      </div>
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
