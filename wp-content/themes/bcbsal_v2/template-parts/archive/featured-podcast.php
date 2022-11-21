<?php
/**
 * Featured Podcast module.
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$section_title = $args['section_title'] ?? '';
$modifier      = $args['modifier'] ?? '';
$podcast       = parse_slug_from_url( $args['podcast'] );

// posts query.
$featured_podcasts = get_posts(
	array(
		'posts_per_page' => 3,
		'cat'            => get_category_by_slug( $podcast )->term_id,
		'post_status'    => 'publish',
		'order'          => 'ASC',
		'orderby'        => 'ID',
	)
);

$featured_podcast = get_post( get_post_id_by_slug( $podcast ) );

if ( $featured_podcast && count( $featured_podcasts ) > 1 ) { ?>
<div class="archive-featured-podcast <?php echo esc_attr( $modifier ); ?>">
	<div class="container">
		<h2 class="sq-section-title"><?php echo esc_attr( $section_title ); ?></h2>

		<div class="archive-featured-podcast__content">
			<div class="archive-featured-podcast__column">
				<h3 class="archive-featured-podcast__title"><?php echo esc_attr( $featured_podcast->post_title ); ?></h3>
				<p class="archive-featured-podcast__description"><?php echo esc_html( get_post_deck( $featured_podcast->ID ) ); ?></p>
			</div>
			<div class="archive-featured-podcast__column">
				<a href="<?php echo esc_url( get_custom_post_permalink( $featured_podcast->ID ) ); ?>" class="button archive-featured-podcast__button archive-featured-podcast__button--desktop">See all episodes</a>
			</div>
		</div>

		<div class="row archive-featured-podcast__posts">
			<?php
			// @codingStandardsIgnoreLine
			foreach ( $featured_podcasts as $post ) {
				setup_postdata( $post );
				?>
			<div class="col-sm-4">
				<?php
				get_template_part(
					'template-parts/common/card-top-image',
					'card-top-image',
					array(
						'image_width'    => 376,
						'modifier'       => 'archive-featured-podcast__post card-top-image--top-mobile',
						'image_ratio'    => '1x1',
						'keep_top_image' => true,
					)
				);
				?>
			</div>
			<?php } wp_reset_postdata(); ?>
		</div>
		<a href="<?php echo esc_url( get_custom_post_permalink( $featured_podcast->ID ) ); ?>" class="button archive-featured-podcast__button archive-featured-podcast__button--mobile">See all episodes</a>
	</div>
</div>
	<?php
}
