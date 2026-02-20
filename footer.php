
<!-- Desktop Footer -->
<footer class="hidden lg:block bg-black border-t border-gray-800">
<div class="container mx-auto px-6 py-12">
<div class="grid grid-cols-2 gap-8 md:grid-cols-4">
<div>
<h4 class="font-bold text-white"><?php echo __t('Shop'); ?></h4>
<nav class="mt-4 flex flex-col gap-2">
<a class="text-sm text-gray-400 hover:text-[#FFB800]" href="#"><?php echo __t('New Arrivals'); ?></a>
<a class="text-sm text-gray-400 hover:text-[#FFB800]" href="#"><?php echo __t('Best Sellers'); ?></a>
<a class="text-sm text-gray-400 hover:text-[#FFB800]" href="#"><?php echo __t('Deals'); ?></a>
<a class="text-sm text-gray-400 hover:text-[#FFB800]" href="#"><?php echo __t('All Categories'); ?></a>
</nav>
</div>
<div>
<h4 class="font-bold text-white"><?php echo __t('Customer Service'); ?></h4>
<nav class="mt-4 flex flex-col gap-2">
<a class="text-sm text-gray-400 hover:text-[#FFB800]" href="#"><?php echo __t('Contact Us'); ?></a>
<a class="text-sm text-gray-400 hover:text-[#FFB800]" href="#"><?php echo __t('Help & FAQ'); ?></a>
<a class="text-sm text-gray-400 hover:text-[#FFB800]" href="#"><?php echo __t('Shipping'); ?></a>
<a class="text-sm text-gray-400 hover:text-[#FFB800]" href="#"><?php echo __t('Returns'); ?></a>
</nav>
</div>
<div>
<h4 class="font-bold text-white"><?php echo __t('About Us'); ?></h4>
<nav class="mt-4 flex flex-col gap-2">
<a class="text-sm text-gray-400 hover:text-[#FFB800]" href="#"><?php echo __t('Our Story'); ?></a>
<a class="text-sm text-gray-400 hover:text-[#FFB800]" href="#"><?php echo __t('Careers'); ?></a>
<a class="text-sm text-gray-400 hover:text-[#FFB800]" href="#"><?php echo __t('Press'); ?></a>
</nav>
</div>
<div>
<h4 class="font-bold text-white"><?php echo __t('Stay Connected'); ?></h4>
<p class="mt-4 text-sm text-gray-400"><?php echo __t('Join our newsletter for the latest deals and updates.'); ?></p>
<form class="mt-4 flex gap-2">
<input class="form-input flex-1 rounded-lg border-gray-700 bg-gray-900 text-sm text-white focus:border-[#FFB800] focus:ring-[#FFB800]" placeholder="<?php echo __t('Enter your email'); ?>" type="email"/>
<button class="flex h-10 cursor-pointer items-center justify-center rounded-lg px-4 bg-[#FFB800] text-sm font-bold text-black hover:bg-[#FFB800]/90">
<span><?php echo __t('Sign Up'); ?></span>
</button>
</form>
</div>
</div>
<div class="mt-12 border-t border-gray-800 pt-8 text-center text-sm text-gray-400">
<p>© 2026 Ador Kini. <?php echo __t('All Rights Reserved.'); ?></p>
<p class="text-xs mt-2"><?php echo __t('Version'); ?> 3.4.1</p>
<p class="text-xs mt-2">Last edit: <?php echo esc_html( current_time( 'M j, Y g:i A' ) ); ?></p>
</div>
</div>
</footer>

<!-- Mobile Bottom Navigation -->
<nav class="lg:hidden fixed bottom-0 left-0 right-0 z-50 w-full bg-black border-t border-black pb-safe">
    <?php
    $is_home = is_front_page() || is_home();
    $is_cats = is_page('categories') || is_product_category() || is_singular('product');
    $is_cart = is_cart();
    $is_user = is_account_page() || is_page('profile') || is_page('my-account');
    ?>
    <div class="flex h-[60px] items-center justify-around px-2">
        <!-- Home -->
        <a class="flex flex-col items-center justify-center gap-[2px] text-[#FFB800] w-1/4" href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <div class="flex h-6 w-6 items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-[20px] h-[20px]">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </div>
            <span class="text-[10px] font-medium leading-none"><?php echo __t('Home'); ?></span>
        </a>

        <!-- Categories -->
        <a class="flex flex-col items-center justify-center gap-[2px] text-[#FFB800] w-1/4" href="<?php echo esc_url( site_url( '/categories' ) ); ?>">
            <div class="flex h-6 w-6 items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-[20px] h-[20px]">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </div>
            <span class="text-[10px] font-medium leading-none"><?php echo __t('Categories'); ?></span>
        </a>

        <!-- Cart -->
        <a class="flex flex-col items-center justify-center gap-[2px] text-[#FFB800] relative w-1/4" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
            <div class="flex h-6 w-6 items-center justify-center relative">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-[20px] h-[20px]">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                <!-- Badge -->
                <span class="cart-count absolute -top-[4px] -right-[6px] flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[9px] font-bold text-white border border-black" 
                      style="<?php echo (function_exists('WC') && WC()->cart && WC()->cart->get_cart_contents_count() > 0) ? '' : 'display: none;'; ?>">
                    <?php echo function_exists('WC') && WC()->cart ? WC()->cart->get_cart_contents_count() : '0'; ?>
                </span>
            </div>
            <span class="text-[10px] font-medium leading-none"><?php echo __t('My Cart'); ?></span>
        </a>

        <!-- Profile -->
        <a class="flex flex-col items-center justify-center gap-[2px] text-[#FFB800] w-1/4" href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>">
            <div class="flex h-6 w-6 items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-[20px] h-[20px]">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
            <span class="text-[10px] font-medium leading-none"><?php echo __t('My Account'); ?></span>
        </a>
    </div>
</nav>

</div> <!-- End .flex-col -->
<?php wp_footer(); ?>
</body>
</html>
