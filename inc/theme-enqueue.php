<?php
/**
 * Theme Enqueue Scripts & Styles
 * 
 * @package Adorkini
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function adorkini_scripts() {
    // Enqueue Tailwind CSS (Using CDN for Dev speed, can replace with local build later)
    wp_enqueue_style( 'adorkini-tailwind', 'https://cdn.tailwindcss.com', array(), null );

    // Enqueue Theme Styles
    wp_enqueue_style( 'adorkini-style', get_stylesheet_uri(), array(), ADORKINI_VERSION );

    // Enqueue Fonts
    // Inter + Noto Sans Bengali
    wp_enqueue_style( 'adorkini-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap', array(), null );

    // Enqueue Scripts
    wp_enqueue_script( 'adorkini-main', get_template_directory_uri() . '/assets/js/main.js', array(), ADORKINI_VERSION, true );

    // Localize Script for JS translations if needed
    wp_localize_script( 'adorkini-main', 'adorkiniData', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'lang' => adorkini_get_current_lang()
    ));

    // Threaded comments
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'adorkini_scripts' );
