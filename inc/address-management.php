<?php
/**
 * Address Management
 * Delete address, set default address AJAX handlers
 */

if (!defined('ABSPATH')) {
    exit;
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
