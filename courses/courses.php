<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // --- CHANGE 1: ADD 'url' TO EACH COURSE POINTING TO ITS SPECIFIC PAGE ---
    // This array now includes the direct link to each course's detail page.
    require_once __DIR__ . '/../admin/db.php';

    $courses = [];
    try {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT 
            c.course_id AS id,
            c.title,
            cat.title AS category,
            c.level,
            c.price,
            c.discount,
            c.cover_image AS image,
            c.instructor_name AS instructor,
            c.featured,
            COALESCE(cr.avg_rating, 0) AS rating,
            COALESCE(cr.review_count, 0) AS reviews
        FROM courses c
        LEFT JOIN course_categories cat ON cat.category_id = c.category_id
        LEFT JOIN (
            SELECT course_id, ROUND(AVG(rating), 1) AS avg_rating, COUNT(*) AS review_count
            FROM course_reviews
            WHERE is_approved = 1
            GROUP BY course_id
        ) cr ON cr.course_id = c.course_id
        ORDER BY c.created_at DESC');
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($courses as &$course) {
            $course['image'] = $course['image'] ? '../admin/' . $course['image'] : 'assets/Image/default-course.jpg';
            $course['originalPrice'] = $course['discount'] > 0 ? round($course['price'] / (1 - $course['discount'] / 100), 2) : null;
            $course['url'] = 'course-details.php?id=' . $course['id'];
            $course['tags'] = ['Certificate']; // Example tag, adjust as needed
            $course['badge'] = $course['discount'] > 0 ? 'discount' : '';
            $course['badgeText'] = $course['discount'] > 0 ? $course['discount'] . '% OFF' : '';
            $course['reviews'] = isset($course['reviews']) ? (int)$course['reviews'] : 0;
            $course['rating'] = isset($course['rating']) ? (float)$course['rating'] : 0.0;
            $course['featured'] = $course['featured'] == 1; // Ensure featured is boolean
            // Simulate delivery method (replace with actual database field when available)
        $course['delivery_method'] = $course['id'] % 2 == 0 ? 'Live Classes' : 'Self-paced';
        
        // Update tags to include delivery method
        $course['tags'] = ['Certificate', $course['delivery_method']];
        }
    } catch (Throwable $e) {
        error_log('Error fetching courses: ' . $e->getMessage());
    }

    $featuredCourses = array_filter($courses, fn($course) => $course['featured']);
    $nonFeaturedCourses = array_filter($courses, fn($course) => !$course['featured']);

    // Helper function for rendering star ratings in PHP
    function generateRatingStars($rating) {
        $html = '';
        $full = floor($rating);
        $half = ($rating - $full) >= 0.5;
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $full) {
                $html .= '<i class="fas fa-star"></i>';
            } elseif ($i === $full + 1 && $half) {
                $html .= '<i class="fas fa-star-half-alt"></i>';
            } else {
                $html .= '<i class="far fa-star"></i>';
            }
        }
        return $html;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maslax Arts - Enhanced Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
     <style>
        /* Add styles for the tags */
        .tag.level-beginner {
            background-color: #b388eb34; 
            color: var(--color-primary-purple);
        }
        .tag.level-intermediate {
            background-color: #facc1541; 
            color: #a4480f;
        }
        .tag.level-advanced {
            background-color: #b388eb34; 
            color: var(--color-primary-purple);
        }
        .tag2 {
            background: #3000ff26;
            color: #3000FF;
        }
        .tag.certificate-tag {
            background: #21f73a25; 
            color: #166534;
        }
    </style>
</head>
<?php include 'Includes/header.php'; ?>

<body>
  <main>
    <section class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content animate-on-scroll">
            <h1>Explore Our Art Courses</h1>
            <p>Painting, digital illustration, sculpture, and more</p>
            <div class="search-container">
            <img src="assets/Icon/icon-1.svg" alt="Search Icon" class="search-icon2">
            <input type="text" id="search-input" placeholder="Search for courses ..."/>
            </div>
            <div class="filters">
            <select id="category-filter">
                <option value="all">Categories <img src="assets/Icon/icon-3.svg"> </option>
                <option value="digital">Digital Art</option>
                <option value="painting">Painting</option>
                <option value="pottery">Pottery</option>
                <option value="design">Design</option>
                <option value="animation">Animation</option>
            </select>
            <select id="level-filter">
                <option value="all">Skill Levels</option>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
            </select>
            <select id="price-filter">
                <option value="all"> Prices Range</option>
                <option value="0-50">$0 - $50</option>
                <option value="50-100">$50 - $100</option>
                <option value="100">$100+</option>
            </select>
            </div>
        </div>
        </section>

    <section class="courses-section">
        <div class="section-header animate-on-scroll">
            <h2>Featured Courses</h2>
            <div class="carousel-arrows">
                <span class="prev-arrow"><img src="assets/Icon/Vector (1).svg"></span>
                <span class="next-arrow active"><img src="assets/Icon/Vector (2).svg"></span>
            </div>
        </div>
        <div class="featured-courses-container animate-on-scroll">
            <div class="featured-courses-wrapper">
                <?php 
                // Dynamically render the first 5 courses as featured, split into 2 slides (3 + 2)
                $featured = array_slice($courses, 0, 5);
                ?>
                <div class="featured-courses-slide" id="slide1">
                    <?php foreach (array_slice($featured, 0, 3) as $course): ?>
                    <div class="course-card" 
                        data-category="<?= htmlspecialchars($course['category']) ?>" 
                        data-level="<?= htmlspecialchars($course['level']) ?>" 
                        data-price="<?= htmlspecialchars($course['price']) ?>"
                        data-title="<?= htmlspecialchars($course['title']) ?>">
                        <div class="card-image">
                            <img src="<?= htmlspecialchars($course['image']) ?>" alt="<?= htmlspecialchars($course['title']) ?>">
                            <span class="badge featured">Featured</span>
                        </div>
                        <div class="card-content">
                            <div class="tags">
                                <span class="tag level-<?= htmlspecialchars(strtolower($course['level'])) ?>"><?= ucfirst(htmlspecialchars($course['level'])) ?></span>
                                <?php if (!empty($course['tags'])): ?>
                                    <?php foreach ($course['tags'] as $tag): 
                                        $tagClass = 'tag';
                                        if ($tag === 'Self-paced' || $tag === 'Live Classes') {
                                            $tagClass = 'tag2';
                                        } elseif ($tag === 'Certificate') {
                                            $tagClass = 'tag certificate-tag';
                                        }
                                    ?>
                                        <span class="<?= $tagClass ?>"><?= htmlspecialchars($tag) ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <h3><?= htmlspecialchars($course['title']) ?></h3>
                            <p class="instructor">By <?= htmlspecialchars($course['instructor']) ?></p>
                            <div class="rating">
                                <?= generateRatingStars($course['rating']) ?>
                                <?= htmlspecialchars($course['rating']) ?> (<?= htmlspecialchars($course['reviews']) ?> reviews)
                            </div>
                            <div class="price-view">
                                <span class="price">$<?= htmlspecialchars($course['price']) ?>
                                    <?php if (!empty($course['originalPrice'])): ?>
                                        <del>$<?= htmlspecialchars($course['originalPrice']) ?></del>
                                    <?php endif; ?>
                                </span>
                                <a href="<?= htmlspecialchars($course['url']) ?>" class="btn-view">View Course</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="featured-courses-slide" id="slide2">
                    <?php foreach (array_slice($featured, 3, 2) as $course): ?>
                    <div class="course-card" 
                        data-category="<?= htmlspecialchars($course['category']) ?>" 
                        data-level="<?= htmlspecialchars($course['level']) ?>" 
                        data-price="<?= htmlspecialchars($course['price']) ?>"
                        data-title="<?= htmlspecialchars($course['title']) ?>">
                        <div class="card-image">
                            <img src="<?= htmlspecialchars($course['image']) ?>" alt="<?= htmlspecialchars($course['title']) ?>">
                            <span class="badge featured">Featured</span>
                        </div>
                        <div class="card-content">
                            <div class="tags">
                                <span class="tag level-<?= htmlspecialchars(strtolower($course['level'])) ?>"><?= ucfirst(htmlspecialchars($course['level'])) ?></span>
                                <?php if (!empty($course['tags'])): ?>
                                    <?php foreach ($course['tags'] as $tag): 
                                        $tagClass = 'tag';
                                        if ($tag === 'Self-paced' || $tag === 'Live Classes') {
                                            $tagClass = 'tag2';
                                        } elseif ($tag === 'Certificate') {
                                            $tagClass = 'tag certificate-tag';
                                        }
                                    ?>
                                        <span class="<?= $tagClass ?>"><?= htmlspecialchars($tag) ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <h3><?= htmlspecialchars($course['title']) ?></h3>
                            <p class="instructor">By <?= htmlspecialchars($course['instructor']) ?></p>
                            <div class="rating">
                                <?= generateRatingStars($course['rating']) ?>
                                <?= htmlspecialchars($course['rating']) ?> (<?= htmlspecialchars($course['reviews']) ?> reviews)
                            </div>
                            <div class="price-view">
                                <span class="price">$<?= htmlspecialchars($course['price']) ?>
                                    <?php if (!empty($course['originalPrice'])): ?>
                                        <del>$<?= htmlspecialchars($course['originalPrice']) ?></del>
                                    <?php endif; ?>
                                </span>
                                <a href="<?= htmlspecialchars($course['url']) ?>" class="btn-view">View Course</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="carousel-indicators">
            <div class="carousel-indicator active" data-slide="0"></div>
            <div class="carousel-indicator" data-slide="1"></div>
        </div>
    </section>

    <section class="courses-section all-courses">
        <div class="section-header animate-on-scroll">
            <h2 id="All1">All Courses</h2>
            <select class="sort-by" id="sort-by">
                <option value="popular">Sort by: Popular</option>
                <option value="price-asc">Price: Low to High</option>
                <option value="price-desc">Price: High to Low</option>
                <option value="newest">Newest First</option>
            </select>
        </div>
        <div class="courses-grid" id="all-courses-grid">
            
            <!-- The initial course cards will be rendered here by the PHP loop below -->
           <?php foreach ($courses as $course): ?>
            <div class="course-card animate-on-scroll" 
                 data-category="<?= htmlspecialchars($course['category']) ?>" 
                 data-level="<?= htmlspecialchars($course['level']) ?>" 
                 data-price="<?= htmlspecialchars($course['price']) ?>"
                 data-title="<?= htmlspecialchars($course['title']) ?>"
                 data-reviews="<?= htmlspecialchars($course['reviews']) ?>">

                <div class="card-image">
                    <img src="<?= htmlspecialchars($course['image']) ?>" alt="<?= htmlspecialchars($course['title']) ?>">
                    <?php if (!empty($course['badge'])): ?>
                        <span class="badge <?= htmlspecialchars($course['badge']) ?>"><?= htmlspecialchars($course['badgeText']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="card-content">
                    <div class="tags">
                        <span class="tag level-<?= htmlspecialchars(strtolower($course['level'])) ?>"><?= ucfirst(htmlspecialchars($course['level'])) ?></span>
                        <?php if (!empty($course['tags'])): ?>
                            <?php foreach ($course['tags'] as $tag): 
                                $tagClass = 'tag';
                                if ($tag === 'Self-paced' || $tag === 'Live Classes') {
                                    $tagClass = 'tag2';
                                } elseif ($tag === 'Certificate') {
                                    $tagClass = 'tag certificate-tag';
                                }
                            ?>
                                <span class="<?= $tagClass ?>"><?= htmlspecialchars($tag) ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <h3><?= htmlspecialchars($course['title']) ?></h3>
                    <p class="instructor">By <?= htmlspecialchars($course['instructor']) ?></p>
                    <div class="rating">
                        <?= generateRatingStars($course['rating']) ?>
                        <?= htmlspecialchars($course['rating']) ?> (<?= htmlspecialchars($course['reviews']) ?> reviews)
                    </div>
                    <div class="price-view">
                        <span class="price">
                            $<?= htmlspecialchars($course['price']) ?>
                            <?php if (!empty($course['originalPrice'])): ?>
                                <del>$<?= htmlspecialchars($course['originalPrice']) ?></del>
                            <?php endif; ?>
                        </span>
                        <a href="<?= htmlspecialchars($course['url']) ?>" class="btn-view">View Course</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <!-- END OF REPLACEMENT SECTION -->
        
    </div>
        </div>
        <div class="pagination" id="pagination-container">
            <span class="prev-page"><</span>
            <div id="page-numbers"></div>
            <span class="next-page">></span>
        </div>
    </section>

    <section class="why-choose-us">
        <h2 class="animate-on-scroll">Why Choose Maslax Arts</h2>
        <div class="features-grid">
            <div class="feature-card animate-on-scroll">
                <div class="icon-container"><i class="fas fa-certificate"></i></div>
                <h3>Certificate of Completion</h3>
                <p>Earn recognized certificates upon course completion to showcase your new skills and achievements.</p>
            </div>
            <div class="feature-card animate-on-scroll" style="animation-delay: 150ms;">
                <div class="icon-container"><i class="fas fa-user-tie"></i></div>
                <h3>Expert Artists</h3>
                <p>Learn from industry professionals and renowned artists with years of experience and proven teaching methods.</p>
            </div>
            <div class="feature-card animate-on-scroll" style="animation-delay: 300ms;">
                 <div class="icon-container"><i class="fas fa-download"></i></div>
                <h3>Downloadable Resources</h3>
                <p>Access templates, worksheets, and reference materials to support your learning journey and practice.</p>
            </div>
        </div><br>
        <section class="cta-section animate-on-scroll">
          <div class="cta-overlay"></div>
          <div class="cta-content">
            <h2>Start Your Creative Journey Today!</h2>
            <p>Join thousands of students who are discovering their artistic potential and mastering new skills with our expert-led courses.</p>
            <div class="cta-buttons">
              <button class="btn-secondary"><a href="#All1" > Browse Courses</a></button>
              <button class="btn-primary">Enroll Now</button>
            </div>
          </div>
        </section>
    </section>
  </main>

    <?php include 'includes/footer.php';?>

    <!-- This script passes the PHP data to JavaScript for filtering -->
    <script>
        const courses = <?php echo json_encode($courses, JSON_UNESCAPED_SLASHES); ?>;
    </script>
    <script src="assets/js/script.js"></script>
    <script>
        // Override the filterCourses function to use startsWith instead of includes
        function filterCourses() {
            const term = searchInput.value.toLowerCase().trim();
            const category = categoryFilter.value;
            const level = levelFilter.value;
            const priceRange = priceFilter.value;

            filteredCourses = courses.filter(course => {
                // Search term filter - changed from includes to startsWith
                if (term && !course.title.toLowerCase().startsWith(term)) return false;
                
                // Category filter with mapping
                if (category !== 'all') {
                    // Map filter values to actual category names
                    const categoryMap = {
                        'digital': 'Digital Art',
                        'painting': 'Painting',
                        'pottery': 'Pottery',
                        'design': 'Design',
                        'animation': 'Animation'
                    };
                    const mappedCategory = categoryMap[category] || category;
                    if (course.category !== mappedCategory) return false;
                }
                
                // Level filter (convert to proper case)
                if (level !== 'all') {
                    const levelMap = {
                        'beginner': 'Beginner',
                        'intermediate': 'Intermediate',
                        'advanced': 'Advanced'
                    };
                    const mappedLevel = levelMap[level] || level;
                    if (course.level !== mappedLevel) return false;
                }
                
                // Price filter
                if (priceRange !== 'all') {
                    const [min, max] = priceRange.split('-').map(Number);
                    if (max) {
                        // Range with upper bound (e.g., 0-50, 50-100)
                        if (course.price < min || course.price > max) return false;
                    } else {
                        // No upper bound (e.g., 100+)
                        if (course.price < min) return false;
                    }
                }
                
                return true;
            });
        }

        // Function to filter featured courses in the DOM
        function filterFeaturedInDOM() {
            const featuredCards = document.querySelectorAll('.featured-courses-slide .course-card');
            const slides = document.querySelectorAll('.featured-courses-slide');
            const term = searchInput.value.toLowerCase().trim();
            const category = categoryFilter.value;
            const level = levelFilter.value;
            const priceRange = priceFilter.value;
            
            let anyFeaturedVisible = false;
            
            featuredCards.forEach(card => {
                const title = card.dataset.title.toLowerCase();
                const cardCategory = card.dataset.category;
                const cardLevel = card.dataset.level;
                const cardPrice = parseFloat(card.dataset.price);
                
                let visible = true;
                
                // Search term filter
                if (term && !title.startsWith(term)) visible = false;
                
                // Category filter
                if (category !== 'all') {
                    const categoryMap = {
                        'digital': 'Digital Art',
                        'painting': 'Painting',
                        'pottery': 'Pottery',
                        'design': 'Design',
                        'animation': 'Animation'
                    };
                    const mappedCategory = categoryMap[category] || category;
                    if (cardCategory !== mappedCategory) visible = false;
                }
                
                // Level filter
                if (level !== 'all') {
                    const levelMap = {
                        'beginner': 'Beginner',
                        'intermediate': 'Intermediate',
                        'advanced': 'Advanced'
                    };
                    const mappedLevel = levelMap[level] || level;
                    if (cardLevel !== mappedLevel) visible = false;
                }
                
                // Price filter
                if (priceRange !== 'all') {
                    const [min, max] = priceRange.split('-').map(Number);
                    if (max) {
                        // Range with upper bound (e.g., 0-50, 50-100)
                        if (cardPrice < min || cardPrice > max) visible = false;
                    } else {
                        // No upper bound (e.g., 100+)
                        if (cardPrice < min) visible = false;
                    }
                }
                
                // Show/hide the card
                card.style.display = visible ? 'block' : 'none';
                
                if (visible) anyFeaturedVisible = true;
            });
            
            // Show/hide slides based on whether they have visible cards
            slides.forEach(slide => {
                const cardsInSlide = slide.querySelectorAll('.course-card');
                const hasVisibleCards = Array.from(cardsInSlide).some(card => card.style.display !== 'none');
                slide.style.display = hasVisibleCards ? 'grid' : 'none';
            });
        }

        // Override the applyFiltersAndSort function to include featured courses filtering
        function applyFiltersAndSort() {
            // These functions should only run if the filter elements exist on the page
            if (searchInput) filterCourses();
            if (sortSelect) sortCourses();
            
            // MODIFIED: Adjust layout for screen size before rendering
            adjustLayoutForScreenSize(); 
            
            currentPage = 1;

            // Only render if the grid exists
            if (allCoursesGrid) {
                renderAllCourses();
            }
            
            // Filter featured courses in the DOM
            filterFeaturedInDOM();
        }

        // Initialize the page with both sections rendered
        document.addEventListener('DOMContentLoaded', function() {
            // Set up event listeners for filters
            if (searchInput) searchInput.addEventListener('input', applyFiltersAndSort);
            if (categoryFilter) categoryFilter.addEventListener('change', applyFiltersAndSort);
            if (levelFilter) levelFilter.addEventListener('change', applyFiltersAndSort);
            if (priceFilter) priceFilter.addEventListener('change', applyFiltersAndSort);
            if (sortSelect) sortSelect.addEventListener('change', applyFiltersAndSort);
            
            // Set up carousel controls
            setupCarouselControls();
        });

        // Function to setup carousel controls
        function setupCarouselControls() {
            const prevArrow = document.querySelector('.prev-arrow');
            const nextArrow = document.querySelector('.next-arrow');
            const indicators = document.querySelectorAll('.carousel-indicator');
            const featuredWrapper = document.querySelector('.featured-courses-wrapper');
            
            if (!prevArrow || !nextArrow || !featuredWrapper) return;
            
            let currentSlide = 0;
            const totalSlides = 2; // We have 2 slides
            
            // Function to update carousel position
            function updateCarouselPosition() {
                featuredWrapper.style.transform = `translateX(-${currentSlide * 100}%)`;
                
                // Update indicators
                indicators.forEach((indicator, index) => {
                    if (index === currentSlide) {
                        indicator.classList.add('active');
                    } else {
                        indicator.classList.remove('active');
                    }
                });
                
                // Update arrow states
                prevArrow.classList.toggle('active', currentSlide > 0);
                nextArrow.classList.toggle('active', currentSlide < totalSlides - 1);
            }
            
            // Previous arrow click handler
            prevArrow.addEventListener('click', () => {
                if (currentSlide > 0) {
                    currentSlide--;
                    updateCarouselPosition();
                }
            });
            
            // Next arrow click handler
            nextArrow.addEventListener('click', () => {
                if (currentSlide < totalSlides - 1) {
                    currentSlide++;
                    updateCarouselPosition();
                }
            });
            
            // Indicator click handlers
            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => {
                    currentSlide = index;
                    updateCarouselPosition();
                });
            });
            
            // Initialize carousel position
            updateCarouselPosition();
        }
    </script>
</body>
</html>