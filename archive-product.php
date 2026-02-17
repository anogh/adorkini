<?php get_header(); ?>

<main class="flex-grow pb-24 lg:pb-0">
    <?php
    // Logic for Search Results Category Filtering
    $filtered_categories = null;
    if ( is_search() ) {
        $search_term = get_search_query();
        $search_args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            's' => $search_term,
            'posts_per_page' => -1,
            'fields' => 'ids'
        );
        $search_ids = get_posts($search_args);
        
        if (!empty($search_ids)) {
            update_object_term_cache($search_ids, 'product');
            $cat_counts = array();
            
            foreach ($search_ids as $p_id) {
                $terms = get_the_terms($p_id, 'product_cat');
                if ($terms && !is_wp_error($terms)) {
                    foreach ($terms as $term) {
                        if (!isset($cat_counts[$term->term_id])) {
                            $cat_counts[$term->term_id] = $term;
                            $cat_counts[$term->term_id]->filtered_count = 0;
                        }
                        $cat_counts[$term->term_id]->filtered_count++;
                    }
                }
            }
            
            $filtered_categories = array_values($cat_counts);
            
            // Sort by name
            usort($filtered_categories, function($a, $b) {
                return strcmp($a->name, $b->name);
            });
        } else {
            $filtered_categories = array();
        }
    }
    ?>
    
    <!-- Desktop Content (Hidden on Mobile) -->
    <div class="hidden lg:block">
        <div class="container mx-auto px-6 py-8">
            <div class="flex flex-col gap-8 lg:flex-row">
                <aside class="w-full lg:w-64 xl:w-72 flex-shrink-0">
                    <div class="sticky top-24 space-y-6">
                        <!-- Categories Sidebar -->
                        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-background-dark">
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Categories</h3>
                            <div class="mt-4 space-y-2">
                                <?php
                                if ( is_search() ) {
                                    $categories_to_show = $filtered_categories;
                                    
                                    // "All Categories" link for search results
                                    $all_link = remove_query_arg( 'product_cat' );
                                    $is_all_active = ! get_query_var( 'product_cat' );
                                    echo '<a class="block text-sm ' . ( $is_all_active ? 'font-bold text-primary' : 'text-gray-600 hover:text-primary dark:text-gray-300 dark:hover:text-primary' ) . '" href="' . esc_url( $all_link ) . '">All Categories</a>';
                                } else {
                                    $categories_to_show = get_terms( ['taxonomy' => 'product_cat', 'hide_empty' => false] );
                                }

                                if ( ! empty( $categories_to_show ) && ! is_wp_error( $categories_to_show ) ) {
                                    foreach ( $categories_to_show as $category ) {
                                        // Skip "Uncategorized" category
                                        if ( $category->slug === 'uncategorized' ) {
                                            continue;
                                        }

                                        if ( is_search() ) {
                                            // Refine search link
                                            $cat_link = add_query_arg( 'product_cat', $category->slug );
                                            $is_active = get_query_var( 'product_cat' ) === $category->slug;
                                        } else {
                                            // Standard category link
                                            $cat_link = get_term_link( $category );
                                            $is_active = is_product_category() && get_queried_object_id() === $category->term_id;
                                        }

                                        $class = $is_active ? 'font-bold text-primary' : 'text-gray-600 hover:text-primary dark:text-gray-300 dark:hover:text-primary';
                                        
                                        $count_html = '';
                                        if ( is_search() && isset( $category->filtered_count ) ) {
                                            $count_html = ' <span class="text-xs text-gray-400">(' . $category->filtered_count . ')</span>';
                                        }
                                        
                                        echo '<a class="block text-sm ' . esc_attr( $class ) . '" href="' . esc_url( $cat_link ) . '">' . esc_html( $category->name ) . $count_html . '</a>';
                                    }
                                } elseif ( is_search() ) {
                                    echo '<p class="text-sm text-gray-500">No categories found.</p>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </aside>
                <div class="w-full">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?php woocommerce_page_title(); ?></h1>
                        
                        <!-- Desktop Sorting -->
                        <div class="flex items-center gap-2">
                            <?php woocommerce_catalog_ordering(); ?>
                        </div>
                    </div>
                    
                    <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3">
                        <?php
                        if ( have_posts() ) {
                            while ( have_posts() ) {
                                the_post();
                                global $product;
                                ?>
                                <div class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-background-dark">
                                    <a href="<?php the_permalink(); ?>" class="block w-full bg-center bg-no-repeat aspect-square bg-cover rounded-lg" style='background-image: url("<?php echo get_the_post_thumbnail_url(get_the_ID(), 'large'); ?>");'></a>
                                    <div class="flex flex-col flex-1 justify-between gap-4">
                                        <div>
                                            <a href="<?php the_permalink(); ?>" class="text-base font-semibold text-gray-900 dark:text-white hover:text-primary transition-colors"><?php the_title(); ?></a>
                                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo $product->get_price_html(); ?></p>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="button" class="add-to-cart-btn flex-1 flex items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary/10 text-primary text-sm font-bold hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30" data-product-id="<?php echo $product->get_id(); ?>">
                                                <span class="truncate">Add to Cart</span>
                                            </button>
                                            <button type="button" class="warafy-wishlist-btn flex-none w-10 h-10 flex items-center justify-center rounded-lg bg-primary/10 text-primary hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 transition-colors" data-product-id="<?php echo $product->get_id(); ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<p>No products found</p>';
                        }
                        ?>
                    </div>
                    <!-- Pagination -->
                    <div class="mt-8 flex justify-center">
                        <?php
                        the_posts_pagination( array(
                            'prev_text' => '<span class="material-symbols-outlined text-base" data-icon="chevron_left"></span>',
                            'next_text' => '<span class="material-symbols-outlined text-base" data-icon="chevron_right"></span>',
                        ) );
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Content (Hidden on Desktop) -->
    <div class="lg:hidden">
        <!-- Filter & Sort Bar -->
        <div class="sticky top-[61px] z-10 flex gap-3 overflow-x-auto border-b border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-background-dark/80 p-4 backdrop-blur-sm">
            <button id="mobile-filter-btn" class="flex h-10 flex-1 items-center justify-center gap-x-2 rounded-lg bg-slate-100 dark:bg-slate-800 px-4">
                <span class="material-symbols-outlined text-lg text-slate-800 dark:text-slate-200" data-icon="tune"></span>
                <p class="text-sm font-medium text-slate-800 dark:text-slate-200">Filters</p>
            </button>
            <button id="mobile-sort-btn" class="flex h-10 flex-1 items-center justify-center gap-x-2 rounded-lg bg-slate-100 dark:bg-slate-800 px-4">
                <span class="material-symbols-outlined text-lg text-slate-800 dark:text-slate-200" data-icon="swap_vert"></span>
                <p class="text-sm font-medium text-slate-800 dark:text-slate-200">Sort</p>
            </button>
        </div>
        
        <!-- Mobile Filter Drawer -->
        <div id="mobile-filter-drawer" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="mobile-filter-overlay"></div>
            <div class="absolute right-0 top-0 h-full w-[85%] max-w-sm bg-white dark:bg-background-dark shadow-xl transform transition-transform duration-300 translate-x-full" id="mobile-filter-content">
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 p-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Filters</h3>
                    <button id="close-filter-drawer" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <span class="material-symbols-outlined" data-icon="close"></span>
                    </button>
                </div>
                <div class="p-4 overflow-y-auto h-[calc(100%-60px)]">
                    <!-- Custom Mobile Filters -->
                    <div class="space-y-8">
                        <!-- Categories Filter -->
                        <div>
                            <h4 class="text-base font-bold text-gray-900 dark:text-white mb-3">Categories</h4>
                            <div class="space-y-1">
                                <?php
                                $current_cat_id = is_product_category() ? get_queried_object_id() : 0;
                                $shop_page_url = get_permalink( wc_get_page_id( 'shop' ) );
                                
                                // All Products Link
                                $all_products_link = is_search() ? remove_query_arg( 'product_cat' ) : $shop_page_url;
                                $is_all_products_active = is_search() ? ! get_query_var( 'product_cat' ) : ! is_product_category();

                                echo '<a href="' . esc_url( $all_products_link ) . '" class="flex items-center justify-between p-2 rounded-lg ' . ( $is_all_products_active ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-800' ) . '">';
                                echo '<span>All Products</span>';
                                echo '</a>';

                                if ( is_search() ) {
                                    $categories_to_show = $filtered_categories;
                                } else {
                                    $categories_to_show = get_terms( ['taxonomy' => 'product_cat', 'hide_empty' => true] );
                                }

                                if ( ! empty( $categories_to_show ) && ! is_wp_error( $categories_to_show ) ) {
                                    foreach ( $categories_to_show as $category ) {
                                        // Skip "Uncategorized" category
                                        if ( $category->slug === 'uncategorized' ) {
                                            continue;
                                        }

                                        if ( is_search() ) {
                                            $cat_link = add_query_arg( 'product_cat', $category->slug );
                                            $is_active = get_query_var( 'product_cat' ) === $category->slug;
                                        } else {
                                            $cat_link = get_term_link( $category );
                                            $is_active = $current_cat_id === $category->term_id;
                                        }
                                        
                                        echo '<a href="' . esc_url( $cat_link ) . '" class="flex items-center justify-between p-2 rounded-lg ' . ($is_active ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-800') . '">';
                                        echo '<span>' . esc_html( $category->name ) . '</span>';
                                        $count = isset( $category->filtered_count ) ? $category->filtered_count : $category->count;
                                        echo '<span class="text-xs bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded-full">' . $count . '</span>';
                                        echo '</a>';
                                    }
                                } elseif ( is_search() ) {
                                    echo '<p class="text-sm text-gray-500 p-2">No categories found.</p>';
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Price Range Filter -->
                        <div>
                            <h4 class="text-base font-bold text-gray-900 dark:text-white mb-3">Price Range</h4>
                            <?php
                            $filter_action = $shop_page_url;
                            if ( is_product_category() ) {
                                $filter_action = get_term_link( $current_cat_id, 'product_cat' );
                            } elseif ( is_search() ) {
                                $filter_action = home_url( '/' );
                            }
                            ?>
                            <form method="get" action="<?php echo esc_url( $filter_action ); ?>">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="relative w-1/2">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                        <input type="number" name="min_price" placeholder="Min" value="<?php echo isset($_GET['min_price']) ? esc_attr($_GET['min_price']) : ''; ?>" class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg dark:bg-slate-800 dark:border-slate-700 dark:text-white focus:border-primary focus:ring-1 focus:ring-primary" min="0" step="any">
                                    </div>
                                    <span class="text-gray-400 font-medium">-</span>
                                    <div class="relative w-1/2">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                        <input type="number" name="max_price" placeholder="Max" value="<?php echo isset($_GET['max_price']) ? esc_attr($_GET['max_price']) : ''; ?>" class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg dark:bg-slate-800 dark:border-slate-700 dark:text-white focus:border-primary focus:ring-1 focus:ring-primary" min="0" step="any">
                                    </div>
                                </div>
                                
                                <!-- Preserve other query parameters -->
                                <?php 
                                foreach ($_GET as $key => $val) {
                                    if (!in_array($key, ['min_price', 'max_price', 'submit'])) {
                                        if (is_array($val)) {
                                            foreach ($val as $v) {
                                                echo '<input type="hidden" name="' . esc_attr($key) . '[]" value="' . esc_attr($v) . '">';
                                            }
                                        } else {
                                            echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($val) . '">';
                                        }
                                    }
                                }
                                ?>
                                
                                <button type="submit" class="w-full bg-primary text-white py-2.5 rounded-lg text-sm font-bold hover:bg-primary/90 transition-colors shadow-sm">
                                    Apply Filter
                                </button>
                            </form>
                        </div>
                    </div>


                </div>
            </div>
        </div>

        <!-- Mobile Sort Sheet -->
        <div id="mobile-sort-sheet" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="mobile-sort-overlay"></div>
            <div class="absolute bottom-0 left-0 right-0 bg-white dark:bg-background-dark rounded-t-2xl shadow-xl transform transition-transform duration-300 translate-y-full" id="mobile-sort-content">
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 p-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Sort By</h3>
                    <button id="close-sort-sheet" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <span class="material-symbols-outlined" data-icon="close"></span>
                    </button>
                </div>
                <div class="p-4">
                    <!-- We'll clone the desktop sort options here via JS or output them directly -->
                    <div class="mobile-sort-options space-y-2">
                        <?php 
                        $catalog_orderby_options = apply_filters( 'woocommerce_catalog_orderby', array(
                            'menu_order' => __( 'Default sorting', 'woocommerce' ),
                            'popularity' => __( 'Sort by popularity', 'woocommerce' ),
                            'rating'     => __( 'Sort by average rating', 'woocommerce' ),
                            'date'       => __( 'Sort by latest', 'woocommerce' ),
                            'price'      => __( 'Sort by price: low to high', 'woocommerce' ),
                            'price-desc' => __( 'Sort by price: high to low', 'woocommerce' ),
                        ) );
                        
                        $orderby = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
                        
                        foreach ( $catalog_orderby_options as $id => $name ) : ?>
                            <a href="<?php echo esc_url( add_query_arg( 'orderby', $id ) ); ?>" class="flex items-center justify-between p-3 rounded-lg <?php echo $orderby == $id ? 'bg-primary/10 text-primary font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'; ?>">
                                <span><?php echo esc_html( $name ); ?></span>
                                <?php if ( $orderby == $id ) : ?>
                                    <span class="material-symbols-outlined text-primary" data-icon="check"></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-2 gap-4 p-4 mobile-grid-2">
            <?php
            if ( have_posts() ) {
                while ( have_posts() ) {
                    the_post();
                    global $product;
                    ?>
                    <div class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-background-dark shadow-sm">
                        <div class="relative">
                            <a href="<?php the_permalink(); ?>" class="block w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-lg" style='background-image: url("<?php echo get_the_post_thumbnail_url(get_the_ID(), 'woocommerce_thumbnail'); ?>");'></a>
                        </div>
                        <div class="flex flex-col flex-1 justify-between gap-4">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">
                                    <a href="<?php the_permalink(); ?>" class="hover:text-primary transition-colors line-clamp-1"><?php the_title(); ?></a>
                                </h3>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo $product->get_price_html(); ?></p>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" class="add-to-cart-btn flex-1 flex items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary/10 text-primary text-sm font-bold hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 transition-colors" data-product-id="<?php echo $product->get_id(); ?>">
                                    <span class="material-symbols-outlined text-sm add-icon mr-2" data-icon="add_shopping_cart"></span>
                                    <span class="add-text truncate">Add</span>
                                    <span class="material-symbols-outlined text-sm added-icon hidden mr-2" data-icon="check"></span>
                                    <span class="added-text hidden truncate">Added</span>
                                </button>
                                <button type="button" class="warafy-wishlist-btn flex-none w-10 h-10 flex items-center justify-center rounded-lg bg-primary/10 text-primary hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 transition-colors" data-product-id="<?php echo $product->get_id(); ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile Filter Drawer Logic
    const filterBtn = document.getElementById('mobile-filter-btn');
    const filterDrawer = document.getElementById('mobile-filter-drawer');
    const filterContent = document.getElementById('mobile-filter-content');
    const filterOverlay = document.getElementById('mobile-filter-overlay');
    const closeFilterBtn = document.getElementById('close-filter-drawer');

    function openFilters() {
        filterDrawer.classList.remove('hidden');
        setTimeout(() => {
            filterContent.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeFilters() {
        filterContent.classList.add('translate-x-full');
        setTimeout(() => {
            filterDrawer.classList.add('hidden');
        }, 300);
        document.body.style.overflow = '';
    }

    if (filterBtn) {
        filterBtn.addEventListener('click', openFilters);
        closeFilterBtn.addEventListener('click', closeFilters);
        filterOverlay.addEventListener('click', closeFilters);
    }

    // Mobile Sort Sheet Logic
    const sortBtn = document.getElementById('mobile-sort-btn');
    const sortSheet = document.getElementById('mobile-sort-sheet');
    const sortContent = document.getElementById('mobile-sort-content');
    const sortOverlay = document.getElementById('mobile-sort-overlay');
    const closeSortBtn = document.getElementById('close-sort-sheet');

    function openSort() {
        sortSheet.classList.remove('hidden');
        setTimeout(() => {
            sortContent.classList.remove('translate-y-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeSort() {
        sortContent.classList.add('translate-y-full');
        setTimeout(() => {
            sortSheet.classList.add('hidden');
        }, 300);
        document.body.style.overflow = '';
    }

    if (sortBtn) {
        sortBtn.addEventListener('click', openSort);
        closeSortBtn.addEventListener('click', closeSort);
        sortOverlay.addEventListener('click', closeSort);
    }

    // Add to Cart functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.add-to-cart-btn')) {
            e.preventDefault();
            e.stopPropagation();
            
            const button = e.target.closest('.add-to-cart-btn');
            const productId = button.dataset.productId;
            
            // Make button jQuery-compatible for WooCommerce
            if (typeof jQuery !== 'undefined') {
                jQuery(button).data('product-id', productId);
                if (!button.classList.contains('add_to_cart_button')) {
                    button.classList.add('add_to_cart_button');
                }
                if (!button.classList.contains('ajax_add_to_cart')) {
                    button.classList.add('ajax_add_to_cart');
                }
            }
            
            // Detect button style (Mobile New vs Desktop Old)
            const addIcon = button.querySelector('.add-icon');
            const addedIcon = button.querySelector('.added-icon');
            const addText = button.querySelector('.add-text');
            const addedText = button.querySelector('.added-text');
            const isNewStyle = !!(addIcon || addText);
            
            // Fallback for desktop style elements
            const originalIcon = button.querySelector('.material-symbols-outlined') ? button.querySelector('.material-symbols-outlined').textContent : '';
            const genericTextSpan = button.querySelector('span:not(.material-symbols-outlined)');
            
            // Prevent multiple clicks
            if (button.classList.contains('adding')) return;
            
            button.classList.add('adding');
            button.disabled = true;
            
            // Show loading state
            if (isNewStyle) {
                if (addIcon) {
                    addIcon.textContent = 'refresh';
                    addIcon.style.animation = 'spin 1s linear infinite';
                }
                if (addText) addText.textContent = 'Adding...';
            } else {
                if (genericTextSpan) {
                    genericTextSpan.textContent = 'Adding...';
                } else if (button.querySelector('.material-symbols-outlined')) {
                    button.querySelector('.material-symbols-outlined').textContent = 'refresh';
                    button.querySelector('.material-symbols-outlined').style.animation = 'spin 1s linear infinite';
                }
            }
            
            // Check if WooCommerce functions are available
            if (typeof wc_add_to_cart_params !== 'undefined') {
                // Use WooCommerce's built-in AJAX
                const data = {
                    action: 'woocommerce_add_to_cart',
                    product_id: productId,
                    quantity: 1
                };
                
                jQuery.post(wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'), data, function(response) {
                    handleAddToCartResponse(response, button, isNewStyle, addIcon, addedIcon, addText, addedText, originalIcon, genericTextSpan);
                });
            } else {
                // Fallback to custom AJAX
                const formData = new FormData();
                formData.append('action', 'woocommerce_add_to_cart');
                formData.append('product_id', productId);
                formData.append('quantity', 1);
                
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.json())
                .then(data => {
                    handleAddToCartResponse(data, button, isNewStyle, addIcon, addedIcon, addText, addedText, originalIcon, genericTextSpan);
                })
                .catch(error => {
                    handleError(error, button, isNewStyle, addIcon, addedIcon, addText, addedText, originalIcon, genericTextSpan);
                });
            }
        }
    });

    function handleAddToCartResponse(response, button, isNewStyle, addIcon, addedIcon, addText, addedText, originalIcon, genericTextSpan) {
        if (response.error && response.product_url) {
            window.location = response.product_url;
            return;
        }
        
        if (!response.error) {
            // Show success state
            if (isNewStyle) {
                if (addIcon) {
                    addIcon.textContent = 'check';
                    addIcon.style.animation = '';
                    addIcon.classList.add('hidden');
                }
                if (addText) {
                    addText.textContent = 'Added to cart';
                    addText.classList.add('hidden');
                }
                if (addedIcon) addedIcon.classList.remove('hidden');
                if (addedText) addedText.classList.remove('hidden');
                
                button.style.backgroundColor = '#16a34a';
                button.style.color = 'white';
                button.title = 'Added to Cart';
            } else {
                if (genericTextSpan) {
                    genericTextSpan.textContent = 'Added!';
                } else if (button.querySelector('.material-symbols-outlined')) {
                    button.querySelector('.material-symbols-outlined').textContent = 'check';
                    button.querySelector('.material-symbols-outlined').style.animation = '';
                }
                button.classList.remove('bg-primary/10', 'hover:bg-primary/20', 'dark:bg-primary/20', 'dark:hover:bg-primary/30');
                button.classList.add('bg-green-600', 'hover:bg-green-700', 'text-white');
            }
            
            // Update cart fragments
            if (typeof jQuery !== 'undefined' && jQuery('body').trigger) {
                jQuery('body').trigger('added_to_cart', [response.fragments, response.cart_hash]);
            }
            
            // IMMEDIATE cart count update
            const cartCountElements = document.querySelectorAll('.cart-count');
            if (cartCountElements.length > 0) {
                const currentCount = parseInt(cartCountElements[0].textContent || '0');
                const newCount = currentCount + 1;
                
                cartCountElements.forEach(element => {
                    element.textContent = newCount;
                    // Force visual refresh
                    element.style.display = 'none';
                    element.offsetHeight; // Force reflow
                    element.style.display = 'flex';
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
            
            updateCartCount();
            
            // Refresh page on mobile to show correct cart count
            if (window.innerWidth <= 1024) {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }

            // Revert state after delay (only for old style)
            if (!isNewStyle) {
                setTimeout(() => {
                    if (genericTextSpan) {
                        genericTextSpan.textContent = 'Add to Cart';
                    } else if (button.querySelector('.material-symbols-outlined')) {
                        button.querySelector('.material-symbols-outlined').textContent = originalIcon;
                    }
                    button.classList.remove('bg-green-600', 'hover:bg-green-700', 'text-white');
                    button.classList.add('bg-primary/10', 'hover:bg-primary/20', 'dark:bg-primary/20', 'dark:hover:bg-primary/30');
                    button.classList.remove('adding');
                    button.disabled = false;
                }, 2000);
            } else {
                button.classList.remove('adding');
            }
        } else {
            handleError(response.error, button, isNewStyle, addIcon, addedIcon, addText, addedText, originalIcon, genericTextSpan);
        }
    }

    function handleError(errorMsg, button, isNewStyle, addIcon, addedIcon, addText, addedText, originalIcon, genericTextSpan) {
        console.error('Add to cart error:', errorMsg);
        if (isNewStyle) {
            if (addIcon) { addIcon.textContent = 'close'; addIcon.style.animation = ''; }
            if (addText) addText.textContent = 'Error';
            button.style.backgroundColor = '#dc2626';
            button.style.color = 'white';
            
            setTimeout(() => {
                if (addIcon) { addIcon.textContent = 'add_shopping_cart'; addIcon.classList.remove('hidden'); }
                if (addText) { addText.textContent = 'Add to Cart'; addText.classList.remove('hidden'); }
                if (addedIcon) addedIcon.classList.add('hidden');
                if (addedText) addedText.classList.add('hidden');
                button.style.backgroundColor = '';
                button.style.color = '';
                button.classList.remove('adding');
                button.disabled = false;
            }, 2000);
        } else {
            if (genericTextSpan) genericTextSpan.textContent = 'Error';
            else if (button.querySelector('.material-symbols-outlined')) {
                button.querySelector('.material-symbols-outlined').textContent = 'close';
                button.querySelector('.material-symbols-outlined').style.animation = '';
            }
            button.classList.remove('bg-primary/10', 'hover:bg-primary/20', 'dark:bg-primary/20', 'dark:hover:bg-primary/30');
            button.classList.add('bg-red-600', 'text-white');
            
            setTimeout(() => {
                if (genericTextSpan) genericTextSpan.textContent = 'Add to Cart';
                else if (button.querySelector('.material-symbols-outlined')) button.querySelector('.material-symbols-outlined').textContent = originalIcon;
                button.classList.remove('bg-red-600', 'text-white');
                button.classList.add('bg-primary/10', 'hover:bg-primary/20', 'dark:bg-primary/20', 'dark:hover:bg-primary/30');
                button.classList.remove('adding');
                button.disabled = false;
            }, 2000);
        }
    }
    
    // Update cart count function
    function updateCartCount() {
        // Method 1: Trigger WooCommerce fragment refresh
        if (typeof jQuery !== 'undefined' && jQuery('body').trigger) {
            jQuery('body').trigger('wc_fragment_refresh');
        }
        
        // Method 2: Use our custom fragment refresh
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: new URLSearchParams({ 'action': 'warafy_refresh_fragments' }),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.fragments) {
                Object.keys(data.data.fragments).forEach(key => {
                    // Skip cart count elements to preserve styling
                    if (key.includes('.cart-count')) return;
                    
                    const elements = document.querySelectorAll(key);
                    elements.forEach(element => {
                        if (element) element.outerHTML = data.data.fragments[key];
                    });
                });
            }
        });
        
        // Method 3: Direct cart count update - ONLY UPDATE TEXT, PRESERVE STYLING
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: new URLSearchParams({ 'action': 'warafy_get_cart' }),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.cart) {
                const cartCount = data.data.cart.count;
                const cartCountElements = document.querySelectorAll('.cart-count');
                cartCountElements.forEach(element => {
                    element.textContent = cartCount;
                });
                updateButtonStates(data.data.cart);
            }
        });
    }
    
    // Update button states based on cart contents
    function updateButtonStates(cartData) {
        const cartItems = cartData.items || [];
        const cartProductIds = cartItems.map(item => item.product_id.toString());
        
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            const productId = button.dataset.productId;
            const addIcon = button.querySelector('.add-icon');
            const addedIcon = button.querySelector('.added-icon');
            const addText = button.querySelector('.add-text');
            const addedText = button.querySelector('.added-text');
            const isNewStyle = !!(addIcon || addText);
            
            if (cartProductIds.includes(productId)) {
                if (isNewStyle) {
                    if (addIcon) addIcon.classList.add('hidden');
                    if (addText) addText.classList.add('hidden');
                    if (addedIcon) addedIcon.classList.remove('hidden');
                    if (addedText) addedText.classList.remove('hidden');
                    button.style.backgroundColor = '#16a34a';
                    button.style.color = 'white';
                    button.title = 'Added to Cart';
                } else {
                    const icon = button.querySelector('.material-symbols-outlined');
                    if (icon) icon.textContent = 'check';
                    button.style.backgroundColor = '#16a34a';
                    button.style.color = 'white';
                    button.disabled = true;
                }
                button.disabled = true;
            }
        });
    }
    
    // Check cart contents on page load
    function checkCartOnLoad() {
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: new URLSearchParams({ 'action': 'warafy_get_cart' }),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.cart) {
                const cartCount = data.data.cart.count;
                document.querySelectorAll('.cart-count').forEach(element => {
                    element.textContent = cartCount;
                });
                updateButtonStates(data.data.cart);
            }
        });
    }
    
    checkCartOnLoad();
});
</script>
<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

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
</style>

<?php get_footer(); ?>