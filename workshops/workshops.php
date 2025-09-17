<?php
    session_start();

    // Database connection
    $servername = "localhost";
    $username = "root"; // Replace with your DB username
    $password = ""; // Replace with your DB password
    $dbname = "maslax_arts";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch workshops from database
    $sql = "SELECT * FROM workshops ORDER BY created_at DESC";
    $result = $conn->query($sql);

    $workshops = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $workshops[] = $row;
        }
    }

    // Process form submission
    $success_message = "";
    $error_message = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_enrollment'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $skill_level = $_POST['skill_level'];
        $workshop_name = $_POST['workshop_name'];
        $message = $_POST['message'];
        
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO workshops_request (name, email, phone, skill_level, workshop_name, message) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $email, $phone, $skill_level, $workshop_name, $message);
        
        if ($stmt->execute()) {
            // Store success message in session and redirect
            $_SESSION['success_message'] = "Your enrollment request has been submitted successfully!";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $error_message = "Error: " . $stmt->error;
        }
        
        $stmt->close();
    }

    // Check for success message in session
    if (isset($_SESSION['success_message'])) {
        $success_message = $_SESSION['success_message'];
        unset($_SESSION['success_message']); // Clear the message after displaying
    }

    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Maslah Arts Workshops</title>
    <link href="bootstrap_faq/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .navbar-center {
            flex: 1;
            margin-top: 10px;
            margin-bottom: -10px;
        }
        .footer h2, .footer h3 {
            color: #040007;
            margin-bottom: 16px;
            font-size: 20px;
            font-weight: bold;
        }
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 25px;
            border: 1px solid #888;
            width: 90%;
            max-width: 600px;
            max-height: 85vh; /* Limit height */
            border-radius: 8px;
            position: relative;
            overflow: hidden; /* Prevent scrolling of the modal itself */
            display: flex;
            flex-direction: column;
        }

        .modal-body {
            overflow-y: auto; /* Allow scrolling only for the form */
            max-height: calc(85vh - 120px); /* Calculate available space */
            padding-right: 5px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            right: 20px;
            top: 10px;
            z-index: 10;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
        }

        .submit-btn:hover {
            background-color: #45a049;
        }

        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .workshop-display {
            margin-bottom: 20px;
            padding: 12px;
            background-color: #f8f9fa;
            border-radius: 4px;
            font-size: 16px;
        }
        
        /* Custom scrollbar for modal */
        .modal-body::-webkit-scrollbar {
            width: 6px;
        }
        
        .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .modal-body::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        .modal-body::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body class="workshops_body">
    <?php include 'Includes/header.php';   // Header?>

    <section class="hero_workshops">
        <div class="hero-content">
            <h1>Maslah Arts Workshops</h1>
            <p>Unlock your creative potential through our diverse range of art workshops designed for all skill levels.</p>
            
            <div class="workshop-tabs">
                <!-- <div class="tab active"><i class="fas fa-calendar-plus"></i> Upcoming</div>
                <div class="tab"><i class="fas fa-paint-brush"></i> Current</div>
                <div class="tab"><i class="fas fa-history"></i> Archive</div> -->
                <button class="tab-btn active" data-filter="upcoming">
                        <i class="fas fa-calendar-plus"></i> Upcoming
                </button>
                <button class="tab-btn" data-filter="ongoing">
                    <i class="fas fa-paint-brush"></i> Current
                </button>
                <button class="tab-btn" data-filter="past">
                    <i class="fas fa-history"></i> Archive
                </button>
            </div>
        </div>
    </section>

    <div class="workshops_container_exactly">
        <div class="workshops_header">
            <h2 class="faq_h2">Our Workshops</h2>
        </div>
        
        <div class="workshop-grid" id="workshopContainer">
            <?php
            // Function to generate star ratings
            function generateStars($rating) {
                $starsHTML = '';
                $fullStars = floor($rating);
                $hasHalfStar = $rating - $fullStars >= 0.5;
                
                for ($i = 0; $i < $fullStars; $i++) {
                    $starsHTML .= '<i class="fas fa-star"></i>';
                }
                
                if ($hasHalfStar) {
                    $starsHTML .= '<i class="fas fa-star-half-alt"></i>';
                }
                
                $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                for ($i = 0; $i < $emptyStars; $i++) {
                    $starsHTML .= '<i class="far fa-star"></i>';
                }
                
                return $starsHTML;
            }
            
            // Function to get category icon
            function getCategoryIcon($category) {
                $icons = array(
                    'Drawing' => 'pencil-alt',
                    'Painting' => 'paint-brush',
                    'Sketching' => 'city',
                    'Portraits' => 'portrait',
                    'Figure Drawing' => 'user',
                    'Digital Art' => 'tablet-alt'
                );
                
                return isset($icons[$category]) ? $icons[$category] : 'pencil-alt';
            }
            
            // Function to generate badge HTML
            function generateBadge($badgeType) {
                if (!$badgeType || $badgeType === 'none') return '';
                
                $badgeText = array(
                    'trending' => '<i class="fas fa-star"></i> Featured',
                    'popular' => '<i class="fas fa-users"></i> Popular',
                    'limited' => '<i class="fas fa-user-clock"></i> Limited Seats',
                    'new' => '<i class="fas fa-video"></i> Recording Available'
                );
                
                return '<span class="workshop-badge badge-' . $badgeType . '">' . $badgeText[$badgeType] . '</span>';
            }
            
            // Function to determine button text based on status
            function getButtonText($status) {
                if ($status === 'upcoming') {
                    return '<i class="fas fa-pen-fancy"></i> Enroll Now';
                } else if ($status === 'ongoing') {
                    return '<i class="fas fa-user-plus"></i> Join Now';
                } else {
                    return '<i class="fas fa-play-circle"></i> Watch Now';
                }
            }
            
            if (count($workshops) > 0) {
                foreach ($workshops as $workshop) {
                    // FIX: Use correct path to images
                    $image_path = '../admin/' . $workshop['thumbnail_url'];
                    
                    echo '<div class="workshop-card ' . ($workshop['status'] === 'upcoming' ? 'active' : '') . '" data-category="' . $workshop['status'] . '">';
                    echo '    <div class="workshop-thumbnail-container">';
                    echo '        <img src="' . $image_path . '" alt="' . htmlspecialchars($workshop['title']) . '" class="workshop-thumbnail">';
                    echo          generateBadge($workshop['badge_type']);
                    echo '    </div>';
                    echo '    <div class="workshop-content">';
                    echo '        <h3 class="workshop-title">' . htmlspecialchars($workshop['title']) . '</h3>';
                    echo '        <div class="workshop-description collapsed">';
                    echo '            ' . htmlspecialchars($workshop['description']);
                    echo '            <button class="read-more-btn">';
                    echo '                Read more <i class="fas fa-chevron-down"></i>';
                    echo '            </button>';
                    echo '        </div>';
                    echo '        <div class="workshop-meta">';
                    echo '            <img src="maslah.jpg" alt="Instructor" class="instructor-avatar">';
                    echo '            <div class="instructor-info">';
                    echo '                <div class="instructor-name">' . htmlspecialchars($workshop['instructor_name']) . '</div>';
                    echo '                <div class="instructor-role">' . htmlspecialchars($workshop['instructor_role']) . '</div>';
                    echo '            </div>';
                    echo '        </div>';
                    echo '        <div class="workshop-details">';
                    echo '            <div class="workshop-rating">';
                    echo '                <div class="stars">';
                    echo                   generateStars($workshop['rating']);
                    echo '                </div>';
                    echo '                <span class="rating-value">' . $workshop['rating'] . '</span>';
                    echo '            </div>';
                    echo '            <span class="workshop-category">';
                    echo '                <i class="fas fa-' . getCategoryIcon($workshop['category']) . '"></i> ' . $workshop['category'];
                    echo '            </span>';
                    echo '        </div>';
                    echo '        <div class="workshop-footer">';
                    echo '            <span class="difficulty-badge ' . strtolower($workshop['difficulty']) . '">' . $workshop['difficulty'] . '</span>';
                    echo '            <span class="workshop-price ' . ($workshop['price'] == 0 ? 'price-free' : '') . '">' . ($workshop['price'] == 0 ? 'Free' : '$' . $workshop['price']) . '</span>';
                    echo '        </div>';
                    echo '        <button class="join-btn" data-workshop="' . htmlspecialchars($workshop['title']) . '">';
                    echo           getButtonText($workshop['status']);
                    echo '        </button>';
                    echo '    </div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="error">No workshops found in the database.</div>';
            }
            ?>
        </div>
    </div>

    <div class="workshops_benefits">
        <section class="benefits-container">
            <h2 class="benefits-title">Workshop Benefits</h2>
            <p></p><p></p>
            <div class="benefits-grid">
                <div class="benefit-box">
                    <i class="fas fa-chalkboard-teacher benefit-icon"></i>
                    <h3 class="benefit-title">Expert Guidance</h3>
                    <p class="benefit-description">Learn directly from industry professionals with years of practical experience in their fields.</p>
                </div>

                <div class="benefit-box">
                    <i class="fas fa-hands-helping benefit-icon"></i>
                    <h3 class="benefit-title">Hands-on Practice</h3>
                    <p class="benefit-description">Gain practical skills through interactive exercises and real-world applications.</p>
                </div>
                
                <div class="benefit-box">
                    <i class="fas fa-users benefit-icon"></i>
                    <h3 class="benefit-title">Networking</h3>
                    <p class="benefit-description">Connect with like-minded professionals and expand your professional network.</p>
                </div>
                
                <div class="benefit-box">
                    <i class="fas fa-lightbulb benefit-icon"></i>
                    <h3 class="benefit-title">Innovative Ideas</h3>
                    <p class="benefit-description">Spark creativity and discover new approaches to solving complex problems.</p>
                </div>
                
                <div class="benefit-box">
                    <i class="fas fa-certificate benefit-icon"></i>
                    <h3 class="benefit-title">Certification</h3>
                    <p class="benefit-description">Earn a recognized certificate to validate your newly acquired skills.</p>
                </div>
                
                <div class="benefit-box">
                    <i class="fas fa-briefcase benefit-icon"></i>
                    <h3 class="benefit-title">Career Advancement</h3>
                    <p class="benefit-description">Enhance your resume and improve your career prospects with valuable skills.</p>
                </div>

                <div class="benefit-box">
                    <i class="fas fa-clock benefit-icon"></i>
                    <h3 class="benefit-title">Time Efficiency</h3>
                    <p class="benefit-description">Learn concentrated knowledge in short sessions designed for maximum impact.</p>
                </div>

                <div class="benefit-box">
                    <i class="fas fa-comments benefit-icon"></i>
                    <h3 class="benefit-title">Interactive Q&A</h3>
                    <p class="benefit-description">Get your specific questions answered in live discussions with experts.</p>
                </div>
            </div>
        </section>
    </div>

    <div class="f_asked faq-9 faq Faq-section light-background" id="faq">
      <div class="container">
        <div class="faq-header">
          <h2 class="faq_h2">FAQ</h2>
        </div>
        <div class="row">

          <div class="col-lg-5" data-aos="fade-up">
            <h2 class="faq-title">Have a question? Check out the FAQ</h2>
            <p class="faq-description">Maecenas tempus tellus eget condimentum rhoncus sem quam semper libero sit amet adipiscing sem neque sed ipsum.</p>
            <div class="faq-arrow d-none d-lg-block" data-aos="fade-up" data-aos-delay="200">
              <svg class="faq-arrow" width="200" height="211" viewBox="0 0 200 211" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M198.804 194.488C189.279 189.596 179.529 185.52 169.407 182.07L169.384 182.049C169.227 181.994 169.07 181.939 168.912 181.884C166.669 181.139 165.906 184.546 167.669 185.615C174.053 189.473 182.761 191.837 189.146 195.695C156.603 195.912 119.781 196.591 91.266 179.049C62.5221 161.368 48.1094 130.695 56.934 98.891C84.5539 98.7247 112.556 84.0176 129.508 62.667C136.396 53.9724 146.193 35.1448 129.773 30.2717C114.292 25.6624 93.7109 41.8875 83.1971 51.3147C70.1109 63.039 59.63 78.433 54.2039 95.0087C52.1221 94.9842 50.0776 94.8683 48.0703 94.6608C30.1803 92.8027 11.2197 83.6338 5.44902 65.1074C-1.88449 41.5699 14.4994 19.0183 27.9202 1.56641C28.6411 0.625793 27.2862 -0.561638 26.5419 0.358501C13.4588 16.4098 -0.221091 34.5242 0.896608 56.5659C1.8218 74.6941 14.221 87.9401 30.4121 94.2058C37.7076 97.0203 45.3454 98.5003 53.0334 98.8449C47.8679 117.532 49.2961 137.487 60.7729 155.283C87.7615 197.081 139.616 201.147 184.786 201.155L174.332 206.827C172.119 208.033 174.345 211.287 176.537 210.105C182.06 207.125 187.582 204.122 193.084 201.144C193.346 201.147 195.161 199.887 195.423 199.868C197.08 198.548 193.084 201.144 195.528 199.81C196.688 199.192 197.846 198.552 199.006 197.935C200.397 197.167 200.007 195.087 198.804 194.488ZM60.8213 88.0427C67.6894 72.648 78.8538 59.1566 92.1207 49.0388C98.8475 43.9065 106.334 39.2953 114.188 36.1439C117.295 34.8947 120.798 33.6609 124.168 33.635C134.365 33.5511 136.354 42.9911 132.638 51.031C120.47 77.4222 86.8639 93.9837 58.0983 94.9666C58.8971 92.6666 59.783 90.3603 60.8213 88.0427Z" fill="currentColor"></path>
              </svg>
            </div>
          </div>

          <div class="col-lg-7" data-aos="fade-up" data-aos-delay="300">
            <div class="faq-container">

              <div class="faq-item faq-active">
                <h3 class="faq_h3"><i class="question_mark_icon fa-regular fa-circle-question"></i> Non consectetur a erat nam at lectus urna duis?</h3>
                <div class="faq-content">
                  <p>Feugiat pretium nibh ipsum consequat. Tempus iaculis urna id volutpat lacus laoreet non curabitur gravida. Venenatis lectus magna fringilla urna porttitor rhoncus dolor purus non.</p>
                </div>
                <i class="faq-toggle fa-solid fa-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3 class="faq_h3"><i class="question_mark_icon fa-regular fa-circle-question"></i> Feugiat scelerisque varius morbi enim nunc faucibus?</h3>
                <div class="faq-content">
                  <p>Dolor sit amet consectetur adipiscing elit pellentesque habitant morbi. Id interdum velit laoreet id donec ultrices. Fringilla phasellus faucibus scelerisque eleifend donec pretium. Est pellentesque elit ullamcorper dignissim. Mauris ultrices eros in cursus turpis massa tincidunt dui.</p>
                </div>
                <i class="faq-toggle fa-solid fa-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3 class="faq_h3"><i class="question_mark_icon fa-regular fa-circle-question"></i> Dolor sit amet consectetur adipiscing elit pellentesque?</h3>
                <div class="faq-content">
                  <p>Eleifend mi in nulla posuere sollicitudin aliquam ultrices sagittis orci. Faucibus pulvinar elementum integer enim. Sem nulla pharetra diam sit amet nisl suscipit. Rutrum tellus pellentesque eu tincidunt. Lectus urna duis convallis convallis tellus. Urna molestie at elementum eu facilisis sed odio morbi quis</p>
                </div>
                <i class="faq-toggle fa-solid fa-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3 class="faq_h3"><i class="question_mark_icon fa-regular fa-circle-question"></i> Ac odio tempor orci dapibus. Aliquam eleifend mi in nulla?</h3>
                <div class="faq-content">
                  <p>Dolor sit amet consectetur adipiscing elit pellentesque habitant morbi. Id interdum velit laoreet id donec ultrices. Fringilla phasellus faucibus scelerisque eleifend donec pretium. Est pellentesque elit ullamcorper dignissim. Mauris ultrices eros in cursus turpis massa tincidunt dui.</p>
                </div>
                <i class="faq-toggle fa-solid fa-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3 class="faq_h3"><i class="question_mark_icon fa-regular fa-circle-question"></i> Tempus quam pellentesque nec nam aliquam sem et tortor?</h3>
                <div class="faq-content">
                  <p>Molestie a iaculis at erat pellentesque adipiscing commodo. Dignissim suspendisse in est ante in. Nunc vel risus commodo viverra maecenas accumsan. Sit amet nisl suscipit adipiscing bibendum est. Purus gravida quis blandit turpis cursus in</p>
                </div>
                <i class="faq-toggle fa-solid fa-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3 class="faq_h3"><i class="question_mark_icon fa-regular fa-circle-question"></i> Perspiciatis quod quo quos nulla quo illum ullam?</h3>
                <div class="faq-content">
                  <p>Enim ea facilis quaerat voluptas quidem et dolorem. Quis et consequatur non sed in suscipit sequi. Distinctio ipsam dolore et.</p>
                </div>
                <i class="faq-toggle fa-solid fa-chevron-right"></i>
              </div><!-- End Faq item-->

            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Enrollment Modal -->
    <div id="enrollmentModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Enroll in Workshop</h2>
            
            <div class="workshop-display">
                You're enrolling in: <strong id="selectedWorkshopName">Workshop Name</strong>
            </div>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <div class="modal-body">
                <form id="enrollmentForm" method="POST" action="">
                    <input type="hidden" id="workshop_name" name="workshop_name" value="">
                    
                    <div class="form-group">
                        <label for="name">Full Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                    
                    <div class="form-group">
                        <label for="skill_level">Skill Level:</label>
                        <select id="skill_level" name="skill_level" required>
                            <option value="">Select your skill level</option>
                            <option value="Beginner">Beginner</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Advanced">Advanced</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message (Optional):</label>
                        <textarea id="message" name="message" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" name="submit_enrollment" class="submit-btn">Submit Enrollment</button>
                </form>
            </div>
        </div>
    </div>

    <?php include 'Includes/footer.php'; ?>   <!-- Footer -->
    
    <script src="assets/js/script.js"></script>
    <script src="bootstrap_faq/aos.js"></script>

    <script>
        // Add event listeners for read more buttons
        document.addEventListener('DOMContentLoaded', function() {
            const readMoreButtons = document.querySelectorAll('.read-more-btn');
            readMoreButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const description = this.parentElement;
                    description.classList.toggle('collapsed');
                    
                    if (description.classList.contains('collapsed')) {
                        this.innerHTML = 'Read more <i class="fas fa-chevron-down"></i>';
                    } else {
                        this.innerHTML = 'Read less <i class="fas fa-chevron-up"></i>';
                    }
                });
            });
            
            // Get the modal
            var modal = document.getElementById("enrollmentModal");
            
            // Get all buttons that open the modal
            var buttons = document.querySelectorAll(".join-btn");
            
            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[0];
            
            // Get the hidden workshop name input and display element
            var workshopNameInput = document.getElementById("workshop_name");
            var workshopNameDisplay = document.getElementById("selectedWorkshopName");
            
            // When the user clicks a button, open the modal 
            buttons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var workshopName = this.getAttribute("data-workshop");
                    workshopNameInput.value = workshopName;
                    workshopNameDisplay.textContent = workshopName;
                    modal.style.display = "block";
                    document.body.style.overflow = "hidden"; // Prevent background scrolling
                });
            });
            
            // When the user clicks on <span> (x), close the modal
            span.onclick = function() {
                modal.style.display = "none";
                document.body.style.overflow = "auto"; // Re-enable scrolling
            }
            
            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                    document.body.style.overflow = "auto"; // Re-enable scrolling
                }
            }
            
            // Close modal on form submit
            document.getElementById('enrollmentForm').addEventListener('submit', function() {
                setTimeout(function() {
                    modal.style.display = "none";
                    document.body.style.overflow = "auto";
                }, 1000);
            });
        });
    </script>
</body>
</html>