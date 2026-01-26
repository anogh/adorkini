<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * @package Adorkini
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );
?>

<div class="container mx-auto px-4 py-8">

    <?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
		<h1 class="woocommerce-products-header__title page-title text-2xl font-bold mb-6"><?php woocommerce_page_title(); ?></h1>
	<?php endif; ?>

    <?php
    if ( woocommerce_product_loop() ) {
        
        do_action( 'woocommerce_before_shop_loop' );
        ?>

        <!-- Product Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            <?php
            if ( wc_get_loop_prop( 'total' ) ) {
                while ( have_posts() ) {
                    the_post();
                    wc_get_template_part( 'content', 'product' );
                }
            }
            ?>
        </div>

        <?php
        do_action( 'woocommerce_after_shop_loop' );

    } else {
        do_action( 'woocommerce_no_products_found' );
    }
    ?>
</div>

<?php
get_footer( 'shop' );
