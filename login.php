<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: index.php');
    exit();
}

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'travel_db_improved';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string(trim($_POST['username']));
    $password = trim($_POST['password']);
    $user_type = isset($_POST['user_type']) ? $_POST['user_type'] : 'customer';
    
    // Try admin login first
    $result = $conn->query("SELECT * FROM admins WHERE username='$username'");
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if ($user['status'] !== 'Active') {
            $error = 'Your account is inactive. Please contact administrator.';
        } elseif (password_verify($password, $user['password'])) {
            $conn->query("UPDATE admins SET last_login=NOW() WHERE id=" . $user['id']);
            
            $_SESSION['logged_in'] = true;
            $_SESSION['user_type'] = 'admin';
            $_SESSION['user_id'] = $user['admin_id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            
            header('Location: index.php');
            exit();
        } else {
            $error = 'Invalid username or password';
        }
    } else {
        // Try customer login
        $result = $conn->query("SELECT * FROM customers WHERE username='$username'");
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if ($user['status'] !== 'Active') {
                $error = 'Your account is inactive. Please contact administrator.';
            } elseif (password_verify($password, $user['password'])) {
                $conn->query("UPDATE customers SET last_login=NOW() WHERE id=" . $user['id']);
                
                $_SESSION['logged_in'] = true;
                $_SESSION['user_type'] = 'customer';
                $_SESSION['user_id'] = $user['customer_id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['username'] = $user['username'];
                
                header('Location: index.php');
                exit();
            } else {
                $error = 'Invalid username or password';
            }
        } else {
            $error = 'Invalid username or password';
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CarGo</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 50%, #666 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: moveBackground 20s linear infinite;
        }

        @keyframes moveBackground {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 950px;
            width: 100%;
            display: flex;
            animation: slideUp 0.6s ease;
            position: relative;
            z-index: 1;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #666 0%, #2c3e50 100%);
            padding: 60px 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 2px, transparent 2px);
            background-size: 40px 40px;
            animation: rotatePattern 30s linear infinite;
        }

        @keyframes rotatePattern {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .login-left-content {
            position: relative;
            z-index: 1;
        }

        .login-left h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .login-left p {
            font-size: 1.05rem;
            opacity: 0.95;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .feature-list {
            list-style: none;
            margin-top: 30px;
        }

        .feature-list li {
            padding: 12px 0;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .feature-list li i {
            margin-right: 12px;
            font-size: 1.3rem;
            color: #4ade80;
        }

        .login-right {
            flex: 1;
            padding: 60px 50px;
            background: white;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #666 0%, #2c3e50 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 4px 15px rgba(102, 102, 102, 0.3);
        }

        .login-header .logo i {
            font-size: 2rem;
            color: white;
        }

        .login-header h2 {
            color: #2c3e50;
            font-size: 1.9rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .login-header p {
            color: #666;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 1.2rem;
            transition: color 0.3s;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
            background: #fafafa;
        }

        .form-control:focus {
            outline: none;
            border-color: #666;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 102, 102, 0.1);
        }

        .form-control:focus + .input-icon {
            color: #666;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #666 0%, #2c3e50 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 15px;
            box-shadow: 0 4px 15px rgba(102, 102, 102, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 102, 102, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 25px;
            animation: shake 0.5s;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: opacity 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .alert-danger {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .register-link {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 0.95rem;
        }

        .register-link a {
            color: #666;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.3s;
        }

        .register-link a:hover {
            color: #2c3e50;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 25px 0;
            color: #999;
            font-size: 0.85rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e0e0e0;
        }

        .divider span {
            padding: 0 15px;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                border-radius: 15px;
            }

            .login-left {
                padding: 40px 30px;
            }

            .login-right {
                padding: 40px 30px;
            }

            .login-left h1 {
                font-size: 2rem;
            }

            .feature-list {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <div class="login-left-content">
                <h1><i class="bi bi-car-front-fill"></i> CarGo</h1>
                <p>Your trusted car rental management system. Login to access your dashboard and manage bookings with ease.</p>
                <ul class="feature-list">
                    <li><i class="bi bi-check-circle-fill"></i> Manage car rentals efficiently</li>
                    <li><i class="bi bi-check-circle-fill"></i> Track bookings in real-time</li>
                    <li><i class="bi bi-check-circle-fill"></i> Customer management tools</li>
                    <li><i class="bi bi-check-circle-fill"></i> Sales & promotions control</li>
                </ul>
            </div>
        </div>

        <div class="login-right">
            <div class="login-header">
                <div class="logo">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <h2>Welcome Back</h2>
                <p>Please login to your account</p>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> Registration successful! Please login.
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['logout'])): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> You have been logged out successfully.
            </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" name="username" class="form-control" placeholder="Enter your username" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                </button>
            </form>

            <div class="register-link">
                Don't have an account? <a href="register.php">Register here</a>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alerts after 3 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() { alert.remove(); }, 500);
            });
        }, 3000);
    </script>
</body>
</html>
