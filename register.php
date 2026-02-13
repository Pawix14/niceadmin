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
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        // Check if username exists in both tables
        $check_admin = $conn->query("SELECT id FROM admins WHERE username='$username'");
        $check_customer = $conn->query("SELECT id FROM customers WHERE username='$username'");
        
        if ($check_admin->num_rows > 0 || $check_customer->num_rows > 0) {
            $error = 'Username already exists';
        } else {
            // Check if email exists
            $check_email = $conn->query("SELECT id FROM customers WHERE email='$email'");
            if ($check_email->num_rows > 0) {
                $error = 'Email already exists';
            } else {
                $customer_id = 'CUST' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $sql = "INSERT INTO customers (customer_id, username, password, full_name, email, phone, status) 
                        VALUES ('$customer_id', '$username', '$hashed_password', '$full_name', '$email', '$phone', 'Active')";
                
                if ($conn->query($sql)) {
                    header('Location: login.php?registered=1');
                    exit();
                } else {
                    $error = 'Registration failed. Please try again.';
                }
            }
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
    <title>Register - CarGo</title>
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

        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 1000px;
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

        .register-left {
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

        .register-left::before {
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

        .register-left-content {
            position: relative;
            z-index: 1;
        }

        .register-left h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .register-left p {
            font-size: 1.05rem;
            opacity: 0.95;
            line-height: 1.7;
        }

        .register-right {
            flex: 1.2;
            padding: 60px 50px;
            max-height: 90vh;
            overflow-y: auto;
            background: white;
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header .logo {
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

        .register-header .logo i {
            font-size: 2rem;
            color: white;
        }

        .register-header h2 {
            color: #2c3e50;
            font-size: 1.9rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .register-header p {
            color: #666;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 20px;
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

        .password-strength {
            margin-top: 8px;
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            overflow: hidden;
            display: none;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s;
            border-radius: 2px;
        }

        .strength-weak { background: #f44336; width: 33%; }
        .strength-medium { background: #ff9800; width: 66%; }
        .strength-strong { background: #4caf50; width: 100%; }

        .password-strength-text {
            margin-top: 5px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .text-weak { color: #f44336; }
        .text-medium { color: #ff9800; }
        .text-strong { color: #4caf50; }

        .btn-register {
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

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 102, 102, 0.4);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .btn-register:disabled {
            opacity: 0.6;
            cursor: not-allowed;
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

        .login-link {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 0.95rem;
        }

        .login-link a {
            color: #666;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.3s;
        }

        .login-link a:hover {
            color: #2c3e50;
        }

        .password-requirements {
            font-size: 0.85rem;
            color: #666;
            margin-top: 5px;
        }

        .password-requirements ul {
            margin: 5px 0 0 20px;
            padding: 0;
        }

        .password-requirements li {
            margin: 3px 0;
        }

        .requirement-met {
            color: #4caf50;
        }

        @media (max-width: 768px) {
            .register-container {
                flex-direction: column;
            }

            .register-left {
                padding: 40px 30px;
            }

            .register-right {
                padding: 40px 30px;
            }

            .register-left h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-left">
            <div class="register-left-content">
                <h1><i class="bi bi-car-front-fill"></i> CarGo</h1>
                <p>Join our car rental management system. Create your customer account to start booking cars and enjoy exclusive deals.</p>
            </div>
        </div>

        <div class="register-right">
            <div class="register-header">
                <div class="logo">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h2>Create Account</h2>
                <p>Fill in your details to register</p>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="" id="registerForm">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <div class="input-group">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" name="full_name" class="form-control" placeholder="Enter your full name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Username *</label>
                    <div class="input-group">
                        <i class="bi bi-person-badge input-icon"></i>
                        <input type="text" name="username" class="form-control" placeholder="Choose a username" required minlength="4">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <div class="input-group">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <div class="input-group">
                        <i class="bi bi-phone input-icon"></i>
                        <input type="tel" name="phone" class="form-control" placeholder="Enter your phone number">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password *</label>
                    <div class="input-group">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Create a password" required minlength="6">
                    </div>
                    <div class="password-strength" id="passwordStrength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="password-strength-text" id="strengthText"></div>
                    <div class="password-requirements">
                        <ul id="requirements">
                            <li id="req-length">At least 6 characters</li>
                            <li id="req-uppercase">One uppercase letter</li>
                            <li id="req-lowercase">One lowercase letter</li>
                            <li id="req-number">One number</li>
                        </ul>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm Password *</label>
                    <div class="input-group">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input type="password" name="confirm_password" id="confirmPassword" class="form-control" placeholder="Confirm your password" required>
                    </div>
                    <div id="passwordMatch" style="margin-top: 5px; font-size: 0.85rem;"></div>
                </div>

                <button type="submit" class="btn-register" id="submitBtn">
                    <i class="bi bi-person-plus me-2"></i>Create Account
                </button>
            </form>

            <div class="login-link">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        const passwordStrength = document.getElementById('passwordStrength');
        const submitBtn = document.getElementById('submitBtn');
        const passwordMatch = document.getElementById('passwordMatch');

        // Password strength checker
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            passwordStrength.style.display = 'block';
            
            let strength = 0;
            const requirements = {
                length: password.length >= 6,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password)
            };

            // Update requirements
            document.getElementById('req-length').className = requirements.length ? 'requirement-met' : '';
            document.getElementById('req-uppercase').className = requirements.uppercase ? 'requirement-met' : '';
            document.getElementById('req-lowercase').className = requirements.lowercase ? 'requirement-met' : '';
            document.getElementById('req-number').className = requirements.number ? 'requirement-met' : '';

            // Calculate strength
            if (requirements.length) strength++;
            if (requirements.uppercase) strength++;
            if (requirements.lowercase) strength++;
            if (requirements.number) strength++;

            // Update UI
            strengthBar.className = 'password-strength-bar';
            strengthText.className = 'password-strength-text';

            if (strength <= 1) {
                strengthBar.classList.add('strength-weak');
                strengthText.classList.add('text-weak');
                strengthText.textContent = 'Weak password';
            } else if (strength <= 3) {
                strengthBar.classList.add('strength-medium');
                strengthText.classList.add('text-medium');
                strengthText.textContent = 'Medium password';
            } else {
                strengthBar.classList.add('strength-strong');
                strengthText.classList.add('text-strong');
                strengthText.textContent = 'Strong password';
            }

            checkPasswordMatch();
        });

        // Password match checker
        confirmPasswordInput.addEventListener('input', checkPasswordMatch);

        function checkPasswordMatch() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    passwordMatch.innerHTML = '<i class="bi bi-check-circle text-strong"></i> Passwords match';
                    passwordMatch.className = 'text-strong';
                } else {
                    passwordMatch.innerHTML = '<i class="bi bi-x-circle text-weak"></i> Passwords do not match';
                    passwordMatch.className = 'text-weak';
                }
            } else {
                passwordMatch.innerHTML = '';
            }
        }

        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }

            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters!');
                return false;
            }
        });
    </script>

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
