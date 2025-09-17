// news script
document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    const searchInput = document.getElementById('searchInput');
    const filterButtons = document.querySelectorAll('#filterButtons .btn');
    const blogPosts = document.querySelectorAll('.blog-post');
    const newsCards = document.querySelectorAll('.news-card');
    const noResults = document.getElementById('noResults');
    
    // Current active filter
    let activeFilter = 'all';
    
    // Initially animate all cards with a staggered delay
    setTimeout(() => {
    newsCards.forEach((card, index) => {
        setTimeout(() => {
        card.classList.add('visible');
        }, index * 100); // 100ms delay between each card
    });
    }, 100);
    
    // Add event listeners to filter buttons
    filterButtons.forEach(button => {
    button.addEventListener('click', function() {
        // Add animation to button click
        this.style.transform = 'scale(0.95)';
        setTimeout(() => {
        this.style.transform = '';
        }, 200);
        
        // Remove active class from all buttons
        filterButtons.forEach(btn => btn.classList.remove('active'));
        
        // Add active class to clicked button
        this.classList.add('active');
        
        // Get the filter value
        activeFilter = this.getAttribute('data-filter');
        
        // Apply filters
        applyFilters();
    });
    });
    
    // Add event listener to search input
    searchInput.addEventListener('input', function() {
    // Add a subtle animation to the search input
    this.style.transform = 'scale(1.02)';
    setTimeout(() => {
        this.style.transform = '';
    }, 200);
    
    // Apply filters after a short delay to improve performance
    clearTimeout(this.timer);
    this.timer = setTimeout(applyFilters, 300);
    });
    
    // Function to apply both search and filter
    function applyFilters() {
    const searchTerm = searchInput.value.toLowerCase();
    let visiblePosts = 0;
    
    // First, hide all posts with animation
    blogPosts.forEach(post => {
        const card = post.querySelector('.news-card');
        card.classList.remove('visible');
        post.style.display = 'none';
    });
    
    // Hide no results message
    noResults.classList.remove('visible');
    
    // Small delay to allow hide animation to complete
    setTimeout(() => {
        // Loop through all blog posts
        blogPosts.forEach((post, index) => {
        const title = post.querySelector('.card-title').textContent.toLowerCase();
        const desc = post.querySelector('.news-desc').textContent.toLowerCase();
        const tag = post.querySelector('.news-tag').textContent.toLowerCase();
        const postTags = post.getAttribute('data-tags');
        
        // Check if post matches search term
        const matchesSearch = searchTerm === '' || 
                                title.includes(searchTerm) || 
                                desc.includes(searchTerm) || 
                                tag.includes(searchTerm);
        
        // Check if post matches active filter
        const matchesFilter = activeFilter === 'all' || 
                                tag.includes(activeFilter.toLowerCase()) || 
                                postTags.includes(activeFilter.toLowerCase());
        
        // Show or hide post based on filters with staggered animation
        if (matchesSearch && matchesFilter) {
            post.style.display = 'block';
            visiblePosts++;
            
            // Animate in with a delay based on index
            setTimeout(() => {
            post.querySelector('.news-card').classList.add('visible');
            }, index * 80);
        } else {
            post.style.display = 'none';
        }
        });
        
        // Show no results message if no posts are visible
        if (visiblePosts === 0) {
        noResults.style.display = 'block';
        setTimeout(() => {
            noResults.classList.add('visible');
        }, 50);
        } else {
        noResults.style.display = 'none';
        }
    }, 50);
    }
});