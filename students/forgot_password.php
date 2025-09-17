<?php
session_start();

// Database configuration constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'maslax_arts'); // Replace with your actual database name
define('DB_USER', 'root'); // Replace with your database username
define('DB_PASS', ''); // Replace with your database password

// Database connection function
function getDBConnection() {
    try {
        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $db;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

$email = '';
$error_message = '';
$success_message = '';
$show_verification = false;
$verification_code = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    
    // Validate email
    if (empty($email)) {
        $error_message = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        try {
            $db = getDBConnection();
            
            // Check if email exists in database
            $stmt = $db->prepare("SELECT id, username FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                // Generate a unique token and verification code
                $token = bin2hex(random_bytes(50));
                $verification_code = sprintf('%06d', random_int(0, 999999)); // 6-digit code
                $expires = date("Y-m-d H:i:s", strtotime("+1 minute")); // 1-minute expiry
                
                // Store token in database
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $user_id = $user['id'];
                
                // Delete any existing tokens for this user
                $delete_stmt = $db->prepare("DELETE FROM password_reset_tokens WHERE user_id = :user_id");
                $delete_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $delete_stmt->execute();
                
                // Insert new token with verification code
                $insert_stmt = $db->prepare("INSERT INTO password_reset_tokens (user_id, token, verification_code, expires_at) VALUES (:user_id, :token, :verification_code, :expires_at)");
                $insert_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $insert_stmt->bindParam(':token', $token, PDO::PARAM_STR);
                $insert_stmt->bindParam(':verification_code', $verification_code, PDO::PARAM_STR);
                $insert_stmt->bindParam(':expires_at', $expires, PDO::PARAM_STR);
                
                if ($insert_stmt->execute()) {
                    // Store email and token in session for verification step
                    $_SESSION['reset_email'] = $email;
                    $_SESSION['reset_token'] = $token;
                    
                    // For development: Show the code on the page
                    $show_verification = true;
                    
                    // Log the code for development purposes
                    $log_message = date('Y-m-d H:i:s') . " - Verification code for {$user['username']} ($email): $verification_code (Expires: $expires)\n";
                    file_put_contents('email_log.txt', $log_message, FILE_APPEND);
                    
                    // Try to send email (commented out for development)
                    /*
                    if (sendVerificationEmail($email, $verification_code, $user['username'])) {
                        $success_message = "Verification code has been sent to your email address.";
                        header("Location: verify_code.php?email=" . urlencode($email));
                        exit();
                    } else {
                        $error_message = "Failed to send verification email. Please try again.";
                    }
                    */
                    
                    $success_message = "Verification code has been generated. It will expire in 1 minute.";
                } else {
                    $error_message = "Failed to save verification code. Please try again.";
                }
            } else {
                $error_message = "No account found with that email address.";
            }
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    }
}

// Function to send actual email (for production)
function sendVerificationEmail($email, $code, $username) {
    $to = $email;
    $subject = "Password Reset Verification Code - Maslah Arts";
    $message = "
    <html>
    <head>
        <title>Password Reset Verification</title>
    </head>
    <body>
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #8013e6;'>Maslah Arts Password Reset</h2>
            <p>Hello " . htmlspecialchars($username) . ",</p>
            <p>You have requested to reset your password. Please use the following verification code to proceed:</p>
            <div style='background-color: #f0f0f0; border: 2px dashed #8013e6; border-radius: 8px; padding: 15px; text-align: center; margin: 20px 0; font-size: 24px; font-weight: bold; letter-spacing: 5px; color: #8013e6;'>
                " . $code . "
            </div>
            <p><strong>This code will expire in 1 minute for security reasons.</strong></p>
            <p>If you did not request a password reset, please ignore this email.</p>
            <br>
            <p>Best regards,<br>Maslah Arts Team</p>
        </div>
    </body>
    </html>
    ";
    
    // Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Maslah Arts <no-reply@maslaharts.com>" . "\r\n";
    $headers .= "Reply-To: support@maslaharts.com" . "\r\n";
    
    // Send email
    return mail($to, $subject, $message, $headers);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Maslah Arts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
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
        
        .forgot-container {
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
        
        .forgot-header {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80') center/cover;
            padding: 35px 30px;
            color: white;
            text-align: center;
            position: relative;
        }
        
        .forgot-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #8013e6, #6a0fc9);
        }
        
        .forgot-header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }
        
        .forgot-header p {
            font-size: 16px;
            opacity: 0.9;
            font-weight: 300;
        }
        
        .forgot-body {
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
        
        .back-to-login {
            text-align: center;
            margin-top: 25px;
        }
        
        .back-to-login a {
            color: #8013e6;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .back-to-login a:hover {
            color: #6a0fc9;
            text-decoration: underline;
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
        
        .alert-error {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .instructions {
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            color: #64748b;
            border-left: 4px solid #8013e6;
        }
        
        .instructions ol {
            padding-left: 20px;
            margin-top: 12px;
        }
        
        .instructions li {
            margin-bottom: 8px;
            line-height: 1.5;
        }
        
        .email-note {
            background: #f0f9ff;
            border-left: 4px solid #0ea5e9;
            padding: 16px;
            margin: 20px 0;
            border-radius: 8px;
            font-size: 14px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        
        .email-note i {
            color: #0ea5e9;
            font-size: 18px;
        }
        
        .verification-box {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #bae6fd;
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            margin: 25px 0;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(128, 19, 230, 0.2); }
            70% { box-shadow: 0 0 0 12px rgba(128, 19, 230, 0); }
            100% { box-shadow: 0 0 0 0 rgba(128, 19, 230, 0); }
        }
        
        .verification-code {
            font-size: 42px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #8013e6;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .verification-note {
            font-size: 15px;
            color: #64748b;
            margin-top: 15px;
            line-height: 1.5;
        }
        
        .countdown {
            font-size: 16px;
            font-weight: bold;
            color: #ef4444;
            margin: 15px 0;
        }
        
        .timer-circle {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #fef2f2;
            border: 3px solid #ef4444;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 20px;
            font-weight: bold;
            color: #ef4444;
            transition: all 0.3s;
        }
        
        .continue-btn {
            margin-top: 25px;
        }
        
        @media (max-width: 576px) {
            .forgot-container {
                border-radius: 12px;
            }
            
            .forgot-body {
                padding: 25px;
            }
            
            .verification-code {
                font-size: 32px;
                letter-spacing: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="forgot-container">
        <div class="forgot-header">
            <h1>Reset Your Password</h1>
            <p>Enter your email to receive a verification code</p>
        </div>
        
        <div class="forgot-body">
            <div class="instructions">
                <strong>How to reset your password:</strong>
                <ol>
                    <li>Enter your email address below</li>
                    <li>Check your inbox for a verification code</li>
                    <li>Enter the code on the next screen</li>
                    <li>Create your new password</li>
                </ol>
            </div>
            
            <div class="email-note">
                <i class="fas fa-info-circle"></i>
                <div>The verification code will be sent to your email address and will be valid for <strong>1 minute</strong>.</div>
            </div>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($show_verification): ?>
                <div class="verification-box">
                    <h3><i class="fas fa-key"></i> Your Verification Code</h3>
                    <div class="timer-circle">
                        <span id="countdown">60</span>s
                    </div>
                    <div class="verification-code"><?php echo $verification_code; ?></div>
                    <p class="verification-note">This code will expire in <strong id="expiry-time">1 minute</strong>.</p>
                    
                    <div class="continue-btn">
                        <a href="verify_code.php?email=<?php echo urlencode($email); ?>" class="btn">
                            <i class="fas fa-arrow-right"></i> Continue to Verification
                        </a>
                    </div>
                </div>
                
                <script>
                    // Countdown timer for verification code expiry
                    let timeLeft = 60;
                    const countdownEl = document.getElementById('countdown');
                    const expiryTimeEl = document.getElementById('expiry-time');
                    const timerCircle = document.querySelector('.timer-circle');
                    
                    const countdownInterval = setInterval(() => {
                        timeLeft--;
                        countdownEl.textContent = timeLeft;
                        
                        if (timeLeft <= 0) {
                            clearInterval(countdownInterval);
                            expiryTimeEl.textContent = "Code has expired!";
                            expiryTimeEl.style.color = "#ef4444";
                            timerCircle.style.backgroundColor = "#fee2e2";
                        } else if (timeLeft <= 10) {
                            timerCircle.style.borderColor = "#ef4444";
                            timerCircle.style.color = "#ef4444";
                            timerCircle.style.transform = "scale(1.1)";
                        }
                    }, 1000);
                </script>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email address" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn">
                            <i class="fas fa-paper-plane"></i> Send Verification Code
                        </button>
                    </div>
                </form>
            <?php endif; ?>
            
            <div class="back-to-login">
                <a href="signin.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>