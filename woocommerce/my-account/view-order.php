<?php
/**
 * View Order Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/my-account/view-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Redirect if not logged in
if (!is_user_logged_in()) {
    wp_redirect(home_url('/login'));
    exit;
}

$current_user = wp_get_current_user();
$user_id = get_current_user_id();

// Get the order from the URL parameter using WooCommerce endpoint
$order_id = get_query_var('view-order');
if (!$order_id) {
    // Fallback to GET parameter
    $order_id = isset($_GET['view-order']) ? intval($_GET['view-order']) : 0;
}

if (!$order_id) {
    // Another fallback: try to get from URL path
    $url_path = strtok($_SERVER['REQUEST_URI'], '?');
    $path_parts = explode('/', trim($url_path, '/'));
    
    // Look for view-order pattern
    foreach ($path_parts as $key => $part) {
        if ($part === 'view-order' && isset($path_parts[$key + 1])) {
            $order_id = intval($path_parts[$key + 1]);
            break;
        }
    }
}

if (!$order_id) {
    wp_redirect(home_url('/my-account'));
    exit;
}

$order = wc_get_order($order_id);

// Verify this order belongs to the current user
if (!$order || $order->get_customer_id() != $user_id) {
    wp_redirect(home_url('/my-account'));
    exit;
}

get_header();
?>

<!-- Mobile Header -->
<header class="lg:hidden sticky top-0 z-50 w-full border-b border-gray-200/50 dark:border-gray-700/50 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm">
    <div class="container mx-auto px-4">
        <div class="flex h-16 items-center justify-between">
            <button onclick="history.back()" class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full hover:bg-gray-200/50 dark:hover:bg-gray-700/50">
                <span class="material-symbols-outlined text-gray-600 dark:text-gray-300" data-icon="arrow_back"></span>
            </button>
            <h1 class="text-lg font-bold text-gray-900 dark:text-white"><?php echo __t('Order Details'); ?></h1>
            <div class="w-10"></div>
        </div>
    </div>
</header>

<main class="flex-grow">
    <div class="container mx-auto px-4 py-6 lg:px-6 lg:py-8 max-w-5xl">
        
        <!-- Page Title (Desktop) -->
        <div class="hidden lg:flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white"><?php echo __t('Order Details'); ?></h1>
                <nav class="flex mt-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="<?php echo home_url('/my-account'); ?>" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary dark:text-gray-400 dark:hover:text-white">
                                <span class="material-symbols-outlined text-base mr-2" data-icon="home"></span>
                                <?php echo __t('My Account'); ?>
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <span class="material-symbols-outlined text-gray-400 mx-2" data-icon="chevron_right"></span>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo __t('Order Details'); ?></span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
            <a href="<?php echo home_url('/my-account'); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">
                <span class="material-symbols-outlined text-base" data-icon="arrow_back"></span>
                <?php echo __t('Back to Orders'); ?>
            </a>
        </div>

        <?php if ($order) : ?>
            <!-- Order Status Card -->
            <div class="bg-gradient-to-r from-primary to-primary/80 rounded-2xl p-6 mb-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-white/80 text-sm mb-1"><?php echo __t('Order Number'); ?></div>
                        <h2 class="text-2xl lg:text-3xl font-bold mb-2">#<?php echo $order->get_order_number(); ?></h2>
                        <div class="flex items-center gap-4">
                            <span class="inline-flex items-center rounded-full bg-white/20 px-3 py-1 text-sm font-medium">
                                <?php echo wc_get_order_status_name($order->get_status()); ?>
                            </span>
                            <div class="text-white/80 text-sm">
                                <?php echo wc_format_datetime($order->get_date_created()); ?>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-white/80 text-sm mb-1"><?php echo __t('Total Amount'); ?></div>
                        <div class="text-2xl lg:text-3xl font-bold"><?php echo $order->get_formatted_order_total(); ?></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Order Items (Main Content) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Order Items List -->
                    <div class="bg-white rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-background-dark overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary" data-icon="inventory_2"></span>
                                <h3 class="font-bold text-gray-900 dark:text-white"><?php echo __t('Order Items'); ?></h3>
                            </div>
                        </div>
                        <div class="p-6">
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
                                                <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo __t('Quantity'); ?>: <?php echo esc_html($item->get_quantity()); ?></p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo __t('Price'); ?>: <?php echo wc_price($item->get_total() / $item->get_quantity()); ?></p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900 dark:text-white"><?php echo $order->get_formatted_line_subtotal($item); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="bg-white rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-background-dark overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary" data-icon="receipt_long"></span>
                                <h3 class="font-bold text-gray-900 dark:text-white"><?php echo __t('Order Summary'); ?></h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3 text-sm">
                                <?php foreach ($order->get_order_item_totals() as $key => $total) : ?>
                                    <div class="flex justify-between text-gray-700 dark:text-gray-200 <?php echo esc_attr('order-total' === $key ? 'text-base font-bold pt-3 border-t border-gray-200 dark:border-gray-700 mt-3' : ''); ?>">
                                        <span><?php echo esc_html($total['label']); ?></span>
                                        <span><?php echo wp_kses_post($total['value']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if ($order->get_customer_note()) : ?>
                                <div class="mt-6 p-4 rounded-xl bg-gray-50 dark:bg-gray-900/50 text-sm text-gray-700 dark:text-gray-200">
                                    <p class="font-semibold mb-2"><?php echo __t('Order Note'); ?></p>
                                    <p><?php echo esc_html($order->get_customer_note()); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Information -->
                <div class="space-y-6">
                    
                    <!-- Shipping & Billing Information -->
                    <div class="bg-white rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-background-dark overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary" data-icon="location_on"></span>
                                <h3 class="font-bold text-gray-900 dark:text-white"><?php echo __t('Shipping Information'); ?></h3>
                            </div>
                        </div>
                        <div class="p-6 space-y-4">
                            <!-- Shipping Address -->
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-base" data-icon="local_shipping"></span>
                                    <?php echo __t('Shipping Address'); ?>
                                </h4>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <?php echo wp_kses_post($order->get_formatted_shipping_address()); ?>
                                </div>
                            </div>
                            
                            <!-- Billing Address -->
                            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-base" data-icon="receipt"></span>
                                    <?php echo __t('Billing Address'); ?>
                                </h4>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <?php echo wp_kses_post($order->get_formatted_billing_address()); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="bg-white rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-background-dark overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary" data-icon="payment"></span>
                                <h3 class="font-bold text-gray-900 dark:text-white"><?php echo __t('Payment Information'); ?></h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1"><?php echo __t('Payment Method'); ?></p>
                                    <p class="font-semibold text-gray-900 dark:text-white"><?php echo wp_kses_post($order->get_payment_method_title()); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1"><?php echo __t('Payment Status'); ?></p>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        <?php 
                                        if ($order->is_paid()) {
                                            echo '<span class="text-green-600">' . __t('Paid') . '</span>';
                                        } else {
                                            echo '<span class="text-orange-600">' . __t('Pending') . '</span>';
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-background-dark overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary" data-icon="settings"></span>
                                <h3 class="font-bold text-gray-900 dark:text-white"><?php echo __t('Actions'); ?></h3>
                            </div>
                        </div>
                        <div class="p-6 space-y-3">
                            <?php if ($order->needs_payment()) : ?>
                                <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium text-sm">
                                    <span class="material-symbols-outlined text-base" data-icon="payment"></span>
                                    <?php echo __t('Pay for Order'); ?>
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($order->has_status('processing')) : ?>
                                <button class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors font-medium text-sm">
                                    <span class="material-symbols-outlined text-base" data-icon="cancel"></span>
                                    <?php echo __t('Cancel Order'); ?>
                                </button>
                            <?php endif; ?>
                            
                            <a href="<?php echo home_url('/shop'); ?>" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors font-medium text-sm">
                                <span class="material-symbols-outlined text-base" data-icon="shopping_bag"></span>
                                <?php echo __t('Continue Shopping'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        <?php else : ?>
            <div class="bg-white rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-background-dark p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-50 dark:bg-red-900/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-3xl text-red-500" data-icon="error"></span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2"><?php echo __t('Order Not Found'); ?></h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6"><?php echo __t('The order you\'re looking for could not be found.'); ?></p>
                <a href="<?php echo home_url('/my-account'); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium">
                    <span class="material-symbols-outlined" data-icon="arrow_back"></span>
                    <?php echo __t('Back to My Account'); ?>
                </a>
            </div>
        <?php endif; ?>
        
    </div>
</main>

<?php get_footer(); ?>
