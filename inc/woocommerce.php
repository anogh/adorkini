<?php
/**
 * WooCommerce Functions
 * YouTube video field for products
 * 
 * NOTE: Checkout, orders, and cart customizations are in functions.php
 * Duplicate function definitions were removed to prevent fatal errors.
 */

if (!defined('ABSPATH')) {
    exit;
}
// YouTube Video Field for Products
// ==============================================

// Add YouTube Video field to General tab
add_action('woocommerce_product_options_general_product_data', 'warafy_add_youtube_video_field');
function warafy_add_youtube_video_field() {
    echo '<div class="options_group">';
    
    woocommerce_wp_text_input(array(
        'id'          => '_warafy_youtube_url',
        'label'       => __('YouTube Video URL', 'warafy'),
        'placeholder' => 'https://www.youtube.com/watch?v=VIDEO_ID',
        'desc_tip'    => true,
        'description' => __('Enter the YouTube video URL. Supported formats: youtube.com/watch?v=ID or youtu.be/ID. Leave empty if no video.', 'warafy'),
        'type'        => 'url',
        'wrapper_class' => 'show_if_simple show_if_variable show_if_grouped show_if_external'
    ));
    
    echo '<p class="form-field" style="padding-left: 12px; margin-top: 0;">
            <span class="description" style="font-style: italic; color: #666;">
                ' . __('💡 Tip: If a YouTube URL is provided, a "Video Gallery" section will appear on the product page below the image gallery.', 'warafy') . '
            </span>
          </p>';
          
    echo '</div>';
}

// Save YouTube Video field
add_action('woocommerce_process_product_meta', 'warafy_save_youtube_video_field');
function warafy_save_youtube_video_field($post_id) {
    $youtube_url = isset($_POST['_warafy_youtube_url']) ? sanitize_url($_POST['_warafy_youtube_url']) : '';
    update_post_meta($post_id, '_warafy_youtube_url', $youtube_url);
}

// Helper function to extract YouTube video ID from URL
function warafy_get_youtube_video_id($url) {
    if (empty($url)) {
        return false;
    }
    
    $video_id = false;
    
    // Match youtube.com/watch?v=VIDEO_ID
    if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
        $video_id = $matches[1];
    }
    // Match youtu.be/VIDEO_ID
    elseif (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
        $video_id = $matches[1];
    }
    // Match youtube.com/embed/VIDEO_ID
    elseif (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
        $video_id = $matches[1];
    }
    // Match youtube.com/v/VIDEO_ID
    elseif (preg_match('/youtube\.com\/v\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
        $video_id = $matches[1];
    }
    
    return $video_id;
}

// Get YouTube embed URL (privacy-enhanced mode)
function warafy_get_youtube_embed_url($url) {
    $video_id = warafy_get_youtube_video_id($url);
    if ($video_id) {
        return 'https://www.youtube-nocookie.com/embed/' . $video_id;
    }
    return false;
}

// Get YouTube thumbnail URL
function warafy_get_youtube_thumbnail($url, $quality = 'maxresdefault') {
    $video_id = warafy_get_youtube_video_id($url);
    if ($video_id) {
        // Try maxresdefault first, fallback to hqdefault
        return 'https://img.youtube.com/vi/' . $video_id . '/' . $quality . '.jpg';
    }
    return false;
}
