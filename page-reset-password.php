<?php
/*
Template Name: Reset Password Page
*/

// Redirect logged-in users
if (is_user_logged_in()) {
    wp_redirect(home_url('/my-account'));
    exit;
}

$error = '';
$success = false;
$valid_token = false;
$user_id = 0;

// Validate token
if (isset($_GET['token']) && isset($_GET['user_id'])) {
    $token = sanitize_text_field($_GET['token']);
    $user_id = intval($_GET['user_id']);
    
    $stored_token = get_user_meta($user_id, 'password_reset_token', true);
    $token_expiry = get_user_meta($user_id, 'password_reset_expiry', true);
    
    if ($token === $stored_token && $token_expiry > time()) {
        $valid_token = true;
    } else {
        $error = __t('Invalid or expired reset link. Please request a new one.');
    }
}

// Handle password reset form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['warafy_reset_password'])) {
    if (wp_verify_nonce($_POST['warafy_reset_password_nonce'], 'warafy_reset_password')) {
        $token = sanitize_text_field($_POST['token']);
        $user_id = intval($_POST['user_id']);
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Verify token again
        $stored_token = get_user_meta($user_id, 'password_reset_token', true);
        $token_expiry = get_user_meta($user_id, 'password_reset_expiry', true);
        
        if ($token !== $stored_token || $token_expiry < time()) {
            $error = __t('Invalid or expired reset link. Please request a new one.');
            $valid_token = false;
        } elseif (strlen($new_password) < 6) {
            $error = __t('Password must be at least 6 characters long.');
            $valid_token = true;
        } elseif ($new_password !== $confirm_password) {
            $error = __t('Passwords do not match.');
            $valid_token = true;
        } else {
            // Reset password
            wp_set_password($new_password, $user_id);
            
            // Clear reset tokens
            delete_user_meta($user_id, 'password_reset_token');
            delete_user_meta($user_id, 'password_reset_expiry');
            
            // Redirect to login with success message
            wp_redirect(home_url('/login?password_reset=success'));
            exit;
        }
    }
}

get_header(); ?>

<main class="flex-grow">
    <div class="container mx-auto px-4 py-8 max-w-md">
        <!-- Mobile Header -->
        <div class="lg:hidden mb-6">
            <div class="flex items-center gap-4">
                <button onclick="history.back()" class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full hover:bg-gray-200/50 dark:hover:bg-gray-700/50">
                    <span class="material-symbols-outlined text-gray-600 dark:text-gray-300" data-icon="arrow_back"></span>
                </button>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white"><?php echo __t('Reset Password'); ?></h1>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 dark:border-gray-700 dark:bg-background-dark">
            <!-- Header Section -->
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 rounded-full bg-primary/10 dark:bg-primary/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-3xl text-primary" data-icon="password"></span>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo __t('Reset Password'); ?></h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400"><?php echo __t('Create a new password for your account.'); ?></p>
            </div>

            <?php if (!empty($error)) : ?>
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 rounded-lg">
                    <p class="text-sm text-red-800 dark:text-red-200">
                        <span class="material-symbols-outlined text-sm align-middle mr-1" data-icon="error"></span>
                        <?php echo esc_html($error); ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if ($valid_token) : ?>
                <!-- Reset Password Form -->
                <form method="post">
                    <?php wp_nonce_field('warafy_reset_password', 'warafy_reset_password_nonce'); ?>
                    <input type="hidden" name="token" value="<?php echo esc_attr($_GET['token']); ?>">
                    <input type="hidden" name="user_id" value="<?php echo esc_attr($_GET['user_id']); ?>">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="new_password"><?php echo __t('New Password'); ?></label>
                            <div class="relative">
                                <input 
                                    type="password" 
                                    id="new_password" 
                                    name="new_password" 
                                    required
                                    minlength="6"
                                    class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm font-medium placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary pr-10" 
                                    placeholder="<?php echo __t('Enter new password (min. 6 characters)'); ?>"
                                >
                                <button type="button" onclick="togglePassword('new_password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <span class="material-symbols-outlined text-lg" data-icon="visibility"></span>
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="confirm_password"><?php echo __t('Confirm New Password'); ?></label>
                            <div class="relative">
                                <input 
                                    type="password" 
                                    id="confirm_password" 
                                    name="confirm_password" 
                                    required
                                    minlength="6"
                                    class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm font-medium placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary pr-10" 
                                    placeholder="<?php echo __t('Confirm new password'); ?>"
                                >
                                <button type="button" onclick="togglePassword('confirm_password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <span class="material-symbols-outlined text-lg" data-icon="visibility"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="warafy_reset_password" class="mt-6 flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-base font-bold hover:bg-primary/90 transition-colors">
                        <span class="material-symbols-outlined mr-2" data-icon="lock"></span>
                        <span><?php echo __t('Reset Password'); ?></span>
                    </button>
                </form>
            <?php else : ?>
                <!-- Invalid/Expired Token -->
                <div class="text-center">
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <span class="material-symbols-outlined text-4xl text-gray-400 mb-2" data-icon="link_off"></span>
                        <p class="text-gray-600 dark:text-gray-400"><?php echo __t('This password reset link is invalid or has expired.'); ?></p>
                    </div>
                    
                    <a href="<?php echo esc_url(home_url('/forgot-password')); ?>" class="inline-flex items-center justify-center rounded-lg bg-primary px-6 py-3 text-base font-medium text-white hover:bg-primary/90 transition-colors">
                        <span class="material-symbols-outlined mr-2" data-icon="refresh"></span>
                        <?php echo __t('Request New Link'); ?>
                    </a>
                </div>
            <?php endif; ?>

            <div class="mt-6 text-center">
                <p class="text-gray-600 dark:text-gray-400">
                    <?php echo __t('Remember your password?'); ?> 
                    <a href="<?php echo esc_url(home_url('/login')); ?>" class="font-medium text-primary hover:text-primary/80">
                        <?php echo __t('Sign in here'); ?>
                    </a>
                </p>
            </div>
        </div>
    </div>
</main>

<script>
function togglePassword(fieldId, button) {
    const field = document.getElementById(fieldId);
    const icon = button.querySelector('.material-symbols-outlined');
    if (field.type === 'password') {
        field.type = 'text';
        icon.setAttribute('data-icon', 'visibility_off');
    } else {
        field.type = 'password';
        icon.setAttribute('data-icon', 'visibility');
    }
}
</script>

<?php get_footer(); ?>
