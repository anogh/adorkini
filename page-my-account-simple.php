<?php
/*
Template Name: My Account Simple
*/

// Redirect if not logged in
if (!is_user_logged_in()) {
    wp_redirect(home_url('/login'));
    exit;
}

$current_user = wp_get_current_user();
$user_id = get_current_user_id();
$view = isset($_GET['view']) ? sanitize_text_field($_GET['view']) : 'dashboard';

// Handle form submissions for non-AJAX actions
$message = '';
$message_type = '';

if (isset($_POST['save_account_details']) && wp_verify_nonce($_POST['update_account_nonce'], 'update_account_details')) {
    $email = sanitize_email($_POST['email']);
    
    if (!is_email($email)) {
        $message = 'Invalid email address.';
        $message_type = 'error';
    } elseif (email_exists($email) && $email !== $current_user->user_email) {
        $message = 'Email already in use.';
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
        $message = 'Personal details updated successfully.';
        $message_type = 'success';
        $current_user = wp_get_current_user();
    }
}

if (isset($_POST['save_address']) && wp_verify_nonce($_POST['update_address_nonce'], 'update_address')) {
    if (class_exists('WC_Customer')) {
        $customer = new WC_Customer($user_id);
        $customer->set_billing_address_1(sanitize_text_field($_POST['billing_address_1']));
        $customer->set_billing_address_2(sanitize_text_field($_POST['billing_address_2']));
        $customer->set_billing_city(sanitize_text_field($_POST['billing_city']));
        $customer->set_billing_state(sanitize_text_field($_POST['billing_state']));
        $customer->set_billing_postcode(sanitize_text_field($_POST['billing_postcode']));
        $customer->set_billing_country(sanitize_text_field($_POST['billing_country']));
        
        $customer->set_shipping_address_1(sanitize_text_field($_POST['shipping_address_1']));
        $customer->set_shipping_address_2(sanitize_text_field($_POST['shipping_address_2']));
        $customer->set_shipping_city(sanitize_text_field($_POST['shipping_city']));
        $customer->set_shipping_state(sanitize_text_field($_POST['shipping_state']));
        $customer->set_shipping_postcode(sanitize_text_field($_POST['shipping_postcode']));
        $customer->set_shipping_country(sanitize_text_field($_POST['shipping_country']));
        
        $customer->save();
        $message = 'Addresses updated successfully.';
        $message_type = 'success';
    }
}

// Get Data for Dashboard
$customer_orders = array();
if (function_exists('wc_get_orders')) {
    $customer_orders = wc_get_orders(array(
        'customer_id' => $user_id,
        'limit' => -1,
    ));
}
$order_count = count($customer_orders);
$total_spent = function_exists('wc_price') ? wc_price(array_reduce($customer_orders, function($carry, $order) {
    return $carry + ($order->get_status() === 'completed' ? (float)$order->get_total() : 0);
}, 0)) : '0';

$display_name = $current_user->display_name ?: ($current_user->first_name . ' ' . $current_user->last_name);
if (trim($display_name) === '') {
    $display_name = $current_user->user_login;
}

get_header(); ?>

<style>
/* Force dark theme for My Account page — comprehensive override */
/* Background overrides */
.acct-main { background-color: #000000 !important; color: #e5e7eb !important; }
.acct-main .bg-white { background-color: #1f2937 !important; }
.acct-main .bg-gray-50 { background-color: #111827 !important; }
.acct-main .bg-gray-100 { background-color: #1f2937 !important; }
.acct-main .bg-background-light { background-color: #000000 !important; }

/* Text color overrides */
.acct-main .text-gray-900 { color: #ffffff !important; }
.acct-main .text-gray-800 { color: #e5e7eb !important; }
.acct-main .text-gray-700 { color: #d1d5db !important; }
.acct-main .text-gray-600 { color: #9ca3af !important; }
.acct-main .text-gray-500 { color: #9ca3af !important; }
.acct-main .text-gray-400 { color: #6b7280 !important; }
.acct-main .text-gray-300 { color: #6b7280 !important; }

/* Border overrides */
.acct-main .border-gray-200 { border-color: #374151 !important; }
.acct-main .border-gray-100 { border-color: #374151 !important; }
.acct-main .divide-gray-100 > * + * { border-color: #374151 !important; }
.acct-main .divide-gray-200 > * + * { border-color: #374151 !important; }

/* Input overrides */
.acct-main input[type="text"],
.acct-main input[type="email"],
.acct-main input[type="tel"],
.acct-main input[type="password"],
.acct-main input[type="number"],
.acct-main select,
.acct-main textarea {
    background-color: #111827 !important;
    border-color: #374151 !important;
    color: #ffffff !important;
}
.acct-main input::placeholder,
.acct-main textarea::placeholder { color: #6b7280 !important; }

/* Hover states */
.acct-main .hover\:bg-gray-100:hover { background-color: #374151 !important; }
.acct-main .hover\:bg-gray-50:hover { background-color: rgba(55,65,81,0.5) !important; }

/* Card containers with borders */
.acct-main [class*="rounded-2xl"][class*="border"] {
    background-color: #1f2937 !important;
    border-color: #374151 !important;
}

/* Tab styles - inactive tabs */
.acct-main a[class*="rounded-full"][class*="border"][class*="bg-white"] {
    background-color: #1f2937 !important;
    border-color: #374151 !important;
    color: #9ca3af !important;
}
.acct-main a[class*="rounded-full"][class*="border"][class*="bg-white"]:hover {
    background-color: #374151 !important;
}

/* Table head */
.acct-main thead .bg-gray-50,
.acct-main thead[class*="bg-gray"] { background-color: rgba(55,65,81,0.5) !important; }
.acct-main tr:hover { background-color: rgba(55,65,81,0.3) !important; }

/* Misc overrides */
.acct-main .bg-orange-100 { background-color: rgba(245,166,35,0.1) !important; }
.acct-main .bg-red-100 { background-color: rgba(239,68,68,0.1) !important; }
.acct-main .text-orange-600 { color: #F5A623 !important; }
.acct-main .text-red-600 { color: #ef4444 !important; }

/* Secondary button style */
.acct-main a[class*="bg-gray-50"][class*="rounded-lg"],
.acct-main button[class*="bg-gray-50"][class*="rounded-lg"] {
    background-color: rgba(55,65,81,0.5) !important;
    color: #d1d5db !important;
    border-color: #4b5563 !important;
}
.acct-main a[class*="bg-gray-50"][class*="rounded-lg"]:hover {
    background-color: #374151 !important;
}

/* Logout button */
.acct-main a[class*="border"][class*="rounded-lg"][class*="text-gray-700"] {
    border-color: #374151 !important;
    color: #d1d5db !important;
}
.acct-main a[class*="border"][class*="rounded-lg"][class*="text-gray-700"]:hover {
    background-color: #1f2937 !important;
}

/* Help section */
.acct-main [class*="bg-primary\/5"] { background-color: rgba(245,166,35,0.1) !important; }

/* Success/Error messages */
.acct-main .bg-green-50 { background-color: rgba(34,197,94,0.1) !important; }
.acct-main .bg-red-50 { background-color: rgba(239,68,68,0.1) !important; }
.acct-main .text-green-700 { color: #4ade80 !important; }
.acct-main .text-red-700 { color: #f87171 !important; }

/* =========================================
   GLOBAL HEADER & FOOTER OVERRIDES (Force Dark)
   ========================================= */
   
/* Force Primary Color for Icons (fixes missing icons on desktop) */
.acct-main .bg-primary, header .bg-primary, footer .bg-primary {
    background-color: #F5A623 !important;
}
.acct-main .text-primary, header .text-primary, footer .text-primary {
    color: #F5A623 !important;
}

/* Force Desktop Header to Dark Mode */
header.hidden.lg\:block {
    background-color: #000000 !important;
    border-bottom-color: #374151 !important;
}

/* Fix Search Bar on Desktop Header */
header input[type="search"] {
    background-color: #1f2937 !important; /* Dark gray background */
    border-color: #374151 !important;
    color: #ffffff !important;
}
header form .material-symbols-outlined {
    color: #9ca3af !important; /* Search icon color */
}

/* Force Header Navigation Links to Light Gray */
header nav a {
    color: #e5e7eb !important; /* gray-200 */
}
header nav a:hover {
    color: #F5A623 !important; /* primary */
}

/* Force Language Toggle Styling */
header .warafy-language-toggle {
    background-color: #1f2937 !important; /* gray-800 */
    border-color: #374151 !important; /* gray-700 */
    color: #e5e7eb !important; /* gray-200 */
}
header .warafy-language-toggle svg {
    color: #9ca3af !important; /* gray-400 */
}

/* Fix Cart Count Visibility */
header .cart-count {
    color: #F5A623 !important; /* Orange text */
    background-color: #ffffff !important; /* White background */
    z-index: 50 !important;
}

/* Logo size is now controlled globally via Admin → Logo Settings */

/* Force Mobile Header to Dark Mode */
header[class*="bg-background-light"] {
    background-color: rgba(0, 0, 0, 0.9) !important;
    border-bottom-color: rgba(55, 65, 81, 0.5) !important;
}
header h1 { color: #ffffff !important; }
header .material-symbols-outlined { color: #d1d5db !important; } /* Icons */
header button, header a:not(.bg-primary) { color: #d1d5db !important; } /* General text/buttons, exclude primary buttons */

/* Force Footer to Dark Mode */
footer { 
    background-color: #000000 !important; 
    border-top-color: #374151 !important; 
}
footer h4 { color: #ffffff !important; }
footer a { color: #9ca3af !important; }
footer p { color: #9ca3af !important; }
footer .text-gray-900 { color: #ffffff !important; }
footer .text-gray-500 { color: #9ca3af !important; }

/* Force Mobile Bottom Nav to Dark Mode */
nav.fixed.bottom-0 {
    background-color: rgba(0, 0, 0, 0.9) !important; /* Dark background */
    border-top-color: #1e293b !important; /* Dark border */
}
/* Bottom nav icons container - Force dark/gray background instead of white */
nav.fixed.bottom-0 .bg-white {
    background-color: #1f2937 !important; 
    box-shadow: none !important;
}
/* Profile active state - highlighted orange text */
nav.fixed.bottom-0 .text-primary { 
    color: #F5A623 !important; 
}
/* Inactive states */
nav.fixed.bottom-0 .text-slate-500 { 
    color: #94a3b8 !important; 
}
/* Highlight the Profile icon specifically - The last item in the nav */
nav.fixed.bottom-0 > div > a:last-child {
    color: #F5A623 !important; /* Force orange text */
}
nav.fixed.bottom-0 > div > a:last-child > div:first-child {
    background-color: #F5A623 !important; /* Force orange bg circle */
}
nav.fixed.bottom-0 > div > a:last-child > div:first-child svg {
    stroke: #ffffff !important; /* Force white icon */
}
nav.fixed.bottom-0 > div > a:last-child span {
    font-weight: 700 !important; /* Bold text */
}
</style>

<main class="acct-main flex-grow min-h-screen pb-20 lg:pb-12" style="background-color: #000000;">
    <div class="container mx-auto px-4 py-8 lg:px-6 max-w-6xl">
        
        <!-- Welcome Section -->
        <div class="mb-8 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white"><?php echo __t('Welcome back'); ?>, <?php echo esc_html($display_name); ?>!</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1"><?php echo __t('Manage your account, orders, and addresses from here.'); ?></p>
            </div>
            <a href="<?php echo wp_logout_url(home_url()); ?>" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors w-fit">
                <span class="material-symbols-outlined text-lg" data-icon="logout"></span>
                <span><?php echo __t('Log Out'); ?></span>
            </a>
        </div>

        <?php if ($message) : ?>
            <div class="mb-6 p-4 rounded-xl <?php echo $message_type === 'success' ? 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800' : 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800'; ?>">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-xl" data-icon="<?php echo $message_type === 'success' ? 'check_circle' : 'error'; ?>"></span>
                    <p class="font-medium text-sm"><?php echo esc_html($message); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Responsive Tab Navigation -->
        <div class="flex overflow-x-auto pb-4 mb-8 lg:mb-12 scrollbar-none gap-2">
            <?php
            $tabs = [
                'dashboard' => ['label' => __t('Dashboard'), 'icon' => 'dashboard'],
                'orders' => ['label' => __t('Orders'), 'icon' => 'receipt_long'],
                'addresses' => ['label' => __t('Addresses'), 'icon' => 'home'],
                'personal-info' => ['label' => __t('Personal Info'), 'icon' => 'person'],
                'password' => ['label' => __t('Security'), 'icon' => 'lock'],
            ];
            foreach ($tabs as $key => $tab) :
                $active = ($view === $key);
            ?>
                <a href="<?php echo esc_url(add_query_arg('view', $key, home_url('/my-account'))); ?>" 
                   class="flex items-center gap-2 px-4 py-2.5 rounded-full whitespace-nowrap transition-all text-sm font-bold <?php echo $active ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700'; ?>">
                    <span class="material-symbols-outlined text-lg" data-icon="<?php echo $tab['icon']; ?>"></span>
                    <span><?php echo $tab['label']; ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- View Content -->
        <div class="w-full">
            <?php if ($view === 'dashboard') : ?>
                <!-- Dashboard View -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Stats Card 1 -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-2xl" data-icon="shopping_bag"></span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider"><?php echo __t('Total Orders'); ?></p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $order_count; ?></h3>
                        </div>
                    </div>
                    <!-- Stats Card 2 -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-orange-100 dark:bg-orange-500/10 flex items-center justify-center text-orange-600 dark:text-orange-400">
                            <span class="material-symbols-outlined text-2xl" data-icon="payments"></span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider"><?php echo __t('Total Spent'); ?></p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $total_spent; ?></h3>
                        </div>
                    </div>
                    <!-- Stats Card 3 -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-red-100 dark:bg-red-500/10 flex items-center justify-center text-red-600 dark:text-red-400">
                            <span class="material-symbols-outlined text-2xl" data-icon="favorite"></span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider"><?php echo __t('Wishlist'); ?></p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white" id="dashboard-wishlist-count">0</h3>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Recent Orders -->
                    <div class="lg:col-span-2 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white"><?php echo __t('Recent Orders'); ?></h3>
                            <a href="?view=orders" class="text-sm font-bold text-primary hover:underline"><?php echo __t('View All'); ?></a>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <?php if (!empty($customer_orders)) : ?>
                                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <?php 
                                    $recent = array_slice($customer_orders, 0, 5);
                                    foreach ($recent as $order) : 
                                        $status = $order->get_status();
                                    ?>
                                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                                                    <span class="material-symbols-outlined" data-icon="package_2"></span>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">#<?php echo $order->get_order_number(); ?></p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo date_i18n('M j, Y', strtotime($order->get_date_created())); ?></p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-bold text-gray-900 dark:text-white"><?php echo $order->get_formatted_order_total(); ?></p>
                                                <span class="text-[10px] font-bold uppercase tracking-widest text-primary"><?php echo ucfirst($status); ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else : ?>
                                <div class="p-12 text-center">
                                    <span class="block material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600 mb-2" data-icon="shopping_basket"></span>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm"><?php echo __t('No orders found yet.'); ?></p>
                                    <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="mt-4 inline-block text-primary font-bold text-sm"><?php echo __t('Start Shopping'); ?></a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Side Cards -->
                    <div class="space-y-6">
                        <!-- Profile Quick Edit -->
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4"><?php echo __t('Profile Settings'); ?></h3>
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-12 h-12 rounded-full bg-primary flex items-center justify-center text-white">
                                    <span class="material-symbols-outlined" data-icon="person"></span>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-white leading-none"><?php echo esc_html($display_name); ?></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><?php echo esc_html($current_user->user_email); ?></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-2">
                                <a href="?view=personal-info" class="w-full py-2 text-center text-sm font-bold bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border border-gray-100 dark:border-gray-600"><?php echo __t('Account Details'); ?></a>
                                <a href="?view=addresses" class="w-full py-2 text-center text-sm font-bold bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border border-gray-100 dark:border-gray-600"><?php echo __t('Shipping Addresses'); ?></a>
                            </div>
                        </div>
                        <!-- Need Help? -->
                        <div class="bg-primary/5 dark:bg-primary/10 p-6 rounded-2xl border border-primary/20">
                            <h4 class="font-bold text-gray-900 dark:text-white mb-2"><?php echo __t('Need Help?'); ?></h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4"><?php echo __t('Our support team is always here for you. We provide 24/7 assistance for all your queries.'); ?></p>
                            <a href="#" class="text-sm font-bold text-primary hover:underline inline-flex items-center gap-1">
                                <?php echo __t('Contact Support'); ?>
                                <span class="material-symbols-outlined text-sm" data-icon="arrow_forward"></span>
                            </a>
                        </div>
                    </div>
                </div>

            <?php elseif ($view === 'orders') : ?>
                <!-- Orders View -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <?php if (!empty($customer_orders)) : ?>
                        <!-- Desktop Order Table -->
                        <div class="hidden lg:block overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                    <tr>
                                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo __t('Order'); ?></th>
                                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo __t('Date'); ?></th>
                                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo __t('Status'); ?></th>
                                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?php echo __t('Total'); ?></th>
                                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right"><?php echo __t('Action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <?php foreach ($customer_orders as $order) : ?>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">#<?php echo $order->get_order_number(); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><?php echo date_i18n('M j, Y', strtotime($order->get_date_created())); ?></td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary/10 text-primary uppercase tracking-wider">
                                                    <?php echo ucfirst($order->get_status()); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white"><?php echo $order->get_formatted_order_total(); ?></td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="<?php echo esc_url($order->get_view_order_url()); ?>" class="text-sm font-bold text-primary hover:underline"><?php echo __t('View Details'); ?></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Mobile Order List -->
                        <div class="lg:hidden divide-y divide-gray-100 dark:divide-gray-700">
                             <?php foreach ($customer_orders as $order) : ?>
                                <div class="p-4 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">#<?php echo $order->get_order_number(); ?></p>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary uppercase tracking-wider"> <?php echo ucfirst($order->get_status()); ?></span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                        <p><?php echo date_i18n('M j, Y', strtotime($order->get_date_created())); ?></p>
                                        <p class="font-bold text-gray-900 dark:text-white"><?php echo $order->get_formatted_order_total(); ?></p>
                                    </div>
                                    <a href="<?php echo esc_url($order->get_view_order_url()); ?>" class="block w-full py-2 text-center text-xs font-bold bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 rounded-lg"><?php echo __t('View Details'); ?></a>
                                </div>
                             <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="p-12 text-center">
                            <span class="block material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600 mb-2" data-icon="shopping_basket"></span>
                            <p class="text-gray-500 dark:text-gray-400 text-sm"><?php echo __t('You haven\'t placed any orders yet.'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>

            <?php elseif ($view === 'addresses') : ?>
                <!-- Addresses View -->
                <form method="post">
                    <?php wp_nonce_field('update_address', 'update_address_nonce'); ?>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Billing Address -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 space-y-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-4"><?php echo __t('Billing Address'); ?></h3>
                            <div class="grid grid-cols-1 gap-4">
                                <input type="text" name="billing_address_1" placeholder="<?php echo __t('Street Address'); ?>" value="<?php echo esc_attr(get_user_meta($user_id, 'billing_address_1', true)); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none text-gray-900 dark:text-white">
                                <input type="text" name="billing_address_2" placeholder="<?php echo __t('Apartment, suite, etc.'); ?>" value="<?php echo esc_attr(get_user_meta($user_id, 'billing_address_2', true)); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none text-gray-900 dark:text-white">
                                <div class="grid grid-cols-2 gap-4">
                                    <input type="text" name="billing_city" placeholder="<?php echo __t('City'); ?>" value="<?php echo esc_attr(get_user_meta($user_id, 'billing_city', true)); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm">
                                    <input type="text" name="billing_postcode" placeholder="<?php echo __t('Zip Code'); ?>" value="<?php echo esc_attr(get_user_meta($user_id, 'billing_postcode', true)); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <input type="text" name="billing_state" placeholder="<?php echo __t('State'); ?>" value="<?php echo esc_attr(get_user_meta($user_id, 'billing_state', true)); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm">
                                    <input type="text" name="billing_country" placeholder="<?php echo __t('Country'); ?>" value="<?php echo esc_attr(get_user_meta($user_id, 'billing_country', true)); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm">
                                </div>
                            </div>
                        </div>
                        <!-- Shipping Address -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 space-y-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-4"><?php echo __t('Shipping Address'); ?></h3>
                            <div class="grid grid-cols-1 gap-4">
                                <input type="text" name="shipping_address_1" placeholder="<?php echo __t('Street Address'); ?>" value="<?php echo esc_attr(get_user_meta($user_id, 'shipping_address_1', true)); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm">
                                <input type="text" name="shipping_address_2" placeholder="<?php echo __t('Apartment, suite, etc.'); ?>" value="<?php echo esc_attr(get_user_meta($user_id, 'shipping_address_2', true)); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm">
                                <div class="grid grid-cols-2 gap-4">
                                    <input type="text" name="shipping_city" placeholder="<?php echo __t('City'); ?>" value="<?php echo esc_attr(get_user_meta($user_id, 'shipping_city', true)); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm">
                                    <input type="text" name="shipping_postcode" placeholder="<?php echo __t('Zip Code'); ?>" value="<?php echo esc_attr(get_user_meta($user_id, 'shipping_postcode', true)); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <input type="text" name="shipping_state" placeholder="<?php echo __t('State'); ?>" value="<?php echo esc_attr(get_user_meta($user_id, 'shipping_state', true)); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm">
                                    <input type="text" name="shipping_country" placeholder="<?php echo __t('Country'); ?>" value="<?php echo esc_attr(get_user_meta($user_id, 'shipping_country', true)); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8">
                        <button type="submit" name="save_address" class="w-full lg:w-fit px-8 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 transition-all shadow-lg shadow-primary/30">
                            <?php echo __t('Update Addresses'); ?>
                        </button>
                    </div>
                </form>

            <?php elseif ($view === 'personal-info') : ?>
                <!-- Personal Info View -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 lg:p-8 max-w-2xl">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 border-b border-gray-100 dark:border-gray-700 pb-4"><?php echo __t('Personal Details'); ?></h3>
                    <form method="post" class="space-y-6">
                        <?php wp_nonce_field('update_account_details', 'update_account_nonce'); ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 dark:text-gray-300"><?php echo __t('First Name'); ?></label>
                                <input type="text" name="first_name" value="<?php echo esc_attr($current_user->first_name); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-gray-900 dark:text-white" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 dark:text-gray-300"><?php echo __t('Last Name'); ?></label>
                                <input type="text" name="last_name" value="<?php echo esc_attr($current_user->last_name); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-gray-900 dark:text-white" required>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 dark:text-gray-300"><?php echo __t('Display Name'); ?></label>
                            <input type="text" name="display_name" value="<?php echo esc_attr($current_user->display_name); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-gray-900 dark:text-white" required>
                            <p class="text-[10px] text-gray-500 italic mt-1"><?php echo __t('This name will be displayed in the account section and in reviews.'); ?></p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 dark:text-gray-300"><?php echo __t('Email Address'); ?></label>
                            <input type="email" name="email" value="<?php echo esc_attr($current_user->user_email); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-gray-900 dark:text-white" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 dark:text-gray-300"><?php echo __t('Phone Number'); ?></label>
                            <input type="tel" name="phone" value="<?php echo esc_attr(get_user_meta($user_id, 'billing_phone', true)); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-gray-900 dark:text-white">
                        </div>
                        <div class="pt-4">
                            <button type="submit" name="save_account_details" class="w-full lg:w-fit px-8 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">
                                <?php echo __t('Save Changes'); ?>
                            </button>
                        </div>
                    </form>
                </div>

            <?php elseif ($view === 'password') : ?>
                <!-- Security View -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 lg:p-8 max-w-xl">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 border-b border-gray-100 dark:border-gray-700 pb-4"><?php echo __t('Change Password'); ?></h3>
                    <form id="warafy-change-password-form" class="space-y-6">
                        <?php wp_nonce_field('warafy_change_password', 'nonce'); ?>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 dark:text-gray-300"><?php echo __t('Current Password'); ?></label>
                            <input type="password" name="current_password" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm outline-none text-gray-900 dark:text-white" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 dark:text-gray-300"><?php echo __t('New Password'); ?></label>
                            <input type="password" name="new_password" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm outline-none text-gray-900 dark:text-white" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 dark:text-gray-300"><?php echo __t('Confirm New Password'); ?></label>
                            <input type="password" name="confirm_password" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm outline-none text-gray-900 dark:text-white" required>
                        </div>
                        <div id="password-message" class="hidden text-sm font-medium"></div>
                        <div class="pt-2">
                             <button type="submit" class="w-full lg:w-fit px-8 py-3 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                                <span class="spinner hidden w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                                <span><?php echo __t('Update Password'); ?></span>
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password Change Form AJAX
    const pwdForm = document.getElementById('warafy-change-password-form');
    if (pwdForm) {
        pwdForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = pwdForm.querySelector('button[type="submit"]');
            const spinner = btn.querySelector('.spinner');
            const messageDiv = document.getElementById('password-message');
            
            btn.disabled = true;
            spinner.classList.remove('hidden');
            messageDiv.classList.add('hidden');
            
            const formData = new FormData(pwdForm);
            formData.append('action', 'warafy_change_password');
            
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                messageDiv.classList.remove('hidden');
                messageDiv.textContent = data.data.message;
                if (data.success) {
                    messageDiv.className = 'text-green-600 dark:text-green-400 mt-2 font-bold';
                    pwdForm.reset();
                } else {
                    messageDiv.className = 'text-red-600 dark:text-red-400 mt-2 font-bold';
                }
            })
            .catch(err => {
                messageDiv.classList.remove('hidden');
                messageDiv.textContent = 'An error occurred. Please try again.';
                messageDiv.className = 'text-red-600 dark:text-red-400 mt-2 font-bold';
            })
            .finally(() => {
                btn.disabled = false;
                spinner.classList.add('hidden');
            });
        });
    }

    // Fetch Wishlist Items for Dashboard Card
    if (document.getElementById('dashboard-wishlist-count')) {
        const wishlist = JSON.parse(localStorage.getItem('warafy_wishlist') || '[]');
        document.getElementById('dashboard-wishlist-count').textContent = wishlist.length;
    }
});
</script>

<style>
/* Hide scrollbar for Chrome, Safari and Opera */
.scrollbar-none::-webkit-scrollbar {
    display: none;
}
/* Hide scrollbar for IE, Edge and Firefox */
.scrollbar-none {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
</style>

<?php get_footer(); ?>
