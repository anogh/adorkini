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
<main class="flex-grow bg-white dark:bg-background-dark" style="display: block !important; visibility: visible !important; opacity: 1 !important; position: relative !important; z-index: 1 !important;">
    <div class="container mx-auto px-4 py-4" style="display: block !important; visibility: visible !important; opacity: 1 !important; position: relative !important; z-index: 1 !important;">
        <style>
            /* Override any CSS that might be hiding mobile content */
            @media (max-width: 1023px) {
                main,
                main > div,
                main .woocommerce,
                main .container {
                    display: block !important;
                    visibility: visible !important;
                    opacity: 1 !important;
                    position: relative !important;
                    z-index: 1 !important;
                    width: 100% !important;
                    height: auto !important;
                    max-width: none !important;
                    min-width: auto !important;
                    overflow: visible !important;
                    margin: 0 !important;
                    padding: inherit !important;
                }
            }
        </style>
        
        <?php echo do_shortcode('[woocommerce_my_account]'); ?>
    </div>
</main>

<?php get_footer(); ?>
