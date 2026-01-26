<?php
/**
 * Desktop Category Sidebar
 * Displayed on the left side of the homepage on desktop.
 * 
 * @package Adorkini
 */

$args = array(
    'taxonomy'   => 'product_cat',
    'orderby'    => 'count',
    'order'      => 'DESC', // Most popular first
    'hide_empty' => false, // Show even if empty for strict layout structure
    'number'     => 10,
    'parent'     => 0
);
$categories = get_terms( $args );
?>

<aside class="category-sidebar w-64 bg-white border border-gray-200 rounded-lg shadow-sm h-full hidden md:block overflow-y-auto">
    <div class="p-4 border-b border-gray-100">
        <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide flex items-center">
            <span class="mr-2 text-primary"><?php adorkini_icon( 'menu' ); ?></span>
            <?php echo esc_html( __t( 'categories' ) ); ?>
        </h3>
    </div>
    
    <ul class="py-2">
        <?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
            <?php foreach ( $categories as $cat ) : ?>
                <li>
                    <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="flex items-center px-4 py-2.5 hover:bg-gray-50 text-gray-700 hover:text-primary transition-colors text-sm group">
                        <!-- Placeholder for category icon/thumb logic if added later -->
                         <!-- <span class="w-5 h-5 mr-3 bg-gray-100 rounded-full"></span> -->
                         <?php adorkini_icon('chevron-right', 'text-gray-300 w-4 h-4 mr-3 group-hover:text-primary'); ?>
                        <span class="flex-grow"><?php echo esc_html( $cat->name ); ?></span>
                        <span class="text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity">
                            <?php adorkini_icon( 'chevron-right', 'w-3 h-3' ); ?>
                        </span>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php else : ?>
            <li class="px-4 py-2 text-gray-500 text-sm">No categories found.</li>
        <?php endif; ?>
    </ul>
</aside>
