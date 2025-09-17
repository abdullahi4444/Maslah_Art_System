<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- CHANGE 1: ADD 'url' TO EACH COURSE POINTING TO ITS SPECIFIC PAGE ---
// This array now includes the direct link to each course's detail page.
$courses = [
    [
       'id' => 1, 'title' => "Watercolor Florals", 'category' => "painting", 'level' => "beginner", 'price' => 39, 'originalPrice' => 49, 'reviews' => 86, 'rating' => 4.0, 'image' => "assets/image/img-2.png", 'instructor' => "Ahmed", 'tags' => ["Certificate"], 'badge' => "discount", 'badgeText' => "20% OFF",
       'url' => 'Modern Digital Painting.php' // Link to the specific page
    ],
    [
      'id' => 2, 'title' => "Portrait Drawing with Charcoal", 'category' => "painting", 'level' => "intermediate", 'price' => 55, 'reviews' => 152, 'rating' => 4.7, 'image' => "assets/Image/Portrait Drawing1.jpg", 'instructor' => "Farah", 'tags' => ["Self-paced"],
      'url' => 'Portrait Drawing with Charcoal.php' // Link to the specific page
    ],
    [
      'id' => 3, 'title' => "Abstract Art Techniques", 'category' => "painting", 'level' => "advanced", 'price' => 69, 'originalPrice' => 99, 'reviews' => 78, 'rating' => 5.0, 'image' => "assets/Image/Abstract.jpg", 'instructor' => "Cali", 'tags' => ["Certificate"], 'badge' => "discount", 'badgeText' => "30% OFF",
      'url' => 'Modern Digital Painting.php' // Link to the specific page
    ],
    [
      'id' => 4, 'title' => "Graphic Design Fundamentals", 'category' => "design", 'level' => "beginner", 'price' => 45, 'reviews' => 124, 'rating' => 4.2, 'image' => "assets/Image/img-3.png", 'instructor' => "Diiriye", 'tags' => ["Self-paced"],
      'url' => 'Modern Digital Painting.php'
    ],
    [
      'id' => 5, 'title' => "Character Animation", 'category' => "animation", 'level' => "intermediate", 'price' => 84, 'originalPrice' => 99, 'reviews' => 92, 'rating' => 4.6, 'image' => "assets/Image/anime.jpg", 'instructor' => "Maslah", 'tags' => ["Certificate"], 'badge' => "discount", 'badgeText' => "15% OFF",
      'url' => 'Modern Digital Painting.php'
    ],
    [
      'id' => 6, 'title' => "Modern Calligraphy", 'category' => "design", 'level' => "all Levels", 'price' => 35, 'reviews' => 156, 'rating' => 4.9, 'image' => "assets/Image/ModernC.jpg", 'instructor' => "Sophia", 'tags' => ["Self-paced"],
      'url' => 'Modern Calligraphy.php' // This is the page you already created
    ],
    [
      'id' => 7, 'title' => "Oil Painting Masterclass", 'category' => "Intermediate", 'level' => "intermediate", 'price' => 79, 'reviews' => 94, 'rating' => 5, 'image' => "https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=350&q=80", 'instructor' => "Muuse", 'tags' => ["Live Classes"],
      'url' => 'Oil Painting Masterclass.php'
    ],
    [
      'id' => 8, 'title' => "Modern Digital Painting", 'category' => "Beginner", 'level' => "intermediate", 'price' => 49, 'reviews' => 128, 'rating' => 4.5, 'image' => "assets/Image/img-1.png", 'instructor' => "Mohamed", 'tags' => ["Self-paced"],
      'url' => 'Modern Digital Painting.php' // This is the page you already created
    ],
    [
      'id' => 9, 'title' => "Character Design Fundamentals", 'category' => "design", 'level' => "beginner", 'price' => 69.99, 'reviews' => 110, 'rating' => 5.0, 'image' => "assets/Image/char.jpg", 'instructor' => "Aisha", 'tags' => ["Certificate"],
      'url' => 'Modern Digital Painting.php'
    ],
    [
      'id' => 10, 'title' => "Landscape Painting Masterclass", 'category' => "digital", 'level' => "advanced", 'price' => 89.99, 'reviews' => 95, 'rating' => 4.7, 'image' => "assets/Image/land.jpg", 'instructor' => "Hassan", 'tags' => ["Live Classes"],
      'url' => 'Modern Digital Painting.php'
    ],
    [
      'id' => 11, 'title' => "Concept Art for Games", 'category' => "digital", 'level' => "advanced", 'price' => 99.99, 'reviews' => 88, 'rating' => 4.2, 'image' => "assets/Image/game.jpg", 'instructor' => "Yusuf", 'tags' => ["Certificate"],
      'url' => 'Modern Digital Painting.php'
    ],
    [
      'id' => 12, 'title' => "Introduction to Pottery", 'category' => "digital", 'level' => "advanced", 'price' => 65, 'reviews' => 56, 'rating' => 4.0, 'image' => "assets/Image/Pottery.jpg", 'instructor' => "Maria", 'tags' => ["Certificate"],
      'url' => 'Modern Digital Painting.php'
    ],
];

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
                <!-- Slide 1 -->
                <div class="featured-courses-slide" id="slide1">
                    <div class="course-card" data-category="digital" data-level="beginner" data-price="49">
                        <div class="card-image">
                            <img src="assets/Image/img-1.png">
                            <span class="badge featured">Featured</span>
                        </div>
                        <div class="card-content">
                            <div class="tags"><span class="tag level-beginner">Beginner</span><span class="tag">Certificate</span><span class="tag2">Self-paced</span></div>
                            <h3>Modern Digital Painting</h3><p class="instructor">By Mohamed</p>
                            <div class="rating"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i> 4.5 (128 reviews)</div>
                            <div class="price-view"><span class="price">$49</span><a href="Modern Digital Painting.php" class="btn-view">View Course</a></div>
                        </div>
                    </div>
                    <div class="course-card" data-category="painting" data-level="intermediate" data-price="79">
                        <div class="card-image">
                            <img src="assets/Image/Oil Painting.jpeg" alt="Oil Painting Masterclass">
                            <span class="badge featured">Featured</span>
                        </div>
                        <div class="card-content">
                            <div class="tags"><span class="tag level-intermediate">Intermediate</span><span class="tag">Certificate</span><span class="tag2">Live Classes</span></div>
                            <h3>Oil Painting Masterclass</h3><p class="instructor">By Muuse</p>
                            <div class="rating"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i> 5.0 (94 reviews)</div>
                            <div class="price-view"><span class="price">$79</span><a href="Oil Painting Masterclass.php" class="btn-view">View Course</a></div>
                        </div>
                    </div>
                    <div class="course-card" data-category="pottery" data-level="all" data-price="65">
                        <div class="card-image">
                            <img src="assets/Image/Pottery.jpg" alt="Introduction to Pottery">
                            <span class="badge featured">Featured</span>
                        </div>
                        <div class="card-content">
                            <div class="tags"><span class="tag level-all">All Levels</span><span class="tag">Certificate</span><span class="tagn">new</span></div>
                            <h3>Introduction to Pottery</h3><p class="instructor">By Maria</p>
                            <div class="rating"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i> 4.0 (56 reviews)</div>
                            <div class="price-view"><span class="price">$65</span><a href="Introduction to Pottery.php" class="btn-view">View Course</a></div>
                        </div>
                    </div>
                </div>
                <!-- Slide 2 -->
                <div class="featured-courses-slide" id="slide2">
                     <div class="course-card" data-category="design" data-level="intermediate" data-price="55">
                            <div class="card-image">
                                <img src="assets/Image/Portrait Drawing.jpg" alt="Photography">
                                <span class="badge featured">Featured</span>
                            </div>
                            <div class="card-content">
                                <div class="tags"><span class="tag level-intermediate">Intermediate</span><span class="tag">Certificate</span></div>
                                <h3>Portrait Drawing with Charcoal</h3><p class="instructor">By Farah</p>
                                <div class="rating"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i> 4.7 (152 reviews)</div>
                                <div class="price-view"><span class="price">$55</span><a href="Portrait Drawing with Charcoal.php" class="btn-view">View Course</a></div>
                            </div>
                        </div>
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
                            <span class="tag level-<?= htmlspecialchars($course['level']) ?>"><?= ucfirst(htmlspecialchars($course['level'])) ?></span>
                            <?php if (!empty($course['tags'])): ?>
                                <?php foreach ($course['tags'] as $tag): 
                                    $tagClass = ($tag === 'Self-paced' || $tag === 'Live Classes') ? 'tag2' : 'tag';
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
                            <!-- --- CHANGE 2: USE THE 'url' FROM THE ARRAY FOR THE LINK --- -->
                            <a href="<?= htmlspecialchars($course['url']) ?>" class="btn-view">View Course</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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
</body>
</html>