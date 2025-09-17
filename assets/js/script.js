document.addEventListener('DOMContentLoaded', function() {
    // Mobile Navigation
    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
        
        // Close mobile menu when clicking a link
        const navLinks = document.querySelectorAll('.nav-item a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (navMenu.classList.contains('active')) {
                    menuToggle.classList.remove('active');
                    navMenu.classList.remove('active');
                }
            });
        });
    }

    // Hero Background Slideshow
    const heroSlides = document.querySelectorAll('.hero-bg-slide');
    if (heroSlides.length > 0) {
        let currentSlide = 0;
        
        function showSlide(index) {
            heroSlides.forEach(slide => slide.classList.remove('active'));
            heroSlides[index].classList.add('active');
        }
        
        function nextSlide() {
            currentSlide = (currentSlide + 1) % heroSlides.length;
            showSlide(currentSlide);
        }
        
        setInterval(nextSlide, 5000);
        showSlide(0);
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Form validation
    const contactForm = document.querySelector('.contact form');
    if (contactForm) {
          contactForm.addEventListener('submit', function(e) {
            const username = document.getElementById('username');
            const email = document.getElementById('email');
            const message = document.getElementById('message');
            let isValid = true;
            
            // Reset error states
            [username, email, message].forEach(field => {
                field.classList.remove('error');
                const errorMsg = field.nextElementSibling;
                if (errorMsg && errorMsg.classList.contains('error-message')) {
                    errorMsg.remove();
                }
            });
            
            // Validate username
            if (!username.value.trim()) {
                username.classList.add('error');
                showError(username, 'Username is required');
                isValid = false;
            }
            
            // Validate email
            if (!email.value.trim()) {
                email.classList.add('error');
                showError(email, 'Email is required');
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                email.classList.add('error');
                showError(email, 'Please enter a valid email');
                isValid = false;
            }
            
            // Validate message
            if (!message.value.trim()) {
                message.classList.add('error');
                showError(message, 'Message is required');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        // Real-time validation
        const inputs = contactForm.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });
        
        function validateField(field) {
            if (field.id === 'username') {
                if (!field.value.trim()) {
                    field.classList.add('error');
                    showError(field, 'Username is required');
                    return false;
                }
            } else if (field.id === 'email') {
                if (!field.value.trim()) {
                    field.classList.add('error');
                    showError(field, 'Email is required');
                    return false;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
                    field.classList.add('error');
                    showError(field, 'Please enter a valid email');
                    return false;
                }
            } else if (field.id === 'message') {
                if (!field.value.trim()) {
                    field.classList.add('error');
                    showError(field, 'Message is required');
                    return false;
                }
            }
            
            // If valid, remove error
            field.classList.remove('error');
            const errorMsg = field.nextElementSibling;
            if (errorMsg && errorMsg.classList.contains('error-message')) {
                errorMsg.remove();
            }
            return true;
        }
        
        function showError(field, message) {
            if (field.nextElementSibling && field.nextElementSibling.classList.contains('error-message')) {
                return;
            }
            
            const errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            errorElement.style.color = '#ff0000';
            errorElement.style.fontSize = '0.9rem';
            errorElement.style.marginTop = '8px';
            errorElement.style.marginLeft = '10px';
            errorElement.textContent = message;
            field.parentNode.insertBefore(errorElement, field.nextSibling);
        }
    }
    // ... (keep your existing form validation code)
    

    // Improved Animation for Featured Cards and Event Cards
    const animateCardsOnScroll = function() {
        const cards = document.querySelectorAll('.featured-card, .event-card');
        const windowHeight = window.innerHeight;
        const triggerOffset = 150; // Pixels from bottom of viewport
        
        cards.forEach(card => {
            const cardPosition = card.getBoundingClientRect().top;
            
            if (cardPosition < windowHeight - triggerOffset) {
                // Add animation class
                card.classList.add('animate-in');
            }
        });
    };

    // Initialize animation styles
    const style = document.createElement('style');
    style.textContent = `
        .featured-card, .event-card {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }
        .featured-card.animate-in, 
        .event-card.animate-in {
            opacity: 1;
            transform: translateY(0);
        }
    `;
    document.head.appendChild(style);

    // Set up event listeners
    window.addEventListener('load', animateCardsOnScroll);
    window.addEventListener('scroll', animateCardsOnScroll);
    animateCardsOnScroll(); // Run once on page load
});

const scrollContainer = document.getElementById("testimonialScroll");
const scrollLeftBtn = document.getElementById("scrollLeft");
const scrollRightBtn = document.getElementById("scrollRight");

// Scroll left
scrollLeftBtn.addEventListener("click", () => {
    scrollContainer.scrollBy({
        left: -300,
        behavior: "smooth"
    });
});

// Scroll right
scrollRightBtn.addEventListener("click", () => {
    scrollContainer.scrollBy({
        left: 300,
        behavior: "smooth"
    });
});

// Hide arrows when at ends
scrollContainer.addEventListener("scroll", () => {
    const maxScroll = scrollContainer.scrollWidth - scrollContainer.clientWidth;
    
    if (scrollContainer.scrollLeft <= 10) {
        scrollLeftBtn.style.opacity = "0.5";
        scrollLeftBtn.style.cursor = "not-allowed";
    } else {
        scrollLeftBtn.style.opacity = "1";
        scrollLeftBtn.style.cursor = "pointer";
    }
    
    if (scrollContainer.scrollLeft >= maxScroll - 10) {
        scrollRightBtn.style.opacity = "0.5";
        scrollRightBtn.style.cursor = "not-allowed";
    } else {
        scrollRightBtn.style.opacity = "1";
        scrollRightBtn.style.cursor = "pointer";
    }
});

const modeToggle = document.getElementById('mode-toggle');
const languageSelect = document.getElementById('language-select');
const heroText = document.getElementById('hero-text');

// Mode change toggle
modeToggle.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');
    modeToggle.textContent = document.body.classList.contains('dark-mode') ? '☀️' : '🌙';
});

// Language change
languageSelect.addEventListener('change', () => {
    const lang = languageSelect.value;
    if (lang === 'en') {
        heroText.innerHTML = `
            <h1>Maslah Arts is Where Creativity Thrives</h1>
            <p>Connecting artists, learners, and communities through inspiring exhibitions, hands-on workshops, and cultural experiences that celebrate the power of art.</p>
            <div class="hero-buttons">
                <a href="#" class="btn btn-primary">Victory Carlseny</a>
                <a href="#" class="btn btn-secondary">John Ellis</a>
            </div>
        `;
    } else if (lang === 'so') {
        heroText.innerHTML = `
            <h1>Maslah Arts Waa Meesha Fankoodu Koraayo</h1>
            <p>Waxaan isku xirnaa farshaxan yahannada, barayaasha, iyo bulshada si aan u abuurno bandhigyo dhiirigelin leh iyo tababaro wax-ku-ool ah.</p>
            <div class="hero-buttons">
                <a href="#" class="btn btn-primary">Victory Carlseny</a>
                <a href="#" class="btn btn-secondary">John Ellis</a>
            </div>
        `;
    } else if (lang === 'ar') {
        heroText.innerHTML = `
            <h1>ماسلا آرتس هو المكان الذي يزدهر فيه الإبداع</h1>
            <p>نربط الفنانين والمتعلمين والمجتمعات من خلال معارض ملهمة وورش عمل عملية وتجارب ثقافية تحتفي بقوة الفن.</p>
            <div class="hero-buttons">
                <a href="#" class="btn btn-primary">Victory Carlseny</a>
                <a href="#" class="btn btn-secondary">John Ellis</a>
            </div>
        `;
    }
});