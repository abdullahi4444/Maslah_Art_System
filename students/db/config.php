<?php
/**
 * Maslah Arts - Configuration File
 * Database connection settings and application configuration
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'maslah_arts');
define('DB_USER', 'maslah_arts_user');
define('DB_PASS', 'SecurePass123!');
define('DB_CHARSET', 'utf8mb4');

// Application configuration
define('SITE_NAME', 'Maslah Arts');
define('BASE_URL', 'http://localhost/Students/');
define('DEFAULT_TIMEZONE', 'Africa/Mogadishu');

// Security settings
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 15); // minutes
define('PASSWORD_RESET_EXPIRE', 60); // minutes
define('SESSION_TIMEOUT', 60); // minutes

// Error reporting (set to 0 in production)
define('DEBUG_MODE', 1);

// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400, // 1 day
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']), // Use secure cookies if HTTPS
        'httponly' => true, // Prevent JavaScript access
        'samesite' => 'Strict' // CSRF protection
    ]);
    session_start();
}

// Set default timezone
date_default_timezone_set(DEFAULT_TIMEZONE);

// Error reporting settings
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Database connection function
function getDBConnection() {
    static $connection = null;
    
    if ($connection === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $connection = new PDO($dsn, DB_USER, DB_PASS);
            
            // Set PDO attributes
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $connection->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
            
        } catch (PDOException $e) {
            // Log error and display user-friendly message
            error_log("Database connection failed: " . $e->getMessage());
            
            if (DEBUG_MODE) {
                die("Database connection error: " . $e->getMessage());
            } else {
                die("Sorry, we are experiencing technical difficulties. Please try again later.");
            }
        }
    }
    
    return $connection;
}

// Utility function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Utility function to redirect users
function redirect($url) {
    header("Location: " . $url);
    exit;
}

// CSRF protection functions
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Password hashing function
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
}

// Input sanitization function
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Get client IP address
function getClientIP() {
    $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
}

// Check if request is AJAX
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

// JSON response helper
function jsonResponse($success, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Log activity function
function logActivity($userId, $action, $details = '') {
    try {
        $db = getDBConnection();
        $stmt = $db->prepare("INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (:user_id, :action, :details, :ip_address)");
        $stmt->execute([
            ':user_id' => $userId,
            ':action' => $action,
            ':details' => $details,
            ':ip_address' => getClientIP()
        ]);
    } catch (PDOException $e) {
        error_log("Activity logging failed: " . $e->getMessage());
    }
}
?>