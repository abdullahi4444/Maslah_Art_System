
document.addEventListener('DOMContentLoaded', function() {
    // Tab filtering functionality
    const tabButtons = document.querySelectorAll('.tab-btn');
    const workshopCards = document.querySelectorAll('.workshop-card');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all buttons
            tabButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            button.classList.add('active');
            
            const filter = button.getAttribute('data-filter');
            
            // Filter workshop cards
            workshopCards.forEach(card => {
                card.classList.remove('active');
                
                if (card.getAttribute('data-category') === filter) {
                    card.classList.add('active');
                }
            });
        });
    });
    
    // Read more functionality
    const readMoreButtons = document.querySelectorAll('.read-more-btn');
    
    readMoreButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const description = button.parentElement;
            
            if (description.classList.contains('collapsed')) {
                description.classList.remove('collapsed');
                description.classList.add('expanded');
                button.innerHTML = 'Read less <i class="fas fa-chevron-up"></i>';
                button.classList.add('expanded');
            } else {
                description.classList.remove('expanded');
                description.classList.add('collapsed');
                button.innerHTML = 'Read more <i class="fas fa-chevron-down"></i>';
                button.classList.remove('expanded');
            }
        });
    });
    
    // See All button functionality
    const seeAllBtn = document.querySelector('.see-all-btn');
    seeAllBtn.addEventListener('click', () => {
        workshopCards.forEach(card => {
            card.classList.add('active');
        });
        
        tabButtons.forEach(btn => btn.classList.remove('active'));
    });
    
    // Card hover animations
    workshopCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-8px)';
            card.style.boxShadow = '0 20px 25px rgba(0,0,0,0.1)';
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
            card.style.boxShadow = '';
        });
    });
    
    // Initialize all descriptions as collapsed
    const descriptions = document.querySelectorAll('.workshop-description');
    descriptions.forEach(desc => {
        if (!desc.classList.contains('collapsed') && !desc.classList.contains('expanded')) {
            desc.classList.add('collapsed');
        }
    });
});

// FAQ accordion functionality
const questions = document.querySelectorAll('.faq-question');

questions.forEach(question => {
question.addEventListener('click', () => {
    const currentItem = question.parentNode;
    currentItem.classList.toggle('active');

    // Close others
    questions.forEach(q => {
    const item = q.parentNode;
    if (item !== currentItem) item.classList.remove('active');
    });
});
});

//faq answer toggle
(function() {
  //Animation on scroll function and init
  function aosInit() {
    AOS.init({
      duration: 600,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });
  }
  window.addEventListener('load', aosInit);

  //Frequently Asked Questions Toggle
  document.querySelectorAll('.faq-item h3, .faq-item .faq-toggle').forEach((faqItem) => {
    faqItem.addEventListener('click', () => {
      faqItem.parentNode.classList.toggle('faq-active');
    });
  });

})();

// benefits of workshops
document.addEventListener('DOMContentLoaded', function() {
    const benefitBoxes = document.querySelectorAll('.benefit-box');
    
    // Add animation delay to each box
    benefitBoxes.forEach((box, index) => {
        box.style.transitionDelay = `${index * 0.1}s`;
        
        // Add click effect
        box.addEventListener('click', function() {
            this.style.transform = 'translateY(-5px)';
            setTimeout(() => {
                this.style.transform = 'translateY(-10px)';
            }, 200);
        });
    });
});



















//model section
// Updated JavaScript for automatic WhatsApp submission
document.addEventListener('DOMContentLoaded', function() {
  const enrollButtons = document.querySelectorAll('.join-btn');
  const enrollPopup = document.getElementById('enrollPopup');
  const closePopup = document.querySelector('.close-popup');
  const workshopNameInput = document.getElementById('popupWorkshopName');
  const enrollForm = document.getElementById('enrollForm');
  
  // Your WhatsApp number (with country code but without + or spaces)
  const whatsappNumber = '252613667595';
  
  // Open popup when enroll button is clicked
  enrollButtons.forEach(button => {
    button.addEventListener('click', function() {
      const workshopCard = this.closest('.workshop-card');
      const workshopTitle = workshopCard.querySelector('.workshop-title').textContent.trim();
      
      workshopNameInput.value = workshopTitle;
      enrollPopup.classList.add('active');
      document.body.style.overflow = 'hidden';
    });
  });
  
  // Close popup
  closePopup.addEventListener('click', function() {
    enrollPopup.classList.remove('active');
    document.body.style.overflow = '';
  });
  
  // Close when clicking outside
  enrollPopup.addEventListener('click', function(e) {
    if (e.target === enrollPopup) {
      enrollPopup.classList.remove('active');
      document.body.style.overflow = '';
    }
  });
});















//Animation on scroll
// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Animation for hero section on page load
    const heroSection = document.querySelector('.hero_workshops');
    heroSection.style.opacity = '0';
    heroSection.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
        heroSection.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
        heroSection.style.opacity = '1';
        heroSection.style.transform = 'translateY(0)';
    }, 100);
    
    // Animation for workshops container on scroll into view
    const workshopsContainer = document.querySelector('.workshops_container_exactly');
    const workshopsBenefits = document.querySelector('.workshops_benefits');
    
    // Set initial state for elements to be animated
    const elementsToAnimate = [
        {
            element: workshopsContainer,
            animation: 'fadeInUp'
        },
        {
            element: workshopsBenefits,
            animation: 'fadeInUp'
        }
    ];
    
    // Set initial styles for animation elements
    elementsToAnimate.forEach(item => {
        if (item.element) {
            item.element.style.opacity = '0';
            item.element.style.transform = 'translateY(30px)';
            item.element.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
        }
    });
    
    // Function to check if element is in viewport
    function isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top <= (window.innerHeight || document.documentElement.clientHeight) * 0.9 &&
            rect.bottom >= 0
        );
    }
    
    // Function to handle scroll events
    function handleScroll() {
        elementsToAnimate.forEach(item => {
            if (item.element && isInViewport(item.element)) {
                item.element.style.opacity = '1';
                item.element.style.transform = 'translateY(0)';
            }
        });
    }
    
    // Initial check on page load
    handleScroll();
    
    // Add scroll event listener
    window.addEventListener('scroll', handleScroll);
    
    // Animation for workshop cards on page load
    const workshopCards = document.querySelectorAll('.workshop-card');
    workshopCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 300 + (index * 100));
    });
    
    // Animation for benefit boxes on scroll into view
    const benefitBoxes = document.querySelectorAll('.benefit-box');
    
    benefitBoxes.forEach(box => {
        box.style.opacity = '0';
        box.style.transform = 'translateY(20px)';
        box.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    });
    
    function animateBenefitBoxes() {
        benefitBoxes.forEach((box, index) => {
            if (isInViewport(box)) {
                setTimeout(() => {
                    box.style.opacity = '1';
                    box.style.transform = 'translateY(0)';
                }, index * 100);
            }
        });
    }
    
    // Initial check for benefit boxes
    animateBenefitBoxes();
    
    // Add scroll event listener for benefit boxes
    window.addEventListener('scroll', animateBenefitBoxes);
});