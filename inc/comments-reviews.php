<?php
/**
 * Comments and Reviews System
 * Custom tables, AJAX handlers
 */

if (!defined('ABSPATH')) {
    exit;
}

// Create custom tables for comments and reviews
add_action('init', 'warafy_create_comment_review_tables');
function warafy_create_comment_review_tables() {
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $comments_table = $wpdb->prefix . 'warafy_product_comments';
    $sql_comments = "CREATE TABLE IF NOT EXISTS $comments_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        product_id bigint(20) NOT NULL,
        user_id bigint(20) NULL,
        user_name varchar(100) NULL,
        user_email varchar(100) NULL,
        comment_text text NOT NULL,
        comment_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        status varchar(20) DEFAULT 'approved' NOT NULL,
        ip_address varchar(45) NULL,
        PRIMARY KEY  (id),
        KEY product_id (product_id),
        KEY user_id (user_id),
        KEY status (status)
    ) $charset_collate;";
    
    $reviews_table = $wpdb->prefix . 'warafy_product_reviews';
    $sql_reviews = "CREATE TABLE IF NOT EXISTS $reviews_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        product_id bigint(20) NOT NULL,
        user_id bigint(20) NOT NULL,
        rating int(1) NOT NULL,
        review_text text NOT NULL,
        review_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        status varchar(20) DEFAULT 'approved' NOT NULL,
        helpful_count int DEFAULT 0,
        PRIMARY KEY  (id),
        KEY product_id (product_id),
        KEY user_id (user_id),
        KEY status (status)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_comments);
    dbDelta($sql_reviews);
}

// Check if user has purchased the product
function warafy_user_purchased_product($user_id, $product_id) {
    if (!$user_id) return false;
    
    $orders = wc_get_orders([
        'customer_id' => $user_id,
        'status' => ['completed', 'processing'],
        'limit' => -1,
    ]);
    
    foreach ($orders as $order) {
        foreach ($order->get_items() as $item) {
            if ($item->get_product_id() == $product_id || $item->get_variation_id() == $product_id) {
                return true;
            }
        }
    }
    
    return false;
}

// AJAX handler for submitting comments
add_action('wp_ajax_warafy_submit_comment', 'warafy_submit_comment');
add_action('wp_ajax_nopriv_warafy_submit_comment', 'warafy_submit_comment');
function warafy_submit_comment() {
    global $wpdb;
    
    $nonce = sanitize_text_field($_POST['nonce']);
    if (!wp_verify_nonce($nonce, 'warafy_comment_nonce')) {
        wp_send_json_error(['message' => 'Security check failed.']);
        wp_die();
    }
    
    $product_id = intval($_POST['product_id']);
    $comment_text = sanitize_textarea_field($_POST['comment_text']);
    
    if (empty($product_id) || empty($comment_text)) {
        wp_send_json_error(['message' => 'Please fill in all required fields.']);
        wp_die();
    }
    
    if (strlen($comment_text) > 1000) {
        wp_send_json_error(['message' => 'Comment is too long. Maximum 1000 characters allowed.']);
        wp_die();
    }
    
    $user_id = get_current_user_id();
    $user_name = '';
    $user_email = '';
    
    if (!$user_id) {
        $user_name = sanitize_text_field($_POST['user_name']);
        $user_email = sanitize_email($_POST['user_email']);
        
        if (empty($user_name) || empty($user_email)) {
            wp_send_json_error(['message' => 'Please provide your name and email.']);
            wp_die();
        }
        
        if (!is_email($user_email)) {
            wp_send_json_error(['message' => 'Please provide a valid email address.']);
            wp_die();
        }
    } else {
        $user = get_userdata($user_id);
        $user_name = $user->display_name;
        $user_email = $user->user_email;
    }
    
    $table = $wpdb->prefix . 'warafy_product_comments';
    $result = $wpdb->insert(
        $table,
        [
            'product_id' => $product_id,
            'user_id' => $user_id,
            'user_name' => $user_name,
            'user_email' => $user_email,
            'comment_text' => $comment_text,
            'comment_date' => current_time('mysql'),
            'ip_address' => $_SERVER['REMOTE_ADDR']
        ],
        ['%d', '%d', '%s', '%s', '%s', '%s', '%s']
    );
    
    if ($result === false) {
        wp_send_json_error(['message' => 'Database error. Please try again.']);
        wp_die();
    }
    
    wp_send_json_success(['message' => 'Comment posted successfully!']);
    wp_die();
}

// AJAX handler for submitting reviews
add_action('wp_ajax_warafy_submit_review', 'warafy_submit_review');
function warafy_submit_review() {
    global $wpdb;
    
    $nonce = sanitize_text_field($_POST['nonce']);
    if (!wp_verify_nonce($nonce, 'warafy_review_nonce')) {
        wp_send_json_error(['message' => 'Security check failed']);
    }
    
    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_send_json_error(['message' => 'You must be logged in to submit a review']);
    }
    
    $product_id = intval($_POST['product_id']);
    $rating = intval($_POST['rating']);
    $review_text = sanitize_textarea_field($_POST['review_text']);
    
    if ($rating < 1 || $rating > 5) {
        wp_send_json_error(['message' => 'Invalid rating']);
    }
    
    if (empty($review_text)) {
        wp_send_json_error(['message' => 'Review cannot be empty']);
    }
    
    if (strlen($review_text) > 2000) {
        wp_send_json_error(['message' => 'Review is too long (max 2000 characters)']);
    }
    
    if (!warafy_user_purchased_product($user_id, $product_id)) {
        wp_send_json_error(['message' => 'You can only review products you have purchased']);
    }
    
    $table = $wpdb->prefix . 'warafy_product_reviews';
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $table WHERE product_id = %d AND user_id = %d",
        $product_id, $user_id
    ));
    
    if ($existing) {
        wp_send_json_error(['message' => 'You have already reviewed this product']);
    }
    
    $result = $wpdb->insert(
        $table,
        [
            'product_id' => $product_id,
            'user_id' => $user_id,
            'rating' => $rating,
            'review_text' => $review_text,
            'review_date' => current_time('mysql'),
            'status' => 'approved'
        ],
        ['%d', '%d', '%d', '%s', '%s', '%s']
    );
    
    if ($result === false) {
        wp_send_json_error(['message' => 'Failed to submit review']);
    }
    
    wp_send_json_success(['message' => 'Review submitted successfully']);
}

// Get comments for a product
function warafy_get_product_comments($product_id, $limit = 10, $offset = 0) {
    global $wpdb;
    
    $table = $wpdb->prefix . 'warafy_product_comments';
    $comments = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table WHERE product_id = %d AND status = 'approved' 
         ORDER BY comment_date DESC LIMIT %d OFFSET %d",
        $product_id, $limit, $offset
    ));
    
    return $comments;
}

// Get reviews for a product
function warafy_get_product_reviews($product_id, $limit = 10, $offset = 0) {
    global $wpdb;
    
    $table = $wpdb->prefix . 'warafy_product_reviews';
    $reviews = $wpdb->get_results($wpdb->prepare(
        "SELECT r.*, u.display_name as user_name, u.user_email as user_email 
         FROM $table r 
         LEFT JOIN {$wpdb->users} u ON r.user_id = u.ID 
         WHERE r.product_id = %d AND r.status = 'approved' 
         ORDER BY r.review_date DESC LIMIT %d OFFSET %d",
        $product_id, $limit, $offset
    ));
    
    return $reviews;
}

// Get comment and review counts
function warafy_get_comment_count($product_id) {
    global $wpdb;
    
    $table = $wpdb->prefix . 'warafy_product_comments';
    $count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE product_id = %d AND status = 'approved'",
        $product_id
    ));
    
    return intval($count);
}

function warafy_get_review_count($product_id) {
    global $wpdb;
    
    $table = $wpdb->prefix . 'warafy_product_reviews';
    $count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE product_id = %d AND status = 'approved'",
        $product_id
    ));
    
    return intval($count);
}

// Get average rating
function warafy_get_average_rating($product_id) {
    global $wpdb;
    
    $table = $wpdb->prefix . 'warafy_product_reviews';
    $avg_rating = $wpdb->get_var($wpdb->prepare(
        "SELECT AVG(rating) FROM $table WHERE product_id = %d AND status = 'approved'",
        $product_id
    ));
    
    return $avg_rating ? round($avg_rating, 1) : 0;
}

// AJAX handler for loading comments
add_action('wp_ajax_warafy_load_comments', 'warafy_load_comments');
add_action('wp_ajax_nopriv_warafy_load_comments', 'warafy_load_comments');
function warafy_load_comments() {
    $product_id = intval($_POST['product_id']);
    $comments = warafy_get_product_comments($product_id);
    
    ob_start();
    if ($comments) :
        foreach ($comments as $comment) :
            $user_display_name = $comment->user_id ? get_the_author_meta('display_name', $comment->user_id) : $comment->user_name;
            $comment_date = date('F j, Y', strtotime($comment->comment_date));
    ?>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 warafy-comment-card">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center warafy-user-avatar">
                            <span class="text-primary font-semibold text-sm"><?php echo strtoupper(substr($user_display_name, 0, 1)); ?></span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white"><?php echo esc_html($user_display_name); ?></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo $comment_date; ?></p>
                        </div>
                    </div>
                </div>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed"><?php echo esc_html($comment->comment_text); ?></p>
            </div>
    <?php
        endforeach;
    else :
    ?>
        <p class="text-gray-500 dark:text-gray-400 text-center py-8">No comments yet. Be the first to comment!</p>
    <?php
    endif;
    
    $html = ob_get_clean();
    wp_send_json_success(['html' => $html]);
}

// AJAX handler for loading reviews
add_action('wp_ajax_warafy_load_reviews', 'warafy_load_reviews');
add_action('wp_ajax_nopriv_warafy_load_reviews', 'warafy_load_reviews');
function warafy_load_reviews() {
    $product_id = intval($_POST['product_id']);
    $reviews = warafy_get_product_reviews($product_id);
    
    ob_start();
    if ($reviews) :
        foreach ($reviews as $review) :
            $review_date = date('F j, Y', strtotime($review->review_date));
    ?>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 warafy-review-card">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center warafy-user-avatar">
                            <span class="text-primary font-semibold text-sm"><?php echo strtoupper(substr($review->user_name, 0, 1)); ?></span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white"><?php echo esc_html($review->user_name); ?></p>
                            <div class="flex items-center gap-2">
                                <div class="flex text-yellow-500">
                                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                                        <span class="material-symbols-outlined text-xs <?php echo $i <= $review->rating ? 'filled' : ''; ?>" style="<?php echo $i <= $review->rating ? 'font-variation-settings: \'FILL\' 1;' : ''; ?>" data-icon="star"></span>
                                    <?php endfor; ?>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo $review_date; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed"><?php echo esc_html($review->review_text); ?></p>
            </div>
    <?php
        endforeach;
    else :
    ?>
        <p class="text-gray-500 dark:text-gray-400 text-center py-8">No reviews yet. Be the first to review!</p>
    <?php
    endif;
    
    $html = ob_get_clean();
    wp_send_json_success(['html' => $html]);
}
