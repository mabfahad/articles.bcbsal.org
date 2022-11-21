<?php
/**
 * Masonry article layout in category archive
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$section_title = $args['section_title'] ?? '';
$modifier      = $args['modifier'] ?? '';
$post_ids      = get_post_ids_by_urls( $args['featured'] );

// posts query.
$featured_posts = get_posts(
	array(
		'posts_per_page' => 6,
		'post__in'       => $post_ids,
		'post_status'    => 'publish',
		'order'          => 'post__in',
	)
);

if ( $featured_posts && count( $featured_posts ) > 1 ) { ?>
<div class="archive-featured-articles <?php echo esc_attr( $modifier ); ?>">
	<div class="container">
		<h2 class="sq-section-title"><?php echo esc_attr( $section_title ); ?></h2>
		<div class="archive-featured-articles__column--mobile-first-post">
			<?php
			// @codingStandardsIgnoreLine
			foreach ( array_slice( $featured_posts, 0, 1 ) as $post ) {
				setup_postdata( $post );
				get_template_part(
					'template-parts/common/card-top-image',
					'card-top-image',
					array(
						'image_width'    => 376,
						'modifier'       => 'archive-featured-articles__post card-top-image--top-mobile',
						'keep_top_image' => true,
					)
				);
			}
			wp_reset_postdata();
			?>
		</div>

		<div class="archive-featured-articles__posts masonry">
			<?php
			// @codingStandardsIgnoreLine
			foreach ( $featured_posts as $post ) {
				setup_postdata( $post );
				?>
			<div class="archive-featured-articles__column">
				<?php
				get_template_part(
					'template-parts/common/card-top-image',
					'card-top-image',
					array(
						'image_width'    => 376,
						'image_ratio'    => is_category( 'tools' ) ? '3x2' : false,
						'modifier'       => 'archive-featured-articles__post card-top-image--top-mobile',
						'keep_top_image' => true,
					)
				);
				?>
			</div>
			<?php } wp_reset_postdata(); ?>
		</div>
	</div>
</div>
	<?php
}
