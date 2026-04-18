<?php
/*
Template Name: Personal Information
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
            <h1 class="text-lg font-bold text-gray-900 dark:text-white"><?php echo __t('Personal Information'); ?></h1>
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
            <span class="text-gray-900 dark:text-white"><?php echo __t('Personal Information'); ?></span>
        </nav>

        <div class="max-w-2xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2"><?php echo __t('Personal Information'); ?></h1>
                <p class="text-gray-600 dark:text-gray-400"><?php echo __t('Update your personal details and contact information'); ?></p>
            </div>

            <!-- Email Verification Warning -->
            <?php
            $current_user = wp_get_current_user();
            $registration_method = get_user_meta($current_user->ID, 'registration_method', true);
            $email_verified = get_user_meta($current_user->ID, 'email_verified', true);
            $show_email_warning = ($registration_method === 'email' && !$email_verified && strpos($current_user->user_email, '@phone.local') === false);
            
            if ($show_email_warning) :
                $verification_token = get_user_meta($current_user->ID, 'email_verification_token', true);
                $verification_link = $verification_token ? home_url("/verify-email?token={$verification_token}&user_id={$current_user->ID}") : '';
            ?>
                <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg border border-yellow-200 dark:border-yellow-800">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-yellow-600 dark:text-yellow-400 mt-0.5" data-icon="warning"></span>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-yellow-800 dark:text-yellow-200 mb-1"><?php echo __t('Email Verification Required'); ?></h3>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-3">
                                <?php echo __t('Please verify your email address to ensure account security and receive important notifications.'); ?>
                            </p>
                            <?php if ($verification_link) : ?>
                                <div class="flex flex-wrap gap-2">
                                    <a href="<?php echo esc_url($verification_link); ?>" class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-yellow-600 text-white rounded hover:bg-yellow-700 transition-colors">
                                        <span class="material-symbols-outlined text-sm mr-1" data-icon="mark_email_read"></span>
                                        <?php echo __t('Verify Email'); ?>
                                    </a>
                                    <button onclick="resendVerificationEmail()" class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-white dark:bg-gray-800 text-yellow-600 dark:text-yellow-400 border border-yellow-300 dark:border-yellow-600 rounded hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors">
                                        <span class="material-symbols-outlined text-sm mr-1" data-icon="refresh"></span>
                                        <?php echo __t('Resend Email'); ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Personal Information Form -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 dark:border-gray-700 dark:bg-background-dark">
                <form class="space-y-6" method="post">
                    <?php wp_nonce_field('update_account_details', 'update_account_nonce'); ?>
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="firstName"><?php echo __t('First Name'); ?></label>
                            <input 
                                class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm font-medium placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary" 
                                id="firstName" 
                                type="text" 
                                name="first_name" 
                                value="<?php echo esc_attr($current_user->first_name); ?>"
                                required
                            />
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="lastName"><?php echo __t('Last Name'); ?></label>
                            <input 
                                class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm font-medium placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary" 
                                id="lastName" 
                                type="text" 
                                name="last_name" 
                                value="<?php echo esc_attr($current_user->last_name); ?>"
                                required
                            />
                        </div>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="email"><?php echo __t('Email Address'); ?></label>
                        <input 
                            class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm font-medium placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary" 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="<?php echo esc_attr($current_user->user_email); ?>"
                            required
                        />
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="phone"><?php echo __t('Phone Number'); ?></label>
                        <input 
                            class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm font-medium placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary" 
                            id="phone" 
                            type="tel" 
                            name="phone" 
                            value="<?php echo esc_attr(get_user_meta($current_user->ID, 'billing_phone', true)); ?>"
                            placeholder="<?php echo __t('Enter your phone number'); ?>"
                        />
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="display_name"><?php echo __t('Display Name'); ?></label>
                        <input 
                            class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm font-medium placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary" 
                            id="display_name" 
                            type="text" 
                            name="display_name" 
                            value="<?php echo esc_attr($current_user->display_name); ?>"
                            placeholder="<?php echo __t('How your name appears on the site'); ?>"
                        />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400"><?php echo __t('This is how your name will appear in the account section and in reviews.'); ?></p>
                    </div>
                    
                    <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="<?php echo home_url('/my-account'); ?>" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                            <?php echo __t('Cancel'); ?>
                        </a>
                        <button type="submit" name="save_account_details" class="flex min-w-[120px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold shadow-sm hover:bg-primary/90">
                            <span><?php echo __t('Save Changes'); ?></span>
                        </button>
                    </div>
                </form>
                
                <script>
                function resendVerificationEmail() {
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=warafy_resend_verification_email&nonce=<?php echo wp_create_nonce('warafy_resend_verification'); ?>'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('<?php echo __t('Verification email sent! Please check your inbox.'); ?>');
                        } else {
                            alert('<?php echo __t('Error:'); ?> ' + data.data);
                        }
                    });
                }
                </script>
                
                <?php
                // Handle form submission
                if (isset($_POST['save_account_details']) && wp_verify_nonce($_POST['update_account_nonce'], 'update_account_details')) {
                    $user_id = get_current_user_id();
                    $first_name = sanitize_text_field($_POST['first_name']);
                    $last_name = sanitize_text_field($_POST['last_name']);
                    $email = sanitize_email($_POST['email']);
                    $phone = sanitize_text_field($_POST['phone']);
                    $display_name = sanitize_text_field($_POST['display_name']);
                    
                    // Validate email
                    if (!is_email($email)) {
                        echo '<div class="mt-4 p-4 bg-red-50 dark:bg-red-900/30 rounded-lg">';
                        echo '<p class="text-sm text-red-800 dark:text-red-200">' . __t('Please enter a valid email address.') . '</p>';
                        echo '</div>';
                    } else {
                        // Check if email is already taken by another user
                        if (email_exists($email) && $email !== $current_user->user_email) {
                            echo '<div class="mt-4 p-4 bg-red-50 dark:bg-red-900/30 rounded-lg">';
                            echo '<p class="text-sm text-red-800 dark:text-red-200">' . __t('This email address is already in use by another account.') . '</p>';
                            echo '</div>';
                        } else {
                            // Update user data
                            wp_update_user(array(
                                'ID' => $user_id,
                                'first_name' => $first_name,
                                'last_name' => $last_name,
                                'user_email' => $email,
                                'display_name' => $display_name ?: ($first_name . ' ' . $last_name)
                            ));
                            
                            // Update phone
                            update_user_meta($user_id, 'billing_phone', $phone);
                            update_user_meta($user_id, 'phone_number', $phone);
                            
                            // If email changed, mark as unverified and send new verification
                            if ($email !== $current_user->user_email) {
                                $verification_token = wp_generate_password(32, false);
                                update_user_meta($user_id, 'email_verification_token', $verification_token);
                                update_user_meta($user_id, 'email_verified', false);
                                
                                $verification_link = home_url("/verify-email?token={$verification_token}&user_id={$user_id}");
                                $subject = __t('Verify your new email address');
                                $message = sprintf(__t("Hi %s,\n\nYou've changed your email address. Please click the link below to verify your new email:\n\n%s\n\nIf you didn't make this change, please contact support immediately.\n\nBest regards,\nWarafy Team"), $first_name, $verification_link);
                                
                                wp_mail($email, $subject, $message);
                                
                                echo '<div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg">';
                                echo '<p class="text-sm text-yellow-800 dark:text-yellow-200">' . __t('Email updated! Please check your new email address for a verification link.') . '</p>';
                                echo '</div>';
                            } else {
                                echo '<div class="mt-4 p-4 bg-green-50 dark:bg-green-900/30 rounded-lg">';
                                echo '<p class="text-sm text-green-800 dark:text-green-200">' . __t('Account details updated successfully!') . '</p>';
                                echo '</div>';
                            }
                        }
                    }
                }
                ?>
            </div>

            <!-- Password Change Section -->
            <div class="mt-8 bg-white rounded-xl border border-gray-200 p-6 dark:border-gray-700 dark:bg-background-dark">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"><?php echo __t('Change Password'); ?></h2>
                <form class="space-y-4" method="post">
                    <?php wp_nonce_field('change_password', 'change_password_nonce'); ?>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="current_password"><?php echo __t('Current Password'); ?></label>
                        <div class="relative">
                            <input 
                                class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm font-medium placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary pr-10" 
                                id="current_password" 
                                type="password" 
                                name="current_password" 
                                placeholder="<?php echo __t('Enter current password'); ?>"
                            />
                            <button type="button" onclick="togglePassword('current_password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined text-sm" data-icon="visibility_off"></span>
                            </button>
                        </div>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="new_password"><?php echo __t('New Password'); ?></label>
                        <div class="relative">
                            <input 
                                class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm font-medium placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary pr-10" 
                                id="new_password" 
                                type="password" 
                                name="new_password" 
                                placeholder="<?php echo __t('Enter new password'); ?>"
                                minlength="8"
                            />
                            <button type="button" onclick="togglePassword('new_password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined text-sm" data-icon="visibility_off"></span>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400"><?php echo __t('Password must be at least 8 characters long.'); ?></p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="confirm_password"><?php echo __t('Confirm New Password'); ?></label>
                        <div class="relative">
                            <input 
                                class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm font-medium placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary pr-10" 
                                id="confirm_password" 
                                type="password" 
                                name="confirm_password" 
                                placeholder="<?php echo __t('Confirm new password'); ?>"
                            />
                            <button type="button" onclick="togglePassword('confirm_password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined text-sm" data-icon="visibility_off"></span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" name="change_password" class="flex min-w-[120px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold shadow-sm hover:bg-primary/90">
                            <span><?php echo __t('Change Password'); ?></span>
                        </button>
                    </div>
                </form>
                
                <?php
                // Handle password change
                if (isset($_POST['change_password']) && wp_verify_nonce($_POST['change_password_nonce'], 'change_password')) {
                    $current_password = $_POST['current_password'];
                    $new_password = $_POST['new_password'];
                    $confirm_password = $_POST['confirm_password'];
                    
                    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                        echo '<div class="mt-4 p-4 bg-red-50 dark:bg-red-900/30 rounded-lg">';
                        echo '<p class="text-sm text-red-800 dark:text-red-200">' . __t('All password fields are required.') . '</p>';
                        echo '</div>';
                    } elseif (!wp_check_password($current_password, $current_user->user_pass, $current_user->ID)) {
                        echo '<div class="mt-4 p-4 bg-red-50 dark:bg-red-900/30 rounded-lg">';
                        echo '<p class="text-sm text-red-800 dark:text-red-200">' . __t('Current password is incorrect.') . '</p>';
                        echo '</div>';
                    } elseif ($new_password !== $confirm_password) {
                        echo '<div class="mt-4 p-4 bg-red-50 dark:bg-red-900/30 rounded-lg">';
                        echo '<p class="text-sm text-red-800 dark:text-red-200">' . __t('New passwords do not match.') . '</p>';
                        echo '</div>';
                    } elseif (strlen($new_password) < 8) {
                        echo '<div class="mt-4 p-4 bg-red-50 dark:bg-red-900/30 rounded-lg">';
                        echo '<p class="text-sm text-red-800 dark:text-red-200">' . __t('Password must be at least 8 characters long.') . '</p>';
                        echo '</div>';
                    } else {
                        wp_set_password($new_password, $current_user->ID);
                        
                        // Log user in again with new password
                        wp_set_current_user($current_user->ID);
                        wp_set_auth_cookie($current_user->ID, true);
                        
                        echo '<div class="mt-4 p-4 bg-green-50 dark:bg-green-900/30 rounded-lg">';
                        echo '<p class="text-sm text-green-800 dark:text-green-200">' . __t('Password changed successfully!') . '</p>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</main>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    const icon = button.querySelector('span');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.setAttribute('data-icon', 'visibility');
    } else {
        field.type = 'password';
        icon.setAttribute('data-icon', 'visibility_off');
    }
}
</script>

<?php get_footer(); ?>
