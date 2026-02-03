<?php
/**
 * Review order table
 */

defined('ABSPATH') || exit;
?>

<table class="shop_table woocommerce-checkout-review-order-table w-full">
    <thead>
        <tr>
            <th class="product-name text-left"><?php esc_html_e('Product', 'woocommerce'); ?></th>
            <th class="product-total text-right"><?php esc_html_e('Subtotal', 'woocommerce'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        do_action('woocommerce_review_order_before_cart_contents');

        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

            if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
                ?>
                <tr class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
                    <td class="product-name">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                <?php echo $_product->get_image('thumbnail'); ?>
                            </div>
                            <div>
                                <strong class="product-quantity text-gray-900 dark:text-white">
                                    <?php echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key)); ?>
                                    &nbsp;&times;&nbsp;<?php echo apply_filters('woocommerce_checkout_cart_item_quantity', ' <span class="text-sm text-gray-600 dark:text-gray-400">' . sprintf('&times;&nbsp;%s', $cart_item['quantity']) . '</span>', $cart_item, $cart_item_key); ?>
                                </strong>
                                <?php echo wc_get_formatted_cart_item_data($cart_item); ?>
                            </div>
                        </div>
                    </td>
                    <td class="product-total text-right">
                        <span class="font-semibold text-purple-600 dark:text-purple-400">
                            <?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
                        </span>
                    </td>
                </tr>
                <?php
            }
        }

        do_action('woocommerce_review_order_after_cart_contents');
        ?>
    </tbody>
    <tfoot>
        <tr class="cart-subtotal">
            <th class="text-left text-gray-600 dark:text-gray-400"><?php esc_html_e('Subtotal', 'woocommerce'); ?></th>
            <td class="text-right font-semibold text-gray-900 dark:text-white"><?php wc_cart_totals_subtotal_html(); ?></td>
        </tr>

        <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
            <tr class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
                <th class="text-left">
                    <span class="text-green-600 dark:text-green-400 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm" data-icon="local_offer"></span>
                        <?php wc_cart_totals_coupon_label($coupon); ?>
                    </span>
                </th>
                <td class="text-right font-semibold text-green-600 dark:text-green-400"><?php wc_cart_totals_coupon_html($coupon); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>

            <?php do_action('woocommerce_review_order_before_shipping'); ?>

            <?php wc_cart_totals_shipping_html(); ?>

            <?php do_action('woocommerce_review_order_after_shipping'); ?>

        <?php endif; ?>

        <?php foreach (WC()->cart->get_fees() as $fee) : ?>
            <tr class="fee">
                <th class="text-left text-gray-600 dark:text-gray-400"><?php echo esc_html($fee->name); ?></th>
                <td class="text-right font-semibold text-gray-900 dark:text-white"><?php wc_cart_totals_fee_html($fee); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) : ?>
            <?php if ('itemized' === get_option('woocommerce_tax_total_display')) : ?>
                <?php foreach (WC()->cart->get_tax_totals() as $code => $tax) : ?>
                    <tr class="tax-rate tax-rate-<?php echo esc_attr(sanitize_title($code)); ?>">
                        <th class="text-left text-gray-600 dark:text-gray-400"><?php echo esc_html($tax->label); ?></th>
                        <td class="text-right font-semibold text-gray-900 dark:text-white"><?php echo wp_kses_post($tax->formatted_amount); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr class="tax-total">
                    <th class="text-left text-gray-600 dark:text-gray-400"><?php echo esc_html(WC()->countries->tax_or_vat()); ?></th>
                    <td class="text-right font-semibold text-gray-900 dark:text-white"><?php wc_cart_totals_taxes_total_html(); ?></td>
                </tr>
            <?php endif; ?>
        <?php endif; ?>

        <?php do_action('woocommerce_review_order_before_order_total'); ?>

        <tr class="order-total bg-gradient-to-r from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-800/20">
            <th class="text-left text-gray-900 dark:text-white font-bold text-xl"><?php esc_html_e('Total', 'woocommerce'); ?></th>
            <td class="text-right font-bold text-2xl text-purple-600 dark:text-purple-400"><?php wc_cart_totals_order_total_html(); ?></td>
        </tr>

        <?php do_action('woocommerce_review_order_after_order_total'); ?>
    </tfoot>
</table>
