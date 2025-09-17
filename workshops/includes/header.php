<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get the current page filename to determine active link
$current_page = basename($_SERVER['PHP_SELF']);

// Get user profile image if logged in
$profile_image = isset($_SESSION['profile_image']) ? $_SESSION['profile_image'] : '../students/assets/image/icon1.png';
?>

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Arial', sans-serif;
    }
    
    #navbarHeader {
        position: sticky;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
        background-color: #fff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    header.navbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px;
        padding-left: 90px;
        padding-right: 90px; 
        background-color: #fff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        position: relative;
        z-index: 1000;
        flex-wrap: wrap;
    }

    .navbar-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .navbar-left img {
        width: 40px;
        height: auto;
    }

    .navbar-left h2 {
        font-size: 22px;
        font-weight: bold;
        color: #040007ff;
    }

    .logo-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .logo-title img {
        width: 40px;
        height: 40px;
        object-fit: contain;
        border-radius: 50%;
    }

    .logo-title h2 {
        font-size: 22px;
        font-weight: bold;
        color: #040007;
        margin: 0;
    }

    .navbar-icons {
        display: none;
        align-items: center;
        gap: 10px;
    }

    .navbar-center {
        flex: 1;
    }

    .navbar-center ul {
        display: flex;
        justify-content: center;
        gap: 25px;
        list-style: none;
    }

    .navbar-center ul li a {
        position: relative;
        text-decoration: none;
        color: #333;
        font-weight: 500;
        transition: color 0.3s;
        padding-bottom: 5px;
        display: inline-block;
    }

    .navbar-center ul li a:hover,
    .navbar-center ul li a.active {
        color: #8A2BE2;
    }

    .navbar-center ul li a.active::after,
    .navbar-center ul li a:hover::after {
        content: '';
        display: block;
        height: 2px;
        background-color: #8A2BE2;
        width: 100%;
        position: absolute;
        left: 0;
        bottom: 0;
        border-radius: 2px;
    }

    .navbar-right {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .search-icon {
        background-color: #f1f1f1;
        border-radius: 50%;
        padding: 8px;
        font-size: 16px;
        color: #333;
        cursor: pointer;
    }

    .btn-login,
    .btn-signup {
        padding: 6px 14px;
        border-radius: 8px;
        font-weight: bold;
        text-decoration: none;
        transition: background-color 0.3s, color 0.3s;
    }

    .btn-login {
        border: 2px solid #8A2BE2;
        color: #8A2BE2;
        background: none;
    }

    .btn-login:hover {
        background-color: #8A2BE2;
        color: white;
    }

    .btn-signup {
        background-color: #8A2BE2;
        color: white;
        border: none;
    }

    .btn-signup:hover {
        background-color: #6a1ab8;
    }

    .menu-toggle {
        font-size: 24px;
        cursor: pointer;
        color: #8A2BE2;
    }

    /* User Avatar Styles */
    .acc-user-avatar img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #8A2BE2;
        transition: border-color 0.3s;
    }

    .acc-user-avatar img:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(138, 43, 226, 0.4);
    }

    /* Mobile User Profile */
    .mobile-user-profile {
        display: none;
        align-items: center;
        padding: 15px;
        border-top: 1px solid #eee;
        margin-top: 15px;
    }

    .mobile-user-profile img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 15px;
    }

    .mobile-user-info {
        display: flex;
        flex-direction: column;
    }

    .mobile-user-name {
        font-weight: bold;
        color: #333;
    }

    .mobile-user-email {
        font-size: 14px;
        color: #666;
    }

    /* Backdrop menu */
    .navbar-center.fullscreen-menu {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        height: calc(100vh - 70px);
        background: linear-gradient(to bottom right, #f9f9ff, #ece6ff);
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 40px 0;
        z-index: 999;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
        animation: slideDown 0.3s ease forwards;
    }

    /* Animation for opening */
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ========== Responsive ========== */
    @media (max-width: 1150px) {
        header.navbar {
            flex-direction: column;
            align-items: stretch;
            padding: 15px 30px;
        }

        .navbar-left {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-left .logo-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-icons {
            display: flex;
        }

        .navbar-center {
            width: 100%;
            display: none;
            flex-direction: column;
            background-color: #fff;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-center.active {
            margin-top: -20px;
            display: flex;
        }

        .navbar-center ul {
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .navbar-right.desktop-only {
            display: none;
        }

        .navbar-right.mobile-only {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 10px;
        }

        .search-icon.mobile-only {
            display: inline-block;
        }

        .search-icon.desktop-only {
            display: none;
        }
        
        /* Show mobile user profile when menu is active and user is logged in */
        .navbar-center.active .mobile-user-profile {
            display: flex;
        }
        
        /* Hide desktop avatar on mobile */
        .acc-user-avatar.desktop-only {
            display: none;
        }
    }

    @media (min-width: 1150px) {
        .search-icon.mobile-only,
        .navbar-right.mobile-only {
            display: none !important;
        }

        .search-icon.desktop-only {
            display: inline-block;
        }

        .menu-toggle {
            display: none;
        }
        
        /* Hide mobile user profile on desktop */
        .mobile-user-profile {
            display: none !important;
        }
        
        /* Show desktop avatar */
        .acc-user-avatar.desktop-only {
            display: block;
        }
    }
</style>

<!-- ====== NAVBAR START ====== -->
<header class="navbar" id="navbarHeader">
    <!-- Left Side: Logo + Title + Mobile Icons -->
    <div class="navbar-left">
        <div class="logo-title">
            <img src="../logo.jpg" alt="Logo">
            <h2>Maslax Arts</h2>
        </div>
        <!-- Mobile Icons: Search First, Then Menu -->
        <div class="navbar-icons">
            <span class="search-icon mobile-only"><i class="fas fa-search"></i></span>
            <div class="menu-toggle" onclick="toggleMenu(this)">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </div>

    <!-- Center: Links -->
    <nav class="navbar-center" id="mainMenu">
        <ul>
            <li><a href="../index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a></li>
            <li><a href="../blogs/news.php" class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'blogs/') !== false || $current_page == 'news.php') ? 'active' : ''; ?>">Blogs</a></li>
            <li><a href="../gallery/maslah_gallery.php" class="<?php echo ($current_page == 'maslah_gallery.php' || $current_page == 'student_gallery.php') ? 'active' : ''; ?>">Gallery</a></li>
            <li><a href="workshops.php" class="<?php echo ($current_page == 'workshops.php') ? 'active' : ''; ?>">Workshops</a></li>
            <li><a href="../courses/courses.php" class="<?php echo ($current_page == 'courses.php') ? 'active' : ''; ?>">Courses</a></li>
            <li><a href="../events/event.php" class="<?php echo ($current_page == 'event.php') ? 'active' : ''; ?>">Event</a></li>
            <li><a href="../about/about.php" class="<?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">About</a></li>
            <li><a href="../contact/contact.php" class="<?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">Contact</a></li>
        </ul>

        <!-- Mobile User Profile (shown only when logged in) -->
        <?php if (isset($_SESSION['username'])): ?>
        <div class="mobile-user-profile">
            <a href="../students/std-account.php">
                <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="User Avatar" onerror="this.src='../students/assets/image/icon1.png'">
            </a>
            <div class="mobile-user-info">
                <span class="mobile-user-name"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <span class="mobile-user-email"><?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Mobile Right Side -->
        <div class="navbar-right mobile-only">
            <?php if (isset($_SESSION['username'])): ?>
                <a href="../students/logout.php" class="btn-login">Logout</a>
            <?php else: ?>
                <a href="../students/signin.php" class="btn-login">Login</a>
                <a href="../students/signUp.php" class="btn-signup">Signup</a>
            <?php endif; ?>
        </div>
        
        
    </nav>

    <!-- Desktop Right Side -->
    <div class="navbar-right desktop-only">
        <span class="search-icon desktop-only"><i class="fas fa-search"></i></span>
        <?php if (isset($_SESSION['username'])): ?>            
            <a href="../students/logout.php" class="btn-login">Logout</a>
            <div class="acc-user-avatar desktop-only">
                <a href="../students/std-account.php" class="<?php echo ($current_page == 'std-account.php') ? 'active' : ''; ?>">
                    <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="User Avatar" onerror="this.src='../students/assets/image/icon1.png'">
                </a>
            </div>
        <?php else: ?>
            <a href="../students/signin.php" class="btn-login">Login</a>
            <a href="../students/signUp.php" class="btn-signup">Signup</a>
        <?php endif; ?>
    </div>
</header>
<!-- ====== NAVBAR END ====== -->

<!-- Toggle Script -->
<script>
    function toggleMenu(element) {
        const nav = document.getElementById('mainMenu');
        const header = document.getElementById('navbarHeader');
        const icon = element.querySelector('i');

        // Toggling classes
        nav.classList.toggle('active');
        nav.classList.toggle('fullscreen-menu');
        header.classList.toggle('show');

        // Change icon
        icon.classList.toggle('fa-bars');
        icon.classList.toggle('fa-times');
    }
</script>