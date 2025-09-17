<?php
session_start();

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: signin.php");
    exit;
}

// Include database configuration
require_once '../admin/config.php';

// Initialize database connection directly since getDBConnection() is not defined
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

// Initialize variables
$username = $_SESSION["username"] ?? '';
$user_id = $_SESSION["id"] ?? '';
$favorites = [];
$error_message = '';

// Get user's favorites from database
if (!empty($user_id)) {
    try {
        // First check if favorites table exists
        $table_check = $db->query("SHOW TABLES LIKE 'favorites'");
        $favorites_table_exists = $table_check->rowCount() > 0;
        
        if ($favorites_table_exists) {
            // Assuming a favorites table structure that links to artworks
            $stmt = $db->prepare("
                SELECT a.artwork_id, a.title, a.description, a.image_path 
                FROM favorites f 
                JOIN artworks a ON f.artwork_id = a.artwork_id 
                WHERE f.user_id = :user_id
            ");
            $stmt->execute([':user_id' => $user_id]);
            $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $error_message = "Favorites feature is not available yet.";
        }
    } catch (PDOException $e) {
        $error_message = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorites - Maslah Arts</title>
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

        /* Shared Breadcrumb Styles */
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
        .std-breadcrumb a {
            color: #e5e5e5;
            text-decoration: none;
            transition: color 0.3s ease, transform 0.2s ease;
        }

        .std-breadcrumb a:hover {
            color: #ffd166; /* soft yellow hover */
            transform: translateY(-1px); /* subtle lift */
        }

        /* Breadcrumb separator */
        .std-breadcrumb span {
            color: #aaa;
            font-size: 18px;
        }

        /* Breadcrumb Titles */
        .std-breadcrumb h1 {
            color: #fff;
            font-size: 28px;
            font-weight: 700;
            margin-left: 10px;
            font-family: "Poppins", sans-serif;
            letter-spacing: 0.5px;
        }

        /* Page Titles */
        .std-page-title {
            font-size: 26px;
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

        /* Favorites Grid */
        .std-favorites-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        /* Favorite Card */
        .std-favorite-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .std-favorite-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .std-card-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .std-card-content {
            padding: 20px;
        }

        .std-card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }

        .std-card-artist {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }

        .std-card-description {
            font-size: 14px;
            color: #777;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .std-card-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .std-view-btn {
            background: linear-gradient(135deg, #8013e6 0%, #6a0fc9 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .std-view-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .std-remove-btn {
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .std-remove-btn:hover {
            color: #ff4757;
        }

        /* Empty State */
        .std-empty-state {
            text-align: center;
            padding: 50px 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .std-empty-icon {
            font-size: 64px;
            color: #ddd;
            margin-bottom: 20px;
        }

        .std-empty-title {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }

        .std-empty-text {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
        }

        .std-browse-btn {
            background: linear-gradient(135deg, #8013e6 0%, #6a0fc9 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .std-browse-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(128, 19, 230, 0.3);
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
            
            .std-favorites-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .std-header {
                padding: 20px;
            }
            
            .std-breadcrumb h1 {
                font-size: 24px;
            }
            
            .std-main-content {
                width: calc(100% - 200px);
            }
            
            .std-favorites-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
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
            
            .std-favorites-grid {
                grid-template-columns: 1fr;
            }
            
            .std-breadcrumb {
                font-size: 14px;
            }
            
            .std-breadcrumb h1 {
                font-size: 20px;
                margin-bottom: -20px;
            }
        }

        .fav {
            background: #ffffffff;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
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
                    <span><i class="fa-solid fa-house-chimney"></i><a href="welcome.php">Home</a> > Favorites</span>
                </div>
                <h1 class="std-page-title">Favorites</h1>
            </div>
        </div>

        <div class="std-main-layout">           
           <?php include './includes/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="std-main-content">
                <div class="fav">
                    <h2 class="fav-title">Favorites</h2>
                    <div class="fav-sub-Title">
                        <p class="fav-sub-text">Revisit and refine your favorite art</p>
                    </div>
                </div>
                
                
                <?php if (!empty($favorites)): ?>
                    <div class="std-favorites-grid">
                        <?php foreach ($favorites as $favorite): ?>
                            <div class="std-favorite-card">
                                <img src="<?php echo htmlspecialchars($favorite['image_path'] ?? 'assets/image/placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($favorite['title']); ?>" class="std-card-image">
                                <div class="std-card-content">
                                    <h3 class="std-card-title"><?php echo htmlspecialchars($favorite['title']); ?></h3>
                                    <p class="std-card-artist">By <?php echo htmlspecialchars($favorite['artist']); ?></p>
                                    <p class="std-card-description"><?php echo htmlspecialchars(substr($favorite['description'], 0, 100) . '...'); ?></p>
                                    <div class="std-card-actions">
                                        <button class="std-view-btn">View</button>
                                        <button class="std-remove-btn">
                                            <i class="ri-heart-fill" style="color: #ff4757;"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="std-empty-state">
                        <div class="std-empty-icon">
                            <i class="ri-heart-line"></i>
                        </div>
                        <h3 class="std-empty-title">No favorites yet</h3>
                        <p class="std-empty-text">Start exploring our collection and save your favorite artworks.</p>
                        <a href="../gallery/maslah_gallery.php" class="std-browse-btn">Browse Artworks</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php';?> 

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners for remove buttons
            const removeButtons = document.querySelectorAll('.std-remove-btn');
            removeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const card = this.closest('.std-favorite-card');
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.remove();
                        
                        // Check if there are any favorites left
                        if (document.querySelectorAll('.std-favorite-card').length === 0) {
                            // Show empty state
                            const emptyState = document.createElement('div');
                            emptyState.className = 'std-empty-state';
                            emptyState.innerHTML = `
                                <div class="std-empty-icon">
                                    <i class="ri-heart-line"></i>
                                </div>
                                <h3 class="std-empty-title">No favorites yet</h3>
                                <p class="std-empty-text">Start exploring our collection and save your favorite artworks.</p>
                                <a href="gallery.php" class="std-browse-btn">Browse Artworks</a>
                            `;
                            
                            document.querySelector('.std-main-content').appendChild(emptyState);
                        }
                    }, 300);
                });
            });
        });
    </script>
</body>
</html>
