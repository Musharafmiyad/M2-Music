document.addEventListener('DOMContentLoaded', function() {
    initializeSearch();
    initializeStarRating();
    loadReviews(1);
    initializeFormSubmission();
});

function initializeSearch() {
    const searchIcon = document.getElementById('search-icon');
    const searchBox = document.getElementById('searchBox');
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    
    searchIcon.addEventListener('click', () => {
        searchBox.classList.toggle('active');
        if (searchBox.classList.contains('active')) {
            searchInput.focus();
        }
    });
    
    searchButton.addEventListener('click', handleSearch);
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            handleSearch();
        }
    });
}

function handleSearch() {
    const searchInput = document.getElementById('searchInput');
    const searchTerm = searchInput.value.trim();
    if (searchTerm) {
        // Implement search functionality
        console.log('Searching for:', searchTerm);
    }
}

function initializeStarRating() {
    const stars = document.querySelectorAll('.star-rating i');
    const ratingInput = document.getElementById('rating');

    stars.forEach(star => {
        star.addEventListener('click', () => {
            const rating = star.getAttribute('data-rating');
            ratingInput.value = rating;
            updateStars(rating);
        });

        star.addEventListener('mouseover', () => {
            const rating = star.getAttribute('data-rating');
            highlightStars(rating);
        });
    });

    document.querySelector('.star-rating').addEventListener('mouseleave', () => {
        const currentRating = ratingInput.value;
        highlightStars(currentRating);
    });
}

function updateStars(rating) {
    highlightStars(rating);
}

function highlightStars(rating) {
    const stars = document.querySelectorAll('.star-rating i');
    stars.forEach(star => {
        const starRating = star.getAttribute('data-rating');
        star.classList.remove('bx-star', 'bxs-star');
        if (starRating <= rating) {
            star.classList.add('bxs-star');
        } else {
            star.classList.add('bx-star');
        }
    });
}

function validateForm(formData) {
    const name = formData.get('name').trim();
    const rating = parseInt(formData.get('rating'));
    const review = formData.get('review').trim();
    
    if (!name) return 'Please enter your name';
    if (!rating || rating < 1) return 'Please select a rating';
    if (!review) return 'Please enter your review';
    
    return null;
}

function initializeFormSubmission() {
    const form = document.getElementById('customerReviewForm');
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(form);
        const error = validateForm(formData);
        
        if (error) {
            alert(error);
            return;
        }

        try {
            const response = await fetch('/m2music/submit_review.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('Review submitted successfully!');
                form.reset();
                updateStars(0);
                loadReviews(1);
            } else {
                alert('Error submitting review: ' + result.message);
            }
        } catch (error) {
            console.error('Submission error:', error);
            alert('Error submitting review. Please try again.');
        }
    });
}

async function loadReviews(page) {
    try {
        const response = await fetch(`/m2music/get_reviews.php?page=${page}`);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        
        if (data.success) {
            displayReviews(data.reviews);
            displayPagination(data.totalPages, data.currentPage);
        } else {
            throw new Error(data.message || 'Error loading reviews');
        }
    } catch (error) {
        console.error('Error loading reviews:', error.message); // Ensure error.message is used
        document.getElementById('reviewsContainer').innerHTML = 
            '<p class="error-message">Error loading reviews. Please try again later.</p>';
    }
}

function displayReviews(reviews) {
    const container = document.getElementById('reviewsContainer');
    
    if (!reviews || reviews.length === 0) {
        container.innerHTML = '<p class="no-reviews">No reviews found.</p>';
        return;
    }
    
    container.innerHTML = '';
    reviews.forEach(review => {
        const reviewElement = createReviewElement(review);
        container.appendChild(reviewElement);
    });
}

function createReviewElement(review) {
    const reviewBox = document.createElement('div');
    reviewBox.className = 'review-box';
    
    reviewBox.innerHTML = `
        <div class="review-header">
            <h4 class="reviewer-name">${review.name}</h4>
            <span class="review-date">${review.date}</span>
        </div>
        <div class="review-rating">
            ${createStarRating(review.rating)}
        </div>
        <p class="review-content">${review.review}</p>
    `;
    
    return reviewBox;
}

function createStarRating(rating) {
    return Array(5).fill(0).map((_, index) => 
        `<i class='bx ${index < rating ? 'bxs-star' : 'bx-star'}'></i>`
    ).join('');
}

function displayPagination(totalPages, currentPage) {
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';
    
    if (totalPages <= 1) return;
    
    // Previous button
    if (currentPage > 1) {
        const prevButton = document.createElement('button');
        prevButton.textContent = '←';
        prevButton.className = 'pagination-nav';
        prevButton.addEventListener('click', () => loadReviews(currentPage - 1));
        pagination.appendChild(prevButton);
    }
    
    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (
            i === 1 || 
            i === totalPages || 
            (i >= currentPage - 1 && i <= currentPage + 1)
        ) {
            const button = document.createElement('button');
            button.textContent = i;
            button.className = i === currentPage ? 'active' : '';
            button.addEventListener('click', () => loadReviews(i));
            pagination.appendChild(button);
        } else if (
            i === currentPage - 2 ||
            i === currentPage + 2
        ) {
            const ellipsis = document.createElement('span');
            ellipsis.textContent = '...';
            ellipsis.className = 'pagination-ellipsis';
            pagination.appendChild(ellipsis);
        }
    }
    
    // Next button
    if (currentPage < totalPages) {
        const nextButton = document.createElement('button');
        nextButton.textContent = '→';
        nextButton.className = 'pagination-nav';
        nextButton.addEventListener('click', () => loadReviews(currentPage + 1));
        pagination.appendChild(nextButton);
    }
}