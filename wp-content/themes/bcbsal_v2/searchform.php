<?php
/**
 * Search form template
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<div class="container">
	<form class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" autocomplete="off">
		<input aria-label="Search" type="text" class="search-form__input search-form__input--autocomplete" autocomplete="off">
		<input type="text" name="s" maxlength="150" class="search-form__input search-form__input--main" placeholder="Search <?php echo esc_attr( SQ_SITE_NAME ); ?>" aria-label="Search" value="<?php echo get_search_query(); ?>">
		<button type="submit" class="search-form__button search-form__input--filed" aria-label="Search">
			<img src="<?php echo esc_url( image_url( 'icon-search-black.svg' ) ); ?>" alt="Search icon">
		</button>
	</form>
</div>
