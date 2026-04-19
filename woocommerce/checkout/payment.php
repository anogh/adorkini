<?php
/**
 * Checkout Payment Section
 *
 * @version 9.8.0
 */

defined('ABSPATH') || exit;

if (!wp_doing_ajax()) {
    do_action('woocommerce_review_order_before_payment');
}
?>

<div id="payment" class="woocommerce-checkout-payment">
    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-600 to-green-700 text-white flex items-center justify-center">
            <span class="material-symbols-outlined text-lg" data-icon="payment"></span>
        </div>
        <?php esc_html_e('Payment Method', 'woocommerce'); ?>
    </h3>

    <?php if (WC()->cart->needs_payment()) : ?>
        <ul class="wc_payment_methods payment_methods methods space-y-3">
            <?php
            if (!empty($available_gateways)) {
                foreach ($available_gateways as $gateway) {
                    wc_get_template('checkout/payment-method.php', array('gateway' => $gateway));
                }
            } else {
                echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">';
                echo apply_filters('woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__('Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce') : esc_html__('Please fill in your details above to see available payment methods.', 'woocommerce'));
                echo '</li>';
            }
            ?>
        </ul>
    <?php endif; ?>

    <div class="form-row place-order mt-6">
        <noscript>
            <?php esc_html_e('Since your browser does not support JavaScript, or it is disabled, please ensure you click the <em>Update Totals</em> button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce'); ?>
            <br/><button type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e('Update totals', 'woocommerce'); ?>"><?php esc_html_e('Update totals', 'woocommerce'); ?></button>
        </noscript>

        <?php wc_get_template('checkout/terms.php'); ?>

        <?php do_action('woocommerce_review_order_before_submit'); ?>

        <button type="submit" 
                class="button alt w-full bg-black text-white font-bold py-4 px-8 rounded-xl transition-all transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center gap-3 text-lg hover:bg-gray-800" 
                name="woocommerce_checkout_place_order" 
                id="place_order" 
                value="<?php esc_attr_e('Place order', 'woocommerce'); ?>" 
                data-value="<?php esc_attr_e('Place order', 'woocommerce'); ?>">
            <span class="material-symbols-outlined text-2xl" data-icon="shopping_bag_check"></span>
            <?php echo esc_html(apply_filters('woocommerce_order_button_text', __('Place order', 'woocommerce'))); ?>
        </button>

        <?php do_action('woocommerce_review_order_after_submit'); ?>

        <?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>
    </div>
</div>

<?php
if (!wp_doing_ajax()) {
    do_action('woocommerce_review_order_after_payment');
}
?>
