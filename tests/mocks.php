<?php
/**
 * Mock WordPress environment for testing
 */

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

// Mocking WordPress functions used in bengali-language.php
if (!function_exists('add_action')) {
    function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1) {}
}

if (!function_exists('add_filter')) {
    function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1) {}
}

if (!function_exists('__')) {
    function __($text, $domain = 'default') { return $text; }
}

if (!function_exists('_e')) {
    function _e($text, $domain = 'default') { echo $text; }
}

if (!function_exists('esc_attr')) {
    function esc_attr($text) { return htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); }
}

if (!function_exists('esc_textarea')) {
    function esc_textarea($text) { return htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); }
}

if (!function_exists('get_template_directory')) {
    function get_template_directory() {
        return getenv('TEMPLATE_DIR') ?: __DIR__;
    }
}

if (!function_exists('woocommerce_wp_text_input')) {
    function woocommerce_wp_text_input($args) {}
}

if (!function_exists('get_post_meta')) {
    function get_post_meta($post_id, $key = '', $single = false) { return ''; }
}

if (!function_exists('get_the_ID')) {
    function get_the_ID() { return 1; }
}

if (!function_exists('wp_editor')) {
    function wp_editor($content, $editor_id, $settings = array()) {}
}

if (!function_exists('get_term_meta')) {
    function get_term_meta($term_id, $key = '', $single = false) { return ''; }
}

if (!function_exists('admin_url')) {
    function admin_url($path = '', $scheme = 'admin') { return 'http://localhost/wp-admin/' . $path; }
}

if (!function_exists('wp_create_nonce')) {
    function wp_create_nonce($action = -1) { return 'mock-nonce'; }
}

if (!function_exists('wp_verify_nonce')) {
    function wp_verify_nonce($nonce, $action = -1) { return true; }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($str) { return $str; }
}

if (!function_exists('get_term_by')) {
    function get_term_by($field, $value, $taxonomy = '', $output = OBJECT, $filter = 'raw') { return null; }
}

if (!function_exists('is_wp_error')) {
    function is_wp_error($thing) { return false; }
}

if (!function_exists('wp_send_json_success')) {
    function wp_send_json_success($data = null, $status_code = null) {
        echo json_encode(['success' => true, 'data' => $data]);
        // We don't want to exit during tests unless specifically testing AJAX
        // exit;
    }
}

if (!function_exists('wp_send_json_error')) {
    function wp_send_json_error($data = null, $status_code = null) {
        echo json_encode(['success' => false, 'data' => $data]);
        // exit;
    }
}

if (!function_exists('sanitize_textarea_field')) {
    function sanitize_textarea_field($str) { return $str; }
}

if (!function_exists('wp_kses_post')) {
    function wp_kses_post($data) { return $data; }
}

if (!function_exists('get_post_type')) {
    function get_post_type($post = null) { return 'product'; }
}

if (!function_exists('is_admin')) {
    function is_admin() { return false; }
}

if (!function_exists('update_post_meta')) {
    function update_post_meta($post_id, $meta_key, $meta_value, $prev_value = '') { return true; }
}

if (!function_exists('update_term_meta')) {
    function update_term_meta($term_id, $meta_key, $meta_value, $prev_value = '') { return true; }
}

if (!function_exists('remove_query_arg')) {
    function remove_query_arg($key, $query = false) { return 'http://localhost/'; }
}

if (!function_exists('wp_redirect')) {
    function wp_redirect($location, $status = 302, $x_redirect_by = 'WordPress') { return true; }
}
