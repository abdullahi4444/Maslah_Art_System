// Note: The 'courses' constant is now defined in an inline script
// in courses.php, right before this file is loaded.

// ---------- DOM ELEMENTS ----------
const allCoursesGrid = document.getElementById('all-courses-grid');
const searchInput = document.getElementById('search-input');
const sortSelect = document.getElementById('sort-by');
const categoryFilter = document.getElementById('category-filter');
const levelFilter = document.getElementById('level-filter');
const priceFilter = document.getElementById('price-filter');
const prevPageBtn = document.querySelector('.prev-page');
const nextPageBtn = document.querySelector('.next-page');
const pageNumbersContainer = document.getElementById('page-numbers');
const paginationContainer = document.getElementById('pagination-container');

const featuredWrapper = document.querySelector('.featured-courses-wrapper');
const prevArrow = document.querySelector('.prev-arrow');
const nextArrow = document.querySelector('.next-arrow');
const indicators = document.querySelectorAll('.carousel-indicator');
const noFeaturedMessage = document.querySelector('.no-featured-message');

// ---------- STATE ----------
let filteredCourses = [...courses];
let currentPage = 1;
let coursesPerPage = 6; // MODIFIED: Changed from const to let
let isPaginating = false;

let featuredCards = [];
let visibleFeaturedSlides = [];
let currentFeaturedSlide = 0;

// ---------- SCROLL ANIMATION OBSERVER ----------
const scrollObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.1 });

function setupScrollAnimations() {
    const elementsToAnimate = document.querySelectorAll('.animate-on-scroll');
    elementsToAnimate.forEach(el => {
        scrollObserver.observe(el);
    });
}

// ---------- INIT ----------
document.addEventListener('DOMContentLoaded', () => {
    // cache featured slides/cards
    if (document.querySelector('.featured-courses-slide')) {
        featuredCards = Array.from(document.querySelectorAll('.featured-courses-slide'));
        // applyFiltersAndSort is called below for both sections
        setupCarouselControls();
    }
    
    // Initial setup and render for the "All Courses" section
    if (document.getElementById('all-courses-grid')) {
        applyFiltersAndSort();
    }

    // Add a listener for window resize events to handle responsive changes
    window.addEventListener('resize', () => {
        // We re-run the main filter/sort function which now includes the layout adjustment
        if (allCoursesGrid) {
            applyFiltersAndSort();
        }
    });

    setupEventListeners();
    setupAccordion();
    setupVideoModal();
    setupScrollAnimations(); // Initial setup for static elements
});

// ---------- NEW FUNCTION TO HANDLE RESPONSIVE LAYOUT ----------
function adjustLayoutForScreenSize() {
    if (!paginationContainer) return; // Exit if pagination controls aren't on the page

    // We'll consider any screen 768px or less to be "mobile"
    const isMobile = window.matchMedia("(max-width: 768px)").matches;

    if (isMobile) {
        // On mobile, show all courses and hide pagination
        coursesPerPage = filteredCourses.length > 0 ? filteredCourses.length : 1; // Show all, prevent division by zero
        paginationContainer.style.display = 'none';
    } else {
        // On desktop, use pagination
        coursesPerPage = 6; // Set it back to 6 per page
        // Only show pagination if there's more than one page of results
        if (filteredCourses.length > coursesPerPage) {
            paginationContainer.style.display = 'flex';
        } else {
            paginationContainer.style.display = 'none';
        }
    }
}


// ---------- FILTER & SORT ----------
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
    if (featuredCards.length > 0) {
        filterFeaturedInDOM();
        refreshFeaturedIndicators();
        updateCarouselPosition();
    }
}

function filterCourses() {
    const term = searchInput.value.toLowerCase().trim();
    const category = categoryFilter.value;
    const level = levelFilter.value;
    const priceRange = priceFilter.value;

    filteredCourses = courses.filter(course => {
        if (term && !course.title.toLowerCase().includes(term)) return false;
        if (category !== 'all' && course.category !== category) return false;
        if (level !== 'all' && course.level !== level) return false;
        if (priceRange !== 'all') {
            const [min, max] = priceRange.split('-').map(Number);
            if (max) {
                if (course.price < min || course.price > max) return false;
            } else if (course.price < min) return false;
        }
        return true;
    });
}

function sortCourses() {
    const sortBy = sortSelect?.value || 'popular';
    filteredCourses.sort((a, b) => {
        switch (sortBy) {
            case 'price-asc': return a.price - b.price;
            case 'price-desc': return b.price - a.price;
            case 'newest': return b.id - a.id;
            case 'popular':
            default: return b.reviews - a.reviews;
        }
    });
}

// ---------- RENDER ALL COURSES ----------
function renderAllCourses() {
    if (!allCoursesGrid) return;
    allCoursesGrid.innerHTML = '';
    const start = (currentPage - 1) * coursesPerPage;
    const end = Math.min(start + coursesPerPage, filteredCourses.length);
    const paginated = filteredCourses.slice(start, end);

    if (paginated.length === 0) {
        allCoursesGrid.innerHTML = `
        <div class="no-results animate-on-scroll">
          <i class="fas fa-search"></i>
          <h3>No Courses Found</h3>
          <p>Try adjusting your search or filter criteria</p>
        </div>
      `;
        if (paginationContainer) paginationContainer.style.display = 'none';
        setupScrollAnimations(); // Observe the no-results message
        return;
    }
    
    // The adjustLayout function already handles pagination visibility.
    // We just need to ensure it's displayed if needed.
    if (paginationContainer && filteredCourses.length > coursesPerPage) {
        paginationContainer.style.display = 'flex';
    }


    paginated.forEach((course, index) => {
        const card = buildCourseCard(course);
        card.style.animationDelay = `${index * 100}ms`; // Staggered animation
        allCoursesGrid.appendChild(card);
    });

    updatePagination();
    setupScrollAnimations(); // Re-apply for new cards
}


function buildCourseCard(course) {
    const courseCard = document.createElement('div');
    courseCard.className = 'course-card animate-on-scroll';
    courseCard.dataset.category = course.category;
    courseCard.dataset.level = course.level;
    courseCard.dataset.price = course.price;
    courseCard.dataset.title = course.title;
    courseCard.dataset.reviews = course.reviews;

    const levelText = course.level.charAt(0).toUpperCase() + course.level.slice(1);
    const stars = generateRatingStars(course.rating);

    courseCard.innerHTML = `
    <div class="card-image">
      <img src="${course.image}" alt="${course.title}">
      ${course.badge ? `<span class="badge ${course.badge}">${course.badgeText}</span>` : ''}
    </div>
    <div class="card-content">
      <div class="tags">
        <span class="tag level-${course.level}">${levelText}</span>
        ${(course.tags || []).map(t => {
          let tagClass = 'tag'; // Default class
          if (t === 'Self-paced' || t === 'Live Classes') {
            tagClass = 'tag2'; // The blue tag class
          }
          return `<span class="${tagClass}">${t}</span>`;
        }).join('')}
      </div>
      <h3>${course.title}</h3>
      <p class="instructor">By ${course.instructor}</p>
      <div class="rating">
        ${stars} ${course.rating} (${course.reviews} reviews)
      </div>
      <div class="price-view">
        <span class="price">$${course.price} ${course.originalPrice ? `<del>$${course.originalPrice}</del>` : ''}</span>
        <a href="${course.url}" class="btn-view">View Course</a>
      </div>
    </div>
  `;
    return courseCard;
}

function generateRatingStars(rating) {
    let html = '';
    const full = Math.floor(rating);
    const half = rating % 1 >= 0.5;
    for (let i = 1; i <= 5; i++) {
        if (i <= full) html += '<i class="fas fa-star"></i>';
        else if (i === full + 1 && half) html += '<i class="fas fa-star-half-alt"></i>';
        else html += '<i class="far fa-star"></i>';
    }
    return html;
}

// ---------- FEATURED FILTERING IN-DOM ----------
function filterFeaturedInDOM() {
    if (!featuredCards.length) return;
    visibleFeaturedSlides = [];
    featuredCards.forEach((slideEl, slideIdx) => {
        const cards = Array.from(slideEl.querySelectorAll('.course-card'));
        let anyVisible = false;
        cards.forEach(card => {
            const title = (card.querySelector('h3')?.textContent || '').toLowerCase();
            const category = card.dataset.category || '';
            const level = card.dataset.level || '';
            const price = parseFloat(card.dataset.price || 0);
            const searchTerm = searchInput.value.toLowerCase().trim();
            const selectedCategory = categoryFilter.value;
            const selectedLevel = levelFilter.value;
            const priceRange = priceFilter.value;

            let visible = true;
            if (searchTerm && !title.includes(searchTerm)) visible = false;
            if (selectedCategory !== 'all' && category !== selectedCategory) visible = false;
            if (selectedLevel !== 'all' && level !== selectedLevel) visible = false;
            if (priceRange !== 'all') {
                const [min, max] = priceRange.split('-').map(Number);
                if (max) {
                    if (price < min || price > max) visible = false;
                } else if (price < min) visible = false;
            }

            card.style.display = visible ? '' : 'none';
            if (visible) anyVisible = true;
        });

        slideEl.style.display = anyVisible ? 'grid' : 'none';
        if (anyVisible) visibleFeaturedSlides.push(slideIdx);
    });

    if (noFeaturedMessage) {
        if (visibleFeaturedSlides.length === 0) {
            if (featuredWrapper) featuredWrapper.style.display = 'none';
            noFeaturedMessage.style.display = 'block';
        } else {
            if (featuredWrapper) featuredWrapper.style.display = '';
            noFeaturedMessage.style.display = 'none';
            if (!visibleFeaturedSlides.includes(currentFeaturedSlide)) {
                currentFeaturedSlide = visibleFeaturedSlides[0];
            }
        }
    }
}

// ---------- CAROUSEL CONTROLS ----------
function setupCarouselControls() {
    if (!prevArrow || !nextArrow) return;
    prevArrow.addEventListener('click', () => {
        const idx = visibleFeaturedSlides.indexOf(currentFeaturedSlide);
        if (idx > 0) {
            currentFeaturedSlide = visibleFeaturedSlides[idx - 1];
            updateCarouselPosition();
        }
    });
    nextArrow.addEventListener('click', () => {
        const idx = visibleFeaturedSlides.indexOf(currentFeaturedSlide);
        if (idx < visibleFeaturedSlides.length - 1) {
            currentFeaturedSlide = visibleFeaturedSlides[idx + 1];
            updateCarouselPosition();
        }
    });
    document.querySelectorAll('.carousel-indicator').forEach(ind => {
        ind.addEventListener('click', function () {
            const target = parseInt(this.dataset.slide, 10);
            if (visibleFeaturedSlides.includes(target)) {
                currentFeaturedSlide = target;
                updateCarouselPosition();
            }
        });
    });
}

function refreshFeaturedIndicators() {
    if (!indicators.length) return;
    indicators.forEach(ind => {
        const slide = parseInt(ind.dataset.slide, 10);
        if (slide === currentFeaturedSlide) ind.classList.add('active');
        else ind.classList.remove('active');

        if (!visibleFeaturedSlides.includes(slide)) ind.style.display = 'none';
        else ind.style.display = '';
    });
}

function updateCarouselPosition() {
    if (!featuredWrapper) return;
    const offset = currentFeaturedSlide * 100;
    featuredWrapper.style.transform = `translateX(-${offset}%)`;
    refreshFeaturedIndicators();

    if (visibleFeaturedSlides.length === 0) {
        prevArrow.classList.remove('active');
        nextArrow.classList.remove('active');
        return;
    }
    const pos = visibleFeaturedSlides.indexOf(currentFeaturedSlide);
    prevArrow.classList.toggle('active', pos > 0);
    nextArrow.classList.toggle('active', pos < visibleFeaturedSlides.length - 1);
}

// ---------- PAGINATION ----------
function animateAndRenderPage(newPage) {
    const totalPages = Math.ceil(filteredCourses.length / coursesPerPage);
    if (isPaginating || newPage < 1 || newPage > totalPages || newPage === currentPage) {
        return;
    }
    isPaginating = true;
    currentPage = newPage;

    allCoursesGrid.style.opacity = 0;

    setTimeout(() => {
        renderAllCourses();
        allCoursesGrid.style.opacity = 1;

        setTimeout(() => {
            isPaginating = false;
        }, 300);
    }, 300);
}

function updatePagination() {
    if (!paginationContainer || !pageNumbersContainer) return;
    const totalPages = Math.ceil(filteredCourses.length / coursesPerPage);
    pageNumbersContainer.innerHTML = '';

    if (totalPages <= 1) {
        paginationContainer.style.display = 'none';
        return;
    }
    // On desktop, ensure it's visible if needed
    if (!window.matchMedia("(max-width: 768px)").matches) {
       paginationContainer.style.display = 'flex';
    }


    addPageButton(1);
    const startPage = Math.max(2, currentPage - 1);
    const endPage = Math.min(totalPages - 1, currentPage + 1);

    if (startPage > 2) pageNumbersContainer.insertAdjacentHTML('beforeend', '<span>...</span>');
    for (let i = startPage; i <= endPage; i++) addPageButton(i);
    if (endPage < totalPages - 1) pageNumbersContainer.insertAdjacentHTML('beforeend', '<span>...</span>');
    if (totalPages > 1) addPageButton(totalPages);

    prevPageBtn.classList.toggle('disabled', currentPage === 1);
    nextPageBtn.classList.toggle('disabled', currentPage === totalPages);
}

function addPageButton(pageNumber) {
    const pageBtn = document.createElement('span');
    pageBtn.className = 'page';
    pageBtn.textContent = pageNumber;
    if (pageNumber === currentPage) pageBtn.classList.add('active');
    pageBtn.addEventListener('click', () => animateAndRenderPage(pageNumber));
    pageNumbersContainer.appendChild(pageBtn);
}

// ---------- EVENT LISTENERS ----------
function setupEventListeners() {
    if (searchInput) searchInput.addEventListener('input', applyFiltersAndSort);
    if (categoryFilter) categoryFilter.addEventListener('change', applyFiltersAndSort);
    if (levelFilter) levelFilter.addEventListener('change', applyFiltersAndSort);
    if (priceFilter) priceFilter.addEventListener('change', applyFiltersAndSort);
    if (sortSelect) sortSelect.addEventListener('change', applyFiltersAndSort);

    if (prevPageBtn) prevPageBtn.addEventListener('click', () => animateAndRenderPage(currentPage - 1));
    if (nextPageBtn) nextPageBtn.addEventListener('click', () => animateAndRenderPage(currentPage + 1));
}

// ---------- COURSE-DETAILS PAGE SPECIFIC JS ----------
function setupAccordion() {
    const accordionItems = document.querySelectorAll('.accordion-item');
    if (!accordionItems.length) return;

    function setMaxHeight(item, isActive) {
        const content = item.querySelector('.accordion-content');
        if (isActive) {
            content.style.maxHeight = content.scrollHeight + 'px';
        } else {
            content.style.maxHeight = '0px';
        }
    }
    
    const activeItemOnLoad = document.querySelector('.accordion-item.active');
    if (activeItemOnLoad) setMaxHeight(activeItemOnLoad, true);

    accordionItems.forEach(item => {
        const header = item.querySelector('.accordion-header');
        if (header) {
            header.addEventListener('click', () => {
                const wasActive = item.classList.contains('active');
                accordionItems.forEach(i => {
                    i.classList.remove('active');
                    setMaxHeight(i, false);
                });
                if (!wasActive) {
                    item.classList.add('active');
                    setMaxHeight(item, true);
                }
            });
        }
    });

    window.addEventListener('resize', () => {
        const activeItem = document.querySelector('.accordion-item.active');
        if (activeItem) setMaxHeight(activeItem, true);
    });
}

function setupVideoModal() {
    const videoModal = document.getElementById('video-modal');
    if (!videoModal) return;

    const closeModalBtn = document.querySelector('.close-modal-btn');
    const videoPlayer = document.getElementById('course-video-player');
    const videoLinks = document.querySelectorAll('.video-lesson-link');

    const openModal = (videoUrl) => {
        videoPlayer.querySelector('source').setAttribute('src', videoUrl);
        videoPlayer.load();
        videoModal.classList.add('active');
        videoPlayer.play().catch(e => console.error("Video play failed:", e));
    };

    const closeModal = () => {
        videoModal.classList.remove('active');
        videoPlayer.pause();
        videoPlayer.querySelector('source').setAttribute('src', '');
    };

    videoLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const videoUrl = this.getAttribute('data-video-url');
            if (videoUrl) openModal(videoUrl);
        });
    });

    if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    videoModal.addEventListener('click', (e) => {
        if (e.target === videoModal) closeModal();
    });
}