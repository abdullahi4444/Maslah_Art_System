<?php
// sidebar.php - Sidebar component for user account area

// Check if session is started, if not start it
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize variables
$username = 'User';
$email = '';
$profile_image = 'assets/image/icon.png';

// Get user data from database if user is logged in
if (isset($_SESSION["id"])) {
    try {
        // Database connection - Update these credentials with your actual database info
        $host = 'localhost';
        $dbname = 'maslax_arts';
        $user = 'root';  // Use your actual MySQL username
        $pass = '';      // Use your actual MySQL password
        
        $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $db->prepare("SELECT username, email, profile_image FROM users WHERE id = :id");
        $stmt->execute([':id' => $_SESSION["id"]]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $username = $user['username'] ?? 'User';
            $email = $user['email'] ?? '';
            $profile_image = $user['profile_image'] ?? 'assets/image/icon.png';
            
            // Update session variables
            $_SESSION["username"] = $username;
            $_SESSION["email"] = $email;
            $_SESSION["profile_image"] = $profile_image;
        }
    } catch (PDOException $e) {
        // Log error but don't break the page
        error_log("Database error in sidebar: " . $e->getMessage());
        
        // Fallback to session data if database query fails
        if (isset($_SESSION["username"])) {
            $username = $_SESSION["username"];
        }
        if (isset($_SESSION["email"])) {
            $email = $_SESSION["email"];
        }
        if (isset($_SESSION["profile_image"])) {
            $profile_image = $_SESSION["profile_image"];
        }
    }
}

// Determine which page is active
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - Maslah Arts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Fixed Sidebar Styles */
        .acc-sidebar {
            width: 296px;
            margin-left: 20px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            min-height: 80vh;
            position: sticky;
            top: 15%;
            align-self: flex-start;
            overflow-y: auto;
        }

        .acc-user-card {
            background: linear-gradient(135deg, #8013e6 0%, #6a0fc9 100%);
            border-radius: 12px;
            padding: 20px;
            color: white;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .acc-user-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            overflow: hidden;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .acc-user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .acc-user-name {
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .acc-user-email {
            font-size: 14px;
            opacity: 0.9;
            word-break: break-all;
            margin-top: 10px;
            padding: 5px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            display: inline-block;
            max-width: 100%;
            overflow-wrap: break-word;
        }

        .acc-nav-menu {
            top: 17px;
            margin-top: -15px;
            flex: 1;
            border-radius: 12px;
            background: #f8f9fa;
            margin-bottom: 45px;
            list-style: none;
            display: flex;
            flex-direction: column;
            padding: 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .acc-nav-item {
            margin-bottom: 1px;
        }

        .ui{
            display: flex;
            flex-direction: column;
            height: 100%;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .acc-nav-link {
            font-weight: 500;
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #555;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
        }

        .acc-nav-link:hover {
            color: white;
            background: #8013e6;
            border-left: 4px solid #c083fa;
            border-right: 4px solid #c083fa;
        }

        .acc-nav-link.acc-active {
            border-left: 4px solid #c083fa;
            border-right: 4px solid #c083fa;
            background: #8013e6;
            color: white;
        }

        .acc-nav-link i {
            font-weight: 500;
            margin-right: 12px;
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        .acc-logout-link {
            background: rgba(235, 77, 77, 0.1);
            color: #e41313;
            margin-top: auto;
        }

        .acc-logout-link:hover {
            color: white;
            background: #eb2d2d;
            border-left: 4px solid #ff6b6b;
            border-right: 4px solid #ff6b6b;
        }

        /* Responsive adjustments for sidebar */
        @media (max-width: 1024px) {
            .acc-sidebar {
                width: 250px;
            }
        }

        @media (max-width: 768px) {
            .acc-sidebar {
                width: 200px;
                padding: 15px;
            }
            
            .acc-user-card {
                padding: 15px;
            }
            
            .acc-user-avatar {
                width: 60px;
                height: 60px;
            }
        }

        @media (max-width: 480px) {
            .acc-sidebar {
                width: 100%;
                position: relative;
                min-height: auto;
            }
        }
    </style>
</head>
<body>
<!-- Sidebar - Always Visible -->
<div class="acc-sidebar">
    <!-- User Profile Card -->
    <div class="acc-user-card">
        <div class="acc-user-avatar">
            <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="User Avatar" onerror="this.src='assets/image/icon.png'">
        </div>
        <div class="acc-user-name"><?php echo htmlspecialchars($username); ?></div>
        <div class="acc-user-email"><?php echo htmlspecialchars($email); ?></div>
    </div>

    <!-- Navigation Menu -->
    <ul class="acc-nav-menu">
        <div class="ui">
            <li class="acc-nav-item">
                <a href="std-courses.php" class="acc-nav-link <?php echo ($current_page == 'std-courses.php') ? 'acc-active' : ''; ?>">
                    <i class="ri-book-line"></i>
                    My courses
                    <span class="acc-nav-badge"></span>
                </a>
            </li>
            <li class="acc-nav-item">
                <a href="std-favorites.php" class="acc-nav-link <?php echo ($current_page == 'std-favorites.php' || $current_page == 'favorites.php') ? 'acc-active' : ''; ?>">
                    <i class="ri-heart-line"></i>
                    Favorites
                    <span class="acc-nav-badge"></span>
                </a>
            </li>
            <li class="acc-nav-item">
                <a href="std-profile.php" class="acc-nav-link <?php echo ($current_page == 'std-profile.php') ? 'acc-active' : ''; ?>">
                    <i class="ri-user-line"></i>
                    Profile
                </a>
            </li>
           <li class="acc-nav-item">
                <a href="std-account.php" class="acc-nav-link <?php echo ($current_page == 'std-account.php') ? 'acc-active' : ''; ?>">
                    <i class="ri-settings-3-line"></i>
                    Account
                </a>
            </li>

            <li class="acc-nav-item">
                <a href="logout.php" class="acc-nav-link acc-logout-link">
                    <i class="ri-logout-box-line"></i>
                    Logout
                </a>
            </li>
        </div>   
    </ul>
</div>

</body>
</html>