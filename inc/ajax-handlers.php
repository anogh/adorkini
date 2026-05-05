<?php

if (!defined('ABSPATH')) {
    exit;
}

function warafy_wishlist_ajax_handler() {
    wp_die();
}
add_action('wp_ajax_warafy_wishlist', 'warafy_wishlist_ajax_handler');
add_action('wp_ajax_nopriv_warafy_wishlist', 'warafy_wishlist_ajax_handler');

add_action('wp_ajax_warafy_update_cart_item', 'warafy_update_cart_item_ajax');
add_action('wp_ajax_nopriv_warafy_update_cart_item', 'warafy_update_cart_item_ajax');

function warafy_update_cart_item_ajax() {
    check_ajax_referer('warafy_cart_nonce', 'nonce');

    $cart_key = isset($_POST['cart_key']) ? sanitize_text_field(wp_unslash($_POST['cart_key'])) : '';
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

    if (empty($cart_key) || !function_exists('WC') || !WC()->cart) {
        wp_send_json_error(array('message' => 'Invalid request'));
    }

    $cart_item = WC()->cart->get_cart_item($cart_key);
    if (!$cart_item) {
        wp_send_json_error(array('message' => 'Item not found in cart'));
    }

    if ($quantity <= 0) {
        WC()->cart->remove_cart_item($cart_key);
        wp_send_json_success(array(
            'removed'  => true,
            'count'    => WC()->cart->get_cart_contents_count(),
            'subtotal' => WC()->cart->get_cart_subtotal(),
            'total'    => WC()->cart->get_total(),
            'is_empty' => WC()->cart->is_empty(),
        ));
    }

    $product = $cart_item['data'];
    $max_qty = $product->get_max_purchase_quantity();
    if ($max_qty > 0 && $quantity > $max_qty) {
        $quantity = $max_qty;
    }

    WC()->cart->set_quantity($cart_key, $quantity, true);

    $item_subtotal = WC()->cart->get_product_subtotal($product, $quantity);
    $item_price = WC()->cart->get_product_price($product);

    wp_send_json_success(array(
        'removed'      => false,
        'quantity'     => $quantity,
        'item_subtotal' => $item_subtotal,
        'item_price'   => $item_price,
        'count'        => WC()->cart->get_cart_contents_count(),
        'subtotal'     => WC()->cart->get_cart_subtotal(),
        'total'        => WC()->cart->get_total(),
        'is_empty'     => WC()->cart->is_empty(),
    ));
}

add_action('wp_ajax_warafy_remove_cart_item', 'warafy_remove_cart_item_ajax');
add_action('wp_ajax_nopriv_warafy_remove_cart_item', 'warafy_remove_cart_item_ajax');

function warafy_remove_cart_item_ajax() {
    check_ajax_referer('warafy_cart_nonce', 'nonce');

    $cart_key = isset($_POST['cart_key']) ? sanitize_text_field(wp_unslash($_POST['cart_key'])) : '';

    if (empty($cart_key) || !function_exists('WC') || !WC()->cart) {
        wp_send_json_error(array('message' => 'Invalid request'));
    }

    $removed = WC()->cart->remove_cart_item($cart_key);

    if ($removed) {
        wp_send_json_success(array(
            'removed'  => true,
            'count'    => WC()->cart->get_cart_contents_count(),
            'subtotal' => WC()->cart->get_cart_subtotal(),
            'total'    => WC()->cart->get_total(),
            'is_empty' => WC()->cart->is_empty(),
        ));
    }

    wp_send_json_error(array('message' => 'Failed to remove item'));
}
