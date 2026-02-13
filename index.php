<?php
ob_start();
if (!headers_sent() && session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: landing.php');
    exit();
}
$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'customer';
$is_admin = ($user_type === 'admin');
$unread_notifications = 0;
$conn_notif = new mysqli('localhost', 'root', '', 'travel_db_improved');
if (!$conn_notif->connect_error) {
    if ($is_admin) {
        $result = $conn_notif->query("SELECT COUNT(*) as count FROM notifications WHERE user_type='admin' AND is_read=0");
    } else {
        $username = isset($_SESSION['username']) ? $conn_notif->real_escape_string($_SESSION['username']) : '';
        $customer_result = $conn_notif->query("SELECT email FROM customers WHERE username='$username'");
        if ($customer_result && $customer_row = $customer_result->fetch_assoc()) {
            $user_email = $customer_row['email'];
            $result = $conn_notif->query("SELECT COUNT(*) as count FROM notifications WHERE user_type='customer' AND user_id='$user_email' AND is_read=0");
        }
    }
    if (isset($result) && $result && $row = $result->fetch_assoc()) {
        $unread_notifications = $row['count'];
    }
    $conn_notif->close();
}
$profile_picture = null;
$display_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';
if (!$is_admin && isset($_SESSION['username'])) {
    $conn = new mysqli('localhost', 'root', '', 'travel_db_improved');
    if (!$conn->connect_error) {
        $username = $conn->real_escape_string($_SESSION['username']);
        $result = $conn->query("SELECT profile_picture, full_name FROM customers WHERE username='$username'");
        if ($result && $row = $result->fetch_assoc()) {
            $profile_picture = $row['profile_picture'];
            $display_name = $row['full_name'];
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>CarGo - Car Rental Management</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link href="assets/img/icon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="assets/css/style.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #2c3e50 0%, #34495e 50%, #666 100%);
      background-attachment: fixed;
      padding-top: <?php echo $is_admin ? '70px' : '130px'; ?>;
      color: #333;
      position: relative;
    }

    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: radial-gradient(circle, rgba(255,255,255,0.05) 1px, transparent 1px);
      background-size: 50px 50px;
      animation: moveBackground 20s linear infinite;
      pointer-events: none;
      z-index: 0;
    }

    @keyframes moveBackground {
      0% { transform: translate(0, 0); }
      100% { transform: translate(50px, 50px); }
    }
    #header {
      height: 70px;
      background: linear-gradient(135deg, rgba(44, 62, 80, 0.95) 0%, rgba(52, 73, 94, 0.95) 50%, rgba(102, 102, 102, 0.95) 100%);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 2px 15px rgba(0,0,0,0.3);
      z-index: 999;
    }

    .header .logo span {
      font-size: 1.5rem;
      font-weight: 700;
      color: white !important;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    /* Simple Toggle Button */
    .toggle-sidebar-btn {
      font-size: 24px;
      cursor: pointer;
      color: white !important;
      margin-left: 20px;
      transition: all 0.3s;
    }

    .toggle-sidebar-btn:hover {
      color: rgba(255, 255, 255, 0.8) !important;
      transform: scale(1.1);
    }

    /* SIDEBAR WITH GLASS EFFECT - Admin Only */
    #sidebar {
      position: fixed;
      top: 70px;
      left: 0;
      width: 260px;
      height: calc(100vh - 70px);
      background: linear-gradient(180deg, rgba(44, 62, 80, 0.95) 0%, rgba(52, 73, 94, 0.95) 50%, rgba(102, 102, 102, 0.95) 100%);
      backdrop-filter: blur(10px);
      border-right: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 2px 0 15px rgba(0,0,0,0.3);
      z-index: 998;
      overflow-y: auto;
      transition: all 0.3s ease;
    }

    /* Top Navigation Bar - Customer Only */
    #topnav {
      position: fixed;
      top: 70px;
      left: 0;
      right: 0;
      height: 60px;
      background: linear-gradient(135deg, rgba(44, 62, 80, 0.95) 0%, rgba(52, 73, 94, 0.95) 50%, rgba(102, 102, 102, 0.95) 100%);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 2px 15px rgba(0,0,0,0.3);
      z-index: 998;
      display: flex;
      align-items: center;
      padding: 0 30px;
    }

    .topnav-menu {
      display: flex;
      gap: 5px;
      align-items: center;
      list-style: none;
      margin: 0;
      padding: 0;
      flex-wrap: wrap;
    }

    .topnav-item .nav-link {
      display: flex;
      align-items: center;
      padding: 10px 20px;
      color: rgba(255, 255, 255, 0.85) !important;
      text-decoration: none;
      transition: all 0.3s;
      border-radius: 8px;
      white-space: nowrap;
    }

    .topnav-item .nav-link:hover {
      background-color: rgba(255, 255, 255, 0.1);
      color: white !important;
    }

    .topnav-item .nav-link.active {
      background: rgba(255, 255, 255, 0.15);
      color: white !important;
      font-weight: 600;
    }

    .topnav-item .nav-link i {
      margin-right: 8px;
      font-size: 18px;
      color: rgba(255, 255, 255, 0.85) !important;
    }

    .topnav-item .nav-link:hover i,
    .topnav-item .nav-link.active i {
      color: white !important;
    }

    /* Main Content Area */
    #main {
      margin-left: <?php echo $is_admin ? '260px' : '0'; ?>;
      padding: 30px;
      min-height: calc(100vh - <?php echo $is_admin ? '70px' : '130px'; ?>);
      transition: all 0.3s ease;
      position: relative;
      z-index: 1;
    }

    /* Sidebar Navigation - Pure White */
    .sidebar-nav {
      padding: 20px 0;
    }

    .nav-item {
      margin-bottom: 2px;
    }

    /* Navigation Links - Clean Gray */
    .nav-link {
      display: flex;
      align-items: center;
      padding: 12px 25px;
      color: rgba(255, 255, 255, 0.85) !important;
      text-decoration: none;
      transition: all 0.3s;
      border-left: 4px solid transparent;
      position: relative;
      overflow: hidden;
    }

    .nav-link::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      height: 100%;
      width: 0;
      background: rgba(255, 255, 255, 0.1);
      transition: width 0.3s;
      z-index: -1;
    }

    .nav-link:hover::before {
      width: 100%;
    }

    .nav-link:hover {
      background-color: rgba(255, 255, 255, 0.1);
      color: white !important;
      border-left-color: rgba(255, 255, 255, 0.3);
    }

    .nav-link.active {
      background: rgba(255, 255, 255, 0.15);
      color: white !important;
      font-weight: 600;
      border-left-color: white;
    }

    .nav-link i {
      margin-right: 10px;
      font-size: 18px;
      width: 24px;
      text-align: center;
      color: rgba(255, 255, 255, 0.85) !important;
      transition: all 0.3s;
    }

    .nav-link:hover i {
      transform: scale(1.1);
      color: white !important;
    }

    .nav-link.active i {
      color: white !important;
    }

    /* Section Headers */
    .nav-heading {
      padding: 20px 25px 8px;
      font-size: 0.75rem;
      text-transform: uppercase;
      color: rgba(255, 255, 255, 0.6) !important;
      font-weight: 700;
      letter-spacing: 1px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      margin: 0 15px 10px;
    }

    /* User Profile - Clean */
    .nav-profile {
      padding: 8px 15px;
      border-radius: 8px;
      transition: all 0.3s;
    }

    .nav-profile:hover {
      background: rgba(255, 255, 255, 0.1);
      transform: translateY(-2px);
    }

    .avatar-placeholder {
      width: 36px;
      height: 36px;
      background: rgba(255, 255, 255, 0.2);
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 16px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.3);
      transition: all 0.3s;
    }

    .nav-profile:hover .avatar-placeholder {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(0,0,0,0.4);
      border-color: white;
    }

    .avatar-placeholder img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .nav-profile span {
      color: white !important;
      font-weight: 600;
      transition: color 0.3s;
    }

    .nav-profile:hover span {
      color: rgba(255, 255, 255, 0.9) !important;
    }

    /* Clean Dropdown */
    .dropdown-menu {
      border: 1px solid #e0e0e0;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      border-radius: 8px;
      padding: 10px 0;
      background: #ffffff;
    }

    .dropdown-header {
      padding: 10px 15px;
      color: #666;
    }

    .dropdown-header h6 {
      color: #2c3e50;
      font-weight: 700;
      margin-bottom: 5px;
    }

    /* Responsive Design */
    @media (max-width: 992px) {
      #sidebar {
        left: -260px;
        box-shadow: 2px 0 15px rgba(0,0,0,0.1);
      }

      #main {
        margin-left: 0;
      }

      body.sidebar-mobile-show #sidebar {
        left: 0;
        background: linear-gradient(180deg, rgba(44, 62, 80, 0.98) 0%, rgba(52, 73, 94, 0.98) 50%, rgba(102, 102, 102, 0.98) 100%);
      }

      body.sidebar-mobile-show::after {
        content: '';
        position: fixed;
        top: 70px;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 997;
      }

      #topnav {
        height: auto;
        padding: 10px 15px;
      }

      .topnav-menu {
        overflow-x: auto;
        flex-wrap: nowrap;
        padding-bottom: 5px;
      }

      .topnav-item .nav-link {
        padding: 8px 15px;
        font-size: 14px;
      }
    }

    /* Footer */
    #footer {
      background: linear-gradient(135deg, rgba(44, 62, 80, 0.95) 0%, rgba(52, 73, 94, 0.95) 50%, rgba(102, 102, 102, 0.95) 100%);
      backdrop-filter: blur(10px);
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      padding: 20px 0;
      color: rgba(255, 255, 255, 0.85);
      font-size: 0.9rem;
      box-shadow: 0 -2px 10px rgba(0,0,0,0.3);
      position: relative;
      z-index: 1;
    }

    .copyright {
      text-align: center;
    }

    .copyright strong {
      color: white;
      font-weight: 600;
    }

    /* Back to Top Button - Gradient */
    .back-to-top {
      position: fixed;
      bottom: 30px;
      right: 30px;
      width: 50px;
      height: 50px;
      background: linear-gradient(135deg, #666 0%, #2c3e50 100%);
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      box-shadow: 0 4px 15px rgba(0,0,0,0.3);
      transition: all 0.3s;
      z-index: 999;
    }

    .back-to-top:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 20px rgba(0,0,0,0.4);
      color: white;
    }

    /* Travel Wave and Cloud - Subtle */
    .travel-wave-container {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      height: 145px;
      z-index: -1;
      pointer-events: none;
      opacity: 0.08;
    }

    .wave-layer {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 145px;
    }

    .wave-1 {
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" fill="%23666666"/></svg>');
      background-size: 1200px 100%;
      animation: waveMove 25s linear infinite;
      bottom: 48px;
    }

    .wave-2 {
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" fill="%232c3e50"/></svg>');
      background-size: 1200px 100%;
      animation: waveMove 20s linear infinite reverse;
      bottom: 40px;
    }

    .wave-3 {
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" fill="%23666666"/></svg>');
      background-size: 1200px 100%;
      animation: waveMove 30s linear infinite;
      bottom: 56px;
    }

    @keyframes waveMove {
      0% { background-position-x: 0; }
      100% { background-position-x: 1200px; }
    }

    .travel-cloud {
      position: fixed;
      top: 100px;
      right: 50px;
      width: 120px;
      height: 60px;
      background: rgba(102, 102, 102, 0.1);
      border-radius: 50px;
      filter: blur(15px);
      z-index: -1;
      animation: cloudMove 80s linear infinite;
      opacity: 0.2;
    }
    
    .travel-cloud::before {
      content: '';
      position: absolute;
      top: -20px;
      left: 25px;
      width: 70px;
      height: 70px;
      background: rgba(44, 62, 80, 0.1);
      border-radius: 50%;
    }
    
    .travel-cloud::after {
      content: '';
      position: absolute;
      top: -15px;
      right: 25px;
      width: 50px;
      height: 50px;
      background: rgba(44, 62, 80, 0.1);
      border-radius: 50%;
    }
    
    @keyframes cloudMove {
      0% { transform: translateX(0); }
      100% { transform: translateX(-100vw); }
    }

    /* Page Title Styling */
    .pagetitle h1 {
      color: #2c3e50 !important;
      font-weight: 700;
    }

    /* Card Styling for Dashboard */
    .stat-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 12px;
      padding: 25px;
      border: 1px solid rgba(224, 224, 224, 0.5);
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(224, 224, 224, 0.5);
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
      border-radius: 12px;
    }

    /* Scrollbar Styling */
    #sidebar::-webkit-scrollbar {
      width: 4px;
    }

    #sidebar::-webkit-scrollbar-track {
      background: rgba(0, 0, 0, 0.2);
    }

    #sidebar::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.3);
      border-radius: 2px;
    }

    #sidebar::-webkit-scrollbar-thumb:hover {
      background: rgba(255, 255, 255, 0.5);
    }

    /* Override any existing blue styles */
    a {
      color: #2c3e50;
      transition: color 0.3s;
    }

    a:hover {
      color: #666;
    }

    /* Remove any remaining blue highlights */
    .nav-link.active:hover {
      border-left-color: white !important;
    }

    /* Ensure all icons are gray */
    .bi {
      color: #666;
      transition: color 0.3s;
    }

    .bi:hover {
      color: #2c3e50;
    }

    .header .bi {
      color: white !important;
    }

    .sidebar .bi {
      color: rgba(255, 255, 255, 0.85) !important;
    }

    .nav-link .bi {
      color: rgba(255, 255, 255, 0.85) !important;
    }

    .nav-link.active .bi {
      color: white !important;
    }

    /* Clean up dropdown */
    .dropdown-menu {
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(224, 224, 224, 0.5);
      box-shadow: 0 4px 15px rgba(0,0,0,0.15);
      border-radius: 12px;
      padding: 10px 0;
    }

    .dropdown-item {
      color: #333;
      transition: all 0.3s;
    }

    .dropdown-item:hover {
      background: rgba(248, 249, 250, 0.8);
      color: #333;
    }

    /* Breadcrumb styling */
    .breadcrumb {
      background: transparent;
      padding: 0;
      margin-bottom: 1rem;
    }

    .breadcrumb-item a {
      color: #666;
      text-decoration: none;
      transition: color 0.3s;
    }

    .breadcrumb-item a:hover {
      color: #2c3e50;
    }

    .breadcrumb-item.active {
      color: #2c3e50;
      font-weight: 600;
    }

    /* Alert styling */
    .alert {
      border-radius: 12px;
      border: 1px solid rgba(224, 224, 224, 0.5);
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }

    /* CONSISTENT CARD STYLING FOR CARS */
    .car-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 12px;
      border: 1px solid rgba(224, 224, 224, 0.5);
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
      transition: all 0.3s ease;
      height: 100%;
      overflow: hidden;
    }

    .car-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .car-card-img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-bottom: 1px solid rgba(224, 224, 224, 0.5);
    }

    .car-card-body {
      padding: 20px;
    }

    .car-card-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 10px;
    }

    .car-card-text {
      font-size: 0.9rem;
      color: #666;
      margin-bottom: 8px;
    }

    .car-card-price {
      font-size: 1.3rem;
      font-weight: 700;
      color: #2c3e50;
      margin: 15px 0;
    }

    /* INCREASED WHITE SPACE */
    .section-spacing {
      margin-bottom: 40px;
    }

    .card-spacing {
      margin-bottom: 30px;
    }

    .row.g-4 {
      --bs-gutter-y: 2rem;
      --bs-gutter-x: 1.5rem;
    }

    /* COLOR BADGES FOR CAR CATEGORIES */
    .badge-compact {
      background-color: #0d6efd !important;
      color: white !important;
    }

    .badge-electric {
      background-color: #198754 !important;
      color: white !important;
    }

    .badge-suv {
      background-color: #fd7e14 !important;
      color: white !important;
    }

    .badge-sedan {
      background-color: #6c757d !important;
      color: white !important;
    }

    .badge-luxury {
      background-color: #6f42c1 !important;
      color: white !important;
    }

    .badge-van {
      background-color: #0dcaf0 !important;
      color: white !important;
    }

    /* STATUS INDICATORS */
    .status-completed {
      background-color: #198754 !important;
      color: white !important;
    }

    .status-confirmed {
      background-color: #0d6efd !important;
      color: white !important;
    }

    .status-pending {
      background-color: #ffc107 !important;
      color: #000 !important;
    }

    .status-cancelled {
      background-color: #dc3545 !important;
      color: white !important;
    }

    .status-active {
      background-color: #20c997 !important;
      color: white !important;
    }

    /* Badge styling */
    .badge {
      padding: 6px 12px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 0.75rem;
      letter-spacing: 0.5px;
    }

    /* PROFESSIONAL POLISH */
    .table {
      font-size: 0.9rem;
    }

    .table th {
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.75rem;
      letter-spacing: 0.5px;
      color: #666;
      border-bottom: 2px solid #dee2e6;
    }

    .table td {
      vertical-align: middle;
      padding: 12px;
    }

    .form-label {
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 8px;
      font-size: 0.9rem;
    }

    .form-control, .form-select {
      border-radius: 8px;
      border: 1px solid #dee2e6;
      padding: 10px 15px;
      transition: all 0.3s;
    }

    .form-control:focus, .form-select:focus {
      border-color: #666;
      box-shadow: 0 0 0 0.2rem rgba(102, 102, 102, 0.15);
    }

    .btn {
      border-radius: 8px;
      padding: 10px 20px;
      font-weight: 600;
      transition: all 0.3s;
      border: none;
    }

    .btn-sm {
      padding: 6px 12px;
      font-size: 0.85rem;
    }

    .card-header {
      font-weight: 600;
      padding: 15px 20px;
      border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .pagetitle h1 {
      font-size: 1.75rem;
      font-weight: 700;
      margin-bottom: 10px;
    }

    .breadcrumb {
      font-size: 0.85rem;
    }

    /* Professional Shadows */
    .card {
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      transition: all 0.3s;
    }

    .card:hover {
      box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }

    /* Button Styles */
    .btn-primary {
      background: linear-gradient(135deg, #666 0%, #555 100%);
    }

    .btn-primary:hover {
      background: linear-gradient(135deg, #555 0%, #444 100%);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .btn-warning {
      background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
      color: #000;
    }

    .btn-danger {
      background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    }

    .btn-success {
      background: linear-gradient(135deg, #198754 0%, #157347 100%);
    }

    /* Alert Professional Style */
    .alert {
      border-left: 4px solid;
      font-weight: 500;
    }

    .alert-success {
      border-left-color: #198754;
      background: rgba(25, 135, 84, 0.1);
    }

    .alert-danger {
      border-left-color: #dc3545;
      background: rgba(220, 53, 69, 0.1);
    }

    .alert-warning {
      border-left-color: #ffc107;
      background: rgba(255, 193, 7, 0.1);
    }

    /* Image Professional Style */
    .img-thumbnail {
      border-radius: 8px;
      border: 2px solid #dee2e6;
      padding: 4px;
    }

    /* Input Group */
    .input-group {
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
      border-radius: 8px;
      overflow: hidden;
    }

    /* Professional Spacing */
    .mb-3 {
      margin-bottom: 1.25rem !important;
    }

    .mb-4 {
      margin-bottom: 2rem !important;
    }

    /* Smooth Transitions */
    * {
      transition: background-color 0.2s, border-color 0.2s, color 0.2s;
    }
  </style>
</head>

<body>
  <!-- Subtle Background Elements -->
  <div class="travel-wave-container">  
    <div class="wave-layer wave-1"></div>
    <div class="wave-layer wave-2"></div>
    <div class="wave-layer wave-3"></div>
  </div>
  <div class="travel-cloud"></div>

  <!-- HEADER WITH DARK GRADIENT -->
  <header id="header" class="header fixed-top d-flex align-items-center" style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 50%, #666 100%) !important;">
    <div class="d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center">
        <span class="d-none d-lg-block" style="color: white !important; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">🚗 CarGo</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn" style="color: white !important;"></i>
    </div>

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <?php if ($unread_notifications > 0): ?>
        <li class="nav-item">
          <a class="nav-link" href="index.php?page=notifications" title="Notifications">
            <i class="bi bi-bell-fill" style="font-size: 20px; position: relative; color: white !important;">
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">
                <?php echo $unread_notifications; ?>
              </span>
            </i>
          </a>
        </li>
        <?php endif; ?>
        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <div class="avatar-placeholder" style="background: rgba(255, 255, 255, 0.2) !important; border: 2px solid rgba(255, 255, 255, 0.3) !important;">
              <?php if (!$is_admin && $profile_picture && file_exists($profile_picture)): ?>
                <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile">
              <?php else: ?>
                <span style="color: white !important;">✈️</span>
              <?php endif; ?>
            </div>
            <span class="d-none d-md-block dropdown-toggle ps-2" style="color: white !important;"><?php echo $is_admin ? 'Travel Admin' : htmlspecialchars($display_name); ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6>🛂 <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'User'; ?></h6>
              <span><?php echo $is_admin ? (isset($_SESSION['user_role']) ? htmlspecialchars($_SESSION['user_role']) : 'Administrator') : 'Customer'; ?></span>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
  </header>

  <?php if ($is_admin): ?>
  <!-- SIDEBAR WITH DARK GRADIENT - Admin Only -->
  <aside id="sidebar" class="sidebar" style="background: linear-gradient(180deg, #2c3e50 0%, #34495e 50%, #666 100%) !important;">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link <?php echo (!isset($_GET['page']) || $_GET['page'] == 'dashboard') ? 'active' : ''; ?>" href="index.php?page=dashboard" style="color: rgba(255, 255, 255, 0.85) !important;">
          <i class="bi bi-house-door" style="color: rgba(255, 255, 255, 0.85) !important;"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="nav-heading" style="color: rgba(255, 255, 255, 0.6) !important;">✈️ Bookings</li>

      <li class="nav-item">
        <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'all_bookings') ? 'active' : ''; ?>" href="index.php?page=all_bookings" style="color: rgba(255, 255, 255, 0.85) !important;">
          <i class="bi bi-journal-text" style="color: rgba(255, 255, 255, 0.85) !important;"></i>
          <span>All Bookings</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'admin_car_rental') ? 'active' : ''; ?>" href="index.php?page=admin_car_rental" style="color: rgba(255, 255, 255, 0.85) !important;">
          <i class="bi bi-car-front" style="color: rgba(255, 255, 255, 0.85) !important;"></i>
          <span>Car Rental</span>
        </a>
      </li>

      <li class="nav-heading" style="color: rgba(255, 255, 255, 0.6) !important;">🏝️ Management</li>

      <li class="nav-item">
        <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'cars') ? 'active' : ''; ?>" href="index.php?page=cars" style="color: rgba(255, 255, 255, 0.85) !important;">
          <i class="bi bi-car-front-fill" style="color: rgba(255, 255, 255, 0.85) !important;"></i>
          <span>Cars</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'car_maintenance') ? 'active' : ''; ?>" href="index.php?page=car_maintenance" style="color: rgba(255, 255, 255, 0.85) !important;">
          <i class="bi bi-tools" style="color: rgba(255, 255, 255, 0.85) !important;"></i>
          <span>Maintenance</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'car_sales') ? 'active' : ''; ?>" href="index.php?page=car_sales" style="color: rgba(255, 255, 255, 0.85) !important;">
          <i class="bi bi-tag" style="color: rgba(255, 255, 255, 0.85) !important;"></i>
          <span>Car Sales</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'promo_codes') ? 'active' : ''; ?>" href="index.php?page=promo_codes" style="color: rgba(255, 255, 255, 0.85) !important;">
          <i class="bi bi-ticket-perforated" style="color: rgba(255, 255, 255, 0.85) !important;"></i>
          <span>Promo Codes</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'agents') ? 'active' : ''; ?>" href="index.php?page=agents" style="color: rgba(255, 255, 255, 0.85) !important;">
          <i class="bi bi-person-badge" style="color: rgba(255, 255, 255, 0.85) !important;"></i>
          <span>Travel Agents</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'commissions') ? 'active' : ''; ?>" href="index.php?page=commissions" style="color: rgba(255, 255, 255, 0.85) !important;">
          <i class="bi bi-cash-coin" style="color: rgba(255, 255, 255, 0.85) !important;"></i>
          <span>Commissions</span>
        </a>
      </li>

      <li class="nav-heading" style="color: rgba(255, 255, 255, 0.6) !important;">⚙️ System</li>

      <li class="nav-item">
        <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'notifications') ? 'active' : ''; ?>" href="index.php?page=notifications" style="color: rgba(255, 255, 255, 0.85) !important;">
          <i class="bi bi-bell" style="color: rgba(255, 255, 255, 0.85) !important;"></i>
          <span>Notifications</span>
          <?php if ($unread_notifications > 0): ?>
          <span class="badge bg-danger ms-2"><?php echo $unread_notifications; ?></span>
          <?php endif; ?>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'admin_management') ? 'active' : ''; ?>" href="index.php?page=admin_management" style="color: rgba(255, 255, 255, 0.85) !important;">
          <i class="bi bi-shield-lock" style="color: rgba(255, 255, 255, 0.85) !important;"></i>
          <span>Admin Management</span>
        </a>
      </li>
    </ul>
  </aside>
  <?php else: ?>
  <!-- TOP NAVIGATION BAR - Customer Only -->
  <nav id="topnav">
    <ul class="topnav-menu">
      <li class="topnav-item">
        <a class="nav-link <?php echo (!isset($_GET['page']) || $_GET['page'] == 'dashboard') ? 'active' : ''; ?>" href="index.php?page=dashboard">
          <i class="bi bi-house-door"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li class="topnav-item">
        <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'my_bookings') ? 'active' : ''; ?>" href="index.php?page=my_bookings">
          <i class="bi bi-journal-text"></i>
          <span>My Bookings</span>
        </a>
      </li>
      <li class="topnav-item">
        <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'car_rental') ? 'active' : ''; ?>" href="index.php?page=car_rental">
          <i class="bi bi-car-front"></i>
          <span>Book a Car</span>
        </a>
      </li>
      <li class="topnav-item">
        <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'my_profile') ? 'active' : ''; ?>" href="index.php?page=my_profile">
          <i class="bi bi-person-circle"></i>
          <span>My Profile</span>
        </a>
      </li>
      <li class="topnav-item">
        <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'notifications') ? 'active' : ''; ?>" href="index.php?page=notifications">
          <i class="bi bi-bell"></i>
          <span>Notifications</span>
          <?php if ($unread_notifications > 0): ?>
          <span class="badge bg-danger ms-2"><?php echo $unread_notifications; ?></span>
          <?php endif; ?>
        </a>
      </li>
    </ul>
  </nav>
  <?php endif; ?>

  <!-- Main Content Area -->
  <main id="main" class="main">
    <?php
    if (!isset($_GET['page']) || $_GET['page'] == 'dashboard') {
        // Show different dashboard based on user type
        if ($is_admin) {
            include 'modules/dashboard.php';
        } else {
            include 'modules/customer_dashboard.php';
        }
    } else {
        $page = $_GET['page'];
        $module_file = 'modules/' . $page . '.php';
        
        if (file_exists($module_file)) {
            include $module_file;
        } else {
            echo '<div class="pagetitle">
                    <h1>Page Not Found</h1>
                  </div>
                  <section class="section">
                    <div class="alert alert-warning">Module not found. <a href="index.php">Return to Dashboard</a></div>
                  </section>';
        }
    }
    ?>
  </main>

  <!-- Clean Footer -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>CarGo - Car Rental Management System</span></strong>
    </div>
  </footer>

  <!-- Back to Top Button -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  
  <!-- Simple JavaScript -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Sidebar Toggle
      const toggleBtn = document.querySelector('.toggle-sidebar-btn');
      const body = document.body;
      
      toggleBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        if (window.innerWidth >= 992) {
          // Desktop: toggle collapse
          body.classList.toggle('sidebar-collapsed');
          const sidebar = document.getElementById('sidebar');
          const main = document.getElementById('main');
          
          if (body.classList.contains('sidebar-collapsed')) {
            sidebar.style.left = '-260px';
            main.style.marginLeft = '0';
          } else {
            sidebar.style.left = '0';
            main.style.marginLeft = '260px';
          }
        } else {
          // Mobile: show/hide sidebar
          body.classList.toggle('sidebar-mobile-show');
        }
      });
      
      // Close sidebar when clicking outside on mobile
      document.addEventListener('click', function(event) {
        if (window.innerWidth < 992 && 
            body.classList.contains('sidebar-mobile-show') &&
            !event.target.closest('#sidebar') &&
            !event.target.closest('.toggle-sidebar-btn')) {
          body.classList.remove('sidebar-mobile-show');
        }
      });
      
      // Close sidebar when clicking a link on mobile
      document.querySelectorAll('#sidebar .nav-link').forEach(link => {
        link.addEventListener('click', function() {
          if (window.innerWidth < 992) {
            body.classList.remove('sidebar-mobile-show');
          }
        });
      });
      
      // Back to Top Button
      const backToTop = document.querySelector('.back-to-top');
      window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
          backToTop.style.display = 'flex';
        } else {
          backToTop.style.display = 'none';
        }
      });
      
      backToTop.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      });
    });

    function switchToCarRentalTab() {
    const carRentalTab = document.querySelector('[data-bs-target="#car-rental-tab"]');
    if (carRentalTab) {
        const tab = new bootstrap.Tab(carRentalTab);
        tab.show();
    }
}
  </script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

</body>
</html>
<?php
// Flush output buffer
ob_end_flush();
?>
