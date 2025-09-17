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
// Guard: allow pages where `courses` may not be defined (details pages may not need the full list)
const _COURSES = (typeof window !== 'undefined' && typeof window.courses !== 'undefined') ? window.courses : (typeof courses !== 'undefined' ? courses : []);
let filteredCourses = [..._COURSES];
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
    if (searchInput) filterCourses();
    if (sortSelect) sortCourses();

    adjustLayoutForScreenSize();
    currentPage = 1;

    if (allCoursesGrid) {
        renderAllCourses();
    }
    if (featuredCards.length > 0) {
        filterFeaturedInDOM(); // Apply filtering to Featured Courses
        refreshFeaturedIndicators();
        updateCarouselPosition();
    }
}

// Enhance search functionality to match courses starting with the search term
function filterCourses() {
    const term = searchInput.value.toLowerCase().trim();
    const category = categoryFilter.value;
    const level = levelFilter.value;
    const priceRange = priceFilter.value;

    filteredCourses = courses.filter(course => {
        const titleMatch = !term || course.title.toLowerCase().startsWith(term); // Match courses starting with the term
        const categoryMatch = category === 'all' || course.category === category;
        const levelMatch = level === 'all' || course.level === level;
        const [min, max] = priceRange !== 'all' ? priceRange.split('-').map(Number) : [null, null];
        const priceMatch = priceRange === 'all' || (max ? course.price >= min && course.price <= max : course.price >= min);

        return titleMatch && categoryMatch && levelMatch && priceMatch;
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
          const classList = ['tag'];
          const normalized = String(t).toLowerCase();
          if (normalized === 'self-paced') {
            classList.push('delivery-selfpaced');
          } else if (normalized === 'live classes') {
            classList.push('delivery-live');
          } else if (normalized === 'certificate' || normalized === 'certificate of completion' || normalized === 'certified') {
            classList.push('certificate');
          }
          return `<span class="${classList.join(' ')}">${t}</span>`;
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
    const term = searchInput.value.toLowerCase().trim();
    const category = categoryFilter.value;
    const level = levelFilter.value;
    const priceRange = priceFilter.value;

    featuredCards.forEach(slide => {
        const courseCards = slide.querySelectorAll('.course-card');
        courseCards.forEach(card => {
            const title = card.dataset.title.toLowerCase();
            const categoryMatch = category === 'all' || card.dataset.category === category;
            const levelMatch = level === 'all' || card.dataset.level === level;
            const price = parseFloat(card.dataset.price);
            const [min, max] = priceRange !== 'all' ? priceRange.split('-').map(Number) : [null, null];
            const priceMatch = priceRange === 'all' || (max ? price >= min && price <= max : price >= min);
            const searchMatch = !term || title.startsWith(term);

            if (categoryMatch && levelMatch && priceMatch && searchMatch) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
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

// ---------- MOBILE STICKY HEADER FALLBACK (JS) ----------
// Some browsers or complex CSS can break `position: sticky`; this fallback forces
// the header to be fixed on small screens and inserts a spacer to preserve layout.
function enableMobileHeaderFallback() {
    const header = document.querySelector('.all-courses .section-header');
    if (!header) return;

    const spacerId = 'mobile-section-header-spacer';
    let spacer = document.getElementById(spacerId);

    const applyFixed = () => {
        const isMobile = window.matchMedia('(max-width: 768px)').matches;
        if (isMobile) {
            // Create spacer if not present
            if (!spacer) {
                spacer = document.createElement('div');
                spacer.id = spacerId;
                spacer.style.width = '100%';
                spacer.style.height = header.getBoundingClientRect().height + 'px';
                header.parentNode.insertBefore(spacer, header.nextSibling);
            } else {
                spacer.style.height = header.getBoundingClientRect().height + 'px';
            }

            // Align the fixed header to its parent container so it visually stays inside
            // the "All Courses" section while cards scroll horizontally underneath.
            const container = header.parentNode; // should be the .all-courses element
            const containerRect = container.getBoundingClientRect();
            header.style.position = 'sticky';
            header.style.top = '0';
            header.style.left = containerRect.left + 'px';
            header.style.width = containerRect.width + 'px';
            header.style.boxSizing = 'border-box';
            header.style.zIndex = '60';
            header.classList.add('mobile-fixed-header');
        } else {
            if (spacer) {
                spacer.remove();
                spacer = null;
            }
            header.style.position = '';
            header.style.top = '';
            header.style.left = '';
            header.style.right = '';
            header.style.zIndex = '';
            header.style.width = '';
            header.classList.remove('mobile-fixed-header');
        }
    };

    // Apply immediately and on resize/orientation change
    applyFixed();
    window.addEventListener('resize', () => {
        // small throttle
        clearTimeout(window.__mobileHeaderTimer);
        window.__mobileHeaderTimer = setTimeout(applyFixed, 80);
    });
    window.addEventListener('orientationchange', applyFixed);


    
}




// Initialize the fallback once DOM is ready
document.addEventListener('DOMContentLoaded', enableMobileHeaderFallback);



// ===== COURSE ACTIONS: robust, delegated, modal-injecting =====
(function () {
  // Unique IDs/classes to avoid collisions
  const MODAL_ID = "course-action-modal";
  const STYLE_ID = "course-action-modal-styles";
  let modalEl = null;
  let modalBody = null;
  let modalOpenClass = "cam-open";

  function injectStyles() {
    if (document.getElementById(STYLE_ID)) return;
    const css = `
      /* modal wrapper */
      #${MODAL_ID} { position: fixed; inset: 0; display: none; z-index: 1200; font-family: inherit; }
      #${MODAL_ID}.${modalOpenClass} { display: block; }
      #${MODAL_ID} .cam-backdrop { position:absolute; inset:0; background: rgba(0,0,0,0.6); }
      #${MODAL_ID} .cam-dialog { position: absolute; left:50%; top:10vh; transform: translateX(-50%); width: min(520px, 94%); background:#fff; border-radius:10px; padding:18px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
      #${MODAL_ID} .cam-close { position:absolute; right:10px; top:8px; background:transparent; border:0; font-size:20px; cursor:pointer; }
      #${MODAL_ID} .cam-title { margin: 0 0 8px; font-size:18px; }
      #${MODAL_ID} .cam-body p { margin: 8px 0; }
      #${MODAL_ID} .cam-input { width:100%; padding:8px; border:1px solid #ddd; border-radius:8px; box-sizing:border-box; }
      #${MODAL_ID} .cam-actions { display:flex; gap:8px; justify-content:center; margin-top:12px; flex-wrap:wrap; }
      #${MODAL_ID} .cam-btn { padding:8px 12px; border-radius:8px; border:0; cursor:pointer; }
      #${MODAL_ID} .cam-btn.primary { background:#2563eb; color:#fff; }
      #${MODAL_ID} .cam-btn.default { background:#f3f4f6; color:#111; }
    `;
    const s = document.createElement("style");
    s.id = STYLE_ID;
    s.textContent = css;
    document.head.appendChild(s);
  }

  function createModal() {
    if (modalEl) return modalEl;
    if (!document.body) {
      // If executed before body exists, wait for DOMContentLoaded then create
      document.addEventListener("DOMContentLoaded", () => { createModal(); });
      return null;
    }
    injectStyles();
    // create DOM
    modalEl = document.getElementById(MODAL_ID);
    if (!modalEl) {
      modalEl = document.createElement("div");
      modalEl.id = MODAL_ID;
      modalEl.innerHTML = `
        <div class="cam-backdrop" data-role="backdrop"></div>
        <div class="cam-dialog" role="dialog" aria-modal="true" aria-labelledby="cam-title">
          <button class="cam-close" aria-label="Close">&times;</button>
          <div class="cam-content">
            <h2 id="cam-title" class="cam-title" style="font-weight:600"></h2>
            <div class="cam-body" id="cam-body"></div>
            <div class="cam-actions" id="cam-actions" style="display:none"></div>
          </div>
        </div>
      `;
      document.body.appendChild(modalEl);
    }
    modalBody = modalEl.querySelector("#cam-body");
    // close handlers
    modalEl.querySelector(".cam-close").addEventListener("click", closeModal);
    modalEl.querySelector("[data-role='backdrop']").addEventListener("click", (e) => {
      if (e.target === e.currentTarget) closeModal();
    });
    document.addEventListener("keydown", (e) => { if (e.key === "Escape") closeModal(); });
    // delegated modal action handler
    modalEl.addEventListener("click", modalActionHandler);
    return modalEl;
  }

  function openModal({ title = "", html = "", actionsHtml = "" } = {}) {
    try {
      createModal();
      if (!modalEl) return;
      modalEl.classList.add(modalOpenClass);
      const titleEl = modalEl.querySelector("#cam-title");
      titleEl.textContent = title || "";
      modalBody.innerHTML = html || "";
      const actionsWrap = modalEl.querySelector("#cam-actions");
      if (actionsHtml && actionsHtml.trim() !== "") {
        actionsWrap.innerHTML = actionsHtml;
        actionsWrap.style.display = "flex";
      } else {
        actionsWrap.innerHTML = "";
        actionsWrap.style.display = "none";
      }
      // focus first focusable
      setTimeout(() => {
        const focusable = modalEl.querySelector("button, [href], input, textarea, select");
        if (focusable) focusable.focus();
      }, 40);
    } catch (err) {
      console.error("openModal error:", err);
    }
  }

  function closeModal() {
    if (!modalEl) return;
    modalEl.classList.remove(modalOpenClass);
    // optional: clear contents after a small delay
    setTimeout(() => {
      const titleEl = modalEl.querySelector("#cam-title"); if (titleEl) titleEl.textContent = "";
      if (modalBody) modalBody.innerHTML = "";
      const actionsWrap = modalEl.querySelector("#cam-actions"); if (actionsWrap) actionsWrap.innerHTML = "";
    }, 200);
  }

  // Handles clicks INSIDE the modal (by data-action attributes)
  function modalActionHandler(e) {
    const btn = e.target.closest("[data-action]");
    if (!btn) return;
    const action = btn.getAttribute("data-action");
    if (!action) return;
    e.preventDefault();
    if (action === "close") return closeModal();
    if (action === "goto-cart") {
      // Redirect to cart; attach source URL so server can pick it if needed
      const url = "cart.php?from=" + encodeURIComponent(window.location.href);
      window.location.href = url;
      return;
    }
    if (action === "confirm-enroll") {
      // Simple front-end confirmation; you can replace with POST/fetch to enroll.php if backend is ready
      openModal({
        title: "You're enrolled!",
        html: `<p>Enrollment confirmed. Check your email for details.</p>`,
        actionsHtml: `<button class="cam-btn default" data-action="close">Close</button>`
      });
      return;
    }
    if (action === "copy-link") {
      const val = btn.getAttribute("data-value") || window.location.href;
      navigator.clipboard?.writeText(val).then(() => {
        const old = btn.textContent;
        btn.textContent = "Copied!";
        setTimeout(() => (btn.textContent = old), 1200);
      }).catch(() => {
        // fallback for old browsers
        try {
          const input = modalEl.querySelector(".cam-input");
          if (input) { input.select(); document.execCommand("copy"); btn.textContent = "Copied!"; setTimeout(()=>btn.textContent = "Copy", 1200); }
        } catch (err) {
          console.warn("copy failed", err);
        }
      });
      return;
    }
  }

  // Delegated handler for clicks on the page (works if buttons are added dynamically)
  function pageClickHandler(e) {
    // Look for clicks inside a .course-buttons' child button (works regardless of markup order)
    const btn = e.target.closest(".course-buttons .btn-primary, .course-buttons .btn-secondary, .course-buttons .btn-tertiary");
    if (!btn) return;
    // ensure the matched button is inside a .course-buttons container
    const container = btn.closest(".course-buttons");
    if (!container) return;

    e.preventDefault();

    // Try to get a course title (fallbacks)
    const courseTitle = (document.querySelector(".course-title") && document.querySelector(".course-title").textContent.trim())
                        || (document.querySelector("h1") && document.querySelector("h1").textContent.trim())
                        || document.title || "This Course";

    // Distinguish the three buttons by their class
    if (btn.classList.contains("btn-primary")) {
      // Add to Cart
      openModal({
        title: "Added to Cart",
        html: `<p>"${escapeHtml(courseTitle)}" was added to your cart.</p>`,
        actionsHtml: `<button class="cam-btn primary" data-action="goto-cart">Go to Cart</button>
                      <button class="cam-btn default" data-action="close">Continue Browsing</button>`
      });
      return;
    }

    if (btn.classList.contains("btn-secondary")) {
      // Enroll Now
      openModal({
        title: `Enroll in "${escapeHtml(courseTitle)}"`,
        html: `<p>Please confirm your enrollment for this course.</p>`,
        actionsHtml: `<button class="cam-btn primary" data-action="confirm-enroll">Confirm Enrollment</button>
                      <button class="cam-btn default" data-action="close">Cancel</button>`
      });
      return;
    }

    if (btn.classList.contains("btn-tertiary")) {
      // Share: prefer Web Share API, else show copy modal
      const url = window.location.href;
      const shareTitle = courseTitle;
      if (navigator.share) {
        navigator.share({ title: shareTitle, url }).catch(() => {
          // user probably cancelled or API not allowed — fall back to copy modal
          openCopyModal(url);
        });
      } else {
        openCopyModal(url);
      }
      return;
    }
  }

  function openCopyModal(url) {
    openModal({
      title: "Share this Course",
      html: `<p>Copy the link below:</p>
             <input class="cam-input" type="text" readonly value="${escapeHtml(url)}">`,
      actionsHtml: `<button class="cam-btn primary" data-action="copy-link" data-value="${escapeHtml(url)}">Copy Link</button>
                    <button class="cam-btn default" data-action="close">Close</button>`
    });
  }

  // small html escaper to avoid injecting raw chars
  function escapeHtml(str) {
    return String(str).replace(/[&<>"'`=\/]/g, function (s) {
      return ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;', '`': '&#x60;', '=': '&#x3D;', '/': '&#x2F;'
      })[s];
    });
  }

  // Attach the delegated listener immediately (safe even before DOM ready)
  document.addEventListener("click", pageClickHandler, true);

  // make createModal available immediately (no-op until body exists)
  window.setupCourseButtons = function () {
    // Legacy compatibility: calling this will ensure modal is created
    try { createModal(); console.info("setupCourseButtons: modal created (legacy call)"); } catch (e) { console.warn("setupCourseButtons: error", e); }
  };

  // create modal early after DOMContentLoaded (optional)
  document.addEventListener("DOMContentLoaded", () => {
    try { createModal(); } catch (_) {}
    // small flag for debugging
    window.__courseActionModalReady = true;
    console.info("course action modal ready");
  });

})();

