<?php
/**
 * WooCommerce Functions
 * Checkout, orders, cart, payment customizations
 */

if (!defined('ABSPATH')) {
    exit;
}

// Set default sort order to Latest
function warafy_default_catalog_orderby( $sort_by ) {
    return 'date';
}
add_filter( 'woocommerce_default_catalog_orderby', 'warafy_default_catalog_orderby' );

// Ensure the query actually uses date sorting if no other sort is specified
function warafy_pre_get_posts_product_order( $query ) {
    if ( ! is_admin() && $query->is_main_query() && ( is_shop() || is_product_category() || is_product_tag() ) ) {
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
    $allowed_fields = array(
        'billing_first_name',
        'billing_address_1',
        'billing_phone',
        'billing_email',
        'order_comments'
    );
    
    foreach ($fields['billing'] as $key => $field) {
        if (!in_array($key, $allowed_fields)) {
            unset($fields['billing'][$key]);
        }
    }
    
    $fields['shipping'] = array();
    
    $fields['billing']['billing_first_name']['label'] = 'Name';
    $fields['billing']['billing_first_name']['required'] = true;
    $fields['billing']['billing_first_name']['class'] = array('form-row-wide');
    $fields['billing']['billing_first_name']['placeholder'] = 'Enter your full name';
    
    $fields['billing']['billing_address_1']['label'] = 'Address';
    $fields['billing']['billing_address_1']['required'] = true;
    $fields['billing']['billing_address_1']['class'] = array('form-row-wide');
    $fields['billing']['billing_address_1']['placeholder'] = 'Street address, apartment, suite, etc.';
    
    $fields['billing']['billing_phone']['label'] = 'Mobile Number';
    $fields['billing']['billing_phone']['required'] = true;
    $fields['billing']['billing_phone']['class'] = array('form-row-wide');
    $fields['billing']['billing_phone']['placeholder'] = 'Enter your mobile number';
    
    $fields['billing']['billing_email']['label'] = 'Email Address';
    $fields['billing']['billing_email']['required'] = false;
    $fields['billing']['billing_email']['class'] = array('form-row-wide');
    $fields['billing']['billing_email']['placeholder'] = 'Enter your email (optional)';
    
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
    
    $existing_number = $order->get_meta('_warafy_order_number');
    if ($existing_number) return;
    
    $custom_number = warafy_generate_unique_order_number();
    
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
    $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
    if ( array_key_exists( 'cod', $available_gateways ) ) {
        return 'cod';
    } else {
        return key( $available_gateways );
    }
}

// AJAX Handler for getting cart data for button state updates
add_action('wp_ajax_warafy_get_cart', 'warafy_get_cart');
add_action('wp_ajax_nopriv_warafy_get_cart', 'warafy_get_cart');

function warafy_get_cart() {
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
            
            $count = $cart->get_cart_contents_count();
            $fragments = [];
            
            $fragments['.cart-count'] = '<span class="cart-count">' . $count . '</span>';
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
    $youtube_url = isset($_POST['_warafy_youtube_url']) ? sanitize_url($_POST['_warafy_youtube_url']) : '';
    update_post_meta($post_id, '_warafy_youtube_url', $youtube_url);
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
