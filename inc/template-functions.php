<?php
/**
 * Template Functions
 * Custom template overrides and helpers
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Force My Account page to use our custom template
 */
function warafy_force_my_account_template($template) {
    if (is_page('my-account')) {
        return get_template_directory() . '/page-my-account.php';
    }
    return $template;
}
add_filter('template_include', 'warafy_force_my_account_template');
