<?php
/**
 * Order received (thank you) page
 *
 * @package WooCommerce/Templates
 * @version 8.7.0
 */

defined('ABSPATH') || exit;

$warafy_has_header = did_action('get_header');

if (!$warafy_has_header) {
    get_header();
}
?>

<div class="bg-gradient-to-br from-gray-50 via-purple-50/30 to-gray-100 dark:from-background-dark dark:via-gray-900 dark:to-background-dark min-h-[70vh] py-12 lg:py-20">
    <div class="container mx-auto px-4 lg:px-10">
        <div class="max-w-5xl mx-auto space-y-10">
            <div>
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-3">
                    <span class="material-symbols-outlined text-4xl lg:text-5xl text-green-500" data-icon="celebration"></span>
                    Order Confirmation
                </h1>
                <p class="text-gray-600 dark:text-gray-300 max-w-3xl">Relax, we've got it from here. You'll receive status updates soon and can review your purchase details below.</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between max-w-3xl mx-auto">
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-12 h-12 rounded-full bg-green-500 text-white flex items-center justify-center mb-2 shadow-lg">
                            <span class="material-symbols-outlined" data-icon="check"></span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Cart</span>
                    </div>
                    <div class="flex-1 h-1 bg-green-500 mx-2"></div>
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-12 h-12 rounded-full bg-green-500 text-white flex items-center justify-center mb-2 shadow-lg">
                            <span class="material-symbols-outlined" data-icon="check"></span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Checkout</span>
                    </div>
                    <div class="flex-1 h-1 bg-green-500 mx-2"></div>
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-12 h-12 rounded-full bg-primary text-white flex items-center justify-center mb-2 shadow-lg animate-pulse">
                            <span class="material-symbols-outlined" data-icon="task_alt"></span>
                        </div>
                        <span class="text-sm font-semibold text-primary">Complete</span>
                    </div>
                </div>
            </div>

            <?php if ($order) : ?>
                <?php do_action('woocommerce_before_thankyou', $order->get_id()); ?>

                <?php if ($order->has_status('failed')) : ?>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-red-100 dark:border-red-800 p-8 text-center">
                        <span class="material-symbols-outlined text-red-500 text-6xl mb-4" data-icon="error"></span>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Payment Failed</h2>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">Unfortunately your order cannot be processed because the bank/merchant has declined your transaction. Please attempt your purchase again.</p>
                        <div class="flex flex-wrap items-center justify-center gap-4">
                            <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-black text-white font-semibold hover:bg-gray-800 transition">
                                <span class="material-symbols-outlined text-base" data-icon="refresh"></span>
                                Pay Again
                            </a>
                            <a href="<?php echo esc_url($order->get_cancel_order_url()); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                Cancel Order
                            </a>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2 space-y-6">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-8">
                                <div class="flex items-start gap-4">
                                    <div class="w-14 h-14 rounded-full bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-300 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-3xl" data-icon="verified"></span>
                                    </div>
                                    <div>
                                        <p class="text-sm uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Success</p>
                                        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-3">Your order is confirmed!</h2>
                                        <p class="text-gray-700 dark:text-gray-200 text-lg leading-relaxed">Your order number is <span class="font-semibold text-primary">#<?php echo esc_html($order->get_order_number()); ?></span>. Thank you for ordering with us &mdash; we'll notify you once it ships.</p>
                                    </div>
                                </div>

                                <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                                    <div class="space-y-2">
                                        <p class="text-gray-500 dark:text-gray-400">Placed on</p>
                                        <p class="text-gray-900 dark:text-white font-semibold"><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></p>
                                    </div>
                                    <?php if ($order->get_billing_email()) : ?>
                                        <div class="space-y-2">
                                            <p class="text-gray-500 dark:text-gray-400">Confirmation sent to</p>
                                            <p class="text-gray-900 dark:text-white font-semibold break-words"><?php echo esc_html($order->get_billing_email()); ?></p>
                                        </div>
                                    <?php endif; ?>
                                    <div class="space-y-2">
                                        <p class="text-gray-500 dark:text-gray-400">Order total</p>
                                        <p class="text-gray-900 dark:text-white font-semibold text-xl"><?php echo wp_kses_post($order->get_formatted_order_total()); ?></p>
                                    </div>
                                    <?php if ($order->get_payment_method_title()) : ?>
                                        <div class="space-y-2">
                                            <p class="text-gray-500 dark:text-gray-400">Payment method</p>
                                            <p class="text-gray-900 dark:text-white font-semibold"><?php echo wp_kses_post($order->get_payment_method_title()); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Customer Information Section -->
                                <div class="mt-8 p-6 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                        <span class="material-symbols-outlined" data-icon="person"></span>
                                        Customer Information
                                    </h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400 mb-1">Name</p>
                                            <p class="text-gray-900 dark:text-white font-medium"><?php echo esc_html($order->get_formatted_billing_full_name()); ?></p>
                                        </div>
                                        <?php if ($order->get_billing_phone()) : ?>
                                            <div>
                                                <p class="text-gray-500 dark:text-gray-400 mb-1">Phone</p>
                                                <p class="text-gray-900 dark:text-white font-medium"><?php echo esc_html($order->get_billing_phone()); ?></p>
                                            </div>
                                        <?php endif; ?>
                                        <div class="sm:col-span-2">
                                            <p class="text-gray-500 dark:text-gray-400 mb-1">Address</p>
                                            <p class="text-gray-900 dark:text-white font-medium"><?php echo esc_html($order->get_formatted_billing_address()); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-8 flex flex-wrap gap-4">
                                    <?php 
                                    // Get the custom 5-digit order number
                                    $custom_number = $order->get_meta('_warafy_order_number');
                                    if ($custom_number) {
                                        $public_order_url = home_url("/order-details/{$custom_number}/");
                                    } else {
                                        // Fallback to order key
                                        $order_key = $order->get_order_key();
                                        $public_order_url = home_url("/order-details/{$order_key}/");
                                    }
                                    ?>
                                    <a href="<?php echo esc_url($public_order_url); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-black text-white font-semibold hover:bg-gray-800 transition">
                                        <span class="material-symbols-outlined text-base" data-icon="visibility"></span>
                                        View order details
                                    </a>
                                    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-100 font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <span class="material-symbols-outlined text-base" data-icon="storefront"></span>
                                        Continue shopping
                                    </a>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-8">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary to-yellow-600 text-white flex items-center justify-center shadow-lg">
                                        <span class="material-symbols-outlined text-2xl" data-icon="inventory_2"></span>
                                    </div>
                                    Items in your order
                                </h3>
                                <div class="space-y-6">
                                    <?php foreach ($order->get_items() as $item_id => $item) :
                                        $product = $item->get_product();
                                        ?>
                                        <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                                            <div class="w-14 h-14 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-200 flex items-center justify-center flex-shrink-0">
                                                <?php if ($product && $product->get_image()) : ?>
                                                    <?php echo $product->get_image('thumbnail', ['class' => 'w-full h-full object-cover rounded-xl']); ?>
                                                <?php else : ?>
                                                    <span class="material-symbols-outlined" data-icon="shopping_bag"></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-semibold text-gray-900 dark:text-white"><?php echo esc_html($item->get_name()); ?></p>
                                                <?php if ($product && $product->get_sku()) : ?>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">SKU: <?php echo esc_html($product->get_sku()); ?></p>
                                                <?php endif; ?>
                                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Quantity: <?php echo esc_html($item->get_quantity()); ?></p>
                                                <?php if ($item->get_meta('_reduced_stock')) : ?>
                                                    <p class="text-sm text-orange-500 dark:text-orange-400 mt-1">Stock reduced: <?php echo esc_html($item->get_meta('_reduced_stock')); ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-semibold text-gray-900 dark:text-white"><?php echo wp_kses_post($order->get_formatted_line_subtotal($item)); ?></p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo wp_kses_post(wc_price($item->get_total() / $item->get_quantity())); ?> each</p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-1">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-8 sticky top-8">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Order summary</h3>
                                <div class="space-y-3 text-sm">
                                    <?php foreach ($order->get_order_item_totals() as $key => $total) : ?>
                                        <div class="flex justify-between text-gray-700 dark:text-gray-200 <?php echo esc_attr('order-total' === $key ? 'text-base font-bold pt-3 border-t border-gray-200 dark:border-gray-700 mt-3' : ''); ?>">
                                            <span><?php echo esc_html($total['label']); ?></span>
                                            <span><?php echo wp_kses_post($total['value']); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <!-- Order Status -->
                                <div class="mt-6 p-4 rounded-xl bg-gray-100 dark:bg-gray-800/50">
                                    <div class="flex items-center gap-3">
                                        <span class="material-symbols-outlined text-primary" data-icon="info"></span>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Order Status</p>
                                            <p class="text-sm text-gray-700 dark:text-gray-300"><?php echo esc_html(wc_get_order_status_name($order->get_status())); ?></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if ($order->get_customer_note()) : ?>
                                    <div class="mt-6 p-4 rounded-xl bg-gray-50 dark:bg-gray-900 text-sm text-gray-700 dark:text-gray-200">
                                        <p class="font-semibold mb-2">Order note</p>
                                        <p><?php echo esc_html($order->get_customer_note()); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id()); ?>
                <?php do_action('woocommerce_thankyou', $order->get_id()); ?>
            <?php else : ?>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-8 text-center">
                    <span class="material-symbols-outlined text-6xl text-primary mb-4" data-icon="info"></span>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Thank you!</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">We've received your order. If you need details, please contact support and we'll be happy to help.</p>
                    <div class="flex flex-wrap items-center justify-center gap-4">
                        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-black text-white font-semibold hover:bg-gray-800 transition">
                            <span class="material-symbols-outlined text-base" data-icon="storefront"></span>
                            Continue shopping
                        </a>
                        <a href="<?php echo esc_url(get_home_url()); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-100 font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <span class="material-symbols-outlined text-base" data-icon="home"></span>
                            Back to home
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
if (!$warafy_has_header) {
    get_footer();
}
?>
