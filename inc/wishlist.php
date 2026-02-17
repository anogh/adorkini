<?php
/**
 * Wishlist Functionality
 * Wishlist scripts, shortcodes, AJAX handlers
 */

if (!defined('ABSPATH')) {
    exit;
}

function warafy_wishlist_scripts() {
    ?>
    <script>
    window.getWishlist = function() {
        const wishlist = localStorage.getItem('warafy_wishlist');
        return wishlist ? JSON.parse(wishlist) : [];
    };

    window.updateWishlistCount = function() {
        const wishlist = window.getWishlist();
        const count = wishlist.length;
        document.querySelectorAll('.warafy-wishlist-count').forEach(el => {
            el.textContent = count;
            el.style.display = count > 0 ? 'flex' : 'none';
        });
        
        document.querySelectorAll('.warafy-wishlist-btn').forEach(btn => {
            if (wishlist.includes(btn.dataset.productId)) {
                btn.classList.add('active');
                btn.classList.add('bg-green-500', 'text-white', 'border-green-500');
                btn.classList.remove('border-gray-300', 'text-gray-600');
                
                const btnText = btn.querySelector('.btn-text');
                if(btnText) {
                    btnText.textContent = 'Loved!';
                }
                
                if(btn.querySelector('.material-symbols-outlined')) {
                    btn.querySelector('.material-symbols-outlined').dataset.icon = 'favorite';
                    btn.querySelector('.material-symbols-outlined').classList.add('text-white');
                }
            }
        });
    };

    window.toggleWishlist = function(productId, btn) {
        let wishlist = window.getWishlist();
        const index = wishlist.indexOf(productId);
        
        if (index > -1) {
            wishlist.splice(index, 1);
            btn.classList.remove('active');
            btn.classList.remove('bg-green-500', 'text-white', 'border-green-500');
            btn.classList.add('border-gray-300', 'text-gray-600');
            
            const btnText = btn.querySelector('.btn-text');
            if(btnText) {
                btnText.textContent = 'Loved it? Add to love.';
            }
            
            if(btn.querySelector('.material-symbols-outlined')) {
                btn.querySelector('.material-symbols-outlined').dataset.icon = 'favorite_border';
                btn.querySelector('.material-symbols-outlined').classList.remove('text-white');
            }
        } else {
            wishlist.push(productId);
            btn.classList.add('active');
            btn.classList.add('bg-green-500', 'text-white', 'border-green-500');
            btn.classList.remove('border-gray-300', 'text-gray-600');
            
            const btnText = btn.querySelector('.btn-text');
            if(btnText) {
                btnText.textContent = 'Loved!';
            }
            
            if(btn.querySelector('.material-symbols-outlined')) {
                btn.querySelector('.material-symbols-outlined').dataset.icon = 'favorite';
                btn.querySelector('.material-symbols-outlined').classList.add('text-white');
            }
        }
        
        localStorage.setItem('warafy_wishlist', JSON.stringify(wishlist));
        window.updateWishlistCount();
        
        if (document.body.classList.contains('page-template-page-my-love') || window.location.pathname.includes('/my-love')) {
            location.reload();
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        window.updateWishlistCount();
        
        document.body.addEventListener('click', function(e) {
            if (e.target.closest('.warafy-wishlist-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.warafy-wishlist-btn');
                const productId = btn.dataset.productId;
                window.toggleWishlist(productId, btn);
            }
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'warafy_wishlist_scripts');

// Shortcode to display wishlist items
function warafy_wishlist_shortcode($atts) {
    $atts = shortcode_atts(array(
        'view' => 'desktop'
    ), $atts);
    
    $container_id = $atts['view'] === 'mobile' ? 'warafy-wishlist-container-mobile' : 'warafy-wishlist-container-desktop';
    
    ob_start();
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('<?php echo $container_id; ?>');
        
        if (!container) return;
        
        container.innerHTML = '';
        
        const wishlist = window.getWishlist();
        
        if (wishlist.length === 0) {
            container.innerHTML = '<div class="empty-cart-container bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 lg:p-12 text-center max-w-2xl mx-auto mt-12"><div class="mb-6"><span class="material-symbols-outlined text-8xl lg:text-9xl text-gray-300" style="font-size: 6rem;" data-icon="favorite_border"></span></div><h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-3"><?php echo __t('Your love list is empty'); ?></h2><p class="text-gray-600 dark:text-gray-400 mb-8"><?php echo __t("You haven\'t added any items to your love list yet."); ?></p><a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="inline-flex items-center gap-2 bg-primary text-white px-8 py-4 rounded-full font-semibold hover:bg-primary/90 transition-all shadow-lg"><span class="material-symbols-outlined" data-icon="storefront"></span><?php echo __t('Start Shopping'); ?></a></div>';
            return;
        }

        container.innerHTML = '<div class="text-center py-8"><div class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full mb-4"><span class="material-symbols-outlined text-2xl text-blue-600 animate-spin" data-icon="refresh"></span></div><p class="text-gray-500 dark:text-gray-400">Loading ' + wishlist.length + ' loved items...</p></div>';

        const data = new FormData();
        data.append('action', 'warafy_get_wishlist_products');
        data.append('product_ids', JSON.stringify(wishlist));
        data.append('view', '<?php echo $atts['view']; ?>');

        fetch('<?php echo admin_url('admin-ajax.php'); ?>?v=<?php echo time(); ?>', {
            method: 'POST',
            body: data
        })
        .then(response => response.text())
        .then(html => {
            if (html.trim() === '') {
                container.innerHTML = '<div class="text-center py-8"><p class="text-orange-500">No products received from server.</p></div>';
            } else {
                container.innerHTML = html;
                window.updateWishlistCount();
            }
        })
        .catch(error => {
            container.innerHTML = '<div class="text-center py-8"><p class="text-red-500">Error loading wishlist items: ' + error.message + '</p></div>';
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('warafy_wishlist', 'warafy_wishlist_shortcode');

// AJAX Handler for fetching wishlist products
add_action('wp_ajax_warafy_get_wishlist_products', 'warafy_get_wishlist_products');
add_action('wp_ajax_nopriv_warafy_get_wishlist_products', 'warafy_get_wishlist_products');

function warafy_get_wishlist_products() {
    header('Content-Type: text/html; charset=utf-8');
    
    $ids = json_decode(stripslashes($_POST['product_ids']));
    $view = isset($_POST['view']) ? $_POST['view'] : 'desktop';
    
    if (empty($ids)) {
        echo '<div class="empty-cart-container bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 lg:p-12 text-center max-w-2xl mx-auto mt-12">
                <div class="mb-6">
                    <span class="material-symbols-outlined text-8xl lg:text-9xl text-gray-300" style="font-size: 6rem;" data-icon="favorite_border"></span>
                </div>
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-3">' . __t('Your love list is empty') . '</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-8">' . __t("You haven\'t added any items to your love list yet.") . '</p>
                <a href="' . get_permalink(wc_get_page_id('shop')) . '" 
                   class="inline-flex items-center gap-2 bg-primary text-white px-8 py-4 rounded-full font-semibold hover:bg-primary/90 transition-all shadow-lg">
                    <span class="material-symbols-outlined" data-icon="storefront"></span>
                    ' . __t('Start Shopping') . '
                </a>
            </div>';
        wp_die();
    }

    $args = array(
        'post_type' => 'product',
        'post__in' => $ids,
        'posts_per_page' => -1
    );

    $loop = new WP_Query($args);

    if ($loop->have_posts()) {
        if ($view === 'desktop') {
            echo '<div class="mb-6 hidden lg:block">
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">My Love List</h1>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">You have ' . count($ids) . ' item' . (count($ids) > 1 ? 's' : '') . ' in your love list.</p>
                  </div>';
            echo '<div class="flex flex-col gap-4 lg:gap-8 lg:flex-row lg:items-start">';
            echo '<div class="flex-1">';
        }

        if ($view === 'desktop') {
            echo '<div class="hidden lg:block rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-background-dark">
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">';
        } else {
            echo '<div class="lg:hidden bg-white dark:bg-background-dark">
                    <div class="grid grid-cols-2 gap-4 p-4 mobile-grid-2">';
        }
        
        while ($loop->have_posts()) : $loop->the_post();
            global $product;
            
            if ($view === 'desktop') {
                ?>
                <div class="flex flex-col gap-6 p-6 sm:flex-row sm:items-center">
                    <div class="w-full sm:w-32 sm:flex-shrink-0">
                        <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-lg overflow-hidden">
                            <a href="<?php the_permalink(); ?>">
                                <?php echo $product->get_image('woocommerce_thumbnail', array('class' => 'w-full h-full object-cover object-center')); ?>
                            </a>
                        </div>
                    </div>
                    <div class="flex flex-1 flex-col justify-between gap-4 sm:flex-row sm:items-center">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                <a href="<?php the_permalink(); ?>" class="hover:text-primary transition-colors"><?php the_title(); ?></a>
                            </h3>
                            <p class="mt-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                                <?php echo $product->is_in_stock() ? 'In Stock' : 'Out of Stock'; ?>
                            </p>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <p class="w-20 text-right text-lg font-semibold text-gray-900 dark:text-white">
                                <?php echo $product->get_price_html(); ?>
                            </p>
                            <div class="flex flex-col gap-2">
                                <a href="?add-to-cart=<?php echo $product->get_id(); ?>" 
                                   class="flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors">
                                    <span class="material-symbols-outlined text-lg" data-icon="add_shopping_cart"></span>
                                    Add to Cart
                                </a>
                                <button type="button" 
                                        class="warafy-wishlist-btn flex items-center justify-center gap-2 px-3 py-2 border border-red-300 text-red-600 rounded-lg font-medium hover:bg-red-50 transition-colors" 
                                        data-product-id="<?php echo $product->get_id(); ?>">
                                    <span class="material-symbols-outlined text-lg" data-icon="close"></span>
                                    Remove from Loved List
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            } else {
                ?>
                <div class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-background-dark shadow-sm">
                    <div class="relative">
                        <a href="<?php the_permalink(); ?>" class="block w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-lg" style='background-image: url("<?php echo get_the_post_thumbnail_url($product->get_id(), 'woocommerce_thumbnail'); ?>");'></a>
                        <button type="button" 
                                class="warafy-wishlist-btn absolute top-2 right-2 w-8 h-8 flex items-center justify-center rounded-full bg-white/90 text-red-500 hover:bg-red-50 shadow-sm"
                                data-product-id="<?php echo $product->get_id(); ?>">
                            <span class="material-symbols-outlined text-lg" data-icon="close"></span>
                        </button>
                    </div>
                    <div class="flex flex-col flex-1 justify-between gap-4">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">
                                <a href="<?php the_permalink(); ?>" class="hover:text-primary transition-colors line-clamp-1"><?php the_title(); ?></a>
                            </h3>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo $product->get_price_html(); ?></p>
                        </div>
                        <div class="flex gap-2">
                            <a href="?add-to-cart=<?php echo $product->get_id(); ?>" 
                               class="add-to-cart-btn flex-1 flex items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary/10 text-primary text-sm font-bold hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 transition-colors">
                                <span class="material-symbols-outlined text-sm mr-2" data-icon="add_shopping_cart"></span>
                                <span class="truncate">Add</span>
                            </a>
                            <button type="button" 
                                    class="warafy-wishlist-btn flex-none w-10 h-10 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/30 transition-colors"
                                    data-product-id="<?php echo $product->get_id(); ?>">
                                <span class="material-symbols-outlined text-lg" data-icon="delete"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <?php
            }
        endwhile;
        
        echo '</div></div>';
        
        if ($view === 'desktop') {
            echo '</div>';
            echo '<aside class="hidden lg:block w-full lg:w-80 lg:flex-shrink-0">
                    <div class="sticky top-24 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-background-dark">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Love List Summary</h3>
                        <div class="mt-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-600 dark:text-gray-300">Total Items</p>
                                <p class="font-semibold text-gray-900 dark:text-white">' . count($ids) . '</p>
                            </div>
                        </div>
                        <div class="my-6 h-px w-full bg-gray-200 dark:bg-gray-700"></div>
                        <a href="' . get_permalink(wc_get_page_id('shop')) . '" 
                           class="mt-6 flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-base font-bold shadow-lg hover:bg-primary/90">
                            <span class="material-symbols-outlined mr-2" data-icon="storefront"></span>
                            Continue Shopping
                        </a>
                        <p class="mt-4 text-center text-xs text-gray-500 dark:text-gray-400">Add items from your love list to cart</p>
                    </div>
                  </aside>';
            echo '</div>';
        }
        
        if ($view === 'mobile') {
            echo '<div class="mt-4 text-center lg:hidden">
                    <a class="text-sm font-medium text-primary hover:underline" href="' . get_permalink(wc_get_page_id('shop')) . '">Continue Shopping</a>
                  </div>';
        }
        
    } else {
        echo '<p class="text-center py-8 text-gray-500">Products not found.</p>';
    }
    wp_reset_postdata();
    wp_die();
}
