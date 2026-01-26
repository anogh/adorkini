<?php
/**
 * Desktop Header Template
 * 
 * @package Adorkini
 */

$current_lang = adorkini_get_current_lang();
$target_lang = ( $current_lang === 'en' ) ? 'bn' : 'en';
$lang_label  = ( $current_lang === 'en' ) ? 'BN' : 'EN';
?>

<header class="site-header bg-white border-b border-gray-200 sticky top-0 z-50 h-[80px]">
    <div class="container h-full flex items-center justify-between mx-auto px-4">
        
        <!-- Logo -->
        <div class="site-branding flex-shrink-0 mr-8">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="text-2xl font-bold text-gray-900">
                Adorkini
            </a>
        </div>

        <!-- Search Bar -->
        <div class="flex-grow max-w-2xl mx-4">
            <form role="search" method="get" class="search-form flex relative" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <input type="search" 
                       class="w-full h-10 px-4 pr-10 rounded-full border border-gray-300 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary bg-gray-50 text-sm" 
                       placeholder="<?php echo esc_attr( __t( 'search_placeholder' ) ); ?>" 
                       value="<?php echo get_search_query(); ?>" 
                       name="s" />
                <button type="submit" class="absolute right-0 top-0 h-10 w-10 flex items-center justify-center text-gray-500 hover:text-primary">
                    <?php adorkini_icon( 'search' ); ?>
                </button>
            </form>
        </div>

        <!-- Right Definitions -->
        <div class="header-actions flex items-center space-x-6">
            
            <!-- Language Toggle -->
            <a href="?lang=<?php echo esc_attr( $target_lang ); ?>" class="text-sm font-semibold text-gray-600 hover:text-primary transition-colors uppercase border border-gray-300 rounded px-2 py-1">
                <?php echo esc_html( $lang_label ); ?>
            </a>

            <!-- Admin/User Actions -->
            <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="flex flex-col items-center text-gray-600 hover:text-primary group">
                <span class="relative">
                    <?php adorkini_icon( 'user', 'text-2xl mb-1 group-hover:-translate-y-1 transition-transform duration-300' ); ?>
                </span>
                <span class="text-xs font-medium"><?php echo esc_html( __t( 'profile' ) ); ?></span>
            </a>

            <!-- Cart -->
            <?php 
            $cart_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0; 
            ?>
            <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="flex flex-col items-center text-gray-600 hover:text-primary group relative">
                <span class="relative">
                    <?php adorkini_icon( 'cart', 'text-2xl mb-1 group-hover:-translate-y-1 transition-transform duration-300' ); ?>
                    <?php if ( $cart_count > 0 ) : ?>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">
                            <?php echo esc_html( $cart_count ); ?>
                        </span>
                    <?php endif; ?>
                </span>
                <span class="text-xs font-medium"><?php echo esc_html( __t( 'cart' ) ); ?></span>
            </a>

        </div>

    </div>
</header>
