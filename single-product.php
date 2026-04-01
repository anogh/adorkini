<?php get_header(); ?>

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
                                <div class="desktop-carousel-item absolute inset-0 w-full h-full bg-center bg-no-repeat bg-contain transition-opacity duration-300 ease-in-out <?php echo $index === 0 ? 'opacity-100' : 'opacity-0'; ?>" style="background-image: url('<?php echo esc_url($image_url_full); ?>');" data-image-index="<?php echo $index; ?>"></div>
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

                    <div class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        <?php the_excerpt(); ?>
                    </div>

                    <div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo $product->get_price_html(); ?></p>
                        <p class="text-sm text-green-600 dark:text-green-400 font-medium mt-1"><?php echo wc_get_stock_html( $product ); ?></p>
                    </div>

                    <div class="w-full h-px bg-gray-200 dark:bg-gray-700"></div>

                    <!-- Custom Add to Cart Form -->
                    <form class="cart flex flex-col gap-4" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
                        
                        <!-- Quantity -->
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium text-gray-900 dark:text-white" for="quantity">Quantity</label>
                            <?php
                            woocommerce_quantity_input(
                                array(
                                    'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
                                    'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
                                    'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(),
                                    'classes'     => 'form-input w-24 rounded-lg border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-primary focus:ring-primary',
                                )
                            );
                            ?>
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="flex w-full min-w-[84px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-base font-bold shadow-lg hover:bg-primary/90">
                                <span class="material-symbols-outlined" data-icon="add_shopping_cart"></span>
                                <span class="truncate">Add to Cart</span>
                            </button>
                        </div>
                        
                        <!-- Add to Love Button -->
                        <button type="button" class="warafy-wishlist-btn flex items-center justify-center gap-2 rounded-lg h-12 px-6 bg-primary/10 text-primary hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 transition-colors font-medium" data-product-id="<?php echo $product->get_id(); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                            <span class="btn-text">Add to Love</span>
                        </button>
                    </form>
                    
                    <!-- Tabs / Full Description -->
                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Description</h3>
                        <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-gray-300">
                            <?php the_content(); ?>
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Have question or opinion? Comment here</h3>
                        
                        <!-- Comment Form -->
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 mb-6">
                            <form id="warafy-comment-form" class="space-y-4">
                                <?php if (!is_user_logged_in()) : ?>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name *</label>
                                            <input type="text" name="user_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                                            <input type="email" name="user_email" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent">
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Comment *</label>
                                    <textarea name="comment_text" rows="4" required maxlength="1000" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Share your thoughts or ask questions about this product..."></textarea>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Maximum 1000 characters</p>
                                </div>
                                <input type="hidden" name="product_id" value="<?php echo $product->get_id(); ?>">
                                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('warafy_comment_nonce'); ?>">
                                <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                                    Post Comment
                                </button>
                            </form>
                        </div>

                        <!-- Comments List -->
                        <div id="warafy-comments-list" class="space-y-4">
                            <?php
                            $comments = warafy_get_product_comments($product->get_id());
                            if ($comments) :
                                foreach ($comments as $comment) :
                                    $user_display_name = $comment->user_id ? get_the_author_meta('display_name', $comment->user_id) : $comment->user_name;
                                    $comment_date = date('F j, Y', strtotime($comment->comment_date));
                            ?>
                                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 warafy-comment-card">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center warafy-user-avatar">
                                                    <span class="text-primary font-semibold text-sm"><?php echo strtoupper(substr($user_display_name, 0, 1)); ?></span>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900 dark:text-white"><?php echo esc_html($user_display_name); ?></p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo $comment_date; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed"><?php echo esc_html($comment->comment_text); ?></p>
                                    </div>
                            <?php
                                endforeach;
                            else :
                            ?>
                                <p class="text-gray-500 dark:text-gray-400 text-center py-8">No comments yet. Be the first to comment!</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Reviews Section -->
                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Customer Reviews</h3>
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
                                <span class="text-sm text-gray-600 dark:text-gray-400"><?php echo $avg_rating; ?> out of 5</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">(<?php echo warafy_get_review_count($product->get_id()); ?> reviews)</span>
                            </div>
                        </div>

                        <?php if (is_user_logged_in() && warafy_user_purchased_product(get_current_user_id(), $product->get_id())) : ?>
                            <!-- Review Form -->
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 mb-6">
                                <form id="warafy-review-form" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your Rating *</label>
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
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your Review *</label>
                                        <textarea name="review_text" rows="4" required maxlength="2000" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Share your experience with this product..."></textarea>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Maximum 2000 characters</p>
                                    </div>
                                    <input type="hidden" name="product_id" value="<?php echo $product->get_id(); ?>">
                                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('warafy_review_nonce'); ?>">
                                    <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                                        Submit Review
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
                        <?php foreach ($all_image_ids as $attachment_id) :
                            $image_url_large = wp_get_attachment_image_url($attachment_id, 'large');
                        ?>
                            <div class="mobile-carousel-item flex-shrink-0 w-full aspect-square bg-center bg-no-repeat bg-contain" style='background-image: url("<?php echo esc_url($image_url_large); ?>");'></div>
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
                    <p class="text-primary dark:text-primary-light text-3xl font-bold"><?php echo $product->get_price_html(); ?></p>
                </div>

                <!-- Add to Cart Footer (Sticky) -->
                <footer class="fixed bottom-16 left-0 right-0 w-full max-w-md mx-auto bg-white/90 dark:bg-background-dark/90 backdrop-blur-sm border-t border-slate-200 dark:border-slate-700 p-4 z-10">
                     <form class="cart flex gap-2" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
                        <?php
                            woocommerce_quantity_input(
                                array(
                                    'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
                                    'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
                                    'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(),
                                )
                            );
                        ?>
                        <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="flex-1 bg-primary hover:bg-primary/90 text-white font-bold py-3 px-6 rounded-xl flex items-center justify-center gap-2 text-lg">
                            <span class="material-symbols-outlined" data-icon="shopping_bag"></span>
                            Add to Cart
                        </button>
                    </form>
                    <!-- Add to Love Button -->
                    <button type="button" class="warafy-wishlist-btn flex items-center justify-center gap-2 w-full rounded-lg h-12 px-6 bg-primary/10 text-primary hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 transition-colors font-medium mt-2" data-product-id="<?php echo $product->get_id(); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                        <span class="btn-text">Add to Love</span>
                    </button>
                </footer>

                <!-- Description Accordion -->
                <div class="flex flex-col gap-2 border-t border-slate-200 dark:border-slate-700 pt-6">
                    <details class="group" open>
                        <summary class="flex justify-between items-center cursor-pointer py-3 list-none">
                            <h4 class="text-slate-900 dark:text-slate-100 text-base font-bold">Product Description</h4>
                            <span class="material-symbols-outlined text-slate-500 transition-transform duration-300 group-open:rotate-180" data-icon="expand_more"></span>
                        </summary>
                        <div class="pb-3 text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                            <?php the_content(); ?>
                        </div>
                    </details>
                </div>

                <!-- Comments Section -->
                <div class="flex flex-col gap-4 border-t border-slate-200 dark:border-slate-700 pt-6">
                    <h4 class="text-slate-900 dark:text-slate-100 text-base font-bold">Have question or opinion? Comment here</h4>
                    
                    <!-- Comment Form -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-4">
                        <form id="warafy-comment-form-mobile" class="space-y-3">
                            <?php if (!is_user_logged_in()) : ?>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label>
                                        <input type="text" name="user_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email *</label>
                                        <input type="email" name="user_email" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Comment *</label>
                                <textarea name="comment_text" rows="3" required maxlength="1000" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Share your thoughts..."></textarea>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Maximum 1000 characters</p>
                            </div>
                            <input type="hidden" name="product_id" value="<?php echo $product->get_id(); ?>">
                            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('warafy_comment_nonce'); ?>">
                            <button type="submit" class="w-full bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors text-sm font-medium">
                                Post Comment
                            </button>
                        </form>
                    </div>

                    <!-- Comments List -->
                    <div id="warafy-comments-list-mobile" class="space-y-3">
                        <?php
                        $comments = warafy_get_product_comments($product->get_id());
                        if ($comments) :
                            foreach ($comments as $comment) :
                                $user_display_name = $comment->user_id ? get_the_author_meta('display_name', $comment->user_id) : $comment->user_name;
                                $comment_date = date('M j, Y', strtotime($comment->comment_date));
                        ?>
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-3 warafy-comment-card">
                                <div class="flex items-start gap-3 mb-2">
                                    <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center warafy-user-avatar flex-shrink-0">
                                        <span class="text-primary font-semibold text-xs"><?php echo strtoupper(substr($user_display_name, 0, 1)); ?></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 dark:text-white text-sm"><?php echo esc_html($user_display_name); ?></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo $comment_date; ?></p>
                                    </div>
                                </div>
                                <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed"><?php echo esc_html($comment->comment_text); ?></p>
                            </div>
                        <?php
                            endforeach;
                        else :
                        ?>
                            <p class="text-gray-500 dark:text-gray-400 text-center py-6 text-sm">No comments yet. Be the first to comment!</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Reviews Section -->
                <div class="flex flex-col gap-4 border-t border-slate-200 dark:border-slate-700 pt-6">
                    <div class="flex items-center justify-between">
                        <h4 class="text-slate-900 dark:text-slate-100 text-base font-bold">Customer Reviews</h4>
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
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your Rating *</label>
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
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Your Review *</label>
                                    <textarea name="review_text" rows="3" required maxlength="2000" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Share your experience..."></textarea>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Maximum 2000 characters</p>
                                </div>
                                <input type="hidden" name="product_id" value="<?php echo $product->get_id(); ?>">
                                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('warafy_review_nonce'); ?>">
                                <button type="submit" class="w-full bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors text-sm font-medium">
                                    Submit Review
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

    <?php endwhile; ?>
</main>



<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Single product page loaded - Related Products script initialized');
    
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
