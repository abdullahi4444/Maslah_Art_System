<?php

    // Load course from DB by id
    require_once __DIR__ . '/../admin/db.php';
    require_once __DIR__ . '/includes/review_helpers.php';
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $course = null;

    try {
        if ($id > 0 && class_exists('Database')) {
            $pdo = Database::getConnection();
            
            // Helper to normalize media paths so links work from /courses/
            $normalizeMediaPath = function ($path) {
                if (!$path) return '';
                $p = trim($path);
                if ($p === '') return '';
                // Normalize slashes
                $p = str_replace('\\', '/', $p);
                if (preg_match('/^https?:\/\//i', $p)) return $p; // absolute URL
                if ($p[0] === '/') return $p; // root-relative
                // If stored as uploads/... coming from admin, prefix ../admin/
                if (stripos($p, 'uploads/') === 0) return '../admin/' . $p;
                // If path contains /admin/ somewhere, rebuild as relative from courses
                $posAdmin = stripos($p, '/admin/');
                if ($posAdmin !== false) {
                    return '..' . substr($p, $posAdmin);
                }
                // If path contains /uploads/ somewhere, assume under admin/uploads
                $posUploads = stripos($p, '/uploads/');
                if ($posUploads !== false) {
                    return '../admin' . substr($p, $posUploads);
                }
                // If already prefixed with admin/, keep as-is
                if (stripos($p, 'admin/') === 0) return '../' . $p;
                // Default: return as-is
                return $p;
            };
            
            // Fetch main course data
            $stmt = $pdo->prepare('SELECT c.*, cat.title AS category_title FROM courses c LEFT JOIN course_categories cat ON cat.category_id = c.category_id WHERE c.course_id = ? LIMIT 1');
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            
            
            if ($row) {
                $price = (float)$row['price'];
                $discount = (int)$row['discount'];
                $originalPrice = $discount > 0 ? round($price / (1 - max(0, min(90, $discount)) / 100), 2) : null;
                
                // Fetch what you'll learn (outcomes)
                $outcomesStmt = $pdo->prepare('SELECT icon_path, title, description FROM course_outcomes WHERE course_id = ? ORDER BY display_order ASC');
                $outcomesStmt->execute([$id]);
                $outcomes = $outcomesStmt->fetchAll();
                
                $whatYouLearn = [];
                foreach ($outcomes as $outcome) {
                    $whatYouLearn[] = [
                        'icon' => $outcome['icon_path'] ?: 'assets/Icon/icon-9.svg',
                        'title' => $outcome['title'],
                        'desc' => $outcome['description']
                    ];
                }
                
                // Fetch curriculum (sections and lessons)
                $sectionsStmt = $pdo->prepare('SELECT section_id, title, meta FROM course_sections WHERE course_id = ? ORDER BY display_order ASC');
                $sectionsStmt->execute([$id]);
                $sections = $sectionsStmt->fetchAll();
                
                $curriculum = [];
                foreach ($sections as $section) {
                    error_log('Section: ' . print_r($section, true));
                    $lessonsStmt = $pdo->prepare('SELECT type, icon_class, title, duration, video_path, file_url FROM course_lessons WHERE section_id = ? ORDER BY display_order ASC');
                    $lessonsStmt->execute([$section['section_id']]);
                    $lessons = $lessonsStmt->fetchAll();
                    error_log('Lessons for Section ' . $section['section_id'] . ': ' . print_r($lessons, true));
                    
                    $lessonList = [];
                    foreach ($lessons as $lesson) {
                        if ($lesson['type'] === 'video') {
                            $videoPath = $normalizeMediaPath($lesson['video_path']);
                            $lessonList[] = [
                                'type' => 'video',
                                'icon' => $lesson['icon_class'] ?: 'far fa-play-circle',
                                'title' => $lesson['title'],
                                'duration' => $lesson['duration'],
                                'video' => $videoPath ?: 'assets/Image/v.mp4'
                            ];
                        } elseif ($lesson['type'] === 'pdf') {
                            $fileUrl = $normalizeMediaPath($lesson['file_url']);
                            $lessonList[] = [
                                'type' => 'pdf',
                                'icon' => $lesson['icon_class'] ?: 'far fa-file-alt',
                                'title' => $lesson['title'],
                                'url' => $fileUrl ?: '#'
                            ];
                        }
                    }
                    
                    $curriculum[] = [
                        'title' => $section['title'],
                        'meta' => $section['meta'],
                        'lessons' => $lessonList
                    ];
                }
                
                // Fetch course reviews (with error handling)
                $reviews = [];
                $reviewStats = [
                    'average_rating' => 0,
                    'total_reviews' => 0,
                    'rating_bars' => [
                        ['label' => '5 stars', 'width' => '0%', 'percent' => '0%'],
                        ['label' => '4 stars', 'width' => '0%', 'percent' => '0%'],
                        ['label' => '3 stars', 'width' => '0%', 'percent' => '0%'],
                        ['label' => '2 stars', 'width' => '0%', 'percent' => '0%'],
                        ['label' => '1 star', 'width' => '0%', 'percent' => '0%'],
                    ],
                    'stars_html' => '<i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>'
                ];
                $reviewsList = [];
                
                try {
                    // Check if course_reviews table exists
                    $tableCheck = $pdo->query("SHOW TABLES LIKE 'course_reviews'");
                    if ($tableCheck->rowCount() > 0) {
                        $reviewsStmt = $pdo->prepare('SELECT * FROM course_reviews WHERE course_id = ? AND is_approved = 1 ORDER BY is_featured DESC, created_at DESC');
                        $reviewsStmt->execute([$id]);
                        $reviews = $reviewsStmt->fetchAll();
                        
                        // Calculate review statistics
                        $reviewStats = calculateReviewStatistics($reviews);
                        
                        // Format reviews for display
                        foreach ($reviews as $review) {
                            $reviewsList[] = formatReviewData($review);
                        }
                    } else {
                        error_log('course_reviews table does not exist. Please run setup_reviews_table.php');
                    }
                } catch (Exception $e) {
                    error_log('Error fetching reviews: ' . $e->getMessage());
                    // Continue with empty reviews - the page will still work
                }
                
                // Fetch similar courses
                $similarStmt = $pdo->prepare('SELECT c.course_id, c.title, c.short_description, c.price, c.cover_image, c.level 
                                            FROM course_similar cs 
                                            JOIN courses c ON cs.similar_course_id = c.course_id 
                                            WHERE cs.course_id = ?');
                $similarStmt->execute([$id]);
                $similarCourses = $similarStmt->fetchAll();

                $similarCoursesList = [];
                foreach ($similarCourses as $sim) {
                    $similarCoursesList[] = [
                        'id' => $sim['course_id'],
                        'image' => $sim['cover_image'] ?: 'assets/Image/img-1.png',
                        'title' => $sim['title'],
                        'desc' => $sim['short_description'],
                        'price' => '$' . number_format($sim['price'], 2),
                        'level' => $sim['level'],
                        'stars' => '★★★★☆',
                        'rating' => '4.5'
                    ];
                }
                
                // Build the course array
                $course = [
                    'title' => $row['title'],
                    'category' => $row['category_title'] ?: 'Uncategorized',
                    'level' => $row['level'],
                    'hero_image' => $row['cover_image'] ?: 'assets/Image/img-1.png',
                    'instructor_name' => $row['instructor_name'] ?: 'Instructor',
                    'instructor_image' => $row['instructor_image'] ?: 'assets/Image/avatar-1.png',
                    'price' => $price,
                    'original_price' => $originalPrice,
                    'discount' => $discount > 0 ? ($discount . '% off') : null,
                    'features' => [
                        ['icon' => 'assets/Icon/icon-15.svg', 'text' => 'Skill level: ' . $row['level']],
                        ['icon' => 'assets/Icon/icon-5.svg', 'text' => 'Certificate of completion'],
                        ['icon' => 'assets/Icon/icon-6.svg', 'text' => 'Self-paced learning'],
                        ['icon' => 'assets/Icon/icon-7.svg', 'text' => ($row['duration_hours'] ?: 0) . ' hours of content'],
                    ],
                    'what_you_learn' => $whatYouLearn,
                    'description' => $row['full_description'],
                    'curriculum' => $curriculum,
                    'bio' => [
                        'image' => $row['instructor_image'] ?: 'assets/Image/avatar-1.png',
                        'name' => $row['instructor_name'] ?: 'Instructor',
                        'desc' => $row['instructor_bio'] ?: 'Learn with our expert instructor.',
                        'social' => [
                            ['icon' => 'assets/Icon/icon-12.svg', 'url' => $row['instructor_social_facebook'] ?: '#'],
                            ['icon' => 'assets/Icon/icon-13.svg', 'url' => $row['instructor_social_instagram'] ?: '#'],
                            ['icon' => 'assets/Icon/icon-14.svg', 'url' => $row['instructor_social_twitter'] ?: '#'],
                        ],
                    ],
                    'reviews_summary' => [
                        'rating' => $reviewStats['average_rating'] ?? 0,
                        'stars' => $reviewStats['stars_html'] ?? '',
                        'total' => 'Based on ' . (($reviewStats['total_reviews'] ?? 0)) . ' review' . ((($reviewStats['total_reviews'] ?? 0) != 1) ? 's' : ''),
                        'bars' => $reviewStats['rating_bars'] ?? [],
                        'total_reviews' => $reviewStats['total_reviews'] ?? 0
                    ],
                    'reviews_list' => $reviewsList,
                    'similar_courses' => $similarCoursesList,
                ];
            }
        }
    } catch (Throwable $e) {
        // Log error and show a message
        error_log("Error loading course: " . $e->getMessage());
    }


    // If DB did not return a course, show error
    if (!$course) {
        header("HTTP/1.0 404 Not Found");
        echo "<h2>Course not found</h2>";
        echo "<p>The requested course could not be found. Please check the URL and try again.</p>";
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maslax Arts - <?php echo htmlspecialchars($course['title']); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
</head>
<body class="course-details-page">
    <?php include 'includes/header.php';?>
    <main>
        <div class="page-content-wrapper">
            <section class="course-hero-section">
                <div class="hero-image-container">
                    <img src="<?php echo htmlspecialchars($course['hero_image']); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>">
                </div>
                <div class="course-title-wrapper">
                    <div class="container">
                        <div class="course-details">
                            <p class="breadcrumbs"><?php echo htmlspecialchars($course['category']); ?> • <?php echo htmlspecialchars($course['level']); ?></p>
                            <h1><?php echo htmlspecialchars($course['title']); ?></h1>
                            <div class="instructor-info">
                                <img src="<?php echo htmlspecialchars($course['instructor_image']); ?>" alt="<?php echo htmlspecialchars($course['instructor_name']); ?>" class="avatar">
                                <div>
                                    <div class="instructor-name"><?php echo htmlspecialchars($course['instructor_name']); ?></div>
                                    <?php if (!empty($course['reviews_summary'])): ?>
                                    <div class="rating">
                                        <span class="stars"><?php echo $course['reviews_summary']['stars']; ?></span>
                                        <span class="rating-value"><?php echo htmlspecialchars($course['reviews_summary']['rating']); ?> (<?php echo (int)($course['reviews_summary']['total_reviews'] ?? 0); ?> reviews)</span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <aside class="course-sidebar-floating">
                <div class="price-info">
                    <span class="current-price">$<?php echo number_format($course['price'],2); ?></span>
                    <?php if (!empty($course['original_price'])): ?>
                        <span class="original-price">$<?php echo number_format($course['original_price'],2); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($course['discount'])): ?>
                        <span class="discount-badge"><?php echo htmlspecialchars($course['discount']); ?></span>
                    <?php endif; ?>
                </div>
                <ul class="course-features">
                    <?php foreach ($course['features'] as $feature): ?>
                        <li><img src="<?php echo htmlspecialchars($feature['icon']); ?>"> <?php echo htmlspecialchars($feature['text']); ?></li>
                    <?php endforeach; ?>
                </ul>
                <div class="course-buttons">
                    <button class="btn btn-primary">Add to Cart</button>
                    <button class="btn btn-secondary"> Enroll Now</button>
                    <button class="btn btn-tertiary"><i class="fas fa-share"></i> Share</button>
                </div>
            </aside>
            <div class="container-narrow">
                <section class="what-youll-learn animate-on-scroll">
                    <h2 class="section-title text-center">What You'll Learn</h2>
                    <div class="learning-points-grid">
                        <?php foreach ($course['what_you_learn'] as $i => $point): ?>
                        <div class="point-card animate-on-scroll"<?php if ($i > 0) echo ' style="animation-delay: '.($i*150).'ms;"'; ?>>
                            <div class="point-icon"><img src="<?php echo htmlspecialchars($point['icon']); ?>"></div>
                            <div class="point-text">
                                <h3><?php echo htmlspecialchars($point['title']); ?></h3>
                                <p><?php echo htmlspecialchars($point['desc']); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <section class="course-content-section animate-on-scroll">
                    <h2 class="section-title">Course Description</h2>
                    <div class="content-box">
                        <p><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
                    </div>
                    <h2 class="section-title">Course Curriculum</h2>
                    <div class="content-box">
                        <div class="accordion">
                            <?php foreach ($course['curriculum'] as $i => $item): ?>
                            <div class="accordion-item<?php if ($i === 0) echo ' active'; ?>">
                                <div class="accordion-header">
                                    <div class="header-left">
                                        <span class="title"><?php echo htmlspecialchars($item['title']); ?></span>
                                        <?php if (!empty($item['meta'])): ?>
                                            <span class="meta-under"><?php echo htmlspecialchars($item['meta']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="header-right">
                                        <?php if (!empty($item['meta'])): ?>
                                            <span class="meta-right"><?php echo htmlspecialchars($item['meta']); ?></span>
                                        <?php endif; ?>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                                <div class="accordion-content">
                                    <?php if (!empty($item['lessons'])): ?>
                                        <ul>
                                            <?php foreach ($item['lessons'] as $lesson): ?>
                                                <li>
                                                    <?php if ($lesson['type'] === 'video'): ?>
                                                        <a href="#" class="video-lesson-link" data-video-url="<?php echo htmlspecialchars($lesson['video']); ?>">
                                                            <i class="<?php echo htmlspecialchars($lesson['icon']); ?>"></i>
                                                            <?php echo htmlspecialchars($lesson['title']); ?>
                                                            <span class="duration"><?php echo htmlspecialchars($lesson['duration']); ?></span>
                                                        </a>
                                                    <?php elseif ($lesson['type'] === 'pdf'): ?>
                                                        <?php if (!empty($lesson['url']) && $lesson['url'] !== '#'): ?>
                                                            <a href="<?php echo htmlspecialchars($lesson['url']); ?>" target="_blank" rel="noopener noreferrer">
                                                                <i class="<?php echo htmlspecialchars($lesson['icon']); ?>"></i>
                                                                <?php echo htmlspecialchars($lesson['title']); ?>
                                                            </a>
                                                        <?php else: ?>
                                                            <span>
                                                                <i class="<?php echo htmlspecialchars($lesson['icon']); ?>"></i>
                                                                <?php echo htmlspecialchars($lesson['title']); ?> (file unavailable)
                                                            </span>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($lesson); ?>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <h2 class="section-title animate-on-scroll">Instructor Bio</h2>
                    <div class="content-box animate-on-scroll">
                        <div class="bio-card">
                            <img src="<?php echo htmlspecialchars($course['bio']['image']); ?>" alt="<?php echo htmlspecialchars($course['bio']['name']); ?>" class="avatar-large">
                            <div class="bio-text">
                                <h3><?php echo htmlspecialchars($course['bio']['name']); ?></h3>
                                <p><?php echo nl2br(htmlspecialchars($course['bio']['desc'])); ?></p>
                                <?php if (!empty($course['bio']['social'])): ?>
                                    <div class="social-links">
                                        <?php foreach ($course['bio']['social'] as $social): ?>
                                            <a href="<?php echo htmlspecialchars($social['url']); ?>" target="_blank"><img src="<?php echo htmlspecialchars($social['icon']); ?>"></a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($course['reviews_summary']) && isset($course['reviews_summary']['total_reviews']) && $course['reviews_summary']['total_reviews'] > 0): ?>
                    <h2 class="section-title animate-on-scroll">Student Reviews</h2>
                    <div class="content-box animate-on-scroll">
                        <div class="reviews-layout">
                            <div class="reviews-summary">
                                <div class="summary-rating"><?php echo htmlspecialchars($course['reviews_summary']['rating']); ?></div>
                                <div class="summary-stars"><?php echo $course['reviews_summary']['stars']; ?></div>
                                <div class="summary-total"><?php echo htmlspecialchars($course['reviews_summary']['total']); ?></div>
                                <div class="rating-bars">
                                    <?php if (isset($course['reviews_summary']['bars']) && is_array($course['reviews_summary']['bars'])): ?>
                                        <?php foreach ($course['reviews_summary']['bars'] as $bar): ?>
                                            <div class="bar-item">
                                                <div class="label"><?php echo htmlspecialchars($bar['label']); ?></div>
                                                <div class="bar-bg">
                                                    <div class="bar-fill" style="width: <?php echo htmlspecialchars($bar['width']); ?>;"></div>
                                                </div>
                                                <div class="percent"><?php echo htmlspecialchars($bar['percent']); ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="reviews-list">
                                <?php if (isset($course['reviews_list']) && is_array($course['reviews_list'])): ?>
                                    <?php foreach ($course['reviews_list'] as $review): ?>
                                        <div class="review-card">
                                            <div class="review-header">
                                                <img src="<?php echo htmlspecialchars($review['avatar']); ?>" alt="<?php echo htmlspecialchars($review['name']); ?>" class="avatar">
                                                <div class="review-author">
                                                    <h4><?php echo htmlspecialchars($review['name']); ?></h4>
                                                    <div class="stars"><?php echo $review['stars_html']; ?></div>
                                                </div>
                                            </div>
                                            <p><?php echo htmlspecialchars($review['text']); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <h2 class="section-title animate-on-scroll">Student Reviews</h2>
                    <div class="content-box animate-on-scroll">
                        <div class="no-reviews-message">
                            <p>No reviews yet. Be the first to review this course!</p>
                        </div>
                    </div>
                    <?php endif; ?>
        <!-- In course-details.php, replace the similar courses section with this code -->
        <section class="similar-courses-section">
            <h2 class="section-title">Similar Courses You Might Like</h2>
            <div class="similar-courses-grid">
                <?php if (!empty($course['similar_courses'])): ?>
                    <?php foreach ($course['similar_courses'] as $similar): ?>
                        <a href="course-details.php?id=<?= htmlspecialchars($similar['id']) ?>" class="course-card-link">
                            <div class="course-card">
                                <div class="card-image">
                                    <img src="<?= htmlspecialchars($similar['image']) ?>" alt="<?= htmlspecialchars($similar['title']) ?>">
                                </div>
                                <div class="card-content">
                                    
                                    <h3><?= htmlspecialchars($similar['title']) ?></h3>
                                    <p><?= htmlspecialchars($similar['desc']) ?></p>
                                    <div class="course-card-footer">
                                    
                                        <span class="price"><?= htmlspecialchars($similar['price']) ?></span>
                                        <div class="rating">
                                            <span class="stars"><?= $similar['stars'] ?? '★★★★☆' ?></span>
                                            <span class="rating-value"><?= $similar['rating'] ?? '4.5' ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No similar courses available.</p>
                <?php endif; ?>
            </div>
        </section>           
    </main>
    
    <?php include 'includes/footer.php';?>

    <div id="video-modal" class="video-modal">
        <div class="video-modal-content">
            <span class="close-modal-btn">&times;</span>
            <video id="course-video-player" controls>
                <source src="" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    </div>

    <script>const courses = [];</script>
    <script src="assets/js/script.js">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Accordion functionality
            const accordionHeaders = document.querySelectorAll('.accordion-header');
            
            accordionHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const accordionItem = this.parentElement;
                    const accordionContent = this.nextElementSibling;
                    
                    // Toggle active class
                    accordionItem.classList.toggle('active');
                    
                    // Toggle content visibility
                    if (accordionItem.classList.contains('active')) {
                        accordionContent.style.maxHeight = accordionContent.scrollHeight + 'px';
                    } else {
                        accordionContent.style.maxHeight = 0;
                    }
                });
            });
            
            // Initialize accordion heights
            document.querySelectorAll('.accordion-content').forEach(content => {
                if (content.parentElement.classList.contains('active')) {
                    content.style.maxHeight = content.scrollHeight + 'px';
                } else {
                    content.style.maxHeight = 0;
                }
            });
            
            // Video modal functionality
            const videoModal = document.getElementById('video-modal');
            const videoPlayer = document.getElementById('course-video-player');
            const closeModalBtn = document.querySelector('.close-modal-btn');
            const videoLessonLinks = document.querySelectorAll('.video-lesson-link');
            
            videoLessonLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const videoUrl = this.getAttribute('data-video-url');
                    
                    // Set video source and show modal
                    videoPlayer.src = videoUrl;
                    videoModal.style.display = 'flex';
                    videoPlayer.play();
                });
            });
            
            // Close modal
            closeModalBtn.addEventListener('click', function() {
                videoModal.style.display = 'none';
                videoPlayer.pause();
                videoPlayer.currentTime = 0;
            });
            
            // Close modal when clicking outside
            videoModal.addEventListener('click', function(e) {
                if (e.target === videoModal) {
                    videoModal.style.display = 'none';
                    videoPlayer.pause();
                    videoPlayer.currentTime = 0;
                }
            });
        });
        </script>
    </script>
    <!-- Action Modal -->
    <div id="action-modal" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <div id="modal-body"></div>
    </div>
    </div>
</body>
</html>
