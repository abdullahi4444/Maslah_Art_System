// gallery/assets/main.js
document.addEventListener('DOMContentLoaded', function() {
    // Preloader functionality
    const preloader = document.getElementById('preloader');
    const preloaderProgress = document.getElementById('preloaderProgress');
    
    // Global variables for pagination and sorting
    let currentPage = 1;
    let currentSort = 'newest';
    
    // Function to show preloader
    function showPreloader() {
        if (!preloader) return null;
        
        preloader.classList.add('active');
        if (preloaderProgress) preloaderProgress.style.width = '0%';
        
        // Simulate progress
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress >= 100) {
                progress = 100;
                clearInterval(progressInterval);
            }
            if (preloaderProgress) preloaderProgress.style.width = progress + '%';
        }, 200);
        
        return progressInterval;
    }
    
    // Function to hide preloader
    function hidePreloader(progressInterval) {
        if (!preloader || !progressInterval) return;
        
        clearInterval(progressInterval);
        if (preloaderProgress) preloaderProgress.style.width = '100%';
        
        setTimeout(() => {
            preloader.classList.remove('active');
        }, 500);
    }
    
    // Enhanced Hero Slideshow Functionality
    const heroSlideshow = document.getElementById('heroSlideshow');
    let currentSlide = 0;
    let slideInterval;
    let isPlaying = true;
    const slideDuration = 5000; // 5 seconds per slide
    
    // Initialize slideshow if elements exist
    if (heroSlideshow) {
        const heroSlides = document.querySelectorAll('.hero-slide');
        const heroIndicators = document.querySelectorAll('.hero-indicator');
        const prevArrow = document.querySelector('.hero-prev');
        const nextArrow = document.querySelector('.hero-next');
        const autoplayControl = document.getElementById('autoplayControl');
        
        // Initialize slideshow
        function initSlideshow() {
            // Start autoplay
            startAutoplay();
            
            // Add event listeners for navigation
            if (prevArrow) prevArrow.addEventListener('click', showPrevSlide);
            if (nextArrow) nextArrow.addEventListener('click', showNextSlide);
            
            // Add event listeners for indicators
            heroIndicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => {
                    goToSlide(index);
                });
            });
            
            // Add event listener for autoplay control
            if (autoplayControl) autoplayControl.addEventListener('click', toggleAutoplay);
            
            // Pause on hover
            heroSlideshow.addEventListener('mouseenter', pauseAutoplay);
            heroSlideshow.addEventListener('mouseleave', () => {
                if (isPlaying) {
                    startAutoplay();
                }
            });
        }
        
        // Show a specific slide
        function goToSlide(index) {
            // Remove active class from current slide and indicator
            heroSlides[currentSlide].classList.remove('active');
            heroIndicators[currentSlide].classList.remove('active');
            
            // Update current slide index
            currentSlide = index;
            
            // Add active class to new slide and indicator
            heroSlides[currentSlide].classList.add('active');
            heroIndicators[currentSlide].classList.add('active');
            
            // Reset autoplay timer
            resetAutoplay();
        }
        
        // Show next slide
        function showNextSlide() {
            const nextSlide = (currentSlide + 1) % heroSlides.length;
            goToSlide(nextSlide);
        }
        
        // Show previous slide
        function showPrevSlide() {
            const prevSlide = (currentSlide - 1 + heroSlides.length) % heroSlides.length;
            goToSlide(prevSlide);
        }
        
        // Start autoplay
        function startAutoplay() {
            slideInterval = setInterval(showNextSlide, slideDuration);
            isPlaying = true;
            if (autoplayControl) autoplayControl.innerHTML = '<i class="ri-pause-line"></i>';
        }
        
        // Pause autoplay
        function pauseAutoplay() {
            clearInterval(slideInterval);
            isPlaying = false;
            if (autoplayControl) autoplayControl.innerHTML = '<i class="ri-play-line"></i>';
        }
        
        // Toggle autoplay
        function toggleAutoplay() {
            if (isPlaying) {
                pauseAutoplay();
            } else {
                startAutoplay();
            }
        }
        
        // Reset autoplay timer
        function resetAutoplay() {
            clearInterval(slideInterval);
            if (isPlaying) {
                startAutoplay();
            }
        }
        
        // Initialize the slideshow
        initSlideshow();
        
        // Add subtle zoom effect to active slide
        setInterval(() => {
            const activeSlide = document.querySelector('.hero-slide.active');
            if (activeSlide) {
                activeSlide.classList.toggle('zoom');
            }
        }, 8000);
    }
    
    // Image error handling with event delegation
    document.addEventListener('error', function(e) {
        if (e.target.classList.contains('artwork-img')) {
            const placeholder = document.createElement('div');
            placeholder.className = 'no-image-placeholder';
            placeholder.innerHTML = `
                <i class="ri-image-line"></i>
                <span>No Image Available</span>
            `;
            e.target.parentNode.replaceChild(placeholder, e.target);
        }
    }, true);
    
    // View toggle functionality
    const gridViewBtn = document.getElementById('gridView');
    const listViewBtn = document.getElementById('listView');
    const galleryContent = document.getElementById('galleryContent');
    
    if (gridViewBtn && listViewBtn && galleryContent) {
        gridViewBtn.addEventListener('click', function() {
            if (!this.classList.contains('active')) {
                this.classList.add('active');
                listViewBtn.classList.remove('active');
                galleryContent.classList.remove('list-view');
            }
        });
        
        listViewBtn.addEventListener('click', function() {
            if (!this.classList.contains('active')) {
                this.classList.add('active');
                gridViewBtn.classList.remove('active');
                galleryContent.classList.add('list-view');
            }
        });
    }
    
    // Sort dropdown functionality
    const sortBtn = document.getElementById('sortBtn');
    const sortDropdown = document.getElementById('sortDropdown');
    
    if (sortBtn && sortDropdown) {
        sortBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            sortDropdown.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (sortDropdown.classList.contains('show') && 
                !sortBtn.contains(event.target) && 
                !sortDropdown.contains(event.target)) {
                sortDropdown.classList.remove('show');
            }
        });
    }
    
    // Sort option selection
    const sortOptions = document.querySelectorAll('.sort-option');
    
    sortOptions.forEach(option => {
        option.addEventListener('click', function() {
            const sortType = this.getAttribute('data-sort');
            currentSort = sortType;
            
            // Close dropdown
            if (sortDropdown) sortDropdown.classList.remove('show');
            
            // Reset to first page when sorting
            currentPage = 1;
            
            // Show preloader and update gallery
            const progressInterval = showPreloader();
            setTimeout(() => {
                updateGallery(currentPage, currentSort);
                hidePreloader(progressInterval);
            }, 800);
        });
    });
    
    // Modal functionality with event delegation
    const artworkModal = document.getElementById('artworkModal');
    const modalClose = document.getElementById('modalClose');
    const modalImage = document.getElementById('modalImage');
    const modalLoader = document.getElementById('modalLoader');
    const modalArtworkTitle = document.getElementById('modalArtworkTitle');
    const modalArtist = document.getElementById('modalArtist');
    const modalDescription = document.getElementById('modalDescription');
    const modalDate = document.getElementById('modalDate');
    const modalSize = document.getElementById('modalSize');
    const modalLikes = document.getElementById('modalLikes');
    const likeBtn = document.getElementById('likeBtn');
    const downloadBtn = document.getElementById('downloadBtn');
    const shareBtn = document.getElementById('shareBtn');
    
    // Use event delegation for quick view buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.view-artwork-btn')) {
            const btn = e.target.closest('.view-artwork-btn');
            const imageUrl = btn.getAttribute('data-image');
            const title = btn.getAttribute('data-title');
            const artist = btn.getAttribute('data-artist');
            const description = btn.getAttribute('data-description');
            const date = btn.getAttribute('data-date');
            const artworkId = btn.getAttribute('data-id');
            
            // Set modal content
            if (modalArtworkTitle) modalArtworkTitle.textContent = title;
            if (modalArtist) modalArtist.textContent = 'By ' + artist;
            if (modalDescription) modalDescription.textContent = description || 'No description available.';
            if (modalDate) modalDate.textContent = date || 'Date not available';
            
            // Check if this is student gallery (hide meta section)
            const isStudentGallery = document.querySelector('.category-btn[href*="student_gallery"]');
            if (isStudentGallery && modalSize && modalLikes) {
                modalSize.parentElement.parentElement.style.display = 'none';
            } else if (modalSize && modalLikes) {
                modalSize.parentElement.parentElement.style.display = 'flex';
                if (modalSize) modalSize.textContent = '24 × 24 in'; // Default size
                if (modalLikes) modalLikes.textContent = '0'; // Start with 0 likes
            }
            
            // Reset like button
            if (likeBtn) {
                likeBtn.classList.remove('liked');
                likeBtn.innerHTML = '<i class="ri-heart-line"></i> Like';
            }
            
            // Show modal with loader
            if (artworkModal) artworkModal.classList.add('active');
            if (modalImage) modalImage.classList.remove('loaded');
            if (modalLoader) modalLoader.style.display = 'block';
            
            // Load image
            if (modalImage) {
                modalImage.onload = function() {
                    if (modalLoader) modalLoader.style.display = 'none';
                    if (modalImage) modalImage.classList.add('loaded');
                };
                
                modalImage.src = imageUrl;
            }
            
            // Set artwork ID for actions
            if (likeBtn) likeBtn.setAttribute('data-id', artworkId);
            if (downloadBtn) downloadBtn.setAttribute('data-id', artworkId);
            if (shareBtn) shareBtn.setAttribute('data-id', artworkId);
        }
    });
    
    // Close modal
    if (modalClose) modalClose.addEventListener('click', closeModal);
    if (artworkModal) {
        artworkModal.addEventListener('click', function(e) {
            if (e.target === artworkModal) closeModal();
        });
    }
    
    function closeModal() {
        if (artworkModal) artworkModal.classList.remove('active');
    }
    
    // Like functionality
    if (likeBtn) {
        likeBtn.addEventListener('click', function() {
            const artworkId = this.getAttribute('data-id');
            const isLiked = this.classList.contains('liked');
            
            if (isLiked) {
                // Unlike
                this.classList.remove('liked');
                this.innerHTML = '<i class="ri-heart-line"></i> Like';
                // Decrease like count if it's teacher gallery
                if (modalLikes) {
                    modalLikes.textContent = parseInt(modalLikes.textContent) - 1;
                }
            } else {
                // Like
                this.classList.add('liked');
                this.innerHTML = '<i class="ri-heart-fill"></i> Liked';
                // Increase like count if it's teacher gallery
                if (modalLikes) {
                    modalLikes.textContent = parseInt(modalLikes.textContent) + 1;
                }
            }
            
            // Here you would typically send an AJAX request to update the like in the database
            console.log('Artwork ' + artworkId + (isLiked ? ' unliked' : ' liked'));
        });
    }
    
    // Download functionality
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function() {
            const artworkId = this.getAttribute('data-id');
            const imageUrl = modalImage ? modalImage.src : '';
            
            if (!imageUrl) return;
            
            // Create a temporary anchor element to trigger download
            const a = document.createElement('a');
            a.href = imageUrl;
            a.download = 'artwork-' + artworkId + '.jpg';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            
            console.log('Downloading artwork ' + artworkId);
        });
    }
     
    // Pagination functionality
    if (typeof allArtworks !== 'undefined' && allArtworks.length > 0) {
        const itemsPerPage = window.itemsPerPage || 6;
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const pageButtons = document.querySelectorAll('.pagination-btn[data-page]');
        const paginationContainer = document.querySelector('.pagination');
        
        // Function to update pagination visibility
        function updatePaginationVisibility() {
            if (!paginationContainer) return;
            
            const totalPages = Math.ceil(allArtworks.length / itemsPerPage);
            
            // Only show pagination if we have more than one page
            if (totalPages <= 1) {
                paginationContainer.style.display = 'none';
            } else {
                paginationContainer.style.display = 'flex';
            }
        }
        
        // Function to sort artworks
        function sortArtworks(artworks, sortType) {
            const sorted = [...artworks];
            
            switch(sortType) {
                case 'newest':
                    return sorted.sort((a, b) => {
                        const dateA = a.creation_date || a.uploaded_at;
                        const dateB = b.creation_date || b.uploaded_at;
                        return new Date(dateB) - new Date(dateA);
                    });
                case 'oldest':
                    return sorted.sort((a, b) => {
                        const dateA = a.creation_date || a.uploaded_at;
                        const dateB = b.creation_date || b.uploaded_at;
                        return new Date(dateA) - new Date(dateB);
                    });
                case 'a-z':
                    return sorted.sort((a, b) => (a.title || '').localeCompare(b.title || ''));
                case 'z-a':
                    return sorted.sort((a, b) => (b.title || '').localeCompare(a.title || ''));
                case 'popular':
                    // For now, using uploaded_at as popularity proxy
                    return sorted.sort((a, b) => {
                        const dateA = a.uploaded_at || a.creation_date;
                        const dateB = b.uploaded_at || b.creation_date;
                        return new Date(dateB) - new Date(dateA);
                    });
                default:
                    return sorted;
            }
        }
        
        // Function to update the gallery display
        function updateGallery(page, sortType = 'newest') {
            if (!galleryContent) return;
            
            // Show preloader
            const progressInterval = showPreloader();
            galleryContent.classList.add('loading');
            
            setTimeout(() => {
                // Sort artworks based on selected sort type
                const sortedArtworks = sortArtworks(allArtworks, sortType);
                
                const startIndex = (page - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;
                const pageArtworks = sortedArtworks.slice(startIndex, endIndex);
                
                // Clear the gallery
                galleryContent.innerHTML = '';
                
                // Add artworks for current page
                if (pageArtworks.length > 0) {
                    pageArtworks.forEach((artwork, index) => {
                        const artworkElement = createArtworkElement(artwork, index);
                        galleryContent.appendChild(artworkElement);
                    });
                } else {
                    // Show appropriate message based on page number
                    if (page > 1) {
                        galleryContent.innerHTML = `
                            <div class="no-artworks-message">
                                <i class="ri-image-line"></i>
                                <h3>No More Artworks Available</h3>
                                <p>You've reached the end of the gallery.</p>
                            </div>
                        `;
                    } else {
                        galleryContent.innerHTML = `
                            <div class="no-artworks-message">
                                <i class="ri-image-line"></i>
                                <h3>No Artworks Found</h3>
                                <p>No artworks available in the gallery.</p>
                            </div>
                        `;
                    }
                }
                
                // Update pagination buttons
                updatePaginationButtons(page);
                
                // Update pagination visibility
                updatePaginationVisibility();
                
                // Remove loading class and hide preloader
                galleryContent.classList.remove('loading');
                hidePreloader(progressInterval);
            }, 800);
        }
        
        // Function to create artwork element
        function createArtworkElement(artwork, index) {
            const galleryItem = document.createElement('div');
            galleryItem.className = 'gallery-item';
            galleryItem.setAttribute('data-category', window.galleryType || 'teacher');
            galleryItem.style.animationDelay = (index * 0.1) + 's';
            
            // Handle image path - use the same logic as PHP
            let imgHtml = '';
            let imgUrl = '';
            let imgFound = false;
            
            if (artwork.image_path) {
                // Check if it's a full URL
                if (artwork.image_path.startsWith('http')) {
                    imgUrl = artwork.image_path;
                    imgFound = true;
                } else {
                    // For relative paths, try multiple locations like PHP does
                    imgUrl = '../admin/' + artwork.image_path;
                    imgFound = true;
                }
            }
            
            if (imgFound) {
                imgHtml = `
                    <img src="${imgUrl}" alt="${artwork.title || 'Untitled'}" class="artwork-img"
                         data-id="${artwork.artwork_id}"
                         data-title="${artwork.title || 'Untitled'}"
                         data-artist="${(artwork.first_name || '') + ' ' + (artwork.last_name || '')}"
                         data-description="${artwork.description || ''}"
                         data-date="${artwork.creation_date ? new Date(artwork.creation_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : ''}">
                    <div class="artwork-overlay">
                        <button class="view-btn-large view-artwork-btn" 
                                data-id="${artwork.artwork_id}"
                                data-image="${imgUrl}"
                                data-title="${artwork.title || 'Untitled'}"
                                data-artist="${(artwork.first_name || '') + ' ' + (artwork.last_name || '')}"
                                data-description="${artwork.description || ''}"
                                data-date="${artwork.creation_date ? new Date(artwork.creation_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : ''}">
                            <i class="ri-eye-line"></i> Quick View
                        </button>
                    </div>
                `;
            } else {
                imgHtml = `
                    <div class="no-image-placeholder">
                        <i class="ri-image-line"></i>
                        <span>No Image Available</span>
                    </div>
                `;
            }
            
            // Handle artist name
            const artistFirstName = artwork.first_name || '';
            const artistLastName = artwork.last_name || '';
            const artistName = artistFirstName || artistLastName ? 
                `By ${artistFirstName} ${artistLastName}` : 'By unknown artist';
            
            // Handle creation date
            let creationDate = 'Date not available';
            if (artwork.creation_date) {
                const dateObj = new Date(artwork.creation_date);
                creationDate = dateObj.toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric' 
                });
            }
            
            galleryItem.innerHTML = `
                <div class="artwork-card">
                    <div class="artwork-image">
                        ${imgHtml}
                    </div>
                    <div class="artwork-info">
                        <h3 class="artwork-title">${artwork.title || 'Untitled'}</h3>
                        <p class="artwork-artist">${artistName}</p>
                        <p class="artwork-date">${creationDate}</p>
                    </div>
                </div>
            `;
            
            return galleryItem;
        }
        
        // Function to update pagination buttons state
        function updatePaginationButtons(page) {
            const totalPages = Math.ceil(allArtworks.length / itemsPerPage);
            
            // Update prev/next buttons
            if (prevBtn) prevBtn.disabled = page === 1;
            if (nextBtn) nextBtn.disabled = page === totalPages || totalPages === 0;
            
            // Update page number buttons
            pageButtons.forEach(btn => {
                const btnPage = parseInt(btn.getAttribute('data-page'));
                // Hide page buttons that aren't needed
                if (btnPage > totalPages) {
                    btn.style.display = 'none';
                } else {
                    btn.style.display = 'block';
                }
                btn.classList.toggle('active', btnPage === page);
            });
        }
        
        // Initialize pagination visibility
        updatePaginationVisibility();
        
        // Event listeners for pagination buttons
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    updateGallery(currentPage, currentSort);
                }
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                const totalPages = Math.ceil(allArtworks.length / itemsPerPage);
                if (currentPage < totalPages) {
                    currentPage++;
                    updateGallery(currentPage, currentSort);
                }
            });
        }
        
        pageButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const page = parseInt(btn.getAttribute('data-page'));
                currentPage = page;
                updateGallery(currentPage, currentSort);
            });
        });
        
        // Initial gallery load
        updateGallery(currentPage, currentSort);
    } else {
        // If there are no artworks at all, hide pagination
        const paginationContainer = document.querySelector('.pagination');
        if (paginationContainer) paginationContainer.style.display = 'none';
        
        // Show no artworks message
        if (galleryContent) {
            galleryContent.innerHTML = `
                <div class="no-artworks-message">
                    <i class="ri-image-line"></i>
                    <h3>No Artworks Found</h3>
                    <p>No artworks available in the gallery.</p>
                </div>
            `;
        }
    }
});