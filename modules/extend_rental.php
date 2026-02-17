<?php
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['extend_rental'])) {
    $booking_id = $conn->real_escape_string($_POST['booking_id']);
    $extend_days = (int)$_POST['extend_days'];
    $customer_email = $conn->real_escape_string($_POST['customer_email']);
    
    // Get booking details
    $booking = $conn->query("SELECT * FROM car_rentals WHERE booking_id='$booking_id'")->fetch_assoc();
    
    if ($booking) {
        $daily_rate = $booking['daily_rate'];
        $insurance_fee_per_day = $booking['insurance_fee'] / $booking['rental_days'];
        
        // Calculate extension cost
        $extension_subtotal = $daily_rate * $extend_days;
        $extension_insurance = $insurance_fee_per_day * $extend_days;
        $extension_total = $extension_subtotal + $extension_insurance;
        
        // Update booking
        $new_dropoff_date = date('Y-m-d', strtotime($booking['dropoff_date'] . " +$extend_days days"));
        $new_rental_days = $booking['rental_days'] + $extend_days;
        $new_subtotal = $booking['subtotal'] + $extension_subtotal;
        $new_insurance_fee = $booking['insurance_fee'] + $extension_insurance;
        $new_total = $booking['total_amount'] + $extension_total;
        $new_remaining_balance = $booking['remaining_balance'] + $extension_total;
        
        $conn->query("UPDATE car_rentals SET 
            dropoff_date='$new_dropoff_date',
            rental_days=$new_rental_days,
            subtotal=$new_subtotal,
            insurance_fee=$new_insurance_fee,
            total_amount=$new_total,
            remaining_balance=$new_remaining_balance
            WHERE booking_id='$booking_id'");
        
        // Create notification for customer
        $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id, is_read) 
            VALUES ('customer', '$customer_email', 'Rental Extended', 
            'Your rental for booking $booking_id has been extended by $extend_days days. Additional payment: ₱" . number_format($extension_total, 2) . "', 
            '$booking_id', 0)");
        
        // Create notification for staff/admin
        $conn->query("INSERT INTO notifications (user_type, title, message, booking_id, is_read) 
            VALUES ('staff', 'Rental Extension', 
            'Booking $booking_id extended by $extend_days days. Additional payment pending: ₱" . number_format($extension_total, 2) . "', 
            '$booking_id', 0)");
        
        $conn->query("INSERT INTO notifications (user_type, title, message, booking_id, is_read) 
            VALUES ('admin', 'Rental Extension', 
            'Booking $booking_id extended by $extend_days days. Additional payment pending: ₱" . number_format($extension_total, 2) . "', 
            '$booking_id', 0)");
        
        $message = "✅ Rental extended successfully! Extended by $extend_days days. New drop-off date: " . date('M d, Y', strtotime($new_dropoff_date)) . ". Additional payment: ₱" . number_format($extension_total, 2);
        $message_type = 'success';
    } else {
        $message = "❌ Booking not found.";
        $message_type = 'error';
    }
}

// Get booking ID from URL
$booking_id = isset($_GET['booking_id']) ? $conn->real_escape_string($_GET['booking_id']) : '';
$booking = null;

if ($booking_id) {
    $booking = $conn->query("SELECT * FROM car_rentals WHERE booking_id='$booking_id'")->fetch_assoc();
}

$conn->close();
?>

<div class="pagetitle">
  <h1>🔄 Extend Rental</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">Extend Rental</li>
    </ol>
  </nav>
</div>

<section class="section">
  <?php if ($message): ?>
  <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <?php if ($booking): ?>
  <div class="row">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h6 class="mb-0"><i class="bi bi-calendar-plus"></i> Extend Rental Period</h6>
        </div>
        <div class="card-body">
          <div class="alert alert-info mb-4">
            <i class="bi bi-info-circle"></i> <strong>Current Booking Details</strong>
          </div>
          
          <div class="row mb-4">
            <div class="col-md-6">
              <p><strong>Booking ID:</strong> <?php echo $booking['booking_id']; ?></p>
              <p><strong>Car:</strong> <?php echo htmlspecialchars($booking['car_model']); ?></p>
              <p><strong>Customer:</strong> <?php echo htmlspecialchars($booking['customer_name']); ?></p>
            </div>
            <div class="col-md-6">
              <p><strong>Current Drop-off:</strong> <?php echo date('M d, Y', strtotime($booking['dropoff_date'])); ?></p>
              <p><strong>Current Days:</strong> <?php echo $booking['rental_days']; ?> days</p>
              <p><strong>Daily Rate:</strong> ₱<?php echo number_format($booking['daily_rate'], 2); ?></p>
            </div>
          </div>

          <form method="POST" id="extendForm">
            <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
            <input type="hidden" name="customer_email" value="<?php echo $booking['customer_email']; ?>">
            
            <div class="mb-3">
              <label class="form-label">Number of Days to Extend *</label>
              <input type="number" class="form-control" name="extend_days" id="extend_days" min="1" max="30" required>
              <div class="form-text">Maximum 30 days extension</div>
            </div>

            <div id="extensionPreview" class="alert alert-warning" style="display:none;">
              <h6><i class="bi bi-calculator"></i> Extension Preview</h6>
              <p class="mb-1"><strong>New Drop-off Date:</strong> <span id="preview_dropoff"></span></p>
              <p class="mb-1"><strong>Total Rental Days:</strong> <span id="preview_days"></span> days</p>
              <p class="mb-1"><strong>Extension Cost:</strong> ₱<span id="preview_cost">0.00</span></p>
              <p class="mb-0"><strong>New Total Amount:</strong> ₱<span id="preview_total">0.00</span></p>
            </div>

            <button type="submit" name="extend_rental" class="btn btn-primary">
              <i class="bi bi-check-circle"></i> Confirm Extension
            </button>
            <a href="index.php?page=my_bookings" class="btn btn-secondary">Cancel</a>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-header bg-light">
          <h6 class="mb-0"><i class="bi bi-info-circle"></i> Important Notes</h6>
        </div>
        <div class="card-body">
          <ul class="small">
            <li>Extension cost will be added to your remaining balance</li>
            <li>You must pay the extension fee before the new drop-off date</li>
            <li>Insurance coverage will be extended automatically</li>
            <li>Same daily rate applies to extension period</li>
            <li>Staff will be notified of your extension request</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <?php else: ?>
  <div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle"></i> No booking selected. Please select a booking to extend.
  </div>
  <?php endif; ?>
</section>

<script>
document.getElementById('extend_days').addEventListener('input', function() {
    const extendDays = parseInt(this.value) || 0;
    const currentDropoff = new Date('<?php echo $booking['dropoff_date']; ?>');
    const dailyRate = <?php echo $booking['daily_rate']; ?>;
    const insurancePerDay = <?php echo $booking['insurance_fee'] / $booking['rental_days']; ?>;
    const currentTotal = <?php echo $booking['total_amount']; ?>;
    const currentDays = <?php echo $booking['rental_days']; ?>;
    
    if (extendDays > 0) {
        const newDropoff = new Date(currentDropoff);
        newDropoff.setDate(newDropoff.getDate() + extendDays);
        
        const extensionCost = (dailyRate + insurancePerDay) * extendDays;
        const newTotal = currentTotal + extensionCost;
        const newDays = currentDays + extendDays;
        
        document.getElementById('preview_dropoff').textContent = newDropoff.toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'});
        document.getElementById('preview_days').textContent = newDays;
        document.getElementById('preview_cost').textContent = extensionCost.toFixed(2);
        document.getElementById('preview_total').textContent = newTotal.toFixed(2);
        document.getElementById('extensionPreview').style.display = 'block';
    } else {
        document.getElementById('extensionPreview').style.display = 'none';
    }
});
</script>
