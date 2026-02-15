<?php
/*
Template Name: Register Page
*/

// Redirect logged-in users
if (is_user_logged_in()) {
    wp_redirect(home_url('/my-account'));
    exit;
}

$errors = array();
$success_message = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['warafy_register'])) {
    if (wp_verify_nonce($_POST['warafy_register_nonce'], 'warafy_register')) {
        $full_name = sanitize_text_field($_POST['full_name']);
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validate name
        if (empty($full_name)) {
            $errors[] = __t('Please enter your full name.');
        }
        
        // Validate at least one contact method
        if (empty($email) && empty($phone)) {
            $errors[] = __t('Please provide either an email address or phone number.');
        }
        
        // Validate email if provided
        if (!empty($email) && !is_email($email)) {
            $errors[] = __t('Please enter a valid email address.');
        }
        
        // Check if email already exists
        if (!empty($email) && email_exists($email)) {
            $errors[] = __t('An account with this email already exists.');
        }
        
        // Check if phone already exists
        if (!empty($phone)) {
            $existing_user_by_phone = warafy_get_user_by_phone($phone);
            if ($existing_user_by_phone) {
                $errors[] = __t('An account with this phone number already exists.');
            }
        }
        
        // Validate password
        if (empty($password) || strlen($password) < 6) {
            $errors[] = __t('Password must be at least 6 characters long.');
        }
        
        // Validate password confirmation
        if ($password !== $confirm_password) {
            $errors[] = __t('Passwords do not match.');
        }
        
        if (empty($errors)) {
            // Generate username from phone or email
            $username = !empty($email) ? $email : 'user_' . preg_replace('/[^0-9]/', '', $phone);
            
            // Create user
            $user_id = wp_create_user($username, $password, $email ?: $username . '@phone.local');
            
            if (!is_wp_error($user_id)) {
                // Parse full name
                $name_parts = explode(' ', $full_name, 2);
                $first_name = $name_parts[0];
                $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
                
                // Update user meta
                wp_update_user(array(
                    'ID' => $user_id,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'display_name' => $full_name
                ));
                
                // Store phone number
                if (!empty($phone)) {
                    update_user_meta($user_id, 'billing_phone', $phone);
                    update_user_meta($user_id, 'phone_number', $phone);
                }
                
                // Store registration method
                $registration_method = !empty($email) ? 'email' : 'phone';
                update_user_meta($user_id, 'registration_method', $registration_method);
                
                // Handle email verification (optional now)
                if (!empty($email)) {
                    // Generate verification token
                    $verification_token = wp_generate_password(32, false);
                    update_user_meta($user_id, 'email_verification_token', $verification_token);
                    update_user_meta($user_id, 'email_verified', false);
                    
                    // Send verification email
                    $verification_link = home_url("/verify-email?token={$verification_token}&user_id={$user_id}");
                    $subject = __t('Verify your email address');
                    $message = sprintf(__t("Hi %s,\n\nThank you for registering! Please click the link below to verify your email address:\n\n%s\n\nIf you didn't create an account, please ignore this email.\n\nBest regards,\nWarafy Team"), $first_name, $verification_link);
                    
                    wp_mail($email, $subject, $message);
                } else {
                    // Phone only - mark as verified
                    update_user_meta($user_id, 'email_verified', true);
                }
                
                // Auto-login all users after registration
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id, true);
                
                wp_redirect(home_url('/my-account'));
                exit;
            } else {
                $errors[] = __t('Registration failed. Please try again.');
            }
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
                <h1 class="text-xl font-bold text-gray-900 dark:text-white"><?php echo __t('Register'); ?></h1>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 dark:border-gray-700 dark:bg-background-dark">
            <!-- Logo Section -->
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.jpg" alt="Ador Kini" class="h-12 w-auto object-contain">
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo __t('Create Account'); ?></h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400"><?php echo __t('Join us today and start shopping'); ?></p>
            </div>

            <?php if (!empty($errors)) : ?>
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 rounded-lg">
                    <?php foreach ($errors as $error) : ?>
                        <p class="text-sm text-red-800 dark:text-red-200"><?php echo esc_html($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Registration Form -->
            <form method="post" id="register-form">
                <?php wp_nonce_field('warafy_register', 'warafy_register_nonce'); ?>
                
                <div class="space-y-4">
                    <!-- Full Name -->
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="full_name"><?php echo __t('Full Name'); ?> <span class="text-red-500">*</span></label>
                        <input 
                            type="text" 
                            id="full_name" 
                            name="full_name" 
                            required
                            class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm font-medium placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary" 
                            placeholder="<?php echo __t('Enter your full name'); ?>"
                            value="<?php echo isset($_POST['full_name']) ? esc_attr($_POST['full_name']) : ''; ?>"
                        >
                    </div>
                    
                    <!-- Email Address (Optional) -->
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="email">
                            <?php echo __t('Email Address'); ?>
                            <span class="text-gray-400 text-xs font-normal ml-1">(<?php echo __t('optional if phone provided'); ?>)</span>
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm font-medium placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary" 
                            placeholder="<?php echo __t('Enter your email'); ?>"
                            value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>"
                        >
                    </div>
                    
                    <!-- Phone Number (Optional) -->
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="phone">
                            <?php echo __t('Phone Number'); ?>
                            <span class="text-gray-400 text-xs font-normal ml-1">(<?php echo __t('optional if email provided'); ?>)</span>
                        </label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm font-medium placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary" 
                            placeholder="<?php echo __t('Enter your phone number'); ?>"
                            value="<?php echo isset($_POST['phone']) ? esc_attr($_POST['phone']) : ''; ?>"
                        >
                    </div>
                    
                    <!-- Contact Method Note -->
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <p class="text-xs text-blue-700 dark:text-blue-300">
                            <span class="material-symbols-outlined text-sm align-middle mr-1" data-icon="info"></span>
                            <?php echo __t('Provide at least one: email or phone number. Email verification is optional but recommended for security.'); ?>
                        </p>
                    </div>
                    
                    <!-- Password -->
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="password"><?php echo __t('Password'); ?> <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                minlength="6"
                                class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm font-medium placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary pr-10" 
                                placeholder="<?php echo __t('Create a password (min. 6 characters)'); ?>"
                            >
                            <button type="button" onclick="togglePassword('password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined text-lg" data-icon="visibility"></span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Confirm Password -->
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" for="confirm_password"><?php echo __t('Confirm Password'); ?> <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="confirm_password" 
                                name="confirm_password" 
                                required
                                minlength="6"
                                class="form-input mt-1 block w-full rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm font-medium placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary pr-10" 
                                placeholder="<?php echo __t('Confirm your password'); ?>"
                            >
                            <button type="button" onclick="togglePassword('confirm_password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined text-lg" data-icon="visibility"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <label class="flex items-start">
                        <input type="checkbox" name="terms" required class="rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-800 mt-1">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                            <?php echo sprintf(__t('I agree to the %s and %s'), '<a href="#" class="text-primary hover:text-primary/80">' . __t('Terms of Service') . '</a>', '<a href="#" class="text-primary hover:text-primary/80">' . __t('Privacy Policy') . '</a>'); ?>
                        </span>
                    </label>
                </div>

                <button type="submit" name="warafy_register" class="mt-6 flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-black text-white text-base font-bold hover:bg-gray-800 transition-colors">
                    <span><?php echo __t('Create Account'); ?></span>
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600 dark:text-gray-400">
                    <?php echo __t('Already have an account?'); ?> 
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

// Client-side validation for at least one contact method
document.getElementById('register-form').addEventListener('submit', function(e) {
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    
    if (!email && !phone) {
        e.preventDefault();
        alert('<?php echo __t('Please provide either an email address or phone number.'); ?>');
        return false;
    }
});
</script>

<?php get_footer(); ?>
