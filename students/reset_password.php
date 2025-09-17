<?php
session_start();
require_once '../admin/config.php';

$error_message = '';
$success_message = '';

// Check if code is verified
if (!isset($_SESSION['code_verified']) || !$_SESSION['code_verified']) {
    header("Location: verify_code.php");
    exit();
}

$email = $_SESSION['reset_email'];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validate passwords
    if (empty($password)) {
        $error_message = "Please enter a new password.";
    } elseif (strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        try {
            // Create database connection
            $host = defined('DB_HOST') ? DB_HOST : 'localhost';
            $user = defined('DB_USER') ? DB_USER : 'root';
            $pass = defined('DB_PASS') ? DB_PASS : '';
            $dbname = 'maslax_arts';
            
            $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Update password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_stmt = $db->prepare("UPDATE users SET password = :password WHERE email = :email");
            $update_stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
            $update_stmt->bindParam(':email', $email, PDO::PARAM_STR);
            
            if ($update_stmt->execute()) {
                // Mark token as used
                $mark_used_stmt = $db->prepare("UPDATE password_reset_tokens SET is_used = 1 WHERE user_id = (SELECT id FROM users WHERE email = :email)");
                $mark_used_stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $mark_used_stmt->execute();
                
                // Clear session
                unset($_SESSION['reset_email']);
                unset($_SESSION['reset_token']);
                unset($_SESSION['code_verified']);
                
                $success_message = "Password reset successfully! You can now login with your new password.";
            } else {
                $error_message = "Failed to reset password. Please try again.";
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
    <title>Reset Password - Maslah Arts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    body {
        background-color: #d5b0f7ff;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    
    .reset-container {
        background: white;
        border-radius: 16px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
        width: 100%;
        max-width: 480px;
        overflow: hidden;
        animation: fadeIn 0.6s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .reset-header {
        background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80') center/cover;
        padding: 35px 30px;
        color: white;
        text-align: center;
        position: relative;
    }
    
    .reset-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: linear-gradient(90deg, #8013e6, #6a0fc9);
    }
    
    .reset-header h1 {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 10px;
        letter-spacing: 0.5px;
    }
    
    .reset-header p {
        font-size: 16px;
        opacity: 0.9;
        font-weight: 300;
    }
    
    .reset-body {
        padding: 35px;
    }
    
    .form-group {
        margin-bottom: 24px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 10px;
        font-weight: 500;
        color: #333;
        font-size: 15px;
    }
    
    .input-group {
        position: relative;
    }
    
    .input-group i {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #8013e6;
        z-index: 2;
        font-size: 18px;
    }
    
    .form-control {
        width: 100%;
        padding: 16px 20px 16px 50px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        font-size: 16px;
        transition: all 0.3s;
        position: relative;
        z-index: 1;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #8013e6;
        box-shadow: 0 0 0 4px rgba(128, 19, 230, 0.15);
    }
    
    .btn {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, #8013e6 0%, #6a0fc9 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(128, 19, 230, 0.35);
    }
    
    .btn:active {
        transform: translateY(0);
    }
    
    .alert {
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 24px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .alert-success {
        background-color: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    
    .alert-success a {
        color: #166534;
        font-weight: bold;
        text-decoration: underline;
    }
    
    .alert-error {
        background-color: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    
    .password-requirements {
        background: #f8fafc;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 24px;
        font-size: 14px;
        color: #64748b;
        border-left: 4px solid #8013e6;
    }
    
    .password-requirements ul {
        padding-left: 20px;
        margin-top: 12px;
    }
    
    .password-requirements li {
        margin-bottom: 8px;
        line-height: 1.5;
        transition: all 0.3s ease;
    }
    
    .password-requirements strong {
        display: block;
        margin-bottom: 8px;
        font-size: 15px;
        color: #8013e6;
    }
    
    .password-strength {
        height: 5px;
        background: #e9ecef;
        border-radius: 3px;
        margin: 10px 0;
        overflow: hidden;
    }
    
    .strength-meter {
        height: 100%;
        width: 0%;
        background: #dc3545;
        border-radius: 3px;
        transition: all 0.3s ease;
    }
    
    .strength-text {
        font-size: 13px;
        text-align: right;
        color: #666;
        margin-bottom: 15px;
    }
    
    @media (max-width: 576px) {
        .reset-container {
            border-radius: 12px;
        }
        
        .reset-body {
            padding: 25px;
        }
        
        .reset-header {
            padding: 25px 20px;
        }
        
        .reset-header h1 {
            font-size: 28px;
        }
    }
</style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <h1>Create New Password</h1>
            <p>Enter your new password for <?php echo htmlspecialchars($email); ?></p>
        </div>
        
        <div class="reset-body">
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success_message; ?>
                    <p style="margin-top: 10px;"><a href="signin.php" style="color: #155724; font-weight: bold;">Click here to login</a></p>
                </div>
            <?php else: ?>
            <div class="password-requirements">
                <strong>Password requirements:</strong>
                <ul>
                    <li>At least 8 characters long</li>
                    <li>Include uppercase and lowercase letters</li>
                    <li>Include numbers and/or special characters</li>
                </ul>
            </div>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="password">New Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter new password" required>
                    </div>
                    <div class="password-strength">
                        <div class="strength-meter" id="strength-meter"></div>
                    </div>
                    <div class="strength-text" id="strength-text">Password strength: Very Weak</div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>
                    </div>
                    <div id="password-match" style="font-size: 13px; margin-top: 5px;"></div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Reset Password
                    </button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirm_password');
        const strengthMeter = document.getElementById('strength-meter');
        const strengthText = document.getElementById('strength-text');
        const passwordMatch = document.getElementById('password-match');
        const requirements = document.querySelector('.password-requirements');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Check length
            if (password.length >= 8) strength++;
            
            // Check for uppercase and lowercase
            if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) strength++;
            
            // Check for numbers
            if (password.match(/([0-9])/)) strength++;
            
            // Check for special characters
            if (password.match(/([!,@,#,$,%,^,&,*,?,_,~])/)) strength++;
            
            // Update strength meter
            const strengthPercent = strength * 25;
            strengthMeter.style.width = strengthPercent + '%';
            
            // Update strength text and color
            let strengthLabel = '';
            let strengthColor = '';
            
            switch(strength) {
                case 0:
                case 1:
                    strengthLabel = 'Very Weak';
                    strengthColor = '#dc3545';
                    break;
                case 2:
                    strengthLabel = 'Weak';
                    strengthColor = '#fd7e14';
                    break;
                case 3:
                    strengthLabel = 'Good';
                    strengthColor = '#ffc107';
                    break;
                case 4:
                    strengthLabel = 'Strong';
                    strengthColor = '#28a745';
                    break;
            }
            
            strengthText.textContent = 'Password strength: ' + strengthLabel;
            strengthText.style.color = strengthColor;
            strengthMeter.style.backgroundColor = strengthColor;
            
            // Update requirements display
            const items = requirements.querySelectorAll('li');
            items.forEach((item, index) => {
                if (index < strength) {
                    item.style.color = '#28a745';
                    item.style.fontWeight = 'bold';
                } else {
                    item.style.color = '#666';
                    item.style.fontWeight = 'normal';
                }
            });
        });
        
        // Password match validation
        confirmInput.addEventListener('input', function() {
            if (passwordInput.value !== confirmInput.value) {
                passwordMatch.textContent = 'Passwords do not match';
                passwordMatch.style.color = '#dc3545';
            } else {
                passwordMatch.textContent = 'Passwords match';
                passwordMatch.style.color = '#28a745';
            }
        });
    </script>
</body>
</html>