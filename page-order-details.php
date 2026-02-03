<?php
/**
 * Template Name: Public Order Details
 */

get_header();

// Get order ID from query variables (set by rewrite rules)
$custom_order_number = get_query_var('custom_order_number');
$order_id = get_query_var('order_id');
$order_key = get_query_var('order_key');

// Fallback: Check GET parameters
if (!$custom_order_number && !$order_id && !$order_key) {
    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    $order_key = isset($_GET['order_key']) ? sanitize_text_field($_GET['order_key']) : '';
    $custom_order_number = isset($_GET['custom_order_number']) ? sanitize_text_field($_GET['custom_order_number']) : '';
}

// Fallback: Try to parse URL path if still nothing found
if (!$order_id && !$order_key && !$custom_order_number) {
    // Try to get from URL path
    $url_path = strtok($_SERVER['REQUEST_URI'], '?');
    $path_parts = explode('/', trim($url_path, '/'));
    
    // Check if we're on order-details page
    // Path could be /order-details/12345/ or /index.php/order-details/12345/
    $identifier = '';
    foreach ($path_parts as $key => $part) {
        if ($part === 'order-details' && isset($path_parts[$key + 1])) {
            $identifier = $path_parts[$key + 1];
            break;
        }
    }
    
    if ($identifier) {
        if (is_numeric($identifier) && strlen($identifier) === 5) {
            // 5-digit custom order number
            $custom_order_number = $identifier;
        } elseif (is_numeric($identifier)) {
            // Regular order ID
            $order_id = intval($identifier);
        } else {
            // Order key
            $order_key = $identifier;
        }
    }
}

// Get order using the global function
$order = warafy_get_public_order($order_id, $order_key, $custom_order_number);
?>

<div class="bg-gradient-to-br from-gray-50 via-purple-50/30 to-gray-100 dark:from-background-dark dark:via-gray-900 dark:to-background-dark min-h-[70vh] py-12 lg:py-20">
    <div class="container mx-auto px-4 lg:px-10">
        <div class="max-w-5xl mx-auto space-y-10">
            <div>
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-3">
                    <span class="material-symbols-outlined text-4xl lg:text-5xl text-purple-600" data-icon="receipt_long"></span>
                    Order Details
                </h1>
                <p class="text-gray-600 dark:text-gray-300 max-w-3xl">View the order details below. Some information has been masked for privacy protection.</p>
            </div>

            <?php if ($order) : ?>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-8">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <p class="text-sm uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Order Number</p>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                <?php 
                                $custom_number = $order->get_meta('_warafy_order_number');
                                if ($custom_number) {
                                    echo '#' . esc_html($custom_number);
                                } else {
                                    echo '#' . esc_html($order->get_order_number());
                                }
                                ?>
                            </h2>
                        </div>
                        <div class="text-right">
                            <div class="p-3 rounded-xl bg-purple-50 dark:bg-purple-900/30">
                                <span class="material-symbols-outlined text-purple-600 dark:text-purple-300 text-2xl" data-icon="shopping_bag"></span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="space-y-2">
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Order Date</p>
                            <p class="text-gray-900 dark:text-white font-semibold"><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Status</p>
                            <p class="text-gray-900 dark:text-white font-semibold"><?php echo esc_html(wc_get_order_status_name($order->get_status())); ?></p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Total</p>
                            <p class="text-gray-900 dark:text-white font-semibold text-lg"><?php echo wp_kses_post($order->get_formatted_order_total()); ?></p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Payment</p>
                            <p class="text-gray-900 dark:text-white font-semibold"><?php echo wp_kses_post($order->get_payment_method_title()); ?></p>
                        </div>
                    </div>

                    <!-- Customer Information (Masked) -->
                    <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined" data-icon="person"></span>
                            Customer Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400 text-sm mb-1">Name</p>
                                <p class="text-gray-900 dark:text-white font-medium"><?php echo esc_html(warafy_mask_name($order->get_formatted_billing_full_name())); ?></p>
                            </div>
                            <?php if ($order->get_billing_phone()) : ?>
                                <div>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-1">Phone</p>
                                    <p class="text-gray-900 dark:text-white font-medium"><?php echo esc_html(warafy_mask_phone($order->get_billing_phone())); ?></p>
                                </div>
                            <?php endif; ?>
                            <?php if ($order->get_billing_email()) : ?>
                                <div>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-1">Email</p>
                                    <p class="text-gray-900 dark:text-white font-medium"><?php echo esc_html(warafy_mask_email($order->get_billing_email())); ?></p>
                                </div>
                            <?php endif; ?>
                            <div class="md:col-span-2">
                                <p class="text-gray-500 dark:text-gray-400 text-sm mb-1">Address</p>
                                <p class="text-gray-900 dark:text-white font-medium"><?php echo esc_html(warafy_mask_address($order->get_formatted_billing_address())); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined" data-icon="inventory_2"></span>
                            Order Items
                        </h3>
                        <div class="space-y-4">
                            <?php foreach ($order->get_items() as $item_id => $item) :
                                $product = $item->get_product();
                                ?>
                                <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                                    <div class="w-16 h-16 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-200 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                        <?php if ($product && $product->get_image()) : ?>
                                            <?php echo $product->get_image('thumbnail', ['class' => 'w-full h-full object-cover rounded-xl']); ?>
                                        <?php else : ?>
                                            <span class="material-symbols-outlined text-2xl" data-icon="shopping_bag"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900 dark:text-white"><?php echo esc_html($item->get_name()); ?></p>
                                        <?php if ($product && $product->get_sku()) : ?>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">SKU: <?php echo esc_html($product->get_sku()); ?></p>
                                        <?php endif; ?>
                                        <div class="flex items-center gap-4 mt-2">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Quantity: <?php echo esc_html($item->get_quantity()); ?></p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Price: <?php echo wp_kses_post(wc_price($item->get_total() / $item->get_quantity())); ?></p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900 dark:text-white"><?php echo wp_kses_post($order->get_formatted_line_subtotal($item)); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="p-6 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Summary</h3>
                        <div class="space-y-3 text-sm">
                            <?php foreach ($order->get_order_item_totals() as $key => $total) : ?>
                                <div class="flex justify-between text-gray-700 dark:text-gray-200 <?php echo esc_attr('order-total' === $key ? 'text-base font-bold pt-3 border-t border-gray-200 dark:border-gray-700 mt-3' : ''); ?>">
                                    <span><?php echo esc_html($total['label']); ?></span>
                                    <span><?php echo wp_kses_post($total['value']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if ($order->get_customer_note()) : ?>
                            <div class="mt-6 p-4 rounded-xl bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-200">
                                <p class="font-semibold mb-2">Order Note</p>
                                <p><?php echo esc_html($order->get_customer_note()); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-purple-600 text-white font-semibold hover:bg-purple-700 transition">
                            <span class="material-symbols-outlined text-base" data-icon="storefront"></span>
                            Continue Shopping
                        </a>
                        <a href="<?php echo esc_url(get_home_url()); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-100 font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <span class="material-symbols-outlined text-base" data-icon="home"></span>
                            Back to Home
                        </a>
                    </div>
                </div>

            <?php else : ?>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-8 text-center">
                    <span class="material-symbols-outlined text-6xl text-red-500 mb-4" data-icon="error"></span>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Order Not Found</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">The order you're looking for could not be found or the link has expired.</p>
                    <div class="flex flex-wrap items-center justify-center gap-4">
                        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-purple-600 text-white font-semibold hover:bg-purple-700 transition">
                            <span class="material-symbols-outlined text-base" data-icon="storefront"></span>
                            Continue Shopping
                        </a>
                        <a href="<?php echo esc_url(get_home_url()); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-100 font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <span class="material-symbols-outlined text-base" data-icon="home"></span>
                            Back to Home
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
