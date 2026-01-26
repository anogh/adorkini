<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @package Adorkini
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site bg-gray-50 min-h-screen flex flex-col">

	<?php
    // Mobile First approach - but serving distinct HTML for better performance/structure
	if ( wp_is_mobile() ) {
		get_template_part( 'template-parts/header', 'mobile' );
	} else {
		get_template_part( 'template-parts/header', 'desktop' );
	}
	?>

	<div id="content" class="site-content flex-grow">
