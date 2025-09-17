<?php
    session_start();

    // Include configuration files
    $configPath = '../admin/config.php';
    $dbPath = '../admin/db.php';

    if (file_exists($configPath)) {
        require_once $configPath;
    } else {
        die("Configuration file not found. Please check the file path.");
    }

    if (file_exists($dbPath)) {
        require_once $dbPath;
    }

    // Define constants if not already defined in config
    if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
    if (!defined('DB_NAME')) define('DB_NAME', 'maslax_arts');
    if (!defined('DB_USER')) define('DB_USER', 'root');
    if (!defined('DB_PASS')) define('DB_PASS', '');
    if (!defined('DEBUG_MODE')) define('DEBUG_MODE', true);

    $error_message = '';
    $success_message = '';
    $email = $username = '';
    $field_errors = array('email' => '', 'username' => '', 'password' => '');

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = trim($_POST['email']);
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        
        // Reset field errors
        $field_errors = array('email' => '', 'username' => '', 'password' => '');
        
        // Validate inputs
        $is_valid = true;
        
        if (empty($email)) {
            $field_errors['email'] = "Email is required.";
            $is_valid = false;
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $field_errors['email'] = "Invalid email format.";
            $is_valid = false;
        }
        
        if (empty($username)) {
            $field_errors['username'] = "Username is required.";
            $is_valid = false;
        } elseif (strlen($username) < 3) {
            $field_errors['username'] = "Username must be at least 3 characters.";
            $is_valid = false;
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $field_errors['username'] = "Username can only contain letters, numbers, and underscores.";
            $is_valid = false;
        }
        
        if (empty($password)) {
            $field_errors['password'] = "Password is required.";
            $is_valid = false;
        } elseif (strlen($password) < 6) {
            $field_errors['password'] = "Password must be at least 6 characters.";
            $is_valid = false;
        }
        
        if ($is_valid) {
            try {
                // Create database connection
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                
                $db = new PDO($dsn, DB_USER, DB_PASS, $options);
                
                // Check if email or username already exists
                $check_sql = "SELECT id FROM users WHERE email = :email OR username = :username";
                $check_stmt = $db->prepare($check_sql);
                $check_stmt->execute([':email' => $email, ':username' => $username]);
                
                if ($check_stmt->rowCount() > 0) {
                    $error_message = "Email or username already exists.";
                } else {
                    // Hash password and insert new user
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $insert_sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
                    
                    $insert_stmt = $db->prepare($insert_sql);
                    $insert_stmt->execute([
                        ':username' => $username,
                        ':email' => $email,
                        ':password' => $hashed_password
                    ]);
                    
                    if ($insert_stmt->rowCount() > 0) {
                        $_SESSION['success_message'] = "Registration successful! You can now login.";
                        header("Location: signin.php");
                        exit();
                    } else {
                        $error_message = "Something went wrong. Please try again.";
                    }
                }
            } catch (PDOException $e) {
                $error_message = "Database error. Please try again later.";
                if (DEBUG_MODE) {
                    $error_message .= " Error details: " . $e->getMessage();
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
    <title>Sign Up - Maslah Arts</title>
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
            margin-bottom: 0;
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
                <span><i class="fas fa-user-plus"></i>Sign Up</span>
            </div>
            
            <?php if (DEBUG_MODE): ?>
            <!-- <div class="debug-info">
                <strong>Debug Information:</strong><br>
                DB_HOST: <?php echo DB_HOST; ?><br>
                DB_NAME: <?php echo DB_NAME; ?><br>
                DB_USER: <?php echo DB_USER; ?><br>
                Config Path: <?php echo $configPath; ?><br>
                Config Exists: <?php echo file_exists($configPath) ? 'Yes' : 'No'; ?><br>
                DB Path: <?php echo $dbPath; ?><br>
                DB Exists: <?php echo file_exists($dbPath) ? 'Yes' : 'No'; ?>
            </div> -->
            <?php endif; ?>
            
            <form id="std-signup-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" novalidate>
                <div class="std-input-group <?php echo !empty($field_errors['email']) ? 'error' : ''; ?>">
                    <i class="fas fa-envelope std-input-icon"></i>
                    <input type="email" class="std-input-field <?php echo !empty($field_errors['email']) ? 'error' : ''; ?>" 
                           name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" 
                           oninput="clearError(this)">
                    <?php if (!empty($field_errors['email'])): ?>
                        <span class="error-message"><?php echo $field_errors['email']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="std-input-group <?php echo !empty($field_errors['username']) ? 'error' : ''; ?>">
                    <i class="fas fa-user std-input-icon"></i>
                    <input type="text" class="std-input-field <?php echo !empty($field_errors['username']) ? 'error' : ''; ?>" 
                           name="username" placeholder="User name" value="<?php echo htmlspecialchars($username); ?>" 
                           oninput="clearError(this)">
                    <?php if (!empty($field_errors['username'])): ?>
                        <span class="error-message"><?php echo $field_errors['username']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="std-input-group <?php echo !empty($field_errors['password']) ? 'error' : ''; ?>">
                    <i class="fas fa-lock std-input-icon"></i>
                    <input type="password" class="std-input-field <?php echo !empty($field_errors['password']) ? 'error' : ''; ?>" 
                           id="std-password" name="password" placeholder="Password" oninput="clearError(this)">
                    <button type="button" class="std-password-toggle" id="std-password-toggle">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                    <?php if (!empty($field_errors['password'])): ?>
                        <span class="error-message"><?php echo $field_errors['password']; ?></span>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="std-signup-btn">Sign Up</button>
            </form>
            
            <a href="signin.php" class="std-signin-link">
                <span class="std-signin-text"><i class="fa-solid fa-arrow-right-to-bracket"></i>&nbsp; Sign in</span> now
            </a>
        </div>
    </div>

    <script>
        // Password toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const passwordToggle = document.getElementById('std-password-toggle');
            const passwordInput = document.getElementById('std-password');
            
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
            const signupForm = document.getElementById('std-signup-form');
            signupForm.addEventListener('submit', function(e) {
                let isValid = true;
                const email = document.querySelector('input[name="email"]');
                const username = document.querySelector('input[name="username"]');
                const password = document.getElementById('std-password');
                
                // Clear previous errors
                clearAllErrors();
                
                // Validate email
                if (!email.value.trim()) {
                    showError(email, 'Email is required.');
                    isValid = false;
                } else if (!isValidEmail(email.value)) {
                    showError(email, 'Please enter a valid email address.');
                    isValid = false;
                }
                
                // Validate username
                if (!username.value.trim()) {
                    showError(username, 'Username is required.');
                    isValid = false;
                } else if (username.value.length < 3) {
                    showError(username, 'Username must be at least 3 characters.');
                    isValid = false;
                } else if (!/^[a-zA-Z0-9_]+$/.test(username.value)) {
                    showError(username, 'Username can only contain letters, numbers, and underscores.');
                    isValid = false;
                }
                
                // Validate password
                if (!password.value.trim()) {
                    showError(password, 'Password is required.');
                    isValid = false;
                } else if (password.value.length < 6) {
                    showError(password, 'Password must be at least 6 characters.');
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
                showModal('errorModal', '<?php echo addslashes($error_message); ?>');
            <?php endif; ?>
        });
        
        function isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
        
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