    // Global slider state variables
    window.eventCurrentSlide = 0; // Initialize current slide globally
    window.eventSliderInterval = null; // Initialize slider interval globally
 
    function initializeSlider() {
      const slides = document.querySelectorAll('.event-image-slide');
      const dotsContainer = document.querySelector('.slider-dots');
      const dots = document.querySelectorAll('.dot');
      const prevButton = document.getElementById('prevBtn');
      const nextButton = document.getElementById('nextBtn');

     

      const totalSlides = slides.length;

      

      // Ensure current slide is within bounds after re-initialization (e.g., if filter changes and number of slides is different)
      if (window.eventCurrentSlide >= totalSlides) {
          window.eventCurrentSlide = 0; // Reset if out of bounds
      }

      // Clear any existing interval to prevent multiple sliders running simultaneously
      if (window.eventSliderInterval) {
          clearInterval(window.eventSliderInterval);
          window.eventSliderInterval = null; // Clear the global reference
      }
 
      function showSlide(index) {
          slides.forEach((slide, i) => {
              slide.classList.remove('active');
              if (i === index) {
                  slide.classList.add('active');
              } else {
                  // Remove explicit display: none; CSS will handle visibility via opacity
              }
          });
          // Removed dots logic from here
          window.eventCurrentSlide = index; // Update global current slide after showing
      }
 
      function nextSlide() {
          window.eventCurrentSlide = (window.eventCurrentSlide + 1) % totalSlides;
          showSlide(window.eventCurrentSlide);
      }
 
      function prevSlide() {
          window.eventCurrentSlide = (window.eventCurrentSlide - 1 + totalSlides) % totalSlides;
          showSlide(window.eventCurrentSlide);
      }
 
      function startSlider() {
          if (totalSlides > 1) {
              if (!window.eventSliderInterval) { // Only start if not already running
                  window.eventSliderInterval = setInterval(nextSlide, 5000);
              } else {
              }
          } else {
          }
      }
 
      function stopSlider() {
          if (window.eventSliderInterval) {
              clearInterval(window.eventSliderInterval);
              window.eventSliderInterval = null;
          }
          else {
          }
      }
 
      if (totalSlides > 1) { // Only initialize slider functionality if there's more than one slide
          showSlide(window.eventCurrentSlide); // Show the globally tracked slide
          startSlider();
 
          if (prevButton) {
              // Define the event handler as a named function
              const handlePrevButtonClick = function() {
                  stopSlider();
                  prevSlide();
                  startSlider(); // RE-ADDING: Auto-advance should resume after manual click
              };
              // Remove existing listener to prevent duplicates, then add the new one
              prevButton.removeEventListener('click', handlePrevButtonClick);
              prevButton.addEventListener('click', handlePrevButtonClick);
          }
 
          if (nextButton) {
              // Define the event handler as a named function
              const handleNextButtonClick = function() {
                  stopSlider();
                  nextSlide();
                  startSlider(); 
              };
              nextButton.removeEventListener('click', handleNextButtonClick);
              nextButton.addEventListener('click', handleNextButtonClick);
          }
 
           
        } else if (totalSlides === 1) {
            showSlide(window.eventCurrentSlide);
        } else {
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
      if (typeof window.eventCurrentSlide === 'undefined') {
          window.eventCurrentSlide = 0;
      }
      if (typeof window.eventSliderInterval === 'undefined') {
          window.eventSliderInterval = null;
      }
      initializeSlider();

      // Loader functions
      function showPageLoader() {
          const loader = document.getElementById('pageTransitionLoader');
          if (loader) loader.classList.add('active');
      }

      function hidePageLoader() {
          const loader = document.getElementById('pageTransitionLoader');
          if (loader) loader.classList.remove('active');
      }

      // Function to re-attach event listeners to 'Get in Touch' buttons
      function attachGetInTouchListeners() {
          const touchButtons = document.querySelectorAll('#eventsContainer .touch-btn');
          const targetForm = document.getElementById('subscriptionForm');
          if (targetForm) {
              touchButtons.forEach(button => {
                  button.removeEventListener('click', handleGetInTouchClick); // Remove existing to prevent duplicates
                  button.addEventListener('click', handleGetInTouchClick);
              });
          }
      }

      function handleGetInTouchClick(e) {
          e.preventDefault();
          const targetForm = document.getElementById('subscriptionForm');
          if (targetForm) {
              smoothScrollTo(targetForm, 800); // Use custom smooth scroll
          }
      }

      // Dynamic content loading for tabs and pagination
      async function loadEventsContent(filter, page, pushState = true) {
          showPageLoader(); // Show loader when fetching new content
          const url = new URL(window.location.origin + window.location.pathname);
          url.searchParams.set('filter', filter);
          url.searchParams.set('page', page);
          url.searchParams.set('ajax_content', 'true'); // Indicate AJAX request

          try {
              const response = await fetch(url.toString());
              if (!response.ok) {
                  throw new Error(`HTTP error! status: ${response.status}`);
              }
              const html = await response.text();
              
              // Create a temporary div to parse the HTML
              const tempDiv = document.createElement('div');
              tempDiv.innerHTML = html;

              // Extract and replace the events container content
              const newEventsContainer = tempDiv.querySelector('#eventsContainer');
              const oldEventsContainer = document.getElementById('eventsContainer');
              if (newEventsContainer && oldEventsContainer) {
                  oldEventsContainer.innerHTML = newEventsContainer.innerHTML;
              }

              // Extract and replace the pagination controls content
              const newPaginationControls = tempDiv.querySelector('#paginationControls');
              const oldPaginationControls = document.getElementById('paginationControls');
              if (newPaginationControls && oldPaginationControls) {
                  oldPaginationControls.innerHTML = newPaginationControls.innerHTML;
              }

              // Update active classes for tabs
              document.querySelectorAll('.events-tabs .tab').forEach(t => t.classList.remove('active'));
              document.querySelector(`.events-tabs .tab[data-filter="${filter}"]`).classList.add('active');

              // Update URL without reloading page if pushState is true
              if (pushState) {
                  const cleanUrl = new URL(window.location.origin + window.location.pathname);
                  cleanUrl.searchParams.set('filter', filter);
                  cleanUrl.searchParams.set('page', page);
                  window.history.pushState({filter: filter, page: page}, '', cleanUrl.toString());
              }

              attachGetInTouchListeners(); // Re-attach listeners for new buttons
              initializeSlider(); // Re-initialize the slider after new content is loaded

          } catch (error) {
              console.error('Error loading events content:', error);
              // Optionally display an error message to the user
          } finally {
              hidePageLoader(); // Hide loader after content is loaded or an error occurs
          }
      }

      const urlParams = new URLSearchParams(window.location.search);
      const initialFilter = urlParams.get('filter') || 'upcoming';
      const initialPage = parseInt(urlParams.get('page')) || 1;

      const tabs = document.querySelectorAll('.events-tabs .tab');
      tabs.forEach(tab => {
          tab.addEventListener('click', (e) => {
              e.preventDefault();
              const filter = tab.getAttribute('data-filter');
              loadEventsContent(filter, 1); 
          });
      });

      document.getElementById('paginationControls').addEventListener('click', (e) => {
          if (e.target.tagName === 'BUTTON' && !e.target.disabled) {
              e.preventDefault();
              const page = parseInt(e.target.getAttribute('data-page'));
              const filter = e.target.getAttribute('data-filter');
              loadEventsContent(filter, page);
          }
      });

      window.addEventListener('popstate', (event) => {
          if (event.state) {
              loadEventsContent(event.state.filter || 'upcoming', event.state.page || 1, false); // Don't push state again
          } else {
              loadEventsContent(initialFilter, initialPage, false);
          }
      });

      const scrollButtons = document.querySelectorAll('.join-btn, .touch-btn');
      const targetForm = document.getElementById('subscriptionForm');

      function smoothScrollTo(targetElement, duration) {
        const targetY = targetElement.getBoundingClientRect().top + window.scrollY;
        const startY = window.scrollY;
        const distance = targetY - startY;
        const startTime = performance.now();

        function animateScroll(currentTime) {
          const elapsed = currentTime - startTime;
          const progress = Math.min(elapsed / duration, 1);
          // Ease-in-out quadratic
          const easeInOutQuad = progress < 0.5
            ? 2 * progress * progress
            : 1 - Math.pow(-2 * progress + 2, 2) / 2;

          window.scrollTo(0, startY + distance * easeInOutQuad);

          if (progress < 1) {
            requestAnimationFrame(animateScroll);
          }
        }

        requestAnimationFrame(animateScroll);
      }

      if (targetForm) {
        scrollButtons.forEach(button => {
          button.addEventListener('click', (e) => {
            e.preventDefault(); // Prevent default anchor behavior
            smoothScrollTo(targetForm, 800); // Use custom smooth scroll with 800ms duration
          });
        });
      }
      // Initial attachment of Get in Touch listeners for buttons present on first load
      attachGetInTouchListeners();

      // Form Validation
      const subscriptionForm = document.querySelector('.contact form');
      subscriptionForm.addEventListener('submit', function(e) {
        let hasError = false;

        const usernameInput = document.getElementById('username');
        const usernameError = document.getElementById('username-error');
        if (usernameInput.value.trim() === '') {
          usernameError.textContent = 'Full name is required'; // Updated message
          usernameError.style.display = 'block';
          usernameInput.closest('.form-group').classList.add('error');
          hasError = true;
        } else {
          usernameError.style.display = 'none';
          usernameInput.closest('.form-group').classList.remove('error');
        }

        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('email-error');
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (emailInput.value.trim() === '') {
          emailError.textContent = 'Email is required';
          emailError.style.display = 'block';
          emailInput.closest('.form-group').classList.add('error');
          hasError = true;
        } else if (!emailPattern.test(emailInput.value.trim())) {
          emailError.textContent = 'Please enter a valid email address';
          emailError.style.display = 'block';
          emailInput.closest('.form-group').classList.add('error');
          hasError = true;
        } else {
          emailError.style.display = 'none';
          emailInput.closest('.form-group').classList.remove('error');
        }

        const messageInput = document.getElementById('message');
        const messageError = document.getElementById('message-error');
        if (messageInput.value.trim() === '') {
          messageError.textContent = 'Message is required';
          messageError.style.display = 'block';
          messageInput.closest('.form-group').classList.add('error');
          hasError = true;
        } else if (messageInput.value.trim().length > 500) {
          messageError.textContent = 'Message cannot exceed 500 characters';
          messageError.style.display = 'block';
          messageInput.closest('.form-group').classList.add('error');
          hasError = true;
        } else {
          messageError.style.display = 'none';
          messageInput.closest('.form-group').classList.remove('error');
        }

        if (hasError) {
          e.preventDefault(); // Prevent form submission if there are errors
        }
      });

    // Hide loader once the page is fully loaded
    window.addEventListener('load', () => {
        const pageTransitionLoader = document.getElementById('pageTransitionLoader');
        if (pageTransitionLoader) {
            pageTransitionLoader.style.opacity = '0';
            pageTransitionLoader.style.visibility = 'hidden';
        }
    });
 
    // Show loader on page transitions (e.g., when clicking links)
    document.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', (event) => {
            const pageTransitionLoader = document.getElementById('pageTransitionLoader');
            // Only show loader for internal links not targeting # (like subscription form) or external links
            const href = link.getAttribute('href');
            if (href && href.startsWith('#') === false && !href.startsWith('http') && pageTransitionLoader) {
                event.preventDefault(); // Prevent immediate navigation
                pageTransitionLoader.style.opacity = '1';
                pageTransitionLoader.style.visibility = 'visible';
                setTimeout(() => {
                    window.location.href = href;
                }, 300); // Allow loader to show for 300ms before navigating
            }
        });
    });
 
    }); // CLOSING THE FIRST DOMContentLoaded
