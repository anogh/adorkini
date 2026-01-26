<?php
/**
 * Product Ranking System
 * Adds custom ranking field and logic for ranking badges.
 * 
 * @package Adorkini
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Meta Box for Ranking
 */
function adorkini_add_ranking_meta_box() {
    add_meta_box(
        'adorkini_product_ranking',
        __( 'Product Ranking', 'adorkini' ),
        'adorkini_ranking_meta_box_callback',
        'product',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'adorkini_add_ranking_meta_box' );

/**
 * Meta Box Callback
 */
function adorkini_ranking_meta_box_callback( $post ) {
    $value = get_post_meta( $post->ID, '_adorkini_rank', true );
    ?>
    <label for="adorkini_rank_field"><?php _e( 'Rank (1-10):', 'adorkini' ); ?></label>
    <select name="adorkini_rank_field" id="adorkini_rank_field" class="widefat">
        <option value=""><?php _e( 'None', 'adorkini' ); ?></option>
        <?php for ( $i = 1; $i <= 10; $i++ ) : ?>
            <option value="<?php echo esc_attr( $i ); ?>" <?php selected( $value, $i ); ?>>
                <?php echo is_int( $i ) ? '#' . $i : $i; ?>
            </option>
        <?php endfor; ?>
    </select>
    <p class="description"><?php _e( 'Set rank #1, #2, #3 for Gold, Silver, Bronze badges.', 'adorkini' ); ?></p>
    <?php
}

/**
 * Save Meta Box Data
 */
function adorkini_save_ranking_meta_box( $post_id ) {
    if ( array_key_exists( 'adorkini_rank_field', $_POST ) ) {
        update_post_meta(
            $post_id,
            '_adorkini_rank',
            sanitize_text_field( $_POST['adorkini_rank_field'] )
        );
    }
}
add_action( 'save_post', 'adorkini_save_ranking_meta_box' );

/**
 * Get Product Rank
 */
function adorkini_get_product_rank( $product_id ) {
    return get_post_meta( $product_id, '_adorkini_rank', true );
}

/**
 * Display Ranking Badge
 */
function adorkini_show_ranking_badge() {
    global $product;
    if ( ! $product ) return;

    $rank = adorkini_get_product_rank( $product->get_id() );
    if ( ! $rank ) return;

    $badge_color = 'bg-gray-500'; // Default 4-10
    $border_color = 'border-gray-500';
    
    if ( $rank == 1 ) {
        $badge_color = 'bg-[#ffd700] text-black'; // Gold
        $border_color = 'border-[#dba100]';
    } elseif ( $rank == 2 ) {
        $badge_color = 'bg-[#c0c0c0] text-black'; // Silver
        $border_color = 'border-[#a0a0a0]';
    } elseif ( $rank == 3 ) {
        $badge_color = 'bg-[#cd7f32] text-white'; // Bronze
        $border_color = 'border-[#a05a1f]';
    }

    set_query_var( 'rank', $rank );
    set_query_var( 'badge_color', $badge_color );
    set_query_var( 'border_color', $border_color );
    
    get_template_part( 'template-parts/ranking-badge' );
}
add_action( 'woocommerce_before_shop_loop_item_title', 'adorkini_show_ranking_badge', 10 );
add_action( 'woocommerce_single_product_summary', 'adorkini_show_ranking_badge', 5 );
