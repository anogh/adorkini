<!DOCTYPE html>
<?php
// Server-side detection for Facebook/Instagram WebView and other in-app browsers
$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
// Also check for fbclid parameter (Facebook click ID - present in all links from Facebook)
$has_fbclid = isset($_GET['fbclid']) || (isset($_SERVER['QUERY_STRING']) && strpos($_SERVER['QUERY_STRING'], 'fbclid') !== false);
$is_facebook_webview = (
    $has_fbclid || // Links from Facebook always have fbclid
    strpos($user_agent, 'FBAN') !== false ||
    strpos($user_agent, 'FBAV') !== false ||
    strpos($user_agent, 'Instagram') !== false ||
    strpos($user_agent, 'FB_IAB') !== false ||
    strpos($user_agent, 'FBIOS') !== false ||
    strpos($user_agent, 'FB4A') !== false ||
    strpos($user_agent, 'FBSV') !== false ||
    strpos($user_agent, 'Messenger') !== false ||
    strpos($user_agent, 'Line/') !== false ||
    strpos($user_agent, 'KAKAOTALK') !== false ||
    strpos($user_agent, 'Snapchat') !== false ||
    strpos($user_agent, 'Twitter') !== false ||
    strpos($user_agent, 'Pinterest') !== false ||
    strpos($user_agent, 'LinkedIn') !== false ||
    strpos($user_agent, 'Facebook') !== false ||
    strpos($user_agent, 'fbconnect') !== false ||
    (preg_match('/(wv|WebView)/i', $user_agent)) ||
    (preg_match('/(iPhone|iPod|iPad).*AppleWebKit/i', $user_agent) && strpos($user_agent, 'Safari') === false)
);
?>
<html <?php language_attributes(); ?> class="dark">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="<?php echo esc_url( warafy_get_logo_url() ); ?>">
    <link rel="shortcut icon" type="image/jpeg" href="<?php echo esc_url( warafy_get_logo_url() ); ?>">
    <link rel="apple-touch-icon" href="<?php echo esc_url( warafy_get_logo_url() ); ?>">
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <?php wp_head(); ?>
    <?php if ($is_facebook_webview): ?>
    <!-- WebView detected - disable preloader completely and force content visible -->
    <style id="webview-override">
        #warafy-preloader { display: none !important; opacity: 0 !important; visibility: hidden !important; pointer-events: none !important; height: 0 !important; width: 0 !important; overflow: hidden !important; position: absolute !important; }
        #warafy-content, #warafy-content.loaded, div#warafy-content { opacity: 1 !important; pointer-events: auto !important; animation: none !important; visibility: visible !important; transition: none !important; display: flex !important; }
        body, html { visibility: visible !important; opacity: 1 !important; }
        * { -webkit-animation-duration: 0s !important; animation-duration: 0s !important; -webkit-animation: none !important; animation: none !important; }
    </style>
    <script>document.documentElement.classList.add('webview-mode');</script>
    <?php endif; ?>
    <style>
        /* Critical Visibility Fix */
        .hidden { display: none !important; }
        @media (min-width: 1024px) {
            .lg\:block { display: block !important; }
            .lg\:flex { display: flex !important; }
            .lg\:hidden { display: none !important; }
            .lg\:grid { display: grid !important; }
        }
        
        /* PRELOADER - Critical CSS */
        #warafy-preloader {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            background: #000000 !important;
            z-index: 999999 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            overflow: hidden !important;
        }
        #warafy-preloader .preloader-logo {
            width: 40px;
            height: auto;
            animation: logoGrow 3s ease-in-out forwards;
        }
        @keyframes logoGrow {
            0% {
                transform: scale(0.3);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            20% {
                transform: scale(0.5);
            }
            40% {
                transform: scale(1);
            }
            60% {
                transform: scale(2);
            }
            80% {
                transform: scale(4);
                opacity: 0.7;
            }
            100% {
                transform: scale(8);
                opacity: 0;
            }
        }
        #warafy-content {
            opacity: 0;
            pointer-events: none;
            animation: warafy-content-fallback 0.5s ease forwards;
            animation-delay: 4s; /* Fallback: show content after 4s if JS fails */
        }
        #warafy-content.loaded {
            opacity: 1;
            pointer-events: auto;
            transition: opacity 0.3s ease;
            animation: none; /* Cancel fallback animation when JS works */
        }
        @keyframes warafy-content-fallback {
            to {
                opacity: 1;
                pointer-events: auto;
            }
        }
        /* Facebook in-app browser & other webviews - force content visible immediately */
        @supports (-webkit-touch-callout: none) {
            #warafy-content {
                animation-delay: 1s !important; /* Faster fallback for mobile webviews */
            }
        }
        /* Additional fallback for problematic webviews */
        @media (max-width: 768px) {
            #warafy-content {
                animation-delay: 2s !important; /* Even faster for mobile */
            }
        }
    </style>
</head>
<body <?php body_class('bg-background-light dark:bg-background-dark font-display text-gray-800 dark:text-gray-200'); ?>>
<?php if (!$is_facebook_webview): ?>
<!-- Preloader -->
<div id="warafy-preloader">
    <img src="<?php echo esc_url( warafy_get_logo_url() ); ?>" alt="Ador Kini" class="preloader-logo">
</div>
<?php endif; ?>
<script>
    // IMMEDIATE WebView detection - runs before DOM is ready
    (function() {
        var ua = navigator.userAgent || navigator.vendor || '';
        var isFacebookBrowser = /FBAN|FBAV|Instagram|FB_IAB|FBIOS|FB4A|FBSV|Messenger|Line\/|KAKAOTALK|Snapchat|Twitter|Pinterest|LinkedIn|Facebook|fbconnect/i.test(ua);
        var isWebView = /(wv|WebView)/i.test(ua) || (/(iPhone|iPod|iPad).*AppleWebKit/i.test(ua) && ua.indexOf('Safari') === -1);
        // Also detect via URL parameters (fbclid from Facebook links)
        var hasFbclid = window.location.search.indexOf('fbclid') > -1;
        var isMobile = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua);
        
        // For Facebook browser, webviews, fbclid links, and mobile - skip preloader entirely
        if (isFacebookBrowser || isWebView || hasFbclid || isMobile) {
            // Mark as webview mode immediately
            document.documentElement.classList.add('webview-mode');
            // Inject override styles immediately (before DOM ready)
            var style = document.createElement('style');
            style.id = 'webview-js-override';
            style.textContent = '#warafy-preloader { display: none !important; height: 0 !important; width: 0 !important; opacity: 0 !important; visibility: hidden !important; pointer-events: none !important; position: absolute !important; } #warafy-content, div#warafy-content { opacity: 1 !important; pointer-events: auto !important; animation: none !important; visibility: visible !important; transition: none !important; display: flex !important; } body, html { visibility: visible !important; opacity: 1 !important; } * { animation-duration: 0s !important; -webkit-animation-duration: 0s !important; }';
            (document.head || document.documentElement).appendChild(style);
            
            // Also try to remove/hide preloader if it exists
            var preloader = document.getElementById('warafy-preloader');
            if (preloader) {
                preloader.style.cssText = 'display:none!important;height:0!important;width:0!important;opacity:0!important;visibility:hidden!important;';
                try { preloader.remove(); } catch(e) {}
            }
            // Force content visible directly if element exists
            var content = document.getElementById('warafy-content');
            if (content) {
                content.style.cssText = 'opacity:1!important;pointer-events:auto!important;visibility:visible!important;animation:none!important;display:flex!important;';
                content.classList.add('loaded');
            }
            // Force body visibility
            if (document.body) document.body.style.cssText += 'visibility:visible!important;opacity:1!important;';
            
            // Also run again after DOM ready as a fallback
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    var p = document.getElementById('warafy-preloader');
                    if (p) { p.style.cssText = 'display:none!important;'; try { p.remove(); } catch(e) {} }
                    var c = document.getElementById('warafy-content');
                    if (c) { c.style.cssText = 'opacity:1!important;pointer-events:auto!important;visibility:visible!important;animation:none!important;display:flex!important;'; c.classList.add('loaded'); }
                });
            }
            return;
        }
        
        // Check if preloader was already shown in this session
        var preloaderShown = false;
        try {
            preloaderShown = sessionStorage.getItem('warafy_preloader_shown');
        } catch(e) {
            // sessionStorage not available - skip preloader
            preloaderShown = true;
        }
        
        if (preloaderShown) {
            var preloader = document.getElementById('warafy-preloader');
            if (preloader) {
                preloader.style.display = 'none';
                preloader.remove();
            }
            // Force content visible immediately for subsequent visits
            var style = document.createElement('style');
            style.textContent = '#warafy-content { opacity: 1 !important; pointer-events: auto !important; animation: none !important; }';
            document.head.appendChild(style);
        } else {
            // First visit - show animation
            try {
                sessionStorage.setItem('warafy_preloader_shown', 'true');
            } catch(e) {}
            
            setTimeout(function() {
                var preloader = document.getElementById('warafy-preloader');
                var content = document.getElementById('warafy-content');
                if (preloader) {
                    preloader.style.opacity = '0';
                    preloader.style.pointerEvents = 'none';
                    preloader.style.transition = 'opacity 0.3s';
                    setTimeout(function() {
                        preloader.style.display = 'none';
                        if (preloader.parentNode) preloader.remove();
                    }, 300);
                }
                if (content) {
                    content.classList.add('loaded');
                }
            }, 3000);
        }
        
        // Emergency fallback - force content visible after 2 seconds regardless
        setTimeout(function() {
            var content = document.getElementById('warafy-content');
            var preloader = document.getElementById('warafy-preloader');
            if (content && content.style.opacity !== '1') {
                content.style.opacity = '1';
                content.style.pointerEvents = 'auto';
                content.style.visibility = 'visible';
                content.classList.add('loaded');
            }
            if (preloader) {
                preloader.style.display = 'none';
                preloader.remove();
            }
        }, 2000);
    })();
</script>
<noscript>
    <style>
        #warafy-preloader { display: none !important; }
        #warafy-content { opacity: 1 !important; pointer-events: auto !important; animation: none !important; }
    </style>
</noscript>

<div id="warafy-content" class="relative flex min-h-screen w-full flex-col"<?php if ($is_facebook_webview): ?> style="opacity:1!important;visibility:visible!important;pointer-events:auto!important;animation:none!important;"<?php endif; ?>>

<!-- Desktop Header -->
<header class="hidden lg:block sticky top-0 z-50 w-full bg-white dark:bg-background-dark">
<div class="border-b border-gray-200 dark:border-gray-700"></div>
<div class="container mx-auto px-6">
<div class="flex h-16 items-center justify-between">
<div class="flex items-center gap-8">
<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-3">
<img src="<?php echo esc_url( warafy_get_logo_url() ); ?>" alt="Ador Kini" class="h-10 w-auto object-contain" style="max-width: 150px; transform: scale(<?php echo esc_attr( warafy_get_logo_multiplier() ); ?>); transform-origin: left center;">
</a>
</div>
<div class="hidden flex-1 justify-center lg:flex">
<form role="search" method="get" class="relative w-full max-w-lg" action="<?php echo esc_url( home_url( '/' ) ); ?>">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500" data-icon="search"></span>
<input class="warafy-search-input form-input h-10 w-full rounded-lg border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800 pl-10 pr-4 text-sm font-medium placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-primary focus:ring-primary" placeholder="<?php echo __t('Search for products...'); ?>" type="search" name="s" value="<?php echo get_search_query(); ?>" autocomplete="off"/>
<input type="hidden" name="post_type" value="product" />
</form>
</div>
<div class="flex items-center justify-end gap-4">
<nav class="hidden items-center gap-6 lg:flex">
<a class="text-sm font-medium text-gray-700 hover:text-primary dark:text-gray-200 dark:hover:text-primary" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php echo __t('Shop'); ?></a>
<a class="text-sm font-medium text-gray-700 hover:text-primary dark:text-gray-200 dark:hover:text-primary" href="#"><?php echo __t("What's New"); ?></a>
<a class="text-sm font-medium text-gray-700 hover:text-primary dark:text-gray-200 dark:hover:text-primary" href="#"><?php echo __t('Help'); ?></a>
<?php 
// Language toggle using JavaScript instead of URL parameters
?>
<button type="button" class="warafy-language-toggle flex items-center gap-1 px-3 py-1.5 rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs font-bold text-gray-700 dark:text-gray-200 hover:border-primary hover:text-primary transition-all shadow-sm group">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-500 group-hover:text-primary dark:text-gray-400">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
    </svg>
    <span class="lang-text">En<>বাং</span>
</button>
</nav>
<div class="h-6 w-px bg-gray-200 dark:bg-gray-700 hidden lg:block"></div>
<style>
    .warafy-header-icon-btn .material-symbols-outlined {
        filter: brightness(0) saturate(100%) invert(100%) sepia(100%) saturate(0%) hue-rotate(288deg) brightness(102%) contrast(102%);
    }
</style>
<div class="flex items-center gap-2">
<a href="<?php echo esc_url( home_url( '/my-love' ) ); ?>" class="warafy-header-icon-btn relative flex h-10 w-10 cursor-pointer items-center justify-center overflow-hidden rounded-full bg-primary hover:bg-primary/90 shadow-lg">
    <span class="material-symbols-outlined" data-icon="favorite"></span>
    <span class="warafy-wishlist-count absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-white text-xs font-bold text-primary" style="display: none;">0</span>
</a>
<a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" class="warafy-header-icon-btn flex h-10 w-10 cursor-pointer items-center justify-center overflow-hidden rounded-full bg-primary hover:bg-primary/90 shadow-lg">
<span class="material-symbols-outlined" data-icon="person"></span>
</a>
<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="warafy-header-icon-btn relative flex h-10 w-10 cursor-pointer items-center justify-center overflow-hidden rounded-full bg-primary hover:bg-primary/90 shadow-lg">
<span class="material-symbols-outlined" data-icon="shopping_cart"></span>
<span class="cart-count absolute top-1 right-1 flex h-5 w-5 items-center justify-center rounded-full bg-white text-xs font-bold text-primary border-2 border-primary shadow-lg" style="z-index: 20; transform: none; line-height: 1;"><?php 
    $cart_count = WC()->cart->get_cart_contents_count();
    echo $cart_count > 0 ? $cart_count : '';
?></span>
</a>
</div>
</div>
</div>
</div>
</header>

<!-- Mobile Header -->
<?php if ( is_cart() ) : ?>
    <!-- Custom Mobile Cart Header -->
    <header class="lg:hidden sticky top-0 z-50 w-full border-b border-gray-200/50 dark:border-gray-700/50 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm">
        <div class="container mx-auto px-4">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center gap-2">
                    <a href="javascript:history.back()" class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full bg-transparent hover:bg-gray-200/50 dark:hover:bg-gray-700/50">
                        <span class="material-symbols-outlined text-gray-600 dark:text-gray-300" data-icon="arrow_back"></span>
                    </a>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white"><?php echo __t('My Cart'); ?></h1>
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full bg-transparent hover:bg-gray-200/50 dark:hover:bg-gray-700/50">
                        <span class="material-symbols-outlined text-gray-600 dark:text-gray-300" data-icon="search"></span>
                    </button>
                    <button type="button" class="warafy-language-toggle flex items-center gap-1 px-2 py-1 rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs font-bold text-gray-700 dark:text-gray-200 hover:border-primary hover:text-primary transition-all shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                        </svg>
                        <span class="lang-text">En<>বাং</span>
                    </button>
                    <button class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full bg-transparent hover:bg-gray-200/50 dark:hover:bg-gray-700/50">
                        <span class="material-symbols-outlined text-gray-600 dark:text-gray-300" data-icon="more_vert"></span>
                    </button>
                </div>
            </div>
        </div>
    </header>
<?php elseif ( is_page('my-love') ) : ?>
    <!-- Custom Mobile My Love Header -->
    <header class="lg:hidden sticky top-0 z-50 w-full border-b border-gray-200/50 dark:border-gray-700/50 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm">
        <div class="container mx-auto px-4">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center gap-2">
                    <a href="javascript:history.back()" class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full bg-transparent hover:bg-gray-200/50 dark:hover:bg-gray-700/50">
                        <span class="material-symbols-outlined text-gray-600 dark:text-gray-300" data-icon="arrow_back"></span>
                    </a>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white"><?php echo __t('My Love'); ?></h1>
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full bg-transparent hover:bg-gray-200/50 dark:hover:bg-gray-700/50">
                        <span class="material-symbols-outlined text-gray-600 dark:text-gray-300" data-icon="search"></span>
                    </button>
                    <button type="button" class="warafy-language-toggle flex items-center gap-1 px-2 py-1 rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs font-bold text-gray-700 dark:text-gray-200 hover:border-primary hover:text-primary transition-all shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                        </svg>
                        <span class="lang-text">En<>বাং</span>
                    </button>
                    <button class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full bg-transparent hover:bg-gray-200/50 dark:hover:bg-gray-700/50">
                        <span class="material-symbols-outlined text-gray-600 dark:text-gray-300" data-icon="more_vert"></span>
                    </button>
                </div>
            </div>
        </div>
    </header>
<?php else : ?>
    <!-- Default Mobile Header -->
    <header class="lg:hidden sticky top-0 z-20 flex flex-col bg-white dark:bg-background-dark">
        <div class="border-b border-solid border-slate-200 dark:border-slate-800"></div>
        <div class="relative flex items-center justify-between px-4 py-3 w-full mb-2">
            <!-- Logo -->
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex items-center">
                <div class="h-14 w-auto">
                    <img src="<?php echo esc_url( warafy_get_logo_url() ); ?>" alt="Ador Kini" class="h-full w-full object-contain" style="max-width: 200px; transform: scale(<?php echo esc_attr( warafy_get_logo_multiplier() ); ?>); transform-origin: center center;">
                </div>
            </a>
            
            <!-- Cart Icon - Hidden on mobile since it's in sticky menu -->
            <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="lg:hidden relative flex h-10 w-10 cursor-pointer items-center justify-center overflow-hidden rounded-full bg-transparent hover:bg-gray-100 dark:hover:bg-gray-800" style="display: none !important;">
                <span class="material-symbols-outlined text-gray-600 dark:text-gray-300" data-icon="shopping_cart"></span>
                <span class="cart-count absolute top-1 right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white border-2 border-white dark:border-gray-800 shadow-lg" style="z-index: 20; transform: none; line-height: 1;"><?php 
                    $cart_count = WC()->cart->get_cart_contents_count();
                    echo $cart_count > 0 ? $cart_count : '';
                ?></span>
            </a>
            <button type="button" class="warafy-language-toggle ml-auto lg:hidden flex items-center gap-1 px-2 py-1 rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs font-bold text-gray-700 dark:text-gray-200 hover:border-primary hover:text-primary transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                </svg>
                <span class="lang-text">En<>বাং</span>
            </button>
            <!-- Cart Icon - Desktop only -->
            <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="hidden lg:flex relative h-10 w-10 cursor-pointer items-center justify-center overflow-hidden rounded-full bg-transparent hover:bg-gray-100 dark:hover:bg-gray-800">
                <span class="material-symbols-outlined text-gray-600 dark:text-gray-300" data-icon="shopping_cart"></span>
                <span class="cart-count absolute top-1 right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white border-2 border-white dark:border-gray-800 shadow-lg" style="z-index: 20; transform: none; line-height: 1;"><?php 
                    $cart_count = WC()->cart->get_cart_contents_count();
                    echo $cart_count > 0 ? $cart_count : '';
                ?></span>
            </a>
        </div>

        <!-- Search Bar -->
        <div class="px-4 pb-3 w-full">
            <form role="search" method="get" class="relative w-full" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" data-icon="search"></span>
                <input class="warafy-search-input w-full rounded-lg border-gray-200 bg-gray-50 pl-10 pr-4 py-2 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary dark:bg-gray-800 dark:border-gray-700 dark:text-white" placeholder="<?php echo __t('Search for products...'); ?>" type="search" name="s" value="<?php echo get_search_query(); ?>" autocomplete="off"/>
                <input type="hidden" name="post_type" value="product" />
            </form>
        </div>
    </header>
<?php endif; ?>

