<?php
/*
Template Name: Login Page
*/

// Redirect logged-in users
if (is_user_logged_in()) {
    $redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : home_url('/my-account');
    wp_safe_redirect($redirect_to);
    exit;
}

$login_error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['warafy_login'])) {
    if (wp_verify_nonce($_POST['warafy_login_nonce'], 'warafy_login')) {
        $login_identifier = sanitize_text_field($_POST['login_identifier']);
        $password = $_POST['password'];
        $remember = isset($_POST['rememberme']);
        
        // Determine if login is email or phone
        $user = null;
        
        if (is_email($login_identifier)) {
            // Login with email
            $user = get_user_by('email', $login_identifier);
        } else {
            // Try username first
            $user = get_user_by('login', $login_identifier);
            
            // If not found, try phone number
            if (!$user) {
                $user = warafy_get_user_by_phone($login_identifier);
            }
        }
        
        if ($user) {
            // Verify password
            if (wp_check_password($password, $user->data->user_pass, $user->ID)) {
                // Login successful - allow all users regardless of email verification
                wp_set_current_user($user->ID);
                wp_set_auth_cookie($user->ID, $remember);
                
                // Redirect to account page or intended page
                $redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : home_url('/my-account');
                wp_safe_redirect($redirect_to);
                exit;
            } else {
                $login_error = __t('Invalid password. Please try again.');
            }
        } else {
            $login_error = __t('No account found with this email or phone number.');
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
                <h1 class="text-xl font-bold text-gray-900 dark:text-white"><?php echo __t('Login'); ?></h1>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 dark:border-gray-700 dark:bg-background-dark">
            <!-- Logo Section -->
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <img src="<?php echo esc_url( warafy_get_logo_url() ); ?>" alt="Ador Kini" class="warafy-logo-img">
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo __t('Welcome Back'); ?></h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400"><?php echo __t('Sign in to your account to continue'); ?></p>
            </div>

            <?php if (isset($_GET['registered']) && $_GET['registered'] == 'email') : ?>
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 rounded-lg">
                    <p class="text-sm text-green-800 dark:text-green-200">
                        <span class="material-symbols-outlined text-sm align-middle mr-1" data-icon="mark_email_read"></span>
                        <?php echo __t('Registration successful! Please check your email to verify your account before logging in.'); ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['registered']) && $_GET['registered'] == 'success') : ?>
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 rounded-lg">
                    <p class="text-sm text-green-800 dark:text-green-200"><?php echo __t('Registration successful! You can now log in.'); ?></p>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['verified']) && $_GET['verified'] == 'success') : ?>
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 rounded-lg">
                    <p class="text-sm text-green-800 dark:text-green-200">
                        <span class="material-symbols-outlined text-sm align-middle mr-1" data-icon="check_circle"></span>
                        <?php echo __t('Email verified successfully! You can now log in.'); ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['verification']) && $_GET['verification'] == 'failed') : ?>
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 rounded-lg">
                    <p class="text-sm text-red-800 dark:text-red-200"><?php echo __t('Email verification failed. Invalid or expired token.'); ?></p>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['password_reset']) && $_GET['password_reset'] == 'success') : ?>
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 rounded-lg">
                    <p class="text-sm text-green-800 dark:text-green-200">
                        <span class="material-symbols-outlined text-sm align-middle mr-1" data-icon="check_circle"></span>
                        <?php echo __t('Password reset successful! You can now log in with your new password.'); ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if (!empty($login_error)) : ?>
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 rounded-lg">
                    <p class="text-sm text-red-800 dark:text-red-200"><?php echo esc_html($login_error); ?></p>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="post">
                <?php wp_nonce_field('warafy_login', 'warafy_login_nonce'); ?>
                <input type="hidden" name="warafy_login" value="1">
                <?php if (isset($_REQUEST['redirect_to'])) : ?>
                    <input type="hidden" name="redirect_to" value="<?php echo esc_attr($_REQUEST['redirect_to']); ?>">
                <?php endif; ?>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="login_identifier"><?php echo __t('Email or Phone Number'); ?></label>
                        <input 
                            type="text" 
                            id="login_identifier" 
                            name="login_identifier" 
                            required
                            class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm font-medium placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary" 
                            placeholder="<?php echo __t('Enter your email or phone number'); ?>"
                            value="<?php echo isset($_POST['login_identifier']) ? esc_attr($_POST['login_identifier']) : ''; ?>"
                        >
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="password"><?php echo __t('Password'); ?></label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm font-medium placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary pr-10" 
                                placeholder="<?php echo __t('Enter your password'); ?>"
                            >
                            <button type="button" onclick="togglePassword('password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined text-lg" data-icon="visibility"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="rememberme" id="rememberme" class="rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-800">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400"><?php echo __t('Remember me'); ?></span>
                    </label>
                    <a href="<?php echo esc_url(home_url('/forgot-password')); ?>" class="text-sm text-primary hover:text-primary/80">
                        <?php echo __t('Forgot password?'); ?>
                    </a>
                </div>

                <button type="submit" class="mt-6 flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-black text-white text-base font-bold hover:bg-gray-800 transition-colors">
                    <span><?php echo __t('Sign In'); ?></span>
                </button>
            </form>

            <div class="mt-6 text-center">
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
