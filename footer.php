
<!-- Desktop Footer -->
<footer class="hidden lg:block bg-white dark:bg-background-dark border-t border-gray-200 dark:border-gray-700">
<div class="container mx-auto px-6 py-12">
<div class="grid grid-cols-2 gap-8 md:grid-cols-4">
<div>
<h4 class="font-bold text-gray-900 dark:text-white"><?php echo __t('Shop'); ?></h4>
<nav class="mt-4 flex flex-col gap-2">
<a class="text-sm text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-primary" href="#"><?php echo __t('New Arrivals'); ?></a>
<a class="text-sm text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-primary" href="#"><?php echo __t('Best Sellers'); ?></a>
<a class="text-sm text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-primary" href="#"><?php echo __t('Deals'); ?></a>
<a class="text-sm text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-primary" href="#"><?php echo __t('All Categories'); ?></a>
</nav>
</div>
<div>
<h4 class="font-bold text-gray-900 dark:text-white"><?php echo __t('Customer Service'); ?></h4>
<nav class="mt-4 flex flex-col gap-2">
<a class="text-sm text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-primary" href="#"><?php echo __t('Contact Us'); ?></a>
<a class="text-sm text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-primary" href="#"><?php echo __t('Help & FAQ'); ?></a>
<a class="text-sm text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-primary" href="#"><?php echo __t('Shipping'); ?></a>
<a class="text-sm text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-primary" href="#"><?php echo __t('Returns'); ?></a>
</nav>
</div>
<div>
<h4 class="font-bold text-gray-900 dark:text-white"><?php echo __t('About Us'); ?></h4>
<nav class="mt-4 flex flex-col gap-2">
<a class="text-sm text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-primary" href="#"><?php echo __t('Our Story'); ?></a>
<a class="text-sm text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-primary" href="#"><?php echo __t('Careers'); ?></a>
<a class="text-sm text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-primary" href="#"><?php echo __t('Press'); ?></a>
</nav>
</div>
<div>
<h4 class="font-bold text-gray-900 dark:text-white"><?php echo __t('Stay Connected'); ?></h4>
<p class="mt-4 text-sm text-gray-500 dark:text-gray-400"><?php echo __t('Join our newsletter for the latest deals and updates.'); ?></p>
<form class="mt-4 flex gap-2">
<input class="form-input flex-1 rounded-lg border-gray-200 bg-background-light text-sm focus:border-primary focus:ring-primary dark:border-gray-700 dark:bg-gray-800" placeholder="<?php echo __t('Enter your email'); ?>" type="email"/>
<button class="flex h-10 cursor-pointer items-center justify-center rounded-lg px-4 bg-primary text-sm font-bold text-white hover:bg-primary/90">
<span><?php echo __t('Sign Up'); ?></span>
</button>
</form>
</div>
</div>
<div class="mt-12 border-t border-gray-200 pt-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
<p>© 2026 Ador Kini. <?php echo __t('All Rights Reserved.'); ?></p>
<p class="text-xs mt-2"><?php echo __t('Version'); ?> 3.3.4</p>
<p class="text-xs mt-2">Last edit: <?php echo esc_html( current_time( 'M j, Y g:i A' ) ); ?></p>
</div>
</div>
</footer>

<!-- Mobile Bottom Navigation -->
<nav class="lg:hidden fixed bottom-0 left-0 right-0 z-20 w-full border-t border-slate-200 dark:border-slate-800 bg-white/90 dark:bg-background-dark/90 backdrop-blur-sm">
<div class="flex h-16 items-center justify-around">
<a class="flex flex-col items-center justify-center gap-1 <?php echo (is_front_page() || is_home()) ? 'text-primary' : 'text-slate-500 dark:text-slate-400'; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>">
<div class="flex h-10 w-10 items-center justify-center rounded-full <?php echo (is_front_page() || is_home()) ? 'bg-primary' : 'bg-gray-800'; ?> shadow-lg">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
        <polyline points="9,22 9,12 15,12 15,22"/>
    </svg>
</div>
<span class="text-xs <?php echo (is_front_page() || is_home()) ? 'font-bold' : 'font-medium'; ?>"><?php echo __t('Home'); ?></span>
</a>
<a class="flex flex-col items-center justify-center gap-1 <?php echo (is_page('categories') || get_post_type() === 'product') ? 'text-primary' : 'text-slate-500 dark:text-slate-400'; ?>" href="<?php echo esc_url( site_url( '/categories' ) ); ?>">
<div class="flex h-10 w-10 items-center justify-center rounded-full <?php echo (is_page('categories') || get_post_type() === 'product') ? 'bg-primary' : 'bg-gray-800'; ?> shadow-lg">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
    </svg>
</div>
<span class="text-xs <?php echo (is_page('categories') || get_post_type() === 'product') ? 'font-bold' : 'font-medium'; ?>"><?php echo __t('Categories'); ?></span>
</a>
<a class="flex flex-col items-center justify-center gap-1 <?php echo is_cart() ? 'text-primary' : 'text-slate-500 dark:text-slate-400'; ?>" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
    <div class="relative">
        <div class="flex h-10 w-10 items-center justify-center rounded-full <?php echo is_cart() ? 'bg-primary' : 'bg-gray-800'; ?> shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
        </div>
        <?php if ( function_exists('WC') && WC()->cart && WC()->cart->get_cart_contents_count() > 0 ) : ?>
        <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-white text-[10px] font-bold text-primary border border-primary">
            <?php echo WC()->cart->get_cart_contents_count(); ?>
        </span>
        <?php endif; ?>
    </div>
<span class="text-xs <?php echo is_cart() ? 'font-bold' : 'font-medium'; ?>"><?php echo __t('Cart'); ?></span>
</a>
<a class="flex flex-col items-center justify-center gap-1 <?php echo is_page('my-love') ? 'text-primary' : 'text-slate-500 dark:text-slate-400'; ?>" href="<?php echo esc_url( home_url( '/my-love' ) ); ?>">
    <div class="relative">
        <div class="flex h-10 w-10 items-center justify-center rounded-full <?php echo is_page('my-love') ? 'bg-primary' : 'bg-gray-800'; ?> shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ffffff" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
        </div>
        <span class="warafy-wishlist-count absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-white text-[10px] font-bold text-primary border border-primary" style="display: none;">0</span>
    </div>
    <span class="text-xs <?php echo is_page('my-love') ? 'font-bold' : 'font-medium'; ?>"><?php echo __t('My Love'); ?></span>
</a>
<a class="flex flex-col items-center justify-center gap-1 <?php echo (is_account_page() || is_page('profile') || is_page('my-account')) ? 'text-primary' : 'text-slate-500 dark:text-slate-400'; ?>" href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>">
<div class="flex h-10 w-10 items-center justify-center rounded-full <?php echo (is_account_page() || is_page('profile') || is_page('my-account')) ? 'bg-primary' : 'bg-gray-800'; ?> shadow-lg">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
    </svg>
</div>
<span class="text-xs <?php echo (is_account_page() || is_page('profile') || is_page('my-account')) ? 'font-bold' : 'font-medium'; ?>"><?php echo __t('Profile'); ?></span>
</a>
</div>
</nav>

</div> <!-- End .flex-col -->
<?php wp_footer(); ?>
</body>
</html>
