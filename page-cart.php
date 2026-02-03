<?php
/**
 * Template Name: Cart Page
 *
 * This template is automatically used for the page with slug 'cart'.
 */

defined( 'ABSPATH' ) || exit;

get_header(); ?>

<!-- Cart Content -->
<main class="flex-grow bg-white dark:bg-background-dark lg:bg-background-light" style="display: block !important; visibility: visible !important; opacity: 1 !important; position: relative !important; z-index: 1 !important;">
    <div class="container mx-auto px-4 lg:px-6 py-4 lg:py-8" style="display: block !important; visibility: visible !important; opacity: 1 !important; position: relative !important; z-index: 1 !important;">
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
        <div class="woocommerce">
            <?php
            while ( have_posts() ) :
                the_post();
                the_content();
            endwhile;
            ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
