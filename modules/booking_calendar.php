<?php
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');

$message = '';
$message_type = '';

// Modify booking
if(isset($_POST['modify_booking'])) {
    $booking_id = $conn->real_escape_string($_POST['booking_id']);
    $new_pickup = $conn->real_escape_string($_POST['new_pickup_date']);
    $new_dropoff = $conn->real_escape_string($_POST['new_dropoff_date']);
    $car_model = $conn->real_escape_string($_POST['car_model']);
    
    // Check for conflicts
    $conflict = $conn->query("SELECT * FROM car_rentals 
                              WHERE car_model='$car_model' 
                              AND booking_id != '$booking_id'
                              AND status IN ('Confirmed', 'Pending')
                              AND (
                                  (pickup_date <= '$new_dropoff' AND dropoff_date >= '$new_pickup')
                              )")->num_rows;
    
    if($conflict > 0) {
        $message = '❌ Conflict detected! Car is already booked for these dates.';
        $message_type = 'danger';
    } else {
        $days = (strtotime($new_dropoff) - strtotime($new_pickup)) / 86400;
        $booking = $conn->query("SELECT * FROM car_rentals WHERE booking_id='$booking_id'")->fetch_assoc();
        $new_subtotal = $booking['daily_rate'] * $days;
        $new_total = $new_subtotal + $booking['insurance_fee'] + $booking['additional_fees'] - $booking['discount_amount'];
        
        $conn->query("UPDATE car_rentals SET 
                      pickup_date='$new_pickup', 
                      dropoff_date='$new_dropoff',
                      rental_days=$days,
                      subtotal=$new_subtotal,
                      total_amount=$new_total
                      WHERE booking_id='$booking_id'");
        
        $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id) 
                      VALUES ('customer', '{$booking['customer_email']}', 'Booking Modified', 
                      'Your booking $booking_id has been rescheduled.', '$booking_id')");
        
        $message = '✅ Booking modified successfully!';
        $message_type = 'success';
    }
}

// Cancel booking
if(isset($_POST['cancel_booking'])) {
    $booking_id = $conn->real_escape_string($_POST['booking_id']);
    $reason = $conn->real_escape_string($_POST['cancel_reason']);
    
    $booking = $conn->query("SELECT * FROM car_rentals WHERE booking_id='$booking_id'")->fetch_assoc();
    $days_until = (strtotime($booking['pickup_date']) - time()) / 86400;
    
    // Refund policy: 100% if >7 days, 50% if 3-7 days, 0% if <3 days
    $refund_percent = $days_until > 7 ? 100 : ($days_until >= 3 ? 50 : 0);
    $refund_amount = ($booking['total_amount'] * $refund_percent) / 100;
    
    $conn->query("UPDATE car_rentals SET 
                  status='Cancelled', 
                  payment_status='Refunded',
                  updated_at=NOW() 
                  WHERE booking_id='$booking_id'");
    
    $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id) 
                  VALUES ('customer', '{$booking['customer_email']}', 'Booking Cancelled', 
                  'Your booking $booking_id has been cancelled. Refund: ₱".number_format($refund_amount, 2)." ($refund_percent%)', '$booking_id')");
    
    $message = "✅ Booking cancelled. Refund: ₱".number_format($refund_amount, 2)." ($refund_percent%)";
    $message_type = 'success';
}

// Get current month/year
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Get all bookings for calendar
$first_day = "$year-".str_pad($month, 2, '0', STR_PAD_LEFT)."-01";
$last_day = date('Y-m-t', strtotime($first_day));

$bookings = $conn->query("SELECT * FROM car_rentals 
                          WHERE (pickup_date <= '$last_day' AND dropoff_date >= '$first_day')
                          AND status IN ('Confirmed', 'Pending')
                          ORDER BY pickup_date")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<div class="pagetitle">
  <h1>📅 Booking Calendar</h1>
</div>

<section class="section">
  <?php if($message): ?>
  <div class="alert alert-<?php echo $message_type; ?>" style="background-color: <?php echo $message_type=='success'?'#d1fae5':'#fee2e2'; ?>; border: 2px solid <?php echo $message_type=='success'?'#10b981':'#dc3545'; ?>; color: <?php echo $message_type=='success'?'#065f46':'#991b1b'; ?>; font-weight: 600; font-size: 16px;"><?php echo $message; ?></div>
  <?php endif; ?>

  <!-- Calendar Navigation -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <a href="?page=booking_calendar&month=<?php echo $month==1?12:$month-1; ?>&year=<?php echo $month==1?$year-1:$year; ?>" class="btn btn-outline-primary">
          <i class="bi bi-chevron-left"></i> Previous
        </a>
        <h4 style="color: #0a2540;"><?php echo date('F Y', strtotime("$year-$month-01")); ?></h4>
        <a href="?page=booking_calendar&month=<?php echo $month==12?1:$month+1; ?>&year=<?php echo $month==12?$year+1:$year; ?>" class="btn btn-outline-primary">
          Next <i class="bi bi-chevron-right"></i>
        </a>
      </div>
    </div>
  </div>

  <!-- Calendar View -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="calendar-grid">
        <div class="calendar-header">Sun</div>
        <div class="calendar-header">Mon</div>
        <div class="calendar-header">Tue</div>
        <div class="calendar-header">Wed</div>
        <div class="calendar-header">Thu</div>
        <div class="calendar-header">Fri</div>
        <div class="calendar-header">Sat</div>
        
        <?php
        $first_day_of_month = date('w', strtotime("$year-$month-01"));
        $days_in_month = date('t', strtotime("$year-$month-01"));
        
        for($i = 0; $i < $first_day_of_month; $i++) {
            echo '<div class="calendar-day empty"></div>';
        }
        
        for($day = 1; $day <= $days_in_month; $day++) {
            $current_date = "$year-".str_pad($month, 2, '0', STR_PAD_LEFT)."-".str_pad($day, 2, '0', STR_PAD_LEFT);
            $day_bookings = array_filter($bookings, function($b) use ($current_date) {
                return $current_date >= $b['pickup_date'] && $current_date <= $b['dropoff_date'];
            });
            
            $is_today = $current_date == date('Y-m-d');
            echo '<div class="calendar-day'.($is_today?' today':'').'">';
            echo '<div class="day-number">'.$day.'</div>';
            
            if(count($day_bookings) > 0) {
                echo '<div class="booking-count">'.count($day_bookings).' booking'.(count($day_bookings)>1?'s':'').'</div>';
            }
            echo '</div>';
        }
        ?>
      </div>
    </div>
  </div>

  <!-- Bookings List -->
  <div class="card">
    <div class="card-header" style="background-color: #0a2540; color: white;">
      <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>All Bookings</h6>
    </div>
    <div class="card-body">
      <table class="table">
        <thead>
          <tr>
            <th>Booking ID</th>
            <th>Customer</th>
            <th>Car</th>
            <th>Dates</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($bookings as $b): ?>
          <tr>
            <td><strong><?php echo $b['booking_id']; ?></strong></td>
            <td><?php echo htmlspecialchars($b['customer_name']); ?></td>
            <td><?php echo htmlspecialchars($b['car_model']); ?></td>
            <td>
              <small><?php echo date('M d', strtotime($b['pickup_date'])); ?> - <?php echo date('M d, Y', strtotime($b['dropoff_date'])); ?></small>
            </td>
            <td><span class="badge bg-<?php echo $b['status']=='Confirmed'?'success':'warning'; ?>"><?php echo $b['status']; ?></span></td>
            <td>
              <button class="btn btn-sm btn-primary" onclick="showModifyModal('<?php echo $b['booking_id']; ?>', '<?php echo $b['car_model']; ?>', '<?php echo $b['pickup_date']; ?>', '<?php echo $b['dropoff_date']; ?>')">
                <i class="bi bi-pencil"></i> Modify
              </button>
              <button class="btn btn-sm btn-danger" onclick="showCancelModal('<?php echo $b['booking_id']; ?>')">
                <i class="bi bi-x-circle"></i> Cancel
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- Modify Modal -->
<div id="modifyModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:white; padding:30px; border-radius:12px; max-width:500px; width:90%;">
    <h5>Modify Booking</h5>
    <form method="POST">
      <input type="hidden" name="booking_id" id="modify_booking_id">
      <input type="hidden" name="car_model" id="modify_car_model">
      <div class="mb-3">
        <label>New Pickup Date:</label>
        <input type="date" class="form-control" name="new_pickup_date" id="modify_pickup" required>
      </div>
      <div class="mb-3">
        <label>New Dropoff Date:</label>
        <input type="date" class="form-control" name="new_dropoff_date" id="modify_dropoff" required>
      </div>
      <button type="submit" name="modify_booking" class="btn btn-primary">Save Changes</button>
      <button type="button" class="btn btn-secondary" onclick="document.getElementById('modifyModal').style.display='none'">Cancel</button>
    </form>
  </div>
</div>

<!-- Cancel Modal -->
<div id="cancelModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:white; padding:30px; border-radius:12px; max-width:500px; width:90%;">
    <h5>Cancel Booking</h5>
    <div class="alert alert-warning">
      <strong>Refund Policy:</strong><br>
      • More than 7 days: 100% refund<br>
      • 3-7 days: 50% refund<br>
      • Less than 3 days: No refund
    </div>
    <form method="POST">
      <input type="hidden" name="booking_id" id="cancel_booking_id">
      <div class="mb-3">
        <label>Cancellation Reason:</label>
        <textarea class="form-control" name="cancel_reason" rows="3" required></textarea>
      </div>
      <button type="submit" name="cancel_booking" class="btn btn-danger">Confirm Cancellation</button>
      <button type="button" class="btn btn-secondary" onclick="document.getElementById('cancelModal').style.display='none'">Close</button>
    </form>
  </div>
</div>

<script>
function showModifyModal(bookingId, carModel, pickup, dropoff) {
  document.getElementById('modify_booking_id').value = bookingId;
  document.getElementById('modify_car_model').value = carModel;
  document.getElementById('modify_pickup').value = pickup;
  document.getElementById('modify_dropoff').value = dropoff;
  document.getElementById('modifyModal').style.display = 'flex';
}

function showCancelModal(bookingId) {
  document.getElementById('cancel_booking_id').value = bookingId;
  document.getElementById('cancelModal').style.display = 'flex';
}
</script>

<style>
.calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; }
.calendar-header { background: #0a2540; color: white; padding: 12px; text-align: center; font-weight: 600; border-radius: 8px; }
.calendar-day { background: white; border: 2px solid #e2e8f0; padding: 12px; min-height: 100px; border-radius: 8px; position: relative; }
.calendar-day.today { border-color: #0a2540; background: #f0f9ff; }
.calendar-day.empty { background: #f8fafc; border: none; }
.day-number { font-weight: 700; color: #0a2540; font-size: 1.1rem; }
.booking-count { background: #ffc107; color: #000; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; margin-top: 8px; display: inline-block; }
</style>
