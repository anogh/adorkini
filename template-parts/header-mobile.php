<?php
/**
 * Mobile Header Template
 * 
 * @package Adorkini
 */

$is_home = is_front_page() || is_home();
$is_checkout = is_checkout();
$is_cart = is_cart();
$page_title = get_the_title();

if ( $is_checkout ) {
    $page_title = 'Checkout';
} elseif ( $is_cart ) {
    $page_title = __t( 'cart' );
}
?>

<header class="mobile-header bg-white border-b border-gray-200 sticky top-0 z-40 h-[60px] flex items-center px-4 justify-between">
    
    <?php if ( $is_home ) : ?>
        <!-- Home Header: Logo + Search Icon -->
        <div class="flex items-center w-full">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-xl font-bold text-gray-900 mr-auto">
                Adorkini
            </a>
            
            <a href="?lang=<?php echo adorkini_get_current_lang() === 'en' ? 'bn' : 'en'; ?>" class="mr-4 text-xs font-bold border border-gray-300 rounded px-2 py-1 uppercase">
                <?php echo adorkini_get_current_lang() === 'en' ? 'BN' : 'EN'; ?>
            </a>

            <!-- Search Trigger (could open modal, for now simple link to search page or just input) -->
             <button class="text-gray-600 p-2">
                <?php adorkini_icon( 'search', 'text-xl' ); ?>
            </button>
        </div>

    <?php else : ?>
        <!-- Internal Page Header: Back Button + Title -->
        <div class="flex items-center w-full">
            <a href="javascript:history.back()" class="mr-4 p-1 text-gray-600">
                <span class="icon text-xl">&larr;</span> <!-- Or SVG chevron-left -->
            </a>
            <h1 class="text-lg font-semibold text-gray-900 truncate">
                <?php echo esc_html( $page_title ); ?>
            </h1>
        </div>
    <?php endif; ?>

</header>
