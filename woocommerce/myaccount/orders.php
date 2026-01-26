<?php
/**
 * Orders
 *
 * @package Adorkini
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders ); ?>

<?php if ( $has_orders ) : ?>

	<div class="overflow-x-auto">
        <table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : ?>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-<?php echo esc_attr( $column_id ); ?> p-4 font-semibold text-gray-700 text-sm"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
                    <?php endforeach; ?>
                </tr>
            </thead>

            <tbody>
                <?php
                foreach ( $customer_orders->orders as $customer_order ) {
                    $order      = wc_get_order( $customer_order ); // phpcs:ignore
                    $item_count = $order->get_item_count() - $order->get_item_count_refunded();
                    ?>
                    <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr( $order->get_status() ); ?> order border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : ?>
                            <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-<?php echo esc_attr( $column_id ); ?> p-4 text-sm text-gray-600" data-title="<?php echo esc_attr( $column_name ); ?>">
                                <?php if ( has_action( 'woocommerce_my_account_my_orders_column_' . $column_id ) ) : ?>
                                    <?php do_action( 'woocommerce_my_account_my_orders_column_' . $column_id, $order ); ?>
                                <?php elseif ( 'order-number' === $column_id ) : ?>
                                    <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="font-bold text-primary hover:underline">
                                        <?php echo esc_html( _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number() ); ?>
                                    </a>
                                <?php elseif ( 'order-date' === $column_id ) : ?>
                                    <time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time>
                                <?php elseif ( 'order-status' === $column_id ) : ?>
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800 status-badge status-<?php echo esc_attr( $order->get_status() ); ?>">
                                        <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
                                    </span>
                                <?php elseif ( 'order-total' === $column_id ) : ?>
                                    <?php
                                    /* translators: 1: formatted order total 2: total order items */
                                    echo wp_kses_post( sprintf( _n( '%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce' ), $order->get_formatted_order_total(), $item_count ) );
                                    ?>
                                <?php elseif ( 'order-actions' === $column_id ) : ?>
                                    <?php
                                    $actions = wc_get_account_orders_actions( $order );

                                    if ( ! empty( $actions ) ) {
                                        foreach ( $actions as $key => $action ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                                            echo '<a href="' . esc_url( $action['url'] ) . '" class="woocommerce-button button ' . sanitize_html_class( $key ) . ' text-primary hover:text-blue-700 font-medium mr-2">' . esc_html( $action['name'] ) . '</a>';
                                        }
                                    }
                                    ?>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>

	<?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

	<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
		<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination mt-6 flex justify-between">
			<?php if ( 1 !== $current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button px-4 py-2 border border-gray-300 rounded hover:bg-gray-50" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'woocommerce' ); ?></a>
			<?php endif; ?>

			<?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button px-4 py-2 border border-gray-300 rounded hover:bg-gray-50 ml-auto" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'woocommerce' ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php else : ?>
	
    <div class="text-center py-12 bg-gray-50 rounded-xl border border-dashed border-gray-300">
        <div class="mb-4 text-gray-400">
            <?php adorkini_icon('cart', 'text-4xl'); ?>
        </div>
        <h3 class="text-lg font-bold text-gray-700 mb-2"><?php esc_html_e( 'No orders found', 'woocommerce' ); ?></h3>
        <a class="woocommerce-Button button px-6 py-2 bg-primary text-white rounded hover:bg-blue-600 transition-colors inline-block mt-2" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
            <?php esc_html_e( 'Browse products', 'woocommerce' ); ?>
        </a>
    </div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
