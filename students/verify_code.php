<?php
session_start();
require_once '../admin/config.php';

$email = isset($_GET['email']) ? $_GET['email'] : '';
$error_message = '';
$success_message = '';

// Check if email is in session
if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_token'])) {
    header("Location: forgot_password.php");
    exit();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $verification_code = trim($_POST['verification_code']);
    
    // Validate verification code
    if (empty($verification_code)) {
        $error_message = "Please enter the verification code.";
    } elseif (!preg_match('/^\d{6}$/', $verification_code)) {
        $error_message = "Please enter a valid 6-digit code.";
    } else {
        try {
            // Check if constants are defined, otherwise use direct values
            $host = defined('DB_HOST') ? DB_HOST : 'localhost';
            $user = defined('DB_USER') ? DB_USER : 'root';
            $pass = defined('DB_PASS') ? DB_PASS : '';
            $dbname = 'maslax_arts';
            
            // Create database connection
            $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $email = $_SESSION['reset_email'];
            
            // First, let's check what's actually in the database for this user
            $checkStmt = $db->prepare("SELECT * FROM password_reset_tokens WHERE user_id = (SELECT id FROM users WHERE email = :email) ORDER BY created_at DESC LIMIT 1");
            $checkStmt->bindParam(':email', $email, PDO::PARAM_STR);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                $tokenData = $checkStmt->fetch(PDO::FETCH_ASSOC);
                
                // Debug information (you can remove this in production)
                error_log("Stored code in DB: " . $tokenData['verification_code']);
                error_log("Entered code: " . $verification_code);
                error_log("Expires at: " . $tokenData['expires_at']);
                error_log("Current time: " . date('Y-m-d H:i:s'));
                
                // Check if the token has expired
                if (strtotime($tokenData['expires_at']) < time()) {
                    $error_message = "Verification code has expired. Please request a new one.";
                } 
                // Check if the verification code matches
                else if ($tokenData['verification_code'] !== $verification_code) {
                    $error_message = "Invalid verification code. Please check and try again.";
                } else {
                    // Code is valid, redirect to password reset page
                    $_SESSION['code_verified'] = true;
                    header("Location: reset_password.php");
                    exit();
                }
            } else {
                $error_message = "No verification code found for this email. Please request a new code.";
            }
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
            error_log("Database error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code - Maslah Arts</title>
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
    
    .verify-container {
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
    
    .verify-header {
        background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?ixlib=rb-4.0.3&ixid=M3wxMj极速3DA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80') center/cover;
        padding: 35px 30px;
        color: white;
        text-align: center;
        position: relative;
    }
    
    .verify-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: linear-gradient(90deg, #8013e6, #6a0fc9);
    }
    
    .verify-header h1 {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 10px;
        letter-spacing: 0.5px;
    }
    
    .verify-header p {
        font-size: 16px;
        opacity: 0.9;
        font-weight: 300;
    }
    
    .verify-body {
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
        font-size: 20px;
    }
    
    .form-control {
        width: 100%;
        padding: 16px 20px 16px 50px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        font-size: 24px;
        transition: all 0.3s;
        position: relative;
        z-index: 1;
        text-align: center;
        letter-spacing: 8px;
        font-weight: bold;
        color: #8013e6;
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
    
    .back-to-forgot {
        text-align: center;
        margin-top: 25px;
    }
    
    .back-to-forgot a {
        color: #8013e6;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .back-to-forgot a:hover {
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
        line-height: 1.6;
    }
    
    .email-display {
        background: #f0f9ff;
        border-left: 4px solid #0ea5e9;
        padding: 16px;
        margin: 20px 0;
        border-radius: 8px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .email-display i {
        color: #0ea5e9;
        font-size: 18px;
    }
    
    .resend-code {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
        color: #64748b;
    }
    
    .resend-code a {
        color: #8013e6;
        text-decoration: none;
        font-weight: 500;
    }
    
    .resend-code a:hover {
        text-decoration: underline;
    }
    
    .code-inputs {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }
    
    .code-input {
        width: 50px;
        height: 60px;
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        transition: all 0.3s;
    }
    
    .code-input:focus {
        outline: none;
        border-color: #8013e6;
        box-shadow: 0 0 0 3px rgba(128, 19, 230, 0.15);
    }
    
    @media (max-width: 576px) {
        .verify-container {
            border-radius: 12px;
        }
        
        .verify-body {
            padding: 25px;
        }
        
        .code-input {
            width: 40px;
            height: 50px;
            font-size: 20px;
        }
        
        .form-control {
            font-size: 20px;
            letter-spacing: 5px;
        }
    }
</style>
</head>
<body>
    <div class="verify-container">
        <div class="verify-header">
            <h1>Verify Your Code</h1>
            <p>Enter the 6-digit code sent to <?php echo htmlspecialchars($email); ?></p>
        </div>
        
        <div class="verify-body">
            <div class="instructions">
                <strong>Check your email for a 6-digit verification code.</strong>
                <p>Enter the code below to continue with your password reset.</p>
            </div>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="verification_code">Verification Code</label>
                    <div class="input-group">
                        <i class="fas fa-key"></i>
                        <input type="text" id="verification_code" name="verification_code" class="form-control" placeholder="000000" maxlength="6" pattern="\d{6}" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">
                        <i class="fas fa-check-circle"></i> Verify Code
                    </button>
                </div>
            </form>
            
            <div class="back-to-forgot">
                <a href="forgot_password.php"><i class="fas fa-arrow-left"></i> Use a different email</a>
            </div>
        </div>
    </div>

    <script>
        // Auto-tab between code digits
        document.getElementById('verification_code').addEventListener('input', function(e) {
            if (this.value.length === 6) {
                this.form.submit();
            }
        });
        
        // Focus on input when page loads
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('verification_code').focus();
        });
    </script>
</body>
</html>