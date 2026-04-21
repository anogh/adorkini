<?php
/**
 * Single Product
 *
 * @version 1.6.4
 */
get_header(); ?>

<main class="flex-grow pb-40 lg:pb-0">
    <?php
    while ( have_posts() ) :
        the_post();
        global $product;
        
        $all_image_ids = [];
        $post_thumbnail_id = $product->get_image_id();
        if ($post_thumbnail_id) {
            $all_image_ids[] = $post_thumbnail_id;
        }
        $gallery_image_ids = $product->get_gallery_image_ids();
        if (!empty($gallery_image_ids)) {
            $all_image_ids = array_merge($all_image_ids, $gallery_image_ids);
        }
        // Remove duplicates if the featured image is also in the gallery
        $all_image_ids = array_unique($all_image_ids);

        $regular_price = (float) $product->get_regular_price();
        $sale_price = (float) $product->get_sale_price();
        $has_discount = $product->is_on_sale() && $regular_price > 0 && $sale_price > 0 && $sale_price < $regular_price;
        $save_percentage = $has_discount ? round((($regular_price - $sale_price) / $regular_price) * 100) : 0;
        $min_purchase_quantity = $product->get_min_purchase_quantity();
        $max_purchase_quantity = $product->get_max_purchase_quantity();
        ?>
        
        <!-- Desktop Content -->
        <div class="hidden lg:block container mx-auto px-6 py-8">
            <!-- Breadcrumbs -->
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                <?php woocommerce_breadcrumb(); ?>
            </nav>

            <section class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                <!-- Image Gallery -->
                <div class="flex flex-col gap-4">
                    <div id="desktop-product-carousel-main" class="relative group w-full aspect-square overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                        <?php if (!empty($all_image_ids)) : ?>
                            <?php foreach ($all_image_ids as $index => $attachment_id) :
                                $image_url_full = wp_get_attachment_image_url($attachment_id, 'full');
                            ?>
                                <div class="desktop-carousel-item absolute inset-0 w-full h-full bg-center bg-no-repeat bg-contain transition-opacity duration-300 ease-in-out cursor-zoom-in <?php echo $index === 0 ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'; ?>" style="background-image: url('<?php echo esc_url($image_url_full); ?>');" data-image-index="<?php echo $index; ?>" data-image-url="<?php echo esc_url($image_url_full); ?>" data-image-alt="<?php echo esc_attr( $product->get_name() ); ?>"></div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-500">No Image</div>
                        <?php endif; ?>

                        <!-- Navigation Arrows -->
                        <?php if (count($all_image_ids) > 1) : ?>
                            <button class="desktop-carousel-prev absolute left-4 top-1/2 -translate-y-1/2 bg-white/70 dark:bg-gray-800/70 p-2 rounded-full shadow-md text-gray-800 dark:text-white hover:bg-white dark:hover:bg-gray-700 transition-colors duration-200 hidden group-hover:block z-10">
                                <span class="material-symbols-outlined" data-icon="chevron_left"></span>
                            </button>
                            <button class="desktop-carousel-next absolute right-4 top-1/2 -translate-y-1/2 bg-white/70 dark:bg-gray-800/70 p-2 rounded-full shadow-md text-gray-800 dark:text-white hover:bg-white dark:hover:bg-gray-700 transition-colors duration-200 hidden group-hover:block z-10">
                                <span class="material-symbols-outlined" data-icon="chevron_right"></span>
                            </button>
                        <?php endif; ?>
                    </div>
                    <!-- Thumbnail Gallery -->
                    <?php if (count($all_image_ids) > 1) : ?>
                        <div id="desktop-product-carousel-thumbs" class="grid grid-cols-5 gap-4">
                            <?php foreach ($all_image_ids as $index => $attachment_id) :
                                $thumbnail_url = wp_get_attachment_image_url($attachment_id, 'thumbnail');
                            ?>
                                <div class="desktop-carousel-thumb w-full aspect-square bg-center bg-no-repeat bg-cover rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary cursor-pointer <?php echo $index === 0 ? 'border-primary' : ''; ?>" style="background-image: url('<?php echo esc_url($thumbnail_url); ?>');" data-image-index="<?php echo $index; ?>"></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                    <!-- Video Gallery Section (Desktop) -->
                    <?php 
                    $youtube_url = get_post_meta($product->get_id(), '_warafy_youtube_url', true);
                    $youtube_embed_url = warafy_get_youtube_embed_url($youtube_url);
                    if ($youtube_embed_url) : 
                    ?>
                    <div class="mt-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                            <span class="material-symbols-outlined text-red-500" data-icon="play_circle"></span>
                            <?php echo __t('Video Gallery'); ?>
                        </h3>
                        <div class="warafy-video-container rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm">
                            <div class="warafy-video-wrapper">
                                <iframe 
                                    src="<?php echo esc_url($youtube_embed_url); ?>" 
                                    title="Product Video"
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                    allowfullscreen
                                    loading="lazy"
                                    class="warafy-video-iframe"
                                ></iframe>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                <!-- Product Details -->
                <div class="flex flex-col gap-6">
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white"><?php the_title(); ?></h1>
                        <div class="mt-2 flex items-center gap-2">
                            <div class="flex items-center gap-1 text-yellow-500">
                                <?php 
                                $rating = $product->get_average_rating();
                                for ($i = 0; $i < 5; $i++) {
                                    if ($i < $rating) {
                                        echo '<span class="material-symbols-outlined filled text-base" style="font-variation-settings: \'FILL\' 1;" data-icon="star"></span>';
                                    } else {
                                        echo '<span class="material-symbols-outlined text-base" data-icon="star"></span>';
                                    }
                                }
                                ?>
                            </div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">(<?php echo $product->get_review_count(); ?> reviews)</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-end gap-2 flex-wrap">
                            <?php if ($has_discount) : ?>
                                <p class="text-3xl font-extrabold text-amber-500 dark:text-amber-400"><?php echo wc_price($sale_price); ?></p>
                                <p class="text-lg font-medium text-red-500 line-through decoration-2"><?php echo wc_price($regular_price); ?></p>
                            <?php else : ?>
                                <p class="text-3xl font-extrabold text-gray-900 dark:text-white"><?php echo $product->get_price_html(); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php if ($has_discount) : ?>
                            <span class="inline-flex items-center rounded-2xl bg-emerald-600 px-4 py-2 text-lg font-bold text-white shadow-sm">
                                <?php echo esc_html(sprintf(__('Save %d%%', 'woocommerce'), $save_percentage)); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="w-full h-px bg-gray-200 dark:bg-gray-700"></div>

                    <!-- Custom Add to Cart Form -->
                    <form class="cart flex flex-col gap-4" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
                        
                        <!-- Quantity + Buttons Row -->
                        <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                            <?php if ( !$product->is_sold_individually() ) : ?>
                                <div class="single-product-qty inline-flex h-12 items-center overflow-hidden rounded-xl border border-[#e6b400] bg-white dark:border-[#e6b400] dark:bg-gray-900 flex-shrink-0">
                                    <button type="button" class="single-qty-btn single-qty-minus flex h-12 w-12 items-center justify-center text-2xl font-medium text-gray-500 transition-colors hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800" aria-label="Decrease quantity">−</button>
                                    <input
                                        type="number"
                                        id="quantity"
                                        name="quantity"
                                        value="<?php echo isset( $_POST['quantity'] ) ? esc_attr( wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) ) : esc_attr( $min_purchase_quantity ); ?>"
                                        min="<?php echo esc_attr( apply_filters( 'woocommerce_quantity_input_min', $min_purchase_quantity, $product ) ); ?>"
                                        max="<?php echo esc_attr( apply_filters( 'woocommerce_quantity_input_max', $max_purchase_quantity, $product ) ); ?>"
                                        step="1"
                                        inputmode="numeric"
                                        class="single-qty-input h-12 w-12 border-0 bg-transparent text-center text-base font-medium text-gray-900 focus:outline-none focus:ring-0 dark:text-white appearance-none [-moz-appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                    >
                                    <button type="button" class="single-qty-btn single-qty-plus flex h-12 w-12 items-center justify-center text-2xl font-medium text-gray-500 transition-colors hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800" aria-label="Increase quantity">+</button>
                                </div>
                            <?php endif; ?>
                            <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="flex flex-1 min-w-[84px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-slate-900 text-white text-base font-semibold shadow-md hover:bg-slate-800">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                                    <line x1="9" y1="13" x2="15" y2="13"></line>
                                </svg>
                                <span class="truncate"><?php echo __t('Add to Cart'); ?></span>
                            </button>
                        </div>
                        
                        <!-- Buy Now Button -->
                        <button type="button" class="buy-now-btn flex items-center justify-center gap-2 rounded-lg h-12 px-6 bg-[#F5A623] text-white hover:bg-[#E8960E] transition-colors font-semibold shadow-lg" data-product-id="<?php echo $product->get_id(); ?>" data-checkout-url="<?php echo esc_url( wc_get_checkout_url() ); ?>" title="<?php echo __t('Order Now'); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                            <span class="btn-text"><?php echo __t('Order Now'); ?></span>
                        </button>
                    </form>

                    <div class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        <?php the_excerpt(); ?>
                    </div>
                    


                    <!-- Reviews Section -->
                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white"><?php echo __t('Customer Reviews'); ?></h3>
                            <div class="flex items-center gap-2">
                                <div class="flex text-yellow-500">
                                    <?php
                                    $avg_rating = warafy_get_average_rating($product->get_id());
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $avg_rating) {
                                            echo '<span class="material-symbols-outlined filled text-sm" style="font-variation-settings: \'FILL\' 1;" data-icon="star"></span>';
                                        } else {
                                            echo '<span class="material-symbols-outlined text-sm" data-icon="star"></span>';
                                        }
                                    }
                                    ?>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400"><?php echo $avg_rating; ?> <?php echo __t('out of 5'); ?></span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">(<?php echo warafy_get_review_count($product->get_id()); ?> <?php echo __t('reviews'); ?>)</span>
                            </div>
                        </div>

                        <?php if (is_user_logged_in() && warafy_user_purchased_product(get_current_user_id(), $product->get_id())) : ?>
                            <!-- Review Form -->
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 mb-6">
                                <form id="warafy-review-form" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?php echo __t('Your Rating'); ?> *</label>
                                        <div class="flex gap-2" id="rating-stars">
                                            <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                <button type="button" class="rating-star text-gray-300 hover:text-yellow-500 transition-colors" data-rating="<?php echo $i; ?>">
                                                    <span class="material-symbols-outlined text-2xl" data-icon="star"></span>
                                                </button>
                                            <?php endfor; ?>
                                        </div>
                                        <input type="hidden" name="rating" id="selected-rating" value="0" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?php echo __t('Your Review'); ?> *</label>
                                        <textarea name="review_text" rows="4" required maxlength="2000" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="<?php echo __t('Share your experience with this product...'); ?>"></textarea>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Maximum 2000 characters</p>
                                    </div>
                                    <input type="hidden" name="product_id" value="<?php echo $product->get_id(); ?>">
                                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('warafy_review_nonce'); ?>">
                                    <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                                        <?php echo __t('Submit Review'); ?>
                                    </button>
                                </form>
                            </div>
                        <?php elseif (!is_user_logged_in()) : ?>
                            <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6 warafy-info-message">
                                <p class="text-blue-800 dark:text-blue-200">
                                    <span class="material-symbols-outlined text-sm align-middle" data-icon="info"></span>
                                    Please <a href="<?php echo wc_get_page_permalink('myaccount'); ?>" class="text-primary hover:underline">login</a> to write a review.
                                </p>
                            </div>
                        <?php else : ?>
                            <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 mb-6 warafy-info-message">
                                <p class="text-yellow-800 dark:text-yellow-200">
                                    <span class="material-symbols-outlined text-sm align-middle" data-icon="info"></span>
                                    You can only review products you have purchased.
                                </p>
                            </div>
                        <?php endif; ?>

                        <!-- Reviews List -->
                        <div id="warafy-reviews-list" class="space-y-4">
                            <?php
                            $reviews = warafy_get_product_reviews($product->get_id());
                            if ($reviews) :
                                foreach ($reviews as $review) :
                                    $review_date = date('F j, Y', strtotime($review->review_date));
                            ?>
                                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 warafy-review-card">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center warafy-user-avatar">
                                                    <span class="text-primary font-semibold text-sm"><?php echo strtoupper(substr($review->user_name, 0, 1)); ?></span>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900 dark:text-white"><?php echo esc_html($review->user_name); ?></p>
                                                    <div class="flex items-center gap-2">
                                                        <div class="flex text-yellow-500">
                                                            <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                                <span class="material-symbols-outlined text-xs <?php echo $i <= $review->rating ? 'filled' : ''; ?>" style="<?php echo $i <= $review->rating ? 'font-variation-settings: \'FILL\' 1;' : ''; ?>" data-icon="star"></span>
                                                            <?php endfor; ?>
                                                        </div>
                                                        <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo $review_date; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed"><?php echo esc_html($review->review_text); ?></p>
                                    </div>
                            <?php
                                endforeach;
                            else :
                            ?>
                                <p class="text-gray-500 dark:text-gray-400 text-center py-8">No reviews yet. Be the first to review!</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Related Products Section (Desktop) -->
            <section class="mt-12 warafy-related-section">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6"><?php echo __t('Related Products'); ?></h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 warafy-related-grid">
                    <!-- Products loaded via AJAX -->
                </div>
                <div class="mt-8 flex justify-center warafy-related-loading-trigger">
                    <div class="loading-spinner hidden">
                        <span class="material-symbols-outlined animate-spin text-primary text-3xl" data-icon="progress_activity"></span>
                    </div>
                </div>
            </section>
        </div>

        <!-- Mobile Content -->
        <div class="lg:hidden pb-[216px]">
            <!-- Header Image Carousel -->
            <div id="mobile-product-carousel" class="relative overflow-hidden w-full bg-white dark:bg-background-dark">
                <?php if (!empty($all_image_ids)) : ?>
                    <div class="mobile-carousel-track flex transition-transform duration-300 ease-in-out">
                        <?php foreach ($all_image_ids as $index => $attachment_id) :
                            $image_url_large = wp_get_attachment_image_url($attachment_id, 'large');
                        ?>
                            <div class="mobile-carousel-item flex-shrink-0 w-full aspect-square bg-center bg-no-repeat bg-contain cursor-zoom-in" style='background-image: url("<?php echo esc_url($image_url_large); ?>");' data-image-index="<?php echo $index; ?>" data-image-url="<?php echo esc_url(wp_get_attachment_image_url($attachment_id, 'full')); ?>" data-image-alt="<?php echo esc_attr( $product->get_name() ); ?>"></div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($all_image_ids) > 1) : ?>
                        <div class="mobile-carousel-dots absolute bottom-4 left-0 right-0 flex justify-center space-x-2">
                            <?php foreach ($all_image_ids as $index => $attachment_id) : ?>
                                <span class="mobile-carousel-dot w-2 h-2 rounded-full bg-gray-300 dark:bg-gray-600 cursor-pointer <?php echo $index === 0 ? 'bg-primary dark:bg-primary-light' : ''; ?>" data-image-index="<?php echo $index; ?>"></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="mobile-carousel-item flex-shrink-0 w-full aspect-square bg-gray-200 flex items-center justify-center text-gray-500">No Image</div>
                <?php endif; ?>
            </div>

            <!-- Video Gallery Section (Mobile) -->
            <?php 
            // Reuse YouTube URL already fetched for desktop
            if (!isset($youtube_url)) {
                $youtube_url = get_post_meta($product->get_id(), '_warafy_youtube_url', true);
            }
            if (!isset($youtube_embed_url)) {
                $youtube_embed_url = warafy_get_youtube_embed_url($youtube_url);
            }
            if ($youtube_embed_url) : 
            ?>
            <div class="px-4 pt-4">
                <h4 class="text-base font-bold text-slate-900 dark:text-slate-100 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-red-500 text-xl" data-icon="play_circle"></span>
                    <?php echo __t('Video Gallery'); ?>
                </h4>
                <div class="warafy-video-container rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="warafy-video-wrapper">
                        <iframe 
                            src="<?php echo esc_url($youtube_embed_url); ?>" 
                            title="Product Video"
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                            allowfullscreen
                            loading="lazy"
                            class="warafy-video-iframe"
                        ></iframe>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="p-4 flex flex-col gap-6">
                <!-- Breadcrumbs -->
                <div class="flex flex-wrap items-center gap-2 text-sm">
                    <?php woocommerce_breadcrumb(); ?>
                </div>
                <!-- Page Heading (Title & Price) -->
                <div class="flex flex-wrap justify-between items-center gap-3">
                    <div class="flex flex-col gap-1">
                        <h2 class="text-slate-900 dark:text-slate-50 text-3xl font-black leading-tight tracking-tight"><?php the_title(); ?></h2>
                        <div class="flex items-center gap-2">
                             <div class="flex text-yellow-500">
                                <?php 
                                $rating = $product->get_average_rating();
                                for ($i = 0; $i < 5; $i++) {
                                    echo '<span class="material-symbols-outlined !text-lg" ' . ($i < $rating ? 'style="font-variation-settings: \'FILL\' 1;"' : '') . '" data-icon="star"></span>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-end gap-2 flex-wrap">
                            <?php if ($has_discount) : ?>
                                <p class="text-3xl font-extrabold text-amber-500 dark:text-amber-400"><?php echo wc_price($sale_price); ?></p>
                                <p class="text-lg font-medium text-red-500 line-through decoration-2"><?php echo wc_price($regular_price); ?></p>
                            <?php else : ?>
                                <p class="text-primary dark:text-primary-light text-3xl font-bold"><?php echo $product->get_price_html(); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php if ($has_discount) : ?>
                            <span class="inline-flex items-center rounded-2xl bg-emerald-600 px-4 py-2 text-base font-bold text-white shadow-sm">
                                <?php echo esc_html(sprintf(__('Save %d%%', 'woocommerce'), $save_percentage)); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Product Description -->
                    <div class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        <?php the_excerpt(); ?>
                    </div>
                </div>

                <!-- Add to Cart Footer (Sticky) -->
                <footer class="fixed bottom-16 left-0 right-0 w-full max-w-md mx-auto bg-white/90 dark:bg-background-dark/90 backdrop-blur-sm border-t border-slate-200 dark:border-slate-700 p-4 z-10">
                     <form class="cart flex flex-col gap-2" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
                        <!-- Quantity + Add to Cart in same line -->
                        <div class="flex items-stretch gap-3">
                            <?php if ( $product->is_sold_individually() ) : ?>
                                <div class="single-product-qty h-12 min-w-[120px] rounded-xl border border-gray-200 bg-white px-4 text-center text-base font-semibold leading-[48px] text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white flex-shrink-0">
                                    1
                                    <input type="hidden" name="quantity" value="1">
                                </div>
                            <?php else : ?>
                                <div class="single-product-qty inline-flex h-12 items-center overflow-hidden rounded-xl border border-[#e6b400] bg-white dark:border-[#e6b400] dark:bg-gray-900 flex-shrink-0">
                                    <button type="button" class="single-qty-btn single-qty-minus flex h-12 w-12 items-center justify-center text-2xl font-medium text-gray-500 transition-colors hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800" aria-label="Decrease quantity">−</button>
                                    <input
                                        type="number"
                                        id="quantity-mobile"
                                        name="quantity"
                                        value="<?php echo isset( $_POST['quantity'] ) ? esc_attr( wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) ) : esc_attr( $min_purchase_quantity ); ?>"
                                        min="<?php echo esc_attr( apply_filters( 'woocommerce_quantity_input_min', $min_purchase_quantity, $product ) ); ?>"
                                        max="<?php echo esc_attr( apply_filters( 'woocommerce_quantity_input_max', $max_purchase_quantity, $product ) ); ?>"
                                        step="1"
                                        inputmode="numeric"
                                        class="single-qty-input h-12 w-12 border-0 bg-transparent text-center text-base font-medium text-gray-900 focus:outline-none focus:ring-0 dark:text-white appearance-none [-moz-appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                    >
                                    <button type="button" class="single-qty-btn single-qty-plus flex h-12 w-12 items-center justify-center text-2xl font-medium text-gray-500 transition-colors hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800" aria-label="Increase quantity">+</button>
                                </div>
                            <?php endif; ?>
                            <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="flex-1 bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 px-6 rounded-xl flex items-center justify-center gap-2 text-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                                    <line x1="9" y1="13" x2="15" y2="13"></line>
                                </svg>
                                <?php echo __t('Add to Cart'); ?>
                            </button>
                        </div>

                        <!-- Buy Now Button -->
                        <button type="button" class="buy-now-btn flex items-center justify-center gap-2 w-full rounded-lg h-12 px-6 bg-[#F5A623] text-white hover:bg-[#E8960E] transition-colors font-semibold shadow-lg" data-product-id="<?php echo $product->get_id(); ?>" data-checkout-url="<?php echo esc_url( wc_get_checkout_url() ); ?>" title="<?php echo __t('Order Now'); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                            <span class="btn-text"><?php echo __t('Order Now'); ?></span>
                        </button>
                    </form>
                </footer>



                <!-- Reviews Section -->
                <div class="flex flex-col gap-4 border-t border-slate-200 dark:border-slate-700 pt-6">
                    <div class="flex items-center justify-between">
                        <h4 class="text-slate-900 dark:text-slate-100 text-base font-bold"><?php echo __t('Customer Reviews'); ?></h4>
                        <div class="flex items-center gap-1">
                            <div class="flex text-yellow-500">
                                <?php
                                $avg_rating = warafy_get_average_rating($product->get_id());
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $avg_rating) {
                                        echo '<span class="material-symbols-outlined filled text-xs" style="font-variation-settings: \'FILL\' 1;" data-icon="star"></span>';
                                    } else {
                                        echo '<span class="material-symbols-outlined text-xs" data-icon="star"></span>';
                                    }
                                }
                                ?>
                            </div>
                            <span class="text-xs text-gray-600 dark:text-gray-400"><?php echo $avg_rating; ?> (<?php echo warafy_get_review_count($product->get_id()); ?>)</span>
                        </div>
                    </div>
                    
                    <?php if (is_user_logged_in() && warafy_user_purchased_product(get_current_user_id(), $product->get_id())) : ?>
                        <!-- Review Form -->
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-4">
                            <form id="warafy-review-form-mobile" class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?php echo __t('Your Rating'); ?> *</label>
                                    <div class="flex gap-1 justify-center" id="rating-stars-mobile">
                                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                                            <button type="button" class="rating-star text-gray-300 hover:text-yellow-500 transition-colors p-1" data-rating="<?php echo $i; ?>">
                                                <span class="material-symbols-outlined text-xl" data-icon="star"></span>
                                            </button>
                                        <?php endfor; ?>
                                    </div>
                                    <input type="hidden" name="rating" id="selected-rating-mobile" value="0" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"><?php echo __t('Your Review'); ?> *</label>
                                    <textarea name="review_text" rows="3" required maxlength="2000" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="<?php echo __t('Share your experience...'); ?>"></textarea>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Maximum 2000 characters</p>
                                </div>
                                <input type="hidden" name="product_id" value="<?php echo $product->get_id(); ?>">
                                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('warafy_review_nonce'); ?>">
                                <button type="submit" class="w-full bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors text-sm font-medium">
                                    <?php echo __t('Submit Review'); ?>
                                </button>
                            </form>
                        </div>
                    <?php elseif (!is_user_logged_in()) : ?>
                        <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg p-3 mb-4 warafy-info-message">
                            <p class="text-blue-800 dark:text-blue-200 text-sm">
                                <span class="material-symbols-outlined text-sm align-middle" data-icon="info"></span>
                                Please <a href="<?php echo wc_get_page_permalink('myaccount'); ?>" class="text-primary hover:underline">login</a> to write a review.
                            </p>
                        </div>
                    <?php else : ?>
                        <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg p-3 mb-4 warafy-info-message">
                            <p class="text-yellow-800 dark:text-yellow-200 text-sm">
                                <span class="material-symbols-outlined text-sm align-middle" data-icon="info"></span>
                                You can only review products you have purchased.
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Reviews List -->
                    <div id="warafy-reviews-list-mobile" class="space-y-3">
                        <?php
                        $reviews = warafy_get_product_reviews($product->get_id());
                        if ($reviews) :
                            foreach ($reviews as $review) :
                                $review_date = date('M j, Y', strtotime($review->review_date));
                        ?>
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-3 warafy-review-card">
                                <div class="flex items-start gap-3 mb-2">
                                    <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center warafy-user-avatar flex-shrink-0">
                                        <span class="text-primary font-semibold text-xs"><?php echo strtoupper(substr($review->user_name, 0, 1)); ?></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 dark:text-white text-sm"><?php echo esc_html($review->user_name); ?></p>
                                        <div class="flex items-center gap-2">
                                            <div class="flex text-yellow-500">
                                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                    <span class="material-symbols-outlined text-xs <?php echo $i <= $review->rating ? 'filled' : ''; ?>" style="<?php echo $i <= $review->rating ? 'font-variation-settings: \'FILL\' 1;' : ''; ?>" data-icon="star"></span>
                                                <?php endfor; ?>
                                            </div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo $review_date; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed"><?php echo esc_html($review->review_text); ?></p>
                            </div>
                        <?php
                            endforeach;
                        else :
                        ?>
                            <p class="text-gray-500 dark:text-gray-400 text-center py-6 text-sm">No reviews yet. Be the first to review!</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Related Products Section (Mobile) -->
                <div class="flex flex-col gap-4 border-t border-slate-200 dark:border-slate-700 pt-6 warafy-related-section-mobile px-4">
                    <h4 class="text-slate-900 dark:text-slate-100 text-base font-bold"><?php echo __t('Related Products'); ?></h4>
                    <div class="grid grid-cols-2 gap-4 mobile-grid-2 warafy-related-grid-mobile">
                         <!-- Products loaded via AJAX -->
                    </div>
                    <div class="mt-4 flex justify-center warafy-related-loading-trigger-mobile">
                        <div class="loading-spinner hidden">
                            <span class="material-symbols-outlined animate-spin text-primary text-2xl" data-icon="progress_activity"></span>
                        </div>
                    </div>
                </div>
                </div>
            </div>

        <!-- Product Image Modal -->
        <div id="warafy-product-image-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 p-4" aria-hidden="true">
            <div id="warafy-product-image-modal-panel" class="relative flex items-center justify-center overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-900" role="dialog" aria-modal="true" style="width: 70vw; height: 70vh;">
                <button type="button" id="warafy-product-image-modal-close" class="absolute right-3 top-3 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-black/50 text-white transition-colors hover:bg-black/70" aria-label="Close image modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
                <div id="warafy-product-image-modal-stage" class="warafy-product-image-modal-stage flex h-full w-full items-center justify-center overflow-hidden bg-black/5">
                    <img id="warafy-product-image-modal-image" src="" alt="<?php echo esc_attr( $product->get_name() ); ?>" class="warafy-product-image-modal-image block max-h-full max-w-full select-none object-contain">
                </div>
            </div>
        </div>

        <style id="warafy-product-gallery-inline-style">
            #warafy-product-image-modal-close {
                background-color: rgba(0,0,0,0.6) !important;
                color: #ffffff !important;
            }
            
            #warafy-product-image-modal-close:hover {
                background-color: rgba(0,0,0,0.8) !important;
            }
            
            #warafy-product-image-modal-panel {
                width: 70vw !important;
                height: 70vh !important;
                max-width: 70vw !important;
                max-height: 70vh !important;
            }

            .warafy-product-image-modal-stage {
                touch-action: none;
            }

            .warafy-product-image-modal-image {
                transition: transform 0.15s ease;
                transform-origin: center center;
                will-change: transform;
            }

            @media (max-width: 1024px) {
                #warafy-product-image-modal-panel {
                    width: 90vw !important;
                    height: 75vh !important;
                    max-width: 90vw !important;
                    max-height: 75vh !important;
                }
            }

            @media (max-width: 640px) {
                #warafy-product-image-modal-panel {
                    width: 94vw !important;
                    height: 70vh !important;
                    max-width: 94vw !important;
                    max-height: 70vh !important;
                }
            }
        </style>

        <script id="warafy-product-gallery-inline-script">
        (function () {
            const ROTATION_INTERVAL_MS = 6000;

            function initializeProductGallery() {
                if (window.__warafyProductGalleryInitialized) {
                    return;
                }

                window.__warafyProductGalleryInitialized = true;

                const desktopMainCarousel = document.getElementById('desktop-product-carousel-main');
                const desktopCarouselItems = desktopMainCarousel ? Array.from(desktopMainCarousel.querySelectorAll('.desktop-carousel-item')) : [];
                const desktopCarouselThumbs = Array.from(document.querySelectorAll('.desktop-carousel-thumb'));
                const desktopPrevBtn = document.querySelector('.desktop-carousel-prev');
                const desktopNextBtn = document.querySelector('.desktop-carousel-next');
                let currentDesktopIndex = 0;
                let desktopInterval = null;

                function renderDesktopCarousel() {
                    desktopCarouselItems.forEach((item, index) => {
                        const isActive = index === currentDesktopIndex;
                        item.classList.toggle('opacity-100', isActive);
                        item.classList.toggle('opacity-0', !isActive);
                        item.classList.toggle('pointer-events-auto', isActive);
                        item.classList.toggle('pointer-events-none', !isActive);
                    });

                    desktopCarouselThumbs.forEach((thumb, index) => {
                        thumb.classList.toggle('border-primary', index === currentDesktopIndex);
                    });
                }

                function startDesktopInterval() {
                    if (desktopInterval) {
                        clearInterval(desktopInterval);
                    }

                    if (desktopCarouselItems.length > 1) {
                        desktopInterval = setInterval(() => {
                            currentDesktopIndex = (currentDesktopIndex + 1) % desktopCarouselItems.length;
                            renderDesktopCarousel();
                        }, ROTATION_INTERVAL_MS);
                    }
                }

                function goToDesktopSlide(index) {
                    if (!desktopCarouselItems.length) {
                        return;
                    }

                    currentDesktopIndex = ((index % desktopCarouselItems.length) + desktopCarouselItems.length) % desktopCarouselItems.length;
                    renderDesktopCarousel();
                    startDesktopInterval();
                }

                if (desktopCarouselItems.length) {
                    currentDesktopIndex = Math.max(0, desktopCarouselItems.findIndex((item) => item.classList.contains('opacity-100')));
                    renderDesktopCarousel();
                    startDesktopInterval();

                    desktopCarouselThumbs.forEach((thumb) => {
                        thumb.addEventListener('click', () => {
                            const nextIndex = parseInt(thumb.dataset.imageIndex || '0', 10);
                            goToDesktopSlide(nextIndex);
                        });
                    });

                    if (desktopPrevBtn) {
                        desktopPrevBtn.addEventListener('click', () => goToDesktopSlide(currentDesktopIndex - 1));
                    }

                    if (desktopNextBtn) {
                        desktopNextBtn.addEventListener('click', () => goToDesktopSlide(currentDesktopIndex + 1));
                    }
                }

                const mobileCarousel = document.getElementById('mobile-product-carousel');
                const mobileCarouselTrack = mobileCarousel ? mobileCarousel.querySelector('.mobile-carousel-track') : null;
                const mobileCarouselItems = mobileCarousel ? Array.from(mobileCarousel.querySelectorAll('.mobile-carousel-item')) : [];
                const mobileCarouselDots = mobileCarousel ? Array.from(mobileCarousel.querySelectorAll('.mobile-carousel-dot')) : [];
                let currentMobileIndex = 0;
                let mobileInterval = null;
                let mobileTouchStartX = 0;
                let mobileTouchEndX = 0;

                function renderMobileCarousel() {
                    if (!mobileCarouselTrack || !mobileCarouselItems.length) {
                        return;
                    }

                    const itemWidth = mobileCarouselItems[0].clientWidth;
                    mobileCarouselTrack.style.transform = 'translateX(-' + (currentMobileIndex * itemWidth) + 'px)';

                    mobileCarouselDots.forEach((dot, index) => {
                        const isActive = index === currentMobileIndex;
                        dot.classList.toggle('bg-primary', isActive);
                        dot.classList.toggle('dark:bg-primary-light', isActive);
                        dot.classList.toggle('bg-gray-300', !isActive);
                        dot.classList.toggle('dark:bg-gray-600', !isActive);
                    });
                }

                function startMobileInterval() {
                    if (mobileInterval) {
                        clearInterval(mobileInterval);
                    }

                    if (mobileCarouselItems.length > 1) {
                        mobileInterval = setInterval(() => {
                            currentMobileIndex = (currentMobileIndex + 1) % mobileCarouselItems.length;
                            renderMobileCarousel();
                        }, ROTATION_INTERVAL_MS);
                    }
                }

                function goToMobileSlide(index) {
                    if (!mobileCarouselItems.length) {
                        return;
                    }

                    currentMobileIndex = ((index % mobileCarouselItems.length) + mobileCarouselItems.length) % mobileCarouselItems.length;
                    renderMobileCarousel();
                    startMobileInterval();
                }

                if (mobileCarouselTrack && mobileCarouselItems.length) {
                    renderMobileCarousel();
                    startMobileInterval();

                    mobileCarouselDots.forEach((dot) => {
                        dot.addEventListener('click', () => {
                            const nextIndex = parseInt(dot.dataset.imageIndex || '0', 10);
                            if (nextIndex < mobileCarouselItems.length) {
                                goToMobileSlide(nextIndex);
                            }
                        });
                    });

                    mobileCarousel.addEventListener('touchstart', (event) => {
                        if (!event.touches.length) {
                            return;
                        }

                        mobileTouchStartX = event.touches[0].clientX;
                        mobileTouchEndX = mobileTouchStartX;
                    }, { passive: true });

                    mobileCarousel.addEventListener('touchmove', (event) => {
                        if (!event.touches.length) {
                            return;
                        }

                        mobileTouchEndX = event.touches[0].clientX;
                    }, { passive: true });

                    mobileCarousel.addEventListener('touchend', () => {
                        const swipeDistance = mobileTouchStartX - mobileTouchEndX;
                        const sensitivity = 50;

                        if (swipeDistance > sensitivity) {
                            goToMobileSlide(currentMobileIndex + 1);
                        } else if (swipeDistance < -sensitivity) {
                            goToMobileSlide(currentMobileIndex - 1);
                        }
                    });

                    window.addEventListener('resize', renderMobileCarousel);
                }

                const modal = document.getElementById('warafy-product-image-modal');
                const modalImage = document.getElementById('warafy-product-image-modal-image');
                const modalStage = document.getElementById('warafy-product-image-modal-stage');
                const modalClose = document.getElementById('warafy-product-image-modal-close');
                let modalZoom = 1;
                let pinchStartDistance = 0;
                let pinchStartZoom = 1;

                function clampZoom(value) {
                    return Math.min(4, Math.max(1, value));
                }

                function applyModalZoom() {
                    if (!modalImage) {
                        return;
                    }

                    modalImage.style.transform = 'scale(' + modalZoom + ')';
                }

                function resetModalZoom() {
                    modalZoom = 1;
                    applyModalZoom();
                }

                function getTouchDistance(touches) {
                    if (touches.length < 2) {
                        return 0;
                    }

                    const dx = touches[0].clientX - touches[1].clientX;
                    const dy = touches[0].clientY - touches[1].clientY;
                    return Math.sqrt((dx * dx) + (dy * dy));
                }

                function openModal(imageUrl, imageAlt) {
                    if (!modal || !modalImage || !imageUrl) {
                        return;
                    }

                    modalImage.src = imageUrl;
                    modalImage.alt = imageAlt || '';
                    resetModalZoom();
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    modal.setAttribute('aria-hidden', 'false');
                    document.body.style.overflow = 'hidden';
                }

                function closeModal() {
                    if (!modal) {
                        return;
                    }

                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    modal.setAttribute('aria-hidden', 'true');
                    document.body.style.overflow = '';
                    resetModalZoom();
                }

                document.querySelectorAll('.desktop-carousel-item, .mobile-carousel-item').forEach((imageItem) => {
                    imageItem.addEventListener('click', () => {
                        openModal(imageItem.dataset.imageUrl, imageItem.dataset.imageAlt);
                    });
                });

                if (modalClose) {
                    modalClose.addEventListener('click', closeModal);
                }

                if (modal) {
                    modal.addEventListener('click', (event) => {
                        if (event.target === modal) {
                            closeModal();
                        }
                    });
                }

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                        closeModal();
                    }
                });

                if (modalStage) {
                    modalStage.addEventListener('wheel', (event) => {
                        if (!modal || modal.classList.contains('hidden')) {
                            return;
                        }

                        event.preventDefault();
                        modalZoom = clampZoom(modalZoom + (event.deltaY < 0 ? 0.15 : -0.15));
                        applyModalZoom();
                    }, { passive: false });

                    modalStage.addEventListener('touchstart', (event) => {
                        if (event.touches.length === 2) {
                            pinchStartDistance = getTouchDistance(event.touches);
                            pinchStartZoom = modalZoom;
                        }
                    }, { passive: true });

                    modalStage.addEventListener('touchmove', (event) => {
                        if (!modal || modal.classList.contains('hidden')) {
                            return;
                        }

                        if (event.touches.length === 2 && pinchStartDistance) {
                            event.preventDefault();
                            modalZoom = clampZoom(pinchStartZoom * (getTouchDistance(event.touches) / pinchStartDistance));
                            applyModalZoom();
                        }
                    }, { passive: false });

                    modalStage.addEventListener('touchend', () => {
                        pinchStartDistance = 0;
                    });

                    modalStage.addEventListener('touchcancel', () => {
                        pinchStartDistance = 0;
                    });

                    modalStage.addEventListener('dblclick', () => {
                        modalZoom = modalZoom > 1 ? 1 : 2;
                        applyModalZoom();
                    });

                    applyModalZoom();
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializeProductGallery, { once: true });
            } else {
                initializeProductGallery();
            }
        })();
        </script>

    <?php endwhile; ?>
</main>



<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Single product page loaded - Related Products script initialized');

    document.body.addEventListener('click', function(e) {
        const qtyBtn = e.target.closest('.single-qty-btn');
        if (!qtyBtn) return;

        const qtyWrap = qtyBtn.closest('.single-product-qty');
        if (!qtyWrap) return;

        const input = qtyWrap.querySelector('.single-qty-input');
        if (!input) return;

        const current = parseInt(input.value || '1', 10);
        const min = parseInt(input.min || '1', 10);
        const max = parseInt(input.max || '9999', 10);
        const next = qtyBtn.classList.contains('single-qty-plus') ? Math.min(max, current + 1) : Math.max(min, current - 1);

        input.value = Number.isFinite(next) ? next : min;
        input.dispatchEvent(new Event('change', { bubbles: true }));
    });

    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.buy-now-btn');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const form = btn.closest('form');
        const quantityInput = form ? form.querySelector('input[name="quantity"]') : null;
        const quantity = quantityInput && quantityInput.value ? parseInt(quantityInput.value, 10) : 1;
        const productId = btn.dataset.productId;
        const checkoutUrl = btn.dataset.checkoutUrl || '<?php echo esc_url( wc_get_checkout_url() ); ?>';

        if (!productId) return;

        const url = new URL(checkoutUrl, window.location.origin);
        url.searchParams.set('add-to-cart', productId);
        url.searchParams.set('quantity', Number.isFinite(quantity) && quantity > 0 ? quantity : 1);

        btn.disabled = true;
        window.location.href = url.toString();
    });
    
    // Infinite Scroll logic for Related Products
    const setupRelatedProductsInfiniteScroll = (gridSelector, triggerSelector, isMobile) => {
        const grid = document.querySelector(gridSelector);
        const trigger = document.querySelector(triggerSelector);
        
        if (grid && trigger) {
            const spinner = trigger.querySelector('.loading-spinner');
            let page = 1;
            let isLoading = false;
            let hasMore = true;
            let prefetchedData = null;
            // Get product ID safely from PHP
            const productId = <?php echo isset($product) ? $product->get_id() : get_the_ID(); ?>;
            
            // CSS for animation (unique ID to avoid conflict if already added)
            if (!document.getElementById('warafy-fade-style')) {
                const style = document.createElement('style');
                style.id = 'warafy-fade-style';
                style.textContent = `
                    .product-card-recommendation {
                        opacity: 0;
                        transform: translateY(20px);
                        animation: fadeInUp 0.4s ease-out forwards;
                    }
                    @keyframes fadeInUp {
                        to {
                            opacity: 1;
                            transform: translateY(0);
                        }
                    }
                `;
                document.head.appendChild(style);
            }

            const fetchPage = async (pageNum) => {
                const response = await fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'warafy_load_related_products',
                        page: pageNum,
                        product_id: productId
                    })
                });
                return response.json();
            };
            
            const renderProducts = (html) => {
                const temp = document.createElement('div');
                temp.innerHTML = html;
                const cards = temp.querySelectorAll('.product-card-recommendation');
                cards.forEach((card, index) => {
                    card.style.animationDelay = `${index * 0.05}s`;
                });
                grid.insertAdjacentHTML('beforeend', temp.innerHTML);
            };
            
            const prefetchNextPage = async () => {
                if (!hasMore || prefetchedData) return;
                try {
                    prefetchedData = await fetchPage(page + 1);
                    if (!prefetchedData.success || !prefetchedData.data?.html) {
                        prefetchedData = null;
                    }
                } catch (e) {
                    prefetchedData = null;
                }
            };
            
            const loadMoreProducts = async () => {
                if (isLoading || !hasMore) return;
                
                isLoading = true;
                if (spinner) spinner.classList.remove('hidden');
                
                try {
                    let data;
                    if (prefetchedData && page > 1) {
                        data = prefetchedData;
                        prefetchedData = null;
                        page++;
                    } else {
                        data = await fetchPage(page);
                        if (data.success && data.data?.html) {
                            page++;
                        }
                    }
                    
                    if (data.success && data.data?.html) {
                        renderProducts(data.data.html);
                        prefetchNextPage();
                    } else {
                        hasMore = false;
                        trigger.innerHTML = '<p class="text-gray-500 text-sm py-4"><?php echo __t("No more products to show"); ?></p>';
                    }
                } catch (error) {
                    console.error('Error loading related products:', error);
                } finally {
                    isLoading = false;
                    if (spinner) spinner.classList.add('hidden');
                }
            };
            
            // Initial Load
            loadMoreProducts();
            
            // Intersection Observer
            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting && !isLoading && hasMore) {
                    loadMoreProducts();
                }
            }, { rootMargin: '600px' });
            
            observer.observe(trigger);
        }
    };

    // Initialize for Desktop
    setupRelatedProductsInfiniteScroll('.warafy-related-grid', '.warafy-related-loading-trigger', false);
    
    // Initialize for Mobile
    setupRelatedProductsInfiniteScroll('.warafy-related-grid-mobile', '.warafy-related-loading-trigger-mobile', true);
});
</script>

<?php get_footer(); ?>
