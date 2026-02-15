<?php
/**
 * Template Name: My Account Page
 *
 * This template is automatically used for the page with slug 'my-account'.
 */

defined( 'ABSPATH' ) || exit;

// Redirect if not logged in
if (!is_user_logged_in()) {
    wp_redirect(home_url('/login'));
    exit;
}

get_header(); ?>

<!-- Account Content -->
<main class="flex-grow bg-black dark:bg-background-dark" style="display: block !important; visibility: visible !important; opacity: 1 !important; position: relative !important; z-index: 1 !important;">
    <div class="container mx-auto px-4 py-4" style="display: block !important; visibility: visible !important; opacity: 1 !important; position: relative !important; z-index: 1 !important;">
        <style>
            /* Force dark mode on all content */
            * {
                background-color: #000000 !important;
                color: #ffffff !important;
            }
            
            h1, h2, h3, h4, h5, h6 {
                color: #ffffff !important;
            }
            
            a {
                color: #F5A623 !important;
            }
            
            button, .button, input[type="submit"] {
                background-color: #F5A623 !important;
                color: #ffffff !important;
            }
            
            input, textarea, select {
                background-color: #1a1a1a !important;
                border-color: #333333 !important;
                color: #ffffff !important;
            }
            
            table, th, td {
                border-color: #333333 !important;
            }
        </style>
        
        <?php 
        // Let WooCommerce render the account content normally
        wc_get_template('myaccount/my-account.php');
        ?>
    </div>
</main>

<?php get_footer(); ?>
