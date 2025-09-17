<?php
    session_start();
    // At the top of your main PHP file
    // require_once './includes/sidebar.php';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    // Check if user is logged in, if not redirect to login page
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("location: signin.php");
        exit;
    }

    // Include database configuration
    require_once '../admin/config.php';

    // Add getDBConnection function if not already defined
    if (!function_exists('getDBConnection')) {
        function getDBConnection() {
            // Update these variables according to your config.php settings
            $host = 'localhost';
            $dbname = 'maslax_arts';
            $username = 'root';
            $password = '';
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            try {
                $pdo = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                return $pdo;
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
    }

    $username = $_SESSION["username"] ?? '';
    $user_id = $_SESSION["id"] ?? '';
    $email = '';
    $join_date = '';
    $profile_image = 'assets/image/icon.png';
    $success_message = '';
    $error_message = '';

    // Form validation variables
    $current_password_err = $new_password_err = $confirm_password_err = '';

    // Get user details from database
    if (!empty($user_id)) {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("SELECT email, created_at, profile_image FROM users WHERE id = :id");
            $stmt->execute([':id' => $user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $email = $user['email'] ?? '';
                $join_date = $user['created_at'] ?? '';
                $profile_image = $user['profile_image'] ?? 'assets/image/icon.png';
            }
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    }

    // Handle form submission for password change
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $current_password = trim($_POST['current_password'] ?? '');
        $new_password = trim($_POST['new_password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');
        
        // Validate inputs
        $is_valid = true;
        
        if (empty($current_password)) {
            $current_password_err = "Current password is required.";
            $is_valid = false;
        }
        
        if (empty($new_password)) {
            $new_password_err = "New password is required.";
            $is_valid = false;
        } elseif (strlen($new_password) < 8) {
            $new_password_err = "Password must be at least 8 characters long.";
            $is_valid = false;
        }
        
        if (empty($confirm_password)) {
            $confirm_password_err = "Please confirm your password.";
            $is_valid = false;
        } elseif ($new_password !== $confirm_password) {
            $confirm_password_err = "New passwords do not match.";
            $is_valid = false;
        }
        
        if ($is_valid) {
            // Verify current password
            try {
                $stmt = $db->prepare("SELECT password FROM users WHERE id = :id");
                $stmt->execute([':id' => $user_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user && password_verify($current_password, $user['password'])) {
                    // Update password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
                    $update_stmt->execute([
                        ':password' => $hashed_password,
                        ':id' => $user_id
                    ]);
                    
                    $success_message = "Password updated successfully!";
                } else {
                    $current_password_err = "Current password is incorrect.";
                }
            } catch (PDOException $e) {
                $error_message = "Database error: " . $e->getMessage();
            }
        }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - Maslah Arts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #d5b0f7ff;
            color: #333;
            line-height: 1.6;
        }

        .acc-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Desktop Header */
        .acc-main-header {
            display: block;
        }

        .acc-header {
            background: radial-gradient(circle at center, rgba(221, 11, 225, 0.42), rgba(0, 0, 0, 0.9)),
                        url('Assets/image/hero.png') center/cover no-repeat;
            padding: 50px;
            height: 250px;
            color: white;
            box-shadow: inset 0 0 100px rgba(16, 8, 8, 0.9),
                        0 10px 30px rgba(0, 0, 0, 0.3);
        }
  
        /* Shared Breadcrumb Styles */
            .acc-breadcrumb,
            .std-breadcrumb {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 20px;
                font-size: 15px;
                font-weight: 500;
                font-family: "Poppins", sans-serif; /* Clean modern font */
            }

            /* Breadcrumb links */
            .acc-breadcrumb a {
                color: #e5e5e5;
                text-decoration: none;
                transition: color 0.3s ease, transform 0.2s ease;
            }

            .acc-breadcrumb a:hover {
                color: #4ade80; /* soft green hover */
                transform: translateY(-1px); /* subtle lift */
            }

            /* Breadcrumb separator */
            .acc-breadcrumb span {
                color: #aaa;
                font-size: 18px;
            }

            /* Breadcrumb Titles */
            .acc-breadcrumb h1 {
                color: #fff;
                font-size: 28px;
                font-weight: 700;
                margin-left: 10px;
                font-family: "Poppins", sans-serif;
                letter-spacing: 0.5px;
            }

            /* Page Titles */
            .acc-page-title {
                font-size: 26px;
                font-weight: 700;
                margin-bottom: 15px;
                color: #fff;
                font-family: "Poppins", sans-serif;
            }


        .acc-page-subtitle {
            font-size: 16px;
            opacity: 0.9;
        }

        /* Main Content Layout */
        .acc-main-layout {
            display: flex;
            flex: 1;
        }

        /* Main Content Styles */
        .acc-main-content {
            flex: 1;
            padding: 30px;
        }

        /* Account Card Styles */
        .acc-account-card {
            width: 100%;
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .acc-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .acc-card-title {
            font-size: 22px;
            font-weight: 600;
            color: #333;
        }

        .acc-card-subtitle {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }

        /* Profile Section */
        .acc-profile-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
        }

        .acc-profile-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #7d0de6ff 0%, #9b4fedff 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            margin-right: 15px;
            overflow: hidden;
        }

        .acc-profile-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .acc-profile-info h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .acc-profile-info p {
            font-size: 14px;
            color: #666;
        }

        /* Form Styles */
        .acc-form-section h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }

        .acc-form-group {
            margin-bottom: 20px;
        }

        .acc-form-label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
        }

        .acc-input-group {
            position: relative;
        }

        .acc-input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 16px;
        }

        .acc-form-input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .acc-form-input:focus {
            outline: none;
            border-color: #8013e6;
            box-shadow: 0 0 0 3px rgba(128, 19, 230, 0.1);
        }

        .acc-form-input[readonly] {
            background-color: #f8f9fa;
            color: #666;
        }

        .acc-password-note {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
        }

        .acc-btn-primary {
            background: linear-gradient(135deg, #8013e6 0%, #6a0fc9 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        .acc-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(128, 19, 230, 0.3);
        }

        .acc-btn-primary i {
            margin-right: 8px;
        }

        /* Error Messages */
        .acc-error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }

        .acc-input-error {
            border-color: #dc3545 !important;
        }

        /* Modal Styles */
        .acc-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        .acc-modal-content {
            background-color: white;
            border-radius: 12px;
            padding: 30px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            position: relative;
            text-align: center;
        }

        .acc-modal-success .acc-modal-content {
            border-top: 5px solid #28a745;
        }

        .acc-modal-error .acc-modal-content {
            border-top: 5px solid #dc3545;
        }

        .acc-modal-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .acc-modal-success .acc-modal-icon {
            color: #28a745;
        }

        .acc-modal-error .acc-modal-icon {
            color: #dc3545;
        }

        .acc-modal-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .acc-modal-message {
            margin-bottom: 25px;
            color: #666;
        }

        .acc-modal-close {
            background: linear-gradient(135deg, #8013e6 0%, #6a0fc9 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
        }

        /* Responsive Design - Modified to keep sidebar always visible */
        @media (max-width: 1024px) {
            .acc-main-layout {
                flex-direction: row;
            }
            
            .acc-sidebar {
                width: 250px;
                transform: translateX(0);
                position: relative;
                height: auto;
                z-index: 1;
                overflow-y: visible;
            }
            
            .acc-main-content {
                width: calc(100% - 250px);
                padding: 20px;
            }
            
            .acc-header {
                padding: 30px;
            }
            
            .acc-account-card {
                padding: 20px;
            }
        }

        @media (max-width: 768px) {
            .acc-header {
                padding: 20px;
            }
            
            .acc-breadcrumb h1 {
                font-size: 24px;
            }
            
            .acc-profile-section {
                flex-direction: column;
                text-align: center;
            }
            
            .acc-profile-icon {
                margin-right: 0;
                margin-bottom: 15px;
            }
            
            .acc-card-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .acc-card-title {
                font-size: 20px;
            }
            
            .acc-sidebar {
                width: 200px;
            }
            
            .acc-main-content {
                width: calc(100% - 200px);
            }
        }

        @media (max-width: 480px) {
            .acc-main-layout {
                flex-direction: column;
            }
            
            .acc-sidebar {
                width: 100%;
                order: 1;
            }
            
            .acc-main-content {
                width: 100%;
                order: 2;
                padding: 15px;
            }
            
            .acc-header {
                padding: 15px;
            }
            
            .acc-account-card {
                padding: 15px;
            }
            
            .acc-form-input {
                padding: 12px 12px 12px 40px;
            }
            
            .acc-btn-primary {
                width: 100%;
                justify-content: center;
            }
            
            .acc-modal-content {
                padding: 20px;
            }
            
            .acc-breadcrumb {
                font-size: 14px;
            }
            
            .acc-breadcrumb h1 {
                font-size: 20px;
                margin-bottom: -20px;
            }
        }
    </style>
</head>
<body>
   
    <div class="acc-container">     
        <?php include 'header.php';?>   
        <!-- Desktop Header Only - Mobile Header Removed -->
        <div class="acc-main-header">
            <div class="acc-header">
                <div class="acc-breadcrumb">
                    <span><i class="fa-solid fa-house-chimney"></i><a href="../index.php">Home</a> > Account</span>
                </div>
                <h1 class="acc-page-title">User Credentials</h1>
            </div>
        </div>

        <div class="acc-main-layout">      
                
          <?php include './includes/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="acc-main-content">
                <!-- Account Card -->
                <div class="acc-account-card">
                    <div class="acc-card-header">
                        <div>
                            <h2 class="acc-card-title">Account</h2>
                            <p class="acc-card-subtitle">Secure your profile and manage login settings</p>
                        </div>
                    </div>

                    <!-- Profile Photo Section -->
                    <div class="acc-profile-section">
                        <div class="acc-profile-icon">
                            <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Icon" onerror="this.src='assets/image/icon.png'">
                        </div>
                        <div class="acc-profile-info">
                            <h3>Profile photo</h3>
                            <p>Update your profile photo and personal details</p>
                        </div>
                    </div>

                    <!-- Account Information Form -->
                    <div class="acc-form-section">
                        <h3>Account Information</h3>
                        
                        <form method="POST" action="" id="acc-passwordForm">
                            <!-- Email Address -->
                            <div class="acc-form-group">
                                <label class="acc-form-label">Email address</label>
                                <div class="acc-input-group">
                                    <div class="acc-input-icon">
                                        <i class="ri-mail-line"></i>
                                    </div>
                                    <input type="email" class="acc-form-input" value="<?php echo htmlspecialchars($email); ?>" readonly>
                                </div>
                            </div>

                            <!-- Current Password -->
                            <div class="acc-form-group">
                                <label class="acc-form-label">Current password</label>
                                <div class="acc-input-group">
                                    <div class="acc-input-icon">
                                        <i class="ri-lock-line"></i>
                                    </div>
                                    <input type="password" name="current_password" class="acc-form-input <?php echo !empty($current_password_err) ? 'acc-input-error' : ''; ?>" placeholder="Enter current password" >
                                </div>
                                <?php if (!empty($current_password_err)): ?>
                                    <span class="acc-error-message"><?php echo $current_password_err; ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- New Password -->
                            <div class="acc-form-group">
                                <label class="acc-form-label">New password</label>
                                <div class="acc-input-group">
                                    <div class="acc-input-icon">
                                        <i class="ri-lock-line"></i>
                                    </div>
                                    <input type="password" name="new_password" class="acc-form-input <?php echo !empty($new_password_err) ? 'acc-input-error' : ''; ?>" placeholder="Enter new password" >
                                </div>
                                <p class="acc-password-note">Password must be at least 8 characters long</p>
                                <?php if (!empty($new_password_err)): ?>
                                    <span class="acc-error-message"><?php echo $new_password_err; ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Confirm Password -->
                            <div class="acc-form-group">
                                <label class="acc-form-label">Confirm password</label>
                                <div class="acc-input-group">
                                    <div class="acc-input-icon">
                                        <i class="ri-lock-line"></i>
                                    </div>
                                    <input type="password" name="confirm_password" class="acc-form-input <?php echo !empty($confirm_password_err) ? 'acc-input-error' : ''; ?>" placeholder="Confirm new password" >
                                </div>
                                <?php if (!empty($confirm_password_err)): ?>
                                    <span class="acc-error-message"><?php echo $confirm_password_err; ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Save Button -->
                            <button type="submit" class="acc-btn-primary">
                                <i class="ri-save-line"></i> Save changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="acc-modal acc-modal-success" id="acc-successModal">
        <div class="acc-modal-content">
            <div class="acc-modal-icon">
                <i class="ri-checkbox-circle-fill"></i>
            </div>
            <h3 class="acc-modal-title">Success!</h3>
            <p class="acc-modal-message" id="acc-successMessage"></p>
            <button class="acc-modal-close" onclick="accCloseModal('acc-successModal')">OK</button>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="acc-modal acc-modal-error" id="acc-errorModal">
        <div class="acc-modal-content">
            <div class="acc-modal-icon">
                <i class="ri-error-warning-fill"></i>
            </div>
            <h3 class="acc-modal-title">Error!</h3>
            <p class="acc-modal-message" id="acc-errorMessage"></p>
            <button class="acc-modal-close" onclick="accCloseModal('acc-errorModal')">OK</button>
        </div>
    </div>
    <?php include '../includes/footer.php';?> 

    <script>
        // Add password visibility toggle
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInputs = document.querySelectorAll('input[type="password"]');
            
            passwordInputs.forEach(input => {
                const toggle = document.createElement('button');
                toggle.type = 'button';
                toggle.innerHTML = '<i class="ri-eye-line"></i>';
                toggle.style.position = 'absolute';
                toggle.style.right = '15px';
                toggle.style.top = '50%';
                toggle.style.transform = 'translateY(-50%)';
                toggle.style.background = 'none';
                toggle.style.border = 'none';
                toggle.style.color = '#666';
                toggle.style.cursor = 'pointer';
                toggle.style.fontSize = '16px';
                
                const wrapper = input.parentElement;
                wrapper.style.position = 'relative';
                wrapper.appendChild(toggle);
                
                toggle.addEventListener('click', function() {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    
                    const eyeIcon = this.querySelector('i');
                    if (type === 'password') {
                        eyeIcon.classList.remove('ri-eye-off-line');
                        eyeIcon.classList.add('ri-eye-line');
                    } else {
                        eyeIcon.classList.remove('ri-eye-line');
                        eyeIcon.classList.add('ri-eye-off-line');
                    }
                });
            });

            // Show modals if there are messages
            <?php if (!empty($success_message)): ?>
                accShowModal('acc-successModal', '<?php echo $success_message; ?>');
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                accShowModal('acc-errorModal', '<?php echo $error_message; ?>');
            <?php endif; ?>

            // Form validation
            document.getElementById('acc-passwordForm').addEventListener('submit', function(e) {
                let isValid = true;
                const currentPassword = document.querySelector('input[name="current_password"]');
                const newPassword = document.querySelector('input[name="new_password"]');
                const confirmPassword = document.querySelector('input[name="confirm_password"]');
                
                // Clear previous error messages
                document.querySelectorAll('.acc-error-message').forEach(el => el.remove());
                document.querySelectorAll('.acc-input-error').forEach(el => el.classList.remove('acc-input-error'));
                
                // Validate current password
                if (!currentPassword.value.trim()) {
                    accShowError(currentPassword, 'Current password is required.');
                    isValid = false;
                }
                
                // Validate new password
                if (!newPassword.value.trim()) {
                    accShowError(newPassword, 'New password is required.');
                    isValid = false;
                } else if (newPassword.value.length < 8) {
                    accShowError(newPassword, 'Password must be at least 8 characters long.');
                    isValid = false;
                }
                
                // Validate confirm password
                if (!confirmPassword.value.trim()) {
                    accShowError(confirmPassword, 'Please confirm your password.');
                    isValid = false;
                } else if (newPassword.value !== confirmPassword.value) {
                    accShowError(confirmPassword, 'New passwords do not match.');
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                }
            });
        });

        function accShowError(input, message) {
            input.classList.add('acc-input-error');
            const errorElement = document.createElement('span');
            errorElement.className = 'acc-error-message';
            errorElement.textContent = message;
            input.parentElement.parentElement.appendChild(errorElement);
        }

        function accShowModal(modalId, message) {
            const modal = document.getElementById(modalId);
            const messageElement = modal.querySelector('.acc-modal-message');
            messageElement.textContent = message;
            modal.style.display = 'flex';
        }

        function accCloseModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('acc-modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>