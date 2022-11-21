<?php
/**
 * Header for archive page
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$modifier = $args['modifier'] ?? '';

if ( in_array( get_queried_object()->slug, array( ...PRIMARY_CATEGORIES, ...LANDING_PAGES ), true ) ) {
	$deck = get_post_deck( get_post_id_by_slug( 'hub-settings-' . get_queried_object()->slug ) );
} else {
	$deck = get_post_deck( get_post_id_by_slug( get_queried_object()->slug ) );
}
?>

<div class="archive-header <?php echo esc_attr( $modifier ); ?>">
	<div class="container">
		<?php if ( is_category( surge_category_group_by_slug( 'collections', true ), get_the_ID() ) ) { ?>
		<p class="archive-header__subheading">Collection</p>
		<?php } ?>
		<h1 class="archive-header__heading"><?php echo esc_html( get_queried_object()->name ); ?></h1>
		<?php if ( $deck && ! is_tag() ) { ?>
			<p class="archive-header__deck"><?php echo esc_html( $deck ); ?></p>
		<?php } ?>
	</div>
</div>
