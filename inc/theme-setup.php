<?php
/**
 * Theme Setup & Core Functions
 * Enqueue scripts, theme setup, widgets
 */

if (!defined('ABSPATH')) {
    exit;
}

function warafy_enqueue_scripts() {
    // Bengali Font Support
    wp_enqueue_style('google-fonts-bengali', 'https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap', array(), null);
    
    // Modern SVG Icons (Premium design alternative to Material Symbols font)
    wp_enqueue_style('material-symbols', get_template_directory_uri() . '/assets/css/modern-svg-icons.css', array(), '1.4.0');
    
    // Tailwind CDN
    wp_enqueue_script('tailwind', 'https://cdn.tailwindcss.com?plugins=forms,container-queries', array(), null, false);
    
    // Theme Styles
    $theme_version = wp_get_theme()->get( 'Version' );
    wp_enqueue_style('warafy-style', get_stylesheet_uri(), array(), $theme_version);

    // Tailwind Config
    wp_add_inline_script('tailwind', '
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "primary": "#137fec",
              "background-light": "#f6f7f8",
              "background-dark": "#101922",
            },
            fontFamily: {
              "display": ["Inter", "sans-serif"]
            },
            borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
          },
        },
      }
    ');

    // Enqueue product carousel script only on single product pages
    if (is_product()) {
        wp_enqueue_script('warafy-product-carousel', get_template_directory_uri() . '/assets/js/product-carousel.js', array(), '1.0.0', true);
        wp_enqueue_script('warafy-comments-reviews', get_template_directory_uri() . '/assets/js/comments-reviews.js', array(), '1.0.0', true);
        
        // Pass AJAX URL to JavaScript
        wp_localize_script('warafy-comments-reviews', 'warafy_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('warafy_ajax_nonce')
        ));
    }
    
    // Force My Account page to use our custom template
    add_filter('template_include', function($template) {
        if (is_page('my-account')) {
            $custom_template = get_stylesheet_directory() . '/page-my-account.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        return $template;
    }, 99);

    // Add hash handler for my-account page
    if (is_page('my-account')) {
        wp_add_inline_script('tailwind', '
            (function() {
                const handleHash = () => {
                    const hash = window.location.hash;
                    if (!hash) return;
                    
                    const hashValue = hash.substring(1);
                    const hashMap = {
                        "account-details": "personal-info",
                        "edit-account": "personal-info", 
                        "personal-info": "personal-info",
                        "orders": "orders",
                        "edit-address": "addresses",
                        "addresses": "addresses"
                    };

                    const view = hashMap[hashValue];
                    if (view) {
                        window.location.replace("' . home_url('/my-account') . '?view=" + view);
                    }
                };

                // Run immediately and also after DOM is ready
                handleHash();
                if (document.readyState === "loading") {
                    document.addEventListener("DOMContentLoaded", handleHash);
                } else {
                    handleHash();
                }
                window.addEventListener("hashchange", handleHash);
            })();
        ');
    }
}
add_action('wp_enqueue_scripts', 'warafy_enqueue_scripts');

function warafy_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('woocommerce');
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'warafy-modern'),
        'mobile' => __('Mobile Menu', 'warafy-modern'),
    ));
}
add_action('after_setup_theme', 'warafy_theme_setup');

// Register Shop Sidebar
function warafy_widgets_init() {
    register_sidebar( array(
        'name'          => esc_html__( 'Shop Sidebar', 'warafy-modern' ),
        'id'            => 'shop-sidebar',
        'description'   => esc_html__( 'Add widgets here to appear in your shop page sidebar.', 'warafy-modern' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s mb-8">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'warafy_widgets_init' );

// Create Categories Page on Theme Activation
function warafy_create_categories_page() {
    $existing_page = get_page_by_path('categories');
    
    if (!$existing_page) {
        $page_data = array(
            'post_title'    => 'Categories',
            'post_content'  => '<!-- This page uses the Categories List template -->',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_name'     => 'categories'
        );
        
        $page_id = wp_insert_post($page_data);
        
        if ($page_id && !is_wp_error($page_id)) {
            update_post_meta($page_id, '_wp_page_template', 'page-categories.php');
        }
    }
    
    flush_rewrite_rules();
}
add_action('after_setup_theme', 'warafy_create_categories_page');

// Create My Love Page on Theme Activation
function warafy_create_my_love_page() {
    $existing_page = get_page_by_path('my-love');
    
    if (!$existing_page) {
        $page_data = array(
            'post_title'    => 'My Love',
            'post_content'  => '<!-- This page uses the My Love template -->',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_name'     => 'my-love'
        );
        
        $page_id = wp_insert_post($page_data);
        
        if ($page_id && !is_wp_error($page_id)) {
            update_post_meta($page_id, '_wp_page_template', 'page-my-love.php');
        }
    }
    
    flush_rewrite_rules();
}
add_action('after_setup_theme', 'warafy_create_my_love_page');

// Also flush rules on theme switch
add_action('switch_theme', 'flush_rewrite_rules');

// Force page creation on admin init if it doesn't exist
function warafy_ensure_categories_page() {
    if (!get_page_by_path('categories')) {
        warafy_create_categories_page();
    }
}
add_action('admin_init', 'warafy_ensure_categories_page');

function warafy_ensure_my_love_page() {
    if (!get_page_by_path('my-love')) {
        warafy_create_my_love_page();
    }
}
add_action('admin_init', 'warafy_ensure_my_love_page');

// Create page immediately when this file is loaded (for testing)
if (!get_page_by_path('categories')) {
    warafy_create_categories_page();
}

if (!get_page_by_path('my-love')) {
    warafy_create_my_love_page();
}
