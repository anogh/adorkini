<?php
/**
 * Theme Setup and Support
 *
 * @package Adorkini
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'adorkini_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 */
	function adorkini_setup() {
		// Make theme available for translation.
		load_theme_textdomain( 'adorkini', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// Let WordPress manage the document title.
		add_theme_support( 'title-tag' );

		// Enable support for Post Thumbnails on posts and pages.
		add_theme_support( 'post-thumbnails' );

		// Register Navigation Menus
		register_nav_menus(
			array(
				'menu-1' => esc_html__( 'Primary Menu', 'adorkini' ),
				'menu-mobile' => esc_html__( 'Mobile Menu', 'adorkini' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		// WooCommerce Support
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );

		// Disable default WooCommerce styles to use our own Tailwind-based styles
		add_filter( 'woocommerce_enqueue_styles', '__return_false' );
	}
endif;
add_action( 'after_setup_theme', 'adorkini_setup' );

/**
 * Remove default image sizes to save space and generation time if not needed.
 * But for now, we'll keep standard ones and maybe add custom ones later.
 */
