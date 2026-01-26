<?php
/**
 * Product Video Gallery
 * Adds YouTube video support to products.
 * 
 * @package Adorkini
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Video URL Field to Product General Tab
 */
function adorkini_add_video_field() {
    woocommerce_wp_text_input(
        array(
            'id'          => '_adorkini_video_url',
            'label'       => __( 'YouTube Video URL', 'adorkini' ),
            'placeholder' => 'https://www.youtube.com/watch?v=...',
            'desc_tip'    => 'true',
            'description' => __( 'Enter the full YouTube URL. It will be displayed below the product gallery.', 'adorkini' ),
        )
    );
}
add_action( 'woocommerce_product_options_general_product_data', 'adorkini_add_video_field' );

/**
 * Save Video Field
 */
function adorkini_save_video_field( $post_id ) {
    $video_url = isset( $_POST['_adorkini_video_url'] ) ? sanitize_text_field( $_POST['_adorkini_video_url'] ) : '';
    update_post_meta( $post_id, '_adorkini_video_url', $video_url );
}
add_action( 'woocommerce_process_product_meta', 'adorkini_save_video_field' );

/**
 * Display Video Gallery on Single Product
 * Hooked out of standard tabs or image gallery
 */
function adorkini_show_video_gallery() {
    global $product;
    if ( ! $product ) return;

    $video_url = get_post_meta( $product->get_id(), '_adorkini_video_url', true );
    if ( empty( $video_url ) ) return;

    // Extract Video ID
    $video_id = '';
    parse_str( parse_url( $video_url, PHP_URL_QUERY ), $params );
    if ( isset( $params['v'] ) ) {
        $video_id = $params['v'];
    } elseif ( preg_match( '/youtu\.be\/([a-zA-Z0-9_-]+)/', $video_url, $matches ) ) {
        $video_id = $matches[1];
    }

    if ( $video_id ) {
        ?>
        <div class="adorkini-video-gallery mt-8 mb-8">
             <h3 class="text-lg font-bold mb-4 text-gray-900 border-l-4 border-red-600 pl-3">
                <?php _e( 'Video Gallery', 'adorkini' ); ?>
            </h3>
            <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden shadow-md">
                <iframe 
                    width="100%" 
                    height="400" 
                    src="https://www.youtube-nocookie.com/embed/<?php echo esc_attr( $video_id ); ?>?rel=0&modestbranding=1" 
                    title="Product Video"
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen
                    class="w-full h-full object-cover">
                </iframe>
            </div>
        </div>
        <?php
    }
}
// Add to single product page, after summary (or wherever preferred)
add_action( 'woocommerce_after_single_product_summary', 'adorkini_show_video_gallery', 15 );
