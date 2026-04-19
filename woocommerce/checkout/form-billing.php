<?php
/**
 * Checkout billing form
 *
 * @version 3.6.0
 */
defined('ABSPATH') || exit;

$base_country = function_exists('WC') && WC()->countries ? WC()->countries->get_base_country() : 'BD';
$base_state = function_exists('WC') && WC()->countries ? WC()->countries->get_base_state() : '';

$billing_fields = $checkout->get_checkout_fields('billing');
?>

<div class="woocommerce-billing-fields">
    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-600 to-purple-700 text-white flex items-center justify-center text-sm font-bold">1</div>
        <?php echo __t('Delivery Details'); ?>
    </h3>

    <?php do_action('woocommerce_before_checkout_billing_form', $checkout); ?>

    <div class="woocommerce-billing-fields__field-wrapper space-y-4">
        <?php
        $visible_fields = array('billing_first_name', 'billing_phone', 'billing_address_1', 'billing_email');

        foreach ($visible_fields as $key) {
            if (isset($billing_fields[$key])) {
                $field = $billing_fields[$key];
                $field['class'] = array('form-row-wide');
                woocommerce_form_field($key, $field, $checkout->get_value($key));
            }
        }

        woocommerce_form_field('billing_country', array(
            'type' => 'hidden',
            'required' => false,
        ), $checkout->get_value('billing_country') ?: $base_country);

        woocommerce_form_field('billing_state', array(
            'type' => 'hidden',
            'required' => false,
        ), $checkout->get_value('billing_state') ?: $base_state);

        woocommerce_form_field('billing_city', array(
            'type' => 'hidden',
            'required' => false,
        ), $checkout->get_value('billing_city') ?: '');

        woocommerce_form_field('billing_postcode', array(
            'type' => 'hidden',
            'required' => false,
        ), $checkout->get_value('billing_postcode') ?: '');
        ?>
    </div>

    <?php do_action('woocommerce_after_checkout_billing_form', $checkout); ?>
</div>
