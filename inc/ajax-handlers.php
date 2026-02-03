<?php
/**
 * AJAX Handlers
 * All AJAX request handlers
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handle wishlist AJAX requests
 */
function warafy_wishlist_ajax_handler() {
    // Your AJAX handler code here
    wp_die();
}
add_action('wp_ajax_warafy_wishlist', 'warafy_wishlist_ajax_handler');
add_action('wp_ajax_nopriv_warafy_wishlist', 'warafy_wishlist_ajax_handler');
