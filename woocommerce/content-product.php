<?php
/**
 * The template for displaying product content within loops
 *
 * @package Adorkini
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<div <?php wc_product_class( 'group relative bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1', $product ); ?>>
	
    <!-- Ranking Badge Hook -->
    <?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?>

    <!-- Product Image -->
	<a href="<?php echo esc_url( get_permalink() ); ?>" class="block relative aspect-w-1 aspect-h-1 overflow-hidden bg-gray-100">
		<?php 
        if ( has_post_thumbnail() ) {
            echo get_the_post_thumbnail( $product->get_id(), 'woocommerce_thumbnail', array( 'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500' ) );
        } else {
            echo '<img src="' . wc_placeholder_img_src() . '" alt="Placeholder" class="w-full h-full object-cover opacity-50">';
        }
        ?>
        
        <!-- Quick Action Overlay (Optional) -->
        <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center p-4">
            <!-- Add to Cart or View Button could go here (mobile optimized usually avoids hover though) -->
        </div>
	</a>

    <!-- Product Info -->
	<div class="p-3">
        <!-- Category -->
        <div class="text-[10px] text-gray-500 uppercase tracking-wider mb-1 truncate">
            <?php echo wc_get_product_category_list( $product->get_id(), ', ' ); ?>
        </div>

        <!-- Title -->
		<h2 class="text-sm font-medium text-gray-900 line-clamp-2 min-h-[2.5em] mb-1">
			<a href="<?php echo esc_url( get_permalink() ); ?>">
				<?php echo get_the_title(); ?>
			</a>
		</h2>

        <!-- Price -->
		<div class="font-bold text-primary text-base">
			<?php echo $product->get_price_html(); ?>
		</div>

        <!-- Add to Cart Loop (simplified) -->
        <div class="mt-3">
            <?php woocommerce_template_loop_add_to_cart(); ?>
        </div>
	</div>
</div>
