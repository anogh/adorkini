<?php
/**
 * Template Name: My Love (Wishlist)
 */

get_header(); ?>

<!-- Desktop Content -->
<main class="hidden lg:block flex-grow bg-white dark:bg-background-dark lg:bg-background-light">
    <div class="container mx-auto px-4 lg:px-6 py-4 lg:py-8">
        <div class="woocommerce">
            <div class="bg-white dark:bg-background-dark rounded-xl border border-gray-200 dark:border-gray-700">
                <!-- Wishlist Content -->
                <div id="warafy-wishlist-container-desktop" class="p-6 lg:p-8">
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full mb-4">
                            <span class="material-symbols-outlined text-3xl text-gray-400" data-icon="favorite_border"></span>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-lg mb-4">Loading your loved items...</p>
                    </div>
                </div>
                
                <?php echo do_shortcode('[warafy_wishlist view="desktop"]'); ?>
            </div>
        </div>
    </div>
</main>

<!-- Mobile Content -->
<main class="lg:hidden flex-grow bg-white dark:bg-background-dark" style="display: block !important; visibility: visible !important; opacity: 1 !important; position: relative !important; z-index: 1 !important;">
    <div class="container mx-auto px-4 py-4" style="display: block !important; visibility: visible !important; opacity: 1 !important; position: relative !important; z-index: 1 !important;">
        <style>
            /* Override any CSS that might be hiding mobile content */
            @media (max-width: 1023px) {
                main.lg\:hidden,
                main.lg\:hidden > div,
                main.lg\:hidden .woocommerce,
                main.lg\:hidden .container {
                    display: block !important;
                    visibility: visible !important;
                    opacity: 1 !important;
                    position: relative !important;
                    z-index: 1 !important;
                    width: 100% !important;
                    height: auto !important;
                    max-width: none !important;
                    min-width: auto !important;
                    overflow: visible !important;
                    margin: 0 !important;
                    padding: inherit !important;
                }
                /* Ensure mobile footer is visible */
                footer.lg\:hidden {
                    display: block !important;
                    visibility: visible !important;
                    opacity: 1 !important;
                    position: relative !important;
                    z-index: 40 !important;
                }
            }
        </style>
        <div class="woocommerce">
            <!-- Wishlist Content -->
            <div id="warafy-wishlist-container-mobile" class="bg-white dark:bg-background-dark">
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-full mb-3">
                        <span class="material-symbols-outlined text-2xl text-gray-400" data-icon="favorite_border"></span>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-3">Loading your loved items...</p>
                </div>
            </div>
            
            <?php echo do_shortcode('[warafy_wishlist view="mobile"]'); ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
