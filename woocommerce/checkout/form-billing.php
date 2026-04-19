<?php
/**
 * Checkout billing information form
 */

defined('ABSPATH') || exit;
?>

<div class="woocommerce-billing-fields">
    <?php if (wc_ship_to_billing_address_only() && WC()->cart->needs_shipping()) : ?>

        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-600 to-purple-700 text-white flex items-center justify-center text-sm font-bold">1</div>
            <?php esc_html_e('Billing &amp; Shipping', 'woocommerce'); ?>
        </h3>

    <?php else : ?>

        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-600 to-purple-700 text-white flex items-center justify-center text-sm font-bold">1</div>
            <?php esc_html_e('Billing details', 'woocommerce'); ?>
        </h3>

    <?php endif; ?>

    <?php do_action('woocommerce_before_checkout_billing_form', $checkout); ?>

    <div class="woocommerce-billing-fields__field-wrapper grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php
        $fields = $checkout->get_checkout_fields('billing');

        foreach ($fields as $key => $field) {
            if (in_array($key, ['billing_country', 'billing_state', 'billing_city', 'billing_postcode'])) {
                woocommerce_form_field($key, $field, $checkout->get_value($key));
                continue;
            }

            // Make the visible fields full width
            if (in_array($key, ['billing_first_name', 'billing_address_1', 'billing_phone'])) {
                echo '<div class="md:col-span-2">';
                woocommerce_form_field($key, $field, $checkout->get_value($key));
                echo '</div>';
            } else {
                woocommerce_form_field($key, $field, $checkout->get_value($key));
            }
        }
        ?>
    </div>

    <?php do_action('woocommerce_after_checkout_billing_form', $checkout); ?>
</div>

<!-- Account creation removed for faster checkout -->
