<?php
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['extend_rental'])) {
    $booking_id = $conn->real_escape_string($_POST['booking_id']);
    $extend_days = (int)$_POST['extend_days'];
    $staff_notes = $conn->real_escape_string($_POST['staff_notes']);
    
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
        $notification_msg = "Your rental for booking $booking_id has been extended by $extend_days days by staff. New drop-off: " . date('M d, Y', strtotime($new_dropoff_date)) . ". Additional payment: ₱" . number_format($extension_total, 2);
        if ($staff_notes) {
            $notification_msg .= " | Staff notes: $staff_notes";
        }
        
        $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id, is_read) 
            VALUES ('customer', '{$booking['customer_email']}', 'Rental Extended by Staff', '$notification_msg', '$booking_id', 0)");
        
        // Create notification for admin
        $conn->query("INSERT INTO notifications (user_type, title, message, booking_id, is_read) 
            VALUES ('admin', 'Staff Extended Rental', 
            'Staff extended booking $booking_id by $extend_days days. Additional payment: ₱" . number_format($extension_total, 2) . "', 
            '$booking_id', 0)");
        
        $message = "✅ Rental extended successfully! Extended by $extend_days days. Customer has been notified. Additional payment: ₱" . number_format($extension_total, 2);
        $message_type = 'success';
    } else {
        $message = "❌ Booking not found.";
        $message_type = 'error';
    }
}

// Get all active/confirmed bookings
$bookings = $conn->query("SELECT * FROM car_rentals WHERE status IN ('Confirmed', 'Active') ORDER BY dropoff_date ASC");

$conn->close();
?>

<div class="pagetitle">
  <h1>🔄 Staff: Extend Rental</h1>
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

  <div class="card">
    <div class="card-header bg-primary text-white">
      <h6 class="mb-0"><i class="bi bi-calendar-plus"></i> Active Bookings - Extend Rental</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Booking ID</th>
              <th>Customer</th>
              <th>Car</th>
              <th>Current Drop-off</th>
              <th>Days</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while($booking = $bookings->fetch_assoc()): ?>
            <tr>
              <td><strong><?php echo $booking['booking_id']; ?></strong></td>
              <td>
                <?php echo htmlspecialchars($booking['customer_name']); ?><br>
                <small class="text-muted"><?php echo $booking['customer_email']; ?></small>
              </td>
              <td><?php echo htmlspecialchars($booking['car_model']); ?></td>
              <td><?php echo date('M d, Y', strtotime($booking['dropoff_date'])); ?></td>
              <td><?php echo $booking['rental_days']; ?> days</td>
              <td><span class="badge bg-<?php echo $booking['status']=='Active'?'success':'primary'; ?>"><?php echo $booking['status']; ?></span></td>
              <td>
                <button class="btn btn-sm btn-primary" onclick="openExtendModal('<?php echo $booking['booking_id']; ?>', '<?php echo htmlspecialchars($booking['customer_name']); ?>', '<?php echo htmlspecialchars($booking['car_model']); ?>', '<?php echo $booking['dropoff_date']; ?>', <?php echo $booking['rental_days']; ?>, <?php echo $booking['daily_rate']; ?>, <?php echo $booking['insurance_fee'] / $booking['rental_days']; ?>, <?php echo $booking['total_amount']; ?>)">
                  <i class="bi bi-calendar-plus"></i> Extend
                </button>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<!-- Extend Modal -->
<div class="modal fade" id="extendModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-calendar-plus"></i> Extend Rental</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <div class="modal-body">
          <input type="hidden" name="booking_id" id="modal_booking_id">
          
          <div class="alert alert-info">
            <strong>Booking:</strong> <span id="modal_booking_display"></span><br>
            <strong>Customer:</strong> <span id="modal_customer"></span><br>
            <strong>Car:</strong> <span id="modal_car"></span><br>
            <strong>Current Drop-off:</strong> <span id="modal_current_dropoff"></span>
          </div>

          <div class="mb-3">
            <label class="form-label">Number of Days to Extend *</label>
            <input type="number" class="form-control" name="extend_days" id="modal_extend_days" min="1" max="30" required>
            <div class="form-text">Maximum 30 days extension</div>
          </div>

          <div class="mb-3">
            <label class="form-label">Staff Notes (Optional)</label>
            <textarea class="form-control" name="staff_notes" rows="2" placeholder="Reason for extension, special instructions, etc."></textarea>
          </div>

          <div id="modal_preview" class="alert alert-warning" style="display:none;">
            <h6><i class="bi bi-calculator"></i> Extension Preview</h6>
            <p class="mb-1"><strong>New Drop-off Date:</strong> <span id="modal_preview_dropoff"></span></p>
            <p class="mb-1"><strong>Total Rental Days:</strong> <span id="modal_preview_days"></span> days</p>
            <p class="mb-1"><strong>Extension Cost:</strong> ₱<span id="modal_preview_cost">0.00</span></p>
            <p class="mb-0"><strong>New Total Amount:</strong> ₱<span id="modal_preview_total">0.00</span></p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="extend_rental" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> Confirm Extension
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
let currentBookingData = {};

function openExtendModal(bookingId, customer, car, dropoff, days, dailyRate, insurancePerDay, totalAmount) {
    currentBookingData = {
        bookingId: bookingId,
        customer: customer,
        car: car,
        dropoff: dropoff,
        days: days,
        dailyRate: dailyRate,
        insurancePerDay: insurancePerDay,
        totalAmount: totalAmount
    };
    
    document.getElementById('modal_booking_id').value = bookingId;
    document.getElementById('modal_booking_display').textContent = bookingId;
    document.getElementById('modal_customer').textContent = customer;
    document.getElementById('modal_car').textContent = car;
    document.getElementById('modal_current_dropoff').textContent = new Date(dropoff).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'});
    document.getElementById('modal_extend_days').value = '';
    document.getElementById('modal_preview').style.display = 'none';
    
    new bootstrap.Modal(document.getElementById('extendModal')).show();
}

document.getElementById('modal_extend_days').addEventListener('input', function() {
    const extendDays = parseInt(this.value) || 0;
    
    if (extendDays > 0) {
        const currentDropoff = new Date(currentBookingData.dropoff);
        const newDropoff = new Date(currentDropoff);
        newDropoff.setDate(newDropoff.getDate() + extendDays);
        
        const extensionCost = (currentBookingData.dailyRate + currentBookingData.insurancePerDay) * extendDays;
        const newTotal = currentBookingData.totalAmount + extensionCost;
        const newDays = currentBookingData.days + extendDays;
        
        document.getElementById('modal_preview_dropoff').textContent = newDropoff.toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'});
        document.getElementById('modal_preview_days').textContent = newDays;
        document.getElementById('modal_preview_cost').textContent = extensionCost.toFixed(2);
        document.getElementById('modal_preview_total').textContent = newTotal.toFixed(2);
        document.getElementById('modal_preview').style.display = 'block';
    } else {
        document.getElementById('modal_preview').style.display = 'none';
    }
});
</script>
