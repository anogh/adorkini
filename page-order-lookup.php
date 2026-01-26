<?php
/**
 * Template Name: Order Lookup
 * 
 * @package Adorkini
 */

get_header();

$order_found = false;
$error_msg = '';

if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['order_lookup_nonce'] ) ) {
    if ( wp_verify_nonce( $_POST['order_lookup_nonce'], 'adorkini_order_lookup' ) ) {
        $order_key = sanitize_text_field( $_POST['order_key'] );
        $contact = sanitize_text_field( $_POST['contact_input'] );

        $order = adorkini_verify_guest_order( $order_key, $contact );

        if ( $order ) {
            $order_found = $order;
        } else {
            $error_msg = __( 'Invalid Order Key or Contact Information.', 'adorkini' );
        }
    }
}
?>

<div class="container mx-auto px-4 py-12 max-w-lg">
    
    <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800"><?php echo esc_html( __t( 'order_lookup' ) ); ?></h1>

        <?php if ( ! $order_found ) : ?>
            
            <?php if ( $error_msg ) : ?>
                <div class="bg-red-50 text-red-600 p-3 rounded mb-4 text-sm text-center border border-red-100">
                    <?php echo esc_html( $error_msg ); ?>
                </div>
            <?php endif; ?>

            <form method="post" class="space-y-4">
                <?php wp_nonce_field( 'adorkini_order_lookup', 'order_lookup_nonce' ); ?>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"><?php _e( 'Order Key (#)', 'adorkini' ); ?></label>
                    <input type="text" name="order_key" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors" placeholder="e.g. 48291">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"><?php _e( 'Email or Phone', 'adorkini' ); ?></label>
                    <input type="text" name="contact_input" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors" placeholder="Required for verification">
                </div>

                <button type="submit" class="w-full bg-primary text-white font-bold py-3 rounded-lg hover:bg-blue-600 transition-colors shadow-md hover:shadow-lg">
                    <?php _e( 'Track Order', 'adorkini' ); ?>
                </button>
            </form>

        <?php else : ?>

            <div class="order-result space-y-4">
                <div class="text-center mb-6">
                     <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-2 text-2xl">
                        ✓
                     </div>
                     <h2 class="text-xl font-bold text-gray-900"><?php _e( 'Order Found', 'adorkini' ); ?></h2>
                </div>

                <div class="border-t border-b border-gray-100 py-4 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Order Status:</span>
                        <span class="font-bold text-gray-800"><?php echo wc_get_order_status_name( $order_found->get_status() ); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Customer Name:</span>
                        <span class="font-medium text-gray-800"><?php echo adorkini_mask_string( $order_found->get_formatted_billing_full_name() ); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Total:</span>
                        <span class="font-bold text-primary"><?php echo $order_found->get_formatted_order_total(); ?></span>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <a href="<?php echo esc_url( home_url('/') ); ?>" class="text-primary hover:underline text-sm"><?php _e( 'Back to Home', 'adorkini' ); ?></a>
                </div>
            </div>

        <?php endif; ?>

    </div>

</div>

<?php get_footer(); ?>
