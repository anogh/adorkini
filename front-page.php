<?php 
get_header(); 

// Get WooCommerce product categories
$categories = get_terms([
    'taxonomy' => 'product_cat',
    'hide_empty' => false,
    'parent' => 0, // Only top-level categories
]);

// Icon mapping for different categories (using available SVG icons)
$category_icons = [
    'electronics' => 'bolt',
    'men\'s fashion' => 'person',
    'mens fashion' => 'person',
    'men' => 'person',
    'women\'s fashion' => 'person',
    'womens fashion' => 'person',
    'women' => 'person',
    'home & garden' => 'home',
    'home and garden' => 'home',
    'home' => 'home',
    'sports & outdoors' => 'bolt',
    'sports and outdoors' => 'bolt',
    'sports' => 'bolt',
    'toys & games' => 'star',
    'toys and games' => 'star',
    'toys' => 'star',
    'books' => 'inventory_2',
    'beauty & health' => 'favorite',
    'beauty and health' => 'favorite',
    'beauty' => 'favorite',
    'health' => 'favorite',
    'clothing' => 'shopping_bag',
    'accessories' => 'star',
    'jewelry' => 'star',
    'shoes' => 'shopping_bag',
    'bags' => 'shopping_bag',
    'food' => 'shopping_cart',
    'pets' => 'favorite',
    'automotive' => 'shopping_cart',
    'music' => 'star',
    'gaming' => 'bolt',
    'office' => 'inventory_2',
    'baby' => 'favorite',
    'kids' => 'favorite',
];

// Random Products Query for New Arrivals
$random_products_query = new WP_Query([
    'post_type' => 'product',
    'posts_per_page' => 8,
    'orderby' => 'rand',
    'post_status' => 'publish',
]);
?>

<main class="flex-grow pb-24 lg:pb-0">
    
    <!-- Desktop Content -->
    <div class="hidden lg:block">
        <div class="container mx-auto px-6 py-8">
            <!-- Hero Section -->
            <section class="flex flex-col gap-6 lg:flex-row">
                <!-- Vertical Category List (15%) -->
                <aside class="w-full lg:w-[18%] lg:max-w-[240px] flex-shrink-0">
                    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-background-dark" style="height: 500px;">
                        <div class="h-full flex flex-col">
                            <h3 class="mb-4 text-base font-bold text-gray-900 dark:text-white p-4 pb-0"><?php echo __t('Categories'); ?></h3>
                            <div class="flex-1 overflow-y-auto px-4 pb-4">
                        <nav class="flex flex-col gap-1">
                            <?php
                            if (!empty($categories) && !is_wp_error($categories)) {
                                $index = 0;
                                foreach ($categories as $category) {
                                    // Get the icon for this category
                                    $category_slug = strtolower($category->slug);
                                    $category_name_lower = strtolower($category->name);
                                    
                                    // Try to match icon by slug or name
                                    $icon = 'category'; // default icon
                                    foreach ($category_icons as $key => $icon_name) {
                                        if (strpos($category_slug, $key) !== false || strpos($category_name_lower, $key) !== false) {
                                            $icon = $icon_name;
                                            break;
                                        }
                                    }
                                    
                                    // First category gets highlighted style
                                    $class = $index === 0 
                                        ? 'flex items-center gap-3 rounded-lg bg-primary/10 px-3 py-2 text-primary dark:bg-primary/20' 
                                        : 'flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-800/50';
                                    ?>
                                    <a class="<?php echo esc_attr($class); ?>" href="<?php echo esc_url(get_term_link($category)); ?>">
                                        <?php echo warafy_get_icon_svg($icon, 'w-5 h-5'); ?>
                                        <p class="text-sm font-medium"><?php echo esc_html($category->name); ?></p>
                                    </a>
                                    <?php
                                    $index++;
                                }
                            } else {
                                // Fallback if no categories exist
                                echo '<p class="text-sm text-gray-500 dark:text-gray-400">' . __t('No categories found') . '</p>';
                            }
                            ?>
                        </nav>
                            </div>
                        </div>
                    </div>
                </aside>
                <!-- Hero Slider (85%) -->
                <div class="relative w-full flex-1 overflow-hidden rounded-xl hero-slider-container" style="height: 500px;">
                    <?php for ($i = 1; $i <= 3; $i++): 
                        $s = ($i == 1) ? '' : '_' . $i;
                        $bg = get_option('warafy_hero_desktop_image'.$s, ($i == 1 ? 'https://lh3.googleusercontent.com/aida-public/AB6AXuBQt_hNv9RJISICe6QmR94cZW3qkEA5JS5XEya0vVDuE6PczpKl1RV2_DCp0Aire2HzStJG74f44FC71rhQIE5i1JA4Z4i2CtFawU4Rsf1yfjJFCHy6oJx6rVaW0lRtVsoRSL0oEoTWYHNJieRZD6BtMbnGqXg7LKmuZ4f7NiCpk_ynULjVXdCo_lUTuxYWM_f2PjENgs6vmjCqBTYJxsU9Br-R7MnIjo-AHXeGJmdUaE7xvx8b1wq7jNB2kHXlWmMPTX30bfVZkQY' : ''));
                        
                        if (empty($bg)) continue;
                        
                        $title = get_option('warafy_hero_title'.$s, ($i == 1 ? 'Summer Styles Are Here' : ''));
                        $desc = get_option('warafy_hero_description'.$s, ($i == 1 ? 'Discover the hottest trends of the season and refresh your wardrobe.' : ''));
                        $btn_text = get_option('warafy_hero_button_text'.$s, ($i == 1 ? __t('Shop Collection') : __t('Shop Now')));
                        $btn_url = get_option('warafy_hero_button_url'.$s, ($i == 1 ? wc_get_page_permalink('shop') : ''));
                        
                        // Bengali Override
                        $lang = isset($_COOKIE['warafy_language']) ? $_COOKIE['warafy_language'] : 'en';
                        if ($lang === 'bn') {
                            $title_bn = get_option('warafy_hero_title_bn'.$s);
                            if (!empty($title_bn)) $title = $title_bn;
                            
                            $desc_bn = get_option('warafy_hero_description_bn'.$s);
                            if (!empty($desc_bn)) $desc = $desc_bn;
                            
                            $btn_text_bn = get_option('warafy_hero_button_text_bn'.$s);
                            if (!empty($btn_text_bn)) $btn_text = $btn_text_bn;
                        }
                    ?>
                    <div class="hero-slide absolute inset-0 w-full h-full bg-center bg-no-repeat bg-cover flex items-end p-12 text-white transition-opacity duration-1000 <?php echo $i == 1 ? 'opacity-100 z-10' : 'opacity-0 z-0'; ?>" 
                         data-index="<?php echo $i; ?>"
                         style='background-image: linear-gradient(to top, rgba(0,0,0,0.6), rgba(0,0,0,0)), url("<?php echo esc_url($bg); ?>");'>
                        <div class="max-w-md slide-content transition-transform duration-1000 <?php echo $i == 1 ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'; ?>">
                            <h2 class="text-4xl font-extrabold tracking-tight"><?php echo __t($title); ?></h2>
                            <p class="mt-2 text-lg text-white/90"><?php echo __t($desc); ?></p>
                            <a href="<?php echo esc_url($btn_url); ?>" class="mt-6 flex cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-base font-bold shadow-lg hover:bg-primary/90 w-fit">
                                <span><?php echo __t($btn_text); ?></span>
                            </a>
                        </div>
                    </div>
                    <?php endfor; ?>
                    
                    <!-- Slider Indicators -->
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-20">
                        <button class="slider-dot h-2 w-6 rounded-full bg-white transition-all duration-300" data-index="1"></button>
                        <button class="slider-dot h-2 w-2 rounded-full bg-white/50 transition-all duration-300" data-index="2"></button>
                        <button class="slider-dot h-2 w-2 rounded-full bg-white/50 transition-all duration-300" data-index="3"></button>
                    </div>
                </div>
            </section>
        <?php
        if (!function_exists('warafy_render_desktop_compact_product')) {
            function warafy_render_desktop_compact_product($product) {
                if (!$product) return;
                ?>
                <div class="bg-white border border-gray-100 flex flex-col p-3 h-full rounded-xl relative transition-all hover:shadow-xl warafy-desktop-product-card">
                    <a href="<?php echo get_permalink($product->get_id()); ?>" class="w-full aspect-square bg-center bg-no-repeat bg-contain mb-4 block" style='background-image: url("<?php echo get_the_post_thumbnail_url($product->get_id(), 'woocommerce_thumbnail'); ?>");'></a>
                    
                    <a href="<?php echo get_permalink($product->get_id()); ?>" class="w-full text-base font-medium text-black line-clamp-2 leading-[1.4] min-h-[44px] mb-3 hover:text-primary">
                        <?php echo get_the_title($product->get_id()); ?>
                    </a>
                    
                    <div class="warafy-product-actions warafy-desktop-product-actions w-full mt-auto mb-3">
                        <div class="flex items-center flex-wrap gap-2 min-w-0">
                            <span class="text-gray-400 line-through text-sm"><?php echo wc_price($product->get_regular_price()); ?></span>
                            <span class="text-black font-bold text-2xl"><?php echo wc_price($product->get_price()); ?></span>
                        </div>
                        
                        <button class="add-to-cart-btn bg-[#FFC107] hover:bg-[#FFB300] text-black text-sm font-bold px-4 py-2 rounded-full flex items-center justify-center whitespace-normal text-center flex-shrink-0 transition-colors shadow-sm" data-product-id="<?php echo $product->get_id(); ?>">
                            <span class="add-text"><?php echo __t('Add to cart'); ?></span>
                            <span class="added-text hidden text-white"><?php echo __t('Added'); ?></span>
                        </button>
                    </div>

                    <a href="<?php echo get_permalink($product->get_id()); ?>" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold text-xl py-3 rounded-lg text-center transition-colors shadow-md">
                        <?php echo __t('ORDER NOW'); ?>
                    </a>
                </div>
                <?php
            }
        }

        // Queries
        $flash_sale_query_desktop = new WP_Query([
            'post_type' => 'product',
            'post__in' => array_merge(array(0), wc_get_product_ids_on_sale()),
            'posts_per_page' => 5,
            'post_status' => 'publish',
        ]);
        
        $homepage_rankings_desktop = warafy_get_ranked_products('homepage', 5);
        if (!empty($homepage_rankings_desktop)) {
            $most_popular_query_desktop = new WP_Query([
                'post_type' => 'product',
                'post__in' => $homepage_rankings_desktop,
                'orderby' => 'post__in',
                'posts_per_page' => 5
            ]);
        } else {
            $most_popular_query_desktop = new WP_Query([
                'post_type' => 'product',
                'posts_per_page' => 5,
                'meta_key' => 'total_sales',
                'orderby' => 'meta_value_num',
                'post_status' => 'publish',
            ]);
        }
        
        $new_arrivals_query_desktop = new WP_Query([
            'post_type' => 'product',
            'posts_per_page' => 5,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_status' => 'publish',
        ]);
        ?>

        <!-- Flash Sale Desktop -->
        <?php if ($flash_sale_query_desktop->have_posts()): ?>
        <section class="mt-8">
            <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white mb-4 front-page-section-title"><?php echo __t('Flash Sale'); ?></h2>
            <div class="grid grid-cols-5 gap-4">
                <?php
                while ($flash_sale_query_desktop->have_posts()) {
                    $flash_sale_query_desktop->the_post();
                    global $product;
                    warafy_render_desktop_compact_product($product);
                }
                wp_reset_postdata();
                ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Most Popular Desktop -->
        <?php if ($most_popular_query_desktop->have_posts()): ?>
        <section class="mt-8">
            <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white mb-4 front-page-section-title"><?php echo __t('Most Popular'); ?></h2>
            <div class="grid grid-cols-5 gap-4">
                <?php
                while ($most_popular_query_desktop->have_posts()) {
                    $most_popular_query_desktop->the_post();
                    global $product;
                    warafy_render_desktop_compact_product($product);
                }
                wp_reset_postdata();
                ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- New Arrivals Desktop -->
        <?php if ($new_arrivals_query_desktop->have_posts()): ?>
        <section class="mt-8">
            <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white mb-4 front-page-section-title"><?php echo __t('New Arrivals'); ?></h2>
            <div class="grid grid-cols-5 gap-4">
                <?php
                while ($new_arrivals_query_desktop->have_posts()) {
                    $new_arrivals_query_desktop->the_post();
                    global $product;
                    warafy_render_desktop_compact_product($product);
                }
                wp_reset_postdata();
                ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Promotional Banner Desktop -->
        <section class="mt-8 mb-4">
            <div class="w-full bg-[#E50914] text-white text-center py-3 text-lg font-medium tracking-wide rounded-md">
                <?php echo __t('Free Shipping over 2500'); ?>
            </div>
        </section>

    </div>
    </div> <!-- Close desktop container -->

    <!-- Mobile Content -->
    <div class="block lg:hidden relative mobile-content-wrapper bg-[#f5f5f5] pb-4">
        <!-- Hero Slider (Mobile) -->
        <div class="relative w-full overflow-hidden mobile-hero-section" style="height: 160px; width: 100% !important; max-width: 100% !important; overflow-x: hidden !important; box-sizing: border-box !important;">
            <div class="relative h-full w-full">
                <?php for ($i = 1; $i <= 3; $i++): 
                    $s = ($i == 1) ? '' : '_' . $i;
                    $bg = get_option('warafy_hero_mobile_image'.$s);
                    // Fallback to desktop if mobile empty
                    if (empty($bg)) $bg = get_option('warafy_hero_desktop_image'.$s, ($i == 1 ? 'https://lh3.googleusercontent.com/aida-public/AB6AXuBQt_hNv9RJISICe6QmR94cZW3qkEA5JS5XEya0vVDuE6PczpKl1RV2_DCp0Aire2HzStJG74f44FC71rhQIE5i1JA4Z4i2CtFawU4Rsf1yfjJFCHy6oJx6rVaW0lRtVsoRSL0oEoTWYHNJieRZD6BtMbnGqXg7LKmuZ4f7NiCpk_ynULjVXdCo_lUTuxYWM_f2PjENgs6vmjCqBTYJxsU9Br-R7MnIjo-AHXeGJmdUaE7xvx8b1wq7jNB2kHXlWmMPTX30bfVZkQY' : ''));
                    
                    if (empty($bg)) continue;
                    
                    $title = get_option('warafy_hero_mobile_title'.$s, ($i == 1 ? 'Summer Styles' : ''));
                    $desc = get_option('warafy_hero_mobile_description'.$s, ($i == 1 ? 'Discover the latest trends for the season.' : ''));
                    $btn_text = get_option('warafy_hero_mobile_button_text'.$s, ($i == 1 ? __t('Shop Now') : __t('Shop Now')));
                    $btn_url = get_option('warafy_hero_button_url'.$s, ($i == 1 ? wc_get_page_permalink('shop') : ''));

                    // Bengali Override
                    $lang = isset($_COOKIE['warafy_language']) ? $_COOKIE['warafy_language'] : 'en';
                    if ($lang === 'bn') {
                        $title_bn = get_option('warafy_hero_mobile_title_bn'.$s);
                        if (!empty($title_bn)) $title = $title_bn;
                        
                        $desc_bn = get_option('warafy_hero_mobile_description_bn'.$s);
                        if (!empty($desc_bn)) $desc = $desc_bn;
                        
                        $btn_text_bn = get_option('warafy_hero_mobile_button_text_bn'.$s);
                        if (!empty($btn_text_bn)) $btn_text = $btn_text_bn;
                    }
                ?>
                <div class="mobile-hero-slide absolute inset-0 w-full h-full bg-gray-800 bg-cover bg-center transition-opacity duration-1000 <?php echo $i == 1 ? 'opacity-100 z-10' : 'opacity-0 z-0'; ?>"
                     data-index="<?php echo $i; ?>" 
                     style='background-image: linear-gradient(to top, rgba(0,0,0,0.6), rgba(0,0,0,0)), url("<?php echo esc_url($bg); ?>"); width: 100% !important; max-width: 100% !important; overflow-x: hidden !important; box-sizing: border-box !important;'>
                    <div class="absolute inset-0 bg-black/40"></div>
                    <div class="relative z-10 flex h-full flex-col items-start justify-end p-6 text-white slide-content transition-transform duration-1000 <?php echo $i == 1 ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'; ?>">
                        <h2 class="text-3xl font-bold drop-shadow-md"><?php echo __t($title); ?></h2>
                        <p class="mb-4 mt-1 text-sm drop-shadow-md"><?php echo __t($desc); ?></p>
                        <a href="<?php echo esc_url($btn_url); ?>" class="flex max-w-xs cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-5 bg-primary text-white text-sm font-bold shadow-lg">
                            <span><?php echo __t($btn_text); ?></span>
                        </a>
                    </div>
                </div>
                <?php endfor; ?>
            </div>

            <div class="absolute bottom-4 left-1/2 flex -translate-x-1/2 space-x-2 z-20">
                <button class="mobile-slider-dot h-2 w-2 rounded-full bg-white shadow-sm transition-all duration-300 opacity-100" data-index="1"></button>
                <button class="mobile-slider-dot h-2 w-2 rounded-full bg-white/50 shadow-sm transition-all duration-300" data-index="2"></button>
                <button class="mobile-slider-dot h-2 w-2 rounded-full bg-white/50 shadow-sm transition-all duration-300" data-index="3"></button>
            </div>
        </div>
        <?php
        // Flash Sale Query
        $flash_sale_query = new WP_Query([
            'post_type' => 'product',
            'post__in' => array_merge(array(0), wc_get_product_ids_on_sale()),
            'posts_per_page' => 4,
            'post_status' => 'publish',
        ]);
        
        // Most Popular Query
        $homepage_rankings = warafy_get_ranked_products('homepage', 4);
        if (!empty($homepage_rankings)) {
            $most_popular_query = new WP_Query([
                'post_type' => 'product',
                'post__in' => $homepage_rankings,
                'orderby' => 'post__in',
                'posts_per_page' => 4
            ]);
        } else {
            $most_popular_query = new WP_Query([
                'post_type' => 'product',
                'posts_per_page' => 4,
                'meta_key' => 'total_sales',
                'orderby' => 'meta_value_num',
                'post_status' => 'publish',
            ]);
        }
        
        // New Arrivals Query
        $new_arrivals_query = new WP_Query([
            'post_type' => 'product',
            'posts_per_page' => 4,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_status' => 'publish',
        ]);

        if (!function_exists('warafy_render_mobile_compact_product')) {
            function warafy_render_mobile_compact_product($product) {
                if (!$product) return;
                ?>
                <div class="bg-white border border-gray-100 flex flex-col p-2 h-full rounded-xl relative warafy-mobile-product-card shadow-sm">
                    <a href="<?php echo get_permalink($product->get_id()); ?>" class="w-full aspect-square bg-center bg-no-repeat bg-contain mb-3 block" style='background-image: url("<?php echo get_the_post_thumbnail_url($product->get_id(), 'woocommerce_thumbnail'); ?>");'></a>
                    
                    <a href="<?php echo get_permalink($product->get_id()); ?>" class="w-full text-[12px] font-medium text-black line-clamp-2 leading-[1.3] min-h-[32px] mb-2 hover:text-primary">
                        <?php echo get_the_title($product->get_id()); ?>
                    </a>
                    
                    <div class="warafy-product-actions warafy-mobile-product-actions w-full mt-auto mb-2">
                        <div class="flex items-center flex-wrap gap-1 min-w-0">
                            <span class="text-gray-400 line-through text-xs"><?php echo wc_price($product->get_regular_price()); ?></span>
                            <span class="text-black font-bold text-lg"><?php echo wc_price($product->get_price()); ?></span>
                        </div>
                        
                        <button class="add-to-cart-btn bg-[#FFC107] hover:bg-[#FFB300] text-black text-xs font-bold px-3 py-1.5 rounded-full flex items-center justify-center whitespace-normal text-center flex-shrink-0 transition-colors shadow-sm" data-product-id="<?php echo $product->get_id(); ?>">
                            <span class="add-text"><?php echo __t('Add to cart'); ?></span>
                            <span class="added-text hidden text-white"><?php echo __t('Added'); ?></span>
                        </button>
                    </div>

                    <a href="<?php echo get_permalink($product->get_id()); ?>" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold text-base py-2.5 rounded-lg text-center transition-colors shadow-sm">
                        <?php echo __t('ORDER NOW'); ?>
                    </a>
                </div>
                <?php
            }
        }
        ?>

        <style>
            .warafy-mobile-product-card {
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }

            .warafy-product-actions {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                gap: 8px;
                align-items: center;
                width: 100%;
            }

            .warafy-product-actions .mobile-compact-price {
                min-width: 0;
                width: 100%;
                padding-right: 0 !important;
            }

            .warafy-product-actions .add-to-cart-btn {
                width: auto;
                justify-self: end;
                transform: none !important;
                box-sizing: border-box;
                max-width: 100%;
                overflow: hidden;
                white-space: normal !important;
                text-align: center;
            }

            .warafy-mobile-product-card .add-to-cart-btn {
                padding: 7px 12px !important;
                font-size: 12px !important;
                line-height: 1.15;
            }

            .warafy-product-actions .add-to-cart-btn .add-text,
            .warafy-product-actions .add-to-cart-btn .added-text {
                display: block;
                width: 100%;
                white-space: normal;
                overflow: visible;
                text-overflow: clip;
                font-size: inherit !important;
                font-weight: inherit !important;
                line-height: 1.1 !important;
            }

            .warafy-product-actions .add-to-cart-btn:hover {
                transform: translateY(-1px) !important;
            }
            
            /* Desktop: Add to cart button - 10% smaller */
            .warafy-desktop-product-card .add-to-cart-btn {
                padding: 6px 12px !important;
                font-size: 13px !important;
            }
            
            /* Desktop: Original price strikethrough */
            .warafy-desktop-product-card .mobile-compact-price del,
            .warafy-desktop-product-card .mobile-compact-price .woocommerce-Price-amount del,
            .warafy-desktop-product-card .mobile-compact-price span.price del,
            .warafy-desktop-product-card .mobile-compact-price p.price del {
                text-decoration: line-through !important;
                text-decoration-thickness: 2px !important;
                text-decoration-color: #9ca3af !important;
                color: #9ca3af !important;
                opacity: 0.6 !important;
                font-size: 0.85em !important;
                font-weight: 400 !important;
                margin-right: 6px !important;
                display: inline-block !important;
            }
            
            /* Desktop: Sale price more prominent */
            .warafy-desktop-product-card .mobile-compact-price ins,
            .warafy-desktop-product-card .mobile-compact-price .woocommerce-Price-amount ins {
                text-decoration: none !important;
                font-weight: 800 !important;
                color: #000 !important;
                font-size: 1.15em !important;
            }
            
            .dark .warafy-desktop-product-card .mobile-compact-price del {
                color: #6b7280 !important;
                text-decoration-color: #6b7280 !important;
            }
            
            .dark .warafy-desktop-product-card .mobile-compact-price ins {
                color: #fff !important;
            }
            
            @media (max-width: 479px) {
                .warafy-product-actions {
                    grid-template-columns: minmax(0, 1fr);
                    gap: 6px;
                }

                .warafy-product-actions .add-to-cart-btn {
                    justify-self: stretch;
                    width: 100%;
                    padding: 6px 12px !important;
                    font-size: 11px !important;
                }
            }

            @media (min-width: 480px) and (max-width: 767px) {
                .warafy-product-actions {
                    gap: 8px;
                }

                .warafy-product-actions .add-to-cart-btn {
                    padding: 7px 12px !important;
                    font-size: 12px !important;
                }
            }

            @media (min-width: 768px) and (max-width: 1023px) {
                .warafy-product-actions {
                    gap: 10px;
                }

                .warafy-product-actions .add-to-cart-btn {
                    padding: 8px 14px !important;
                    font-size: 13px !important;
                }
            }

            @media (min-width: 1024px) {
                .warafy-product-actions {
                    gap: 12px;
                }

                .warafy-product-actions .add-to-cart-btn {
                    padding: 8px 14px !important;
                    font-size: 13px !important;
                }

                /* Mobile: Original price strikethrough */
                .warafy-mobile-product-card .mobile-compact-price del,
                .warafy-mobile-product-card .mobile-compact-price .woocommerce-Price-amount del {
                    text-decoration: line-through !important;
                    text-decoration-thickness: 2px !important;
                    text-decoration-color: #9ca3af !important;
                    color: #9ca3af !important;
                    opacity: 0.6 !important;
                    font-size: 0.85em !important;
                    font-weight: 400 !important;
                    margin-right: 6px !important;
                    display: inline-block !important;
                }
                
                /* Mobile: Sale price more prominent */
                .warafy-mobile-product-card .mobile-compact-price ins,
                .warafy-mobile-product-card .mobile-compact-price .woocommerce-Price-amount ins {
                    text-decoration: none !important;
                    font-weight: 800 !important;
                    color: #000 !important;
                    font-size: 1.15em !important;
                }
                
                .dark .warafy-mobile-product-card .mobile-compact-price del {
                    color: #6b7280 !important;
                    text-decoration-color: #6b7280 !important;
                }
                
                .dark .warafy-mobile-product-card .mobile-compact-price ins {
                    color: #fff !important;
                }
            }
            
            /* Add to cart button adding state */
            .add-to-cart-btn.adding {
                background-color: #d1d5db !important;
                color: #4b5563 !important;
            }
        </style>

        <!-- Flash Sale -->
        <?php if ($flash_sale_query->have_posts()): ?>
        <div class="bg-white w-full pt-1 pb-4 mb-2 shadow-sm">
            <h2 class="text-black text-[16px] font-bold tracking-tight px-4 py-2 bg-white front-page-section-title"><?php echo __t('Flash Sale'); ?></h2>
            <div class="grid grid-cols-2 gap-[10px] px-3">
                <?php
                while ($flash_sale_query->have_posts()) {
                    $flash_sale_query->the_post();
                    global $product;
                    warafy_render_mobile_compact_product($product);
                }
                wp_reset_postdata();
                ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Most Popular -->
        <?php if ($most_popular_query->have_posts()): ?>
        <div class="bg-white w-full pt-1 pb-4 mb-2 shadow-sm">
            <h2 class="text-black text-[16px] font-bold tracking-tight px-4 py-2 bg-white front-page-section-title"><?php echo __t('Most Popular'); ?></h2>
            <div class="grid grid-cols-2 gap-[10px] px-3">
                <?php
                while ($most_popular_query->have_posts()) {
                    $most_popular_query->the_post();
                    global $product;
                    warafy_render_mobile_compact_product($product);
                }
                wp_reset_postdata();
                ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- New Arrivals -->
        <?php if ($new_arrivals_query->have_posts()): ?>
        <div class="bg-white w-full pt-1 pb-4 mb-2 shadow-sm">
            <h2 class="text-black text-[16px] font-bold tracking-tight px-4 py-2 bg-white front-page-section-title"><?php echo __t('New Arrivals'); ?></h2>
            <div class="grid grid-cols-2 gap-[10px] px-3">
                <?php
                while ($new_arrivals_query->have_posts()) {
                    $new_arrivals_query->the_post();
                    global $product;
                    warafy_render_mobile_compact_product($product);
                }
                wp_reset_postdata();
                ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Promotional Banner -->
        <div class="w-full bg-[#E50914] text-white text-center py-2.5 text-[15px] font-medium tracking-wide">
            <?php echo __t('Free Shipping over 2500'); ?>
        </div>
        <!-- CSS Version: 3.4.4 - Desktop price strikethrough + button 10% smaller -->
    </div>

</main>


<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Front page loaded - Add to Cart script initialized');
    
    // DEBUG: Check cart count elements on load
    const cartElements = document.querySelectorAll('.cart-count');
    console.log('DEBUG - Found cart count elements on page load:', cartElements.length);
    cartElements.forEach((element, index) => {
        console.log(`DEBUG - Cart element ${index}:`, element, 'current count:', element.textContent);
    });
    
    // Infinite Scroll Logic (Handles Multiple Instances)
    function initInfiniteScroll(gridSelector, triggerSelector) {
        // We select ALL instances (desktop and mobile)
        const triggers = document.querySelectorAll(triggerSelector);
        
        triggers.forEach(trigger => {
            // Find the grid associated with this trigger
            // Strategies:
            // 1. Check previous element sibling (standard structure)
            // 2. Check by specific mobile ID
            // 3. Fallback to generic class selector (desktop)
            
            let grid = null;
            
            // Strategy 1: Sibling
            if (trigger.previousElementSibling && trigger.previousElementSibling.classList.contains('warafy-recommended-grid')) {
                grid = trigger.previousElementSibling;
            } else if (trigger.parentElement && trigger.parentElement.previousElementSibling && trigger.parentElement.previousElementSibling.classList.contains('warafy-recommended-grid')) {
                 // Check parent's sibling (if trigger is wrapped in a dedicated div)
                 grid = trigger.parentElement.previousElementSibling;
            }
            
            // Strategy 2: Mobile ID
            if (!grid && trigger.id === 'warafy-loading-trigger-mobile') {
                grid = document.getElementById('warafy-recommended-grid-mobile');
            }
            
            // Strategy 3: Desktop fallback
            if (!grid && !trigger.id) {
                 grid = document.querySelector('.warafy-recommended-grid:not(#warafy-recommended-grid-mobile)');
            }
            
            if (!grid) {
                console.warn('Infinite Scroll: Could not find grid for trigger', trigger);
                return;
            }
            
            const spinner = trigger.querySelector('.loading-spinner');
            
            let page = 1;
            let isLoading = false;
            let hasMore = true;
            let prefetchedData = null;

            // Shared Styles
            const styleId = 'warafy-fade-style';
            if (!document.getElementById(styleId)) {
                const style = document.createElement('style');
                style.id = styleId;
                style.textContent = `
                    .product-card-recommendation {
                        opacity: 0;
                        transform: translateY(20px);
                        animation: fadeInUp 0.4s ease-out forwards;
                    }
                    @keyframes fadeInUp {
                        to {
                            opacity: 1;
                            transform: translateY(0);
                        }
                    }
                `;
                document.head.appendChild(style);
            }
            
            const fetchPage = async (pageNum) => {
                const response = await fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({action: 'warafy_load_recommendations', page: pageNum})
                });
                return response.json();
            };
            
            const renderProducts = (html) => {
                const temp = document.createElement('div');
                temp.innerHTML = html; // Convert to nodes
                const cards = temp.querySelectorAll('.product-card-recommendation');
                cards.forEach((card, index) => {
                    card.style.animationDelay = `${index * 0.05}s`;
                });
                grid.insertAdjacentHTML('beforeend', temp.innerHTML);
            };
            
            const prefetchNextPage = async () => {
                if (!hasMore || prefetchedData) return;
                try {
                    prefetchedData = await fetchPage(page + 1);
                    if (!prefetchedData.success || !prefetchedData.data?.html) prefetchedData = null;
                } catch (e) { prefetchedData = null; }
            };
            
            const loadMoreProducts = async () => {
                if (isLoading || !hasMore) return;
                
                // Only load if the grid is visible check
                if (grid.offsetParent === null && window.getComputedStyle(grid).display === 'none') {
                   return;
                }

                isLoading = true;
                if(spinner) spinner.classList.remove('hidden');
                
                try {
                    let data;
                    if (prefetchedData && page > 1) {
                        data = prefetchedData;
                        prefetchedData = null;
                        page++;
                    } else {
                        data = await fetchPage(page);
                        if (data.success && data.data?.html) page++;
                    }
                    
                    if (data.success && data.data?.html) {
                        renderProducts(data.data.html);
                        prefetchNextPage();
                    } else {
                        hasMore = false;
                        trigger.innerHTML = '<p class="text-gray-500 text-sm py-4"><?php echo __t("No more products to show"); ?></p>';
                    }
                } catch (error) {
                    console.error('Error loading recommendations:', error);
                } finally {
                    isLoading = false;
                    if(spinner) spinner.classList.add('hidden');
                }
            };
            
            // Initial load check
            loadMoreProducts();
            
            // Config
            const isMobile = window.innerWidth < 1024;
            const rootMargin = isMobile ? '200px' : '600px';
            
            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting && !isLoading && hasMore) {
                    loadMoreProducts();
                }
            }, { rootMargin: rootMargin });
            
            observer.observe(trigger);
            
            // Scroll fallback for mobile
            if (isMobile) {
                let scrollTimeout;
                const checkScroll = () => {
                    if (isLoading || !hasMore) return;
                    if (grid.offsetParent === null) return; // Hidden
                    
                    const triggerRect = trigger.getBoundingClientRect();
                    if (triggerRect.top < window.innerHeight + 200) {
                        loadMoreProducts();
                    }
                };
                window.addEventListener('scroll', () => {
                    clearTimeout(scrollTimeout);
                    scrollTimeout = setTimeout(checkScroll, 100);
                }, { passive: true });
                setTimeout(checkScroll, 500);
            }
        });
    }

    // Initialize Infinite Scroll
    initInfiniteScroll('.warafy-recommended-grid', '.warafy-loading-trigger');

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
            
            console.log('Elements found:', { addIcon, addedIcon, addText, addedText });
            
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
                        
                        // Update cart fragments
                        if (typeof jQuery !== 'undefined' && jQuery('body').trigger) {
                            // Don't pass our button to avoid jQuery conflicts
                            jQuery('body').trigger('added_to_cart', [response.fragments, response.cart_hash]);
                        }
                        
                        // IMMEDIATE cart count update - no delay
                        const cartCountElements = document.querySelectorAll('.cart-count');
                        console.log('IMMEDIATE UPDATE - found cart count elements:', cartCountElements.length);
                        console.log('IMMEDIATE UPDATE - current elements:', cartCountElements);
                        
                        // Get current count and increment by 1
                        if (cartCountElements.length > 0) {
                            const currentCount = parseInt(cartCountElements[0].textContent || '0');
                            const newCount = currentCount + 1;
                            console.log('IMMEDIATE UPDATE from', currentCount, 'to', newCount);
                            
                            cartCountElements.forEach((element, index) => {
                                console.log(`IMMEDIATE UPDATE - updating element ${index}:`, element, 'from', element.textContent, 'to', newCount);
                                element.textContent = newCount;
                                // Force DOM update with multiple methods
                                element.style.display = 'none';
                                element.offsetHeight; // Force reflow
                                element.style.display = 'flex';
                                // Add animation to draw attention
                                element.style.transform = 'scale(1.2)';
                                setTimeout(() => {
                                    element.style.transform = 'scale(1)';
                                }, 200);
                            });
                            
                            console.log('IMMEDIATE UPDATE completed - new count should be:', newCount);
                            
                            // Force mobile-specific update
                            if (window.innerWidth <= 1024) {
                                console.log('MOBILE DETECTED - forcing additional update');
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
                        } else {
                            console.error('IMMEDIATE UPDATE - No cart count elements found!');
                        }
                        
                        // Also call the full update function for consistency
                        updateCartCount();
                        
                        // Refresh page on mobile to show correct cart count
                        if (window.innerWidth <= 1024) {
                            console.log('MOBILE DETECTED - refreshing page to show correct cart count');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000); // Wait 1 second to show "Added to cart" state
                        }
                        
                        button.classList.remove('adding');
                        button.disabled = false;
                        
                        console.log('Product successfully added to cart');
                    } else {
                        // Show error state
                        console.error('Add to cart error:', response.error);
                        if (addIcon) {
                            addIcon.textContent = 'close';
                            addIcon.style.animation = '';
                        }
                        if (addText) addText.textContent = 'Error';
                        
                        button.style.backgroundColor = '#dc2626';
                        button.style.color = 'white';
                        button.title = response.error || 'Failed to add to cart';
                        
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
                        
                        // IMMEDIATE cart count update - no delay
                        const cartCountElements = document.querySelectorAll('.cart-count');
                        console.log('IMMEDIATE UPDATE (fallback) - found cart count elements:', cartCountElements.length);
                        
                        // Get current count and increment by 1
                        if (cartCountElements.length > 0) {
                            const currentCount = parseInt(cartCountElements[0].textContent || '0');
                            const newCount = currentCount + 1;
                            console.log('IMMEDIATE UPDATE (fallback) from', currentCount, 'to', newCount);
                            
                            cartCountElements.forEach((element, index) => {
                                console.log(`IMMEDIATE UPDATE (fallback) - updating element ${index}:`, element, 'from', element.textContent, 'to', newCount);
                                element.textContent = newCount;
                                // Force DOM update with multiple methods
                                element.style.display = 'none';
                                element.offsetHeight; // Force reflow
                                element.style.display = 'flex';
                                // Add animation to draw attention
                                element.style.transform = 'scale(1.2)';
                                setTimeout(() => {
                                    element.style.transform = 'scale(1)';
                                }, 200);
                            });
                            
                            // Force mobile-specific update
                            if (window.innerWidth <= 1024) {
                                console.log('MOBILE DETECTED (fallback) - forcing additional update');
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
                        } else {
                            console.error('IMMEDIATE UPDATE (fallback) - No cart count elements found!');
                        }
                        
                        // Also call the full update function for consistency
                        updateCartCount();
                        
                        // Refresh page on mobile to show correct cart count
                        if (window.innerWidth <= 1024) {
                            console.log('MOBILE DETECTED (fallback) - refreshing page to show correct cart count');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000); // Wait 1 second to show "Added to cart" state
                        }
                        
                        button.classList.remove('adding');
                        button.disabled = false;
                        
                        console.log('Product successfully added to cart');
                    } else {
                        // Show error state
                        console.error('Add to cart error:', data.error);
                        if (addIcon) {
                            addIcon.textContent = 'close';
                            addIcon.style.animation = '';
                        }
                        if (addText) addText.textContent = 'Error';
                        
                        button.style.backgroundColor = '#dc2626';
                        button.style.color = 'white';
                        button.title = data.error || 'Failed to add to cart';
                        
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
                })
                .catch(error => {
                    console.error('Add to cart error:', error);
                    
                    // Reset button state on error
                    if (addIcon) {
                        addIcon.textContent = 'add_shopping_cart';
                        addIcon.style.animation = '';
                    }
                    if (addText) addText.textContent = 'Add to Cart';
                    
                    button.style.backgroundColor = '';
                    button.style.color = '';
                    button.title = 'Add to Cart';
                    
                    if (addIcon) addIcon.classList.remove('hidden');
                    if (addText) addText.classList.remove('hidden');
                    if (addedIcon) addedIcon.classList.add('hidden');
                    if (addedText) addedText.classList.add('hidden');
                    
                    button.classList.remove('adding');
                    button.disabled = false;
                });
            }
        }
    });
    
    // Update cart count function
    function updateCartCount() {
        console.log('Updating cart count...');
        
        // Skip WooCommerce fragment refresh to avoid overriding our styled elements
        // Method 1: Use our custom fragment refresh (but skip cart count elements)
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
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
                        console.log('Skipping cart count fragment to preserve styling:', key);
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
            console.log('Initial cart data:', data);
            if (data.success && data.data && data.data.cart) {
                const cartCount = data.data.cart.count;
                console.log('Initial cart count:', cartCount);
                
                // Update all cart count elements with the specific class
                const cartCountElements = document.querySelectorAll('.cart-count');
                console.log('Found cart count elements on load:', cartCountElements.length);
                
                cartCountElements.forEach(element => {
                    element.textContent = cartCount;
                    console.log('Set initial cart count to:', cartCount);
                });
                
                // Update button states
                updateButtonStates(data.data.cart);
            }
        })
        .catch(error => console.error('Initial cart check error:', error));
    }
    
    // Initialize cart state on page load
    checkCartOnLoad();
});
</script>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.add-to-cart-btn.adding {
    opacity: 0.7;
    cursor: not-allowed;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Desktop Slider
    const desktopContainer = document.querySelector('.hero-slider-container');
    if (desktopContainer) {
        initSlider(desktopContainer, '.hero-slide', '.slider-dot');
    }

    // Mobile Slider
    const mobileContainer = document.querySelector('.mobile-hero-section');
    if (mobileContainer) {
        initSlider(mobileContainer, '.mobile-hero-slide', '.mobile-slider-dot');
    }

    function initSlider(container, slideSelector, dotSelector) {
        const slides = container.querySelectorAll(slideSelector);
        const dots = container.querySelectorAll(dotSelector);
        if (slides.length <= 1) return;

        let currentSlide = 1;
        const totalSlides = slides.length;
        let slideInterval;

        function showSlide(index) {
            // Normalize index
            if (index > totalSlides) index = 1;
            if (index < 1) index = totalSlides;

            // Update state
            currentSlide = index;

            // Update UI
            slides.forEach(slide => {
                const slideIndex = parseInt(slide.dataset.index);
                const content = slide.querySelector('.slide-content');
                
                if (slideIndex === currentSlide) {
                    slide.classList.remove('opacity-0', 'z-0');
                    slide.classList.add('opacity-100', 'z-10');
                    // Reset content animation
                    if (content) {
                        content.classList.remove('translate-y-10', 'opacity-0');
                        content.classList.add('translate-y-0', 'opacity-100');
                    }
                } else {
                    slide.classList.remove('opacity-100', 'z-10');
                    slide.classList.add('opacity-0', 'z-0');
                    if (content) {
                        content.classList.remove('translate-y-0', 'opacity-100');
                        content.classList.add('translate-y-10', 'opacity-0');
                    }
                }
            });

            dots.forEach(dot => {
                const dotIndex = parseInt(dot.dataset.index);
                if (dotIndex === currentSlide) {
                    dot.classList.remove('bg-white/50', 'w-2');
                    dot.classList.add('bg-white', 'w-6');
                } else {
                    dot.classList.remove('bg-white', 'w-6');
                    dot.classList.add('bg-white/50', 'w-2');
                }
            });
        }

        function nextSlide() {
            showSlide(currentSlide + 1);
        }

        function startTimer() {
            slideInterval = setInterval(nextSlide, 6000);
        }

        function stopTimer() {
            clearInterval(slideInterval);
        }

        // Initialize events
        dots.forEach(dot => {
            dot.addEventListener('click', function() {
                stopTimer();
                showSlide(parseInt(this.dataset.index));
                startTimer();
            });
        });

        // Hover pause (optional)
        container.addEventListener('mouseenter', stopTimer);
        container.addEventListener('mouseleave', startTimer);

        // Start auto-rotation
        startTimer();
    }
});
</script>

<?php get_footer(); ?>
