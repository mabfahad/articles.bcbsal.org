<?php
/**
 * Header section of the home template
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="home-header">
	<div class="container">
		<img class="home-header__logo" width="100%" height="auto" src="<?php echo esc_url( image_url( 'logo-tsq-black-temp.svg' ) ); ?>" alt="The Reader">
		<div class="home-header-category-menu">
			<ul class="home-header-category-menu__items">
				<?php surge_custom_menu( SLIDE_MENU_CATEGORIES ); ?>
			</ul>
		</div>
	</div>
</div>
