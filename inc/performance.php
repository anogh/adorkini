<?php
/**
 * Performance Optimizations
 * 
 * @package Adorkini
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Inject Critical CSS in Head
 * Loads the contents of assets/css/critical.css directly into <style> tags.
 */
function adorkini_critical_css() {
    $critical_file = get_template_directory() . '/assets/css/critical.css';
    if ( file_exists( $critical_file ) ) {
        echo '<style id="adorkini-critical-css">';
        include $critical_file;
        echo '</style>';
    }
}
add_action( 'wp_head', 'adorkini_critical_css', 1 );

/**
 * Add Preloader HTML
 * 
 * Adds the preloader markup to the footer (or header) to ensure it's available immediately.
 */
function adorkini_add_preloader() {
    get_template_part( 'template-parts/preloader' );
}
add_action( 'wp_body_open', 'adorkini_add_preloader' );

/**
 * Image Lazy Loading
 * 
 * Ensure native lazy loading is active and add custom classes if needed.
 */
function adorkini_lazy_load_attributes( $attr, $attachment, $size ) {
    if ( ! isset( $attr['loading'] ) ) {
        $attr['loading'] = 'lazy';
    }
    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'adorkini_lazy_load_attributes', 10, 3 );
