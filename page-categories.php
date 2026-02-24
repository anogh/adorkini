<?php
/*
Template Name: Categories List
*/
get_header(); ?>

<!-- Mobile Content -->
<div class="mobile-content-wrapper lg:hidden">
    <div class="container mx-auto px-4 py-6">
        <!-- Page Title -->
        <div class="flex flex-wrap justify-between gap-3 p-4">
            <p class="text-slate-900 dark:text-white text-2xl font-black leading-tight tracking-[-0.033em]">Shop by Category</p>
        </div>

        <!-- Horizontal Categories Scroll -->
        <div class="categories-scroll-container mb-8">
            <div class="categories-horizontal-scroll flex gap-4 overflow-x-auto pb-4 px-4" id="categoriesContainer">
                <?php
                $categories = get_terms( ['taxonomy' => 'product_cat', 'hide_empty' => false] );
                $category_icons = [
                    'Automotive' => 'directions_car',
                    'Bags' => 'shopping_bag',
                    'Bedding' => 'bed',
                    'Computers' => 'computer',
                    'DIY' => 'handyman',
                    'Electronics' => 'devices',
                    'Fashion' => 'checkroom',
                    'Furniture' => 'chair',
                    'Default' => 'category'
                ];
                
                if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
                    foreach ( $categories as $category ) {
                        $icon_name = 'Default';
                        foreach ($category_icons as $key => $icon) {
                            if (stripos($category->name, $key) !== false) {
                                $icon_name = $key;
                                break;
                            }
                        }
                        $icon = $category_icons[$icon_name];
                        ?>
                        <div class="category-item flex-shrink-0 cursor-pointer transition-all duration-200 hover:scale-105" data-category="<?php echo esc_attr($category->slug); ?>">
                            <div class="category-circle w-16 h-16 rounded-full flex items-center justify-center mb-2 transition-all duration-200 bg-gray-100 dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                <span class="material-symbols-outlined text-2xl text-gray-600 dark:text-gray-400 category-icon" data-icon="<?php echo esc_attr($icon); ?>"></span>
                            </div>
                            <p class="text-xs text-center text-gray-700 dark:text-gray-300 font-medium whitespace-nowrap">
                                <?php echo esc_html($category->name); ?>
                            </p>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="products-section px-4">
            <div id="productsGrid" class="grid grid-cols-2 gap-4 mb-8">
                <?php
                $args = [
                    'post_type' => 'product',
                    'posts_per_page' => 20,
                    'post_status' => 'publish'
                ];
                $products_query = new WP_Query($args);
                
                if ($products_query->have_posts()) {
                    while ($products_query->have_posts()) {
                        $products_query->the_post();
                        global $product;
                        
                        $product_cats = wp_get_post_terms(get_the_ID(), 'product_cat');
                        $category_slugs = [];
                        foreach ($product_cats as $cat) {
                            $category_slugs[] = $cat->slug;
                        }
                        ?>
                        <div class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-background-dark shadow-sm" data-categories="<?php echo esc_attr(implode(',', $category_slugs)); ?>">
                            <div class="relative">
                                <a href="<?php the_permalink(); ?>" class="block w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-lg" style='background-image: url("<?php echo has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'woocommerce_thumbnail') : 'https://via.placeholder.com/300'; ?>");'></a>
                            </div>
                            <div class="flex flex-col flex-1 justify-between gap-4">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">
                                        <a href="<?php the_permalink(); ?>" class="hover:text-primary transition-colors line-clamp-1"><?php the_title(); ?></a>
                                    </h3>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo $product->get_price_html(); ?></p>
                                </div>
                                <div class="flex gap-2">
                                    <?php if ($product->is_in_stock()) : ?>
                                        <button class="add-to-cart-btn flex-1 flex items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary/10 text-primary text-sm font-bold hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 transition-colors" data-product-id="<?php echo $product->get_id(); ?>" title="Add to Cart">
                                            <span class="material-symbols-outlined text-sm add-icon mr-2" data-icon="add_shopping_cart"></span>
                                            <span class="add-text truncate">Add</span>
                                            <span class="material-symbols-outlined text-sm added-icon hidden mr-2" data-icon="check"></span>
                                            <span class="added-text hidden truncate">Added</span>
                                        </button>
                                    <?php else : ?>
                                        <button class="flex-1 flex items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-gray-300 text-gray-500 text-sm font-medium cursor-not-allowed" disabled title="Out of Stock">
                                            <span class="material-symbols-outlined text-sm mr-2" data-icon="remove_shopping_cart"></span>
                                            <span class="truncate">Out of Stock</span>
                                        </button>
                                    <?php endif; ?>
                                    <button class="warafy-wishlist-btn flex-none w-10 h-10 flex items-center justify-center rounded-lg bg-primary/10 text-primary hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 transition-colors" data-product-id="<?php echo $product->get_id(); ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    wp_reset_postdata();
                }
                ?>
            </div>

            <!-- Pagination -->
            <div class="pagination-container flex justify-center items-center gap-2" id="pagination">
                <button class="pagination-btn px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed" id="prevBtn" disabled>
                    <span class="material-symbols-outlined text-sm" data-icon="chevron_left"></span>
                </button>
                <div class="pagination-numbers flex gap-1" id="pageNumbers">
                    <button class="page-number px-3 py-2 rounded-lg bg-blue-600 text-white" data-page="1">1</button>
                    <button class="page-number px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700" data-page="2">2</button>
                    <button class="page-number px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700" data-page="3">3</button>
                </div>
                <button class="pagination-btn px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700" id="nextBtn">
                    <span class="material-symbols-outlined text-sm" data-icon="chevron_right"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Desktop Content -->
<div class="hidden lg:block">
    <main class="flex-grow pb-24">
        <div class="container mx-auto px-4 py-6">
            <!-- Page Title -->
            <div class="flex flex-wrap justify-between gap-3 p-4">
                <p class="text-slate-900 dark:text-white text-3xl font-black leading-tight tracking-[-0.033em]">Shop by Category</p>
            </div>

            <!-- Horizontal Categories Scroll -->
            <div class="categories-scroll-container mb-8">
                <div class="categories-horizontal-scroll flex gap-4 overflow-x-auto pb-4 px-4" id="categoriesContainer">
                    <?php
                    $categories = get_terms( ['taxonomy' => 'product_cat', 'hide_empty' => false] );
                    $category_icons = [
                        'Automotive' => 'directions_car',
                        'Bags' => 'shopping_bag',
                        'Bedding' => 'bed',
                        'Computers' => 'computer',
                        'DIY' => 'handyman',
                        'Electronics' => 'devices',
                        'Fashion' => 'checkroom',
                        'Furniture' => 'chair',
                        'Default' => 'category'
                    ];
                    
                    if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
                        foreach ( $categories as $category ) {
                            $icon_name = 'Default';
                            foreach ($category_icons as $key => $icon) {
                                if (stripos($category->name, $key) !== false) {
                                    $icon_name = $key;
                                    break;
                                }
                            }
                            $icon = $category_icons[$icon_name];
                            ?>
                            <div class="category-item flex-shrink-0 cursor-pointer transition-all duration-200 hover:scale-105" data-category="<?php echo esc_attr($category->slug); ?>">
                                <div class="category-circle w-16 h-16 sm:w-20 sm:h-20 rounded-full flex items-center justify-center mb-2 transition-all duration-200 bg-gray-100 dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                    <span class="material-symbols-outlined text-2xl sm:text-3xl text-gray-600 dark:text-gray-400 category-icon" data-icon="<?php echo esc_attr($icon); ?>"></span>
                                </div>
                                <p class="text-xs sm:text-sm text-center text-gray-700 dark:text-gray-300 font-medium whitespace-nowrap">
                                    <?php echo esc_html($category->name); ?>
                                </p>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="products-section px-4">
                <div id="productsGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-8">
                    <?php
                    $args = [
                        'post_type' => 'product',
                        'posts_per_page' => 20,
                        'post_status' => 'publish'
                    ];
                    $products_query = new WP_Query($args);
                    
                    if ($products_query->have_posts()) {
                        while ($products_query->have_posts()) {
                            $products_query->the_post();
                            global $product;
                            
                            $product_cats = wp_get_post_terms(get_the_ID(), 'product_cat');
                            $category_slugs = [];
                            foreach ($product_cats as $cat) {
                                $category_slugs[] = $cat->slug;
                            }
                            ?>
                            <div class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-background-dark shadow-sm" data-categories="<?php echo esc_attr(implode(',', $category_slugs)); ?>">
                                <div class="relative">
                                    <a href="<?php the_permalink(); ?>" class="block w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-lg" style='background-image: url("<?php echo has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'woocommerce_thumbnail') : 'https://via.placeholder.com/300'; ?>");'></a>
                                </div>
                                <div class="flex flex-col flex-1 justify-between gap-4">
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">
                                            <a href="<?php the_permalink(); ?>" class="hover:text-primary transition-colors line-clamp-1"><?php the_title(); ?></a>
                                        </h3>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo $product->get_price_html(); ?></p>
                                    </div>
                                    <div class="flex gap-2">
                                        <?php if ($product->is_in_stock()) : ?>
                                            <button class="add-to-cart-btn flex-1 flex items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary/10 text-primary text-sm font-bold hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 transition-colors" data-product-id="<?php echo $product->get_id(); ?>" title="Add to Cart">
                                                <span class="material-symbols-outlined text-sm add-icon mr-2" data-icon="add_shopping_cart"></span>
                                                <span class="add-text truncate">Add</span>
                                                <span class="material-symbols-outlined text-sm added-icon hidden mr-2" data-icon="check"></span>
                                                <span class="added-text hidden truncate">Added</span>
                                            </button>
                                        <?php else : ?>
                                            <button class="flex-1 flex items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-gray-300 text-gray-500 text-sm font-medium cursor-not-allowed" disabled title="Out of Stock">
                                                <span class="material-symbols-outlined text-sm mr-2" data-icon="remove_shopping_cart"></span>
                                                <span class="truncate">Out of Stock</span>
                                            </button>
                                        <?php endif; ?>
                                        <button class="warafy-wishlist-btn flex-none w-10 h-10 flex items-center justify-center rounded-lg bg-primary/10 text-primary hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 transition-colors" data-product-id="<?php echo $product->get_id(); ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        wp_reset_postdata();
                    }
                    ?>
                </div>

                <!-- Pagination -->
                <div class="pagination-container flex justify-center items-center gap-2" id="pagination">
                    <button class="pagination-btn px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed" id="prevBtn" disabled>
                        <span class="material-symbols-outlined text-sm" data-icon="chevron_left"></span>
                    </button>
                    <div class="pagination-numbers flex gap-1" id="pageNumbers">
                        <button class="page-number px-3 py-2 rounded-lg bg-blue-600 text-white" data-page="1">1</button>
                        <button class="page-number px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700" data-page="2">2</button>
                        <button class="page-number px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700" data-page="3">3</button>
                    </div>
                    <button class="pagination-btn px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700" id="nextBtn">
                        <span class="material-symbols-outlined text-sm" data-icon="chevron_right"></span>
                    </button>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Debug: Log when page loads
    console.log('Categories page loaded');
    console.log('jQuery available:', typeof jQuery !== 'undefined');
    console.log('WooCommerce params available:', typeof wc_add_to_cart_params !== 'undefined');
    
    const categoriesContainer = document.getElementById('categoriesContainer');
    const productsGrid = document.getElementById('productsGrid');
    const categoryItems = document.querySelectorAll('.category-item');
    const productCards = document.querySelectorAll('.product-card');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const pageNumbers = document.getElementById('pageNumbers');
    
    let currentPage = 1;
    let selectedCategory = null;
    const productsPerPage = 20;
    
    // Category selection
    categoryItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove previous selection
            categoryItems.forEach(cat => {
                cat.querySelector('.category-circle').classList.remove('bg-blue-100', 'dark:bg-blue-900/30');
                cat.querySelector('.category-icon').classList.remove('text-blue-600', 'dark:text-blue-400');
                cat.querySelector('.category-circle').classList.add('bg-gray-100', 'dark:bg-gray-800');
                cat.querySelector('.category-icon').classList.add('text-gray-600', 'dark:text-gray-400');
            });
            
            // Add selection to clicked category
            const circle = this.querySelector('.category-circle');
            const icon = this.querySelector('.category-icon');
            circle.classList.remove('bg-gray-100', 'dark:bg-gray-800');
            circle.classList.add('bg-blue-100', 'dark:bg-blue-900/30');
            icon.classList.remove('text-gray-600', 'dark:text-gray-400');
            icon.classList.add('text-blue-600', 'dark:text-blue-400');
            
            // Filter products
            selectedCategory = this.dataset.category;
            filterProducts();
        });
    });
    
    // Add to Cart functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.add-to-cart-btn')) {
            e.preventDefault();
            e.stopPropagation();
            
            const button = e.target.closest('.add-to-cart-btn');
            const productId = button.dataset.productId;
            const addIcon = button.querySelector('.add-icon');
            const addedIcon = button.querySelector('.added-icon');
            const addText = button.querySelector('.add-text');
            const addedText = button.querySelector('.added-text');
            
            // Prevent multiple clicks
            if (button.classList.contains('adding')) return;
            
            button.classList.add('adding');
            button.disabled = true;
            
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
                        // Show success state
                        addIcon.classList.add('hidden');
                        addText.classList.add('hidden');
                        addedIcon.classList.remove('hidden');
                        addedText.classList.remove('hidden');
                        button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                        button.classList.add('bg-green-600', 'hover:bg-green-700');
                        button.title = 'Added to Cart';
                        
                        // Update cart fragments
                        if (typeof jQuery !== 'undefined' && jQuery('body').trigger) {
                            jQuery('body').trigger('added_to_cart', [response.fragments, response.cart_hash, button]);
                        }
                        
                        // Update cart count instantly
                        updateCartCount();
                        
                        // Refresh page briefly to ensure all states are updated
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        // Show error state
                        console.error('Add to cart error:', response.error);
                        button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                        button.classList.add('bg-red-600');
                        button.title = response.error || 'Failed to add to cart';
                        
                        setTimeout(() => {
                            button.classList.remove('bg-red-600');
                            button.classList.add('bg-blue-600', 'hover:bg-blue-700');
                            button.classList.remove('adding');
                            button.disabled = false;
                        }, 2000);
                    }
                });
            } else {
                // Fallback to custom AJAX if WooCommerce params not available
                const formData = new FormData();
                formData.append('action', 'woocommerce_add_to_cart');
                formData.append('product_id', productId);
                formData.append('quantity', 1);
                
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
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
                        // Show success state
                        addIcon.classList.add('hidden');
                        addText.classList.add('hidden');
                        addedIcon.classList.remove('hidden');
                        addedText.classList.remove('hidden');
                        button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                        button.classList.add('bg-green-600', 'hover:bg-green-700');
                        button.title = 'Added to Cart';
                        
                        // Update cart count if cart widget exists
                        updateCartCount();
                        
                        // Refresh page briefly to ensure all states are updated
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        // Show error state
                        console.error('Add to cart error:', data.error);
                        button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                        button.classList.add('bg-red-600');
                        button.title = data.error || 'Failed to add to cart';
                        
                        setTimeout(() => {
                            button.classList.remove('bg-red-600');
                            button.classList.add('bg-blue-600', 'hover:bg-blue-700');
                            button.classList.remove('adding');
                            button.disabled = false;
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.error('Add to cart error:', error);
                    button.classList.remove('adding');
                    button.disabled = false;
                });
            }
        }
    });
    
    // Update cart count function
    function updateCartCount() {
        console.log('Updating cart count...');
        
        // Method 1: Refresh cart fragments to update cart count
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: new URLSearchParams({
                'action': 'woocommerce_get_cart_fragments'
            }),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Fragment refresh response:', data);
            if (data.fragments) {
                // Update cart fragments but skip .cart-count elements to preserve styling
                Object.keys(data.fragments).forEach(key => {
                    // Skip cart count elements to preserve our styling
                    if (key.includes('.cart-count')) {
                        console.log('Skipping cart count fragment to preserve styling:', key);
                        return;
                    }
                    
                    const elements = document.querySelectorAll(key);
                    elements.forEach(element => {
                        if (element) {
                            element.outerHTML = data.fragments[key];
                        }
                    });
                });
            }
        })
        .catch(error => console.error('Fragment refresh error:', error));
        
        // Method 2: Direct cart count update - ONLY UPDATE TEXT, PRESERVE STYLING
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
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
                    console.log('Updated cart count text to:', cartCount, 'preserving styling');
                });
                
                // Also update button states
                updateButtonStates(data.data.cart);
            }
        })
        .catch(error => console.error('Cart data fetch error:', error));
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
                
                button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                button.classList.add('bg-green-600', 'hover:bg-green-700');
                button.title = 'Added to Cart';
                button.disabled = true;
            }
        });
    }
    
    function filterProducts() {
        productCards.forEach(card => {
            if (!selectedCategory || card.dataset.categories.includes(selectedCategory)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
        currentPage = 1;
        updatePagination();
    }
    
    function updatePagination() {
        const visibleProducts = Array.from(productCards).filter(card => card.style.display !== 'none');
        const totalPages = Math.ceil(visibleProducts.length / productsPerPage);
        
        // Update page numbers
        pageNumbers.innerHTML = '';
        for (let i = 1; i <= Math.min(totalPages, 3); i++) {
            const pageBtn = document.createElement('button');
            pageBtn.className = `page-number px-3 py-2 rounded-lg ${i === currentPage ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700'}`;
            pageBtn.textContent = i;
            pageBtn.dataset.page = i;
            pageBtn.addEventListener('click', () => goToPage(i));
            pageNumbers.appendChild(pageBtn);
        }
        
        // Update prev/next buttons
        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === totalPages;
        
        // Show/hide products based on current page
        const startIndex = (currentPage - 1) * productsPerPage;
        const endIndex = startIndex + productsPerPage;
        
        visibleProducts.forEach((card, index) => {
            if (index >= startIndex && index < endIndex) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    function goToPage(page) {
        currentPage = page;
        updatePagination();
    }
    
    prevBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            goToPage(currentPage - 1);
        }
    });
    
    nextBtn.addEventListener('click', () => {
        const visibleProducts = Array.from(productCards).filter(card => card.style.display !== 'none');
        const totalPages = Math.ceil(visibleProducts.length / productsPerPage);
        if (currentPage < totalPages) {
            goToPage(currentPage + 1);
        }
    });
    
    // Initialize pagination
    updatePagination();
    
    // Check cart contents on page load and update button states
    checkCartOnLoad();
    
    function checkCartOnLoad() {
        console.log('Checking cart on page load...');
        
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
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
            console.log('Cart check on load response:', data);
            if (data.success && data.data && data.data.cart) {
                // Update button states based on current cart contents
                updateButtonStates(data.data.cart);
                
                // Update cart count display
                const cartCount = data.data.cart.count;
                const cartCountElements = document.querySelectorAll('.cart-count');
                cartCountElements.forEach(element => {
                    element.textContent = cartCount;
                });
            }
        })
        .catch(error => console.error('Cart check on load error:', error));
    }
    
    // Debug: Add test function to console
    window.testAddToCartButton = function(productId) {
        const button = document.querySelector(`[data-product-id="${productId}"]`);
        if (button) {
            console.log('Testing button:', button);
            const addIcon = button.querySelector('.add-icon');
            const addedIcon = button.querySelector('.added-icon');
            const addText = button.querySelector('.add-text');
            const addedText = button.querySelector('.added-text');
            
            // Simulate success state
            addIcon.classList.add('hidden');
            addText.classList.add('hidden');
            addedIcon.classList.remove('hidden');
            addedText.classList.remove('hidden');
            button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            button.classList.add('bg-green-600', 'hover:bg-green-700');
            button.title = 'Added to Cart';
            
            console.log('Button state changed to success');
        } else {
            console.log('Button not found for product ID:', productId);
        }
    };
    
    console.log('Test function available: testAddToCartButton(productId)');
});
</script>

<style>
.categories-scroll-container {
    position: relative;
}

.categories-horizontal-scroll {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
}

.categories-horizontal-scroll::-webkit-scrollbar {
    height: 6px;
}

.categories-horizontal-scroll::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.categories-horizontal-scroll::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.categories-horizontal-scroll::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.dark .categories-horizontal-scroll::-webkit-scrollbar-track {
    background: #374151;
}

.dark .categories-horizontal-scroll::-webkit-scrollbar-thumb {
    background: #6b7280;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Add to cart button states */
.add-to-cart-btn.bg-green-600 {
    background-color: rgb(22 163 74) !important;
}

.add-to-cart-btn.bg-green-600:hover {
    background-color: rgb(21 128 61) !important;
}

.add-to-cart-btn.bg-red-600 {
    background-color: rgb(220 38 38) !important;
}

.add-to-cart-btn.adding {
    opacity: 0.7;
    cursor: not-allowed;
}

@media (max-width: 640px) {
    .products-section {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}
</style>

<?php get_footer(); ?>
