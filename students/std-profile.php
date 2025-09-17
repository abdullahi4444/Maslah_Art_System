<?php
session_start();

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: signin.php");
    exit;
}

// Include database configuration
require_once '../admin/config.php';

// Initialize database connection
try {
    // Check if config.php defines database constants
    if (defined('DB_SERVER') && defined('DB_NAME') && defined('DB_USERNAME') && defined('DB_PASSWORD')) {
        // Use constants from config.php
        $db = new PDO("mysql:host=" . "DB_SERVER" . ";dbname=" . DB_NAME, "DB_USERNAME", "DB_PASSWORD");
    } else {
        // Fallback: Use direct database credentials (replace with your actual credentials)
        $db = new PDO("mysql:host=localhost;dbname=maslax_arts", "root", "");
    }
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Initialize variables with default values
$username = $_SESSION["username"] ?? '';
$user_id = $_SESSION["id"] ?? '';
$first_name = '';
$last_name = '';
$email = '';
$bio = '';
$twitter_link = '';
$facebook_link = '';
$linkedin_link = '';
$profile_image = 'assets/image/icon.png';
$success_message = '';
$error_message = '';

// Get user details from database
if (!empty($user_id)) {
    try {
        // First, check if social media columns exist in user_profiles table
        $check_columns = $db->prepare("SHOW COLUMNS FROM user_profiles LIKE 'twitter_link'");
        $check_columns->execute();
        $columns_exist = $check_columns->fetch();
        
        if ($columns_exist) {
            // Columns exist, use the full query
            $stmt = $db->prepare("
                SELECT u.email, u.profile_image, 
                       up.first_name, up.last_name, up.bio, 
                       up.twitter_link, up.facebook_link, up.linkedin_link 
                FROM users u 
                LEFT JOIN user_profiles up ON u.id = up.user_id 
                WHERE u.id = :id
            ");
        } else {
            // Columns don't exist, use basic query
            $stmt = $db->prepare("
                SELECT u.email, u.profile_image, 
                       up.first_name, up.last_name, up.bio
                FROM users u 
                LEFT JOIN user_profiles up ON u.id = up.user_id 
                WHERE u.id = :id
            ");
        }
        
        $stmt->execute([':id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $email = $user['email'] ?? '';
            $first_name = $user['first_name'] ?? '';
            $last_name = $user['last_name'] ?? '';
            $bio = $user['bio'] ?? '';
            $twitter_link = $user['twitter_link'] ?? '';
            $facebook_link = $user['facebook_link'] ?? '';
            $linkedin_link = $user['linkedin_link'] ?? '';
            $profile_image = $user['profile_image'] ?? 'assets/image/icon.png';
            
            // Ensure profile_image has a default value if empty
            if (empty($profile_image)) {
                $profile_image = 'assets/image/icon.png';
            }
        }
    } catch (PDOException $e) {
        $error_message = "Database error: " . $e->getMessage();
    }
}

// Handle profile photo upload first
if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = $_FILES['profile_photo']['type'];
    
    if (in_array($file_type, $allowed_types)) {
        if ($_FILES['profile_photo']['size'] <= 3 * 1024 * 1024) { // 3MB limit
            $upload_dir = 'assets/uploads/profiles/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
            $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
            $destination = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $destination)) {
                // Update database with new profile image path
                try {
                    $update_stmt = $db->prepare("UPDATE users SET profile_image = :profile_image WHERE id = :id");
                    $update_stmt->execute([
                        ':profile_image' => $destination,
                        ':id' => $user_id
                    ]);
                    
                    $profile_image = $destination;
                    $success_message = "Profile photo updated successfully!";
                } catch (PDOException $e) {
                    $error_message = "Database error: " . $e->getMessage();
                }
            } else {
                $error_message = "Failed to upload profile photo.";
            }
        } else {
            $error_message = "File size too large. Maximum size is 3MB.";
        }
    } else {
        $error_message = "Invalid file type. Only JPG, PNG and GIF files are allowed.";
    }
}

// Handle form submission for profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['first_name'])) {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $twitter_link = trim($_POST['twitter_link'] ?? '');
    $facebook_link = trim($_POST['facebook_link'] ?? '');
    $linkedin_link = trim($_POST['linkedin_link'] ?? '');
    
    // Validate inputs
    $is_valid = true;
    
    if (empty($first_name)) {
        $error_message = "First name is required.";
        $is_valid = false;
    }
    
    if (empty($last_name)) {
        if (!empty($error_message)) $error_message .= "<br>";
        $error_message .= "Last name is required.";
        $is_valid = false;
    }
    
    // Validate URLs if provided
    if (!empty($twitter_link) && !filter_var($twitter_link, FILTER_VALIDATE_URL)) {
        if (!empty($error_message)) $error_message .= "<br>";
        $error_message .= "Twitter link is not a valid URL.";
        $is_valid = false;
    }
    
    if (!empty($facebook_link) && !filter_var($facebook_link, FILTER_VALIDATE_URL)) {
        if (!empty($error_message)) $error_message .= "<br>";
        $error_message .= "Facebook link is not a valid URL.";
        $is_valid = false;
    }
    
    if (!empty($linkedin_link) && !filter_var($linkedin_link, FILTER_VALIDATE_URL)) {
        if (!empty($error_message)) $error_message .= "<br>";
        $error_message .= "LinkedIn link is not a valid URL.";
        $is_valid = false;
    }
    
    if ($is_valid) {
        try {
            // First check if user profile exists
            $check_stmt = $db->prepare("SELECT COUNT(*) FROM user_profiles WHERE user_id = :id");
            $check_stmt->execute([':id' => $user_id]);
            $profile_exists = $check_stmt->fetchColumn();
            
            // Check if social media columns exist
            $check_columns = $db->prepare("SHOW COLUMNS FROM user_profiles LIKE 'twitter_link'");
            $check_columns->execute();
            $columns_exist = $check_columns->fetch();
            
            if ($columns_exist) {
                // Social media columns exist
                if ($profile_exists) {
                    // Update existing profile with social links
                    $update_stmt = $db->prepare("
                        UPDATE user_profiles 
                        SET first_name = :first_name, last_name = :last_name, bio = :bio, 
                            twitter_link = :twitter_link, facebook_link = :facebook_link, 
                            linkedin_link = :linkedin_link 
                        WHERE user_id = :id
                    ");
                } else {
                    // Insert new profile with social links
                    $update_stmt = $db->prepare("
                        INSERT INTO user_profiles 
                        (user_id, first_name, last_name, bio, twitter_link, facebook_link, linkedin_link) 
                        VALUES (:id, :first_name, :last_name, :bio, :twitter_link, :facebook_link, :linkedin_link)
                    ");
                }
                
                $update_stmt->execute([
                    ':first_name' => $first_name,
                    ':last_name' => $last_name,
                    ':bio' => $bio,
                    ':twitter_link' => $twitter_link,
                    ':facebook_link' => $facebook_link,
                    ':linkedin_link' => $linkedin_link,
                    ':id' => $user_id
                ]);
            } else {
                // Social media columns don't exist
                if ($profile_exists) {
                    // Update existing profile without social links
                    $update_stmt = $db->prepare("
                        UPDATE user_profiles 
                        SET first_name = :first_name, last_name = :last_name, bio = :bio
                        WHERE user_id = :id
                    ");
                } else {
                    // Insert new profile without social links
                    $update_stmt = $db->prepare("
                        INSERT INTO user_profiles 
                        (user_id, first_name, last_name, bio) 
                        VALUES (:id, :first_name, :last_name, :bio)
                    ");
                }
                
                $update_stmt->execute([
                    ':first_name' => $first_name,
                    ':last_name' => $last_name,
                    ':bio' => $bio,
                    ':id' => $user_id
                ]);
            }
            
            // Only set success message if not already set by photo upload
            if (empty($success_message)) {
                $success_message = "Profile updated successfully!";
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
    <title>Profile - Maslah Arts</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

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
            background-color: #d5b0f7;
            color: #333;
            line-height: 1.6;
        }

        .std-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Desktop Header */
        .std-main-header {
            display: block;
        }

        .std-header {
        background: radial-gradient(circle at center, rgba(221, 11, 225, 0.42), rgba(0, 0, 0, 0.9)),
                            url('Assets/image/hero.png') center/cover no-repeat;
                padding: 50px;
                height: 250px;
                color: white;
                margin-bottom: 30px;
                box-shadow: inset 0 0 100px rgba(16, 8, 8, 0.9),
                            0 10px 30px rgba(0, 0, 0, 0.3);
        }

        /* Breadcrumb container */
        .std-breadcrumb {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            font-size: 17px;
            font-weight: 500;
            font-family: "Poppins", sans-serif; /* Clean modern font */
        }

        /* Breadcrumb links */
        .std-breadcrumb a {
            color: #dcdcdc;
            text-decoration: none;
            transition: color 0.3s ease, transform 0.2s ease;
        }

        .std-breadcrumb a:hover {
            color: #4ade80; /* soft green hover */
            transform: translateY(-1px); /* subtle lift */
        }

        /* Breadcrumb separator */
        .std-breadcrumb span {
            color: #aaa;
            font-size: 18px;
        }

        /* Breadcrumb Title */
        .std-breadcrumb h1 {
            color: #fff;
            font-size: 30px;
            font-weight: 700;
            margin-left: 12px;
            margin-bottom: -25px;
            font-family: "Poppins", sans-serif;
            letter-spacing: 0.5px;
        }

        /* Page Title */
        .std-page-title {
            font-size: 30px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #fff;
            font-family: "Poppins", sans-serif;
        }


        .std-page-subtitle {
            font-size: 16px;
            opacity: 0.9;
        }

        /* Main Content Layout */
        .std-main-layout {
            display: flex;
            flex: 1;
        }

        /* Main Content Styles */
        .std-main-content {
            flex: 1;
            padding: 30px;
        }

        /* Profile Card Styles */
        .std-profile-card {
            width: 100%;
            border-radius: 12px;
            top: 30px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .std-card-header {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .std-card-title {
            font-size: 22px;
            font-weight: 600;
            color: #333;
        }

        .std-card-subtitle {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }

        /* Profile Photo Section */
        .std-profile-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .std-profile-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 20px;
        }

        .std-profile-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #7d0de6 0%, #9b4fed 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            margin-bottom: 15px;
            overflow: hidden;
        }

        .std-profile-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .std-profile-info h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .std-profile-info p {
            font-size: 14px;
            color: #666;
        }

        .std-upload-section {
            border-top: 1px solid #e9ecef;
            padding-top: 20px;
            text-align: center;
        }

        .std-file-input {
            display: none;
        }

        .std-upload-btn {
            background: linear-gradient(135deg, #8013e6 0%, #6a0fc9 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .std-upload-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .std-upload-btn i {
            margin-right: 8px;
        }

        .std-file-info {
            font-size: 13px;
            color: #666;
            margin-top: 10px;
        }

        .std-btn-primary {
            background: linear-gradient(135deg, #8013e6 0%, #6a0fc9 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        .std-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(128, 19, 230, 0.3);
        }

        .std-btn-primary i {
            margin-right: 8px;
        }

        /* Modal Styles */
        .std-modal {
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

        .std-modal-content {
            background-color: white;
            border-radius: 12px;
            padding: 30px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            position: relative;
            text-align: center;
        }

        .std-modal-success .std-modal-content {
            border-top: 5px solid #28a745;
        }

        .std-modal-error .std-modal-content {
            border-top: 5px solid #dc3545;
        }

        .std-modal-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .std-modal-success .std-modal-icon {
            color: #28a745;
        }

        .std-modal-error .std-modal-icon {
            color: #dc3545;
        }

        .std-modal-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .std-modal-message {
            margin-bottom: 25px;
            color: #666;
        }

        .std-modal-close {
            background: linear-gradient(135deg, #8013e6 0%, #6a0fc9 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
        }

        /* Form Styles */
        .std-form-container {
            background: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 25px;
        }

        .std-form-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }

        .std-form-group {
            margin-bottom: 20px;
        }

        .std-form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #555;
            margin-bottom: 8px;
        }

        .std-form-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .std-form-input:focus {
            border-color: #8013e6;
            outline: none;
        }

        .std-form-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            resize: none;
            min-height: 120px;
            transition: border-color 0.3s ease;
        }

        .std-form-textarea:focus {
            border-color: #8013e6;
            outline: none;
        }

        .std-input-error {
            border-color: #dc3545 !important;
        }

        .std-error-message {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
            display: block;
        }

        .std-save-section {
            text-align: right;
            margin-top: 20px;
        }

        /* Social Media Input Styles */
        .std-social-input-container {
            position: relative;
        }
        .std-social-input-container i {
            position: absolute;
            font-size: large;
            font-weight: 20px;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6a0fc9;
            font-size: 16px;
        }

        .std-social-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 16px;
        }

        .std-social-input {
            width: 100%;
            padding: 12px 15px 12px 40px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .std-social-input:focus {
            border-color: #8013e6;
            outline: none;
        }

        /* Grid System */
        .std-grid {
            display: grid;
            gap: 20px;
        }

        .std-grid-cols-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .std-main-layout {
                flex-direction: row;
            }
            
            .std-main-content {
                width: calc(100% - 250px);
                padding: 20px;
            }
            
            .std-header {
                padding: 30px;
            }
            
            .std-profile-card {
                padding: 20px;
            }
        }

        @media (max-width: 768px) {
            .std-header {
                padding: 20px;
            }
            
            .std-breadcrumb h1 {
                font-size: 24px;
            }
            
            .std-card-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .std-card-title {
                font-size: 20px;
            }
            
            .std-main-content {
                width: calc(100% - 200px);
            }
            
            .std-grid-cols-2 {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .std-main-layout {
                flex-direction: column;
            }
            
            .std-main-content {
                width: 100%;
                order: 2;
                padding: 15px;
            }
            
            .std-header {
                padding: 15px;
            }
            
            .std-profile-card {
                padding: 15px;
            }
            
            .std-btn-primary {
                width: 100%;
                justify-content: center;
            }
            
            .std-modal-content {
                padding: 20px;
            }
            
            .std-breadcrumb {
                font-size: 14px;
            }
            
            .std-breadcrumb h1 {
                font-size: 20px;
                margin-bottom: -20px;
            }
        }
</style>
</head>
<body>
    <div class="std-container">
        <?php include 'header.php';?>
        <!-- Desktop Header Only - Mobile Header Removed -->
        <div class="std-main-header">
            <div class="std-header">
                <div class="std-breadcrumb">
                    <span><i class="fa-solid fa-house-chimney"></i><a href="../index.php">Home</a> > Profile</span>
                </div>
                <h1 class="std-page-title">User profile</h1>
                <p class="std-page-subtitle">Manage your personal information and preferences.</p>
            </div>
        </div>

        <div class="std-main-layout">           
           <?php 
           // Check if sidebar exists in the includes directory
           $sidebar_path = './includes/sidebar.php';
           if (file_exists($sidebar_path)) {
               include $sidebar_path;
           } else {
               // Fallback to potential alternative path
               $sidebar_path_alt = 'includes/sidebar.php';
               if (file_exists($sidebar_path_alt)) {
                   include $sidebar_path_alt;
               }
           }
           ?>

            <!-- Main Content -->
            <div class="std-main-content">
                <!-- Profile Card -->
                <div class="std-profile-card">
                    <div class="std-card-header">
                        <div>
                            <h2 class="std-card-title">Profile</h2>
                            <p class="std-card-subtitle">Manage your personal information and preferences</p>
                        </div>
                    </div>

                    <!-- Profile Photo Section -->
                    <div class="std-profile-section">
                        <div class="std-profile-header">
                            <div class="std-profile-icon">
                                <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Icon" onerror="this.src='assets/image/icon.png'">
                            </div>
                            <div class="std-profile-info">
                                <h3>Profile Photo</h3>
                                <p>JPG, PNG or GIF. Max size 3MB.</p>
                            </div>
                        </div>
                        <div class="std-upload-section">
                            <form method="POST" enctype="multipart/form-data" id="std-photoForm">
                                <input type="file" name="profile_photo" id="std-profilePhoto" class="std-file-input" accept="image/jpeg,image/png,image/gif">
                                <label for="std-profilePhoto" class="std-upload-btn">
                                    <i class="ri-upload-line"></i> Upload photo
                                </label>
                            </form>
                        </div>
                    </div>           

                    <!-- Profile Details Form -->
                    <form method="POST" id="std-profileForm" enctype="multipart/form-data">
                        <!-- Profile Info Section -->
                        <div class="std-form-container">
                            <h3 class="std-form-title">Profile Info</h3>
                            
                            <div class="std-grid std-grid-cols-2">
                                <div class="std-form-group">
                                    <label class="std-form-label">First name *</label>
                                    <input type="text" name="first_name" class="std-form-input" 
                                        value="<?php echo htmlspecialchars($first_name); ?>" required>
                                </div>
                                <div class="std-form-group">
                                    <label class="std-form-label">Last name *</label>
                                    <input type="text" name="last_name" class="std-form-input"
                                        value="<?php echo htmlspecialchars($last_name); ?>" required>
                                </div>
                            </div>

                            <div class="std-form-group">
                                <label class="std-form-label">Email</label>
                                <input type="email" class="std-form-input" 
                                    value="<?php echo htmlspecialchars($email); ?>" disabled>
                            </div>

                            <div class="std-form-group">
                                <label class="std-form-label">Biography</label>
                                <textarea name="bio" class="std-form-textarea"
                                    placeholder="Tell us about yourself..."><?php echo htmlspecialchars($bio); ?></textarea>
                            </div>
                        </div>

                        <!-- Social Links Section -->
                        <div class="std-form-container">
                            <h3 class="std-form-title">Social Links</h3>
                            
                            <div class="std-form-group">
                                <label class="std-form-label">Twitter link</label>
                                <div class="std-social-input-container">
                                    <i class="ri-twitter-x-line std-social-icon"></i>
                                    <input type="url" name="twitter_link" class="std-social-input"
                                        placeholder="https://twitter.com/username" 
                                        value="<?php echo htmlspecialchars($twitter_link); ?>">
                                </div>
                            </div>

                            <div class="std-form-group">
                                <label class="std-form-label">Facebook link</label>
                                <div class="std-social-input-container">
                                    <i class="ri-facebook-line std-social-icon"></i>
                                    <input type="url" name="facebook_link" class="std-social-input"
                                        placeholder="https://facebook.com/username" 
                                        value="<?php echo htmlspecialchars($facebook_link); ?>">
                                </div>
                            </div>

                            <div class="std-form-group">
                                <label class="std-form-label">LinkedIn link</label>
                                <div class="std-social-input-container">
                                    <i class="ri-linkedin-line std-social-icon"></i>
                                    <input type="url" name="linkedin_link" class="std-social-input"
                                        placeholder="https://linkedin.com/in/username" 
                                        value="<?php echo htmlspecialchars($linkedin_link); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="std-save-section">
                            <button type="submit" class="std-btn-primary">
                                <i class="ri-save-line"></i> Save Changes
                            </button>
                        </div>
                    </form>        
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="std-modal std-modal-success" id="std-successModal">
        <div class="std-modal-content">
            <div class="std-modal-icon">
                <i class="ri-checkbox-circle-fill"></i>
            </div>
            <h3 class="std-modal-title">Success!</h3>
            <p class="std-modal-message" id="std-successMessage"></p>
            <button class="std-modal-close" onclick="stdCloseModal('std-successModal')">OK</button>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="std-modal std-modal-error" id="std-errorModal">
        <div class="std-modal-content">
            <div class="std-modal-icon">
                <i class="ri-error-warning-fill"></i>
            </div>
            <h3 class="std-modal-title">Error!</h3>
            <p class="std-modal-message" id="std-errorMessage"></p>
            <button class="std-modal-close" onclick="stdCloseModal('std-errorModal')">OK</button>
        </div>
    </div>

    <?php include '../includes/footer.php';?> 

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show modals if there are messages
            <?php if (!empty($success_message)): ?>
                stdShowModal('std-successModal', `<?php echo addslashes($success_message); ?>`);
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                stdShowModal('std-errorModal', `<?php echo addslashes($error_message); ?>`);
            <?php endif; ?>

            // Handle file input change
            const fileInput = document.getElementById('std-profilePhoto');
            
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    // Validate file before submission
                    const file = this.files[0];
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    const maxSize = 3 * 1024 * 1024; // 3MB
                    
                    if (!allowedTypes.includes(file.type)) {
                        stdShowModal('std-errorModal', 'Invalid file type. Only JPG, PNG and GIF files are allowed.');
                        this.value = '';
                        return;
                    }
                    
                    if (file.size > maxSize) {
                        stdShowModal('std-errorModal', 'File size too large. Maximum size is 3MB.');
                        this.value = '';
                        return;
                    }
                    
                    // Auto-submit the form when a file is selected
                    document.getElementById('std-photoForm').submit();
                }
            });

            // Form validation
            document.getElementById('std-profileForm').addEventListener('submit', function(e) {
                let isValid = true;
                const firstName = document.querySelector('input[name="first_name"]');
                const lastName = document.querySelector('input[name="last_name"]');
                const twitterLink = document.querySelector('input[name="twitter_link"]');
                const facebookLink = document.querySelector('input[name="facebook_link"]');
                const linkedinLink = document.querySelector('input[name="linkedin_link"]');
                
                // Clear previous error messages
                document.querySelectorAll('.std-error-message').forEach(el => el.remove());
                document.querySelectorAll('.std-input-error').forEach(el => el.classList.remove('std-input-error'));
                
                // Validate first name
                if (!firstName.value.trim()) {
                    showError(firstName, 'First name is required.');
                    isValid = false;
                }
                
                // Validate last name
                if (!lastName.value.trim()) {
                    showError(lastName, 'Last name is required.');
                    isValid = false;
                }
                
                // Validate URLs if provided
                if (twitterLink.value.trim() && !isValidUrl(twitterLink.value.trim())) {
                    showError(twitterLink, 'Please enter a valid URL for Twitter.');
                    isValid = false;
                }
                
                if (facebookLink.value.trim() && !isValidUrl(facebookLink.value.trim())) {
                    showError(facebookLink, 'Please enter a valid URL for Facebook.');
                    isValid = false;
                }
                
                if (linkedinLink.value.trim() && !isValidUrl(linkedinLink.value.trim())) {
                    showError(linkedinLink, 'Please enter a valid URL for LinkedIn.');
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                    stdShowModal('std-errorModal', 'Please fix the errors in the form.');
                }
            });

            function showError(input, message) {
                input.classList.add('std-input-error');
                const errorElement = document.createElement('span');
                errorElement.className = 'std-error-message';
                errorElement.textContent = message;
                input.parentElement.appendChild(errorElement);
            }
            
            function isValidUrl(string) {
                try {
                    new URL(string);
                    return true;
                } catch (_) {
                    return false;
                }
            }
        });

        function stdShowModal(modalId, message) {
            const modal = document.getElementById(modalId);
            const messageElement = modal.querySelector('.std-modal-message');
            messageElement.innerHTML = message;
            modal.style.display = 'flex';
        }

        function stdCloseModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('std-modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>