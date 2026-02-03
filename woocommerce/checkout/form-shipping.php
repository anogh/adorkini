<?php
/**
 * Checkout shipping information form
 */

defined('ABSPATH') || exit;
?>

<div class="woocommerce-shipping-fields mt-8">
    <?php if (true === WC()->cart->needs_shipping_address()) : ?>

        <h3 id="ship-to-different-address" class="text-xl font-bold text-gray-900 dark:text-white mb-6">
            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox flex items-center gap-3 cursor-pointer">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-600 to-blue-700 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">2</div>
                <input id="ship-to-different-address-checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500" <?php checked(apply_filters('woocommerce_ship_to_different_address_checked', 'shipping' === get_option('woocommerce_ship_to_destination') ? 1 : 0), 1); ?> type="checkbox" name="ship_to_different_address" value="1" />
                <span class="font-bold"><?php esc_html_e('Ship to a different address?', 'woocommerce'); ?></span>
            </label>
        </h3>

        <div class="shipping_address p-6 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">

            <?php do_action('woocommerce_before_checkout_shipping_form', $checkout); ?>

            <div class="woocommerce-shipping-fields__field-wrapper grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php
                $fields = $checkout->get_checkout_fields('shipping');

                foreach ($fields as $key => $field) {
                    // Make these fields full width
                    if (in_array($key, ['shipping_first_name', 'shipping_last_name', 'shipping_address_1', 'shipping_address_2', 'shipping_postcode', 'shipping_city'])) {
                        echo '<div class="md:col-span-2">';
                        woocommerce_form_field($key, $field, $checkout->get_value($key));
                        echo '</div>';
                    } else {
                        woocommerce_form_field($key, $field, $checkout->get_value($key));
                    }
                }
                ?>
            </div>

            <?php do_action('woocommerce_after_checkout_shipping_form', $checkout); ?>

        </div>

    <?php endif; ?>
</div>

<div class="woocommerce-additional-fields mt-8">
    <?php do_action('woocommerce_before_order_notes', $checkout); ?>

    <?php if (apply_filters('woocommerce_enable_order_notes_field', 'yes' === get_option('woocommerce_enable_order_comments', 'yes'))) : ?>

        <?php if (!WC()->cart->needs_shipping() || wc_ship_to_billing_address_only()) : ?>

            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-600 to-green-700 text-white flex items-center justify-center text-sm font-bold">
                    <span class="material-symbols-outlined text-lg" data-icon="note_add"></span>
                </div>
                <?php esc_html_e('Additional information', 'woocommerce'); ?>
            </h3>

        <?php endif; ?>

        <div class="woocommerce-additional-fields__field-wrapper p-6 bg-gradient-to-br from-green-50 to-blue-50 dark:from-gray-800 dark:to-gray-700 rounded-xl border border-green-100 dark:border-gray-600">
            <?php foreach ($checkout->get_checkout_fields('order') as $key => $field) : ?>
                <?php woocommerce_form_field($key, $field, $checkout->get_value($key)); ?>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

    <?php do_action('woocommerce_after_order_notes', $checkout); ?>
</div>
