<?php
/*
Template Name: Forgot Password Page
*/

// Redirect logged-in users
if (is_user_logged_in()) {
    wp_redirect(home_url('/my-account'));
    exit;
}

$message = '';
$message_type = '';

// Handle forgot password form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['warafy_forgot_password'])) {
    if (wp_verify_nonce($_POST['warafy_forgot_password_nonce'], 'warafy_forgot_password')) {
        $identifier = sanitize_text_field($_POST['identifier']);
        
        // Find user by email or phone
        $user = null;
        
        if (is_email($identifier)) {
            $user = get_user_by('email', $identifier);
        } else {
            // Try phone number
            $user = warafy_get_user_by_phone($identifier);
        }
        
        if ($user) {
            // Generate password reset token
            $reset_token = wp_generate_password(32, false);
            $reset_expiry = time() + (60 * 60); // 1 hour expiry
            
            update_user_meta($user->ID, 'password_reset_token', $reset_token);
            update_user_meta($user->ID, 'password_reset_expiry', $reset_expiry);
            
            // Check if user has email
            $user_email = $user->user_email;
            
            if ($user_email && strpos($user_email, '@phone.local') === false) {
                // Send reset email
                $reset_link = home_url("/reset-password?token={$reset_token}&user_id={$user->ID}");
                $subject = __t('Reset Your Password');
                $message_body = sprintf(
                    __t("Hi %s,\n\nYou requested to reset your password. Click the link below to set a new password:\n\n%s\n\nThis link will expire in 1 hour.\n\nIf you didn't request this, please ignore this email.\n\nBest regards,\nAdor Kini Team"),
                    $user->display_name,
                    $reset_link
                );
                
                wp_mail($user_email, $subject, $message_body);
                
                $message = __t('Password reset link has been sent to your email address. Please check your inbox.');
                $message_type = 'success';
            } else {
                // Phone-only user - show reset form directly or provide alternative
                $message = __t('Password reset is only available for accounts with email addresses. Please contact support for assistance.');
                $message_type = 'warning';
            }
        } else {
            // Don't reveal if user exists or not for security
            $message = __t('If an account exists with this email or phone number, you will receive a password reset link.');
            $message_type = 'success';
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
                <h1 class="text-xl font-bold text-gray-900 dark:text-white"><?php echo __t('Forgot Password'); ?></h1>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 dark:border-gray-700 dark:bg-background-dark">
            <!-- Header Section -->
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 rounded-full bg-primary/10 dark:bg-primary/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-3xl text-primary" data-icon="lock_reset"></span>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo __t('Forgot Password?'); ?></h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400"><?php echo __t("Don't worry! Enter your email or phone number and we'll send you a reset link."); ?></p>
            </div>

            <?php if (!empty($message)) : ?>
                <div class="mb-6 p-4 rounded-lg <?php echo $message_type === 'success' ? 'bg-green-50 dark:bg-green-900/30' : ($message_type === 'warning' ? 'bg-yellow-50 dark:bg-yellow-900/30' : 'bg-red-50 dark:bg-red-900/30'); ?>">
                    <p class="text-sm <?php echo $message_type === 'success' ? 'text-green-800 dark:text-green-200' : ($message_type === 'warning' ? 'text-yellow-800 dark:text-yellow-200' : 'text-red-800 dark:text-red-200'); ?>">
                        <?php if ($message_type === 'success') : ?>
                            <span class="material-symbols-outlined text-sm align-middle mr-1" data-icon="check_circle"></span>
                        <?php elseif ($message_type === 'warning') : ?>
                            <span class="material-symbols-outlined text-sm align-middle mr-1" data-icon="warning"></span>
                        <?php endif; ?>
                        <?php echo esc_html($message); ?>
                    </p>
                </div>
            <?php endif; ?>

            <!-- Forgot Password Form -->
            <form method="post">
                <?php wp_nonce_field('warafy_forgot_password', 'warafy_forgot_password_nonce'); ?>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="identifier"><?php echo __t('Email or Phone Number'); ?></label>
                        <input 
                            type="text" 
                            id="identifier" 
                            name="identifier" 
                            required
                            class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm font-medium placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary" 
                            placeholder="<?php echo __t('Enter your email or phone number'); ?>"
                            value="<?php echo isset($_POST['identifier']) ? esc_attr($_POST['identifier']) : ''; ?>"
                        >
                    </div>
                </div>

                <button type="submit" name="warafy_forgot_password" class="mt-6 flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-black text-white text-base font-bold hover:bg-gray-800 transition-colors">
                    <span class="material-symbols-outlined mr-2" data-icon="mail"></span>
                    <span><?php echo __t('Send Reset Link'); ?></span>
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600 dark:text-gray-400">
                    <?php echo __t('Remember your password?'); ?> 
                    <a href="<?php echo esc_url(home_url('/login')); ?>" class="font-medium text-primary hover:text-primary/80">
                        <?php echo __t('Sign in here'); ?>
                    </a>
                </p>
            </div>
            
            <div class="mt-4 text-center">
                <p class="text-gray-600 dark:text-gray-400">
                    <?php echo __t("Don't have an account?"); ?> 
                    <a href="<?php echo esc_url(home_url('/register')); ?>" class="font-medium text-primary hover:text-primary/80">
                        <?php echo __t('Register here'); ?>
                    </a>
                </p>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
