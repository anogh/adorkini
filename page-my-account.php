<?php
/*
Template Name: My Account Page
*/

// Redirect if not logged in
if (!is_user_logged_in()) {
    wp_redirect(home_url('/login'));
    exit;
}

$current_user = wp_get_current_user();
$user_id = get_current_user_id();
$account_url = home_url('/my-account');

// Handle form submissions
$message = '';
$message_type = '';

// Handle Personal Info Update
if (isset($_POST['save_account_details']) && wp_verify_nonce($_POST['update_account_nonce'], 'update_account_details')) {
    $email = sanitize_email($_POST['email']);
    
    if (!is_email($email)) {
        $message = __t('Invalid email address.');
        $message_type = 'error';
    } elseif (email_exists($email) && $email !== $current_user->user_email) {
        $message = __t('Email already in use.');
        $message_type = 'error';
    } else {
        wp_update_user(array(
            'ID' => $user_id,
            'first_name' => sanitize_text_field($_POST['first_name']),
            'last_name' => sanitize_text_field($_POST['last_name']),
            'user_email' => $email,
            'display_name' => sanitize_text_field($_POST['display_name'])
        ));
        update_user_meta($user_id, 'billing_phone', sanitize_text_field($_POST['phone']));
        $message = __t('Personal details updated successfully.');
        $message_type = 'success';
        $current_user = wp_get_current_user();
    }
}

// Handle Address Update
if (isset($_POST['save_address']) && wp_verify_nonce($_POST['update_address_nonce'], 'update_address')) {
    $customer = new WC_Customer($user_id);
    $customer->set_shipping_address_1(sanitize_text_field($_POST['shipping_address_1']));
    $customer->set_shipping_address_2(sanitize_text_field($_POST['shipping_address_2']));
    $customer->set_shipping_city(sanitize_text_field($_POST['shipping_city']));
    $customer->set_shipping_state(sanitize_text_field($_POST['shipping_state']));
    $customer->set_shipping_postcode(sanitize_text_field($_POST['shipping_postcode']));
    $customer->set_shipping_country(sanitize_text_field($_POST['shipping_country']));
    $customer->save();
    $message = __t('Shipping address updated successfully.');
    $message_type = 'success';
}

// Get WooCommerce customer data
$customer = new WC_Customer($user_id);

// Get orders
$customer_orders = wc_get_orders(array(
    'customer_id' => $user_id,
    'limit' => 10,
    'orderby' => 'date',
    'order' => 'DESC',
));

$display_name = $current_user->display_name ?: ($current_user->first_name . ' ' . $current_user->last_name);
if (trim($display_name) === '') {
    $display_name = $current_user->user_login;
}

get_header(); 
?>

<!-- Desktop Content -->
<main class="hidden lg:block flex-grow bg-background-light dark:bg-background-dark">
    <div class="container mx-auto px-6 py-8 max-w-5xl">
        
        <!-- Page Title -->
        <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white mb-8"><?php echo __t('My Account'); ?></h1>
        
        <!-- Success/Error Messages -->
        <?php if ($message) : ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $message_type === 'success' ? 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800' : 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800'; ?>">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-xl" data-icon="<?php echo $message_type === 'success' ? 'check_circle' : 'error'; ?>"></span>
                    <?php echo esc_html($message); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Profile Card -->
        <div class="bg-gradient-to-r from-primary to-primary/80 rounded-2xl p-6 mb-6 text-white">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 lg:w-20 lg:h-20 rounded-full bg-white/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-3xl lg:text-4xl" data-icon="person"></span>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl lg:text-2xl font-bold"><?php echo esc_html($display_name); ?></h2>
                    <p class="text-white/80 text-sm lg:text-base"><?php echo esc_html($current_user->user_email); ?></p>
                    <?php if ($phone = get_user_meta($user_id, 'billing_phone', true)) : ?>
                        <p class="text-white/70 text-sm"><?php echo esc_html($phone); ?></p>
                    <?php endif; ?>
                </div>
                <a href="<?php echo wp_logout_url(home_url()); ?>" class="hidden lg:flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-xl" data-icon="logout"></span>
                    <span class="font-medium"><?php echo __t('Logout'); ?></span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Personal Information Section -->
            <div class="bg-white rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-background-dark overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary" data-icon="person"></span>
                        <h3 class="font-bold text-gray-900 dark:text-white"><?php echo __t('Personal Information'); ?></h3>
                    </div>
                </div>
                <form method="post" class="p-6">
                    <?php wp_nonce_field('update_account_details', 'update_account_nonce'); ?>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"><?php echo __t('First Name'); ?></label>
                                <input type="text" name="first_name" value="<?php echo esc_attr($current_user->first_name); ?>" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"><?php echo __t('Last Name'); ?></label>
                                <input type="text" name="last_name" value="<?php echo esc_attr($current_user->last_name); ?>" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"><?php echo __t('Display Name'); ?></label>
                            <input type="text" name="display_name" value="<?php echo esc_attr($current_user->display_name); ?>" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"><?php echo __t('Email Address'); ?></label>
                            <input type="email" name="email" value="<?php echo esc_attr($current_user->user_email); ?>" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"><?php echo __t('Phone Number'); ?></label>
                            <input type="tel" name="phone" value="<?php echo esc_attr(get_user_meta($user_id, 'billing_phone', true)); ?>" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>
                        <button type="submit" name="save_account_details" class="w-full px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium text-sm">
                            <?php echo __t('Save Changes'); ?>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Shipping Address Section -->
            <div class="bg-white rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-background-dark overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary" data-icon="local_shipping"></span>
                        <h3 class="font-bold text-gray-900 dark:text-white"><?php echo __t('Shipping Address'); ?></h3>
                    </div>
                </div>
                <form method="post" class="p-6">
                    <?php wp_nonce_field('update_address', 'update_address_nonce'); ?>
                    <div class="space-y-4">
                        <div class="space-y-3">
                            <input type="text" name="shipping_address_1" placeholder="<?php echo __t('Street Address'); ?>" value="<?php echo esc_attr($customer->get_shipping_address_1()); ?>" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <input type="text" name="shipping_address_2" placeholder="<?php echo __t('Apartment, suite, etc.'); ?>" value="<?php echo esc_attr($customer->get_shipping_address_2()); ?>" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <div class="grid grid-cols-2 gap-3">
                                <input type="text" name="shipping_city" placeholder="<?php echo __t('City'); ?>" value="<?php echo esc_attr($customer->get_shipping_city()); ?>" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                <input type="text" name="shipping_postcode" placeholder="<?php echo __t('Postal Code'); ?>" value="<?php echo esc_attr($customer->get_shipping_postcode()); ?>" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <input type="text" name="shipping_state" placeholder="<?php echo __t('State/Province'); ?>" value="<?php echo esc_attr($customer->get_shipping_state()); ?>" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                <input type="text" name="shipping_country" placeholder="<?php echo __t('Country'); ?>" value="<?php echo esc_attr($customer->get_shipping_country()); ?>" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            </div>
                        </div>
                        
                        <button type="submit" name="save_address" class="w-full px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium text-sm">
                            <?php echo __t('Save Shipping Address'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Order History Section -->
        <div class="mt-6 bg-white rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-background-dark overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary" data-icon="receipt_long"></span>
                        <h3 class="font-bold text-gray-900 dark:text-white"><?php echo __t('Order History'); ?></h3>
                    </div>
                    <span class="text-sm text-gray-500 dark:text-gray-400"><?php echo count($customer_orders); ?> <?php echo __t('orders'); ?></span>
                </div>
            </div>
            
            <?php if ($customer_orders) : ?>
                <!-- Desktop Order Table -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo __t('Order'); ?></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo __t('Date'); ?></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo __t('Status'); ?></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo __t('Total'); ?></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo __t('Actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <?php foreach ($customer_orders as $order) : 
                                $status = $order->get_status();
                                $status_colors = array(
                                    'completed' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400',
                                    'processing' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
                                    'on-hold' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400',
                                    'pending' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-400',
                                    'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400',
                                    'refunded' => 'bg-gray-100 text-gray-700 dark:bg-gray-900/40 dark:text-gray-400',
                                    'failed' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400',
                                );
                                $status_class = isset($status_colors[$status]) ? $status_colors[$status] : 'bg-gray-100 text-gray-700 dark:bg-gray-900/40 dark:text-gray-400';
                            ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">#<?php echo $order->get_order_number(); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo date_i18n('M j, Y', strtotime($order->get_date_created())); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center rounded-full <?php echo $status_class; ?> px-2.5 py-1 text-xs font-medium"><?php echo ucfirst($status); ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"><?php echo $order->get_formatted_order_total(); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="<?php echo esc_url($order->get_view_order_url()); ?>" class="text-primary hover:text-primary/80 font-medium"><?php echo __t('View'); ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <div class="p-8 text-center">
                    <span class="material-symbols-outlined text-4xl text-gray-400 mb-2" data-icon="inventory_2"></span>
                    <p class="text-gray-500 dark:text-gray-400"><?php echo __t('No orders yet'); ?></p>
                    <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="inline-block mt-4 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors text-sm font-medium">
                        <?php echo __t('Start Shopping'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- Mobile Content -->
<main class="lg:hidden flex-grow bg-white dark:bg-background-dark">
    <div class="container mx-auto px-4 py-6">
        
        <!-- Success/Error Messages -->
        <?php if ($message) : ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $message_type === 'success' ? 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800' : 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800'; ?>">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-xl" data-icon="<?php echo $message_type === 'success' ? 'check_circle' : 'error'; ?>"></span>
                    <?php echo esc_html($message); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Profile Card -->
        <div class="bg-gradient-to-r from-primary to-primary/80 rounded-2xl p-6 mb-6 text-white">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-3xl" data-icon="person"></span>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold"><?php echo esc_html($display_name); ?></h2>
                    <p class="text-white/80 text-sm"><?php echo esc_html($current_user->user_email); ?></p>
                </div>
                <a href="<?php echo wp_logout_url(home_url()); ?>" class="flex items-center gap-2 px-3 py-1.5 bg-white/20 hover:bg-white/30 rounded-lg transition-colors text-sm">
                    <span class="material-symbols-outlined text-lg" data-icon="logout"></span>
                </a>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="space-y-3 mb-6">
            <a href="<?php echo esc_url(home_url('/account-personal-info')); ?>" class="flex items-center justify-between p-4 bg-white dark:bg-background-dark rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary" data-icon="person"></span>
                    <span class="font-medium text-gray-900 dark:text-white"><?php echo __t('Personal Information'); ?></span>
                </div>
                <span class="material-symbols-outlined text-gray-400" data-icon="chevron_right"></span>
            </a>
            <a href="<?php echo esc_url(home_url('/account-addresses')); ?>" class="flex items-center justify-between p-4 bg-white dark:bg-background-dark rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary" data-icon="local_shipping"></span>
                    <span class="font-medium text-gray-900 dark:text-white"><?php echo __t('Addresses'); ?></span>
                </div>
                <span class="material-symbols-outlined text-gray-400" data-icon="chevron_right"></span>
            </a>
            <a href="<?php echo esc_url(home_url('/account-orders')); ?>" class="flex items-center justify-between p-4 bg-white dark:bg-background-dark rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary" data-icon="receipt_long"></span>
                    <span class="font-medium text-gray-900 dark:text-white"><?php echo __t('Order History'); ?></span>
                </div>
                <span class="material-symbols-outlined text-gray-400" data-icon="chevron_right"></span>
            </a>
        </div>

        <!-- Recent Orders -->
        <?php if ($customer_orders) : ?>
            <div class="bg-white dark:bg-background-dark rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <h3 class="font-bold text-gray-900 dark:text-white"><?php echo __t('Recent Orders'); ?></h3>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach (array_slice($customer_orders, 0, 5) as $order) : 
                        $status = $order->get_status();
                        $status_colors = array(
                            'completed' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400',
                            'processing' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
                            'on-hold' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400',
                            'pending' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-400',
                            'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400',
                            'refunded' => 'bg-gray-100 text-gray-700 dark:bg-gray-900/40 dark:text-gray-400',
                            'failed' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400',
                        );
                        $status_class = isset($status_colors[$status]) ? $status_colors[$status] : 'bg-gray-100 text-gray-700 dark:bg-gray-900/40 dark:text-gray-400';
                    ?>
                        <a href="<?php echo esc_url($order->get_view_order_url()); ?>" class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">#<?php echo $order->get_order_number(); ?></div>
                                <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo date_i18n('M j, Y', strtotime($order->get_date_created())); ?></div>
                            </div>
                            <div class="text-right">
                                <div class="font-medium text-gray-900 dark:text-white"><?php echo $order->get_formatted_order_total(); ?></div>
                                <span class="inline-flex items-center rounded-full <?php echo $status_class; ?> px-2 py-0.5 text-xs font-medium"><?php echo ucfirst($status); ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php if (count($customer_orders) > 5) : ?>
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="<?php echo esc_url(home_url('/account-orders')); ?>" class="block text-center text-primary font-medium text-sm"><?php echo __t('View All Orders'); ?></a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
