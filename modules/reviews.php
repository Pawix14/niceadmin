<?php
// modules/reviews.php

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $conn->real_escape_string($_POST['customer_name']);
    $review_type = $conn->real_escape_string($_POST['review_type']);
    $item_name = $conn->real_escape_string($_POST['item_name']);
    $rating = (int)$_POST['rating'];
    $review_text = $conn->real_escape_string($_POST['review_text']);
    $review_date = $conn->real_escape_string($_POST['review_date']);
    $status = $conn->real_escape_string($_POST['status']);
    
    // Generate review ID
    $review_id = 'REV-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    
    $sql = "INSERT INTO customer_reviews (review_id, customer_name, review_type, item_name, rating, review_text, review_date, status) 
            VALUES ('$review_id', '$customer_name', '$review_type', '$item_name', $rating, '$review_text', '$review_date', '$status')";
    
    if ($conn->query($sql)) {
        $message = "✅ Customer review submitted successfully! Review ID: <strong>$review_id</strong>";
        $message_type = "success";
    } else {
        $message = "❌ Error submitting review: " . $conn->error;
        $message_type = "error";
    }
}
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">⭐ Customer Reviews</h5>
            
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <form class="row g-3" method="POST" action="">
                <div class="col-md-6">
                    <label for="customer_name" class="form-label">Customer Name *</label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name" required placeholder="Ex. Sarah Johnson">
                </div>
                
                <div class="col-md-6">
                    <label for="review_type" class="form-label">Review Type *</label>
                    <select class="form-select" id="review_type" name="review_type" required>
                        <option value="Hotel">🏨 Hotel</option>
                        <option value="Tour">🚌 Tour</option>
                        <option value="Flight">✈️ Flight</option>
                        <option value="Package">📦 Package</option>
                        <option value="General">🌟 General</option>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="item_name" class="form-label">Item/Service Name *</label>
                    <input type="text" class="form-control" id="item_name" name="item_name" required placeholder="Ex. Grand Hotel Paris or Japan Golden Route">
                </div>
                
                <div class="col-md-6">
                    <label for="rating" class="form-label">Rating *</label>
                    <div class="rating-stars">
                        <select class="form-select" id="rating" name="rating" required>
                            <option value="5">⭐⭐⭐⭐⭐ (5 - Excellent)</option>
                            <option value="4">⭐⭐⭐⭐ (4 - Very Good)</option>
                            <option value="3">⭐⭐⭐ (3 - Good)</option>
                            <option value="2">⭐⭐ (2 - Fair)</option>
                            <option value="1">⭐ (1 - Poor)</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label for="review_date" class="form-label">Review Date *</label>
                    <input type="date" class="form-control" id="review_date" name="review_date" required max="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="col-md-6">
                    <label for="status" class="form-label">Status *</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="Published">✅ Published</option>
                        <option value="Pending">⏳ Pending Review</option>
                        <option value="Hidden">👁️ Hidden</option>
                    </select>
                </div>
                
                <div class="col-12">
                    <label for="review_text" class="form-label">Review Text *</label>
                    <textarea class="form-control" id="review_text" name="review_text" rows="4" required placeholder="Share your experience..."></textarea>
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                    <button type="reset" class="btn btn-secondary">Clear Form</button>
                </div>
            </form>
            
            <!-- Display existing reviews -->
            <div class="mt-5">
                <h5 class="card-title">📋 Recent Customer Reviews</h5>
                <div class="row">
                    <?php
                    $reviews_result = $conn->query("SELECT * FROM customer_reviews ORDER BY review_date DESC LIMIT 6");
                    while($review = $reviews_result->fetch_assoc()): 
                        $rating = $review['rating'];
                        $stars = str_repeat('⭐', $rating);
                    ?>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0"><?php echo htmlspecialchars($review['item_name']); ?></h6>
                                    <span class="badge bg-<?php 
                                    switch($review['status']) {
                                        case 'Published': echo 'success'; break;
                                        case 'Pending': echo 'warning'; break;
                                        case 'Hidden': echo 'secondary'; break;
                                        default: echo 'primary';
                                    }
                                    ?>"><?php echo $review['status']; ?></span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-warning"><?php echo $stars; ?></span>
                                    <span class="text-muted ms-2">(<?php echo $rating; ?>/5)</span>
                                </div>
                                <p class="card-text">"<?php echo htmlspecialchars(substr($review['review_text'], 0, 150)); ?><?php if (strlen($review['review_text']) > 150) echo '...'; ?>"</p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <small class="text-muted">
                                        <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($review['customer_name']); ?>
                                    </small>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar"></i> <?php echo date('M d, Y', strtotime($review['review_date'])); ?>
                                    </small>
                                </div>
                                <div class="mt-2">
                                    <span class="badge bg-info">
                                    <?php 
                                    switch($review['review_type']) {
                                        case 'Hotel': echo '🏨'; break;
                                        case 'Tour': echo '🚌'; break;
                                        case 'Flight': echo '✈️'; break;
                                        case 'Package': echo '📦'; break;
                                        case 'General': echo '🌟'; break;
                                    }
                                    echo ' ' . $review['review_type']; 
                                    ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                
                <!-- Reviews summary -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h6 class="card-title">📊 Reviews Summary</h6>
                        <?php
                        $summary_result = $conn->query("
                            SELECT 
                                COUNT(*) as total_reviews,
                                AVG(rating) as avg_rating,
                                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
                            FROM customer_reviews 
                            WHERE status = 'Published'
                        ");
                        $summary = $summary_result->fetch_assoc();
                        ?>
                        <div class="row text-center">
                            <div class="col-md-3">
                                <h4><?php echo $summary['total_reviews'] ?? 0; ?></h4>
                                <small class="text-muted">Total Reviews</small>
                            </div>
                            <div class="col-md-3">
                                <h4><?php echo number_format($summary['avg_rating'] ?? 0, 1); ?>/5</h4>
                                <small class="text-muted">Average Rating</small>
                            </div>
                            <div class="col-md-3">
                                <h4><?php echo $summary['five_star'] ?? 0; ?></h4>
                                <small class="text-muted">⭐⭐⭐⭐⭐ Reviews</small>
                            </div>
                            <div class="col-md-3">
                                <h4><?php echo ($summary['five_star'] + $summary['four_star']) ?? 0; ?></h4>
                                <small class="text-muted">Positive Reviews (4+ stars)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Set default review date to today
window.addEventListener('DOMContentLoaded', function() {
    var reviewDateField = document.getElementById('review_date');
    reviewDateField.value = '<?php echo date('Y-m-d'); ?>';
    reviewDateField.max = '<?php echo date('Y-m-d'); ?>';
});
</script>

<?php $conn->close(); ?>