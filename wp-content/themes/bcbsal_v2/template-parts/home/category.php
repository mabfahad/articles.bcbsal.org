<?php
/**
 * Category section of the home template
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// category.
$category       = get_term_by( 'name', $args['category'], 'category' );
$category_posts = get_post_ids_by_urls( $args['posts'] );

// posts query.
$category_posts = get_posts(
	array(
		'posts_per_page' => 5,
		'category'       => $category->term_id,
		'post__in'       => $category_posts,
		'post_status'    => 'publish',
		'order'          => 'post__in',
	)
);

if ( $category && count( $category_posts ) > 1 ) { ?>
<div class="home-category">
	<div class="container">
		<h2 class="sq-section-title"><?php echo esc_attr( $category->name ); ?> <a href="<?php echo esc_url( get_category_link( $category ) ); ?>">View all</a></h2>
		<?php
		get_template_part(
			'template-parts/common/card-large-image',
			'card-large-image',
			array(
				'post_obj'    => array_slice( $category_posts, 0, 1 )[0],
				'image_size'  => 690,
				'image_ratio' => '1x1',
				'modifier'    => 'home-category__featured-post',
			)
		);
		?>
		<div class="row home-category__featured-grid">
			<?php
			// @codingStandardsIgnoreLine
			foreach ( array_slice( $category_posts, 1, 4 ) as $post ) {
				setup_postdata( $post );
				?>
			<div class="col-sm-3">
				<?php
				get_template_part(
					'template-parts/common/card-top-image',
					'card-top-image',
					array(
						'image_width' => 227,
						'modifier'    => 'home-category__featured-grid-post',
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

