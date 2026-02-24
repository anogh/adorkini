document.addEventListener('DOMContentLoaded', function() {
    console.log('Theme scripts loaded');

    // Check cart contents on page load and update button states
    checkCartOnLoad();

    // Add to Cart functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.add-to-cart-btn')) {
            e.preventDefault();
            e.stopPropagation();

            const button = e.target.closest('.add-to-cart-btn');
            const productId = button.dataset.productId;
            console.log('Add to cart clicked for product:', productId);

            // Make button jQuery-compatible for WooCommerce
            if (typeof jQuery !== 'undefined') {
                jQuery(button).data('product-id', productId);
                // Add required classes for WooCommerce script
                if (!button.classList.contains('add_to_cart_button')) {
                    button.classList.add('add_to_cart_button');
                }
                if (!button.classList.contains('ajax_add_to_cart')) {
                    button.classList.add('ajax_add_to_cart');
                }
            }

            const addIcon = button.querySelector('.add-icon');
            const addedIcon = button.querySelector('.added-icon');
            const addText = button.querySelector('.add-text');
            const addedText = button.querySelector('.added-text');

            // Prevent multiple clicks
            if (button.classList.contains('adding')) return;

            button.classList.add('adding');
            button.disabled = true;

            // Show loading state
            if (addIcon) {
                addIcon.textContent = 'refresh';
                addIcon.style.animation = 'spin 1s linear infinite';
            }
            if (addText) addText.textContent = 'Adding...';

            // Check if WooCommerce functions are available
            if (typeof wc_add_to_cart_params !== 'undefined') {
                // Use WooCommerce's built-in AJAX
                const data = {
                    action: 'woocommerce_add_to_cart',
                    product_id: productId,
                    quantity: 1
                };

                jQuery.post(wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'), data, function(response) {
                    console.log('WooCommerce add to cart response:', response);

                    if (response.error && response.product_url) {
                        window.location = response.product_url;
                        return;
                    }

                    if (!response.error) {
                        handleAddToCartSuccess(button, addIcon, addedIcon, addText, addedText);

                        // Update cart fragments
                        if (typeof jQuery !== 'undefined' && jQuery('body').trigger) {
                            jQuery('body').trigger('added_to_cart', [response.fragments, response.cart_hash]);
                        }

                        updateCartCountAndUI(true);

                    } else {
                        handleAddToCartError(button, addIcon, addedIcon, addText, addedText, response.error);
                    }
                });
            } else {
                // Fallback to custom AJAX if WooCommerce params not available
                const formData = new FormData();
                formData.append('action', 'woocommerce_add_to_cart');
                formData.append('product_id', productId);
                formData.append('quantity', 1);

                // Get admin-ajax.php URL from localized script or hardcoded fallback
                const ajaxUrl = (typeof warafy_ajax !== 'undefined') ? warafy_ajax.ajax_url : '/wp-admin/admin-ajax.php';

                fetch(ajaxUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Custom add to cart response:', data);

                    if (data.success && !data.error) {
                        handleAddToCartSuccess(button, addIcon, addedIcon, addText, addedText);
                        updateCartCountAndUI(true);
                    } else {
                        handleAddToCartError(button, addIcon, addedIcon, addText, addedText, data.error);
                    }
                })
                .catch(error => {
                    console.error('Add to cart error:', error);
                    handleAddToCartError(button, addIcon, addedIcon, addText, addedText, error);
                });
            }
        }
    });

    function handleAddToCartSuccess(button, addIcon, addedIcon, addText, addedText) {
        // Show success state
        if (addIcon) {
            addIcon.textContent = 'check';
            addIcon.style.animation = '';
        }
        if (addText) addText.textContent = 'Added to cart';

        if (addIcon) addIcon.classList.add('hidden');
        if (addText) addText.classList.add('hidden');
        if (addedIcon) addedIcon.classList.remove('hidden');
        if (addedText) addedText.classList.remove('hidden');

        // Change button to green
        button.style.backgroundColor = '#16a34a';
        button.style.color = 'white';
        button.title = 'Added to Cart';

        button.classList.remove('adding');
        button.disabled = false;

        console.log('Product successfully added to cart');
    }

    function handleAddToCartError(button, addIcon, addedIcon, addText, addedText, errorMessage) {
        // Show error state
        console.error('Add to cart error:', errorMessage);
        if (addIcon) {
            addIcon.textContent = 'close';
            addIcon.style.animation = '';
        }
        if (addText) addText.textContent = 'Error';

        button.style.backgroundColor = '#dc2626';
        button.style.color = 'white';
        button.title = errorMessage || 'Failed to add to cart';

        setTimeout(() => {
            // Reset button state
            if (addIcon) {
                addIcon.textContent = 'add_shopping_cart';
            }
            if (addText) addText.textContent = 'Add to Cart';

            button.style.backgroundColor = '';
            button.style.color = '';
            button.title = 'Add to Cart';

            if (addIcon) addIcon.classList.remove('hidden');
            if (addText) addText.classList.remove('hidden');
            if (addedIcon) addedIcon.classList.add('hidden');
            if (addedText) addedText.classList.add('hidden');
        }, 2000);

        button.classList.remove('adding');
        button.disabled = false;
    }

    // Update cart count function
    function updateCartCount() {
        console.log('Updating cart count...');
        const ajaxUrl = (typeof warafy_ajax !== 'undefined') ? warafy_ajax.ajax_url : '/wp-admin/admin-ajax.php';

        // Method 1: Use our custom fragment refresh (but skip cart count elements)
        fetch(ajaxUrl, {
            method: 'POST',
            body: new URLSearchParams({
                'action': 'warafy_refresh_fragments'
            }),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Fragment refresh response:', data);
            if (data.success && data.data && data.data.fragments) {
                // Update cart fragments but skip .cart-count elements to preserve styling
                Object.keys(data.data.fragments).forEach(key => {
                    // Skip cart count elements to preserve our styling
                    if (key.includes('.cart-count')) {
                        return;
                    }

                    const elements = document.querySelectorAll(key);
                    elements.forEach(element => {
                        if (element) {
                            element.outerHTML = data.data.fragments[key];
                        }
                    });
                });
            }
        })
        .catch(error => console.error('Fragment refresh error:', error));

        // Method 2: Direct cart count update - ONLY UPDATE TEXT, PRESERVE STYLING
        fetch(ajaxUrl, {
            method: 'POST',
            body: new URLSearchParams({
                'action': 'warafy_get_cart'
            }),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Cart data response:', data);
            if (data.success && data.data && data.data.cart) {
                const cartCount = data.data.cart.count;
                console.log('New cart count:', cartCount);

                // Update ONLY the text content of cart count elements, preserve all styling
                const cartCountElements = document.querySelectorAll('.cart-count');
                console.log('Found cart count elements:', cartCountElements.length);

                cartCountElements.forEach(element => {
                    // Only update text content, preserve all styling and classes
                    element.textContent = cartCount;

                    // Force DOM update
                    element.style.display = 'none';
                    element.offsetHeight; // Force reflow
                    element.style.display = 'flex';
                    // Add animation
                    element.style.transform = 'scale(1.2)';
                    setTimeout(() => {
                        element.style.transform = 'scale(1)';
                    }, 200);
                });

                // Also update button states
                updateButtonStates(data.data.cart);
            }
        })
        .catch(error => console.error('Cart data fetch error:', error));
    }

    function updateCartCountAndUI(refreshPageOnMobile) {
        // IMMEDIATE cart count update - no delay
        const cartCountElements = document.querySelectorAll('.cart-count');

        // Get current count and increment by 1
        if (cartCountElements.length > 0) {
            const currentCount = parseInt(cartCountElements[0].textContent || '0');
            const newCount = currentCount + 1;

            cartCountElements.forEach((element) => {
                element.textContent = newCount;
                // Force DOM update
                element.style.display = 'none';
                element.offsetHeight; // Force reflow
                element.style.display = 'flex';
                // Add animation
                element.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    element.style.transform = 'scale(1)';
                }, 200);
            });

            // Force mobile-specific update
            if (window.innerWidth <= 1024) {
                setTimeout(() => {
                    const mobileCartElements = document.querySelectorAll('.cart-count');
                    mobileCartElements.forEach(element => {
                        element.textContent = newCount;
                        element.style.display = 'flex';
                        element.style.visibility = 'visible';
                        element.style.opacity = '1';
                    });
                }, 100);
            }
        }

        // Also call the full update function for consistency
        updateCartCount();

        // Refresh page on mobile to show correct cart count
        if (refreshPageOnMobile && window.innerWidth <= 1024) {
            console.log('MOBILE DETECTED - refreshing page to show correct cart count');
            setTimeout(() => {
                window.location.reload();
            }, 1000); // Wait 1 second to show "Added to cart" state
        }
    }

    // Update button states based on cart contents
    function updateButtonStates(cartData) {
        const cartItems = cartData.items || [];
        const cartProductIds = cartItems.map(item => item.product_id.toString());

        // Update all add-to-cart buttons on the page
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            const productId = button.dataset.productId;

            if (cartProductIds.includes(productId)) {
                // Product is in cart - show "Added to cart" state
                const addIcon = button.querySelector('.add-icon');
                const addedIcon = button.querySelector('.added-icon');
                const addText = button.querySelector('.add-text');
                const addedText = button.querySelector('.added-text');

                if (addIcon) addIcon.classList.add('hidden');
                if (addText) addText.classList.add('hidden');
                if (addedIcon) addedIcon.classList.remove('hidden');
                if (addedText) addedText.classList.remove('hidden');

                button.style.backgroundColor = '#16a34a';
                button.style.color = 'white';
                button.title = 'Added to Cart';
                button.disabled = true;
            }
        });
    }

    // Check cart contents on page load and update button states
    function checkCartOnLoad() {
        console.log('Checking cart on page load...');
        const ajaxUrl = (typeof warafy_ajax !== 'undefined') ? warafy_ajax.ajax_url : '/wp-admin/admin-ajax.php';

        fetch(ajaxUrl, {
            method: 'POST',
            body: new URLSearchParams({
                'action': 'warafy_get_cart'
            }),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Initial cart data:', data);
            if (data.success && data.data && data.data.cart) {
                const cartCount = data.data.cart.count;

                // Update all cart count elements with the specific class
                const cartCountElements = document.querySelectorAll('.cart-count');

                cartCountElements.forEach(element => {
                    element.textContent = cartCount;
                });

                // Update button states
                updateButtonStates(data.data.cart);
            }
        })
        .catch(error => console.error('Initial cart check error:', error));
    }
});
