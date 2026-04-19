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
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-10 lg:w-12 h-10 lg:h-12 text-green-500">
                      <path d="M9.663 17h4.673M12 3v1m6.364 1.636-.707.707M21 12h-1M4 12H3m3.343-5.657-.707-.707m2.828 9.9a5 5 0 1 1 7.072 0l-.548.547A3.374 3.374 0 0 0 14 18.469V19a2 2 0 1 1-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    Order Confirmation
                </h1>
                <p class="text-gray-600 dark:text-gray-300 max-w-3xl">Relax, we've got it from here. You'll receive status updates soon and can review your purchase details below.</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between max-w-3xl mx-auto">
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-12 h-12 rounded-full bg-green-500 text-white flex items-center justify-center mb-2 shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                              <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Cart</span>
                    </div>
                    <div class="flex-1 h-1 bg-green-500 mx-2"></div>
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-12 h-12 rounded-full bg-green-500 text-white flex items-center justify-center mb-2 shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                              <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Checkout</span>
                    </div>
                    <div class="flex-1 h-1 bg-green-500 mx-2"></div>
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-12 h-12 rounded-full bg-primary text-white flex items-center justify-center mb-2 shadow-lg animate-pulse">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                              <polyline points="9 11 12 14 22 4"></polyline>
                              <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-primary">Complete</span>
                    </div>
                </div>
            </div>

            <?php if ($order) : ?>
                <?php do_action('woocommerce_before_thankyou', $order->get_id()); ?>

                <?php if ($order->has_status('failed')) : ?>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-red-100 dark:border-red-800 p-8 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-500 w-16 h-16 mx-auto mb-4">
                          <circle cx="12" cy="12" r="10"></circle>
                          <line x1="15" y1="9" x2="9" y2="15"></line>
                          <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Payment Failed</h2>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">Unfortunately your order cannot be processed because the bank/merchant has declined your transaction. Please attempt your purchase again.</p>
                        <div class="flex flex-wrap items-center justify-center gap-4">
                            <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-black text-white font-semibold hover:bg-gray-800 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                  <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                                  <path d="M3 3v5h5"></path>
                                </svg>
                                Pay Again
                            </a>
                            <a href="<?php echo esc_url($order->get_cancel_order_url()); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                  <line x1="18" y1="6" x2="6" y2="18"></line>
                                  <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                                Cancel Order
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
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8">
                                          <circle cx="12" cy="12" r="10"></circle>
                                          <path d="M9 12l2 2 4-4"></path>
                                        </svg>
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
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                          <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
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
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                          <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        View order details
                                    </a>
                                    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-100 font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                          <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                        </svg>
                                        Continue shopping
                                    </a>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-8">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary to-yellow-600 text-white flex items-center justify-center shadow-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                                          <path d="M3 9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"></path>
                                          <path d="M3 9l3-5h12l3 5"></path>
                                        </svg>
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
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                                                          <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                                          <line x1="3" y1="6" x2="21" y2="6"></line>
                                                          <path d="M16 10a4 4 0 0 1-8 0"></path>
                                                        </svg>
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
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-primary">
                                          <circle cx="12" cy="12" r="10"></circle>
                                          <line x1="12" y1="16" x2="12" y2="12"></line>
                                          <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                        </svg>
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
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-16 h-16 text-primary mx-auto mb-4">
                      <circle cx="12" cy="12" r="10"></circle>
                      <line x1="12" y1="16" x2="12" y2="12"></line>
                      <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Thank you!</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">We've received your order. If you need details, please contact support and we'll be happy to help.</p>
                    <div class="flex flex-wrap items-center justify-center gap-4">
                            <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-black text-white font-semibold hover:bg-gray-800 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                  <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                  <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                </svg>
                                Continue shopping
                            </a>
                            <a href="<?php echo esc_url(get_home_url()); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-100 font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                  <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                  <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                </svg>
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
