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
            update_object_term_cache($search_ids, 'product_cat');
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

    // Get categories for the ribbon
    if ( is_search() ) {
        $ribbon_categories = $filtered_categories;
    } else {
        $ribbon_categories = get_terms( ['taxonomy' => 'product_cat', 'hide_empty' => false] );
    }

    // Category icon mapping
    function get_category_icon($slug) {
        $icons = [
            'bags-and-travel' => 'shopping_bag',
            'bedding-bath' => 'bed',
            'diy-outdoor' => 'hardware',
            'fashion' => 'apparel',
            'furniture-decor' => 'chair',
            'health-beauty' => 'spa',
            'kitchen-dining' => 'restaurant',
            'electronics' => 'devices',
            'home-garden' => 'home',
            'sports-outdoors' => 'sports',
            'toys-games' => 'toys',
            'laundry-cleaning' => 'cleaning',
            'mother-baby' => 'baby',
            'tools' => 'tools',
            'default' => 'category'
        ];
        return isset($icons[$slug]) ? $icons[$slug] : $icons['default'];
    }
    ?>
    
    <!-- Desktop Content -->
    <div class="hidden lg:block">
        <!-- Horizontal Category Ribbon -->
        <div class="border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-background-dark">
            <div class="relative">
                <!-- Left Arrow -->
                <button id="cat-scroll-left" class="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center bg-gradient-to-r from-white dark:from-background-dark to-transparent hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <span class="material-symbols-outlined text-gray-600 dark:text-gray-300" data-icon="chevron_left"></span>
                </button>
                
                <!-- Scrollable Container -->
                <div id="cat-ribbon-desktop" class="flex gap-6 overflow-x-auto px-12 py-4 scrollbar-hide scroll-smooth" style="scrollbar-width: none; -ms-overflow-style: none;">
                    <?php
                    // All Products link
                    $shop_page_url = get_permalink( wc_get_page_id( 'shop' ) );
                    $all_link = is_search() ? remove_query_arg( 'product_cat' ) : $shop_page_url;
                    $is_all_active = is_search() ? ! get_query_var( 'product_cat' ) : ! is_product_category();
                    
                    echo '<a href="' . esc_url( $all_link ) . '" class="flex flex-col items-center gap-2 min-w-[80px] group ' . ( $is_all_active ? 'text-primary' : 'text-gray-600 dark:text-gray-300 hover:text-primary' ) . '">';
                    echo '<div class="w-14 h-14 rounded-xl flex items-center justify-center ' . ( $is_all_active ? 'bg-primary/20' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-primary/10' ) . ' transition-colors">';
                    echo '<span class="material-symbols-outlined text-2xl" data-icon="apps"></span>';
                    echo '</div>';
                    echo '<span class="text-xs font-medium text-center whitespace-nowrap">' . __t('All') . '</span>';
                    echo '</a>';

                    if ( ! empty( $ribbon_categories ) && ! is_wp_error( $ribbon_categories ) ) {
                        foreach ( $ribbon_categories as $category ) {
                            // Skip "Uncategorized" category
                            if ( $category->slug === 'uncategorized' ) {
                                continue;
                            }

                            if ( is_search() ) {
                                $cat_link = add_query_arg( 'product_cat', $category->slug );
                                $is_active = get_query_var( 'product_cat' ) === $category->slug;
                            } else {
                                $cat_link = get_term_link( $category );
                                $is_active = is_product_category() && get_queried_object_id() === $category->term_id;
                            }

                            $icon = get_category_icon($category->slug);
                            
                            echo '<a href="' . esc_url( $cat_link ) . '" class="flex flex-col items-center gap-2 min-w-[80px] group ' . ( $is_active ? 'text-primary' : 'text-gray-600 dark:text-gray-300 hover:text-primary' ) . '">';
                            echo '<div class="w-14 h-14 rounded-xl flex items-center justify-center ' . ( $is_active ? 'bg-primary/20' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-primary/10' ) . ' transition-colors">';
                            echo '<span class="material-symbols-outlined text-2xl" data-icon="' . $icon . '"></span>';
                            echo '</div>';
                            echo '<span class="text-xs font-medium text-center whitespace-nowrap">' . esc_html( $category->name ) . '</span>';
                            echo '</a>';
                        }
                    }
                    ?>
                </div>
                
                <!-- Right Arrow -->
                <button id="cat-scroll-right" class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center bg-gradient-to-l from-white dark:from-background-dark to-transparent hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <span class="material-symbols-outlined text-gray-600 dark:text-gray-300" data-icon="chevron_right"></span>
                </button>
            </div>
        </div>

        <div class="container mx-auto px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?php echo __t( woocommerce_page_title(false) ); ?></h1>

                <!-- Desktop Sorting -->
                <div class="flex items-center gap-2">
                    <?php warafy_woocommerce_catalog_ordering(); ?>
                </div>
            </div>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <?php
                if ( have_posts() ) {
                    while ( have_posts() ) {
                        the_post();
                        global $product;
                        warafy_render_desktop_compact_product($product);
                    }
                } else {
                    echo '<p>' . __t('No products found') . '</p>';
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

    <!-- Mobile Content -->
    <div class="lg:hidden">
        <!-- Horizontal Category Ribbon (Mobile) -->
        <div class="border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-background-dark sticky top-[61px] z-10">
            <div id="cat-ribbon-mobile" class="flex gap-4 overflow-x-auto px-4 py-3 scrollbar-hide scroll-smooth" style="scrollbar-width: none; -ms-overflow-style: none; -webkit-overflow-scrolling: touch;">
                <?php
                // All Products link
                echo '<a href="' . esc_url( $all_link ) . '" class="flex flex-col items-center gap-1.5 min-w-[64px] flex-shrink-0 ' . ( $is_all_active ? 'text-primary' : 'text-gray-600 dark:text-gray-300' ) . '">';
                echo '<div class="w-12 h-12 rounded-xl flex items-center justify-center ' . ( $is_all_active ? 'bg-primary/20' : 'bg-gray-100 dark:bg-gray-800' ) . '">';
                echo '<span class="material-symbols-outlined text-xl" data-icon="apps"></span>';
                echo '</div>';
                echo '<span class="text-[10px] font-medium text-center whitespace-nowrap">' . __t('All') . '</span>';
                echo '</a>';

                if ( ! empty( $ribbon_categories ) && ! is_wp_error( $ribbon_categories ) ) {
                    foreach ( $ribbon_categories as $category ) {
                        // Skip "Uncategorized" category
                        if ( $category->slug === 'uncategorized' ) {
                            continue;
                        }

                        if ( is_search() ) {
                            $cat_link = add_query_arg( 'product_cat', $category->slug );
                            $is_active = get_query_var( 'product_cat' ) === $category->slug;
                        } else {
                            $cat_link = get_term_link( $category );
                            $is_active = is_product_category() && get_queried_object_id() === $category->term_id;
                        }

                        $icon = get_category_icon($category->slug);
                        
                        echo '<a href="' . esc_url( $cat_link ) . '" class="flex flex-col items-center gap-1.5 min-w-[64px] flex-shrink-0 ' . ( $is_active ? 'text-primary' : 'text-gray-600 dark:text-gray-300' ) . '">';
                        echo '<div class="w-12 h-12 rounded-xl flex items-center justify-center ' . ( $is_active ? 'bg-primary/20' : 'bg-gray-100 dark:bg-gray-800' ) . '">';
                        echo '<span class="material-symbols-outlined text-xl" data-icon="' . $icon . '"></span>';
                        echo '</div>';
                        echo '<span class="text-[10px] font-medium text-center whitespace-nowrap">' . esc_html( $category->name ) . '</span>';
                        echo '</a>';
                    }
                }
                ?>
            </div>
        </div>

        <!-- Filter & Sort Bar -->
        <div class="flex gap-3 overflow-x-auto border-b border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-background-dark/80 p-4 backdrop-blur-sm">
            <button id="mobile-filter-btn" class="flex h-10 flex-1 items-center justify-center gap-x-2 rounded-lg bg-slate-100 dark:bg-slate-800 px-4">
                <span class="material-symbols-outlined text-lg text-slate-800 dark:text-slate-200" data-icon="tune"></span>
                <p class="text-sm font-medium text-slate-800 dark:text-slate-200"><?php echo __t('Filters'); ?></p>
            </button>
            <button id="mobile-sort-btn" class="flex h-10 flex-1 items-center justify-center gap-x-2 rounded-lg bg-slate-100 dark:bg-slate-800 px-4">
                <span class="material-symbols-outlined text-lg text-slate-800 dark:text-slate-200" data-icon="swap_vert"></span>
                <p class="text-sm font-medium text-slate-800 dark:text-slate-200"><?php echo __t('Sort'); ?></p>
            </button>
        </div>
        
        <!-- Mobile Filter Drawer -->
        <div id="mobile-filter-drawer" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="mobile-filter-overlay"></div>
            <div class="absolute right-0 top-0 h-full w-[85%] max-w-sm bg-white dark:bg-background-dark shadow-xl transform transition-transform duration-300 translate-x-full" id="mobile-filter-content">
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 p-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white"><?php echo __t('Filters'); ?></h3>
                    <button id="close-filter-drawer" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <span class="material-symbols-outlined" data-icon="close"></span>
                    </button>
                </div>
                <div class="p-4 overflow-y-auto h-[calc(100%-60px)]">
                    <!-- Custom Mobile Filters -->
                    <div class="space-y-8">
                        <!-- Price Range Filter -->
                        <div>
                            <h4 class="text-base font-bold text-gray-900 dark:text-white mb-3"><?php echo __t('Price Range'); ?></h4>
                            <?php
                            $filter_action = $shop_page_url;
                            if ( is_product_category() ) {
                                $filter_action = get_term_link( get_queried_object_id(), 'product_cat' );
                            } elseif ( is_search() ) {
                                $filter_action = home_url( '/' );
                            }
                            ?>
                            <form method="get" action="<?php echo esc_url( $filter_action ); ?>">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="relative w-1/2">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                        <input type="number" name="min_price" placeholder="<?php echo __t('Min'); ?>" value="<?php echo isset($_GET['min_price']) ? esc_attr($_GET['min_price']) : ''; ?>" class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg dark:bg-slate-800 dark:border-slate-700 dark:text-white focus:border-primary focus:ring-1 focus:ring-primary" min="0" step="any">
                                    </div>
                                    <span class="text-gray-400 font-medium">-</span>
                                    <div class="relative w-1/2">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                        <input type="number" name="max_price" placeholder="<?php echo __t('Max'); ?>" value="<?php echo isset($_GET['max_price']) ? esc_attr($_GET['max_price']) : ''; ?>" class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg dark:bg-slate-800 dark:border-slate-700 dark:text-white focus:border-primary focus:ring-1 focus:ring-primary" min="0" step="any">
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
                                    <?php echo __t('Apply Filter'); ?>
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
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white"><?php echo __t('Sort By'); ?></h3>
                    <button id="close-sort-sheet" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <span class="material-symbols-outlined" data-icon="close"></span>
                    </button>
                </div>
                <div class="p-4">
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
                    warafy_render_mobile_compact_product($product);
                }
            }
            ?>
        </div>
    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Desktop Category Ribbon Scroll
    const catRibbonDesktop = document.getElementById('cat-ribbon-desktop');
    const scrollLeftBtn = document.getElementById('cat-scroll-left');
    const scrollRightBtn = document.getElementById('cat-scroll-right');
    
    if (catRibbonDesktop && scrollLeftBtn && scrollRightBtn) {
        scrollLeftBtn.addEventListener('click', () => {
            catRibbonDesktop.scrollBy({ left: -200, behavior: 'smooth' });
        });
        
        scrollRightBtn.addEventListener('click', () => {
            catRibbonDesktop.scrollBy({ left: 200, behavior: 'smooth' });
        });
    }

    // Mobile Category Ribbon - Touch swipe support
    const catRibbonMobile = document.getElementById('cat-ribbon-mobile');
    if (catRibbonMobile) {
        let isDown = false;
        let startX;
        let scrollLeft;

        catRibbonMobile.addEventListener('mousedown', (e) => {
            isDown = true;
            startX = e.pageX - catRibbonMobile.offsetLeft;
            scrollLeft = catRibbonMobile.scrollLeft;
        });

        catRibbonMobile.addEventListener('mouseleave', () => {
            isDown = false;
        });

        catRibbonMobile.addEventListener('mouseup', () => {
            isDown = false;
        });

        catRibbonMobile.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - catRibbonMobile.offsetLeft;
            const walk = (x - startX) * 2;
            catRibbonMobile.scrollLeft = scrollLeft - walk;
        });

        // Touch events for mobile
        catRibbonMobile.addEventListener('touchstart', (e) => {
            startX = e.touches[0].pageX - catRibbonMobile.offsetLeft;
            scrollLeft = catRibbonMobile.scrollLeft;
        }, { passive: true });

        catRibbonMobile.addEventListener('touchmove', (e) => {
            const x = e.touches[0].pageX - catRibbonMobile.offsetLeft;
            const walk = (x - startX) * 2;
            catRibbonMobile.scrollLeft = scrollLeft - walk;
        }, { passive: true });
    }

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
});
</script>

<style>
/* Hide scrollbar for category ribbons */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

<?php get_footer(); ?>
