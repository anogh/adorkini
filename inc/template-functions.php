<?php
/**
 * Template Functions
 * Custom template overrides and helpers
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Force My Account page to use our custom template
 */
function warafy_force_my_account_template($template) {
    if (is_page('my-account')) {
        return get_template_directory() . '/page-my-account.php';
    }
    return $template;
}
add_filter('template_include', 'warafy_force_my_account_template');

/**
 * Render Desktop Compact Product Card
 */
if (!function_exists('warafy_render_desktop_compact_product')) {
    function warafy_render_desktop_compact_product($product, $attributes = []) {
        if (!$product) return;

        $attr_string = '';
        if (!empty($attributes)) {
            foreach ($attributes as $key => $value) {
                $attr_string .= ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
            }
        }
        ?>
        <div class="bg-white border border-gray-200 flex flex-col p-3 h-full rounded-md relative transition-shadow hover:shadow-lg warafy-desktop-product-card"<?php echo $attr_string; ?>>
            <a href="<?php echo get_permalink($product->get_id()); ?>" class="w-full aspect-square bg-center bg-no-repeat bg-contain mb-3 block" style='background-image: url("<?php echo get_the_post_thumbnail_url($product->get_id(), 'woocommerce_thumbnail'); ?>");'></a>

            <a href="<?php echo get_permalink($product->get_id()); ?>" class="w-full text-sm font-medium text-black dark:text-gray-200 line-clamp-2 leading-[1.3] min-h-[36px] mb-3 hover:text-primary">
                <?php echo get_the_title($product->get_id()); ?>
            </a>

            <div class="w-full flex items-end justify-between mt-auto gap-2">
                <div class="flex items-center flex-wrap mobile-compact-price flex-1 min-w-0 pr-1">
                    <?php echo $product->get_price_html(); ?>
                </div>

                <button class="add-to-cart-btn bg-[#FFB800] hover:bg-[#e6a600] text-black text-xs font-bold px-4 py-1.5 rounded-full flex items-center justify-center whitespace-nowrap flex-shrink-0 transition-colors" data-product-id="<?php echo $product->get_id(); ?>">
                    <span class="add-text"><?php echo __t('Add to cart'); ?></span>
                    <span class="added-text hidden text-white"><?php echo __t('Added'); ?></span>
                </button>
            </div>
        </div>
        <?php
    }
}

/**
 * Render Mobile Compact Product Card
 */
if (!function_exists('warafy_render_mobile_compact_product')) {
    function warafy_render_mobile_compact_product($product, $attributes = []) {
        if (!$product) return;

        $attr_string = '';
        if (!empty($attributes)) {
            foreach ($attributes as $key => $value) {
                $attr_string .= ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
            }
        }
        ?>
        <div class="bg-white border border-gray-200 flex flex-col p-2 h-full rounded-[2px] relative warafy-mobile-product-card"<?php echo $attr_string; ?>>
            <a href="<?php echo get_permalink($product->get_id()); ?>" class="w-full aspect-square bg-center bg-no-repeat bg-contain mb-2 block" style='background-image: url("<?php echo get_the_post_thumbnail_url($product->get_id(), 'woocommerce_thumbnail'); ?>");'></a>

            <a href="<?php echo get_permalink($product->get_id()); ?>" class="w-full text-[11px] font-medium text-black line-clamp-2 leading-[1.3] min-h-[29px] mb-2 hover:text-primary">
                <?php echo get_the_title($product->get_id()); ?>
            </a>

            <div class="w-full flex items-end justify-between mt-auto gap-1">
                <div class="flex items-center flex-wrap mobile-compact-price flex-1 min-w-0 pr-1">
                    <?php echo $product->get_price_html(); ?>
                </div>

                <button class="add-to-cart-btn bg-[#FFB800] hover:bg-[#e6a600] text-black text-[10px] font-bold px-[8px] py-[3px] rounded-full flex items-center justify-center whitespace-nowrap flex-shrink-0" data-product-id="<?php echo $product->get_id(); ?>">
                    <span class="add-text"><?php echo __t('Add to cart'); ?></span>
                    <span class="added-text hidden text-white"><?php echo __t('Added'); ?></span>
                </button>
            </div>
        </div>
        <?php
    }
}
