<?php
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');

$customer_username = $_SESSION['username'];
$customer_info = $conn->query("SELECT email FROM customers WHERE username='$customer_username'")->fetch_assoc();
$customer_email = $customer_info['email'];

// Remove from favorites
if(isset($_GET['remove_favorite'])) {
    $car_model = $conn->real_escape_string($_GET['remove_favorite']);
    $conn->query("DELETE FROM favorite_cars WHERE customer_email='$customer_email' AND car_model='$car_model'");
    header("Location: index.php?page=favorite_cars");
    exit();
}

// Get favorite cars
$favorites = $conn->query("SELECT * FROM favorite_cars WHERE customer_email='$customer_email' ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<div class="pagetitle">
  <h1>❤️ My Favorite Cars</h1>
</div>

<section class="section">
  <?php if(empty($favorites)): ?>
  <div class="card">
    <div class="card-body text-center py-5">
      <i class="bi bi-heart display-1 text-muted"></i>
      <h5 class="mt-3">No favorite cars yet</h5>
      <p class="text-muted">Browse cars and add them to your favorites!</p>
      <a href="index.php?page=car_rental" class="btn btn-primary mt-3">
        <i class="bi bi-car-front me-2"></i>Browse Cars
      </a>
    </div>
  </div>
  <?php else: ?>
  <div class="row g-4">
    <?php foreach($favorites as $car): ?>
    <div class="col-md-6 col-lg-4">
      <div class="card h-100">
        <img src="<?php echo $car['car_image'] ?: 'assets/img/car-placeholder.jpg'; ?>" class="card-img-top" style="height:200px; object-fit:cover;">
        <div class="card-body">
          <h5 class="card-title"><?php echo htmlspecialchars($car['car_model']); ?></h5>
          <p class="text-muted small"><?php echo $car['car_type']; ?></p>
          <h4 class="text-primary">₱<?php echo number_format($car['daily_rate'], 2); ?>/day</h4>
          <div class="d-grid gap-2">
            <a href="index.php?page=car_rental&car_name=<?php echo urlencode($car['car_model']); ?>" class="btn btn-primary">
              <i class="bi bi-calendar-check me-2"></i>Book Now
            </a>
            <a href="?page=favorite_cars&remove_favorite=<?php echo urlencode($car['car_model']); ?>" class="btn btn-outline-danger btn-sm">
              <i class="bi bi-heart-fill me-2"></i>Remove
            </a>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>
