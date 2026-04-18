<?php
/*
Template Name: Shipping Addresses
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
            <h1 class="text-lg font-bold text-gray-900 dark:text-white"><?php echo __t('Shipping Addresses'); ?></h1>
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
            <span class="text-gray-900 dark:text-white"><?php echo __t('Shipping Addresses'); ?></span>
        </nav>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2"><?php echo __t('Shipping Addresses'); ?></h1>
            <p class="text-gray-600 dark:text-gray-400"><?php echo __t('Manage your shipping addresses for faster checkout'); ?></p>
        </div>

        <div class="max-w-4xl">
            <!-- Add New Address Button -->
            <div class="mb-6">
                <button onclick="showAddAddressForm()" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                    <span class="material-symbols-outlined mr-2" data-icon="add"></span>
                    <?php echo __t('Add New Address'); ?>
                </button>
            </div>

            <!-- Add/Edit Address Form (Hidden by default) -->
            <div id="addressForm" class="hidden mb-8 bg-white rounded-xl border border-gray-200 p-6 dark:border-gray-700 dark:bg-background-dark">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" id="formTitle"><?php echo __t('Add New Address'); ?></h2>
                <form class="space-y-4" method="post">
                    <?php wp_nonce_field('save_address', 'save_address_nonce'); ?>
                    <input type="hidden" id="address_id" name="address_id" value="">
                    
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="first_name"><?php echo __t('First Name'); ?> *</label>
                            <input type="text" id="first_name" name="first_name" required class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="last_name"><?php echo __t('Last Name'); ?> *</label>
                            <input type="text" id="last_name" name="last_name" required class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary">
                        </div>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="company"><?php echo __t('Company'); ?></label>
                        <input type="text" id="company" name="company" class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary">
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="address_1"><?php echo __t('Address Line 1'); ?> *</label>
                        <input type="text" id="address_1" name="address_1" required class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary">
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="address_2"><?php echo __t('Address Line 2'); ?></label>
                        <input type="text" id="address_2" name="address_2" class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary">
                    </div>
                    
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="city"><?php echo __t('City'); ?> *</label>
                            <input type="text" id="city" name="city" required class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="state"><?php echo __t('State/Province'); ?> *</label>
                            <input type="text" id="state" name="state" required class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="postcode"><?php echo __t('Postal Code'); ?> *</label>
                            <input type="text" id="postcode" name="postcode" required class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary">
                        </div>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="country"><?php echo __t('Country'); ?> *</label>
                        <select id="country" name="country" required class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary">
                            <?php
                            $countries = WC()->countries->get_countries();
                            foreach ($countries as $code => $name) {
                                echo '<option value="' . esc_attr($code) . '">' . esc_html($name) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="phone"><?php echo __t('Phone'); ?></label>
                        <input type="tel" id="phone" name="phone" class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary">
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="default_address" name="default_address" class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                        <label for="default_address" class="ml-2 text-sm text-gray-700 dark:text-gray-300"><?php echo __t('Set as default shipping address'); ?></label>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="hideAddressForm()" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                            <?php echo __t('Cancel'); ?>
                        </button>
                        <button type="submit" name="save_address" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                            <?php echo __t('Save Address'); ?>
                        </button>
                    </div>
                </form>
                
                <?php
                // Handle address save
                if (isset($_POST['save_address']) && wp_verify_nonce($_POST['save_address_nonce'], 'save_address')) {
                    $user_id = get_current_user_id();
                    $address_id = intval($_POST['address_id']);
                    $is_default = isset($_POST['default_address']);
                    
                    $address_data = array(
                        'first_name' => sanitize_text_field($_POST['first_name']),
                        'last_name' => sanitize_text_field($_POST['last_name']),
                        'company' => sanitize_text_field($_POST['company']),
                        'address_1' => sanitize_text_field($_POST['address_1']),
                        'address_2' => sanitize_text_field($_POST['address_2']),
                        'city' => sanitize_text_field($_POST['city']),
                        'state' => sanitize_text_field($_POST['state']),
                        'postcode' => sanitize_text_field($_POST['postcode']),
                        'country' => sanitize_text_field($_POST['country']),
                        'phone' => sanitize_text_field($_POST['phone']),
                    );
                    
                    // Save as WooCommerce customer address
                    if ($address_id) {
                        // Update existing address
                        update_user_meta($user_id, 'shipping_address_' . $address_id, $address_data);
                    } else {
                        // Add new address
                        $addresses = get_user_meta($user_id, 'shipping_addresses', true) ?: array();
                        $address_id = count($addresses) + 1;
                        $addresses[$address_id] = $address_data;
                        update_user_meta($user_id, 'shipping_addresses', $addresses);
                        update_user_meta($user_id, 'shipping_address_' . $address_id, $address_data);
                    }
                    
                    // Set as default if checked
                    if ($is_default) {
                        update_user_meta($user_id, 'default_shipping_address', $address_id);
                    }
                    
                    echo '<div class="mt-4 p-4 bg-green-50 dark:bg-green-900/30 rounded-lg">';
                    echo '<p class="text-sm text-green-800 dark:text-green-200">' . __t('Address saved successfully!') . '</p>';
                    echo '</div>';
                    
                    // Hide form after successful save
                    echo '<script>hideAddressForm();</script>';
                }
                ?>
            </div>

            <!-- Existing Addresses -->
            <div class="space-y-4">
                <?php
                $user_id = get_current_user_id();
                $addresses = get_user_meta($user_id, 'shipping_addresses', true) ?: array();
                $default_address_id = get_user_meta($user_id, 'default_shipping_address', true);
                
                if (empty($addresses)) {
                    // Try to get WooCommerce default billing address
                    $customer = new WC_Customer($user_id);
                    if ($customer->get_billing_address()) {
                        $addresses[1] = array(
                            'first_name' => $customer->get_billing_first_name(),
                            'last_name' => $customer->get_billing_last_name(),
                            'company' => $customer->get_billing_company(),
                            'address_1' => $customer->get_billing_address_1(),
                            'address_2' => $customer->get_billing_address_2(),
                            'city' => $customer->get_billing_city(),
                            'state' => $customer->get_billing_state(),
                            'postcode' => $customer->get_billing_postcode(),
                            'country' => $customer->get_billing_country(),
                            'phone' => $customer->get_billing_phone(),
                        );
                        if (!$default_address_id) {
                            $default_address_id = 1;
                            update_user_meta($user_id, 'default_shipping_address', 1);
                        }
                    }
                }
                
                if (!empty($addresses)) :
                    foreach ($addresses as $address_id => $address) :
                        $is_default = ($address_id == $default_address_id);
                ?>
                        <div class="bg-white rounded-xl border border-gray-200 p-6 dark:border-gray-700 dark:bg-background-dark <?php echo $is_default ? 'ring-2 ring-primary' : ''; ?>">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-gray-400" data-icon="home"></span>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white">
                                            <?php echo esc_html($address['first_name'] . ' ' . $address['last_name']); ?>
                                            <?php if ($is_default) : ?>
                                                <span class="ml-2 inline-flex items-center rounded-full bg-primary/10 px-2 py-1 text-xs font-medium text-primary">
                                                    <?php echo __t('Default'); ?>
                                                </span>
                                            <?php endif; ?>
                                        </h3>
                                        <?php if (!empty($address['company'])) : ?>
                                            <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo esc_html($address['company']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <?php if (!$is_default) : ?>
                                        <button onclick="setDefaultAddress(<?php echo $address_id; ?>)" class="text-sm text-primary hover:text-primary/80">
                                            <?php echo __t('Set as default'); ?>
                                        </button>
                                    <?php endif; ?>
                                    <button onclick="editAddress(<?php echo $address_id; ?>)" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                                        <?php echo __t('Edit'); ?>
                                    </button>
                                    <button onclick="deleteAddress(<?php echo $address_id; ?>)" class="text-sm text-red-600 hover:text-red-700">
                                        <?php echo __t('Delete'); ?>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                <p><?php echo esc_html($address['address_1']); ?></p>
                                <?php if (!empty($address['address_2'])) : ?>
                                    <p><?php echo esc_html($address['address_2']); ?></p>
                                <?php endif; ?>
                                <p>
                                    <?php 
                                    echo esc_html($address['city']);
                                    if (!empty($address['state'])) echo ', ' . esc_html($address['state']);
                                    if (!empty($address['postcode'])) echo ' ' . esc_html($address['postcode']);
                                    ?>
                                </p>
                                <p><?php echo WC()->countries->countries[$address['country']] ?? $address['country']; ?></p>
                                <?php if (!empty($address['phone'])) : ?>
                                    <p><?php echo __t('Phone'); ?>: <?php echo esc_html($address['phone']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="text-center py-12 bg-white rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-background-dark">
                        <span class="material-symbols-outlined text-5xl text-gray-300 mb-4" data-icon="home"></span>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2"><?php echo __t('No shipping addresses'); ?></h3>
                        <p class="text-gray-500 dark:text-gray-400"><?php echo __t('Add your first shipping address to make checkout faster.'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
function showAddAddressForm() {
    document.getElementById('addressForm').classList.remove('hidden');
    document.getElementById('formTitle').textContent = '<?php echo __t('Add New Address'); ?>';
    document.getElementById('address_id').value = '';
    document.querySelector('#addressForm form').reset();
}

function hideAddressForm() {
    document.getElementById('addressForm').classList.add('hidden');
}

function editAddress(addressId) {
    document.getElementById('addressForm').classList.remove('hidden');
    document.getElementById('formTitle').textContent = '<?php echo __t('Edit Address'); ?>';
    document.getElementById('address_id').value = addressId;
    
    // Load address data and populate form
    // This would need AJAX call to load address data
    showAddAddressForm(); // For now, just show the form
}

function deleteAddress(addressId) {
    if (confirm('<?php echo __t('Are you sure you want to delete this address?'); ?>')) {
        // Send delete request via AJAX
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=delete_address&address_id=' + addressId + '&nonce=<?php echo wp_create_nonce('delete_address_nonce'); ?>'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.data);
            }
        });
    }
}

function setDefaultAddress(addressId) {
    // Send set default request via AJAX
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=set_default_address&address_id=' + addressId + '&nonce=<?php echo wp_create_nonce('set_default_address_nonce'); ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.data);
        }
    });
}
</script>


<?php get_footer(); ?>
