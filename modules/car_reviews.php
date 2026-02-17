<?php
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');

// Create reviews table
$conn->query("CREATE TABLE IF NOT EXISTS car_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(50) NOT NULL,
    car_model VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'Approved',
    FOREIGN KEY (booking_id) REFERENCES car_rentals(booking_id)
)");

$message = '';

// Submit review (customer)
if(isset($_POST['submit_review']) && $_SESSION['user_type'] == 'customer') {
    $booking_id = $conn->real_escape_string($_POST['booking_id']);
    $car_model = $conn->real_escape_string($_POST['car_model']);
    $rating = intval($_POST['rating']);
    $review_text = $conn->real_escape_string($_POST['review_text']);
    $customer_username = $_SESSION['username'];
    $customer_info = $conn->query("SELECT email FROM customers WHERE username='$customer_username'")->fetch_assoc();
    $customer_email = $customer_info['email'];
    $customer_name = $conn->real_escape_string($_POST['customer_name']);
    
    $conn->query("INSERT INTO car_reviews (booking_id, car_model, customer_email, customer_name, rating, review_text) 
                  VALUES ('$booking_id', '$car_model', '$customer_email', '$customer_name', $rating, '$review_text')");
    
    // Notify staff
    $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id) 
                  VALUES ('staff', 'all', 'New Review Submitted', 'Customer submitted a $rating-star review for $car_model', '$booking_id')");
    $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id) 
                  VALUES ('admin', 'all', 'New Review Submitted', 'Customer submitted a $rating-star review for $car_model', '$booking_id')");
    
    $message = '✅ Review submitted successfully!';
}

// Delete review (staff)
if(isset($_GET['delete_review']) && in_array($_SESSION['user_type'], ['admin', 'staff'])) {
    $id = intval($_GET['delete_review']);
    $conn->query("DELETE FROM car_reviews WHERE id=$id");
    $message = '✅ Review deleted successfully!';
}

// Update status (staff)
if(isset($_POST['update_status']) && in_array($_SESSION['user_type'], ['admin', 'staff'])) {
    $id = intval($_POST['review_id']);
    $status = $conn->real_escape_string($_POST['status']);
    $conn->query("UPDATE car_reviews SET status='$status' WHERE id=$id");
    $message = '✅ Review status updated!';
}

if($_SESSION['user_type'] == 'customer') {
    $customer_username = $_SESSION['username'];
    $customer_info = $conn->query("SELECT * FROM customers WHERE username='$customer_username'")->fetch_assoc();
    $customer_email = $customer_info['email'];
    $customer_name = $customer_info['full_name'];
    
    // If booking_id is passed, show only that booking for review
    if(isset($_GET['booking_id'])) {
        $booking_id = $conn->real_escape_string($_GET['booking_id']);
        // First check if review already exists
        $existing_review = $conn->query("SELECT id FROM car_reviews WHERE booking_id='$booking_id'")->fetch_assoc();
        
        if($existing_review) {
            $message = '⚠️ You have already submitted a review for this booking.';
            $completed_bookings = [];
        } else {
            $completed_bookings = $conn->query("SELECT cr.booking_id, cr.car_model, cr.customer_name, cr.dropoff_date 
                                                FROM car_rentals cr 
                                                WHERE cr.booking_id='$booking_id' AND (cr.customer_email='$customer_email' OR cr.customer_name='$customer_name') AND cr.status='Completed' 
                                                ORDER BY cr.dropoff_date DESC")->fetch_all(MYSQLI_ASSOC);
        }
    } else {
        $completed_bookings = $conn->query("SELECT cr.booking_id, cr.car_model, cr.customer_name, cr.dropoff_date 
                                            FROM car_rentals cr 
                                            WHERE (cr.customer_email='$customer_email' OR cr.customer_name='$customer_name') AND cr.status='Completed' 
                                            AND NOT EXISTS (SELECT 1 FROM car_reviews rv WHERE rv.booking_id = cr.booking_id) 
                                            ORDER BY cr.dropoff_date DESC")->fetch_all(MYSQLI_ASSOC);
    }
    
    $my_reviews = $conn->query("SELECT * FROM car_reviews WHERE customer_email='$customer_email' ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
} else {
    $all_reviews = $conn->query("SELECT cr.*, 
        COALESCE((SELECT booking_id FROM car_rentals WHERE booking_id = cr.booking_id LIMIT 1), cr.booking_id) as rental_booking_id
        FROM car_reviews cr 
        ORDER BY cr.created_at DESC")->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>

<div class="pagetitle">
  <h1>⭐ Car Reviews & Ratings</h1>
</div>

<section class="section">
  <?php if($message): ?>
  <div class="alert alert-success" style="background-color: #d1fae5; border: 2px solid #10b981; color: #065f46; font-weight: 600; font-size: 16px;"><?php echo $message; ?></div>
  <?php endif; ?>

  <?php if($_SESSION['user_type'] == 'customer'): ?>
  
  <!-- Customer: Submit Reviews -->
  <?php if(!empty($completed_bookings)): ?>
  <div class="card mb-4">
    <div class="card-header" style="background-color: #0a2540; color: white;">
      <h6 class="mb-0"><i class="bi bi-star-fill me-2"></i>Rate Your Recent Rentals</h6>
    </div>
    <div class="card-body">
      <?php foreach($completed_bookings as $booking): ?>
      <div class="card mb-3" style="border: 2px solid #e2e8f0;">
        <div class="card-body">
          <h6 style="color: #0a2540;"><?php echo htmlspecialchars($booking['car_model']); ?></h6>
          <small class="text-muted">Booking: <?php echo $booking['booking_id']; ?> • Completed: <?php echo date('M d, Y', strtotime($booking['dropoff_date'])); ?></small>
          <form method="POST" class="mt-3">
            <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
            <input type="hidden" name="car_model" value="<?php echo $booking['car_model']; ?>">
            <input type="hidden" name="customer_name" value="<?php echo $booking['customer_name']; ?>">
            <div class="mb-3">
              <label class="form-label fw-bold">Rating *</label>
              <div class="star-rating">
                <?php for($i=5; $i>=1; $i--): ?>
                <input type="radio" name="rating" id="star<?php echo $i.$booking['booking_id']; ?>" value="<?php echo $i; ?>" required>
                <label for="star<?php echo $i.$booking['booking_id']; ?>">★</label>
                <?php endfor; ?>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Your Review</label>
              <textarea class="form-control" name="review_text" rows="3" placeholder="Share your experience..."></textarea>
            </div>
            <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Customer: My Reviews -->
  <div class="card">
    <div class="card-header" style="background-color: #f8fafc;">
      <h6 class="mb-0"><i class="bi bi-chat-left-text me-2"></i>My Reviews</h6>
    </div>
    <div class="card-body">
      <?php if(empty($my_reviews)): ?>
      <div class="text-center py-5">
        <i class="bi bi-star display-1 text-muted"></i>
        <h5 class="mt-3">No reviews yet</h5>
        <p class="text-muted">Complete a rental to leave a review</p>
      </div>
      <?php else: ?>
      <?php foreach($my_reviews as $review): ?>
      <div class="card mb-3">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 style="color: #0a2540;"><?php echo htmlspecialchars($review['car_model']); ?></h6>
              <div class="mb-2">
                <?php for($i=1; $i<=5; $i++): ?>
                <span style="color: <?php echo $i<=$review['rating'] ? '#ffc107' : '#e0e0e0'; ?>; font-size: 1.2rem;">★</span>
                <?php endfor; ?>
              </div>
            </div>
            <span class="badge bg-<?php echo $review['status']=='Approved'?'success':'warning'; ?>"><?php echo $review['status']; ?></span>
          </div>
          <p class="mb-1"><?php echo htmlspecialchars($review['review_text']); ?></p>
          <small class="text-muted"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></small>
        </div>
      </div>
      <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <?php else: ?>
  
  <!-- Staff/Admin: Manage All Reviews -->
  <div class="card">
    <div class="card-header" style="background-color: #0a2540; color: white;">
      <h6 class="mb-0"><i class="bi bi-star-fill me-2"></i>All Customer Reviews (<?php echo count($all_reviews); ?>)</h6>
    </div>
    <div class="card-body">
      <table class="table">
        <thead>
          <tr>
            <th>Car</th>
            <th>Customer</th>
            <th>Rating</th>
            <th>Review</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if(empty($all_reviews)): ?>
          <tr>
            <td colspan="7" class="text-center py-5">
              <i class="bi bi-star display-4 text-muted"></i>
              <h6 class="mt-3">No reviews yet</h6>
              <p class="text-muted">Customer reviews will appear here once submitted</p>
            </td>
          </tr>
          <?php else: ?>
          <?php foreach($all_reviews as $review): ?>
          <tr>
            <td><strong><?php echo htmlspecialchars($review['car_model']); ?></strong></td>
            <td><?php echo htmlspecialchars($review['customer_name']); ?></td>
            <td>
              <?php for($i=1; $i<=5; $i++): ?>
              <span style="color: <?php echo $i<=$review['rating'] ? '#ffc107' : '#e0e0e0'; ?>;">★</span>
              <?php endfor; ?>
            </td>
            <td><?php echo htmlspecialchars(substr($review['review_text'], 0, 50)); ?>...</td>
            <td><?php echo date('M d, Y', strtotime($review['created_at'])); ?></td>
            <td>
              <form method="POST" class="d-inline">
                <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                  <option value="Approved" <?php echo $review['status']=='Approved'?'selected':''; ?>>Approved</option>
                  <option value="Pending" <?php echo $review['status']=='Pending'?'selected':''; ?>>Pending</option>
                  <option value="Hidden" <?php echo $review['status']=='Hidden'?'selected':''; ?>>Hidden</option>
                </select>
                <input type="hidden" name="update_status" value="1">
              </form>
            </td>
            <td>
              <a href="?page=car_reviews&delete_review=<?php echo $review['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this review?')">
                <i class="bi bi-trash"></i>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php endif; ?>
</section>

<style>
.star-rating { display: flex; flex-direction: row-reverse; justify-content: flex-end; }
.star-rating input { display: none; }
.star-rating label { font-size: 2rem; color: #ddd; cursor: pointer; transition: color 0.2s; }
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label { color: #ffc107; }
</style>
