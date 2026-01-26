<?php
/**
 * My Account Dashboard
 *
 * @package Adorkini
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$allowed_html = array(
	'a' => array(
		'href' => array(),
	),
);
?>

<div class="adorkini-dashboard">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">
            <?php
            /* translators: 1: user display name 2: logout url */
            printf(
                __( 'Hello %1$s (not %1$s? <a href="%2$s" class="text-primary hover:underline">Log out</a>)', 'woocommerce' ),
                '<strong>' . esc_html( $current_user->display_name ) . '</strong>',
                esc_url( wc_logout_url() )
            );
            ?>
        </h2>
        <p class="text-gray-600">
            <?php
            printf(
                __( 'From your account dashboard you can view your <a href="%1$s" class="text-primary">recent orders</a>, manage your <a href="%2$s" class="text-primary">shipping and billing addresses</a>, and <a href="%3$s" class="text-primary">edit your password and account details</a>.', 'woocommerce' ),
                esc_url( wc_get_endpoint_url( 'orders' ) ),
                esc_url( wc_get_endpoint_url( 'edit-address' ) ),
                esc_url( wc_get_endpoint_url( 'edit-account' ) )
            );
            ?>
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Orders Card -->
        <a href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>" class="block p-6 bg-white border border-gray-200 rounded-xl hover:shadow-lg transition-all group">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                <?php adorkini_icon( 'cart', 'text-2xl' ); ?>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2"><?php _e( 'Orders', 'woocommerce' ); ?></h3>
            <p class="text-sm text-gray-500">Track and view your order history.</p>
        </a>

        <!-- Addresses Card -->
        <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>" class="block p-6 bg-white border border-gray-200 rounded-xl hover:shadow-lg transition-all group">
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-full flex items-center justify-center mb-4 group-hover:bg-green-600 group-hover:text-white transition-colors">
                <?php adorkini_icon( 'home', 'text-2xl' ); ?> <!-- Using home icon for address -->
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2"><?php _e( 'Addresses', 'woocommerce' ); ?></h3>
            <p class="text-sm text-gray-500">Edit shipping and billing details.</p>
        </a>

        <!-- Account Details Card -->
        <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-account' ) ); ?>" class="block p-6 bg-white border border-gray-200 rounded-xl hover:shadow-lg transition-all group">
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center mb-4 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                <?php adorkini_icon( 'user', 'text-2xl' ); ?>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2"><?php _e( 'Account Details', 'woocommerce' ); ?></h3>
            <p class="text-sm text-gray-500">Update password and personal info.</p>
        </a>
    </div>

	<?php
		/**
		 * My Account dashboard.
		 *
		 * @since 2.6.0
		 */
		do_action( 'woocommerce_account_dashboard' );

		/**
		 * Deprecated woocommerce_before_my_account action.
		 *
		 * @deprecated 2.6.0
		 */
		do_action( 'woocommerce_before_my_account' );

		/**
		 * Deprecated woocommerce_after_my_account action.
		 *
		 * @deprecated 2.6.0
		 */
		do_action( 'woocommerce_after_my_account' );
	?>
</div>
