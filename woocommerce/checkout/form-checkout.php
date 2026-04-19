<?php
/**
 * Checkout Form
 *
 * @version 9.4.0
 */

defined('ABSPATH') || exit;
?>

<div class="warafy-checkout-wrapper">
    <?php do_action('woocommerce_before_checkout_form', $checkout); ?>

    <form name="checkout" method="post" class="warafy-checkout-form" action="<?php echo esc_url(add_query_arg('warafy_checkout', '1', wc_get_checkout_url())); ?>" enctype="multipart/form-data">

        <?php if ($checkout->get_checkout_fields()) : ?>

            <?php do_action('woocommerce_checkout_before_customer_details'); ?>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8 mb-6 border border-gray-100 dark:border-gray-700" id="customer_details">
                <div class="col2-set" id="customer_details_inner">
                    <div class="col-1">
                        <?php do_action('woocommerce_checkout_billing'); ?>
                    </div>

                    <div class="col-2" style="display:none;">
                        <?php do_action('woocommerce_checkout_shipping'); ?>
                    </div>
                </div>
            </div>

            <?php do_action('woocommerce_checkout_after_customer_details'); ?>

        <?php endif; ?>

        <div class="warafy-order-review-section bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8 border border-gray-100 dark:border-gray-700">
            <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>

            <h3 id="order_review_heading" class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-600 to-purple-700 text-white flex items-center justify-center shadow-lg">
                    <span class="material-symbols-outlined text-2xl" data-icon="receipt_long"></span>
                </div>
                <?php esc_html_e('Your order', 'woocommerce'); ?>
            </h3>

            <?php do_action('woocommerce_checkout_before_order_review'); ?>

            <div id="order_review" class="woocommerce-checkout-review-order">
                <?php do_action('woocommerce_checkout_order_review'); ?>
            </div>

            <?php do_action('woocommerce_checkout_after_order_review'); ?>
        </div>

    </form>
</div>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>
