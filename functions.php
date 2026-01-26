<?php
/**
 * Adorkini Theme Functions
 * 
 * @package Adorkini
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define Theme Constants
define( 'ADORKINI_VERSION', '1.0.0' );
define( 'ADORKINI_DIR', get_template_directory() );
define( 'ADORKINI_URI', get_template_directory_uri() );

/**
 * Theme Includes
 * 
 * The $adorkini_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed.
 */
$adorkini_includes = array(
	'inc/theme-setup.php',          // Theme setup and custom theme supports.
	'inc/performance.php',          // Performance optimizations (Critical CSS, Preload).
	'inc/svg-icons.php',            // SVG Icon helper functions.
	'inc/translation-system.php',   // Custom translation system.
	'inc/theme-enqueue.php',        // Enqueue scripts and styles.
    'inc/product-ranking.php',      // Product ranking logic.
    'inc/video-gallery.php',        // Product video gallery.
    'inc/custom-orders.php',        // Custom order numbers and guest access.
    'inc/checkout-customization.php' // Checkout simplifications.
);

foreach ( $adorkini_includes as $file ) {
	$filepath = ADORKINI_DIR . '/' . $file;
	if ( file_exists( $filepath ) ) {
		require_once $filepath;
	}
}
