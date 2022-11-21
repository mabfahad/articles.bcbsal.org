<?php
/**
 * Featured Collection module.
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$section_title = $args['section_title'] ?? '';
$modifier      = $args['modifier'] ?? '';
$collection    = parse_slug_from_url( $args['collection'] );

// posts query.
$featured_collection = get_posts(
	array(
		'posts_per_page' => 3,
		'cat'            => get_category_by_slug( $collection )->term_id,
		'post_status'    => 'publish',
	)
);

$collection_post = get_post( get_post_id_by_slug( $collection ) );

if ( $featured_collection && count( $featured_collection ) > 1 ) { ?>
<div class="archive-featured-collection <?php echo esc_attr( $modifier ); ?>">
	<div class="container">
		<h2 class="sq-section-title sq-section-title--on-black"><?php echo esc_attr( $section_title ); ?></h2>

		<div class="archive-featured-collection__content">
			<div class="archive-featured-collection__column">
				<h3 class="archive-featured-collection__title">
					<a href="<?php echo esc_url( get_custom_post_permalink( $collection_post->ID ) ); ?>"><?php echo esc_attr( $collection_post->post_title ); ?></a>
				</h3>
				<p class="archive-featured-collection__description"><?php echo esc_html( get_post_deck( $collection_post->ID ) ); ?></p>
			</div>
			<div class="archive-featured-collection__column">
				<a href="<?php echo esc_url( get_custom_post_permalink( $collection_post->ID ) ); ?>" class="button archive-featured-collection__button archive-featured-collection__button--desktop">See full collection</a>
			</div>
		</div>

		<div class="row archive-featured-collection__posts">
			<?php
			// @codingStandardsIgnoreLine
			foreach ( $featured_collection as $post ) {
				setup_postdata( $post );
				?>
			<div class="col-sm-4">
				<?php
				get_template_part(
					'template-parts/common/card-top-image',
					'card-top-image',
					array(
						'image_width'    => 376,
						'modifier'       => 'archive-featured-collection__post card-top-image--top-mobile',
						'keep_top_image' => true,
					)
				);
				?>
			</div>
			<?php } wp_reset_postdata(); ?>
		</div>
		<a href="<?php echo esc_url( get_custom_post_permalink( $collection_post->ID ) ); ?>" class="button archive-featured-collection__button archive-featured-collection__button--mobile">See full collection</a>
	</div>
</div>
	<?php
}
