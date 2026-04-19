<?php
/**
 * Template Name: Checkout Page - Modern Premium Design
 *
 * This template provides a modern, premium checkout experience
 */

defined('ABSPATH') || exit;
if (!defined('WARAFY_CHECKOUT_WRAPPER')) {
    define('WARAFY_CHECKOUT_WRAPPER', true);
}

get_header();

$is_order_received = function_exists('is_order_received_page') && is_order_received_page();
$is_order_pay_endpoint = function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('order-pay');
$allow_empty_cart_checkout = $is_order_received || $is_order_pay_endpoint;
?>

<main class="flex-grow bg-gradient-to-br from-gray-50 via-purple-50/20 to-gray-50 dark:bg-background-dark min-h-screen pb-24 lg:pb-8">
    <div class="container mx-auto px-4 lg:px-6 py-6 lg:py-12">
        <?php if (!$is_order_received) : ?>
            <!-- Page Header with Progress -->
            <div class="mb-8">
                <nav class="text-sm mb-6">
                    <ol class="flex items-center space-x-2 text-gray-500">
                        <li><a href="<?php echo home_url(); ?>" class="hover:text-blue-600 transition-colors"><?php echo __t('Home'); ?></a></li>
                        <li><span class="mx-2">/</span></li>
                        <li><a href="<?php echo wc_get_cart_url(); ?>" class="hover:text-blue-600 transition-colors"><?php echo __t('Cart'); ?></a></li>
                        <li><span class="mx-2">/</span></li>
                        <li class="text-gray-900 dark:text-white font-medium"><?php echo __t('Checkout'); ?></li>
                    </ol>
                </nav>

                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                    <span class="material-symbols-outlined text-4xl lg:text-5xl text-purple-600" data-icon="shopping_bag"></span>
                    <?php echo __t('Secure Checkout'); ?>
                </h1>

                <!-- Progress Steps -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-8">
                    <div class="flex items-center justify-between max-w-3xl mx-auto">
                        <div class="flex flex-col items-center flex-1">
                            <div class="w-12 h-12 rounded-full bg-green-500 text-white flex items-center justify-center mb-2 shadow-lg">
                                <?php echo warafy_get_icon_svg('check', 'w-6 h-6'); ?>
                            </div>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white"><?php echo __t('Cart'); ?></span>
                        </div>
                        <div class="flex-1 h-1 bg-purple-500 mx-2"></div>
                        <div class="flex flex-col items-center flex-1">
                            <div class="w-12 h-12 rounded-full <?php echo $is_order_received ? 'bg-green-500' : 'bg-purple-600'; ?> text-white flex items-center justify-center mb-2 shadow-lg <?php echo $is_order_received ? '' : 'animate-pulse'; ?>">
                                <?php echo warafy_get_icon_svg($is_order_received ? 'check' : 'edit', 'w-6 h-6'); ?>
                            </div>
                            <span class="text-sm font-semibold <?php echo $is_order_received ? 'text-gray-900 dark:text-white' : 'text-purple-600'; ?>"><?php echo __t('Checkout'); ?></span>
                        </div>
                        <div class="flex-1 h-1 <?php echo $is_order_received ? 'bg-purple-500' : 'bg-gray-300 dark:bg-gray-600'; ?> mx-2"></div>
                        <div class="flex flex-col items-center flex-1">
                            <div class="w-12 h-12 rounded-full <?php echo $is_order_received ? 'bg-purple-600 text-white animate-pulse' : 'bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400'; ?> flex items-center justify-center mb-2">
                                <?php echo warafy_get_icon_svg('task_alt', 'w-6 h-6'); ?>
                            </div>
                            <span class="text-sm font-medium <?php echo $is_order_received ? 'text-purple-600' : 'text-gray-500 dark:text-gray-400'; ?>"><?php echo __t('Complete'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($is_order_received) : ?>
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-10">
                <div class="woocommerce">
                    <?php
                    if (function_exists('wc_print_notices')) {
                        wc_print_notices();
                    }

                    echo do_shortcode('[woocommerce_checkout]');
                    ?>
                </div>
            </div>
        <?php else : ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Checkout Form Section -->
                <div class="lg:col-span-2">
                    <div class="woocommerce">
                        <?php
                        if (function_exists('wc_print_notices')) {
                            wc_print_notices();
                        }
                        
                        if (WC()->cart->is_empty() && !$allow_empty_cart_checkout) {
                            echo '<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-12 text-center">';
                            echo '<span class="material-symbols-outlined text-9xl text-gray-300 mb-6" data-icon="shopping_cart"></span>';
                            echo '<h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">' . __t('Your cart is empty') . '</h2>';
                            echo '<p class="text-gray-600 dark:text-gray-400 mb-8">' . __t('Please add items to your cart before proceeding to checkout.') . '</p>';
                            echo '<a href="' . get_permalink(wc_get_page_id('shop')) . '" class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white px-8 py-4 rounded-xl font-semibold hover:from-purple-700 hover:to-purple-800 transition-all transform hover:scale-105 shadow-lg">';
                            echo '<span class="material-symbols-outlined" data-icon="storefront"></span> ' . __t('Continue Shopping') . '</a>';
                            echo '</div>';
                        } else {
                            echo do_shortcode('[woocommerce_checkout]');
                        }
                        ?>
                    </div>
                </div>

                <!-- Order Summary Sidebar -->
                <div class="lg:col-span-1 desktop-order-summary">
                    <div class="sticky top-8 space-y-6">
                        <!-- Order Summary Card -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border border-gray-100 dark:border-gray-700">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-600 to-purple-700 text-white flex items-center justify-center shadow-lg">
                                    <span class="material-symbols-outlined text-2xl" data-icon="receipt_long"></span>
                                </div>
                                <?php echo __t('Order Summary'); ?>
                            </h3>
                            
                            <div class="space-y-4" id="desktop-order-summary">
                                <?php if (WC()->cart && !WC()->cart->is_empty()) : ?>
                                    <!-- Cart Items -->
                                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 space-y-3">
                                        <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) : ?>
                                            <?php
                                            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                                            $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
                                            $quantity = $cart_item['quantity'];
                                            $product_price = WC()->cart->get_product_price($_product);
                                            $product_subtotal = WC()->cart->get_product_subtotal($_product, $quantity);
                                            ?>
                                            <div class="flex items-start space-x-3">
                                                <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900 dark:to-purple-800 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <span class="material-symbols-outlined text-lg text-purple-600 dark:text-purple-300" data-icon="shopping_bag"></span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="font-semibold text-gray-900 dark:text-white text-sm truncate"><?php echo esc_html($product_name); ?></h4>
                                                    <p class="text-xs text-gray-600 dark:text-gray-400"><?php echo __t('Quantity'); ?>: <?php echo esc_html($quantity); ?></p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="font-semibold text-gray-900 dark:text-white text-sm"><?php echo $product_subtotal; ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <!-- Price Summary -->
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                            <span><?php echo __t('Subtotal'); ?></span>
                                            <span><?php echo WC()->cart->get_cart_subtotal(); ?></span>
                                        </div>
                                        
                                        <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
                                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                                <span><?php echo __t('Shipping'); ?></span>
                                                <span><?php echo __t('Calculated at next step'); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php foreach (WC()->cart->get_fees() as $fee) : ?>
                                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                                <span><?php echo esc_html($fee->name); ?></span>
                                                <span><?php echo wc_price($fee->amount); ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                        
                                        <?php if (WC()->cart->get_discount_tax()) : ?>
                                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                                <span><?php echo __t('Discount'); ?></span>
                                                <span>-<?php echo wc_price(WC()->cart->get_discount_total()); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                                            <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white">
                                                <span><?php echo __t('Total'); ?></span>
                                                <span class="text-purple-600 dark:text-purple-400"><?php echo WC()->cart->get_total(); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <!-- Empty Cart -->
                                    <div class="text-center py-8">
                                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <span class="material-symbols-outlined text-2xl text-gray-400" data-icon="shopping_cart"></span>
                                        </div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2"><?php echo __t('Your cart is empty'); ?></h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4"><?php echo __t('Add items to your cart to see them here'); ?></p>
                                        <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="inline-flex items-center gap-2 bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors">
                                            <span class="material-symbols-outlined text-lg" data-icon="storefront"></span>
                                            <?php echo __t('Continue Shopping'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Security Badge -->
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-2xl p-4 border border-green-200 dark:border-green-800">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white" data-icon="security"></span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-green-800 dark:text-green-200"><?php echo __t('Secure Checkout'); ?></h4>
                                    <p class="text-sm text-green-600 dark:text-green-400"><?php echo __t('Your data is protected'); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Help Section -->
                        <div class="text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2"><?php echo __t('Need Help?'); ?></p>
                            <a href="#" class="text-purple-600 hover:text-purple-700 font-medium text-sm"><?php echo __t('Contact Support'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Version Footer -->
<div class="fixed bottom-2 right-2 bg-black/70 text-white px-3 py-1 rounded-full text-xs font-mono z-50">
    v9.1-cod-context
</div>

<?php get_footer(); ?>
