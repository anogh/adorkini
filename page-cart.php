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
                                    <span class="absolute top-2 left-2 px-2 py-1 bg-red-500 text-white text-xs font-bold rounded"><?php echo __t('Sale!'); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex flex-col flex-1 justify-between gap-4">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">
                                        <a href="<?php echo get_permalink(); ?>" class="hover:text-primary transition-colors line-clamp-1"><?php echo get_the_title(); ?></a>
                                    </h3>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo $product->get_price_html(); ?></p>
                                </div>
                                <div>
                                    <button class="add-to-cart-btn flex items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary/10 text-primary text-sm font-bold hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 transition-colors w-full" data-product-id="<?php echo $product->get_id(); ?>">
                                        <span class="material-symbols-outlined text-sm add-icon mr-2" data-icon="add_shopping_cart"></span>
                                        <span class="add-text truncate"><?php echo __t('Add'); ?></span>
                                        <span class="material-symbols-outlined text-sm added-icon hidden mr-2" data-icon="check"></span>
                                        <span class="added-text hidden truncate"><?php echo __t('Added'); ?></span>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add to Cart functionality for New in Store section
    document.addEventListener('click', function(e) {
        if (e.target.closest('.add-to-cart-btn')) {
            e.preventDefault();
            e.stopPropagation();

            const button = e.target.closest('.add-to-cart-btn');
            const productId = button.dataset.productId;

            // Prevent multiple clicks
            if (button.classList.contains('adding')) return;

            button.classList.add('adding');
            button.disabled = true;

            const addIcon = button.querySelector('.add-icon');
            const addedIcon = button.querySelector('.added-icon');
            const addText = button.querySelector('.add-text');
            const addedText = button.querySelector('.added-text');

            // Show loading state
            if (addIcon) {
                addIcon.innerHTML = '<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" stroke-dasharray="31.416" stroke-dashoffset="31.416"><animate attributeName="stroke-dashoffset" from="31.416" to="0" dur="1s" repeatCount="indefinite"/></circle>';
            }
            if (addText) addText.textContent = '<?php echo __t('Adding...'); ?>';

            // Use WooCommerce's built-in AJAX
            if (typeof wc_add_to_cart_params !== 'undefined' && typeof jQuery !== 'undefined') {
                const data = {
                    action: 'woocommerce_add_to_cart',
                    product_id: productId,
                    quantity: 1
                };

                jQuery.post(wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'), data, function(response) {
                    if (response.error && response.product_url) {
                        window.location = response.product_url;
                        return;
                    }

                    if (!response.error) {
                        // Show success state
                        if (addIcon) addIcon.classList.add('hidden');
                        if (addText) addText.classList.add('hidden');
                        if (addedIcon) addedIcon.classList.remove('hidden');
                        if (addedText) addedText.classList.remove('hidden');

                        // Change button to green
                        button.style.backgroundColor = '#16a34a';
                        button.style.color = 'white';
                        button.title = '<?php echo __t('Added to Cart'); ?>';

                        // Update cart fragments
                        jQuery('body').trigger('added_to_cart', [response.fragments, response.cart_hash]);

                        // Update cart count
                        const cartCountElements = document.querySelectorAll('.cart-count');
                        if (cartCountElements.length > 0) {
                            const currentCount = parseInt(cartCountElements[0].textContent || '0');
                            const newCount = currentCount + 1;
                            cartCountElements.forEach(el => {
                                el.textContent = newCount;
                                el.classList.remove('hidden');
                            });
                        }

                        // Reset button after 2 seconds
                        setTimeout(function() {
                            button.classList.remove('adding');
                            button.disabled = false;
                            if (addIcon) addIcon.classList.remove('hidden');
                            if (addText) addText.classList.remove('hidden');
                            if (addedIcon) addedIcon.classList.add('hidden');
                            if (addedText) addedText.classList.add('hidden');
                            if (addText) addText.textContent = '<?php echo __t('Add'); ?>';
                            button.style.backgroundColor = '';
                            button.style.color = '';
                        }, 2000);
                    } else {
                        // Error state
                        button.classList.remove('adding');
                        button.disabled = false;
                        if (addText) addText.textContent = '<?php echo __t('Add'); ?>';
                        alert('<?php echo __t('Error adding to cart. Please try again.'); ?>');
                    }
                }).fail(function() {
                    button.classList.remove('adding');
                    button.disabled = false;
                    if (addText) addText.textContent = '<?php echo __t('Add'); ?>';
                    alert('<?php echo __t('Error adding to cart. Please try again.'); ?>');
                });
            } else {
                // Fallback: redirect to product page
                window.location.href = button.closest('.warafy-new-in-store')?.querySelector('a[href*="product"]')?.href || '/';
            }
        }
    });

    // Wishlist functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.warafy-wishlist-btn')) {
            e.preventDefault();
            e.stopPropagation();

            const button = e.target.closest('.warafy-wishlist-btn');
            const productId = button.dataset.productId;

            // Toggle wishlist
            if (button.classList.contains('in-wishlist')) {
                // Remove from wishlist
                button.classList.remove('in-wishlist');
                button.style.backgroundColor = '';
                button.style.color = '';
                button.querySelector('svg')?.setAttribute('fill', 'none');
            } else {
                // Add to wishlist
                button.classList.add('in-wishlist');
                button.style.backgroundColor = '#ef4444';
                button.style.color = 'white';
                button.querySelector('svg')?.setAttribute('fill', 'currentColor');
            }

            // AJAX call to update wishlist
            if (typeof jQuery !== 'undefined') {
                jQuery.ajax({
                    url: warafy_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'warafy_toggle_wishlist',
                        product_id: productId,
                        nonce: warafy_ajax.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update wishlist count
                            const wishlistCountElements = document.querySelectorAll('.wishlist-count');
                            wishlistCountElements.forEach(el => {
                                el.textContent = response.data.count;
                                el.classList.toggle('hidden', response.data.count === 0);
                            });
                        }
                    }
                });
            }
        }
    });
});
</script>
