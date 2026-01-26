<?php
/**
 * The front page template file
 *
 * @package Adorkini
 */

get_header(); ?>

<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row gap-6 h-full">

        <!-- Left Sidebar (Desktop Only) -->
        <div class="hidden md:block w-64 min-w-[16rem]">
            <?php get_template_part( 'template-parts/category-sidebar' ); ?>
        </div>

        <!-- Main Content Area -->
        <div class="flex-grow w-full min-w-0">
            
            <!-- Hero Section -->
            <section class="hero-section mb-8 rounded-lg overflow-hidden relative group">
                <!-- Placeholder for Slider - Can be replaced with a real slider plugin or custom JS slider -->
                <div class="relative w-full pb-[50%] md:pb-[30%] bg-gray-200">
                    <img src="https://placehold.co/1200x500/137fec/ffffff?text=Adorkini+Hero+Banner" alt="Hero Banner" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/10 transition-colors group-hover:bg-black/0"></div>
                </div>
            </section>

            <!-- Mobile: Category Bubbles (Horizontal Scroll) -->
            <?php if ( wp_is_mobile() ) : 
                $cat_args = array(
                    'taxonomy'   => 'product_cat',
                    'number'     => 10,
                    'parent'     => 0,
                    'hide_empty' => false,
                );
                $mobile_cats = get_terms( $cat_args );
            ?>
                <section class="mobile-categories mb-8 -mx-4 px-4 overflow-x-auto hide-scrollbar flex space-x-4 md:hidden snap-x">
                    <?php if ( ! empty( $mobile_cats ) && ! is_wp_error( $mobile_cats ) ) : 
                        foreach ( $mobile_cats as $m_cat ) : ?>
                            <a href="<?php echo esc_url( get_term_link( $m_cat ) ); ?>" class="flex flex-col items-center flex-shrink-0 snap-start w-20">
                                <div class="w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center mb-2 border border-blue-100">
                                    <!-- Icon Placeholder -->
                                    <span class="text-primary font-bold text-xl"><?php echo esc_html( strtoupper( substr( $m_cat->name, 0, 1 ) ) ); ?></span>
                                </div>
                                <span class="text-xs text-center font-medium text-gray-700 leading-tight line-clamp-2"><?php echo esc_html( __t( $m_cat->name ) ); ?></span>
                            </a>
                        <?php endforeach; 
                    endif; ?>
                </section>
            <?php endif; ?>

            <!-- Best Sellers Section -->
             <section class="best-sellers mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg md:text-xl font-bold text-gray-900 border-l-4 border-primary pl-3">
                        <?php echo esc_html( __t( 'best_sellers' ) ); ?>
                    </h2>
                    <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="text-xs font-semibold text-primary hover:text-blue-700">See All</a>
                </div>

                <?php
                // Query Best Selling Products
                $best_sellers_args = array(
                    'post_type'      => 'product',
                    'posts_per_page' => 10,
                    'meta_key'       => 'total_sales',
                    'orderby'        => 'meta_value_num',
                    'order'          => 'DESC',
                );
                $best_sellers_query = new WP_Query( $best_sellers_args );

                if ( $best_sellers_query->have_posts() ) : ?>
                    <!-- Desktop: Grid 5 cols, Mobile: Vertical List -->
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <?php while ( $best_sellers_query->have_posts() ) : $best_sellers_query->the_post(); 
                            // Using standard WC template part but wrapper handles layout
                            wc_get_template_part( 'content', 'product' );
                        endwhile; ?>
                    </div>
                <?php else : ?>
                    <p class="text-gray-500 text-sm">No best sellers found.</p>
                <?php endif; wp_reset_postdata(); ?>
            </section>

            <!-- New Arrivals Section -->
            <section class="new-arrivals mb-8">
                 <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg md:text-xl font-bold text-gray-900 border-l-4 border-green-500 pl-3">
                        <?php echo esc_html( __t( 'new_arrivals' ) ); ?>
                    </h2>
                </div>

                <?php
                // Query New Arrivals
                $new_arrivals_args = array(
                    'post_type'      => 'product',
                    'posts_per_page' => 8,
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                );
                $new_arrivals_query = new WP_Query( $new_arrivals_args );

                if ( $new_arrivals_query->have_posts() ) : ?>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <?php while ( $new_arrivals_query->have_posts() ) : $new_arrivals_query->the_post(); 
                            wc_get_template_part( 'content', 'product' );
                        endwhile; ?>
                    </div>
                <?php endif; wp_reset_postdata(); ?>
            </section>

        </div>
    </div>
</div>

<?php
get_footer();
