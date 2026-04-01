<?php
/*
Template Name: Top Ranking Page
*/

get_header();

// Fetch and process ranked products once for both desktop and mobile views
$products = [];
$full_rankings = warafy_get_ranked_products('full', 100);
if (!empty($full_rankings)) {
    $args = array(
        'post_type' => 'product',
        'post__in' => $full_rankings,
        'orderby' => 'post__in'
    );
    $loop = new WP_Query($args);
    if ($loop->have_posts()) {
        $rank = 1;
        while ($loop->have_posts()) : $loop->the_post();
            global $product;
            $products[] = [
                'rank' => $rank,
                'product' => $product,
                'title' => get_the_title(),
                'permalink' => get_permalink(),
                'thumbnail' => get_the_post_thumbnail_url($product->get_id(), 'woocommerce_thumbnail'),
                'price_html' => $product->get_price_html()
            ];
            $rank++;
        endwhile;
        wp_reset_postdata();
    }
}
?>

<main class="flex-grow pb-24 lg:pb-0">
    
    <!-- Desktop Content -->
    <div class="hidden lg:block">
        <div class="container mx-auto px-6 py-8">
            <!-- Page Header -->
            <section class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Top 100 Products Ranking</h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Discover the best-selling products ranked by popularity</p>
                    </div>
                    <a href="<?php echo home_url('/'); ?>" class="flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                        <span class="material-symbols-outlined text-sm" data-icon="home"></span>
                        <span>Back to Home</span>
                    </a>
                </div>
            </section>

            <!-- Ranking Grid -->
            <section class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php
                if (!empty($products)) {
                        // Display all products in grid layout
                        foreach ($products as $item) {
                            $rank_class = $item['rank'] == 1 ? 'gold' : ($item['rank'] == 2 ? 'silver' : ($item['rank'] == 3 ? 'bronze' : 'default'));
                            $border_class = $item['rank'] == 1 ? 'border-yellow-400' : ($item['rank'] == 2 ? 'border-gray-400' : ($item['rank'] == 3 ? 'border-orange-600' : 'border-gray-200'));
                            $bg_class = $item['rank'] == 1 ? 'bg-gradient-to-r from-yellow-50 to-transparent' : ($item['rank'] == 2 ? 'bg-gradient-to-r from-gray-50 to-transparent' : ($item['rank'] == 3 ? 'bg-gradient-to-r from-orange-50 to-transparent' : 'bg-white'));
                            ?>
                            <div class="flex items-center gap-4 p-4 rounded-xl border-2 <?php echo $border_class; ?> <?php echo $bg_class; ?> dark:border-gray-700 dark:bg-background-dark hover:shadow-lg transition-all ranking-item relative overflow-hidden">
                                <?php if ($item['rank'] <= 3): ?>
                                    <div class="absolute top-0 right-0 w-16 h-16 opacity-10">
                                        <span class="material-symbols-outlined text-8xl <?php echo $item['rank'] == 1 ? 'text-yellow-500' : ($item['rank'] == 2 ? 'text-gray-400' : 'text-orange-600'); ?>" data-icon="emoji_events"></span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Rank Number -->
                                <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full font-bold text-lg ranking-badge <?php echo $rank_class; ?>">
                                    <?php echo $item['rank']; ?>
                                </div>
                                
                                <!-- Product Image -->
                                <a href="<?php echo $item['permalink']; ?>" class="flex-shrink-0 w-16 h-16 bg-center bg-no-repeat bg-cover rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600" style='background-image: url("<?php echo $item['thumbnail']; ?>");'></a>
                                
                                <!-- Product Info -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">
                                        <a href="<?php echo $item['permalink']; ?>" class="hover:text-primary transition-colors line-clamp-1"><?php echo $item['title']; ?></a>
                                    </h3>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo $item['price_html']; ?></p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1"><?php echo $item['product']->is_in_stock() ? 'In Stock' : 'Out of Stock'; ?></p>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex items-start gap-2 mt-3">
                                        <div class="flex flex-col gap-2">
                                            <a href="<?php echo esc_url( wc_get_checkout_url() . '?add-to-cart=' . $item['product']->get_id() ); ?>" class="flex items-center gap-2 px-3 py-2 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary/90 transition-colors justify-center" title="Buy Now">
                                                <span class="material-symbols-outlined text-sm" data-icon="bolt"></span>
                                                <span>Buy Now</span>
                                            </a>
                                            <button class="add-to-cart-btn flex items-center gap-2 px-3 py-2 bg-primary/10 text-primary text-sm font-bold rounded-lg hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 transition-colors justify-center" data-product-id="<?php echo $item['product']->get_id(); ?>" title="Add to Cart">
                                                <span class="material-symbols-outlined text-sm add-icon" data-icon="add_shopping_cart"></span>
                                                <span class="add-text">Add to Cart</span>
                                                <span class="material-symbols-outlined text-sm added-icon hidden" data-icon="check"></span>
                                                <span class="added-text hidden">Added</span>
                                            </button>
                                        </div>
                                        <button class="warafy-wishlist-btn flex-none w-10 h-10 flex items-center justify-center rounded-lg bg-primary/10 text-primary hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 transition-colors" data-product-id="<?php echo $item['product']->get_id(); ?>" title="Add to Love">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        
                } else {
                    echo '<div class="col-span-full"><p class="text-gray-500 dark:text-gray-400 text-center py-12">No ranked products found. Please select products in the <a href="' . admin_url('themes.php?page=warafy-product-ranking') . '" class="text-primary hover:underline">Product Ranking admin page</a>.</p></div>';
                }
                ?>
            </section>
        </div>
    </div>

    <!-- Mobile Content -->
    <div class="block lg:hidden">
        <div class="px-4 py-4">
            <!-- Page Header -->
            <section class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Top 100 Ranking</h1>
                    <a href="<?php echo home_url('/'); ?>" class="flex items-center gap-1 p-2 border border-gray-300 text-gray-600 rounded-lg">
                        <span class="material-symbols-outlined text-sm" data-icon="home"></span>
                    </a>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Best-selling products ranked by popularity</p>
            </section>

            <!-- Ranking List -->
            <section class="space-y-3">
                <?php
                if (!empty($products)) {
                        // Display all products in single column for mobile
                        foreach ($products as $item) {
                            $rank_class = $item['rank'] == 1 ? 'gold' : ($item['rank'] == 2 ? 'silver' : ($item['rank'] == 3 ? 'bronze' : 'default'));
                            $border_class = $item['rank'] == 1 ? 'border-yellow-400' : ($item['rank'] == 2 ? 'border-gray-400' : ($item['rank'] == 3 ? 'border-orange-600' : 'border-gray-200'));
                            $bg_class = $item['rank'] == 1 ? 'bg-gradient-to-r from-yellow-50 to-transparent' : ($item['rank'] == 2 ? 'bg-gradient-to-r from-gray-50 to-transparent' : ($item['rank'] == 3 ? 'bg-gradient-to-r from-orange-50 to-transparent' : 'bg-white'));
                            ?>
                            <div class="flex items-center gap-3 p-3 rounded-xl border-2 <?php echo $border_class; ?> <?php echo $bg_class; ?> dark:border-gray-700 dark:bg-background-dark hover:shadow-lg transition-all ranking-item relative overflow-hidden">
                                <?php if ($item['rank'] <= 3): ?>
                                    <div class="absolute top-0 right-0 w-12 h-12 opacity-10">
                                        <span class="material-symbols-outlined text-6xl <?php echo $item['rank'] == 1 ? 'text-yellow-500' : ($item['rank'] == 2 ? 'text-gray-400' : 'text-orange-600'); ?>" data-icon="emoji_events"></span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Rank Number -->
                                <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full font-bold text-sm ranking-badge <?php echo $rank_class; ?>">
                                    <?php echo $item['rank']; ?>
                                </div>
                                
                                <!-- Product Image -->
                                <a href="<?php echo $item['permalink']; ?>" class="flex-shrink-0 w-12 h-12 bg-center bg-no-repeat bg-cover rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600" style='background-image: url("<?php echo $item['thumbnail']; ?>");'></a>
                                
                                <!-- Product Info -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                                        <a href="<?php echo $item['permalink']; ?>" class="hover:text-primary transition-colors line-clamp-1"><?php echo $item['title']; ?></a>
                                    </h3>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white"><?php echo $item['price_html']; ?></p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1"><?php echo $item['product']->is_in_stock() ? 'In Stock' : 'Out of Stock'; ?></p>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex items-start gap-2 mt-2">
                                        <div class="flex flex-col gap-2">
                                            <a href="<?php echo esc_url( wc_get_checkout_url() . '?add-to-cart=' . $item['product']->get_id() ); ?>" class="flex items-center gap-1.5 px-3 py-2 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary/90 transition-colors justify-center" title="Buy Now">
                                                <span class="material-symbols-outlined text-sm" data-icon="bolt"></span>
                                                <span>Buy Now</span>
                                            </a>
                                            <button class="add-to-cart-btn flex items-center gap-1.5 px-3 py-2 bg-primary/10 text-primary text-sm font-bold rounded-lg hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 transition-colors justify-center" data-product-id="<?php echo $item['product']->get_id(); ?>" title="Add to Cart">
                                                <span class="material-symbols-outlined text-sm add-icon" data-icon="add_shopping_cart"></span>
                                                <span class="add-text">Add</span>
                                                <span class="material-symbols-outlined text-sm added-icon hidden" data-icon="check"></span>
                                                <span class="added-text hidden">Added</span>
                                            </button>
                                        </div>
                                        <button class="warafy-wishlist-btn flex-none w-10 h-10 flex items-center justify-center rounded-lg bg-primary/10 text-primary hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 transition-colors" data-product-id="<?php echo $item['product']->get_id(); ?>" title="Add to Love">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        
                } else {
                    echo '<div class="text-center py-8"><p class="text-gray-500 text-sm">No ranked products found.</p></div>';
                }
                ?>
            </section>
        </div>
    </div>

</main>

<?php get_footer(); ?>
