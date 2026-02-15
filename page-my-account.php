<?php
/*
Template Name: My Account Page
*/

// Redirect if not logged in
if (!is_user_logged_in()) {
    wp_redirect(home_url('/login'));
    exit;
}

get_header();
?>

<main class="flex-grow bg-white dark:bg-background-dark">
    <div class="container mx-auto px-4 py-6">
        <?php echo do_shortcode('[woocommerce_my_account]'); ?>
    </div>
</main>

<?php get_footer(); ?>
