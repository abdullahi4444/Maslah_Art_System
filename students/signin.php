<?php
    session_start();

    // Define database constants
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DEBUG_MODE', true);

    // Try to detect the correct database name
    $database_name = 'maslax_arts'; // Default database name
    $error_message = '';
    $username = '';
    $field_errors = array('username' => '', 'password' => '');

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $remember = isset($_POST['remember']) ? true : false;
        
        // Reset field errors
        $field_errors = array('username' => '', 'password' => '');
        
        // Validate inputs
        $is_valid = true;
        
        if (empty($username)) {
            $field_errors['username'] = "Username or email is required.";
            $is_valid = false;
        }
        
        if (empty($password)) {
            $field_errors['password'] = "Password is required.";
            $is_valid = false;
        }
        
        if ($is_valid) {
            try {
                // First try to connect to the database
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . $database_name . ";charset=utf8";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                
                $db = new PDO($dsn, DB_USER, DB_PASS, $options);
                
                // Check if user exists in users table
                $sql = "SELECT id, username, password FROM users WHERE username = :username OR email = :email";
                $stmt = $db->prepare($sql);
                $stmt->execute([':username' => $username, ':email' => $username]);
                
                if ($stmt->rowCount() == 1) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (password_verify($password, $user['password'])) {
                        // Password is correct, start session
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $user['id'];
                        $_SESSION["username"] = $user['username'];
                        $_SESSION["user_type"] = "student"; // Regular user
                        
                        // Remember me functionality
                        if ($remember) {
                            setcookie("remember_user", $username, time() + (30 * 24 * 60 * 60), "/");
                        }
                        
                        // Redirect to student account page
                        header("location: std-account.php");
                        exit;
                    } else {
                        $error_message = "Invalid password.";
                    }
                } else {
                    // Check if user exists in admins table
                    $sql = "SELECT admin_id, first_name, last_name, email, password_hash FROM admins WHERE email = :username";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([':username' => $username]);
                    
                    if ($stmt->rowCount() == 1) {
                        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if (password_verify($password, $admin['password_hash'])) {
                            // Password is correct, start admin session
                            $_SESSION["loggedin"] = true;
                            $_SESSION["admin_id"] = $admin['admin_id'];
                            $_SESSION["admin_name"] = $admin['first_name'] . ' ' . $admin['last_name'];
                            $_SESSION["user_type"] = "admin"; // Admin user
                            
                            // Remember me functionality
                            if ($remember) {
                                setcookie("remember_admin", $username, time() + (30 * 24 * 60 * 60), "/");
                            }
                            
                            // Redirect to admin dashboard
                            header("location: ../admin/dashboard.php");
                            exit;
                        } else {
                            $error_message = "Invalid password.";
                        }
                    } else {
                        $error_message = "No account found with that username/email.";
                    }
                }
            } catch (PDOException $e) {
                // If database doesn't exist, try to create it
                if ($e->getCode() == 1049) { // Unknown database error
                    try {
                        // Connect without database name
                        $dsn = "mysql:host=" . DB_HOST . ";charset=utf8";
                        $db = new PDO($dsn, DB_USER, DB_PASS, $options);
                        
                        // Create database
                        $db->exec("CREATE DATABASE IF NOT EXISTS $database_name");
                        $db->exec("USE $database_name");
                        
                        // Create users table
                        $db->exec("CREATE TABLE IF NOT EXISTS users (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            username VARCHAR(50) NOT NULL UNIQUE,
                            email VARCHAR(100) NOT NULL UNIQUE,
                            password VARCHAR(255) NOT NULL,
                            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                        )");
                        
                        // Create admins table
                        $db->exec("CREATE TABLE IF NOT EXISTS admins (
                            admin_id INT AUTO_INCREMENT PRIMARY KEY,
                            first_name VARCHAR(50) NOT NULL,
                            last_name VARCHAR(50) NOT NULL,
                            email VARCHAR(100) NOT NULL UNIQUE,
                            password_hash VARCHAR(255) NOT NULL,
                            phone VARCHAR(20) NULL,
                            social_links TEXT NULL,
                            bio TEXT NULL,
                            skills VARCHAR(255) NULL,
                            profile_picture VARCHAR(255) NULL,
                            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                        )");
                        
                        // Insert default admin user
                        $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
                        $db->exec("INSERT INTO admins (first_name, last_name, email, password_hash) VALUES 
                                ('Admin', 'User', 'admin@example.com', '$defaultPassword')");
                        
                        $error_message = "Database and tables created successfully. Default admin created (admin@example.com / admin123). Please try to sign up first for student account.";
                        
                    } catch (PDOException $create_error) {
                        $error_message = "Database error. Please try again later.";
                        if (DEBUG_MODE) {
                            $error_message .= " Error details: " . $create_error->getMessage();
                        }
                    }
                } else {
                    $error_message = "Database error. Please try again later.";
                    if (DEBUG_MODE) {
                        $error_message .= " Error details: " . $e->getMessage();
                    }
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Maslah Arts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .error-message {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 5px;
            display: block;
            padding-left: 50px;
            animation: fadeIn 0.3s ease-in;
            position: absolute;
            bottom: -20px;
            left: -30px;
            width: 100%;
        }
        
        .std-input-group {
            margin-bottom: 30px;
            position: relative;
        }
        
        .std-input-group.error {
            margin-bottom: 30px;
        }
        
        .std-input-field.error {
            border: 2px solid #dc3545;
            background-color: #ffe6e6;
        }
        
        .alert-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .alert-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 400px;
            width: 90%;
            animation: modalFadeIn 0.3s ease-out;
        }
        
        .alert-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        
        .alert-success .alert-icon {
            color: #28a745;
        }
        
        .alert-error .alert-icon {
            color: #dc3545;
        }
        
        .alert-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }
        
        .alert-message {
            margin-bottom: 20px;
            color: #666;
            line-height: 1.5;
        }
        
        .alert-button {
            padding: 12px 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .alert-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .shake {
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .std-form-container {
            padding: 40px 30px;
            position: relative;
        }
        
        .debug-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            font-family: monospace;
            font-size: 14px;
        }
        
        .info-message {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #0c5460;
        }
        
        .admin-login-note {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 10px;
            margin: 15px 0;
            color: #856404;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body class="std-body">
    <!-- Error Modal -->
    <div id="errorModal" class="alert-modal alert-error">
        <div class="alert-content">
            <div class="alert-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h3 class="alert-title">Error</h3>
            <p class="alert-message" id="errorModalMessage"></p>
            <button class="alert-button" onclick="closeModal('errorModal')">OK</button>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="alert-modal alert-success">
        <div class="alert-content">
            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 class="alert-title">Success</h3>
            <p class="alert-message" id="successModalMessage"></p>
            <button class="alert-button" onclick="closeModal('successModal')">OK</button>
        </div>
    </div>

    <div class="std-card">
        <div class="std-header">
            <div class="std-logo-container">
                <img class="std-logo" src="logo.jpg" alt="Maslah Arts Logo" class="std-logo">
            </div>
            <div class="std-cont">
                <h1 class="std-welcome-text">Hello!</h1>
                <p class="std-subtitle">Welcome to maslah arts</p>
            </div>
        </div>
        
        <div class="std-form-container">
            <div class="std-form-title">
                <span><i class="fas fa-sign-in-alt"></i>Login</span>
            </div>
            
            <?php if (DEBUG_MODE): ?>
            <!-- <div class="debug-info">
                <strong>Debug Information:</strong><br>
                DB_HOST: <?php echo DB_HOST; ?><br>
                DB_NAME: <?php echo $database_name; ?><br>
                DB_USER: <?php echo DB_USER; ?><br>
            </div> -->
            <?php endif; ?>
            
            <?php if (strpos($error_message, 'Database and tables created successfully') !== false): ?>
            <div class="info-message">
                <i class="fas fa-info-circle"></i> 
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>
            
            <div class="admin-login-note">
                <i class="fas fa-info-circle"></i> 
                Admins: Please use your email address to login
            </div>
            
            <form id="std-login-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" novalidate>
                <div class="std-input-group <?php echo !empty($field_errors['username']) ? 'error' : ''; ?>">
                    <i class="fas fa-user std-input-icon"></i>
                    <input type="text" class="std-input-field <?php echo !empty($field_errors['username']) ? 'error' : ''; ?>" 
                           name="username" placeholder="Username or Email" value="<?php echo htmlspecialchars($username); ?>" 
                           oninput="clearError(this)">
                    <?php if (!empty($field_errors['username'])): ?>
                        <span class="error-message"><?php echo $field_errors['username']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="std-input-group <?php echo !empty($field_errors['password']) ? 'error' : ''; ?>">
                    <i class="fas fa-lock std-input-icon"></i>
                    <input type="password" class="std-input-field <?php echo !empty($field_errors['password']) ? 'error' : ''; ?>" 
                           id="std-login-password" name="password" placeholder="Password" oninput="clearError(this)">
                    <button type="button" class="std-password-toggle" id="std-login-password-toggle">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                    <?php if (!empty($field_errors['password'])): ?>
                        <span class="error-message"><?php echo $field_errors['password']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="std-remember-forgot">
                    <label class="std-remember">
                        <input type="checkbox" name="remember">
                        Remember me
                    </label>
                    <a href="forgot_password.php" class="std-forgot">Forgot password?</a>
                </div>
                
                <button type="submit" class="std-login-btn">Login</button>
            </form>
            
            <a href="signUp.php" class="std-signup-link">
                <span class="std-signup-text"><i class="fas fa-user-plus"></i>Sign up</span> now
            </a>
        </div>
    </div>

    <script>
        // Password toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const passwordToggle = document.getElementById('std-login-password-toggle');
            const passwordInput = document.getElementById('std-login-password');
            
            passwordToggle.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle eye icon
                const eyeIcon = this.querySelector('i');
                if (type === 'password') {
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                } else {
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                }
            });
            
            // Form validation
            const loginForm = document.getElementById('std-login-form');
            loginForm.addEventListener('submit', function(e) {
                let isValid = true;
                const username = document.querySelector('input[name="username"]');
                const password = document.getElementById('std-login-password');
                
                // Clear previous errors
                clearAllErrors();
                
                // Validate username
                if (!username.value.trim()) {
                    showError(username, 'Username or email is required.');
                    isValid = false;
                }
                
                // Validate password
                if (!password.value.trim()) {
                    showError(password, 'Password is required.');
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                    // Shake form for attention
                    document.querySelector('.std-form-container').classList.add('shake');
                    setTimeout(() => {
                        document.querySelector('.std-form-container').classList.remove('shake');
                    }, 500);
                }
            });
            
            // Show server-side error if exists
            <?php if (!empty($error_message)): ?>
                <?php if (strpos($error_message, 'Database and tables created successfully') !== false): ?>
                    showModal('successModal', '<?php echo addslashes($error_message); ?>');
                <?php else: ?>
                    showModal('errorModal', '<?php echo addslashes($error_message); ?>');
                <?php endif; ?>
            <?php endif; ?>
        });
        
        function showError(input, message) {
            const inputGroup = input.closest('.std-input-group');
            inputGroup.classList.add('error');
            input.classList.add('error');
            
            // Remove existing error message
            const existingError = inputGroup.querySelector('.error-message');
            if (existingError) {
                existingError.remove();
            }
            
            // Add new error message
            const errorSpan = document.createElement('span');
            errorSpan.className = 'error-message';
            errorSpan.textContent = message;
            inputGroup.appendChild(errorSpan);
        }
        
        function clearError(input) {
            const inputGroup = input.closest('.std-input-group');
            inputGroup.classList.remove('error');
            input.classList.remove('error');
            
            const errorSpan = inputGroup.querySelector('.error-message');
            if (errorSpan) {
                errorSpan.remove();
            }
        }
        
        function clearAllErrors() {
            document.querySelectorAll('.std-input-group.error').forEach(group => {
                group.classList.remove('error');
            });
            document.querySelectorAll('.std-input-field.error').forEach(input => {
                input.classList.remove('error');
            });
            document.querySelectorAll('.error-message').forEach(error => {
                error.remove();
            });
        }
        
        function showModal(modalId, message) {
            const modal = document.getElementById(modalId);
            const messageElement = document.getElementById(modalId + 'Message');
            
            if (messageElement && message) {
                messageElement.textContent = message;
            }
            
            modal.style.display = 'flex';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('alert-modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>