<?php
function warafy_enqueue_scripts() {
    // Bengali Font Support
    wp_enqueue_style('google-fonts-bengali', 'https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap', array(), null);
    
    // Modern SVG Icons (Premium design alternative to Material Symbols font)
    wp_enqueue_style('material-symbols', get_template_directory_uri() . '/assets/css/modern-svg-icons.css', array(), '1.4.0');
    
    // Tailwind CDN
    wp_enqueue_script('tailwind', 'https://cdn.tailwindcss.com?plugins=forms,container-queries', array(), null, false);
    
    // Theme Styles
    $theme_version = wp_get_theme()->get( 'Version' );
    wp_enqueue_style('warafy-style', get_stylesheet_uri(), array(), $theme_version);

    // Tailwind Config
    wp_add_inline_script('tailwind', '
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "primary": "#F5A623",
              "background-light": "#f6f7f8",
              "background-dark": "#000000",
            },
            fontFamily: {
              "display": ["Inter", "sans-serif"]
            },
            borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
          },
        },
      }
    ');

    // Enqueue product carousel script only on single product pages
    if (is_product()) {
        wp_enqueue_script('warafy-product-carousel', get_template_directory_uri() . '/assets/js/product-carousel.js', array(), '1.0.0', true);
        wp_enqueue_script('warafy-comments-reviews', get_template_directory_uri() . '/assets/js/comments-reviews.js', array(), '1.0.0', true);
        
        // Pass AJAX URL to JavaScript
        wp_localize_script('warafy-comments-reviews', 'warafy_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('warafy_ajax_nonce')
        ));
    }
    
    // Dark mode for account pages is handled by the custom template
    // (page-my-account-simple.php) which uses Tailwind dark: classes

// Add hash handler for my-account page
    if (is_page('my-account') || is_account_page()) {
        wp_add_inline_script('tailwind', '
            (function() {
                const handleHash = () => {
                    const hash = window.location.hash;
                    if (!hash) return;
                    
                    const hashValue = hash.substring(1);
                    const hashMap = {
                        "account-details": "personal-info",
                        "edit-account": "personal-info", 
                        "personal-info": "personal-info",
                        "orders": "orders",
                        "edit-address": "addresses",
                        "addresses": "addresses"
                    };

                    const view = hashMap[hashValue];
                    if (view) {
                        window.location.replace("' . home_url('/my-account') . '?view=" + view);
                    }
                };

                // Run immediately and also after DOM is ready
                handleHash();
                if (document.readyState === "loading") {
                    document.addEventListener("DOMContentLoaded", handleHash);
                } else {
                    handleHash();
                }
                window.addEventListener("hashchange", handleHash);
            })();
        ');
    }
}
add_action('wp_enqueue_scripts', 'warafy_enqueue_scripts');

function warafy_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('woocommerce');
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'adorkini'),
        'mobile' => __('Mobile Menu', 'adorkini'),
    ));
}
add_action('after_setup_theme', 'warafy_theme_setup');

// Helper function to output inline SVG icons
function warafy_get_icon_svg($icon_name, $class = 'w-6 h-6') {
    $icons = [
        'favorite' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ffffff" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>',
        'person' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
        'shopping_cart' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>',
        'category' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>',
        'bolt' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '"><polygon points="13,2 3,14 12,14 11,22 21,10 12,10"/></svg>',
        'home' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>',
        'star' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ffffff" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
        'inventory_2' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '"><path d="M21 8v13H3V8"/><path d="M1 3h22v5H1z"/><path d="M10 12h4"/></svg>',
        'shopping_bag' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>',
    ];
    
    return $icons[$icon_name] ?? $icons['category'];
}

// Custom Favicon - use logo image as favicon
function warafy_custom_favicon() {
    $favicon_url = get_template_directory_uri() . '/assets/images/favicon.jpg';
    echo '<link rel="icon" type="image/jpeg" href="' . esc_url($favicon_url) . '">' . "\n";
    echo '<link rel="shortcut icon" type="image/jpeg" href="' . esc_url($favicon_url) . '">' . "\n";
    echo '<link rel="apple-touch-icon" href="' . esc_url($favicon_url) . '">' . "\n";
}
add_action('wp_head', 'warafy_custom_favicon', 1);

// Remove default WordPress site icon to prevent conflicts
add_filter('get_site_icon_url', function() {
    return get_template_directory_uri() . '/assets/images/favicon.jpg';
}, 999);

// Register Shop Sidebar
require_once get_template_directory() . '/inc/class-warafy-session-manager.php';
require_once get_template_directory() . '/inc/homepage-settings.php';
require_once get_template_directory() . '/inc/logo-settings.php';
require_once get_template_directory() . '/inc/translation-hub.php';

// Initialize Session Manager
add_action('init', function() {
    Warafy_Session_Manager::instance();
});

// AJAX Handler for Infinite Scroll Recommendations
add_action('wp_ajax_warafy_load_recommendations', 'warafy_load_recommendations_ajax');
add_action('wp_ajax_nopriv_warafy_load_recommendations', 'warafy_load_recommendations_ajax');

function warafy_load_recommendations_ajax() {
    // Optional: check nonce if you implement one in JS
    // check_ajax_referer('warafy_recommendation_nonce', 'nonce');
    
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $per_page = 16; // Increased for smoother experience
    
    $products = Warafy_Session_Manager::instance()->get_recommended_products($page, $per_page);
    
    if (empty($products)) {
        wp_send_json_error(['message' => 'No more products']);
    }
    
    ob_start();
    foreach ($products as $post) {
        setup_postdata($post);
        global $product;
        // Ensure global product object is set for template functions
        $product = wc_get_product($post->ID);
        
        // Render Product Card (Mobile/Desktop adaptive)
        // Note: Using a simplified reliable layout that matches existing styles
        ?>
        <div class="product-card-recommendation flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-background-dark hover:shadow-lg transition-all">
            <a href="<?php echo get_permalink($product->get_id()); ?>" class="w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-lg block" style='background-image: url("<?php echo get_the_post_thumbnail_url($product->get_id(), 'woocommerce_thumbnail'); ?>");'></a>
            <div class="flex flex-col flex-1 justify-between gap-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">
                        <a href="<?php echo get_permalink($product->get_id()); ?>" class="hover:text-primary transition-colors line-clamp-1"><?php echo get_the_title($product->get_id()); ?></a>
                    </h3>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo $product->get_price_html(); ?></p>
                </div>
                <!-- Action Buttons: Add to Cart & Wishlist -->
                <div class="flex gap-2">
                    <button class="add-to-cart-btn flex-1 flex items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary/10 text-primary text-sm font-bold hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 transition-colors" data-product-id="<?php echo $product->get_id(); ?>">
                        <span class="material-symbols-outlined text-sm add-icon mr-2" data-icon="add_shopping_cart"></span>
                        <span class="add-text truncate"><?php echo __t('Add'); ?></span>
                        <span class="material-symbols-outlined text-sm added-icon hidden mr-2" data-icon="check"></span>
                        <span class="added-text hidden truncate"><?php echo __t('Added'); ?></span>
                    </button>
                    <button class="warafy-wishlist-btn flex-none w-10 h-10 flex items-center justify-center rounded-lg bg-primary/10 text-primary hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 transition-colors" data-product-id="<?php echo $product->get_id(); ?>">
                         <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
    wp_reset_postdata();
    $html = ob_get_clean();
    
    wp_send_json_success(['html' => $html]);
}

// AJAX Handler for Infinite Scroll Related Products
add_action('wp_ajax_warafy_load_related_products', 'warafy_load_related_products_ajax');
add_action('wp_ajax_nopriv_warafy_load_related_products', 'warafy_load_related_products_ajax');

function warafy_load_related_products_ajax() {
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $per_page = 8;
    
    if (!$product_id) {
        wp_send_json_error(['message' => 'Invalid Product ID']);
    }

    // Get current product's categories
    $terms = wp_get_post_terms($product_id, 'product_cat', ['fields' => 'ids']);
    if (empty($terms) || is_wp_error($terms)) {
        wp_send_json_error(['message' => 'No related categories']);
    }

    // Use session manager to consistently randomize products
    // We'll use a specific seed for related products so it's consistent for this user session
    $session_id = Warafy_Session_Manager::instance()->get_homepage_data()['recommended_ids'] ? md5(json_encode(Warafy_Session_Manager::instance()->get_homepage_data()['recommended_ids'])) : 'default_seed';
    
    // Get all related products IDs first (cached query)
    $transient_key = 'warafy_related_' . $product_id . '_' . md5(json_encode($terms));
    $related_ids = get_transient($transient_key);

    if (false === $related_ids) {
        $related_ids = get_posts([
            'post_type' => 'product',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'post_status' => 'publish',
            'tax_query' => [[
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $terms,
            ]],
            'post__not_in' => [$product_id], // Exclude current product
        ]);
        
        if (!empty($related_ids)) {
            // Shuffle using session seed to keep order consistent for user
            srand(crc32($session_id));
            shuffle($related_ids);
            // Reset random seed
            srand();
        }
        
        set_transient($transient_key, $related_ids, 3600); // Cache for 1 hour
    }
    
    if (empty($related_ids)) {
         wp_send_json_error(['message' => 'No related products']);
    }

    // Pagination
    $offset = ($page - 1) * $per_page;
    $total_products = count($related_ids);
    
    if ($offset >= $total_products) {
         wp_send_json_error(['message' => 'No more products']);
    }
    
    $current_page_ids = array_slice($related_ids, $offset, $per_page);
    
    ob_start();
    foreach ($current_page_ids as $post_id) {
        $product = wc_get_product($post_id);
        if (!$product) continue;
        
        // Render Product Card (Consistent with homepage recommendations)
        ?>
        <div class="product-card-recommendation flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-background-dark hover:shadow-lg transition-all">
            <a href="<?php echo get_permalink($product->get_id()); ?>" class="w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-lg block" style='background-image: url("<?php echo get_the_post_thumbnail_url($product->get_id(), 'woocommerce_thumbnail'); ?>");'></a>
            <div class="flex flex-col flex-1 justify-between gap-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">
                        <a href="<?php echo get_permalink($product->get_id()); ?>" class="hover:text-primary transition-colors line-clamp-1"><?php echo get_the_title($product->get_id()); ?></a>
                    </h3>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo $product->get_price_html(); ?></p>
                </div>
                <!-- Action Buttons: Add to Cart & Wishlist -->
                <div class="flex gap-2">
                    <button class="add-to-cart-btn flex-1 flex items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary/10 text-primary text-sm font-bold hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 transition-colors" data-product-id="<?php echo $product->get_id(); ?>">
                        <span class="material-symbols-outlined text-sm add-icon mr-2" data-icon="add_shopping_cart"></span>
                        <span class="add-text truncate"><?php echo __t('Add'); ?></span>
                        <span class="material-symbols-outlined text-sm added-icon hidden mr-2" data-icon="check"></span>
                        <span class="added-text hidden truncate"><?php echo __t('Added'); ?></span>
                    </button>
                    <button class="warafy-wishlist-btn flex-none w-10 h-10 flex items-center justify-center rounded-lg bg-primary/10 text-primary hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 transition-colors" data-product-id="<?php echo $product->get_id(); ?>">
                         <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
    $html = ob_get_clean();
    
    wp_send_json_success(['html' => $html]);
}

function warafy_widgets_init() {
    register_sidebar( array(
        'name'          => esc_html__( 'Shop Sidebar', 'warafy-modern' ),
        'id'            => 'shop-sidebar',
        'description'   => esc_html__( 'Add widgets here to appear in your shop page sidebar.', 'warafy-modern' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s mb-8">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'warafy_widgets_init' );

// Set default sort order to Latest
function warafy_default_catalog_orderby( $sort_by ) {
    return 'date';
}
add_filter( 'woocommerce_default_catalog_orderby', 'warafy_default_catalog_orderby' );

// Ensure the query actually uses date sorting if no other sort is specified
function warafy_pre_get_posts_product_order( $query ) {
    if ( ! is_admin() && $query->is_main_query() && ( is_shop() || is_product_category() || is_product_tag() ) ) {
        // Only modify if no ordering is set in URL
        if ( ! isset( $_GET['orderby'] ) ) {
            $query->set( 'orderby', 'date' );
            $query->set( 'order', 'DESC' );
        }
    }
}
add_action( 'pre_get_posts', 'warafy_pre_get_posts_product_order' );

// Disable redirect to product page if only one result found in search
add_filter( 'woocommerce_redirect_single_search_result', '__return_false' );


add_filter( 'woocommerce_checkout_fields' , 'warafy_custom_checkout_fields' );

function warafy_custom_checkout_fields( $fields ) {
    // Keep only the fields we need
    $allowed_fields = array(
        'billing_first_name',  // Name (required)
        'billing_address_1',   // Address (required)
        'billing_phone',        // Mobile (required)
        'billing_email',        // Email (optional)
        'order_comments'        // Instructions (optional)
    );
    
    // Remove all billing fields except the ones we want
    foreach ($fields['billing'] as $key => $field) {
        if (!in_array($key, $allowed_fields)) {
            unset($fields['billing'][$key]);
        }
    }
    
    // Remove all shipping fields (not needed)
    $fields['shipping'] = array();
    
    // Configure the remaining fields
    // Name (required)
    $fields['billing']['billing_first_name']['label'] = 'Name';
    $fields['billing']['billing_first_name']['required'] = true;
    $fields['billing']['billing_first_name']['class'] = array('form-row-wide');
    $fields['billing']['billing_first_name']['placeholder'] = 'Enter your full name';
    
    // Address (required)
    $fields['billing']['billing_address_1']['label'] = 'Address';
    $fields['billing']['billing_address_1']['required'] = true;
    $fields['billing']['billing_address_1']['class'] = array('form-row-wide');
    $fields['billing']['billing_address_1']['placeholder'] = 'Street address, apartment, suite, etc.';
    
    // Mobile Number (required)
    $fields['billing']['billing_phone']['label'] = 'Mobile Number';
    $fields['billing']['billing_phone']['required'] = true;
    $fields['billing']['billing_phone']['class'] = array('form-row-wide');
    $fields['billing']['billing_phone']['placeholder'] = 'Enter your mobile number';
    
    // Email (optional)
    $fields['billing']['billing_email']['label'] = 'Email Address';
    $fields['billing']['billing_email']['required'] = false;
    $fields['billing']['billing_email']['class'] = array('form-row-wide');
    $fields['billing']['billing_email']['placeholder'] = 'Enter your email (optional)';
    
    // Order Instructions (optional)
    $fields['order']['order_comments']['label'] = 'Order Instructions';
    $fields['order']['order_comments']['required'] = false;
    $fields['order']['order_comments']['placeholder'] = 'Any special instructions for your order...';

    return $fields;
}

add_filter('woocommerce_thankyou_order_received_text', 'warafy_custom_order_received_text', 10, 2);

function warafy_custom_order_received_text($text, $order) {
    if ($order) {
        return 'Your order has been received! Thank you for your purchase.';
    }
    return $text;
}

// Allow guests to view order details without login
add_filter('woocommerce_order_details_allow_guest_access', '__return_true');

// Remove login requirement for order viewing
add_filter('woocommerce_is_checkout', '__return_false');

// Generate unique 5-digit order number
add_action('woocommerce_new_order', 'warafy_generate_custom_order_number', 10, 1);
function warafy_generate_custom_order_number($order_id) {
    $order = wc_get_order($order_id);
    if (!$order) return;
    
    // Check if custom order number already exists
    $existing_number = $order->get_meta('_warafy_order_number');
    if ($existing_number) return;
    
    // Generate unique 5-digit number
    $custom_number = warafy_generate_unique_order_number();
    
    // Save to order meta
    $order->update_meta_data('_warafy_order_number', $custom_number);
    $order->save();
}

function warafy_generate_unique_order_number() {
    do {
        $number = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $exists = warafy_order_number_exists($number);
    } while ($exists);
    
    return $number;
}

function warafy_order_number_exists($number) {
    global $wpdb;
    
    $count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->postmeta} 
         WHERE meta_key = '_warafy_order_number' 
         AND meta_value = %s",
        $number
    ));
    
    return $count > 0;
}

// Get order by custom number
function warafy_get_order_by_custom_number($custom_number) {
    global $wpdb;
    
    $post_id = $wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} 
         WHERE meta_key = '_warafy_order_number' 
         AND meta_value = %s 
         LIMIT 1",
        $custom_number
    ));
    
    return $post_id ? wc_get_order($post_id) : null;
}

// Update rewrite rules to use custom order numbers
add_action('init', 'warafy_add_custom_order_details_rewrite_rule');
function warafy_add_custom_order_details_rewrite_rule() {
    add_rewrite_rule(
        '^order-details/([0-9]{5})/?$',
        'index.php?pagename=order-details&custom_order_number=$matches[1]',
        'top'
    );
    
    // Keep the original rules for backward compatibility
    add_rewrite_rule(
        '^order-details/([0-9]+)/?$',
        'index.php?pagename=order-details&order_id=$matches[1]',
        'top'
    );
    
    add_rewrite_rule(
        '^order-details/([^/]+)/?$',
        'index.php?pagename=order-details&order_key=$matches[1]',
        'top'
    );
}

// Add custom query variable
add_filter('query_vars', 'warafy_add_custom_order_query_vars');
function warafy_add_custom_order_query_vars($vars) {
    $vars[] = 'custom_order_number';
    $vars[] = 'order_id';
    $vars[] = 'order_key';
    return $vars;
}

// Update the public order retrieval function
function warafy_get_public_order($order_id = null, $order_key = null, $custom_number = null) {
    if ($custom_number) {
        // Try to get order by custom number first
        $order = warafy_get_order_by_custom_number($custom_number);
        return $order;
    } elseif ($order_id) {
        $order = wc_get_order($order_id);
    } elseif ($order_key) {
        $orders = wc_get_orders([
            'limit' => 1,
            'meta_key' => '_order_key',
            'meta_value' => $order_key,
        ]);
        $order = !empty($orders) ? $orders[0] : null;
    } else {
        $order = null;
    }
    
    return $order;
}

// Display custom order number on thank you page and order details
add_filter('woocommerce_order_number', 'warafy_display_custom_order_number', 10, 2);
function warafy_display_custom_order_number($order_number, $order) {
    $custom_number = $order->get_meta('_warafy_order_number');
    if ($custom_number) {
        return '#' . $custom_number;
    }
    return $order_number;
}

// Update the public order link generation
add_action('woocommerce_thankyou', 'warafy_add_public_order_link', 10, 1);
function warafy_add_public_order_link($order_id) {
    $order = wc_get_order($order_id);
    if ($order) {
        $custom_number = $order->get_meta('_warafy_order_number');
        if ($custom_number) {
            $public_url = home_url("/order-details/{$custom_number}/");
        } else {
            // Fallback to order key
            $order_key = $order->get_order_key();
            $public_url = home_url("/order-details/{$order_key}/");
        }
        
        echo '<div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/30 rounded-xl">';
        echo '<p class="text-sm text-blue-800 dark:text-blue-200 mb-2">Share this link to view order details:</p>';
        echo '<div class="flex items-center gap-2">';
        echo '<input type="text" value="' . esc_url($public_url) . '" readonly class="flex-1 px-3 py-2 bg-white dark:bg-gray-800 border border-blue-200 dark:border-blue-700 rounded-lg text-sm">';
        echo '<button onclick="navigator.clipboard.writeText(\'' . esc_js($public_url) . '\').then(() => { this.textContent = \'Copied!\'; setTimeout(() => { this.textContent = \'Copy\'; }, 2000); })" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">Copy</button>';
        echo '</div>';
        echo '</div>';
    }
}

// Privacy masking functions
function warafy_mask_name($name) {
    if (strlen($name) <= 2) {
        return $name;
    }
    $first = substr($name, 0, 1);
    $last = substr($name, -1);
    $middle = str_repeat('*', strlen($name) - 2);
    return $first . $middle . $last;
}

function warafy_mask_phone($phone) {
    // Remove non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) <= 4) {
        return $phone;
    }
    $last_four = substr($phone, -4);
    $masked = str_repeat('*', strlen($phone) - 4) . $last_four;
    return $masked;
}

function warafy_mask_email($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $email;
    }
    
    list($local, $domain) = explode('@', $email);
    $local_length = strlen($local);
    
    if ($local_length <= 2) {
        return $email;
    }
    
    $first_char = substr($local, 0, 1);
    $last_char = substr($local, -1);
    $masked_local = $first_char . str_repeat('*', $local_length - 2) . $last_char;
    
    return $masked_local . '@' . $domain;
}

function warafy_mask_address($address) {
    $words = explode(' ', trim($address));
    if (count($words) <= 1) {
        return warafy_mask_name($address);
    }
    return end($words);
}

function warafy_default_cash_on_delivery_gateway( $default_gateway ) {
    // Check if Cash on Delivery is an available gateway
    $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
    if ( array_key_exists( 'cod', $available_gateways ) ) {
        return 'cod';
    } else {
        // If COD is not available, return the first available gateway
        return key( $available_gateways );
    }
}

// AJAX Handler for getting cart data for button state updates
add_action('wp_ajax_warafy_get_cart', 'warafy_get_cart');
add_action('wp_ajax_nopriv_warafy_get_cart', 'warafy_get_cart');

function warafy_get_cart() {
    // Set content type
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        if (function_exists('WC') && WC()->cart) {
            $cart = WC()->cart;
            $cart_items = [];
            
            if ($cart->get_cart()) {
                foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
                    $cart_items[] = [
                        'product_id' => $cart_item['product_id'],
                        'quantity' => $cart_item['quantity'],
                        'variation_id' => $cart_item['variation_id'] ?? 0,
                    ];
                }
            }
            
            wp_send_json_success([
                'cart' => [
                    'items' => $cart_items,
                    'count' => $cart->get_cart_contents_count(),
                    'total' => $cart->get_total(),
                ]
            ]);
        } else {
            wp_send_json_error(['message' => 'WooCommerce not available']);
        }
    } catch (Exception $e) {
        wp_send_json_error(['message' => 'Error: ' . $e->getMessage()]);
    }
}

// AJAX Handler for refreshing cart fragments
add_action('wp_ajax_warafy_refresh_fragments', 'warafy_refresh_fragments');
add_action('wp_ajax_nopriv_warafy_refresh_fragments', 'warafy_refresh_fragments');

function warafy_refresh_fragments() {
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        if (function_exists('WC') && WC()->cart) {
            $cart = WC()->cart;
            
            // Simple cart count fragment
            $count = $cart->get_cart_contents_count();
            $fragments = [];
            
            // Create simple HTML fragments for common selectors
            $fragments['.warafy-cart-qty'] = '<span class="warafy-cart-qty">' . $count . '</span>';
            $fragments['.cart-counter'] = '<span class="cart-counter">' . $count . '</span>';
            
            wp_send_json_success([
                'fragments' => $fragments,
                'count' => $count
            ]);
        } else {
            wp_send_json_error(['message' => 'WooCommerce not available']);
        }
    } catch (Exception $e) {
        wp_send_json_error(['message' => 'Error: ' . $e->getMessage()]);
    }
}

/* Bengali Language Support */

// Add Bengali fields to product admin
add_action('woocommerce_product_options_general_product_data', 'warafy_add_bengali_product_fields');

function warafy_add_bengali_product_fields() {
    echo '<div class="options_group">';
    woocommerce_wp_text_input(array(
        'id' => '_bengali_title',
        'label' => __('Bengali Title', 'warafy-modern'),
        'placeholder' => __('Enter Bengali product title', 'warafy-modern'),
        'desc_tip' => true,
        'description' => __('Optional: Bengali translation of product title', 'warafy-modern'),
        'type' => 'text',
    ));
    echo '</div>';
    
    echo '<div class="options_group">';
    echo '<h4>' . __('Bengali Description', 'warafy-modern') . '</h4>';
    echo '<p class="description">' . __('Optional: Bengali translation of product description', 'warafy-modern') . '</p>';
    
    // Get current Bengali description
    $bengali_description = get_post_meta(get_the_ID(), '_bengali_description', true);
    
    // Add WordPress editor for Bengali description
    wp_editor(
        $bengali_description,
        '_bengali_description',
        array(
            'textarea_name' => '_bengali_description',
            'textarea_rows' => 10,
            'media_buttons' => true,
            'teeny' => false,
            'quicktags' => true,
            'editor_css' => '<style>#wp-_bengali_description-editor-container { border: 1px solid #ddd; margin-top: 10px; }</style>',
        )
    );
    echo '</div>';
    
    echo '<div class="options_group">';
    echo '<h4>' . __('Bengali Short Description', 'warafy-modern') . '</h4>';
    echo '<p class="description">' . __('Optional: Bengali translation of product short description', 'warafy-modern') . '</p>';
    
    // Get current Bengali short description
    $bengali_short_description = get_post_meta(get_the_ID(), '_bengali_short_description', true);
    
    // Add WordPress editor for Bengali short description
    wp_editor(
        $bengali_short_description,
        '_bengali_short_description',
        array(
            'textarea_name' => '_bengali_short_description',
            'textarea_rows' => 8,
            'media_buttons' => true,
            'teeny' => false,
            'quicktags' => true,
            'editor_css' => '<style>#wp-_bengali_short_description-editor-container { border: 1px solid #ddd; margin-top: 10px; }</style>',
        )
    );
    echo '</div>';
}

// Save Bengali product fields
add_action('woocommerce_process_product_meta', 'warafy_save_bengali_product_fields');

function warafy_save_bengali_product_fields($post_id) {
    $bengali_title = isset($_POST['_bengali_title']) ? sanitize_textarea_field($_POST['_bengali_title']) : '';
    $bengali_description = isset($_POST['_bengali_description']) ? wp_kses_post($_POST['_bengali_description']) : '';
    $bengali_short_description = isset($_POST['_bengali_short_description']) ? wp_kses_post($_POST['_bengali_short_description']) : '';
    
    update_post_meta($post_id, '_bengali_title', $bengali_title);
    update_post_meta($post_id, '_bengali_description', $bengali_description);
    update_post_meta($post_id, '_bengali_short_description', $bengali_short_description);
}

// Add Bengali fields to product category admin
add_action('product_cat_add_form_fields', 'warafy_add_bengali_category_fields', 10, 2);
add_action('product_cat_edit_form_fields', 'warafy_add_bengali_category_fields', 10, 2);

function warafy_add_bengali_category_fields($term, $taxonomy = null) {
    // Handle both add and edit forms
    $term_id = isset($term->term_id) ? $term->term_id : 0;
    $bengali_name = $term_id ? get_term_meta($term_id, '_bengali_name', true) : '';
    $bengali_description = $term_id ? get_term_meta($term_id, '_bengali_description', true) : '';
    
    ?>
    <div class="form-field term-bengali-name-wrap">
        <label for="bengali_name"><?php _e('Bengali Name', 'warafy-modern'); ?></label>
        <input type="text" id="bengali_name" name="bengali_name" value="<?php echo esc_attr($bengali_name); ?>" placeholder="<?php _e('Enter Bengali category name', 'warafy-modern'); ?>">
        <p class="description"><?php _e('Optional: Bengali translation of category name', 'warafy-modern'); ?></p>
    </div>
    
    <div class="form-field term-bengali-description-wrap">
        <label for="bengali_description"><?php _e('Bengali Description', 'warafy-modern'); ?></label>
        <textarea id="bengali_description" name="bengali_description" rows="5" placeholder="<?php _e('Enter Bengali category description', 'warafy-modern'); ?>"><?php echo esc_textarea($bengali_description); ?></textarea>
        <p class="description"><?php _e('Optional: Bengali translation of category description', 'warafy-modern'); ?></p>
    </div>
    <?php
}

// Save Bengali category fields
add_action('created_product_cat', 'warafy_save_bengali_category_fields');
add_action('edited_product_cat', 'warafy_save_bengali_category_fields');

function warafy_save_bengali_category_fields($term_id) {
    if (isset($_POST['bengali_name'])) {
        update_term_meta($term_id, '_bengali_name', sanitize_text_field($_POST['bengali_name']));
    }
    if (isset($_POST['bengali_description'])) {
        update_term_meta($term_id, '_bengali_description', sanitize_textarea_field($_POST['bengali_description']));
    }
}

// Helper function to get Bengali content with fallback
function warafy_get_bengali_content($post_id, $field, $default = '') {
    $bengali_value = get_post_meta($post_id, '_bengali_' . $field, true);
    return !empty($bengali_value) ? $bengali_value : $default;
}

// Helper function to get Bengali category content with fallback
function warafy_get_bengali_category_content($term_id, $field, $default = '') {
    $bengali_value = get_term_meta($term_id, '_bengali_' . $field, true);
    return !empty($bengali_value) ? $bengali_value : $default;
}

// Language switching functionality
add_action('wp_head', 'warafy_language_switcher_script');

function warafy_language_switcher_script() {
    ?>
    <script>
    // Language preference management
    function warafy_setLanguage(lang) {
        // Set localStorage for JS
        localStorage.setItem('warafy_language', lang);
        
        // Set cookie for PHP (valid for 1 year)
        document.cookie = "warafy_language=" + lang + "; path=/; max-age=" + (60*60*24*365);
        
        // Reload page to apply PHP translations
        window.location.reload();
    }
    
    function warafy_getLanguage() {
        // Try to get from localStorage, fallback to cookie, then 'en'
        let lang = localStorage.getItem('warafy_language');
        if (!lang) {
            const match = document.cookie.match(new RegExp('(^| )warafy_language=([^;]+)'));
            lang = match ? match[2] : 'en';
        }
        return lang;
    }
    
    function warafy_updateLanguage(lang) {
        // Update language toggle button text
        document.querySelectorAll('.warafy-language-toggle .lang-text').forEach(element => {
            element.textContent = lang === 'bn' ? 'বাং<>En' : 'En<>বাং';
        });
        
        // Update all translatable elements
        document.querySelectorAll('.warafy-translatable').forEach(element => {
            const bengaliText = element.getAttribute('data-bn');
            const englishText = element.getAttribute('data-en');
            
            if (lang === 'bn' && bengaliText) {
                element.innerHTML = bengaliText;
            } else if (lang === 'en' && englishText) {
                element.innerHTML = englishText;
            }
        });
        
        // Update category names and descriptions via AJAX
        if (lang === 'bn') {
            // Get all category elements
            document.querySelectorAll('.category-title, .woocommerce-loop-category__title, .term-name').forEach(element => {
                const categoryUrl = element.closest('a')?.href;
                if (categoryUrl && categoryUrl.includes('product-category')) {
                    // Extract category slug from URL
                    const slug = categoryUrl.split('/').filter(Boolean).pop();
                    if (slug) {
                        // Fetch Bengali category name
                        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'action=warafy_get_bengali_category&category_slug=' + slug + '&nonce=<?php echo wp_create_nonce('warafy_bengali_nonce'); ?>'
                        }).then(response => response.json()).then(data => {
                            if (data.success && data.data.bengali_name) {
                                element.setAttribute('data-en', element.textContent);
                                element.setAttribute('data-bn', data.data.bengali_name);
                                element.textContent = data.data.bengali_name;
                            }
                        });
                    }
                }
            });
        } else {
            // Restore English category names
            document.querySelectorAll('[data-en].category-title, [data-en].woocommerce-loop-category__title, [data-en].term-name').forEach(element => {
                const englishText = element.getAttribute('data-en');
                if (englishText) {
                    element.textContent = englishText;
                }
            });
        }
        
        // Update body class for styling
        document.body.classList.toggle('bengali-mode', lang === 'bn');
        
        // Dispatch custom event for other scripts
        window.dispatchEvent(new CustomEvent('languageChanged', { detail: { language: lang } }));
    }
    
    // Initialize language on page load
    document.addEventListener('DOMContentLoaded', function() {
        const currentLang = warafy_getLanguage();
        warafy_updateLanguage(currentLang);
        
        // Add click handlers
        document.querySelectorAll('.warafy-language-toggle').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const lang = warafy_getLanguage();
                warafy_setLanguage(lang === 'en' ? 'bn' : 'en');
            });
        });
    });
    </script>
    <?php
}

// AJAX handler for Bengali content
add_action('wp_ajax_warafy_get_bengali_content', 'warafy_get_bengali_content_ajax');
add_action('wp_ajax_nopriv_warafy_get_bengali_content', 'warafy_get_bengali_content_ajax');

// AJAX handler for Bengali category data
add_action('wp_ajax_warafy_get_bengali_category', 'warafy_get_bengali_category_ajax');
add_action('wp_ajax_nopriv_warafy_get_bengali_category', 'warafy_get_bengali_category_ajax');

function warafy_get_bengali_category_ajax() {
    if (!wp_verify_nonce($_POST['nonce'], 'warafy_bengali_nonce')) {
        wp_send_json_error('Invalid nonce');
    }
    
    $category_slug = sanitize_text_field($_POST['category_slug']);
    $term = get_term_by('slug', $category_slug, 'product_cat');
    
    if ($term && !is_wp_error($term)) {
        $bengali_name = get_term_meta($term->term_id, '_bengali_name', true);
        $bengali_description = get_term_meta($term->term_id, '_bengali_description', true);
        
        wp_send_json_success([
            'bengali_name' => $bengali_name,
            'bengali_description' => $bengali_description
        ]);
    } else {
        wp_send_json_error('Category not found');
    }
}

function warafy_get_bengali_content_ajax() {
    if (!wp_verify_nonce($_POST['nonce'], 'warafy_bengali_nonce')) {
        wp_send_json_error('Invalid nonce');
    }
    
    $product_id = intval($_POST['product_id']);
    $content_type = sanitize_text_field($_POST['content_type']);
    
    $response = [];
    
    switch ($content_type) {
        case 'title':
            $response['bengali_title'] = get_post_meta($product_id, '_bengali_title', true);
            break;
        case 'description':
            $response['bengali_description'] = get_post_meta($product_id, '_bengali_description', true);
            break;
        case 'short_description':
            $response['bengali_short_description'] = get_post_meta($product_id, '_bengali_short_description', true);
            break;
    }
    
    wp_send_json_success($response);
}

// Filter product title for Bengali display
add_filter('the_title', 'warafy_filter_product_title', 10, 2);

function warafy_filter_product_title($title, $id = null) {
    // Don't filter in admin
    if (is_admin()) {
        return $title;
    }
    
    // Check if this is a product and we're in the right context
    if ($id && get_post_type($id) === 'product') {
        $bengali_title = get_post_meta($id, '_bengali_title', true);
        if (!empty($bengali_title)) {
            // Store original title as data attribute for language switching
            return '<span class="warafy-translatable" data-en="' . esc_attr($title) . '" data-bn="' . esc_attr($bengali_title) . '">' . $title . '</span>';
        }
    }
    
    return $title;
}

// Filter product content for Bengali display
add_filter('woocommerce_product_short_description', 'warafy_filter_product_short_description');
add_filter('the_content', 'warafy_filter_product_content');

function warafy_filter_product_short_description($content) {
    if (!is_admin() && function_exists('get_the_ID') && get_the_ID()) {
        $post_id = get_the_ID();
        $bengali_content = get_post_meta($post_id, '_bengali_short_description', true);
        if (!empty($bengali_content)) {
            return '<div class="warafy-translatable" data-en="' . esc_attr($content) . '" data-bn="' . esc_attr($bengali_content) . '">' . $content . '</div>';
        }
    }
    return $content;
}

function warafy_filter_product_content($content) {
    if (!is_admin() && function_exists('get_the_ID') && get_the_ID() && function_exists('get_post_type') && get_post_type(get_the_ID()) === 'product') {
        $post_id = get_the_ID();
        $bengali_content = get_post_meta($post_id, '_bengali_description', true);
        if (!empty($bengali_content)) {
            return '<div class="warafy-translatable" data-en="' . esc_attr($content) . '" data-bn="' . esc_attr($bengali_content) . '">' . $content . '</div>';
        }
    }
    return $content;
}

// Filter category name and description for Bengali display
// Disabled - using JavaScript approach instead for better compatibility
// add_filter('get_term', 'warafy_filter_term', 10, 3);

function warafy_filter_term($term, $taxonomy, $raw_term) {
    // Only run on front-end and for valid terms
    if (is_admin() || empty($term) || !is_object($term) || !isset($term->term_id)) {
        return $term;
    }
    
    // Only process product categories
    if ($taxonomy !== 'product_cat') {
        return $term;
    }
    
    // Get current language from localStorage via JavaScript (we'll handle this differently)
    // For now, let's not filter in admin context at all
    if (is_admin()) {
        return $term;
    }
    
    return $term;
}

/* Email Verification Functionality */

// Handle email verification
add_action('init', 'warafy_handle_email_verification');
function warafy_handle_email_verification() {
    if (isset($_GET['token']) && isset($_GET['user_id'])) {
        $token = sanitize_text_field($_GET['token']);
        $user_id = intval($_GET['user_id']);
        
        $stored_token = get_user_meta($user_id, 'email_verification_token', true);
        
        if ($token === $stored_token) {
            // Verify email
            update_user_meta($user_id, 'email_verified', true);
            delete_user_meta($user_id, 'email_verification_token');
            
            // Redirect to login with success message
            wp_redirect(home_url('/login?verified=success'));
            exit;
        } else {
            // Invalid token
            wp_redirect(home_url('/login?verification=failed'));
            exit;
        }
    }
}

/**
 * Get user by phone number
 * @param string $phone Phone number to search for
 * @return WP_User|false User object or false if not found
 */
function warafy_get_user_by_phone($phone) {
    // Normalize phone number - remove non-numeric characters
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    
    // Search in billing_phone meta
    $users = get_users(array(
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => 'billing_phone',
                'value' => $phone,
                'compare' => '='
            ),
            array(
                'key' => 'phone_number',
                'value' => $phone,
                'compare' => '='
            )
        ),
        'number' => 1
    ));
    
    if (!empty($users)) {
        return $users[0];
    }
    
    // Also try with different phone formats
    $phone_variants = array(
        $phone,
        '+' . ltrim($phone, '+'),
        ltrim($phone, '+'),
        ltrim($phone, '0'),
        '0' . ltrim($phone, '0')
    );
    
    foreach ($phone_variants as $variant) {
        $users = get_users(array(
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => 'billing_phone',
                    'value' => $variant,
                    'compare' => '='
                ),
                array(
                    'key' => 'phone_number',
                    'value' => $variant,
                    'compare' => '='
                )
            ),
            'number' => 1
        ));
        
        if (!empty($users)) {
            return $users[0];
        }
    }
    
    return false;
}

// Check if user email is verified
function warafy_is_user_email_verified($user_id = null) {
    if ($user_id === null) {
        $user_id = get_current_user_id();
    }
    return get_user_meta($user_id, 'email_verified', true) === '1';
}

// Email verification notice removed - now only shown in profile section
function warafy_email_verification_notice() {
    if (is_user_logged_in() && !warafy_is_user_email_verified()) {
        ?>
        <style>
        .email-verification-notice {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            background: #fef3c7;
            border-bottom: 1px solid #fbbf24;
            padding: 12px;
            text-align: center;
        }
        .email-verification-notice.dark {
            background: #92400e;
            border-bottom-color: #d97706;
        }
        </style>
        <div class="email-verification-notice" id="email-verification-notice">
            <div class="container mx-auto px-4">
                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                    <strong>Please verify your email address.</strong> Check your inbox for the verification link.
                    <button onclick="resendVerificationEmail()" class="ml-2 text-yellow-800 underline dark:text-yellow-200">Resend email</button>
                </p>
                <button onclick="document.getElementById('email-verification-notice').style.display='none'" class="absolute top-2 right-2 text-yellow-800 dark:text-yellow-200">
                    <span class="material-symbols-outlined text-base" data-icon="close"></span>
                </button>
            </div>
        </div>
        <script>
        function resendVerificationEmail() {
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=warafy_resend_verification_email&nonce=<?php echo wp_create_nonce('warafy_resend_verification'); ?>'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Verification email sent! Please check your inbox.');
                } else {
                    alert('Error: ' + data.data);
                }
            });
        }
        </script>
        <?php
    }
}

// AJAX handler for resending verification email
add_action('wp_ajax_warafy_resend_verification_email', 'warafy_resend_verification_email');
add_action('wp_ajax_nopriv_warafy_resend_verification_email', 'warafy_resend_verification_email');

function warafy_resend_verification_email() {
    if (!wp_verify_nonce($_POST['nonce'], 'warafy_resend_verification')) {
        wp_send_json_error('Invalid nonce');
    }
    
    if (!is_user_logged_in()) {
        wp_send_json_error('User not logged in');
    }
    
    $user_id = get_current_user_id();
    $user = wp_get_current_user();
    
    // Generate new verification token
    $verification_token = wp_generate_password(32, false);
    update_user_meta($user_id, 'email_verification_token', $verification_token);
    
    // Send verification email
    $verification_link = home_url("/verify-email?token={$verification_token}&user_id={$user_id}");
    $subject = 'Verify your email address';
    $message = "Hi {$user->first_name},\n\n";
    $message .= "Please click the link below to verify your email address:\n\n";
    $message .= $verification_link . "\n\n";
    $message .= "If you didn't request this verification, please ignore this email.\n\n";
    $message .= "Best regards,\nWarafy Team";
    
    $sent = wp_mail($user->user_email, $subject, $message);
    
    if ($sent) {
        wp_send_json_success('Verification email sent successfully');
    } else {
        wp_send_json_error('Failed to send verification email');
    }
}

/* Wishlist Functionality */
function warafy_wishlist_scripts() {
    ?>
    <script>
    // Make wishlist functions globally accessible
    window.getWishlist = function() {
        const wishlist = localStorage.getItem('warafy_wishlist');
        return wishlist ? JSON.parse(wishlist) : [];
    };

    window.updateWishlistCount = function() {
        const wishlist = window.getWishlist();
        const count = wishlist.length;
        document.querySelectorAll('.warafy-wishlist-count').forEach(el => {
            el.textContent = count;
            el.style.display = count > 0 ? 'flex' : 'none';
        });
        
        // Update button states on page load
        document.querySelectorAll('.warafy-wishlist-btn').forEach(btn => {
            if (wishlist.includes(btn.dataset.productId)) {
                btn.classList.add('active');
                btn.classList.add('bg-green-500', 'text-white', 'border-green-500');
                btn.classList.remove('border-gray-300', 'text-gray-600');
                
                const btnText = btn.querySelector('.btn-text');
                if(btnText) {
                    btnText.textContent = 'Loved!';
                }
                
                if(btn.querySelector('.material-symbols-outlined')) {
                    btn.querySelector('.material-symbols-outlined').dataset.icon = 'favorite';
                    btn.querySelector('.material-symbols-outlined').classList.add('text-white');
                }
            }
        });
    };

    window.toggleWishlist = function(productId, btn) {
        let wishlist = window.getWishlist();
        const index = wishlist.indexOf(productId);
        
        if (index > -1) {
            // Remove from wishlist
            wishlist.splice(index, 1);
            btn.classList.remove('active');
            btn.classList.remove('bg-green-500', 'text-white', 'border-green-500');
            btn.classList.add('border-gray-300', 'text-gray-600');
            
            const btnText = btn.querySelector('.btn-text');
            if(btnText) {
                btnText.textContent = 'Loved it? Add to love.';
            }
            
            if(btn.querySelector('.material-symbols-outlined')) {
                btn.querySelector('.material-symbols-outlined').dataset.icon = 'favorite_border';
                btn.querySelector('.material-symbols-outlined').classList.remove('text-white');
            }
        } else {
            // Add to wishlist
            wishlist.push(productId);
            btn.classList.add('active');
            btn.classList.add('bg-green-500', 'text-white', 'border-green-500');
            btn.classList.remove('border-gray-300', 'text-gray-600');
            
            const btnText = btn.querySelector('.btn-text');
            if(btnText) {
                btnText.textContent = 'Loved!';
            }
            
            if(btn.querySelector('.material-symbols-outlined')) {
                btn.querySelector('.material-symbols-outlined').dataset.icon = 'favorite';
                btn.querySelector('.material-symbols-outlined').classList.add('text-white');
            }
        }
        
        localStorage.setItem('warafy_wishlist', JSON.stringify(wishlist));
        window.updateWishlistCount();
        
        // If on wishlist page, reload to update list
        if (document.body.classList.contains('page-template-page-my-love') || window.location.pathname.includes('/my-love')) {
            location.reload();
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        window.updateWishlistCount();
        
        // Handle Add to Wishlist Click
        document.body.addEventListener('click', function(e) {
            if (e.target.closest('.warafy-wishlist-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.warafy-wishlist-btn');
                const productId = btn.dataset.productId;
                window.toggleWishlist(productId, btn);
            }
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'warafy_wishlist_scripts');

/* Product Ranking System */
// Add admin menu for product ranking
add_action('admin_menu', 'warafy_product_ranking_menu');
function warafy_product_ranking_menu() {
    add_theme_page(
        'Product Ranking',
        'Product Ranking',
        'manage_options',
        'warafy-product-ranking',
        'warafy_product_ranking_page'
    );
}

// Display admin page for product ranking
function warafy_product_ranking_page() {
    ?>
    <div class="wrap">
        <h1>Product Ranking Management</h1>
        <p>Enter comma-separated product IDs for ranking. The order determines the ranking position.</p>
        
        <form method="post" action="options.php">
            <?php
            settings_fields('warafy_product_ranking');
            do_settings_sections('warafy_product_ranking');
            ?>
            
            <h2>Top 10 Products (Homepage)</h2>
            <p>Enter up to 10 product IDs separated by commas. The first ID will be #1, second will be #2, etc.</p>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="warafy_homepage_ranking_ids">Homepage Ranking IDs</label>
                    </th>
                    <td>
                        <textarea id="warafy_homepage_ranking_ids" name="warafy_homepage_ranking_ids" rows="3" class="large-text" placeholder="123, 456, 789, 1011, 1213"><?php 
                            $homepage_ids = get_option('warafy_homepage_ranking_ids', '');
                            echo esc_textarea($homepage_ids);
                        ?></textarea>
                        <p class="description">Enter product IDs separated by commas. Maximum 10 products.</p>
                    </td>
                </tr>
            </table>
            
            <div class="homepage-preview">
                <h3>Preview:</h3>
                <div id="homepage-preview-container" class="ranking-preview">
                    <?php
                    $homepage_ids = get_option('warafy_homepage_ranking_ids', '');
                    if (!empty($homepage_ids)) {
                        $ids_array = array_map('trim', explode(',', $homepage_ids));
                        $ids_array = array_filter($ids_array);
                        $ids_array = array_slice($ids_array, 0, 10);
                        
                        foreach ($ids_array as $index => $product_id) {
                            $product = wc_get_product($product_id);
                            if ($product) {
                                echo '<div class="preview-item">';
                                echo '<span class="rank">#' . ($index + 1) . '</span>';
                                echo '<span class="product-info">' . $product->get_name() . ' (ID: ' . $product_id . ')</span>';
                                echo '</div>';
                            } else {
                                echo '<div class="preview-item error">';
                                echo '<span class="rank">#' . ($index + 1) . '</span>';
                                echo '<span class="product-info">Invalid Product ID: ' . $product_id . '</span>';
                                echo '</div>';
                            }
                        }
                    } else {
                        echo '<p class="no-products">No product IDs entered.</p>';
                    }
                    ?>
                </div>
            </div>
            
            <h2 class="mt-8">Top 100 Products (Ranking Page)</h2>
            <p>Enter up to 100 product IDs separated by commas. The first ID will be #1, second will be #2, etc.</p>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="warafy_full_ranking_ids">Full Ranking IDs</label>
                    </th>
                    <td>
                        <textarea id="warafy_full_ranking_ids" name="warafy_full_ranking_ids" rows="5" class="large-text" placeholder="123, 456, 789, 1011, 1213, 1415, 1617, 1819, 2021, 2223"><?php 
                            $full_ids = get_option('warafy_full_ranking_ids', '');
                            echo esc_textarea($full_ids);
                        ?></textarea>
                        <p class="description">Enter product IDs separated by commas. Maximum 100 products.</p>
                    </td>
                </tr>
            </table>
            
            <div class="full-preview">
                <h3>Preview (First 20):</h3>
                <div id="full-preview-container" class="ranking-preview">
                    <?php
                    $full_ids = get_option('warafy_full_ranking_ids', '');
                    if (!empty($full_ids)) {
                        $ids_array = array_map('trim', explode(',', $full_ids));
                        $ids_array = array_filter($ids_array);
                        $preview_ids = array_slice($ids_array, 0, 20);
                        
                        foreach ($preview_ids as $index => $product_id) {
                            $product = wc_get_product($product_id);
                            if ($product) {
                                echo '<div class="preview-item">';
                                echo '<span class="rank">#' . ($index + 1) . '</span>';
                                echo '<span class="product-info">' . $product->get_name() . ' (ID: ' . $product_id . ')</span>';
                                echo '</div>';
                            } else {
                                echo '<div class="preview-item error">';
                                echo '<span class="rank">#' . ($index + 1) . '</span>';
                                echo '<span class="product-info">Invalid Product ID: ' . $product_id . '</span>';
                                echo '</div>';
                            }
                        }
                        
                        if (count($ids_array) > 20) {
                            echo '<p class="more-items">... and ' . (count($ids_array) - 20) . ' more products</p>';
                        }
                    } else {
                        echo '<p class="no-products">No product IDs entered.</p>';
                    }
                    ?>
                </div>
            </div>
            
            <?php submit_button(); ?>
        </form>
    </div>
    
    <style>
    .ranking-preview {
        border: 1px solid #ddd;
        padding: 10px;
        background: #f9f9f9;
        margin: 10px 0;
        max-height: 300px;
        overflow-y: auto;
    }
    .preview-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 5px 0;
        border-bottom: 1px solid #eee;
    }
    .preview-item:last-child {
        border-bottom: none;
    }
    .preview-item.error {
        color: #d63638;
    }
    .rank {
        font-weight: bold;
        color: #0073aa;
        min-width: 40px;
    }
    .product-info {
        flex: 1;
    }
    .no-products, .more-items {
        text-align: center;
        color: #666;
        font-style: italic;
        padding: 20px;
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // Live preview for homepage rankings
        $('#warafy_homepage_ranking_ids').on('input', function() {
            updatePreview($(this).val(), '#homepage-preview-container', 10);
        });
        
        // Live preview for full rankings
        $('#warafy_full_ranking_ids').on('input', function() {
            updatePreview($(this).val(), '#full-preview-container', 20);
        });
        
        function updatePreview(idsString, containerSelector, maxItems) {
            var container = $(containerSelector);
            container.empty();
            
            if (!idsString.trim()) {
                container.html('<p class="no-products">No product IDs entered.</p>');
                return;
            }
            
            var ids = idsString.split(',').map(function(id) {
                return id.trim();
            }).filter(function(id) {
                return id !== '';
            });
            
            var previewIds = ids.slice(0, maxItems);
            
            previewIds.forEach(function(productId, index) {
                if ($.isNumeric(productId)) {
                    container.append('<div class="preview-item"><span class="rank">#' + (index + 1) + '</span><span class="product-info">Loading product ID: ' + productId + '...</span></div>');
                    
                    // AJAX to verify product exists
                    $.post(ajaxurl, {
                        action: 'warafy_verify_product',
                        product_id: productId
                    }, function(response) {
                        var item = container.find('.preview-item').eq(index);
                        if (response.success) {
                            item.find('.product-info').text(response.data.name + ' (ID: ' + productId + ')');
                            item.removeClass('error');
                        } else {
                            item.find('.product-info').text('Invalid Product ID: ' + productId);
                            item.addClass('error');
                        }
                    });
                } else {
                    container.append('<div class="preview-item error"><span class="rank">#' + (index + 1) + '</span><span class="product-info">Invalid ID format: ' + productId + '</span></div>');
                }
            });
            
            if (ids.length > maxItems) {
                container.append('<p class="more-items">... and ' + (ids.length - maxItems) + ' more products</p>');
            }
        }
    });
    </script>
    <?php
}

// Register settings
add_action('admin_init', 'warafy_product_ranking_settings');
function warafy_product_ranking_settings() {
    register_setting('warafy_product_ranking', 'warafy_homepage_ranking_ids');
    register_setting('warafy_product_ranking', 'warafy_full_ranking_ids');
}

// AJAX handler for product verification
add_action('wp_ajax_warafy_verify_product', 'warafy_verify_product');
function warafy_verify_product() {
    $product_id = intval($_POST['product_id']);
    
    $product = wc_get_product($product_id);
    if ($product) {
        wp_send_json_success(['name' => $product->get_name()]);
    } else {
        wp_send_json_error('Product not found');
    }
}

// Get ranked products helper function
function warafy_get_ranked_products($type = 'homepage', $limit = null) {
    if ($type === 'homepage') {
        $ids_string = get_option('warafy_homepage_ranking_ids', '');
        $rankings = array_map('trim', explode(',', $ids_string));
        $rankings = array_filter($rankings);
        $rankings = array_map('intval', $rankings);
        if ($limit) {
            $rankings = array_slice($rankings, 0, $limit);
        }
    } else {
        $ids_string = get_option('warafy_full_ranking_ids', '');
        $rankings = array_map('trim', explode(',', $ids_string));
        $rankings = array_filter($rankings);
        $rankings = array_map('intval', $rankings);
        if ($limit) {
            $rankings = array_slice($rankings, 0, $limit);
        }
    }
    
    return $rankings;
}

// Shortcode to display wishlist items
function warafy_wishlist_shortcode($atts) {
    $atts = shortcode_atts(array(
        'view' => 'desktop'
    ), $atts);
    
    $container_id = $atts['view'] === 'mobile' ? 'warafy-wishlist-container-mobile' : 'warafy-wishlist-container-desktop';
    
    ob_start();
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Wishlist shortcode loaded for <?php echo $atts['view']; ?> view');
        const container = document.getElementById('<?php echo $container_id; ?>');
        
        if (!container) {
            console.error('Wishlist container not found: <?php echo $container_id; ?>');
            return;
        }
        
        // Clear any existing content first
        container.innerHTML = '';
        
        const wishlist = window.getWishlist();
        console.log('Wishlist items:', wishlist);
        
        if (wishlist.length === 0) {
            container.innerHTML = '<div class="empty-cart-container bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 lg:p-12 text-center max-w-2xl mx-auto mt-12"><div class="mb-6"><span class="material-symbols-outlined text-8xl lg:text-9xl text-gray-300" style="font-size: 6rem;" data-icon="favorite_border"></span></div><h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-3"><?php echo __t('Your love list is empty'); ?></h2><p class="text-gray-600 dark:text-gray-400 mb-8"><?php echo __t("You haven\'t added any items to your love list yet."); ?></p><a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="inline-flex items-center gap-2 bg-primary text-white px-8 py-4 rounded-full font-semibold hover:bg-primary/90 transition-all shadow-lg"><span class="material-symbols-outlined" data-icon="storefront"></span><?php echo __t('Start Shopping'); ?></a></div>';
            return;
        }

        // Show loading state
        container.innerHTML = '<div class="text-center py-8"><div class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full mb-4"><span class="material-symbols-outlined text-2xl text-blue-600 animate-spin" data-icon="refresh"></span></div><p class="text-gray-500 dark:text-gray-400">Loading ' + wishlist.length + ' loved items...</p></div>';

        // Fetch products via AJAX
        const data = new FormData();
        data.append('action', 'warafy_get_wishlist_products');
        data.append('product_ids', JSON.stringify(wishlist));
        data.append('view', '<?php echo $atts['view']; ?>');

        console.log('Fetching wishlist products for <?php echo $atts['view']; ?>...', wishlist);

        fetch('<?php echo admin_url('admin-ajax.php'); ?>?v=<?php echo time(); ?>', {
            method: 'POST',
            body: data
        })
        .then(response => {
            console.log('AJAX Response status:', response.status);
            
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.text();
        })
        .then(html => {
            console.log('HTML received for <?php echo $atts['view']; ?>, length:', html.length);
            
            if (html.trim() === '') {
                container.innerHTML = '<div class="text-center py-8"><p class="text-orange-500">No products received from server.</p></div>';
            } else {
                container.innerHTML = html;
                window.updateWishlistCount(); // Re-run to update buttons in the loaded content
                console.log('Products loaded successfully for <?php echo $atts['view']; ?>');
            }
        })
        .catch(error => {
            console.error('Error fetching wishlist products for <?php echo $atts['view']; ?>:', error);
            container.innerHTML = '<div class="text-center py-8"><p class="text-red-500">Error loading wishlist items: ' + error.message + '</p></div>';
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('warafy_wishlist', 'warafy_wishlist_shortcode');

// AJAX Handler for fetching wishlist products
add_action('wp_ajax_warafy_get_wishlist_products', 'warafy_get_wishlist_products');
add_action('wp_ajax_nopriv_warafy_get_wishlist_products', 'warafy_get_wishlist_products');

function warafy_get_wishlist_products() {
    // Debug: Log that the function was called
    error_log('warafy_get_wishlist_products called');
    
    // Set content type
    header('Content-Type: text/html; charset=utf-8');
    
    $ids = json_decode(stripslashes($_POST['product_ids']));
    $view = isset($_POST['view']) ? $_POST['view'] : 'desktop';
    
    // Debug: Log the received parameters
    error_log('Received product IDs: ' . print_r($ids, true));
    error_log('View type: ' . $view);
    
    if (empty($ids)) {
        echo '<div class="empty-cart-container bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 lg:p-12 text-center max-w-2xl mx-auto mt-12">
                <div class="mb-6">
                    <span class="material-symbols-outlined text-8xl lg:text-9xl text-gray-300" style="font-size: 6rem;" data-icon="favorite_border"></span>
                </div>
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-3">' . __t('Your love list is empty') . '</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-8">' . __t("You haven\'t added any items to your love list yet.") . '</p>
                <a href="' . get_permalink(wc_get_page_id('shop')) . '" 
                   class="inline-flex items-center gap-2 bg-primary text-white px-8 py-4 rounded-full font-semibold hover:bg-primary/90 transition-all shadow-lg">
                    <span class="material-symbols-outlined" data-icon="storefront"></span>
                    ' . __t('Start Shopping') . '
                </a>
            </div>';
        wp_die();
    }

    $args = array(
        'post_type' => 'product',
        'post__in' => $ids,
        'posts_per_page' => -1
    );

    $loop = new WP_Query($args);
    
    // Debug: Log query results
    error_log('WP_Query found ' . $loop->found_posts . ' products');

    if ($loop->have_posts()) {
        // Only show page title for desktop
        if ($view === 'desktop') {
            echo '<div class="mb-6 hidden lg:block">
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">My Love List</h1>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">You have ' . count($ids) . ' item' . (count($ids) > 1 ? 's' : '') . ' in your love list.</p>
                  </div>';

            // Main Layout Container for desktop
            echo '<div class="flex flex-col gap-4 lg:gap-8 lg:flex-row lg:items-start">';
            
            // Products Section
            echo '<div class="flex-1">';
        }

        if ($view === 'desktop') {
            // Desktop Products Container
            echo '<div class="hidden lg:block rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-background-dark">
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">';
        } else {
            // Mobile Products Container
            echo '<div class="lg:hidden bg-white dark:bg-background-dark">
                    <div class="flex flex-col gap-4 p-4">';
        }
        
        while ($loop->have_posts()) : $loop->the_post();
            global $product;
            
            if ($view === 'desktop') {
                ?>
                <!-- Desktop Wishlist Item -->
                <div class="flex flex-col gap-6 p-6 sm:flex-row sm:items-center">
                    <div class="w-full sm:w-32 sm:flex-shrink-0">
                        <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-lg overflow-hidden">
                            <a href="<?php the_permalink(); ?>">
                                <?php echo $product->get_image('woocommerce_thumbnail', array('class' => 'w-full h-full object-cover object-center')); ?>
                            </a>
                        </div>
                    </div>
                    <div class="flex flex-1 flex-col justify-between gap-4 sm:flex-row sm:items-center">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                <a href="<?php the_permalink(); ?>" class="hover:text-primary transition-colors"><?php the_title(); ?></a>
                            </h3>
                            <p class="mt-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                                <?php echo $product->is_in_stock() ? 'In Stock' : 'Out of Stock'; ?>
                            </p>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <p class="w-20 text-right text-lg font-semibold text-gray-900 dark:text-white">
                                <?php echo $product->get_price_html(); ?>
                            </p>
                            <div class="flex flex-col gap-2">
                                <a href="?add-to-cart=<?php echo $product->get_id(); ?>" 
                                   class="flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors">
                                    <span class="material-symbols-outlined text-lg" data-icon="add_shopping_cart"></span>
                                    Add to Cart
                                </a>
                                <button type="button" 
                                        class="warafy-wishlist-btn flex items-center justify-center gap-2 px-3 py-2 border border-red-300 text-red-600 rounded-lg font-medium hover:bg-red-50 transition-colors" 
                                        data-product-id="<?php echo $product->get_id(); ?>">
                                    <span class="material-symbols-outlined text-lg" data-icon="close"></span>
                                    Remove from Loved List
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            } else {
                ?>
                <!-- Mobile Wishlist Item -->
                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-background-dark">
                    <div class="flex items-start gap-4">
                        <!-- Product Image -->
                        <div class="w-24 flex-shrink-0">
                            <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-md overflow-hidden">
                                <a href="<?php the_permalink(); ?>">
                                    <?php echo $product->get_image('woocommerce_thumbnail', array('class' => 'w-full h-full object-cover object-center')); ?>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Product Details -->
                        <div class="flex flex-1 flex-col justify-between self-stretch">
                            <div>
                                <div class="flex items-start justify-between">
                                    <h3 class="font-semibold text-gray-900 dark:text-white pr-2">
                                        <a href="<?php the_permalink(); ?>" class="hover:text-primary transition-colors"><?php the_title(); ?></a>
                                    </h3>
                                    <button type="button" 
                                            class="warafy-wishlist-btn text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400"
                                            data-product-id="<?php echo $product->get_id(); ?>"
                                            title="Remove from Love">
                                        <span class="material-symbols-outlined text-xl" data-icon="close"></span>
                                    </button>
                                </div>
                                <p class="mt-2 text-lg font-bold text-gray-900 dark:text-white">
                                    <?php echo $product->get_price_html(); ?>
                                </p>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="mt-3 flex flex-col gap-2">
                                <a href="?add-to-cart=<?php echo $product->get_id(); ?>" 
                                   class="flex w-full items-center justify-center gap-2 rounded-full h-10 px-4 bg-primary text-white text-sm font-bold hover:bg-primary/90 transition-colors">
                                    <span class="material-symbols-outlined text-base" data-icon="add_shopping_cart"></span>
                                    Add to Cart
                                </a>
                                <button type="button" 
                                        class="warafy-wishlist-btn flex w-full items-center justify-center gap-2 rounded-full h-10 px-4 border border-red-300 text-red-600 text-sm font-medium hover:bg-red-50 transition-colors"
                                        data-product-id="<?php echo $product->get_id(); ?>">
                                    <span class="material-symbols-outlined text-base" data-icon="close"></span>
                                    Remove from Loved List
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        endwhile;
        
        echo '</div></div>';
        
        if ($view === 'desktop') {
            echo '</div>'; // Close products section
            
            // Love List Summary Sidebar (Desktop Only)
            echo '<aside class="hidden lg:block w-full lg:w-80 lg:flex-shrink-0">
                    <div class="sticky top-24 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-background-dark">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Love List Summary</h3>
                        <div class="mt-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-600 dark:text-gray-300">Total Items</p>
                                <p class="font-semibold text-gray-900 dark:text-white">' . count($ids) . '</p>
                            </div>
                        </div>
                        <div class="my-6 h-px w-full bg-gray-200 dark:bg-gray-700"></div>
                        <a href="' . get_permalink(wc_get_page_id('shop')) . '" 
                           class="mt-6 flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-base font-bold shadow-lg hover:bg-primary/90">
                            <span class="material-symbols-outlined mr-2" data-icon="storefront"></span>
                            Continue Shopping
                        </a>
                        <p class="mt-4 text-center text-xs text-gray-500 dark:text-gray-400">Add items from your love list to cart</p>
                    </div>
                  </aside>';
            
            echo '</div>'; // Close main layout
        }
        
        // Continue Shopping Link (Mobile Only)
        if ($view === 'mobile') {
            echo '<div class="mt-4 text-center lg:hidden">
                    <a class="text-sm font-medium text-primary hover:underline" href="' . get_permalink(wc_get_page_id('shop')) . '">Continue Shopping</a>
                  </div>';
        }
        
    } else {
        echo '<p class="text-center py-8 text-gray-500">Products not found.</p>';
    }
    wp_reset_postdata();
    wp_die();
}

// Best Sellers Settings
function warafy_best_sellers_menu() {
    add_menu_page(
        'Best Sellers Settings',
        'Best Sellers',
        'manage_options',
        'warafy-best-sellers',
        'warafy_best_sellers_page_html',
        'dashicons-star-filled',
        20
    );
}
add_action('admin_menu', 'warafy_best_sellers_menu');

function warafy_best_sellers_page_html() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('warafy_best_sellers_options');
            do_settings_sections('warafy-best-sellers');
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}

function warafy_best_sellers_settings_init() {
    register_setting('warafy_best_sellers_options', 'warafy_best_seller_ids');

    add_settings_section(
        'warafy_best_sellers_section',
        'Select Products',
        'warafy_best_sellers_section_callback',
        'warafy-best-sellers'
    );

    add_settings_field(
        'warafy_best_seller_ids',
        'Product IDs (comma separated)',
        'warafy_best_seller_ids_callback',
        'warafy-best-sellers',
        'warafy_best_sellers_section'
    );
}
add_action('admin_init', 'warafy_best_sellers_settings_init');

function warafy_best_sellers_section_callback() {
    echo '<p>Enter the product IDs you want to display in the Best Sellers section.</p>';
}

function warafy_best_seller_ids_callback() {
    $ids = get_option('warafy_best_seller_ids');
    ?>
    <input type="text" name="warafy_best_seller_ids" value="<?php echo esc_attr($ids); ?>" class="regular-text">
    <p class="description">Example: 12, 15, 23</p>
    <?php
}

// Create Categories Page on Theme Activation
function warafy_create_categories_page() {
    // Check if page already exists
    $existing_page = get_page_by_path('categories');
    
    if (!$existing_page) {
        // Create the page
        $page_data = array(
            'post_title'    => 'Categories',
            'post_content'  => '<!-- This page uses the Categories List template -->',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_name'     => 'categories'
        );
        
        $page_id = wp_insert_post($page_data);
        
        // Assign the Categories List template
        if ($page_id && !is_wp_error($page_id)) {
            update_post_meta($page_id, '_wp_page_template', 'page-categories.php');
        }
    }
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
add_action('after_setup_theme', 'warafy_create_categories_page');

// Create My Love Page on Theme Activation
function warafy_create_my_love_page() {
    // Check if page already exists
    $existing_page = get_page_by_path('my-love');
    
    if (!$existing_page) {
        // Create the page
        $page_data = array(
            'post_title'    => 'My Love',
            'post_content'  => '<!-- This page uses the My Love template -->',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_name'     => 'my-love'
        );
        
        $page_id = wp_insert_post($page_data);
        
        // Assign the My Love template
        if ($page_id && !is_wp_error($page_id)) {
            update_post_meta($page_id, '_wp_page_template', 'page-my-love.php');
        }
    }
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
add_action('after_setup_theme', 'warafy_create_my_love_page');

// Also flush rules on theme switch
add_action('switch_theme', 'flush_rewrite_rules');

// Force page creation on admin init if it doesn't exist
function warafy_ensure_categories_page() {
    if (!get_page_by_path('categories')) {
        warafy_create_categories_page();
    }
}
add_action('admin_init', 'warafy_ensure_categories_page');

// Force My Love page creation on admin init if it doesn't exist
function warafy_ensure_my_love_page() {
    if (!get_page_by_path('my-love')) {
        warafy_create_my_love_page();
    }
}
add_action('admin_init', 'warafy_ensure_my_love_page');

// Create page immediately when this file is loaded (for testing)
if (!get_page_by_path('categories')) {
    warafy_create_categories_page();
}

if (!get_page_by_path('my-love')) {
    warafy_create_my_love_page();
}

/* Search Autocomplete Functionality */

// AJAX handler for live search autocomplete
add_action('wp_ajax_warafy_live_search', 'warafy_live_search');
add_action('wp_ajax_nopriv_warafy_live_search', 'warafy_live_search');

function warafy_live_search() {
    // Set content type
    header('Content-Type: application/json; charset=utf-8');
    
    $search_term = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    
    if (empty($search_term) || strlen($search_term) < 2) {
        wp_send_json_success(['products' => []]);
        wp_die();
    }
    
    // Check cache first
    $cache_key = 'warafy_search_' . md5(strtolower($search_term));
    $cached_results = get_transient($cache_key);
    
    if ($cached_results !== false) {
        wp_send_json_success(['products' => $cached_results]);
        wp_die();
    }
    
    $products = array();
    
    // First try: Direct WordPress search with expanded terms
    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 10,
        'orderby' => 'relevance',
    );
    
    // Use the search term directly first
    $args['s'] = $search_term;
    
    $products_query = new WP_Query($args);
    
    if ($products_query->have_posts()) {
        while ($products_query->have_posts()) {
            $products_query->the_post();
            $product = wc_get_product(get_the_ID());
            
            if ($product) {
                $products[] = array(
                    'id' => $product->get_id(),
                    'title' => get_the_title(),
                    'price' => $product->get_price_html(),
                    'image' => wp_get_attachment_image_src($product->get_image_id(), 'thumbnail')[0] ?? wc_placeholder_img_src(),
                    'url' => get_permalink(),
                    'sku' => $product->get_sku(),
                    'stock_status' => $product->is_in_stock() ? 'In Stock' : 'Out of Stock',
                    'categories' => warafy_get_product_categories(get_the_ID()),
                    'type' => 'exact'
                );
            }
        }
    }
    
    // Second try: Category search if no products found
    if (empty($products)) {
        $category_products = warafy_search_by_category($search_term);
        $products = array_merge($products, $category_products);
    }
    
    // Third try: Partial word matching if no results
    if (empty($products)) {
        // Split search term into individual words
        $search_words = explode(' ', trim($search_term));
        $search_words = array_filter($search_words, function($word) {
            return strlen($word) >= 2;
        });
        
        if (!empty($search_words)) {
            // Try each word individually and combine results
            $all_product_ids = array();
            
            foreach ($search_words as $word) {
                $word_args = array(
                    'post_type' => 'product',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'fields' => 'ids',
                    's' => $word . '*', // Add wildcard for partial matching
                );
                
                $word_query = new WP_Query($word_args);
                $word_ids = $word_query->posts;
                $all_product_ids = array_merge($all_product_ids, $word_ids);
            }
            
            // Count occurrences and get most relevant products
            $product_counts = array_count_values($all_product_ids);
            arsort($product_counts);
            
            // Get products that match multiple words first
            $relevant_ids = array_keys($product_counts);
            
            if (!empty($relevant_ids)) {
                $final_args = array(
                    'post_type' => 'product',
                    'post_status' => 'publish',
                    'posts_per_page' => 10,
                    'post__in' => $relevant_ids,
                    'orderby' => 'post__in',
                );
                
                $final_query = new WP_Query($final_args);
                
                if ($final_query->have_posts()) {
                    while ($final_query->have_posts()) {
                        $final_query->the_post();
                        $product = wc_get_product(get_the_ID());
                        
                        if ($product) {
                            $title = get_the_title();
                            $products[] = array(
                                'id' => $product->get_id(),
                                'title' => $title,
                                'price' => $product->get_price_html(),
                                'image' => wp_get_attachment_image_src($product->get_image_id(), 'thumbnail')[0] ?? wc_placeholder_img_src(),
                                'url' => get_permalink(),
                                'sku' => $product->get_sku(),
                                'stock_status' => $product->is_in_stock() ? 'In Stock' : 'Out of Stock',
                                'categories' => warafy_get_product_categories(get_the_ID()),
                                'relevance' => $product_counts[get_the_ID()] ?? 1,
                                'type' => 'partial'
                            );
                        }
                    }
                }
            }
        }
    }
    
    // Fourth try: Typo correction and suggestions
    if (empty($products)) {
        $suggested_term = warafy_suggest_correction($search_term);
        if ($suggested_term && $suggested_term !== $search_term) {
            // Search with suggested term
            $correction_args = array(
                'post_type' => 'product',
                'post_status' => 'publish',
                'posts_per_page' => 10,
                'orderby' => 'relevance',
                's' => $suggested_term,
            );
            
            $correction_query = new WP_Query($correction_args);
            
            if ($correction_query->have_posts()) {
                while ($correction_query->have_posts()) {
                    $correction_query->the_post();
                    $product = wc_get_product(get_the_ID());
                    
                    if ($product) {
                        $products[] = array(
                            'id' => $product->get_id(),
                            'title' => get_the_title(),
                            'price' => $product->get_price_html(),
                            'image' => wp_get_attachment_image_src($product->get_image_id(), 'thumbnail')[0] ?? wc_placeholder_img_src(),
                            'url' => get_permalink(),
                            'sku' => $product->get_sku(),
                            'stock_status' => $product->is_in_stock() ? 'In Stock' : 'Out of Stock',
                            'categories' => warafy_get_product_categories(get_the_ID()),
                            'type' => 'correction',
                            'suggestion' => $suggested_term
                        );
                    }
                }
            }
        }
    }
    
    // Fifth try: Fuzzy similarity matching as last resort
    if (empty($products) && strlen($search_term) >= 3) {
        $fallback_args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => 50,
            'orderby' => 'title',
            'order' => 'ASC'
        );
        
        $fallback_query = new WP_Query($fallback_args);
        
        if ($fallback_query->have_posts()) {
            while ($fallback_query->have_posts()) {
                $fallback_query->the_post();
                $title = get_the_title();
                $similarity = warafy_calculate_similarity($search_term, $title);
                
                // Lower threshold for fuzzy search (40% instead of 60%)
                if ($similarity >= 0.4) {
                    $product = wc_get_product(get_the_ID());
                    if ($product) {
                        $products[] = array(
                            'id' => $product->get_id(),
                            'title' => $title,
                            'price' => $product->get_price_html(),
                            'image' => wp_get_attachment_image_src($product->get_image_id(), 'thumbnail')[0] ?? wc_placeholder_img_src(),
                            'url' => get_permalink(),
                            'sku' => $product->get_sku(),
                            'stock_status' => $product->is_in_stock() ? 'In Stock' : 'Out of Stock',
                            'categories' => warafy_get_product_categories(get_the_ID()),
                            'similarity' => $similarity,
                            'type' => 'fuzzy'
                        );
                    }
                }
            }
            
            // Sort by similarity
            usort($products, function($a, $b) {
                return ($b['similarity'] ?? 0) - ($a['similarity'] ?? 0);
            });
            
            // Limit to 10 results
            $products = array_slice($products, 0, 10);
        }
    }
    
    // Sort results: exact matches first, then partial, then corrections, then fuzzy
    usort($products, function($a, $b) {
        $type_order = ['exact' => 0, 'partial' => 1, 'category' => 2, 'correction' => 3, 'fuzzy' => 4];
        $a_type = $a['type'] ?? 'fuzzy';
        $b_type = $b['type'] ?? 'fuzzy';
        
        $a_order = $type_order[$a_type] ?? 4;
        $b_order = $type_order[$b_type] ?? 4;
        
        if ($a_order !== $b_order) {
            return $a_order - $b_order;
        }
        
        // Within same type, sort by relevance/similarity
        $a_score = $a['relevance'] ?? $a['similarity'] ?? 0;
        $b_score = $b['relevance'] ?? $b['similarity'] ?? 0;
        
        return $b_score - $a_score;
    });
    
    // Limit to 10 results after sorting
    $products = array_slice($products, 0, 10);
    
    // Cache results for 10 minutes
    set_transient($cache_key, $products, 600);
    
    // Log search for analytics
    warafy_log_search($search_term, count($products));
    
    wp_reset_postdata();
    wp_send_json_success(['products' => $products]);
    wp_die();
}

// Search by category
function warafy_search_by_category($search_term) {
    $products = array();
    
    // Get categories that match the search term
    $category_args = array(
        'taxonomy' => 'product_cat',
        'name__like' => $search_term,
        'hide_empty' => true,
        'number' => 5
    );
    
    $categories = get_terms($category_args);
    
    if (!empty($categories) && !is_wp_error($categories)) {
        foreach ($categories as $category) {
            $cat_products = get_posts(array(
                'post_type' => 'product',
                'post_status' => 'publish',
                'posts_per_page' => 3,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => $category->term_id,
                    ),
                ),
            ));
            
            foreach ($cat_products as $product_post) {
                $product = wc_get_product($product_post->ID);
                if ($product) {
                    $products[] = array(
                        'id' => $product->get_id(),
                        'title' => get_the_title($product_post->ID),
                        'price' => $product->get_price_html(),
                        'image' => wp_get_attachment_image_src($product->get_image_id(), 'thumbnail')[0] ?? wc_placeholder_img_src(),
                        'url' => get_permalink($product_post->ID),
                        'sku' => $product->get_sku(),
                        'stock_status' => $product->is_in_stock() ? 'In Stock' : 'Out of Stock',
                        'categories' => warafy_get_product_categories($product_post->ID),
                        'type' => 'category',
                        'matched_category' => $category->name
                    );
                }
            }
        }
    }
    
    return $products;
}

// Get product categories
function warafy_get_product_categories($product_id) {
    $categories = get_the_terms($product_id, 'product_cat');
    $category_names = array();
    
    if ($categories && !is_wp_error($categories)) {
        foreach ($categories as $category) {
            $category_names[] = $category->name;
        }
    }
    
    return $category_names;
}

// Typo correction and suggestion
function warafy_suggest_correction($search_term) {
    $common_typos = array(
        'vibr' => 'vibration',
        'anti' => 'anti',
        'washng' => 'washing',
        'machin' => 'machine',
        'pad' => 'pads',
        'faucet' => 'faucet',
        'flex' => 'flexible',
        'swivl' => 'swivel',
        'turbo' => 'turbo',
        'kichen' => 'kitchen',
        'fone' => 'phone',
        'laptap' => 'laptop',
        'headfone' => 'headphone',
        'bluetoth' => 'bluetooth',
        'chargr' => 'charger',
        'cabel' => 'cable',
        'mous' => 'mouse',
        'keybord' => 'keyboard',
        'monitr' => 'monitor',
        'spikr' => 'speaker',
        'camra' => 'camera',
        'batri' => 'battery',
        'powr' => 'power',
        'adaptr' => 'adapter',
    );
    
    $search_lower = strtolower($search_term);
    
    // Direct typo lookup
    if (isset($common_typos[$search_lower])) {
        return $common_typos[$search_lower];
    }
    
    // Check for partial matches in common typos
    foreach ($common_typos as $typo => $correction) {
        if (strpos($typo, $search_lower) !== false || strpos($search_lower, $typo) !== false) {
            return $correction;
        }
    }
    
    // Use Levenshtein distance for close matches
    foreach ($common_typos as $typo => $correction) {
        $distance = levenshtein($search_lower, $typo);
        if ($distance <= 2 && strlen($search_lower) >= 4) {
            return $correction;
        }
    }
    
    return false;
}

// AJAX handler for getting popular searches
add_action('wp_ajax_warafy_get_popular_searches', 'warafy_get_popular_searches');
add_action('wp_ajax_nopriv_warafy_get_popular_searches', 'warafy_get_popular_searches');

function warafy_get_popular_searches() {
    header('Content-Type: application/json; charset=utf-8');
    
    $search_logs = get_option('warafy_search_logs', array());
    $popular_searches = array();
    
    // Aggregate search counts from last 7 days
    $cutoff_date = date('Y-m-d', strtotime('-7 days'));
    
    foreach ($search_logs as $date => $logs) {
        if ($date >= $cutoff_date) {
            foreach ($logs as $term => $data) {
                if (!isset($popular_searches[$term])) {
                    $popular_searches[$term] = 0;
                }
                $popular_searches[$term] += $data['count'];
            }
        }
    }
    
    // Sort by popularity
    arsort($popular_searches);
    
    // Return top 10
    $top_searches = array_slice(array_keys($popular_searches), 0, 10);
    
    // If no analytics data, return default popular searches
    if (empty($top_searches)) {
        $top_searches = array(
            'phone case',
            'laptop stand', 
            'wireless charger',
            'bluetooth speaker',
            'phone holder',
            'cable organizer',
            'desk lamp',
            'power bank',
            'headphones',
            'mouse pad'
        );
    }
    
    wp_send_json_success(['searches' => $top_searches]);
    wp_die();
}

// Log search for analytics
function warafy_log_search($search_term, $result_count) {
    $search_logs = get_option('warafy_search_logs', array());
    $today = date('Y-m-d');
    
    if (!isset($search_logs[$today])) {
        $search_logs[$today] = array();
    }
    
    if (!isset($search_logs[$today][$search_term])) {
        $search_logs[$today][$search_term] = array(
            'count' => 0,
            'results' => 0,
            'last_search' => current_time('mysql')
        );
    }
    
    $search_logs[$today][$search_term]['count']++;
    $search_logs[$today][$search_term]['results'] = $result_count;
    $search_logs[$today][$search_term]['last_search'] = current_time('mysql');
    
    // Keep only last 30 days of logs
    $cutoff_date = date('Y-m-d', strtotime('-30 days'));
    foreach ($search_logs as $date => $logs) {
        if ($date < $cutoff_date) {
            unset($search_logs[$date]);
        }
    }
    
    update_option('warafy_search_logs', $search_logs);
}

// Calculate similarity between search term and product title
function warafy_calculate_similarity($search, $title) {
    $search_lower = strtolower($search);
    $title_lower = strtolower($title);
    
    // Exact match
    if ($search_lower === $title_lower) {
        return 1.0;
    }
    
    // Contains search term
    if (strpos($title_lower, $search_lower) !== false) {
        return 0.9;
    }
    
    // Enhanced partial word matching
    $search_words = explode(' ', trim($search_lower));
    $title_words = explode(' ', $title_lower);
    
    // Remove empty words and short words
    $search_words = array_filter($search_words, function($word) {
        return strlen($word) >= 2;
    });
    $title_words = array_filter($title_words, function($word) {
        return strlen($word) >= 2;
    });
    
    if (empty($search_words)) {
        return 0;
    }
    
    $word_matches = 0;
    $partial_matches = 0;
    $total_similarity = 0;
    
    foreach ($search_words as $search_word) {
        $best_match = 0;
        $best_title_word = '';
        
        foreach ($title_words as $title_word) {
            // Check if search word is contained in title word
            if (strpos($title_word, $search_word) !== false) {
                $partial_matches++;
                $best_match = max($best_match, 0.8);
                continue;
            }
            
            // Check if title word is contained in search word
            if (strpos($search_word, $title_word) !== false) {
                $partial_matches++;
                $best_match = max($best_match, 0.7);
                continue;
            }
            
            // Calculate similarity for this word pair
            similar_text($search_word, $title_word, $word_percent);
            $word_similarity = $word_percent / 100;
            
            if ($word_similarity >= 0.6) {
                $word_matches++;
                $best_match = max($best_match, $word_similarity);
            } elseif ($word_similarity >= 0.4) {
                // Partial similarity still counts
                $best_match = max($best_match, $word_similarity * 0.5);
            }
        }
        
        $total_similarity += $best_match;
    }
    
    // Calculate average similarity
    $avg_similarity = $total_similarity / count($search_words);
    
    // Boost for multiple word matches
    if ($word_matches > 0) {
        $word_bonus = ($word_matches / count($search_words)) * 0.2;
        $avg_similarity = max($avg_similarity, $word_bonus);
    }
    
    // Boost for partial matches
    if ($partial_matches > 0) {
        $partial_bonus = ($partial_matches / count($search_words)) * 0.3;
        $avg_similarity = max($avg_similarity, $partial_bonus);
    }
    
    // Ensure minimum similarity for any match
    if ($word_matches > 0 || $partial_matches > 0) {
        $avg_similarity = max($avg_similarity, 0.3);
    }
    
    return min($avg_similarity, 1.0);
}

// Create searchable words meta field for products
function warafy_update_searchable_words($post_id) {
    if (get_post_type($post_id) !== 'product') {
        return;
    }
    
    $product = wc_get_product($post_id);
    if (!$product) {
        return;
    }
    
    $title = get_the_title($post_id);
    $description = $product->get_description() ?: '';
    $short_description = $product->get_short_description() ?: '';
    $sku = $product->get_sku() ?: '';
    
    // Combine all text content
    $all_text = $title . ' ' . $description . ' ' . $short_description . ' ' . $sku;
    
    // Extract words (remove special characters, convert to lowercase)
    $words = array();
    preg_match_all('/\b[a-z0-9]{2,}\b/i', strtolower($all_text), $matches);
    
    if (!empty($matches[0])) {
        $words = array_unique($matches[0]);
        $searchable_words = implode(' ', $words);
        update_post_meta($post_id, '_search_words', $searchable_words);
    }
}

// Hook to update searchable words when product is saved
add_action('save_post', 'warafy_update_searchable_words');
add_action('wp_insert_post', 'warafy_update_searchable_words');

// Batch update existing products
function warafy_batch_update_searchable_words() {
    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids'
    );
    
    $product_ids = get_posts($args);
    
    foreach ($product_ids as $product_id) {
        warafy_update_searchable_words($product_id);
    }
}

// Run batch update once (can be triggered manually if needed)
add_action('admin_init', function() {
    if (isset($_GET['warafy_update_search']) && current_user_can('manage_options')) {
        warafy_batch_update_searchable_words();
        wp_redirect(admin_url('admin.php?page=warafy-search-settings&updated=true'));
        exit;
    }
});

// Enqueue search autocomplete JavaScript
function warafy_search_autocomplete_scripts() {
    ?>
    <script>
    // Search Autocomplete Functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Enhanced search with autocomplete
        function initSearchAutocomplete() {
            const searchInputs = document.querySelectorAll('input[name="s"][type="search"]');
            
            searchInputs.forEach(searchInput => {
                // Create dropdown container
                const dropdown = document.createElement('div');
                dropdown.className = 'warafy-search-dropdown hidden absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto';
                
                // Insert dropdown after search input parent
                const searchForm = searchInput.closest('form');
                if (searchForm) {
                    searchForm.style.position = 'relative';
                    searchForm.appendChild(dropdown);
                }
                
                let searchTimeout;
                let currentSearchTerm = '';
                
                // Search on input with debounce
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.trim();
                    
                    // Clear previous timeout
                    clearTimeout(searchTimeout);
                    
                    if (searchTerm.length < 2) {
                        hideDropdown();
                        return;
                    }
                    
                    // Don't search if same term
                    if (searchTerm === currentSearchTerm) {
                        return;
                    }
                    
                    currentSearchTerm = searchTerm;
                    
                    // Debounce search
                    searchTimeout = setTimeout(() => {
                        performSearch(searchTerm);
                    }, 300);
                });
                
                // Hide dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!searchForm.contains(e.target)) {
                        hideDropdown();
                    }
                });
                
                // Handle keyboard navigation
                searchInput.addEventListener('keydown', function(e) {
                    const items = dropdown.querySelectorAll('.search-result-item');
                    const activeItem = dropdown.querySelector('.search-result-item.active');
                    let index = activeItem ? Array.from(items).indexOf(activeItem) : -1;
                    
                    switch (e.key) {
                        case 'ArrowDown':
                            e.preventDefault();
                            if (index < items.length - 1) {
                                if (activeItem) activeItem.classList.remove('active');
                                items[index + 1].classList.add('active');
                                items[index + 1].scrollIntoView({ block: 'nearest' });
                            }
                            break;
                        case 'ArrowUp':
                            e.preventDefault();
                            if (index > 0) {
                                if (activeItem) activeItem.classList.remove('active');
                                items[index - 1].classList.add('active');
                                items[index - 1].scrollIntoView({ block: 'nearest' });
                            }
                            break;
                        case 'Enter':
                            e.preventDefault();
                            if (activeItem) {
                                window.location.href = activeItem.dataset.url;
                            } else {
                                searchForm.submit();
                            }
                            break;
                        case 'Escape':
                            hideDropdown();
                            searchInput.blur();
                            break;
                    }
                });
                
                function performSearch(term) {
                    // Show loading state
                    showLoading();
                    
                    // Fetch search results
                    const url = '<?php echo admin_url('admin-ajax.php'); ?>';
                    const params = new URLSearchParams({
                        action: 'warafy_live_search',
                        s: term
                    });
                    
                    fetch(url + '?' + params.toString())
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.data.products) {
                                displayResults(data.data.products);
                            } else {
                                displayNoResults();
                            }
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                            displayError();
                        });
                }
                
                function showLoading() {
                    dropdown.innerHTML = `
                        <div class="p-4 text-center">
                            <div class="inline-flex items-center justify-center w-8 h-8">
                                <span class="material-symbols-outlined text-2xl text-blue-600 animate-spin" data-icon="refresh"></span>
                            </div>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Searching...</p>
                        </div>
                    `;
                    dropdown.classList.remove('hidden');
                }
                
                function displayResults(products) {
                    if (products.length === 0) {
                        displayNoResults();
                        return;
                    }
                    
                    let resultsHTML = '';
                    let hasSuggestions = false;
                    let hasCorrections = false;
                    
                    // Check for corrections or suggestions
                    products.forEach(product => {
                        if (product.type === 'correction' && product.suggestion) {
                            hasCorrections = true;
                        }
                    });
                    
                    // Show correction notice if applicable
                    if (hasCorrections) {
                        const suggestedTerm = products.find(p => p.type === 'correction')?.suggestion;
                        resultsHTML += `
                            <div class="search-suggestion px-4 py-3 bg-blue-50 dark:bg-blue-900/30 border-b border-blue-200 dark:border-blue-700">
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400" data-icon="spellcheck"></span>
                                    <span class="text-blue-800 dark:text-blue-200">Did you mean: <strong>"${suggestedTerm}"</strong>?</span>
                                </div>
                            </div>
                        `;
                    }
                    
                    // Show recent searches if search is empty
                    if (searchInput.value.trim() === '') {
                        const recentSearches = getRecentSearches();
                        if (recentSearches.length > 0) {
                            resultsHTML += `
                                <div class="recent-searches-section">
                                    <div class="px-4 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Recent Searches</div>
                                    ${recentSearches.slice(0, 5).map(term => `
                                        <div class="recent-search-item flex items-center gap-3 px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors" data-search="${term}">
                                            <span class="material-symbols-outlined text-gray-400 text-sm" data-icon="history"></span>
                                            <span class="text-sm text-gray-700 dark:text-gray-300">${term}</span>
                                        </div>
                                    `).join('')}
                                </div>
                            `;
                        }
                    }
                    
                    // Display products
                    resultsHTML += products.map((product, index) => {
                        let badgeHTML = '';
                        let categoryHTML = '';
                        
                        // Add type badge
                        if (product.type === 'category' && product.matched_category) {
                            badgeHTML = `<span class="inline-block px-2 py-1 text-xs bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 rounded-full">Category: ${product.matched_category}</span>`;
                        } else if (product.type === 'correction') {
                            badgeHTML = `<span class="inline-block px-2 py-1 text-xs bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-full">Corrected</span>`;
                        } else if (product.type === 'fuzzy') {
                            badgeHTML = `<span class="inline-block px-2 py-1 text-xs bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300 rounded-full">Similar</span>`;
                        }
                        
                        // Add categories
                        if (product.categories && product.categories.length > 0) {
                            categoryHTML = `<div class="text-xs text-gray-400 dark:text-gray-500 mt-1">${product.categories.slice(0, 2).join(', ')}</div>`;
                        }
                        
                        return `
                            <div class="search-result-item flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors" 
                                 data-url="${product.url}">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden flex-shrink-0">
                                    <img src="${product.image}" alt="${product.title}" 
                                         class="w-full h-full object-cover"
                                         onerror="this.src='<?php echo wc_placeholder_img_src(); ?>'">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">${product.title}</h4>
                                        ${badgeHTML}
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">${product.price}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                        ${product.stock_status === 'In Stock' ? 
                                            '<span class="text-green-600 dark:text-green-400">✓ In Stock</span>' : 
                                            '<span class="text-red-600 dark:text-red-400">Out of Stock</span>'}
                                    </p>
                                    ${categoryHTML}
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="material-symbols-outlined text-gray-400" data-icon="arrow_forward"></span>
                                </div>
                            </div>
                        `;
                    }).join('');
                    
                    dropdown.innerHTML = resultsHTML;
                    dropdown.classList.remove('hidden');
                    
                    // Add click handlers to result items
                    dropdown.querySelectorAll('.search-result-item').forEach(item => {
                        item.addEventListener('click', function() {
                            const searchTerm = searchInput.value.trim();
                            if (searchTerm) {
                                saveRecentSearch(searchTerm);
                            }
                            window.location.href = this.dataset.url;
                        });
                    });
                    
                    // Add click handlers to recent search items
                    dropdown.querySelectorAll('.recent-search-item').forEach(item => {
                        item.addEventListener('click', function() {
                            const term = this.dataset.search;
                            searchInput.value = term;
                            performSearch(term);
                        });
                    });
                }
                
                async function displayNoResults() {
                    // Show popular searches when no results found
                    const popularSearches = await getPopularSearches();
                    let suggestionsHTML = `
                        <div class="p-4 text-center">
                            <span class="material-symbols-outlined text-3xl text-gray-300" data-icon="search_off"></span>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No products found</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Try these popular searches:</p>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700">
                    `;
                    
                    popularSearches.slice(0, 5).forEach(term => {
                        suggestionsHTML += `
                            <div class="popular-search-item flex items-center gap-3 px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors" data-search="${term}">
                                <span class="material-symbols-outlined text-gray-400 text-sm" data-icon="trending_up"></span>
                                <span class="text-sm text-gray-700 dark:text-gray-300">${term}</span>
                            </div>
                        `;
                    });
                    
                    suggestionsHTML += '</div>';
                    dropdown.innerHTML = suggestionsHTML;
                    dropdown.classList.remove('hidden');
                    
                    // Add click handlers to popular search items
                    dropdown.querySelectorAll('.popular-search-item').forEach(item => {
                        item.addEventListener('click', function() {
                            const term = this.dataset.search;
                            searchInput.value = term;
                            performSearch(term);
                        });
                    });
                }
                
                function getRecentSearches() {
                    const recent = localStorage.getItem('warafy_recent_searches');
                    return recent ? JSON.parse(recent) : [];
                }
                
                function saveRecentSearch(term) {
                    let recent = getRecentSearches();
                    recent = recent.filter(t => t !== term); // Remove if exists
                    recent.unshift(term); // Add to beginning
                    recent = recent.slice(0, 10); // Keep only 10
                    localStorage.setItem('warafy_recent_searches', JSON.stringify(recent));
                }
                
                async function getPopularSearches() {
                    try {
                        const response = await fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=warafy_get_popular_searches');
                        const data = await response.json();
                        if (data.success && data.data.searches) {
                            return data.data.searches;
                        }
                    } catch (error) {
                        console.error('Error fetching popular searches:', error);
                    }
                    
                    // Fallback to default searches
                    return [
                        'phone case',
                        'laptop stand',
                        'wireless charger',
                        'bluetooth speaker',
                        'phone holder',
                        'cable organizer',
                        'desk lamp',
                        'power bank',
                        'headphones',
                        'mouse pad'
                    ];
                }
                
                function displayError() {
                    dropdown.innerHTML = `
                        <div class="p-4 text-center">
                            <span class="material-symbols-outlined text-3xl text-red-300" data-icon="error"></span>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Search error</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Please try again</p>
                        </div>
                    `;
                    dropdown.classList.remove('hidden');
                }
                
                function hideDropdown() {
                    dropdown.classList.add('hidden');
                }
            });
        }
        
        // Initialize search autocomplete
        initSearchAutocomplete();
        
        // Re-initialize when new content is loaded (for AJAX pages)
        if (typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length) {
                        const hasNewSearchInputs = Array.from(mutation.addedNodes).some(node => {
                            return node.nodeType === 1 && node.querySelector('input[name="s"][type="search"]');
                        });
                        if (hasNewSearchInputs) {
                            setTimeout(initSearchAutocomplete, 100);
                        }
                    }
                });
            });
            
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    });
    </script>
    
    <style>
    /* Search Dropdown Styles */
    .warafy-search-dropdown {
        animation: fadeInDown 0.2s ease-out;
    }
    
    .search-result-item.active {
        background-color: rgb(243 244 246); /* gray-100 */
        color: rgb(17 24 39); /* gray-900 */
    }
    
    .dark .search-result-item.active {
        background-color: rgb(55 65 81); /* gray-700 */
        color: rgb(243 244 246); /* gray-100 */
    }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Ensure dropdown appears above other content */
    .warafy-search-dropdown {
        z-index: 9999 !important;
    }
    </style>
    <?php
}
add_action('wp_footer', 'warafy_search_autocomplete_scripts');

/* Translation System */

// Load translations
function warafy_get_translations() {
    $json_file = get_template_directory() . '/translations.json';
    if (file_exists($json_file)) {
        $json_content = file_get_contents($json_file);
        return json_decode($json_content, true);
    }
    return [];
}

// Translation helper
function __t($text) {
    $lang = isset($_COOKIE['warafy_language']) ? $_COOKIE['warafy_language'] : 'en';
    
    if ($lang === 'bn') {
        $translations = warafy_get_translations();
        if (isset($translations[$text]) && !empty($translations[$text])) {
            return $translations[$text];
        }
    }
    
    return $text;
}

// Handle language switch via GET parameter
add_action('init', 'warafy_handle_language_switch');
function warafy_handle_language_switch() {
    if (isset($_GET['lang'])) {
        $lang = sanitize_text_field($_GET['lang']);
        if (in_array($lang, ['en', 'bn'])) {
            setcookie('warafy_language', $lang, time() + 365 * 24 * 60 * 60, '/');
            $_COOKIE['warafy_language'] = $lang; // Update current request
            
            // Redirect to remove query param
            $redirect_url = remove_query_arg('lang');
            wp_redirect($redirect_url);
            exit;
        }
    }
}

/* Comments and Reviews System */

// Create custom tables for comments and reviews
add_action('init', 'warafy_create_comment_review_tables');
function warafy_create_comment_review_tables() {
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    
    // Comments table
    $comments_table = $wpdb->prefix . 'warafy_product_comments';
    $sql_comments = "CREATE TABLE IF NOT EXISTS $comments_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        product_id bigint(20) NOT NULL,
        user_id bigint(20) NULL,
        user_name varchar(100) NULL,
        user_email varchar(100) NULL,
        comment_text text NOT NULL,
        comment_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        status varchar(20) DEFAULT 'approved' NOT NULL,
        ip_address varchar(45) NULL,
        PRIMARY KEY  (id),
        KEY product_id (product_id),
        KEY user_id (user_id),
        KEY status (status)
    ) $charset_collate;";
    
    // Reviews table
    $reviews_table = $wpdb->prefix . 'warafy_product_reviews';
    $sql_reviews = "CREATE TABLE IF NOT EXISTS $reviews_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        product_id bigint(20) NOT NULL,
        user_id bigint(20) NOT NULL,
        rating int(1) NOT NULL,
        review_text text NOT NULL,
        review_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        status varchar(20) DEFAULT 'approved' NOT NULL,
        helpful_count int DEFAULT 0,
        PRIMARY KEY  (id),
        KEY product_id (product_id),
        KEY user_id (user_id),
        KEY status (status)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_comments);
    dbDelta($sql_reviews);
}

// Check if user has purchased the product
function warafy_user_purchased_product($user_id, $product_id) {
    if (!$user_id) return false;
    
    $orders = wc_get_orders([
        'customer_id' => $user_id,
        'status' => ['completed', 'processing'],
        'limit' => -1,
    ]);
    
    foreach ($orders as $order) {
        foreach ($order->get_items() as $item) {
            if ($item->get_product_id() == $product_id || $item->get_variation_id() == $product_id) {
                return true;
            }
        }
    }
    
    return false;
}

// AJAX handler for submitting comments
add_action('wp_ajax_warafy_submit_comment', 'warafy_submit_comment');
add_action('wp_ajax_nopriv_warafy_submit_comment', 'warafy_submit_comment');
function warafy_submit_comment() {
    global $wpdb;
    
    // Debug logging
    error_log('Warafy Comment Submission: ' . print_r($_POST, true));
    
    $nonce = sanitize_text_field($_POST['nonce']);
    if (!wp_verify_nonce($nonce, 'warafy_comment_nonce')) {
        error_log('Warafy Comment: Invalid nonce');
        wp_send_json_error(['message' => 'Security check failed.']);
        wp_die();
    }
    
    $product_id = intval($_POST['product_id']);
    $comment_text = sanitize_textarea_field($_POST['comment_text']);
    
    if (empty($product_id) || empty($comment_text)) {
        error_log('Warafy Comment: Missing required fields');
        wp_send_json_error(['message' => 'Please fill in all required fields.']);
        wp_die();
    }
    
    if (strlen($comment_text) > 1000) {
        error_log('Warafy Comment: Comment too long');
        wp_send_json_error(['message' => 'Comment is too long. Maximum 1000 characters allowed.']);
        wp_die();
    }
    
    $user_id = get_current_user_id();
    $user_name = '';
    $user_email = '';
    
    if (!$user_id) {
        $user_name = sanitize_text_field($_POST['user_name']);
        $user_email = sanitize_email($_POST['user_email']);
        
        if (empty($user_name) || empty($user_email)) {
            error_log('Warafy Comment: Missing user info for guest');
            wp_send_json_error(['message' => 'Please provide your name and email.']);
            wp_die();
        }
        
        if (!is_email($user_email)) {
            error_log('Warafy Comment: Invalid email');
            wp_send_json_error(['message' => 'Please provide a valid email address.']);
            wp_die();
        }
    } else {
        $user = get_userdata($user_id);
        $user_name = $user->display_name;
        $user_email = $user->user_email;
    }
    
    $table = $wpdb->prefix . 'warafy_product_comments';
    $result = $wpdb->insert(
        $table,
        [
            'product_id' => $product_id,
            'user_id' => $user_id,
            'user_name' => $user_name,
            'user_email' => $user_email,
            'comment_text' => $comment_text,
            'comment_date' => current_time('mysql'),
            'ip_address' => $_SERVER['REMOTE_ADDR']
        ],
        ['%d', '%d', '%s', '%s', '%s', '%s', '%s']
    );
    
    if ($result === false) {
        error_log('Warafy Comment: Database error - ' . $wpdb->last_error);
        wp_send_json_error(['message' => 'Database error. Please try again.']);
        wp_die();
    }
    
    error_log('Warafy Comment: Success');
    wp_send_json_success(['message' => 'Comment posted successfully!']);
    wp_die();
}

// AJAX handler for submitting reviews
add_action('wp_ajax_warafy_submit_review', 'warafy_submit_review');
function warafy_submit_review() {
    global $wpdb;
    
    $nonce = sanitize_text_field($_POST['nonce']);
    if (!wp_verify_nonce($nonce, 'warafy_review_nonce')) {
        wp_send_json_error(['message' => 'Security check failed']);
    }
    
    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_send_json_error(['message' => 'You must be logged in to submit a review']);
    }
    
    $product_id = intval($_POST['product_id']);
    $rating = intval($_POST['rating']);
    $review_text = sanitize_textarea_field($_POST['review_text']);
    
    // Validate rating
    if ($rating < 1 || $rating > 5) {
        wp_send_json_error(['message' => 'Invalid rating']);
    }
    
    // Validate review text
    if (empty($review_text)) {
        wp_send_json_error(['message' => 'Review cannot be empty']);
    }
    
    if (strlen($review_text) > 2000) {
        wp_send_json_error(['message' => 'Review is too long (max 2000 characters)']);
    }
    
    // Check if user purchased the product
    if (!warafy_user_purchased_product($user_id, $product_id)) {
        wp_send_json_error(['message' => 'You can only review products you have purchased']);
    }
    
    // Check if user already reviewed this product
    $table = $wpdb->prefix . 'warafy_product_reviews';
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $table WHERE product_id = %d AND user_id = %d",
        $product_id, $user_id
    ));
    
    if ($existing) {
        wp_send_json_error(['message' => 'You have already reviewed this product']);
    }
    
    // Insert review
    $result = $wpdb->insert(
        $table,
        [
            'product_id' => $product_id,
            'user_id' => $user_id,
            'rating' => $rating,
            'review_text' => $review_text,
            'review_date' => current_time('mysql'),
            'status' => 'approved'
        ],
        ['%d', '%d', '%d', '%s', '%s', '%s']
    );
    
    if ($result === false) {
        wp_send_json_error(['message' => 'Failed to submit review']);
    }
    
    wp_send_json_success(['message' => 'Review submitted successfully']);
}

// Get comments for a product
function warafy_get_product_comments($product_id, $limit = 10, $offset = 0) {
    global $wpdb;
    
    $table = $wpdb->prefix . 'warafy_product_comments';
    $comments = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table WHERE product_id = %d AND status = 'approved' 
         ORDER BY comment_date DESC LIMIT %d OFFSET %d",
        $product_id, $limit, $offset
    ));
    
    return $comments;
}

// Get reviews for a product
function warafy_get_product_reviews($product_id, $limit = 10, $offset = 0) {
    global $wpdb;
    
    $table = $wpdb->prefix . 'warafy_product_reviews';
    $reviews = $wpdb->get_results($wpdb->prepare(
        "SELECT r.*, u.display_name as user_name, u.user_email as user_email 
         FROM $table r 
         LEFT JOIN {$wpdb->users} u ON r.user_id = u.ID 
         WHERE r.product_id = %d AND r.status = 'approved' 
         ORDER BY r.review_date DESC LIMIT %d OFFSET %d",
        $product_id, $limit, $offset
    ));
    
    return $reviews;
}

// Get comment and review counts
function warafy_get_comment_count($product_id) {
    global $wpdb;
    
    $table = $wpdb->prefix . 'warafy_product_comments';
    $count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE product_id = %d AND status = 'approved'",
        $product_id
    ));
    
    return intval($count);
}

function warafy_get_review_count($product_id) {
    global $wpdb;
    
    $table = $wpdb->prefix . 'warafy_product_reviews';
    $count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE product_id = %d AND status = 'approved'",
        $product_id
    ));
    
    return intval($count);
}

// Get average rating
function warafy_get_average_rating($product_id) {
    global $wpdb;
    
    $table = $wpdb->prefix . 'warafy_product_reviews';
    $avg_rating = $wpdb->get_var($wpdb->prepare(
        "SELECT AVG(rating) FROM $table WHERE product_id = %d AND status = 'approved'",
        $product_id
    ));
    
    return $avg_rating ? round($avg_rating, 1) : 0;
}

// AJAX handler for loading comments
add_action('wp_ajax_warafy_load_comments', 'warafy_load_comments');
add_action('wp_ajax_nopriv_warafy_load_comments', 'warafy_load_comments');
function warafy_load_comments() {
    $product_id = intval($_POST['product_id']);
    $comments = warafy_get_product_comments($product_id);
    
    ob_start();
    if ($comments) :
        foreach ($comments as $comment) :
            $user_display_name = $comment->user_id ? get_the_author_meta('display_name', $comment->user_id) : $comment->user_name;
            $comment_date = date('F j, Y', strtotime($comment->comment_date));
    ?>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 warafy-comment-card">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center warafy-user-avatar">
                            <span class="text-primary font-semibold text-sm"><?php echo strtoupper(substr($user_display_name, 0, 1)); ?></span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white"><?php echo esc_html($user_display_name); ?></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo $comment_date; ?></p>
                        </div>
                    </div>
                </div>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed"><?php echo esc_html($comment->comment_text); ?></p>
            </div>
    <?php
        endforeach;
    else :
    ?>
        <p class="text-gray-500 dark:text-gray-400 text-center py-8">No comments yet. Be the first to comment!</p>
    <?php
    endif;
    
    $html = ob_get_clean();
    wp_send_json_success(['html' => $html]);
}

// AJAX handler for loading reviews
add_action('wp_ajax_warafy_load_reviews', 'warafy_load_reviews');
add_action('wp_ajax_nopriv_warafy_load_reviews', 'warafy_load_reviews');
function warafy_load_reviews() {
    $product_id = intval($_POST['product_id']);
    $reviews = warafy_get_product_reviews($product_id);
    
    ob_start();
    if ($reviews) :
        foreach ($reviews as $review) :
            $review_date = date('F j, Y', strtotime($review->review_date));
    ?>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 warafy-review-card">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center warafy-user-avatar">
                            <span class="text-primary font-semibold text-sm"><?php echo strtoupper(substr($review->user_name, 0, 1)); ?></span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white"><?php echo esc_html($review->user_name); ?></p>
                            <div class="flex items-center gap-2">
                                <div class="flex text-yellow-500">
                                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                                        <span class="material-symbols-outlined text-xs <?php echo $i <= $review->rating ? 'filled' : ''; ?>" style="<?php echo $i <= $review->rating ? 'font-variation-settings: \'FILL\' 1;' : ''; ?>" data-icon="star"></span>
                                    <?php endfor; ?>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo $review_date; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed"><?php echo esc_html($review->review_text); ?></p>
            </div>
    <?php
        endforeach;
    else :
    ?>
        <p class="text-gray-500 dark:text-gray-400 text-center py-8">No reviews yet. Be the first to review!</p>
    <?php
    endif;
    
    $html = ob_get_clean();
    wp_send_json_success(['html' => $html]);
}

// Address Management AJAX Handlers
add_action('wp_ajax_delete_address', 'warafy_delete_address');
function warafy_delete_address() {
    if (!wp_verify_nonce($_POST['nonce'], 'delete_address_nonce')) {
        wp_send_json_error('Invalid nonce');
    }
    
    $user_id = get_current_user_id();
    $address_id = intval($_POST['address_id']);
    $addresses = get_user_meta($user_id, 'shipping_addresses', true) ?: array();
    
    if (isset($addresses[$address_id])) {
        unset($addresses[$address_id]);
        update_user_meta($user_id, 'shipping_addresses', $addresses);
        delete_user_meta($user_id, 'shipping_address_' . $address_id);
        
        // Update default if needed
        $default_address_id = get_user_meta($user_id, 'default_shipping_address', true);
        if ($default_address_id == $address_id) {
            delete_user_meta($user_id, 'default_shipping_address');
        }
        
        wp_send_json_success('Address deleted');
    } else {
        wp_send_json_error('Address not found');
    }
}

add_action('wp_ajax_set_default_address', 'warafy_set_default_address');
function warafy_set_default_address() {
    if (!wp_verify_nonce($_POST['nonce'], 'set_default_address_nonce')) {
        wp_send_json_error('Invalid nonce');
    }
    
    $user_id = get_current_user_id();
    $address_id = intval($_POST['address_id']);
    $addresses = get_user_meta($user_id, 'shipping_addresses', true) ?: array();
    
    if (isset($addresses[$address_id])) {
        update_user_meta($user_id, 'default_shipping_address', $address_id);
        wp_send_json_success('Default address updated');
    } else {
        wp_send_json_error('Address not found');
    }
}

// ==============================================
// YouTube Video Field for Products
// ==============================================

// Add YouTube Video field to General tab
add_action('woocommerce_product_options_general_product_data', 'warafy_add_youtube_video_field');
function warafy_add_youtube_video_field() {
    echo '<div class="options_group">';
    
    woocommerce_wp_text_input(array(
        'id'          => '_warafy_youtube_url',
        'label'       => __('YouTube Video URL', 'warafy'),
        'placeholder' => 'https://www.youtube.com/watch?v=VIDEO_ID',
        'desc_tip'    => true,
        'description' => __('Enter the YouTube video URL. Supported formats: youtube.com/watch?v=ID or youtu.be/ID. Leave empty if no video.', 'warafy'),
        'type'        => 'url',
        'wrapper_class' => 'show_if_simple show_if_variable show_if_grouped show_if_external'
    ));
    
    echo '<p class="form-field" style="padding-left: 12px; margin-top: 0;">
            <span class="description" style="font-style: italic; color: #666;">
                ' . __('💡 Tip: If a YouTube URL is provided, a "Video Gallery" section will appear on the product page below the image gallery.', 'warafy') . '
            </span>
          </p>';
          
    echo '</div>';
}

// Save YouTube Video field
add_action('woocommerce_process_product_meta', 'warafy_save_youtube_video_field');
function warafy_save_youtube_video_field($post_id) {
    if (isset($_POST['_warafy_youtube_url'])) {
        $youtube_url = sanitize_url($_POST['_warafy_youtube_url']);
        update_post_meta($post_id, '_warafy_youtube_url', $youtube_url);
    }
}

// Helper function to extract YouTube video ID from URL
function warafy_get_youtube_video_id($url) {
    if (empty($url)) {
        return false;
    }
    
    $video_id = false;
    
    // Match youtube.com/watch?v=VIDEO_ID
    if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
        $video_id = $matches[1];
    }
    // Match youtu.be/VIDEO_ID
    elseif (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
        $video_id = $matches[1];
    }
    // Match youtube.com/embed/VIDEO_ID
    elseif (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
        $video_id = $matches[1];
    }
    // Match youtube.com/v/VIDEO_ID
    elseif (preg_match('/youtube\.com\/v\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
        $video_id = $matches[1];
    }
    
    return $video_id;
}

// Get YouTube embed URL (privacy-enhanced mode)
function warafy_get_youtube_embed_url($url) {
    $video_id = warafy_get_youtube_video_id($url);
    if ($video_id) {
        return 'https://www.youtube-nocookie.com/embed/' . $video_id;
    }
    return false;
}

// Get YouTube thumbnail URL
function warafy_get_youtube_thumbnail($url, $quality = 'maxresdefault') {
    $video_id = warafy_get_youtube_video_id($url);
    if ($video_id) {
        // Try maxresdefault first, fallback to hqdefault
        return 'https://img.youtube.com/vi/' . $video_id . '/' . $quality . '.jpg';
    }
    return false;
}

// Force custom My Account template for WooCommerce account page
add_filter('template_include', function($template) {
    if (function_exists('is_account_page') && is_account_page()) {
        $custom = get_stylesheet_directory() . '/page-my-account-simple.php';
        if (file_exists($custom)) return $custom;
    }
    
    $request_uri = $_SERVER['REQUEST_URI'];
    
    if (strpos($request_uri, '/login') !== false) {
        $custom = get_stylesheet_directory() . '/page-login.php';
        if (file_exists($custom)) return $custom;
    }
    
    if (strpos($request_uri, '/register') !== false) {
        $custom = get_stylesheet_directory() . '/page-register.php';
        if (file_exists($custom)) return $custom;
    }
    
    if (strpos($request_uri, '/forgot-password') !== false) {
        $custom = get_stylesheet_directory() . '/page-forgot-password.php';
        if (file_exists($custom)) return $custom;
    }

    return $template;
}, 999);

// Early redirect for auth pages to ensure themed templates are used
add_action('template_redirect', function() {
    $request_uri = $_SERVER['REQUEST_URI'];
    
    // Skip if in admin
    if (is_admin()) return;
    
    if (strpos($request_uri, '/login') !== false && !is_user_logged_in()) {
        $template = get_stylesheet_directory() . '/page-login.php';
        if (file_exists($template)) {
            include $template;
            exit;
        }
    }
    
    if (strpos($request_uri, '/register') !== false && !is_user_logged_in()) {
        $template = get_stylesheet_directory() . '/page-register.php';
        if (file_exists($template)) {
            include $template;
            exit;
        }
    }
    
    if (strpos($request_uri, '/forgot-password') !== false && !is_user_logged_in()) {
        $template = get_stylesheet_directory() . '/page-forgot-password.php';
        if (file_exists($template)) {
            include $template;
            exit;
        }
    }
});

// Handle password change via AJAX
add_action('wp_ajax_warafy_change_password', 'warafy_change_password_handler');
function warafy_change_password_handler() {
    check_ajax_referer('warafy_change_password', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Not logged in']);
    }
    
    $user = wp_get_current_user();
    $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        wp_send_json_error(['message' => 'All fields are required']);
    }
    
    if (!wp_check_password($current_password, $user->data->user_pass, $user->ID)) {
        wp_send_json_error(['message' => 'Current password is incorrect']);
    }
    
    if ($new_password !== $confirm_password) {
        wp_send_json_error(['message' => 'New passwords do not match']);
    }
    
    if (strlen($new_password) < 6) {
        wp_send_json_error(['message' => 'Password must be at least 6 characters']);
    }
    
    wp_set_password($new_password, $user->ID);
    
    // Re-login the user
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID);
    
    wp_send_json_success(['message' => 'Password changed successfully']);
}

// Custom Login URL
add_filter('login_url', function($login_url, $redirect, $force_reauth) {
    return home_url('/login/') . ($redirect ? '?redirect_to=' . urlencode($redirect) : '');
}, 999, 3);

// Custom Register URL
add_filter('register_url', function($register_url) {
    return home_url('/register/');
}, 999);

// Custom Lost Password URL
add_filter('lostpassword_url', function($lostpassword_url, $redirect) {
    return home_url('/forgot-password/') . ($redirect ? '?redirect_to=' . urlencode($redirect) : '');
}, 999, 2);

// Override WooCommerce catalog ordering to ensure proper styling
function warafy_woocommerce_catalog_ordering() {
    if ( ! wc_get_loop_prop( 'is_paginated' ) || ! woocommerce_products_will_display() ) {
        return;
    }
    
    $orderby                 = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
    $show_default_orderby    = 'menu_order' === apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
    $catalog_orderby_options = apply_filters( 'woocommerce_catalog_orderby', array(
        'menu_order' => __( 'Default sorting', 'woocommerce' ),
        'popularity' => __( 'Sort by popularity', 'woocommerce' ),
        'rating'     => __( 'Sort by average rating', 'woocommerce' ),
        'date'       => __( 'Sort by latest', 'woocommerce' ),
        'price'      => __( 'Sort by price: low to high', 'woocommerce' ),
        'price-desc' => __( 'Sort by price: high to low', 'woocommerce' ),
    ) );

    $default_orderby = wc_get_loop_prop( 'is_search' ) ? 'relevance' : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
    $orderby         = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : $default_orderby;

    if ( wc_get_loop_prop( 'is_search' ) ) {
        $catalog_orderby_options = array_merge( array( 'relevance' => __( 'Relevance', 'woocommerce' ) ), $catalog_orderby_options );

        unset( $catalog_orderby_options['menu_order'] );
        if ( 'menu_order' === $orderby ) {
            $orderby = 'relevance';
        }
    }

    if ( ! $show_default_orderby ) {
        unset( $catalog_orderby_options['menu_order'] );
    }

    if ( ! wc_review_ratings_enabled() ) {
        unset( $catalog_orderby_options['rating'] );
    }

    if ( ! array_key_exists( $orderby, $catalog_orderby_options ) ) {
        $orderby = current( array_keys( $catalog_orderby_options ) );
    }

    wc_get_template( 'loop/orderby.php', array(
        'catalog_orderby_options' => $catalog_orderby_options,
        'orderby'                 => $orderby,
        'show_default_orderby'    => $show_default_orderby,
    ) );
}
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
add_action( 'woocommerce_before_shop_loop', 'warafy_woocommerce_catalog_ordering', 30 );

// Add inline styles to ensure dropdown text is black
function warafy_add_sort_dropdown_inline_styles() {
    echo '<style>
        /* Aggressive styles to force sort dropdown text to be black */
        html body .woocommerce-ordering select,
        html body select[name="orderby"],
        .woocommerce-ordering select,
        select[name="orderby"],
        select.orderby {
            color: #000000 !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
        }
        
        html body .woocommerce-ordering select option,
        html body select[name="orderby"] option,
        .woocommerce-ordering select option,
        select[name="orderby"] option,
        select.orderby option {
            color: #000000 !important;
            background-color: #ffffff !important;
        }
        
        /* Dark mode styles */
        html body.dark .woocommerce-ordering select,
        html body.dark select[name="orderby"],
        body.dark .woocommerce-ordering select,
        body.dark select[name="orderby"],
        .dark .woocommerce-ordering select,
        .dark select[name="orderby"] {
            color: #d1d5db !important;
        }
        
        html body.dark .woocommerce-ordering select option,
        html body.dark select[name="orderby"] option,
        body.dark .woocommerce-ordering select option,
        body.dark select[name="orderby"] option,
        .dark .woocommerce-ordering select option,
        .dark select[name="orderby"] option {
            color: #d1d5db !important;
            background-color: #1f2937 !important;
        }
        
        /* Override any WooCommerce styles */
        .woocommerce .woocommerce-ordering select,
        .woocommerce-page .woocommerce-ordering select {
            color: #000000 !important;
        }
        
        .woocommerce .woocommerce-ordering select option,
        .woocommerce-page .woocommerce-ordering select option {
            color: #000000 !important;
            background-color: #ffffff !important;
        }
        
        .dark .woocommerce .woocommerce-ordering select,
        .dark .woocommerce-page .woocommerce-ordering select {
            color: #d1d5db !important;
        }
        
        .dark .woocommerce .woocommerce-ordering select option,
        .dark .woocommerce-page .woocommerce-ordering select option {
            color: #d1d5db !important;
            background-color: #1f2937 !important;
        }
    </style>';
}
add_action( 'wp_head', 'warafy_add_sort_dropdown_inline_styles', 9999 );
