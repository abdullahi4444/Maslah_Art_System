<?php 
    // Include header first
    include "includes/header.php"; 

    // Include the database connection from the admin directory with correct path
    require_once __DIR__ . '/../admin/db.php';
    require_once __DIR__ . '/../admin/config.php';

    try {
        // Use your existing Database class from admin
        $pdo = Database::getConnection();
        
        // Fetch student artworks (same query as in admin gallery-student.php)
        $stmt = $pdo->prepare("
            SELECT aw.artwork_id, aw.title, aw.description, aw.image_path, 
                aw.gallery_type, aw.creation_date, ar.first_name, ar.last_name 
            FROM artworks aw 
            LEFT JOIN artists ar ON aw.artist_id = ar.artist_id 
            WHERE aw.gallery_type = 'student' 
            ORDER BY aw.uploaded_at DESC
        ");
        $stmt->execute();
        $studentArtworks = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $studentArtworks = []; // Empty array if there's an error
    }

    // Calculate total pages needed
    $itemsPerPage = 6; // 2 rows of 3 cards each
    $totalItems = count($studentArtworks);
    $totalPages = ceil($totalItems / $itemsPerPage);

    // Hero slides data - can be extended with more slides
    $heroSlides = [
        [
            'image' => 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1758&q=80',
            'title' => 'Student Gallery',
            'description' => 'Discover amazing artwork from our talented students.'
        ],
        [
            'image' => 'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1450&q=80',
            'title' => 'Young Talent',
            'description' => 'Showcasing the creative potential of our student artists.'
        ],
        [
            'image' => 'https://images.unsplash.com/photo-1515405295579-ba7b45403062?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1500&q=80',
            'title' => 'Creative Growth',
            'description' => 'Witness the artistic development of our students.'
        ],
        [
            'image' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1500&q=80',
            'title' => 'Student Showcase',
            'description' => 'Celebrating the achievements of our art students.'
        ],
        [
            'image' => 'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1500&q=80',
            'title' => 'Art Education',
            'description' => 'Where learning and creativity come together.'
        ]
    ];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Gallery - Maslax Arts</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <!-- Link to shared CSS file -->
    <link rel="stylesheet" href="../gallery/assets/css/style.css">
</head>

<body>
    <!-- Preloader -->
    <div class="preloader" id="preloader">
        <div class="preloader-content">
            <div class="preloader-spinner">
                <div class="preloader-spinner-inner"></div>
            </div>
            <div class="preloader-text">Loading Artworks</div>
            <div class="preloader-progress">
                <div class="preloader-progress-bar" id="preloaderProgress"></div>
            </div>
        </div>
    </div>

    <main class="item-holder_unique">
        <!-- Enhanced Hero Section -->
        <section class="hero-section">
            <div class="hero-slideshow" id="heroSlideshow">
                <?php foreach ($heroSlides as $index => $slide): ?>
                    <div class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>" 
                        style="background-image: url('<?php echo $slide['image']; ?>');">
                        <div class="hero-content">
                            <h1><?php echo $slide['title']; ?></h1>
                            <p><?php echo $slide['description']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="hero-controls">
                <div class="hero-arrow hero-prev">
                    <i class="ri-arrow-left-s-line"></i>
                </div>
                <div class="hero-arrow hero-next">
                    <i class="ri-arrow-right-s-line"></i>
                </div>
            </div>
            
            <div class="hero-indicators" id="heroIndicators">
                <?php foreach ($heroSlides as $index => $slide): ?>
                    <div class="hero-indicator <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>"></div>
                <?php endforeach; ?>
            </div>
            
            <div class="hero-autoplay-control" id="autoplayControl">
                <i class="ri-pause-line"></i>
            </div>
        </section>

        <div class="gallery-section-wrapper-unique">
            <div class="category-tabs">
                <div class="category-container">
                    <a href="../gallery/maslah_gallery.php"><button class="category-btn active">Teacher Gallery</button></a>
                </div>
            </div>
        
            <!-- Gallery Controls Section -->
            <div class="gallery-controls">
                <div class="view-controls">
                    <button class="view-btn active" id="gridView" title="Grid View">
                        <i class="ri-grid-line"></i> Grid
                    </button>
                    <button class="view-btn" id="listView" title="List View">
                        <i class="ri-list-check"></i> List
                    </button>
                </div>
                
                <div class="sort-controls">
                    <button class="sort-btn" id="sortBtn">
                        <i class="ri-sort-desc"></i> Sort
                    </button>
                    <div class="sort-dropdown" id="sortDropdown">
                        <div class="sort-option" data-sort="newest">Newest First</div>
                        <div class="sort-option" data-sort="oldest">Oldest First</div>
                        <div class="sort-option" data-sort="a-z">Alphabetical A–Z</div>
                        <div class="sort-option" data-sort="z-a">Alphabetical Z–A</div>
                    </div>
                </div>
            </div>
        
            <!-- Gallery content will be dynamically loaded here -->
            
            <div class="gallery-container">
                <div class="gallery-grid" id="galleryContent">
                    <?php if (!empty($studentArtworks)): ?>
                        <?php 
                        // Show only first 6 items initially
                        $initialItems = array_slice($studentArtworks, 0, $itemsPerPage);
                        ?>
                        <?php foreach ($initialItems as $index => $artwork): ?>
                            <div class="gallery-item" data-category="student" style="animation-delay: <?php echo $index * 0.1; ?>s">
                                <div class="artwork-card">
                                    <div class="artwork-image">
                                        <?php
                        
                                        $imgUrl = '';
                                        $imgFound = false;
                                        
                                        if (!empty($artwork['image_path'])) {
                                            // First try: Check if image is in admin folder (where admin uploads them)
                                            $adminImagePath = 'admin/' . ltrim($artwork['image_path'], '/');
                                            $adminFullPath = __DIR__ . '/../' . $adminImagePath;
                                            
                                            // Second try: Check if image is in root folder (alternative location)
                                            $rootImagePath = ltrim($artwork['image_path'], '/');
                                            $rootFullPath = __DIR__ . '/../' . $rootImagePath;
                                            
                                            // Third try: Check if it's a full URL
                                            if (filter_var($artwork['image_path'], FILTER_VALIDATE_URL)) {
                                                $imgUrl = $artwork['image_path'];
                                                $imgFound = true;
                                            }
                                            elseif (file_exists($adminFullPath) && is_readable($adminFullPath)) {
                                                $imgUrl = $adminImagePath;
                                                $imgFound = true;
                                            } 
                                            elseif (file_exists($rootFullPath) && is_readable($rootFullPath)) {
                                                $imgUrl = $rootImagePath;
                                                $imgFound = true;
                                            }
                                        }
                                        ?>
                                        
                                        <?php if ($imgFound): ?>
                                            <img src="../<?php echo htmlspecialchars($imgUrl); ?>" 
                                                alt="<?php echo htmlspecialchars($artwork['title']); ?>" 
                                                class="artwork-img"
                                                data-id="<?php echo $artwork['artwork_id']; ?>"
                                                data-title="<?php echo htmlspecialchars($artwork['title']); ?>"
                                                data-artist="<?php echo htmlspecialchars(trim(($artwork['first_name'] ?? '') . ' ' . ($artwork['last_name'] ?? ''))); ?>"
                                                data-description="<?php echo htmlspecialchars($artwork['description'] ?? ''); ?>"
                                                data-date="<?php echo !empty($artwork['creation_date']) ? date('M j, Y', strtotime($artwork['creation_date'])) : ''; ?>">
                                            <div class="artwork-overlay">
                                                <button class="view-btn-large view-artwork-btn" 
                                                        data-id="<?php echo $artwork['artwork_id']; ?>"
                                                        data-image="../<?php echo htmlspecialchars($imgUrl); ?>"
                                                        data-title="<?php echo htmlspecialchars($artwork['title']); ?>"
                                                        data-artist="<?php echo htmlspecialchars(trim(($artwork['first_name'] ?? '') . ' ' . ($artwork['last_name'] ?? ''))); ?>"
                                                        data-description="<?php echo htmlspecialchars($artwork['description'] ?? ''); ?>"
                                                        data-date="<?php echo !empty($artwork['creation_date']) ? date('M j, Y', strtotime($artwork['creation_date'])) : ''; ?>">
                                                    <i class="ri-eye-line"></i> Quick View
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <div class="no-image-placeholder">
                                                <i class="ri-image-line"></i>
                                                <span>No Image Available</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="artwork-info">
                                        <h3 class="artwork-title"><?php echo htmlspecialchars($artwork['title']); ?></h3>
                                        <p class="artwork-artist">
                                            By <?php echo htmlspecialchars(trim(($artwork['first_name'] ?? '') . ' ' . ($artwork['last_name'] ?? ''))); ?>
                                        </p>
                                        <p class="artwork-date">
                                            <?php echo !empty($artwork['creation_date']) ? 
                                                date('M j, Y', strtotime($artwork['creation_date'])) : 'Date not available'; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-artworks-message">
                            <i class="ri-image-line"></i>
                            <h3>No Student Artworks Yet</h3>
                            <p>Artworks added through the admin panel will appear here automatically.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Pagination Controls - Only show if needed -->
            <div class="pagination-container">
                <div class="pagination">
                    <button class="pagination-btn" id="prevBtn" <?php echo $totalPages <= 1 || $totalItems == 0 ? 'disabled' : ''; ?>>Prev</button>
                    <?php if ($totalPages > 1): ?>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <button class="pagination-btn <?php echo $i === 1 ? 'active' : ''; ?>" data-page="<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </button>
                        <?php endfor; ?>
                    <?php endif; ?>
                    <button class="pagination-btn" id="nextBtn" <?php echo $totalPages <= 1 || $totalItems == 0 ? 'disabled' : ''; ?>>Next</button>
                </div>
            </div>
        </div>
    </main>

    <!-- Artwork Modal - UPDATED TO MATCH TEACHER GALLERY STRUCTURE -->
    <div class="modal-overlay" id="artworkModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2 class="modal-title" id="modalArtworkTitle">Artwork Title</h2>
                <button class="modal-close" id="modalClose"><i class="ri-close-line"></i></button>
            </div>
            <div class="modal-content">
                <div class="modal-image-container">
                    <div class="modal-image-loader" id="modalLoader"></div>
                    <img src="" alt="" class="modal-image" id="modalImage">
                </div>
                <div class="modal-details">
                    <p class="modal-artist" id="modalArtist">By Artist Name</p>
                    <p class="modal-description" id="modalDescription">Artwork description goes here...</p>
                    <div class="modal-meta">
                        <div class="modal-meta-item">
                            <span class="modal-meta-label">Created:</span>
                            <span class="modal-meta-value" id="modalDate">Jan 1, 2023</span>
                        </div>
                        <div class="modal-meta-item">
                            <span class="modal-meta-label">Size:</span>
                            <span class="modal-meta-value" id="modalSize">24 × 24 in</span>
                        </div>
                        <div class="modal-meta-item">
                            <span class="modal-meta-label">Likes:</span>
                            <span class="modal-meta-value" id="modalLikes">42</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-actions">
                <button class="action-btn" id="likeBtn">
                    <i class="ri-heart-line"></i> Like
                </button>
                <button class="action-btn" id="downloadBtn">
                    <i class="ri-download-2-line"></i> Download
                </button>
                <!-- <button class="action-btn" id="shareBtn">
                    <i class="ri-share-forward-line"></i> Share
                </button> -->
            </div>
        </div>
    </div>

    <?php include "includes/footer.php"; ?>

    <!-- JavaScript to pass PHP variables to JS -->
    <script>
        // Pass PHP variables to JavaScript
        const allArtworks = <?php echo json_encode($studentArtworks); ?>;
        const itemsPerPage = <?php echo $itemsPerPage; ?>;
        const galleryType = 'student';
    </script>

    <script src="../gallery/assets/js/main.js"></script>

</body>
</html>