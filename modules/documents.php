<?php
$conn = new mysqli('localhost', 'root', '', 'travel_db_improved');

// Create documents table
$conn->query("CREATE TABLE IF NOT EXISTS customer_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(50) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    document_type VARCHAR(50) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    verified_by VARCHAR(100),
    verified_at TIMESTAMP NULL,
    verification_status VARCHAR(20) DEFAULT 'Pending',
    verification_notes TEXT,
    FOREIGN KEY (booking_id) REFERENCES car_rentals(booking_id)
)");

$message = '';

// Upload document (customer)
if(isset($_POST['upload_document']) && $_SESSION['user_type'] == 'customer') {
    $booking_id = $conn->real_escape_string($_POST['booking_id']);
    $document_type = $conn->real_escape_string($_POST['document_type']);
    $customer_username = $_SESSION['username'];
    $customer_info = $conn->query("SELECT email FROM customers WHERE username='$customer_username'")->fetch_assoc();
    $customer_email = $customer_info['email'];
    
    $upload_dir = 'uploads/documents/';
    if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    
    // Handle Driver's License (both front and back)
    if($document_type == 'Drivers_License') {
        if(isset($_FILES['license_front']) && $_FILES['license_front']['error'] == 0 && 
           isset($_FILES['license_back']) && $_FILES['license_back']['error'] == 0) {
            
            $front_ext = pathinfo($_FILES['license_front']['name'], PATHINFO_EXTENSION);
            $back_ext = pathinfo($_FILES['license_back']['name'], PATHINFO_EXTENSION);
            $front_name = 'License_Front_'.$booking_id.'_'.time().'.'.$front_ext;
            $back_name = 'License_Back_'.$booking_id.'_'.time().'.'.$back_ext;
            $front_path = $upload_dir.$front_name;
            $back_path = $upload_dir.$back_name;
            
            if(move_uploaded_file($_FILES['license_front']['tmp_name'], $front_path) && 
               move_uploaded_file($_FILES['license_back']['tmp_name'], $back_path)) {
                $conn->query("INSERT INTO customer_documents (booking_id, customer_email, document_type, file_path) 
                              VALUES ('$booking_id', '$customer_email', 'License_Front', '$front_path')");
                $conn->query("INSERT INTO customer_documents (booking_id, customer_email, document_type, file_path) 
                              VALUES ('$booking_id', '$customer_email', 'License_Back', '$back_path')");
                
                // Notify staff
                $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id) 
                              VALUES ('staff', 'all', 'New Document Uploaded', 'Customer uploaded Driver\'s License for booking $booking_id', '$booking_id')");
                $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id) 
                              VALUES ('admin', 'all', 'New Document Uploaded', 'Customer uploaded Driver\'s License for booking $booking_id', '$booking_id')");
                
                $message = '✅ Driver\'s License uploaded successfully!';
            }
        }
    } else {
        // Handle other documents
        if(isset($_FILES['document']) && $_FILES['document']['error'] == 0) {
            $file_ext = pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION);
            $file_name = $document_type.'_'.$booking_id.'_'.time().'.'.$file_ext;
            $file_path = $upload_dir.$file_name;
            
            if(move_uploaded_file($_FILES['document']['tmp_name'], $file_path)) {
                $conn->query("INSERT INTO customer_documents (booking_id, customer_email, document_type, file_path) 
                              VALUES ('$booking_id', '$customer_email', '$document_type', '$file_path')");
                
                // Notify staff
                $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id) 
                              VALUES ('staff', 'all', 'New Document Uploaded', 'Customer uploaded $document_type for booking $booking_id', '$booking_id')");
                $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id) 
                              VALUES ('admin', 'all', 'New Document Uploaded', 'Customer uploaded $document_type for booking $booking_id', '$booking_id')");
                
                $message = '✅ Document uploaded successfully!';
            }
        }
    }
}

// Verify document (staff)
if(isset($_POST['verify_document']) && in_array($_SESSION['user_type'], ['admin', 'staff'])) {
    $doc_id = intval($_POST['doc_id']);
    $status = $conn->real_escape_string($_POST['verification_status']);
    $notes = $conn->real_escape_string($_POST['verification_notes']);
    $verified_by = $_SESSION['username'];
    
    $conn->query("UPDATE customer_documents SET 
                  verification_status='$status', 
                  verification_notes='$notes',
                  verified_by='$verified_by',
                  verified_at=NOW()
                  WHERE id=$doc_id");
    
    $doc = $conn->query("SELECT * FROM customer_documents WHERE id=$doc_id")->fetch_assoc();
    $conn->query("INSERT INTO notifications (user_type, user_id, title, message, booking_id) 
                  VALUES ('customer', '{$doc['customer_email']}', 'Document Verification', 
                  'Your {$doc['document_type']} has been $status.', '{$doc['booking_id']}')");
    
    $message = '✅ Document verification updated!';
}

// Generate contract
if(isset($_POST['generate_contract']) && in_array($_SESSION['user_type'], ['admin', 'staff'])) {
    $booking_id = $conn->real_escape_string($_POST['booking_id']);
    $booking = $conn->query("SELECT * FROM car_rentals WHERE booking_id='$booking_id'")->fetch_assoc();
    
    $contract_dir = 'uploads/contracts/';
    if(!is_dir($contract_dir)) mkdir($contract_dir, 0777, true);
    
    $contract_file = $contract_dir.'CONTRACT_'.$booking_id.'.html';
    $contract_content = "
    <html>
    <head><title>Rental Contract - $booking_id</title>
    <style>body{font-family:Arial;padding:40px;max-width:800px;margin:0 auto;} h1{color:#0a2540;} .section{margin:20px 0;} .signature{margin-top:60px;border-top:2px solid #000;width:200px;padding-top:10px;}</style>
    </head>
    <body>
    <h1>🚗 CarGo - Car Rental Agreement</h1>
    <div class='section'>
        <strong>Contract Number:</strong> $booking_id<br>
        <strong>Date:</strong> ".date('F d, Y')."
    </div>
    <div class='section'>
        <h3>Renter Information</h3>
        <strong>Name:</strong> {$booking['customer_name']}<br>
        <strong>Email:</strong> {$booking['customer_email']}<br>
        <strong>Phone:</strong> {$booking['customer_phone']}<br>
        <strong>License Number:</strong> {$booking['license_number']}
    </div>
    <div class='section'>
        <h3>Vehicle Information</h3>
        <strong>Car Model:</strong> {$booking['car_model']}<br>
        <strong>Car Type:</strong> {$booking['car_type']}<br>
        <strong>Daily Rate:</strong> ₱".number_format($booking['daily_rate'], 2)."
    </div>
    <div class='section'>
        <h3>Rental Period</h3>
        <strong>Pickup:</strong> ".date('F d, Y', strtotime($booking['pickup_date']))." at {$booking['pickup_time']}<br>
        <strong>Dropoff:</strong> ".date('F d, Y', strtotime($booking['dropoff_date']))." at {$booking['dropoff_time']}<br>
        <strong>Duration:</strong> {$booking['rental_days']} days
    </div>
    <div class='section'>
        <h3>Payment Details</h3>
        <strong>Subtotal:</strong> ₱".number_format($booking['subtotal'], 2)."<br>
        <strong>Insurance:</strong> ₱".number_format($booking['insurance_fee'], 2)."<br>
        <strong>Additional Fees:</strong> ₱".number_format($booking['additional_fees'], 2)."<br>
        <strong>Total Amount:</strong> ₱".number_format($booking['total_amount'], 2)."
    </div>
    <div class='section'>
        <h3>Terms & Conditions</h3>
        <ol>
            <li>The renter must be at least 21 years old with a valid driver's license.</li>
            <li>The vehicle must be returned in the same condition as received.</li>
            <li>Any damage to the vehicle will be charged to the renter.</li>
            <li>Late returns will incur additional charges.</li>
            <li>The renter is responsible for all traffic violations during the rental period.</li>
        </ol>
    </div>
    <div class='section'>
        <div class='signature'>
            <strong>Renter Signature</strong>
        </div>
        <div class='signature' style='float:right;'>
            <strong>CarGo Representative</strong>
        </div>
    </div>
    </body>
    </html>
    ";
    
    file_put_contents($contract_file, $contract_content);
    
    $conn->query("INSERT INTO customer_documents (booking_id, customer_email, document_type, file_path, verification_status) 
                  VALUES ('$booking_id', '{$booking['customer_email']}', 'Contract', '$contract_file', 'Approved')");
    
    $message = '✅ Contract generated successfully!';
}

if($_SESSION['user_type'] == 'customer') {
    $customer_username = $_SESSION['username'];
    $customer_info = $conn->query("SELECT * FROM customers WHERE username='$customer_username'")->fetch_assoc();
    $customer_email = $customer_info['email'];
    $customer_name = $customer_info['full_name'];
    
    $my_bookings = $conn->query("SELECT * FROM car_rentals WHERE (customer_email='$customer_email' OR customer_name='$customer_name') AND status IN ('Confirmed', 'Pending', 'Completed') ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
    $my_documents = $conn->query("SELECT * FROM customer_documents WHERE customer_email='$customer_email' ORDER BY uploaded_at DESC")->fetch_all(MYSQLI_ASSOC);
} else {
    $all_documents = $conn->query("SELECT cd.*, cr.customer_name FROM customer_documents cd 
                                   LEFT JOIN car_rentals cr ON cd.booking_id = cr.booking_id 
                                   ORDER BY cd.uploaded_at DESC")->fetch_all(MYSQLI_ASSOC);
    $pending_verification = $conn->query("SELECT COUNT(*) as count FROM customer_documents WHERE verification_status='Pending'")->fetch_assoc()['count'];
}

$conn->close();
?>

<div class="pagetitle">
  <h1>📄 Document Management</h1>
</div>

<section class="section">
  <?php if($message): ?>
  <div class="alert alert-success" style="background-color: #d1fae5; border: 2px solid #10b981; color: #065f46; font-weight: 600; font-size: 16px;"><?php echo $message; ?></div>
  <?php endif; ?>

  <?php if($_SESSION['user_type'] == 'customer'): ?>
  
  <!-- Customer: Upload Documents -->
  <div class="card mb-4">
    <div class="card-header" style="background-color: #0a2540; color: white;">
      <h6 class="mb-0"><i class="bi bi-upload me-2"></i>Upload Documents</h6>
    </div>
    <div class="card-body">
      <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> <strong>Required Documents:</strong> Driver's License (front & back), Valid ID, Proof of Address
      </div>
      
      <?php if(empty($my_bookings)): ?>
      <p class="text-muted">No active bookings. Documents can be uploaded after booking confirmation.</p>
      <?php else: ?>
      <form method="POST" enctype="multipart/form-data">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Select Booking:</label>
            <select class="form-select" name="booking_id" required>
              <option value="">Choose booking...</option>
              <?php foreach($my_bookings as $b): ?>
              <option value="<?php echo $b['booking_id']; ?>"><?php echo $b['booking_id']; ?> - <?php echo htmlspecialchars($b['car_model']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Document Type:</label>
            <select class="form-select" name="document_type" id="docType" required onchange="toggleLicenseFields()">
              <option value="">Select type...</option>
              <option value="Drivers_License">Driver's License (Front & Back)</option>
              <option value="Valid_ID">Valid ID</option>
              <option value="Proof_of_Address">Proof of Address</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="col-md-12 mb-3" id="licenseFields" style="display:none;">
            <label class="form-label fw-bold">Driver's License Front:</label>
            <input type="file" class="form-control mb-2" name="license_front" accept="image/*,.pdf">
            <label class="form-label fw-bold">Driver's License Back:</label>
            <input type="file" class="form-control" name="license_back" accept="image/*,.pdf">
            <small class="text-muted">Upload both front and back of your driver's license</small>
          </div>
          <div class="col-md-12 mb-3" id="singleFileField">
            <label class="form-label fw-bold">Upload File:</label>
            <input type="file" class="form-control" name="document" accept="image/*,.pdf">
            <small class="text-muted">Accepted: JPG, PNG, PDF (Max 5MB)</small>
          </div>
        </div>
        <button type="submit" name="upload_document" class="btn btn-primary">
          <i class="bi bi-upload me-2"></i>Upload Document
        </button>
      </form>
      <?php endif; ?>
    </div>
  </div>

  <!-- Customer: My Documents -->
  <div class="card">
    <div class="card-header" style="background-color: #f8fafc;">
      <h6 class="mb-0"><i class="bi bi-files me-2"></i>My Documents</h6>
    </div>
    <div class="card-body">
      <table class="table">
        <thead>
          <tr>
            <th>Booking ID</th>
            <th>Document Type</th>
            <th>Uploaded</th>
            <th>Status</th>
            <th>Notes</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($my_documents as $doc): ?>
          <tr>
            <td><strong><?php echo $doc['booking_id']; ?></strong></td>
            <td><?php echo str_replace('_', ' ', $doc['document_type']); ?></td>
            <td><?php echo date('M d, Y', strtotime($doc['uploaded_at'])); ?></td>
            <td>
              <span class="badge bg-<?php echo $doc['verification_status']=='Approved'?'success':($doc['verification_status']=='Rejected'?'danger':'warning'); ?>">
                <?php echo $doc['verification_status']; ?>
              </span>
            </td>
            <td><?php echo htmlspecialchars($doc['verification_notes']); ?></td>
            <td>
              <a href="<?php echo $doc['file_path']; ?>" target="_blank" class="btn btn-sm btn-primary">
                <i class="bi bi-eye"></i> View
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php else: ?>
  
  <!-- Staff/Admin: Document Verification -->
  <div class="row mb-3">
    <div class="col-md-3">
      <div class="card" style="background: #ffc107; color: #000;">
        <div class="card-body text-center">
          <h3><?php echo $pending_verification; ?></h3>
          <p class="mb-0">Pending Verification</p>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header" style="background-color: #0a2540; color: white;">
      <h6 class="mb-0"><i class="bi bi-files me-2"></i>All Documents</h6>
    </div>
    <div class="card-body">
      <table class="table">
        <thead>
          <tr>
            <th>Booking ID</th>
            <th>Customer</th>
            <th>Document Type</th>
            <th>Uploaded</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($all_documents as $doc): ?>
          <tr>
            <td><strong><?php echo $doc['booking_id']; ?></strong></td>
            <td><?php echo htmlspecialchars($doc['customer_name']); ?></td>
            <td><?php echo str_replace('_', ' ', $doc['document_type']); ?></td>
            <td><?php echo date('M d, Y', strtotime($doc['uploaded_at'])); ?></td>
            <td>
              <span class="badge bg-<?php echo $doc['verification_status']=='Approved'?'success':($doc['verification_status']=='Rejected'?'danger':'warning'); ?>">
                <?php echo $doc['verification_status']; ?>
              </span>
            </td>
            <td>
              <a href="<?php echo $doc['file_path']; ?>" target="_blank" class="btn btn-sm btn-primary">
                <i class="bi bi-eye"></i>
              </a>
              <button class="btn btn-sm btn-success" onclick="showVerifyModal(<?php echo $doc['id']; ?>, '<?php echo $doc['document_type']; ?>')">
                <i class="bi bi-check-circle"></i> Verify
              </button>
              <?php if($doc['document_type'] != 'Contract'): ?>
              <form method="POST" class="d-inline">
                <input type="hidden" name="booking_id" value="<?php echo $doc['booking_id']; ?>">
                <button type="submit" name="generate_contract" class="btn btn-sm btn-info">
                  <i class="bi bi-file-earmark-text"></i> Contract
                </button>
              </form>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php endif; ?>
</section>

<!-- Verify Modal -->
<div id="verifyModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:white; padding:30px; border-radius:12px; max-width:500px; width:90%;">
    <h5>Verify Document</h5>
    <p id="verify_doc_type"></p>
    <form method="POST">
      <input type="hidden" name="doc_id" id="verify_doc_id">
      <div class="mb-3">
        <label class="form-label">Verification Status:</label>
        <select class="form-select" name="verification_status" required>
          <option value="Approved">Approved</option>
          <option value="Rejected">Rejected</option>
          <option value="Pending">Pending</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Notes:</label>
        <textarea class="form-control" name="verification_notes" rows="3"></textarea>
      </div>
      <button type="submit" name="verify_document" class="btn btn-primary">Save Verification</button>
      <button type="button" class="btn btn-secondary" onclick="document.getElementById('verifyModal').style.display='none'">Cancel</button>
    </form>
  </div>
</div>

<script>
function showVerifyModal(docId, docType) {
  document.getElementById('verify_doc_id').value = docId;
  document.getElementById('verify_doc_type').textContent = 'Document: ' + docType.replace(/_/g, ' ');
  document.getElementById('verifyModal').style.display = 'flex';
}

function toggleLicenseFields() {
  const docType = document.getElementById('docType').value;
  const licenseFields = document.getElementById('licenseFields');
  const singleField = document.getElementById('singleFileField');
  
  if(docType === 'Drivers_License') {
    licenseFields.style.display = 'block';
    singleField.style.display = 'none';
    document.querySelector('[name="license_front"]').required = true;
    document.querySelector('[name="license_back"]').required = true;
    document.querySelector('[name="document"]').required = false;
  } else {
    licenseFields.style.display = 'none';
    singleField.style.display = 'block';
    document.querySelector('[name="license_front"]').required = false;
    document.querySelector('[name="license_back"]').required = false;
    document.querySelector('[name="document"]').required = true;
  }
}
</script>
