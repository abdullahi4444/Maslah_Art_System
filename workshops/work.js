// Function to fetch workshops from the server
async function fetchWorkshops() {
    try {
        const response = await fetch('workshops.php');
        const result = await response.json();
        
        if (result.success) {
            displayWorkshops(result.data);
        } else {
            console.error('Failed to fetch workshops:', result.message);
        }
    } catch (error) {
        console.error('Error fetching workshops:', error);
    }
}

// Function to display workshops
function displayWorkshops(workshops) {
    const workshopGrid = document.querySelector('.workshop-grid');
    workshopGrid.innerHTML = ''; // Clear existing content
    
    workshops.forEach(workshop => {
        const workshopCard = createWorkshopCard(workshop);
        workshopGrid.appendChild(workshopCard);
    });
    
    // Add event listeners for read more buttons
    addReadMoreListeners();
}

// Function to create a workshop card
function createWorkshopCard(workshop) {
    const card = document.createElement('div');
    card.className = `workshop-card ${workshop.status === 'upcoming' ? 'active' : ''}`;
    card.setAttribute('data-category', workshop.status);
    
    // Generate badge HTML if needed
    let badgeHTML = '';
    if (workshop.badge_type !== 'none') {
        const badgeText = {
            'trending': '<i class="fas fa-star"></i> Featured',
            'popular': '<i class="fas fa-users"></i> Popular',
            'limited': '<i class="fas fa-user-clock"></i> Limited Seats',
            'new': '<i class="fas fa-video"></i> Recording Available'
        };
        
        badgeHTML = `<span class="workshop-badge badge-${workshop.badge_type}">
            ${badgeText[workshop.badge_type]}
        </span>`;
    }
    
    // Generate stars based on rating
    const fullStars = Math.floor(workshop.rating);
    const hasHalfStar = workshop.rating % 1 >= 0.5;
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
    
    let starsHTML = '';
    for (let i = 0; i < fullStars; i++) {
        starsHTML += '<i class="fas fa-star"></i>';
    }
    if (hasHalfStar) {
        starsHTML += '<i class="fas fa-star-half-alt"></i>';
    }
    for (let i = 0; i < emptyStars; i++) {
        starsHTML += '<i class="far fa-star"></i>';
    }
    
    // Determine button text based on status
    let buttonText = '';
    if (workshop.status === 'upcoming') {
        buttonText = '<i class="fas fa-pen-fancy"></i> Enroll Now';
    } else if (workshop.status === 'ongoing') {
        buttonText = '<i class="fas fa-user-plus"></i> Join Now';
    } else {
        buttonText = '<i class="fas fa-play-circle"></i> Watch Now';
    }
    
    // Get appropriate icon for category
    const categoryIcons = {
        'Drawing': 'pencil-alt',
        'Painting': 'paint-brush',
        'Sketching': 'city',
        'Portraits': 'portrait',
        'Figure Drawing': 'user',
        'Digital Art': 'tablet-alt'
    };
    
    const categoryIcon = categoryIcons[workshop.category] || 'pencil-alt';
    
    card.innerHTML = `
        <div class="workshop-thumbnail-container">
            <img src="${workshop.thumbnail_url}" alt="${workshop.title}" class="workshop-thumbnail">
            ${badgeHTML}
        </div>
        <div class="workshop-content">
            <h3 class="workshop-title">${workshop.title}</h3>
            <div class="workshop-description collapsed">
                ${workshop.description}
                <button class="read-more-btn">
                    Read more <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            
            <div class="workshop-meta">
                <img src="maslah.jpg" alt="Instructor" class="instructor-avatar">
                <div class="instructor-info">
                    <div class="instructor-name">${workshop.instructor_name}</div>
                    <div class="instructor-role">${workshop.instructor_role}</div>
                </div>
            </div>
            
            <div class="workshop-details">
                <div class="workshop-rating">
                    <div class="stars">
                        ${starsHTML}
                    </div>
                    <span class="rating-value">${workshop.rating}</span>
                </div>
                <span class="workshop-category">
                    <i class="fas fa-${categoryIcon}"></i> ${workshop.category}
                </span>
            </div>
            
            <div class="workshop-footer">
                <span class="difficulty-badge ${workshop.difficulty.toLowerCase()}">${workshop.difficulty}</span>
                <span class="workshop-price">$${workshop.price}</span>
            </div>
            
            <button class="join-btn" data-workshop="${workshop.title}">
                ${buttonText}
            </button>
        </div>
    `;
    
    return card;
}

// Function to add event listeners for read more buttons
function addReadMoreListeners() {
    const readMoreButtons = document.querySelectorAll('.read-more-btn');
    readMoreButtons.forEach(button => {
        button.addEventListener('click', function() {
            const description = this.parentElement;
            description.classList.toggle('collapsed');
            
            const icon = this.querySelector('i');
            if (description.classList.contains('collapsed')) {
                this.innerHTML = 'Read more <i class="fas fa-chevron-down"></i>';
            } else {
                this.innerHTML = 'Read less <i class="fas fa-chevron-up"></i>';
            }
        });
    });
    
    // Add event listeners for enroll/join buttons
    const joinButtons = document.querySelectorAll('.join-btn');
    joinButtons.forEach(button => {
        button.addEventListener('click', function() {
            const workshopTitle = this.getAttribute('data-workshop');
            alert(`You've enrolled in: ${workshopTitle}`);
            // Here you would typically redirect to a payment or registration page
        });
    });
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    fetchWorkshops();
});