<?php
    // Start output buffering at the very beginning
    ob_start();
    session_start(); 
    
    // Database configuration
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "maslax_arts";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Process form submission
    $success_message = "";
    $error_message = "";
    $clearForm = false;

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_enrollment'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $message = $_POST['message'];
        
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $phone, $message);
        
        if ($stmt->execute()) {
            // Store success message in session
            $_SESSION['success_message'] = "Your enrollment request has been submitted successfully!";
            $_SESSION['form_submitted'] = true; // Flag to clear form
            
            // Close statement and connection before redirecting
            $stmt->close();
            $conn->close();
            
            // Redirect to avoid form resubmission on refresh
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $error_message = "Error: " . $stmt->error;
            $stmt->close();
        }
    }

    // Check for success message in session
    if (isset($_SESSION['success_message'])) {
        $success_message = $_SESSION['success_message'];
        $clearForm = isset($_SESSION['form_submitted']) ? $_SESSION['form_submitted'] : false;
        unset($_SESSION['success_message']);
        unset($_SESSION['form_submitted']);
    }

    // Now include the header after processing all PHP logic
    include_once 'Includes/header.php';
    
    // Close connection if it's still open
    if (isset($conn) && $conn) {
        $conn->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Maslax Arts</title>
    <link rel="stylesheet" href="./asset/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Display success/error messages -->
    <?php if (!empty($success_message)): ?>
        <div class="toast success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="toast error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-text">
            <h1>Contact Us</h1>
            <p>Help learners, growers & teachers find new opportunities & connections</p>
            <div class="hero-links">
                <a href="#">Home</a> >> <a href="#">Contact</a>
            </div>
        </div>
    </section>

    <!-- Contact Info & Form -->
    <section class="contact-section">
        <div class="contact-info">
            <h2>Get In Touch</h2>
            <p>We value your feedback and are here to assist you. Please use the contact information below or fill out the form to send us a direct message.</p>

            <div class="info-grid">
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <strong>Phone</strong>
                        <p>+252 61 123 4567</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <strong>Email</strong>
                        <p>info@maslaxarts.com</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <strong>Address</strong>
                        <p>Hodan District,<br> Mogadishu Somalia</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fab fa-instagram"></i>
                    <div>
                        <strong>Instagram</strong>
                        <p>@maslax.arts</p>
                    </div>
                </div>
            </div>

            <div class="social-icons">
                <h3>Follow Us</h3>
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>

        <div class="contact-form">
            <h2>Send Us a Message</h2>
            <form id="contactForm" method="POST" action="">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="Your full name" value="<?php echo (!$clearForm && isset($_POST['name'])) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    <div class="error-message" id="nameError">Please enter your name</div>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="your@email.com" value="<?php echo (!$clearForm && isset($_POST['email'])) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <div class="error-message" id="emailError">Please enter a valid email address</div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone (optional)</label>
                    <input type="text" id="phone" name="phone" placeholder="Your phone number" value="<?php echo (!$clearForm && isset($_POST['phone'])) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    <div class="error-message" id="phoneError">Please enter a valid phone number</div>
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" placeholder="How can we help you?"><?php echo (!$clearForm && isset($_POST['message'])) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    <div class="error-message" id="messageError">Please enter your message</div>
                </div>

                <button type="submit" name="submit_enrollment"><span>SUBMIT</span> <i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
        <h3>Find Us</h3>
        <div class="map-container">
            <iframe src="https://www.google.com/maps?q=Mogadishu&output=embed" allowfullscreen></iframe>
        </div>
    </section>

    <!-- Toast Notification -->
    <div id="toast" class="toast"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('contactForm');
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            const phoneInput = document.getElementById('phone');
            const messageInput = document.getElementById('message');
            const toast = document.getElementById('toast');
            
            // Check if we have a success message to show
            <?php if (!empty($success_message)): ?>
                showToast('<?php echo $success_message; ?>', true);
                // Clear form fields after successful submission
                form.reset();
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                showToast('<?php echo $error_message; ?>', false);
            <?php endif; ?>
            
            // Animation on page load
            function initAnimations() {
                // Animate hero text
                setTimeout(() => {
                    document.querySelector('.hero-text').classList.add('animate');
                }, 300);
                
                // Animate contact section
                setTimeout(() => {
                    document.querySelector('.contact-section').classList.add('animate');
                }, 800);
                
                // Animate map section
                setTimeout(() => {
                    document.querySelector('.map-section').classList.add('animate');
                }, 1200);
            }
            
            // Intersection Observer for scroll animations
            function initScrollAnimations() {
                const sections = document.querySelectorAll('.contact-section, .map-section');
                
                const observerOptions = {
                    threshold: 0.2,
                    rootMargin: '0px 0px -100px 0px'
                };
                
                const observer = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('animate');
                            observer.unobserve(entry.target);
                        }
                    });
                }, observerOptions);
                
                sections.forEach(section => {
                    observer.observe(section);
                });
            }
            
            // Show toast message
            function showToast(message, isSuccess) {
                toast.textContent = message;
                toast.className = isSuccess ? 'toast success' : 'toast error';
                
                setTimeout(() => {
                    toast.className = 'toast';
                }, 3000);
            }
            
            // Validate name
            function validateName() {
                const nameValue = nameInput.value.trim();
                const nameError = document.getElementById('nameError');
                
                if (nameValue === '') {
                    nameInput.parentElement.classList.add('error');
                    nameError.textContent = 'Name is required';
                    return false;
                } else if (nameValue.length < 2) {
                    nameInput.parentElement.classList.add('error');
                    nameError.textContent = 'Name must be at least 2 characters';
                    return false;
                } else {
                    nameInput.parentElement.classList.remove('error');
                    nameInput.parentElement.classList.add('success');
                    return true;
                }
            }
            
            // Validate email
            function validateEmail() {
                const emailValue = emailInput.value.trim();
                const emailError = document.getElementById('emailError');
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (emailValue === '') {
                    emailInput.parentElement.classList.add('error');
                    emailError.textContent = 'Email is required';
                    return false;
                } else if (!emailPattern.test(emailValue)) {
                    emailInput.parentElement.classList.add('error');
                    emailError.textContent = 'Please enter a valid email address';
                    return false;
                } else {
                    emailInput.parentElement.classList.remove('error');
                    emailInput.parentElement.classList.add('success');
                    return true;
                }
            }
            
            // Validate phone (optional)
            function validatePhone() {
                const phoneValue = phoneInput.value.trim();
                const phoneError = document.getElementById('phoneError');
                
                // If phone is empty, it's optional so valid
                if (phoneValue === '') {
                    phoneInput.parentElement.classList.remove('error');
                    return true;
                }
                
                // Basic phone validation - adjust pattern as needed
                const phonePattern = /^[+]?[\d\s\-\(\)]{10,}$/;
                
                if (!phonePattern.test(phoneValue)) {
                    phoneInput.parentElement.classList.add('error');
                    phoneError.textContent = 'Please enter a valid phone number';
                    return false;
                } else {
                    phoneInput.parentElement.classList.remove('error');
                    phoneInput.parentElement.classList.add('success');
                    return true;
                }
            }
            
            // Validate message
            function validateMessage() {
                const messageValue = messageInput.value.trim();
                const messageError = document.getElementById('messageError');
                
                if (messageValue === '') {
                    messageInput.parentElement.classList.add('error');
                    messageError.textContent = 'Message is required';
                    return false;
                } else if (messageValue.length < 10) {
                    messageInput.parentElement.classList.add('error');
                    messageError.textContent = 'Message must be at least 10 characters';
                    return false;
                } else {
                    messageInput.parentElement.classList.remove('error');
                    messageInput.parentElement.classList.add('success');
                    return true;
                }
            }
            
            // Real-time validation
            nameInput.addEventListener('blur', validateName);
            emailInput.addEventListener('blur', validateEmail);
            phoneInput.addEventListener('blur', validatePhone);
            messageInput.addEventListener('blur', validateMessage);
            
            // Form submission
            form.addEventListener('submit', function(e) {
                const isNameValid = validateName();
                const isEmailValid = validateEmail();
                const isPhoneValid = validatePhone();
                const isMessageValid = validateMessage();
                
                if (!(isNameValid && isEmailValid && isPhoneValid && isMessageValid)) {
                    e.preventDefault();
                    showToast('Please fix the errors in the form', false);
                }
                // If all valid, allow form to submit naturally
            });
            
            // Initialize animations
            initAnimations();
            initScrollAnimations();
        });
    </script>

    <?php
        include_once 'Includes/footer.php';
        // Clean (erase) the output buffer and turn off output buffering
        ob_end_flush();
    ?>
</body>
</html>