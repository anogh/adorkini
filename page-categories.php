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
                        <?php
                        // Attributes for filtering
                        $attributes = [
                            'data-categories' => implode(',', $category_slugs),
                            'class' => 'product-card' // Ensure product-card class is added for JS selection
                        ];
                        // Add product-card class to the wrapper div inside the function?
                        // No, the function creates the wrapper div. I need to make sure the function adds 'product-card' class if I pass it?
                        // My modified function adds attributes to the wrapper div. So I can pass class.
                        // But wait, the function already has classes. 'class' attribute will overwrite or append?
                        // It will overwrite if I'm not careful. My function does: $attr_string .= ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
                        // And then <div class="... warafy-mobile-product-card"<?php echo $attr_string; ?>>
                        // If I pass 'class' => 'product-card', it will result in class="..." class="product-card". HTML parsers usually take the first one or merge? No, duplicate attributes are invalid/unpredictable.
                        // I should modify the function to merge classes.
                        // Or I can change the JS selector in page-categories.php to use .warafy-mobile-product-card instead of .product-card.
                        // Let's check page-categories.php JS: const productCards = document.querySelectorAll('.product-card');
                        // I'll change the JS selector.
                        warafy_render_mobile_compact_product($product, ['data-categories' => implode(',', $category_slugs)]);
                        ?>
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
                            <?php warafy_render_desktop_compact_product($product, ['data-categories' => implode(',', $category_slugs)]); ?>
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
    // Updated selector to match global product card classes (both mobile and desktop)
    const productCards = document.querySelectorAll('.warafy-mobile-product-card, .warafy-desktop-product-card');
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
