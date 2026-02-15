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
            
            /* Force dark mode on WooCommerce my-account elements */
            .woocommerce-account,
            .woocommerce-account *,
            .woocommerce-MyAccount-content,
            .woocommerce-MyAccount-content *,
            .woocommerce-MyAccount-navigation,
            .woocommerce-MyAccount-navigation *,
            .woocommerce-MyAccount-orders,
            .woocommerce-MyAccount-orders *,
            .woocommerce-address-fields,
            .woocommerce-address-fields *,
            .u-columns,
            .u-columns *,
            .u-column1,
            .u-column1 *,
            .u-column2,
            .u-column2 * {
                background-color: #000000 !important;
                color: #ffffff !important;
                border-color: #333333 !important;
            }
            
            .woocommerce-MyAccount-navigation-link a {
                color: #cccccc !important;
            }
            
            .woocommerce-MyAccount-navigation-link.is-active a {
                color: #F5A623 !important;
                background-color: #1a1a1a !important;
            }
            
            .woocommerce-MyAccount-content a,
            .woocommerce-MyAccount-content button,
            .woocommerce-MyAccount-content input {
                color: #ffffff !important;
            }
            
            .woocommerce-MyAccount-content form h3,
            .woocommerce-MyAccount-content h2,
            .woocommerce-MyAccount-content h3 {
                color: #ffffff !important;
            }
            
            .woocommerce-MyAccount-content .woocommerce-FormRow label {
                color: #cccccc !important;
            }
            
            .woocommerce-MyAccount-content input[type="text"],
            .woocommerce-MyAccount-content input[type="email"],
            .woocommerce-MyAccount-content input[type="password"],
            .woocommerce-MyAccount-content input[type="tel"],
            .woocommerce-MyAccount-content textarea {
                background-color: #1a1a1a !important;
                border-color: #444444 !important;
                color: #ffffff !important;
            }
            
            .woocommerce-MyAccount-content button.button,
            .woocommerce-MyAccount-content input.button {
                background-color: #F5A623 !important;
                color: #ffffff !important;
            }
            
            table.woocommerce-MyAccount-orders th,
            table.woocommerce-MyAccount-orders td {
                color: #ffffff !important;
                background-color: #111111 !important;
            }
            
            table.woocommerce-MyAccount-orders th {
                background-color: #1a1a1a !important;
            }
        </style>
        
        <?php echo do_shortcode('[woocommerce_my_account]'); ?>
    </div>
</main>

<?php get_footer(); ?>
