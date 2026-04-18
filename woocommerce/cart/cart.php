<?php
/**
 * Cart Page - ModernStore Design
 * Desktop: Two-column layout with cart items and order summary
 * Mobile: Clean minimal layout with sticky checkout footer
 */

defined('ABSPATH') || exit;
?>


        
        <?php if (WC()->cart->is_empty()) : ?>
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
        <?php else : ?>
            
            <!-- Desktop: Page Title (Hidden on Mobile) -->
            <div class="mb-6 hidden lg:block">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white"><?php echo __t('Your Shopping Cart'); ?></h1>
                <p class="mt-1 text-gray-500 dark:text-gray-400">
                    <?php
                    $item_count = WC()->cart->get_cart_contents_count();
                    if ($item_count > 1) {
                        echo sprintf(__t('You have %d items in your cart.'), $item_count);
                    } else {
                        echo sprintf(__t('You have %d item in your cart.'), $item_count);
                    }
                    ?>
                </p>
            </div>

            <!-- Desktop & Mobile Layout -->
            <div class="flex flex-col gap-4 lg:gap-8 lg:flex-row lg:items-start">
                
                <!-- Cart Items Section -->
                <div class="flex-1">
                    <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
                        <?php do_action('woocommerce_before_cart_table'); ?>

                        <!-- Desktop: Cart Items in Single Container -->
                        <div class="hidden lg:block rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-background-dark">
                            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
                                    $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                                    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                                    if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) :
                                        $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                                ?>
                                    <!-- Desktop Cart Item -->
                                    <div class="flex flex-col gap-6 p-6 sm:flex-row sm:items-center">
                                        <div class="w-full sm:w-32 sm:flex-shrink-0">
                                            <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-lg overflow-hidden">
                                                <?php
                                                $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('woocommerce_thumbnail'), $cart_item, $cart_item_key);
                                                if (!$product_permalink) {
                                                    echo $thumbnail;
                                                } else {
                                                    printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail);
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="flex flex-1 flex-col justify-between gap-4 sm:flex-row sm:items-center">
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                    <?php
                                                    if (!$product_permalink) {
                                                        echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key));
                                                    } else {
                                                        echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                                                    }
                                                    ?>
                                                </h3>
                                                <?php 
                                                $product_meta = wc_get_formatted_cart_item_data($cart_item);
                                                if ($product_meta) : ?>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        <?php echo strip_tags($product_meta); ?>
                                                    </p>
                                                <?php endif; ?>
                                                <p class="mt-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                                                    <?php echo $_product->is_in_stock() ? __t('In Stock') : __t('Out of Stock'); ?>
                                                </p>
                                            </div>
                                            <div class="flex items-center justify-between gap-4">
                                                <div class="flex items-center gap-2">
                                                    <?php
                                                    if ($_product->is_sold_individually()) {
                                                        echo '<span class="w-8 text-center font-medium">1</span>';
                                                        echo sprintf('<input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
                                                    } else {
                                                        ?>
                                                        <button type="button"
                                                                class="qty-minus flex h-8 w-8 items-center justify-center rounded-md border border-gray-300 bg-gray-300 text-white hover:bg-gray-400 dark:border-gray-600 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500"
                                                                data-cart-key="<?php echo $cart_item_key; ?>">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/></svg>
                                                        </button>
                                                        <input type="number"
                                                               name="cart[<?php echo $cart_item_key; ?>][qty]"
                                                               value="<?php echo $cart_item['quantity']; ?>"
                                                               min="0"
                                                               max="<?php echo $_product->get_max_purchase_quantity(); ?>"
                                                               class="qty-input w-8 text-center border-0 bg-transparent font-medium text-gray-900 dark:text-white focus:outline-none appearance-none [-moz-appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                                               data-cart-key="<?php echo $cart_item_key; ?>" />
                                                        <button type="button"
                                                                class="qty-plus flex h-8 w-8 items-center justify-center rounded-md border border-gray-300 bg-gray-300 text-white hover:bg-gray-400 dark:border-gray-600 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500"
                                                                data-cart-key="<?php echo $cart_item_key; ?>"
                                                                data-max="<?php echo $_product->get_max_purchase_quantity(); ?>">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                                        </button>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <p class="w-20 text-right text-lg font-semibold text-gray-900 dark:text-white">
                                                    <?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
                                                </p>
                                                <button type="button"
                                                        class="text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400"
                                                        onclick="window.location.href='<?php echo esc_url(wc_get_cart_remove_url($cart_item_key)); ?>'"
                                                        title="<?php echo __t('Remove'); ?>">
                                                    <span class="material-symbols-outlined" data-icon="delete"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </div>
                        </div>

                        <!-- Mobile: Clean Minimal Cart Items -->
                        <div class="lg:hidden bg-white dark:bg-background-dark">
                            <div class="flex flex-col gap-4 p-4">
                                <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
                                    $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                                    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                                    if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) :
                                        $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                                ?>
                                    <!-- Mobile Cart Item -->
                                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-background-dark">
                                        <div class="flex items-start gap-4">
                                            <!-- Product Image -->
                                            <div class="w-24 flex-shrink-0">
                                                <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-md overflow-hidden">
                                                    <?php
                                                    $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('woocommerce_thumbnail'), $cart_item, $cart_item_key);
                                                    if (!$product_permalink) {
                                                        echo $thumbnail;
                                                    } else {
                                                        printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail);
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            
                                            <!-- Product Details -->
                                            <div class="flex flex-1 flex-col justify-between self-stretch">
                                                <div>
                                                    <div class="flex items-start justify-between">
                                                        <h3 class="font-semibold text-gray-900 dark:text-white pr-2">
                                                            <?php
                                                            if (!$product_permalink) {
                                                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key));
                                                            } else {
                                                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                                                            }
                                                            ?>
                                                        </h3>
                                                        <button type="button"
                                                                class="text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400"
                                                                onclick="window.location.href='<?php echo esc_url(wc_get_cart_remove_url($cart_item_key)); ?>'"
                                                                title="<?php echo __t('Remove'); ?>">
                                                            <span class="material-symbols-outlined text-xl" data-icon="delete"></span>
                                                        </button>
                                                    </div>
                                                    <?php 
                                                    $product_meta = wc_get_formatted_cart_item_data($cart_item);
                                                    if ($product_meta) : ?>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                                            <?php echo strip_tags($product_meta); ?>
                                                        </p>
                                                    <?php endif; ?>
                                                    <p class="mt-2 text-lg font-bold text-gray-900 dark:text-white">
                                                        <?php echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); ?>
                                                    </p>
                                                </div>
                                                
                                                <!-- Quantity Controls -->
                                                <div class="flex items-center gap-3">
                                                    <?php
                                                    if ($_product->is_sold_individually()) {
                                                        echo '<span class="w-8 text-center font-medium">1</span>';
                                                        echo sprintf('<input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
                                                    } else {
                                                        ?>
                                                        <button type="button"
                                                                class="qty-minus flex h-8 w-8 items-center justify-center rounded-full border border-gray-300 bg-gray-300 text-white hover:bg-gray-400 dark:border-gray-600 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500"
                                                                data-cart-key="<?php echo $cart_item_key; ?>">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/></svg>
                                                        </button>
                                                        <input type="number"
                                                               name="cart[<?php echo $cart_item_key; ?>][qty]"
                                                               value="<?php echo $cart_item['quantity']; ?>"
                                                               min="0"
                                                               max="<?php echo $_product->get_max_purchase_quantity(); ?>"
                                                               class="qty-input w-8 text-center font-medium border-0 bg-transparent p-0 focus:outline-none appearance-none [-moz-appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                                               data-cart-key="<?php echo $cart_item_key; ?>" />
                                                        <button type="button"
                                                                class="qty-plus flex h-8 w-8 items-center justify-center rounded-full border border-gray-300 bg-gray-300 text-white hover:bg-gray-400 dark:border-gray-600 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500"
                                                                data-cart-key="<?php echo $cart_item_key; ?>"
                                                                data-max="<?php echo $_product->get_max_purchase_quantity(); ?>">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                                        </button>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </div>
                        </div>

                        <?php do_action('woocommerce_cart_contents'); ?>
                        <?php do_action('woocommerce_cart_actions'); ?>
                        <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                    </form>

                    <!-- Continue Shopping Link (Desktop Only) -->
                    <div class="mt-4 text-right hidden lg:block">
                        <a class="text-sm font-medium text-primary hover:underline" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php echo __t('Continue Shopping'); ?></a>
                    </div>
                </div>

                <!-- Order Summary Sidebar (Desktop Only) -->
                <aside class="hidden lg:block w-full lg:w-80 lg:flex-shrink-0">
                    <div class="sticky top-24 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-background-dark">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white"><?php echo __t('Order Summary'); ?></h3>
                        <div class="mt-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-600 dark:text-gray-300"><?php echo __t('Subtotal'); ?></p>
                                <p class="font-semibold text-gray-900 dark:text-white"><?php echo WC()->cart->get_cart_subtotal(); ?></p>
                            </div>
                            <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
                                <?php do_action('woocommerce_cart_totals_before_shipping'); ?>
                                <?php wc_cart_totals_shipping_html(); ?>
                                <?php do_action('woocommerce_cart_totals_after_shipping'); ?>
                            <?php elseif (WC()->cart->needs_shipping() && 'yes' === get_option('woocommerce_enable_shipping_calc')) : ?>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-gray-600 dark:text-gray-300"><?php echo __t('Shipping'); ?></p>
                                    <p class="font-semibold text-gray-900 dark:text-white"><?php echo __t('Calculated at checkout'); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <?php foreach (WC()->cart->get_fees() as $fee) : ?>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-gray-600 dark:text-gray-300"><?php echo esc_html($fee->name); ?></p>
                                    <p class="font-semibold text-gray-900 dark:text-white"><?php wc_cart_totals_fee_html($fee); ?></p>
                                </div>
                            <?php endforeach; ?>

                            <?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) : ?>
                                <?php if ('itemized' === get_option('woocommerce_tax_total_display')) : ?>
                                    <?php foreach (WC()->cart->get_tax_totals() as $code => $tax) : ?>
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm text-gray-600 dark:text-gray-300"><?php echo esc_html($tax->label); ?></p>
                                            <p class="font-semibold text-gray-900 dark:text-white"><?php echo wp_kses_post($tax->formatted_amount); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm text-gray-600 dark:text-gray-300"><?php echo esc_html(WC()->countries->tax_or_vat()); ?></p>
                                        <p class="font-semibold text-gray-900 dark:text-white"><?php echo wc_price(WC()->cart->get_taxes_total()); ?></p>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class="my-6 h-px w-full bg-gray-200 dark:bg-gray-700"></div>
                        <div class="flex items-center justify-between">
                            <p class="text-lg font-bold text-gray-900 dark:text-white"><?php echo __t('Total'); ?></p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white"><?php echo WC()->cart->get_total(); ?></p>
                        </div>
                        <a href="<?php echo esc_url(wc_get_checkout_url()); ?>"
                           class="mt-6 flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-black text-white text-base font-bold shadow-lg hover:bg-gray-800">
                            <span><?php echo __t('Proceed to Checkout'); ?></span>
                        </a>
                        <p class="mt-4 text-center text-xs text-gray-500 dark:text-gray-400"><?php echo __t('Shipping and taxes calculated at checkout.'); ?></p>
                    </div>
                </aside>
            </div>

        <?php endif; ?>

        <?php do_action('woocommerce_after_cart'); ?>


<!-- Mobile Fixed Bottom Checkout Bar -->
<?php if (!WC()->cart->is_empty()) : ?>
<footer class="lg:hidden sticky bottom-0 z-40 w-full border-t border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-background-dark">
    <div class="container mx-auto">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo __t('Total Price'); ?></p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo WC()->cart->get_total(); ?></p>
            </div>
            <a href="<?php echo esc_url(wc_get_checkout_url()); ?>"
               class="flex w-auto cursor-pointer items-center justify-center gap-2 rounded-full h-12 px-6 bg-black text-white text-base font-bold shadow-lg hover:bg-gray-800">
                <span><?php echo __t('Checkout'); ?></span>
                <span class="material-symbols-outlined" data-icon="arrow_forward"></span>
            </a>
        </div>
    </div>
</footer>
<!-- Add padding to prevent content from being hidden behind fixed footer -->
<div class="lg:hidden h-20"></div>
<?php endif; ?>

<!-- Auto-submit form when quantity changes -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.woocommerce-cart-form');
    if (!form) return;

    function updateQty(btn, change) {
        const cartKey = btn.dataset.cartKey;
        const inputs = document.querySelectorAll(`input.qty-input[data-cart-key="${cartKey}"]`);
        if (!inputs.length) return;

        const mainInput = inputs[0];
        const currentVal = parseInt(mainInput.value) || 0;
        const max = parseInt(btn.dataset.max) || 999;
        const min = 0;

        let newVal = currentVal + change;
        if (newVal < min) newVal = min;
        if (max > 0 && newVal > max) newVal = max;

        if (newVal !== currentVal) {
            inputs.forEach(input => input.value = newVal);
            
            // Check for update button and trigger it to ensure WooCommerce processes the change
            const updateBtn = form.querySelector('button[name="update_cart"]');
            if (updateBtn) {
                updateBtn.removeAttribute('disabled');
                updateBtn.click();
            } else {
                // Fallback: Create hidden input for update_cart action
                let hidden = form.querySelector('input[name="update_cart"]');
                if (!hidden) {
                    hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'update_cart';
                    hidden.value = '1';
                    form.appendChild(hidden);
                }
                form.submit();
            }
        }
    }

    document.querySelectorAll('.qty-plus').forEach(btn => {
        btn.addEventListener('click', function() {
            updateQty(this, 1);
        });
    });

    document.querySelectorAll('.qty-minus').forEach(btn => {
        btn.addEventListener('click', function() {
            updateQty(this, -1);
        });
    });
});
</script>


