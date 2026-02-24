<?php
/**
 * Minimal WordPress mocks for testing
 */

define('ABSPATH', dirname(__DIR__) . '/');

$template_directory = dirname(__DIR__);

function add_action($tag, $callback, $priority = 10, $accepted_args = 1) {}
function add_filter($tag, $callback, $priority = 10, $accepted_args = 1) {}
function __($text, $domain = 'default') { return $text; }
function _e($text, $domain = 'default') { echo $text; }
function get_template_directory() {
    global $template_directory;
    return $template_directory;
}
function wp_verify_nonce($nonce, $action = -1) { return true; }
function get_term_by($field, $value, $taxonomy = '', $output = 'OBJECT', $filter = 'raw') { return (object)[]; }
function get_term_meta($term_id, $key = '', $single = false) { return ''; }
function get_post_meta($post_id, $key = '', $single = false) { return ''; }
function sanitize_text_field($str) { return $str; }
function sanitize_textarea_field($str) { return $str; }
function wp_kses_post($data) { return $data; }
function update_post_meta($post_id, $meta_key, $meta_value, $prev_value = '') { return true; }
function update_term_meta($term_id, $meta_key, $meta_value, $prev_value = '') { return true; }
function esc_attr($text) { return $text; }
function esc_textarea($text) { return $text; }
function admin_url($path = '', $scheme = 'admin') { return $path; }
function wp_create_nonce($action = -1) { return 'nonce'; }
function wp_send_json_error($data = null, $status_code = null) { return; }
function wp_send_json_success($data = null, $status_code = null) { return; }
function is_admin() { return false; }
function get_post_type($post = null) { return 'product'; }
function get_the_ID() { return 1; }
function wp_redirect($location, $status = 302, $x_redirect_by = 'WordPress') { return true; }
function remove_query_arg($keys, $query = false) { return ''; }
function woocommerce_wp_text_input($args) {}
function wp_editor($content, $editor_id, $settings = array()) {}
