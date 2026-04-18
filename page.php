<?php get_header(); ?>

<main class="flex-grow pb-24 lg:pb-0">
    <div class="container mx-auto px-6 py-8">
        <?php
        while ( have_posts() ) :
            the_post();
            ?>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6"><?php the_title(); ?></h1>
            <div class="prose max-w-none dark:prose-invert">
                <?php the_content(); ?>
            </div>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
