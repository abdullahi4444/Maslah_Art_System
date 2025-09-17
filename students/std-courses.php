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
        // Use constants from config.php - FIXED: Removed quotes around constants
        $db = new PDO("mysql:host=" . "DB_SERVER" . ";dbname=" . DB_NAME, "DB_USERNAME", "DB_PASSWORD");
    } else {
        // Fallback: Use direct database credentials
        $db = new PDO("mysql:host=localhost;dbname=maslax_arts", "root", "");
    }
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Initialize variables with default values
$username = $_SESSION["username"] ?? '';
$user_id = $_SESSION["id"] ?? '';
$email = '';
$join_date = '';
$profile_image = 'assets/image/icon.png';
$success_message = '';
$error_message = '';

// Get user details from database
if (!empty($user_id)) {
    try {
        $stmt = $db->prepare("SELECT email, created_at, profile_image FROM users WHERE id = :id");
        $stmt->execute([':id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $email = $user['email'] ?? '';
            $join_date = $user['created_at'] ?? '';
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

// Get courses from database that are created by admins and active
$courses = [];
try {
    $stmt = $db->prepare("
        SELECT c.course_id, c.title, c.short_description, c.full_description, 
               c.cover_image, c.duration_hours, c.level, c.price, c.status,
               cc.title as category_name, a.first_name, a.last_name
        FROM courses c
        LEFT JOIN course_categories cc ON c.category_id = cc.category_id
        LEFT JOIN admins a ON c.admin_id = a.admin_id  -- Changed from created_by to admin_id
        WHERE c.status = 1
        ORDER BY c.created_at DESC
    ");
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Database error loading courses: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - Maslah Arts</title>
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
                padding: 30px 20px;
                height: auto;
                color: white;
                margin-bottom: 20px;
                box-shadow: inset 0 0 100px rgba(16, 8, 8, 0.9),
                            0 10px 30px rgba(0, 0, 0, 0.3);
            }

            /* Breadcrumb container */
            .acc-breadcrumb {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 20px;
                font-size: 15px;
                font-weight: 500;
                font-family: "Poppins", sans-serif;
            }

            .acc-breadcrumb a {
                color: #e5e5e5;
                text-decoration: none;
                transition: color 0.3s ease, transform 0.2s ease;
            }

            .acc-breadcrumb a:hover{
                color: #4ade80;
                transform: translateY(-1px);
            }

            .acc-breadcrumb span {
                color: #aaa;
                font-size: 18px;
            }

            .acc-breadcrumb h1 {
                color: #fff;
                font-size: 28px;
                font-weight: 700;
                margin-left: 10px;
                font-family: "Poppins", sans-serif;
                letter-spacing: 0.5px;
            }

            .acc-page-title {
                font-size: 26px;
                font-weight: 700;
                margin-bottom: 15px;
                color: #fff;
                font-family: "Poppins", sans-serif;
            }

            .acc-page-subtitle {
                font-size: 14px;
                opacity: 0.9;
            }

            /* Main Content Layout */
            .acc-main-layout {
                display: flex;
                flex: 1;
            }    

            /* Course Cards Styles */
            .course-container {
                flex: 1;
                padding: 15px;
                overflow-y: auto;
                margin-bottom: 20px;
                margin-top: 0;
                border-radius: 12px;
                min-height: 0;
            }
            
            .course-header {
                background-color: white;
                padding: 1rem;
                border-radius: 12px;
                margin-bottom: 1.5rem;
                text-align: center;
            }
            
            .course-header h1 {
                font-size: 1.8rem;
                font-weight: 700;
                color: #2d3748;
                margin-bottom: 0.5rem;
            }
            
            .course-header p {
                color: #718096;
                font-size: 1rem;
            }
            
            .course-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            @media (min-width: 768px) {
                .course-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }
            
            @media (min-width: 1024px) {
                .course-grid {
                    grid-template-columns: repeat(3, 1fr);
                }
                
                .acc-header {
                    padding: 50px;
                    height: 250px;
                }
                
                .acc-breadcrumb {
                    font-size: 17px;
                }
                
                .acc-breadcrumb h1 {
                    font-size: 28px;
                    margin-bottom: -30px;
                }
                
                .acc-page-title {
                    font-size: 28px;
                }
                
                .acc-page-subtitle {
                    font-size: 16px;
                }
                
                .course-container {
                    padding: 20px 40px;
                    margin-bottom: 30px;
                    margin-top: -20px;
                }
                
                .course-header {
                    padding: 1.5rem;
                    text-align: left;
                }
                
                .course-header h1 {
                    font-size: 2.5rem;
                }
                
                .course-header p {
                    font-size: 1.1rem;
                }
            }
            
            .course-card {
                width: 100%;
                background: white;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            
            .course-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            }
            
            .course-image {
                position: relative;
                width: 100%;
                height: 200px;
                overflow: hidden;
            }
            
            .course-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                object-position: top;
            }
            
            .course-menu {
                position: absolute;
                top: 0.5rem;
                right: 0.5rem;
                z-index: 10;
            }
            
            .menu-btn {
                width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 0% 12px 0% 12px;
                background-color: #8013e6;
                color: white;
                border: none;
                cursor: pointer;
                transition: background 0.2s ease;
            }
            
            .menu-btn:hover {
                background-color: #6a0fc9;
            }
            
            .menu-icon {
                color: #fefefe;
                font-size: 1.2rem;
                font-weight: bold;
            }

            .course-content {
                padding: 1.5rem;
            }
            
            .course-title {
                font-size: 1.25rem;
                font-weight: 700;
                color: #2d3748;
                margin-bottom: 1rem;
                line-height: 1.3;
            }
            
            .course-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 1rem;
                font-size: 0.9rem;
                font-weight: 500;
                color: #4a5568;
                margin-bottom: 1rem;
            }
            
            .meta-item {
                display: flex;
                align-items: center;
                gap: 0.25rem;
            }
            
            .progress-container {
                margin-bottom: 1rem;
            }
            
            .progress-info {
                display: flex;
                justify-content: space-between;
                font-size: 0.85rem;
                color: #718096;
                margin-bottom: 0.5rem;
            }
            
            .progress-bar {
                width: 100%;
                height: 8px;
                background: #e2e8f0;
                border-radius: 4px;
                overflow: hidden;
            }
            
            .progress-fill {
                height: 100%;
                border-radius: 4px;
            }
            
            .progress-primary {
                background: #080bec;
            }
            
            .progress-secondary {
                background: #02a23d;
            }
            
            .course-footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1.5rem;
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .rating {
                display: flex;
                align-items: center;
                gap: 0.3rem;
            }
            
            .stars {
                display: flex;
                color: #f59e0b;
            }
            
            .rating-text {
                font-size: 0.85rem;
                color: #718096;
            }
            
            .author {
                font-size: 0.85rem;
                color: #718096;
            }
            
            .author span {
                font-weight: 600;
                color: #2d3748;
            }
            
            .btn {
                display: block;
                width: 100%;
                padding: 0.75rem 1.5rem;
                border: none;
                border-radius: 8px;
                font-weight: 500;
                font-size: 1rem;
                cursor: pointer;
                transition: background 0.2s ease;
                text-align: center;
            }
            
            .btn-primary {
                background: #0407d5;
                color: white;
            }
            
            .btn-primary:hover {
                background: #0c01de;
            }
            
            .btn-secondary {
                background: #11c002;
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
            }
            
            .btn-secondary:hover {
                background: #0fa801;
            }
            
            .icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            /* Responsive adjustments */
            @media (max-width: 480px) {
                .course-header h1 {
                    font-size: 1.5rem;
                }
                
                .course-title {
                    font-size: 1.1rem;
                }
                
                .course-meta {
                    gap: 0.5rem;
                }
                
                .meta-item {
                    font-size: 0.8rem;
                }
                
                .course-footer {
                    flex-direction: column;
                    align-items: flex-start;
                }
            }

            /* Modal Styles */
            .acc-modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1000;
                align-items: center;
                justify-content: center;
            }
            
            .acc-modal-content {
                background: white;
                padding: 2rem;
                border-radius: 12px;
                text-align: center;
                max-width: 400px;
                width: 90%;
            }
            
            .acc-modal-icon {
                font-size: 3rem;
                margin-bottom: 1rem;
            }
            
            .acc-modal-success .acc-modal-icon {
                color: #10b981;
            }
            
            .acc-modal-error .acc-modal-icon {
                color: #ef4444;
            }
            
            .acc-modal-title {
                font-size: 1.5rem;
                font-weight: 600;
                margin-bottom: 0.5rem;
            }
            
            .acc-modal-message {
                margin-bottom: 1.5rem;
                color: #6b7280;
            }
            
            .acc-modal-close {
                background: #3b82f6;
                color: white;
                border: none;
                padding: 0.5rem 1.5rem;
                border-radius: 6px;
                cursor: pointer;
                font-weight: 500;
            }
            
            .acc-modal-close:hover {
                background: #2563eb;
            }
            
            .no-courses {
                text-align: center;
                padding: 3rem;
                background: white;
                border-radius: 12px;
                grid-column: 1 / -1;
            }
            
            .no-courses i {
                font-size: 4rem;
                color: #d1d5db;
                margin-bottom: 1rem;
            }
            
            .no-courses h3 {
                font-size: 1.5rem;
                color: #374151;
                margin-bottom: 0.5rem;
            }
            
            .no-courses p {
                color: #6b7280;
                margin-bottom: 1.5rem;
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
                    <span><i class="fa-solid fa-house-chimney"></i><a href="welcome.php">Home</a> > My courses</span>
                </div>
                <h1 class="acc-page-title"> My courses</h1>
            </div>
        </div>

        <div class="acc-main-layout">           
          <?php include './includes/sidebar.php'; ?>

            <!-- Main Content -->
                <!-- Course Cards Section -->
                <div class="course-container">
                    <div class="course-header">
                        <h1>Courses</h1>
                        <p>Continue your artistic journey</p>
                    </div>
                    
                    <div class="course-grid">
                        <?php if (!empty($courses)): ?>
                            <?php foreach ($courses as $course): ?>
                                <!-- Dynamic Course Card -->
                                <div class="course-card">
                                    <div class="course-image">
                                        <img src="<?php echo !empty($course['cover_image']) ? $course['cover_image'] : 'Assets/image/art1.jpg'; ?>" alt="<?php echo htmlspecialchars($course['title']); ?>">
                                        <div class="course-menu">
                                            <button class="menu-btn">
                                                <span class="menu-icon">⋯</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="course-content">
                                        <h3 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h3>
                                        <p class="course-description"><?php echo htmlspecialchars($course['short_description']); ?></p>
                                        <div class="course-meta">
                                            <div class="meta-item">
                                                <span class="icon">⏱</span>
                                                <span><?php echo $course['duration_hours'] ?? '0'; ?> Hours</span>
                                            </div>
                                            <div class="meta-item">
                                                <span class="icon">📊</span>
                                                <span><?php echo $course['level'] ?? 'Beginner'; ?></span>
                                            </div>
                                            <div class="meta-item">
                                                <span class="icon">💰</span>
                                                <span>$<?php echo $course['price'] ?? '0'; ?></span>
                                            </div>
                                        </div>
                                        <div class="progress-container">
                                            <div class="progress-info">
                                                <span>Progress</span>
                                                <span>0%</span>
                                            </div>
                                            <div class="progress-bar">
                                                <div class="progress-fill progress-primary" style="width: 0%"></div>
                                            </div>
                                        </div>
                                        <div class="course-footer">
                                            <span class="author">by <span><?php echo htmlspecialchars($course['first_name'] . ' ' . $course['last_name']); ?></span></span>
                                            <div class="rating">
                                                <div class="stars">
                                                    <span class="icon">★</span>
                                                    <span class="icon">★</span>
                                                    <span class="icon">★</span>
                                                    <span class="icon">★</span>
                                                    <span class="icon">☆</span>
                                                </div>
                                                <span class="rating-text">(0.0)</span>
                                            </div>
                                        </div>
                                        <button class="btn btn-primary">Start Learning</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- No courses message -->
                            <div class="no-courses">
                                <i class="ri-book-open-line"></i>
                                <h3>No Courses Available</h3>
                                <p>There are no courses available at the moment. Please check back later or contact the administrator.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <?php if (!empty($success_message)): ?>
    <div class="acc-modal acc-modal-success" style="display: flex;">
        <div class="acc-modal-content">
            <div class="acc-modal-icon"><i class="ri-checkbox-circle-fill"></i></div>
            <h3 class="acc-modal-title">Success!</h3>
            <p class="acc-modal-message"><?php echo $success_message; ?></p>
            <button class="acc-modal-close" onclick="this.parentElement.parentElement.style.display='none'">OK</button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Error Modal -->
    <?php if (!empty($error_message)): ?>
    <div class="acc-modal acc-modal-error" style="display: flex;">
        <div class="acc-modal-content">
            <div class="acc-modal-icon"><i class="ri-error-warning-fill"></i></div>
            <h3 class="acc-modal-title">Error!</h3>
            <p class="acc-modal-message"><?php echo $error_message; ?></p>
            <button class="acc-modal-close" onclick="this.parentElement.parentElement.style.display='none'">OK</button>
        </div>
    </div>
    <?php endif; ?>

    <?php include '../includes/footer.php';?> 
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Close modals when clicking outside modal content
        const modals = document.querySelectorAll('.acc-modal');
        modals.forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
        
        // Add functionality for menu buttons in course cards
        const menuButtons = document.querySelectorAll('.menu-btn');
        menuButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                alert('Menu button clicked. Add your custom functionality here.');
            });
        });
        
        // Add functionality for course buttons
        const courseButtons = document.querySelectorAll('.btn-primary');
        courseButtons.forEach(button => {
            button.addEventListener('click', function() {
                const courseTitle = this.closest('.course-card').querySelector('.course-title').textContent;
                alert('Starting course: ' + courseTitle);
            });
        });
    });
    </script>
</body>
</html>