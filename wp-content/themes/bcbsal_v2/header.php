<?php
/**
 * WP Header
 *
 * This is the default site header.
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>"/>
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<meta http-equiv="X-UA-Compatible" content="ie=edge"/>
	<meta name="theme-color" content="#121212">

	<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

<?php
	get_template_part( 'template-parts/common/site-header' );
