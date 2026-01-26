<?php
/**
 * The template for displaying the footer
 *
 * @package Adorkini
 */
?>

    </div><!-- #content -->

    <footer id="colophon" class="site-footer bg-white border-t border-gray-200 py-8 mt-auto hidden md:block">
        <div class="container mx-auto px-4 text-center text-gray-500 text-sm">
            <div class="copyright">
                &copy; <?php echo date( 'Y' ); ?> Adorkini. All rights reserved. <span class="text-xs text-gray-300 ml-2">v<?php echo ADORKINI_VERSION; ?></span>
            </div>
        </div>
    </footer>

</div><!-- #page -->

<?php 
if ( wp_is_mobile() ) {
    get_template_part( 'template-parts/bottom-nav' );
}
?>

<?php wp_footer(); ?>

</body>
</html>
