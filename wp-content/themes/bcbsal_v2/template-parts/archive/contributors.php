<?php
/**
 * Contributors module for archive page
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$settings_page = 'hub-settings-' . $args['page_name'];
$section_title = $args['section_title'];
$section_desc  = $args['section_description'] ?? '';

// get selected authors.
$contributors = array();
foreach ( get_post_authors( get_post_id_by_slug( $settings_page ) ) as $contributor ) {
	$contributors[] = $contributor->ID;
}
// @todo remove all dummy author
?>

<div class="archive-contributors">
	<div class="container">
		<h2 class="sq-section-title"><?php echo esc_attr( $section_title ); ?></h2>
		<?php if ( $section_desc ) { ?>
			<p class="archive-contributors__description"><?php echo esc_html( $section_desc ); ?></p>
		<?php } ?>
		<div class="row">
			<?php
			foreach ( $contributors as $contributor ) {
				?>
				<div class="col-sm-6 col-md-4">
					<?php
					get_template_part(
						'template-parts/common/card-contributor',
						'card-contributor',
						array(
							'author_id' => $contributor,
						)
					);
					?>
				</div>
			<?php } ?>
		</div>
		<?php if ( is_page( 'about' ) ) { ?>
		<div class="archive-contributors__load-more">
			<button type="button" class="archive-contributors__load-more-button button">Load more</button>
		</div>
		<?php } ?>
	</div>
</div>
