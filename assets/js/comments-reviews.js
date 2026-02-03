// Make sure warafy_ajax is available
if (typeof warafy_ajax === 'undefined') {
    console.error('warafy_ajax object not found');
    window.warafy_ajax = {
        ajax_url: '/wp-admin/admin-ajax.php'
    };
}

document.addEventListener('DOMContentLoaded', function() {
    // Debug: Check if warafy_ajax is available
    console.log('warafy_ajax:', warafy_ajax);
    
    // Rating stars functionality - Desktop
    const ratingStars = document.querySelectorAll('#rating-stars .rating-star');
    const selectedRating = document.getElementById('selected-rating');
    
    if (ratingStars.length > 0 && selectedRating) {
        setupRatingStars(ratingStars, selectedRating);
    }
    
    // Rating stars functionality - Mobile
    const ratingStarsMobile = document.querySelectorAll('#rating-stars-mobile .rating-star');
    const selectedRatingMobile = document.getElementById('selected-rating-mobile');
    
    if (ratingStarsMobile.length > 0 && selectedRatingMobile) {
        setupRatingStars(ratingStarsMobile, selectedRatingMobile);
    }
    
    function setupRatingStars(stars, ratingInput) {
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.dataset.rating);
                ratingInput.value = rating;
                updateRatingStars(stars, rating);
            });
            
            star.addEventListener('mouseenter', function() {
                const rating = parseInt(this.dataset.rating);
                updateRatingStars(stars, rating);
            });
        });
        
        stars[0].parentElement.addEventListener('mouseleave', function() {
            const currentRating = parseInt(ratingInput.value) || 0;
            updateRatingStars(stars, currentRating);
        });
    }
    
    function updateRatingStars(stars, rating) {
        stars.forEach((star, index) => {
            const starIcon = star.querySelector('span');
            if (index < rating) {
                starIcon.classList.add('text-yellow-500');
                starIcon.classList.remove('text-gray-300');
                starIcon.style.fontVariationSettings = "'FILL' 1";
            } else {
                starIcon.classList.remove('text-yellow-500');
                starIcon.classList.add('text-gray-300');
                starIcon.style.fontVariationSettings = "";
            }
        });
    }
    
    // Comment form submission - Desktop
    const commentForm = document.getElementById('warafy-comment-form');
    if (commentForm) {
        setupCommentForm(commentForm);
    }
    
    // Comment form submission - Mobile
    const commentFormMobile = document.getElementById('warafy-comment-form-mobile');
    if (commentFormMobile) {
        setupCommentForm(commentFormMobile);
    }
    
    function setupCommentForm(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            // Add the action parameter
            formData.append('action', 'warafy_submit_comment');
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.textContent = 'Posting...';
            
            fetch(warafy_ajax.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear form
                    form.reset();
                    
                    // Show success message
                    showNotification('Comment posted successfully!', 'success');
                    
                    // Reload comments after a short delay
                    setTimeout(() => {
                        loadComments();
                    }, 1000);
                } else {
                    showNotification(data.data.message || 'Error posting comment', 'error');
                }
            })
            .catch(error => {
                console.error('AJAX Error:', error);
                showNotification('Network error. Please try again.', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });
    }
    
    // Review form submission - Desktop
    const reviewForm = document.getElementById('warafy-review-form');
    if (reviewForm) {
        setupReviewForm(reviewForm);
    }
    
    // Review form submission - Mobile
    const reviewFormMobile = document.getElementById('warafy-review-form-mobile');
    if (reviewFormMobile) {
        setupReviewForm(reviewFormMobile);
    }
    
    function setupReviewForm(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            // Add the action parameter
            formData.append('action', 'warafy_submit_review');
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
            
            fetch(warafy_ajax.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear form
                    form.reset();
                    
                    // Reset rating stars
                    const ratingInput = form.querySelector('input[name="rating"]');
                    const ratingStars = form.querySelectorAll('.rating-star');
                    if (ratingInput) ratingInput.value = 0;
                    if (ratingStars.length > 0) updateRatingStars(ratingStars, 0);
                    
                    // Show success message
                    showNotification('Review submitted successfully!', 'success');
                    
                    // Reload reviews after a short delay
                    setTimeout(() => {
                        loadReviews();
                    }, 1000);
                } else {
                    showNotification(data.data.message || 'Error submitting review', 'error');
                }
            })
            .catch(error => {
                console.error('AJAX Error:', error);
                showNotification('Network error. Please try again.', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });
    }
    
    // Load comments function
    function loadComments() {
        const productId = document.querySelector('input[name="product_id"]').value;
        const commentsList = document.getElementById('warafy-comments-list');
        const commentsListMobile = document.getElementById('warafy-comments-list-mobile');
        
        if (!commentsList && !commentsListMobile) return;
        
        const formData = new FormData();
        formData.append('action', 'warafy_load_comments');
        formData.append('product_id', productId);
        
        fetch(warafy_ajax.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (commentsList) commentsList.innerHTML = data.data.html;
                if (commentsListMobile) commentsListMobile.innerHTML = data.data.html;
            }
        })
        .catch(error => {
            console.error('Error loading comments:', error);
        });
    }
    
    // Load reviews function
    function loadReviews() {
        const productId = document.querySelector('input[name="product_id"]').value;
        const reviewsList = document.getElementById('warafy-reviews-list');
        const reviewsListMobile = document.getElementById('warafy-reviews-list-mobile');
        
        if (!reviewsList && !reviewsListMobile) return;
        
        const formData = new FormData();
        formData.append('action', 'warafy_load_reviews');
        formData.append('product_id', productId);
        
        fetch(warafy_ajax.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (reviewsList) reviewsList.innerHTML = data.data.html;
                if (reviewsListMobile) reviewsListMobile.innerHTML = data.data.html;
            }
        })
        .catch(error => {
            console.error('Error loading reviews:', error);
        });
    }
    
    // Notification function
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.warafy-notification');
        existingNotifications.forEach(notification => notification.remove());
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `warafy-notification fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
        
        // Set colors based on type
        if (type === 'success') {
            notification.classList.add('bg-green-500', 'text-white');
        } else if (type === 'error') {
            notification.classList.add('bg-red-500', 'text-white');
        } else {
            notification.classList.add('bg-blue-500', 'text-white');
        }
        
        notification.innerHTML = `
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined" data-icon="${type === 'success' ? 'check_circle' : type === 'error' ? 'error' : 'info'}"></span>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
            notification.classList.add('translate-x-0');
        }, 100);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.classList.remove('translate-x-0');
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
    
    // Character counter for textareas
    const commentTextareas = document.querySelectorAll('textarea[name="comment_text"]');
    const reviewTextareas = document.querySelectorAll('textarea[name="review_text"]');
    
    commentTextareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            const remaining = 1000 - this.value.length;
            updateCharacterCounter(this, remaining);
        });
    });
    
    reviewTextareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            const remaining = 2000 - this.value.length;
            updateCharacterCounter(this, remaining);
        });
    });
    
    function updateCharacterCounter(textarea, remaining) {
        let counter = textarea.parentNode.querySelector('.char-counter');
        if (!counter) {
            counter = document.createElement('div');
            counter.className = 'char-counter text-xs text-gray-500 dark:text-gray-400 mt-1';
            textarea.parentNode.appendChild(counter);
        }
        
        counter.textContent = `${remaining} characters remaining`;
        
        if (remaining < 50) {
            counter.classList.add('text-red-500');
            counter.classList.remove('text-gray-500', 'dark:text-gray-400');
        } else {
            counter.classList.remove('text-red-500');
            counter.classList.add('text-gray-500', 'dark:text-gray-400');
        }
    }
});
