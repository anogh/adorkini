<?php
/**
 * Mobile Bottom Navigation Template
 * 
 * @package Adorkini
 */

$items = array(
    'home' => array(
        'label' => __t('home'),
        'url'   => home_url('/'),
        'icon'  => 'home'
    ),
    'categories' => array(
        'label' => __t('categories'),
        'url'   => home_url('/shop/'), // Or a custom category page
        'icon'  => 'category' // Ensure this matches svg-icons.php key
    ),
    'cart' => array(
        'label' => __t('cart'),
        'url'   => wc_get_cart_url(),
        'icon'  => 'cart',
        'badge' => WC()->cart ? WC()->cart->get_cart_contents_count() : 0
    ),
    'wishlist' => array(
        'label' => __t('wishlist'),
        'url'   => home_url('/wishlist/'), // Assuming wishlist endpoint exists or page
        'icon'  => 'heart'
    ),
    'profile' => array(
        'label' => __t('profile'),
        'url'   => wc_get_account_endpoint_url('dashboard'),
        'icon'  => 'user'
    )
);

// Get current URL to highlight active item
$current_url = home_url( add_query_arg( array(), $wp->request ) );
?>

<div class="bottom-nav fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 z-50 h-[60px] flex justify-around items-center px-2 shadow-[0_-5px_15px_rgba(0,0,0,0.05)] md:hidden">
    <?php foreach ( $items as $key => $item ) : 
        $active_class = ( strpos($current_url, $item['url']) !== false ) ? 'text-primary' : 'text-gray-500 hover:text-gray-900';
    ?>
        <a href="<?php echo esc_url( $item['url'] ); ?>" class="flex flex-col items-center justify-center w-full h-full relative group <?php echo esc_attr( $active_class ); ?>">
            <span class="relative mb-0.5">
                <?php adorkini_icon( $item['icon'], 'text-2xl transition-transform duration-200 group-active:scale-95' ); ?>
                
                <?php if ( isset( $item['badge'] ) && $item['badge'] > 0 ) : ?>
                    <span class="absolute -top-1.5 -right-2 bg-primary text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center border border-white">
                        <?php echo esc_html( $item['badge'] ); ?>
                    </span>
                <?php endif; ?>
            </span>
            <span class="text-[10px] font-medium leading-none"><?php echo esc_html( $item['label'] ); ?></span>
        </a>
    <?php endforeach; ?>
</div>
