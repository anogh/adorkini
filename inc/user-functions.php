<?php
/**
 * User Functions
 * Email verification, user helpers
 */

if (!defined('ABSPATH')) {
    exit;
}

// Handle email verification
add_action('init', 'warafy_handle_email_verification');
function warafy_handle_email_verification() {
    if (isset($_GET['token']) && isset($_GET['user_id'])) {
        $token = sanitize_text_field($_GET['token']);
        $user_id = intval($_GET['user_id']);
        
        $stored_token = get_user_meta($user_id, 'email_verification_token', true);
        
        if ($token === $stored_token) {
            update_user_meta($user_id, 'email_verified', true);
            delete_user_meta($user_id, 'email_verification_token');
            
            wp_redirect(home_url('/login?verified=success'));
            exit;
        } else {
            wp_redirect(home_url('/login?verification=failed'));
            exit;
        }
    }
}

/**
 * Get user by phone number
 */
function warafy_get_user_by_phone($phone) {
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    
    $users = get_users(array(
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => 'billing_phone',
                'value' => $phone,
                'compare' => '='
            ),
            array(
                'key' => 'phone_number',
                'value' => $phone,
                'compare' => '='
            )
        ),
        'number' => 1
    ));
    
    if (!empty($users)) {
        return $users[0];
    }
    
    $phone_variants = array(
        $phone,
        '+' . ltrim($phone, '+'),
        ltrim($phone, '+'),
        ltrim($phone, '0'),
        '0' . ltrim($phone, '0')
    );
    
    foreach ($phone_variants as $variant) {
        $users = get_users(array(
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => 'billing_phone',
                    'value' => $variant,
                    'compare' => '='
                ),
                array(
                    'key' => 'phone_number',
                    'value' => $variant,
                    'compare' => '='
                )
            ),
            'number' => 1
        ));
        
        if (!empty($users)) {
            return $users[0];
        }
    }
    
    return false;
}

// Check if user email is verified
function warafy_is_user_email_verified($user_id = null) {
    if ($user_id === null) {
        $user_id = get_current_user_id();
    }
    return get_user_meta($user_id, 'email_verified', true) === '1';
}

// Email verification notice
function warafy_email_verification_notice() {
    if (is_user_logged_in() && !warafy_is_user_email_verified()) {
        ?>
        <style>
        .email-verification-notice {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            background: #fef3c7;
            border-bottom: 1px solid #fbbf24;
            padding: 12px;
            text-align: center;
        }
        .email-verification-notice.dark {
            background: #92400e;
            border-bottom-color: #d97706;
        }
        </style>
        <div class="email-verification-notice" id="email-verification-notice">
            <div class="container mx-auto px-4">
                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                    <strong>Please verify your email address.</strong> Check your inbox for the verification link.
                    <button onclick="resendVerificationEmail()" class="ml-2 text-yellow-800 underline dark:text-yellow-200">Resend email</button>
                </p>
                <button onclick="document.getElementById('email-verification-notice').style.display='none'" class="absolute top-2 right-2 text-yellow-800 dark:text-yellow-200">
                    <span class="material-symbols-outlined text-base" data-icon="close"></span>
                </button>
            </div>
        </div>
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
                    alert('Verification email sent! Please check your inbox.');
                } else {
                    alert('Error: ' + data.data);
                }
            });
        }
        </script>
        <?php
    }
}

// AJAX handler for resending verification email
add_action('wp_ajax_warafy_resend_verification_email', 'warafy_resend_verification_email');
add_action('wp_ajax_nopriv_warafy_resend_verification_email', 'warafy_resend_verification_email');

function warafy_resend_verification_email() {
    if (!wp_verify_nonce($_POST['nonce'], 'warafy_resend_verification')) {
        wp_send_json_error('Invalid nonce');
    }
    
    if (!is_user_logged_in()) {
        wp_send_json_error('User not logged in');
    }
    
    $user_id = get_current_user_id();
    $user = wp_get_current_user();
    
    $verification_token = wp_generate_password(32, false);
    update_user_meta($user_id, 'email_verification_token', $verification_token);
    
    $verification_link = home_url("/verify-email?token={$verification_token}&user_id={$user_id}");
    $subject = 'Verify your email address';
    $message = "Hi {$user->first_name},\n\n";
    $message .= "Please click the link below to verify your email address:\n\n";
    $message .= $verification_link . "\n\n";
    $message .= "If you didn't request this verification, please ignore this email.\n\n";
    $message .= "Best regards,\nWarafy Team";
    
    $sent = wp_mail($user->user_email, $subject, $message);
    
    if ($sent) {
        wp_send_json_success('Verification email sent successfully');
    } else {
        wp_send_json_error('Failed to send verification email');
    }
}
