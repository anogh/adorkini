<?php
/*
Template Name: Order History
*/

// Redirect if not logged in
if (!is_user_logged_in()) {
    wp_redirect(home_url('/login'));
    exit;
}

get_header(); ?>

<!-- Mobile Header -->
<header class="lg:hidden sticky top-0 z-50 w-full border-b border-gray-200/50 dark:border-gray-700/50 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm">
    <div class="container mx-auto px-4">
        <div class="flex h-16 items-center justify-between">
            <a href="<?php echo home_url('/my-account'); ?>" class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full hover:bg-gray-200/50 dark:hover:bg-gray-700/50">
                <span class="material-symbols-outlined text-gray-600 dark:text-gray-300" data-icon="arrow_back"></span>
            </a>
            <h1 class="text-lg font-bold text-gray-900 dark:text-white"><?php echo __t('Order History'); ?></h1>
            <button class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full hover:bg-gray-200/50 dark:hover:bg-gray-700/50">
                <span class="material-symbols-outlined text-gray-600 dark:text-gray-300" data-icon="more_vert"></span>
            </button>
        </div>
    </div>
</header>

<main class="flex-grow">
    <div class="container mx-auto px-4 py-6 lg:px-6 lg:py-8">
        <!-- Breadcrumb for Desktop -->
        <nav class="hidden lg:flex mb-6 text-sm text-gray-500 dark:text-gray-400">
            <a href="<?php echo home_url('/my-account'); ?>" class="hover:text-gray-700 dark:hover:text-gray-300"><?php echo __t('My Account'); ?></a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 dark:text-white"><?php echo __t('Order History'); ?></span>
        </nav>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2"><?php echo __t('Order History'); ?></h1>
            <p class="text-gray-600 dark:text-gray-400"><?php echo __t('View your past orders and track current ones'); ?></p>
        </div>

        <!-- Order Status Summary (Mobile) -->
        <div class="lg:hidden mb-6 grid grid-cols-2 gap-4">
            <?php
            $customer_orders = wc_get_orders(array(
                'customer_id' => get_current_user_id(),
                'limit' => -1,
                'status' => array('completed', 'processing', 'on-hold', 'pending'),
            ));
            
            $to_ship = 0;
            $to_receive = 0;
            $to_review = 0;
            $completed = 0;
            
            foreach ($customer_orders as $order) {
                $status = $order->get_status();
                if ($status === 'processing') $to_ship++;
                if ($status === 'on-hold' || $status === 'pending') $to_receive++;
                if ($status === 'completed') {
                    $to_receive++;
                    $completed++;
                }
            }
            ?>
            
            <div class="bg-white rounded-lg border border-gray-200 p-4 text-center dark:border-gray-700 dark:bg-gray-900/50">
                <span class="material-symbols-outlined text-primary text-2xl mb-1" data-icon="local_shipping"></span>
                <div class="text-lg font-bold text-gray-900 dark:text-white"><?php echo $to_ship; ?></div>
                <div class="text-xs text-gray-500 dark:text-gray-400"><?php echo __t('To Ship'); ?></div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 p-4 text-center dark:border-gray-700 dark:bg-gray-900/50">
                <span class="material-symbols-outlined text-primary text-2xl mb-1" data-icon="package"></span>
                <div class="text-lg font-bold text-gray-900 dark:text-white"><?php echo $to_receive; ?></div>
                <div class="text-xs text-gray-500 dark:text-gray-400"><?php echo __t('To Receive'); ?></div>
            </div>
        </div>

        <!-- Orders List -->
        <div class="bg-white rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-background-dark">
            <?php if ($customer_orders) : ?>
                <!-- Mobile Orders List -->
                <div class="lg:hidden divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($customer_orders as $order) : ?>
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-white">
                                        <?php echo '#' . $order->get_meta('_warafy_order_number') ?: $order->get_order_number(); ?>
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        <?php echo date_i18n('F j, Y', strtotime($order->get_date_created())); ?>
                                    </div>
                                </div>
                                <?php
                                $status = $order->get_status();
                                $status_class = '';
                                $status_text = '';
                                
                                switch ($status) {
                                    case 'completed':
                                        $status_class = 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400';
                                        $status_text = __t('Delivered');
                                        break;
                                    case 'processing':
                                        $status_class = 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400';
                                        $status_text = __t('Processing');
                                        break;
                                    case 'on-hold':
                                        $status_class = 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400';
                                        $status_text = __t('On Hold');
                                        break;
                                    case 'pending':
                                        $status_class = 'bg-gray-100 text-gray-700 dark:bg-gray-900/40 dark:text-gray-400';
                                        $status_text = __t('Pending');
                                        break;
                                    default:
                                        $status_class = 'bg-gray-100 text-gray-700 dark:bg-gray-900/40 dark:text-gray-400';
                                        $status_text = ucfirst($status);
                                }
                                ?>
                                <span class="inline-flex items-center rounded-full <?php echo $status_class; ?> px-2 py-1 text-xs font-medium">
                                    <?php echo $status_text; ?>
                                </span>
                            </div>
                            
                            <div class="space-y-2 mb-3">
                                <?php
                                $items = $order->get_items();
                                foreach ($items as $item) {
                                    $product = $item->get_product();
                                    if ($product) {
                                        ?>
                                        <div class="flex items-center gap-3">
                                            <?php if ($product->get_image_id()) : ?>
                                                <img src="<?php echo wp_get_attachment_image_url($product->get_image_id(), 'thumbnail'); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" class="w-12 h-12 rounded-lg object-cover">
                                            <?php else : ?>
                                                <div class="w-12 h-12 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-gray-400" data-icon="image"></span>
                                                </div>
                                            <?php endif; ?>
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white"><?php echo $product->get_name(); ?></div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400"><?php echo __t('Quantity'); ?>: <?php echo $item->get_quantity(); ?></div>
                                            </div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                <?php echo $order->get_formatted_line_subtotal($item); ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                            
                            <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                                <div class="text-sm">
                                    <span class="text-gray-500 dark:text-gray-400"><?php echo __t('Total'); ?>:</span>
                                    <span class="ml-2 font-semibold text-gray-900 dark:text-white"><?php echo $order->get_formatted_order_total(); ?></span>
                                </div>
                                <a href="<?php echo esc_url($order->get_view_order_url()); ?>" class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-primary text-white rounded hover:bg-primary/90 transition-colors">
                                    <?php echo __t('View Details'); ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Desktop Orders Table -->
                <div class="hidden lg:block">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo __t('Order'); ?></th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo __t('Date'); ?></th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo __t('Status'); ?></th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo __t('Total'); ?></th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo __t('Actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                <?php foreach ($customer_orders as $order) : ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                <?php echo '#' . $order->get_meta('_warafy_order_number') ?: $order->get_order_number(); ?>
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                <?php echo count($order->get_items()) . ' ' . (count($order->get_items()) == 1 ? __t('item') : __t('items')); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <?php echo date_i18n('F j, Y', strtotime($order->get_date_created())); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center rounded-full <?php echo $status_class; ?> px-2 py-1 text-xs font-medium">
                                                <?php echo $status_text; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <?php echo $order->get_formatted_order_total(); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="<?php echo esc_url($order->get_view_order_url()); ?>" class="text-primary hover:text-primary/80">
                                                <?php echo __t('View'); ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else : ?>
                <div class="text-center py-12">
                    <span class="material-symbols-outlined text-5xl text-gray-300 mb-4" data-icon="receipt_long"></span>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2"><?php echo __t('No orders yet'); ?></h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6"><?php echo __t('You haven\'t placed any orders yet. Start shopping to see your order history here.'); ?></p>
                    <a href="<?php echo home_url('/shop'); ?>" class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                        <span class="material-symbols-outlined mr-2" data-icon="shopping_bag"></span>
                        <?php echo __t('Start Shopping'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Order Statistics (Desktop) -->
        <?php if ($customer_orders) : ?>
            <div class="hidden lg:block mt-8 grid grid-cols-4 gap-6">
                <div class="bg-white rounded-lg border border-gray-200 p-6 text-center dark:border-gray-700 dark:bg-gray-900/50">
                    <span class="material-symbols-outlined text-primary text-3xl mb-2" data-icon="local_shipping"></span>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $to_ship; ?></div>
                    <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo __t('Processing'); ?></div>
                </div>
                
                <div class="bg-white rounded-lg border border-gray-200 p-6 text-center dark:border-gray-700 dark:bg-gray-900/50">
                    <span class="material-symbols-outlined text-primary text-3xl mb-2" data-icon="package"></span>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $to_receive; ?></div>
                    <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo __t('To Receive'); ?></div>
                </div>
                
                <div class="bg-white rounded-lg border border-gray-200 p-6 text-center dark:border-gray-700 dark:bg-gray-900/50">
                    <span class="material-symbols-outlined text-primary text-3xl mb-2" data-icon="rate_review"></span>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $completed; ?></div>
                    <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo __t('Completed'); ?></div>
                </div>
                
                <div class="bg-white rounded-lg border border-gray-200 p-6 text-center dark:border-gray-700 dark:bg-gray-900/50">
                    <span class="material-symbols-outlined text-primary text-3xl mb-2" data-icon="history"></span>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo count($customer_orders); ?></div>
                    <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo __t('Total Orders'); ?></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
