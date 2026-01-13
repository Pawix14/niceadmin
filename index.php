<?php
// Start output buffering to prevent header errors
ob_start();

// Start session at the very beginning
if (!headers_sent() && session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>🌴 Paradise Travel Management</title>
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
      background-color: #f8f9fa;
      padding-top: 70px;
      color: #333;
    }
    #header {
      height: 70px;
      border-bottom: 1px solid #e0e0e0;
      z-index: 999;
    }

    .header .logo span {
      font-size: 1.5rem;
      font-weight: 700;
      color: #333; /* Changed to dark gray instead of blue */
    }

    /* Simple Toggle Button */
    .toggle-sidebar-btn {
      font-size: 24px;
      cursor: pointer;
      color: #666;
      margin-left: 20px;
    }

    .toggle-sidebar-btn:hover {
      color: #333;
    }

    /* PURE WHITE SIDEBAR - No Colors */
    #sidebar {
      position: fixed;
      top: 70px;
      left: 0;
      width: 260px;
      height: calc(100vh - 70px);
      border-right: 1px solid #e0e0e0;
      z-index: 998;
      overflow-y: auto;
      transition: all 0.3s ease;
    }

    /* Main Content Area */
    #main {
      margin-left: 260px;
      padding: 30px;
      min-height: calc(100vh - 70px);
      background: #f8f9fa;
      transition: all 0.3s ease;
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
      color: #666;
      text-decoration: none;
      transition: all 0.3s;
      border-left: 4px solid transparent;
    }

    .nav-link:hover {
      background-color: #f8f9fa;
      color: #333;
      border-left-color: #cccccc;
    }

    .nav-link.active {
      background-color: #f8f9fa;
      color: #333;
      font-weight: 500;
      border-left-color: #666; /* Changed to dark gray instead of blue */
    }

    .nav-link i {
      margin-right: 10px;
      font-size: 18px;
      width: 24px;
      text-align: center;
      color: #666;
    }

    .nav-link.active i {
      color: #333;
    }

    /* Section Headers */
    .nav-heading {
      padding: 20px 25px 8px;
      font-size: 0.75rem;
      text-transform: uppercase;
      color: #888;
      font-weight: 600;
      letter-spacing: 1px;
      border-bottom: 1px solid #e0e0e0;
      margin: 0 15px 10px;
    }

    /* User Profile - Clean */
    .nav-profile {
      padding: 8px 15px;
      border-radius: 8px;
      transition: all 0.3s;
    }

    .nav-profile:hover {
      background: #f8f9fa;
    }

    .avatar-placeholder {
      width: 36px;
      height: 36px;
      background: #666; /* Changed to gray instead of blue */
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 16px;
    }

    .nav-profile span {
      color: #333;
      font-weight: 500;
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
      color: #333;
      font-weight: 600;
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
    }

    /* Footer */
    #footer {
      background: #ffffff;
      border-top: 1px solid #e0e0e0;
      padding: 20px 0;
      color: #666;
      font-size: 0.9rem;
    }

    .copyright {
      text-align: center;
    }

    .copyright strong {
      color: #333;
      font-weight: 600;
    }

    /* Back to Top Button - Neutral Gray */
    .back-to-top {
      position: fixed;
      bottom: 30px;
      right: 30px;
      width: 50px;
      height: 50px;
      background: #666;
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      transition: all 0.3s;
      z-index: 999;
    }

    .back-to-top:hover {
      background: #333;
      transform: translateY(-3px);
      color: white;
    }

    /* Travel Background Elements (Kept but subtle) */
    .travel-wave-container {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      height: 145px;
      z-index: -1;
      pointer-events: none;
      opacity: 0.05;
    }

    .wave-layer {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 145px;
    }

    .wave-1 {
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" fill="%23999999"/></svg>'); /* Gray instead of blue */
      background-size: 1200px 100%;
      animation: waveMove 25s linear infinite;
      bottom: 48px;
    }

    .wave-2 {
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" fill="%23CCCCCC"/></svg>'); /* Light gray */
      background-size: 1200px 100%;
      animation: waveMove 20s linear infinite reverse;
      bottom: 40px;
    }

    .wave-3 {
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" fill="%23999999"/></svg>'); /* Gray */
      background-size: 1200px 100%;
      animation: waveMove 30s linear infinite;
      bottom: 56px;
    }

    @keyframes waveMove {
      0% { background-position-x: 0; }
      100% { background-position-x: 1200px; }
    }

    /* Travel Cloud - Very Subtle */
    .travel-cloud {
      position: fixed;
      top: 100px;
      right: 50px;
      width: 120px;
      height: 60px;
      background: rgba(39, 89, 182, 0.3);
      border-radius: 50px;
      filter: blur(15px);
      z-index: -1;
      animation: cloudMove 80s linear infinite;
      opacity: 0.3;
    }
    
    .travel-cloud::before {
      content: '';
      position: absolute;
      top: -20px;
      left: 25px;
      width: 70px;
      height: 70px;
      background: rgba(255, 255, 255, 0.3);
      border-radius: 50%;
    }
    
    .travel-cloud::after {
      content: '';
      position: absolute;
      top: -15px;
      right: 25px;
      width: 50px;
      height: 50px;
      background: rgba(255, 255, 255, 0.3);
      border-radius: 50%;
    }
    
    @keyframes cloudMove {
      0% { transform: translateX(0); }
      100% { transform: translateX(-100vw); }
    }

    /* Card Styling for Dashboard */
    .stat-card {
      background: white;
      border-radius: 8px;
      padding: 25px;
      border: 1px solid #e0e0e0;
      transition: all 0.3s ease;
    }

    .stat-card:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    /* Scrollbar Styling */
    #sidebar::-webkit-scrollbar {
      width: 4px;
    }

    #sidebar::-webkit-scrollbar-track {
      background: #f1f1f1;
    }

    #sidebar::-webkit-scrollbar-thumb {
      background: #ccc;
      border-radius: 2px;
    }

    #sidebar::-webkit-scrollbar-thumb:hover {
      background: #aaa;
    }

    /* Override any existing blue styles */
    a {
      color: #333;
    }

    a:hover {
      color: #000;
    }

    /* Remove any remaining blue highlights */
    .nav-link.active:hover {
      border-left-color: #666;
    }

    /* Ensure all icons are gray */
    .bi {
      color: #666;
    }

    .nav-link.active .bi {
      color: #333;
    }

    /* Clean up dropdown */
    .dropdown-item {
      color: #333;
    }

    .dropdown-item:hover {
      background: #f8f9fa;
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
    }

    .breadcrumb-item.active {
      color: #333;
    }

    /* Alert styling */
    .alert {
      border-radius: 8px;
      border: 1px solid #e0e0e0;
    }
  </style>
</head>

<body>
  <!-- Very Subtle Background Elements -->
  <div class="travel-wave-container">  
    <div class="wave-layer wave-1"></div>
    <div class="wave-layer wave-2"></div>
    <div class="wave-layer wave-3"></div>
  </div>
  <div class="travel-cloud"></div>

  <!-- PURE WHITE HEADER -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center">
        <span class="d-none d-lg-block">🌴 Paradise Travel</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <div class="avatar-placeholder">
              <span>✈️</span>
            </div>
            <span class="d-none d-md-block dropdown-toggle ps-2">Travel Admin</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6>🛂 Travel Administrator</h6>
              <span>Paradise Travel System</span>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="#">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
  </header>

  <!-- PURE WHITE SIDEBAR -->
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link <?php echo (!isset($_GET['page']) || $_GET['page'] == 'dashboard') ? 'active' : ''; ?>" href="index.php?page=dashboard">
          <i class="bi bi-compass"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="nav-heading">✈️ Bookings</li>

      <li class="nav-item">
        <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'all_bookings') ? 'active' : ''; ?>" href="index.php?page=all_bookings">
          <i class="bi bi-journal-text"></i>
          <span>All Bookings</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'tours') ? 'active' : ''; ?>" href="index.php?page=tours">
          <i class="bi bi-binoculars"></i>
          <span>Tour Activity</span>
        </a>
      </li>

      <li class="nav-item">
  <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'car_rental') ? 'active' : ''; ?>" href="index.php?page=car_rental">
    <i class="bi bi-car-front"></i>
    <span>Car Rental</span>
  </a>
</li>

<li class="nav-item">
  <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'flights') ? 'active' : ''; ?>" href="index.php?page=flights">
    <i class="bi bi-airplane"></i>
    <span>Flights</span>
  </a>
</li>

<li class="nav-item">
  <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'hotels') ? 'active' : ''; ?>" href="index.php?page=hotels">
    <i class="bi bi-building"></i>
    <span>Hotel Bookings</span>
  </a>
</li>

<li class="nav-item">
  <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'cruises') ? 'active' : ''; ?>" href="index.php?page=cruises">
<i class="bi bi-arrows-fullscreen"></i>
    <span>Cruises</span>
  </a>
</li>

      <li class="nav-heading">🏝️ Management</li>

      <li class="nav-item">
        <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'agents') ? 'active' : ''; ?>" href="index.php?page=agents">
          <i class="bi bi-person-badge"></i>
          <span>Travel Agents</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'commissions') ? 'active' : ''; ?>" href="index.php?page=commissions">
          <i class="bi bi-cash-coin"></i>
          <span>Commissions</span>
        </a>
      </li>

  </aside>

  <!-- Main Content Area -->
  <main id="main" class="main">
    <?php
    if (!isset($_GET['page']) || $_GET['page'] == 'dashboard') {
        include 'modules/dashboard.php';
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
      &copy; Copyright <strong><span>🌴 Paradise Travel Management System</span></strong>
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
