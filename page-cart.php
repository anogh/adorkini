<?php
/**
 * Template Name: Cart Page
 * Modern Cart Design - No plugin content interference
 */

defined('ABSPATH') || exit;

get_header();

// Check if cart is empty
$is_cart_empty = WC()->cart->is_empty();
?>

<!-- Cart Content -->
<main class="flex-grow bg-white dark:bg-background-dark lg:bg-background-light">
    <div class="container mx-auto px-4 lg:px-6 py-4 lg:py-8">
        
        <?php if ($is_cart_empty) : ?>
            <!-- Empty Cart State -->
            <div class="empty-cart-container bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 lg:p-12 text-center max-w-2xl mx-auto mt-12">
                <div class="mb-6">
                    <span class="material-symbols-outlined text-8xl lg:text-9xl text-gray-300" style="font-size: 6rem;" data-icon="shopping_cart"></span>
                </div>
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-3"><?php echo __t('Your cart is empty'); ?></h2>
                <p class="text-gray-600 dark:text-gray-400 mb-8"><?php echo __t("Looks like you haven't added anything to your cart yet."); ?></p>
                <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" 
                   class="inline-flex items-center gap-2 bg-black text-white px-8 py-4 rounded-full font-semibold hover:bg-gray-800 transition-all shadow-lg">
                    <span class="material-symbols-outlined" data-icon="storefront"></span>
                    <?php echo __t('Continue Shopping'); ?>
                </a>
            </div>
            
            <!-- Custom New in Store Section -->
            <?php
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => 4,
                'post_status' => 'publish',
                'orderby' => 'rand',
                'meta_query' => array(
                    array(
                        'key' => '_stock_status',
                        'value' => 'instock',
                        'compare' => '='
                    )
                )
            );
            
            $products_query = new WP_Query($args);
            
            if ($products_query->have_posts()) :
            ?>
            <div class="warafy-new-in-store mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 text-center"><?php echo __t('New in store'); ?></h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php while ($products_query->have_posts()) : $products_query->the_post(); ?>
                        <?php global $product; ?>
                        <div class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-background-dark shadow-sm">
                            <div class="relative">
                                <a href="<?php echo get_permalink(); ?>" class="block w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-lg" style='background-image: url("<?php echo get_the_post_thumbnail_url($product->get_id(), 'woocommerce_thumbnail'); ?>");'></a>
                                <?php if ($product->is_on_sale()) : ?>
                                    <span class="absolute top-2 left-2 px-2 py-1 bg-red-500 text-white text-xs font-bold rounded">SALE</span>
                                <?php endif; ?>
                            </div>
                            <div class="flex flex-col flex-1 justify-between gap-4">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">
                                        <a href="<?php echo get_permalink(); ?>" class="hover:text-primary transition-colors line-clamp-1"><?php echo get_the_title(); ?></a>
                                    </h3>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo $product->get_price_html(); ?></p>
                                </div>
                                <div class="flex gap-2">
                                    <button class="add-to-cart-btn flex-1 flex items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary/10 text-primary text-sm font-bold hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 transition-colors" data-product-id="<?php echo $product->get_id(); ?>">
                                        <span class="material-symbols-outlined text-sm add-icon mr-2" data-icon="add_shopping_cart"></span>
                                        <span class="add-text truncate"><?php echo __t('Add'); ?></span>
                                        <span class="material-symbols-outlined text-sm added-icon hidden mr-2" data-icon="check"></span>
                                        <span class="added-text hidden truncate"><?php echo __t('Added'); ?></span>
                                    </button>
                                    <button class="warafy-wishlist-btn flex-none w-10 h-10 flex items-center justify-center rounded-lg bg-primary/10 text-primary hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 transition-colors" data-product-id="<?php echo $product->get_id(); ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php 
            wp_reset_postdata();
            endif;
            ?>
            
        <?php else : ?>
            <!-- Cart with Items -->
            <?php wc_get_template('cart/cart.php'); ?>
        <?php endif; ?>
        
    </div>
</main>

<?php get_footer(); ?>
