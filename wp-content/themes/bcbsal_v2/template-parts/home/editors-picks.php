<?php
/**
 * Editor's Pick section of the home template
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$left_column_post_ids = get_post_ids_by_urls( $args['left_posts'] );
$collection_post_id   = get_post_id_by_slug( $args['collection'] );
$right_column_post_id = get_post_id_by_slug( $args['right_post'] );
$section_desc         = $args['section_description'] ?? '';

// posts query.
$editors_picks = new WP_Query(
	array(
		'post__in'       => $left_column_post_ids,
		'posts_per_page' => 4,
		'post_status'    => 'publish',
		'order'          => 'post__in',
	)
);

$collection      = get_post( $collection_post_id );
$collection_deck = get_post_deck( $collection->ID );
?>
<div class="home-editors-picks">
	<div class="container">
		<h2 class="sq-section-title"><?php echo esc_html( 'Editor’s picks' ); ?></h2>
		<?php if ( $section_desc ) { ?>
			<p class="home-editors-picks__description"><?php echo esc_html( $section_desc ); ?></p>
		<?php } ?>
		<div class="row">
			<div class="col-sm-4 home-editors-picks-left">
				<?php
				while ( $editors_picks->have_posts() ) {
					$editors_picks->the_post();
					get_template_part(
						'template-parts/common/card-no-image',
						'card-no-image-item',
						array(
							'modifier' => 'home-editors-picks-left__post',
						)
					);
				}
				wp_reset_postdata();
				?>
			</div>
			<div class="col-sm-4 home-editors-picks-middle">
				<div class="home-editors-picks-middle__post">
					<div class="home-editors-picks-middle__post-image">
						<a href="<?php echo esc_url( get_custom_post_permalink( $collection->ID ) ); ?>">
							<img src="<?php echo esc_url( nc_image_by_ratio( 376, true, '3x2', $collection->ID ) ); ?>" alt="<?php echo esc_attr( $collection->post_title ); ?>">
						</a>
					</div>
					<div class="home-editors-picks-middle__post-content">
						<div class="home-editors-picks-middle-wrapper">
							<span class="home-editors-picks-middle__post-type">Collection</span>
							<h2 class="home-editors-picks-middle__post-title">
								<a href="<?php echo esc_url( get_custom_post_permalink( $collection->ID ) ); ?>"><?php echo esc_attr( $collection->post_title ); ?></a>
							</h2>
							<?php if ( $collection_deck ) { ?>
								<p class="home-editors-picks-middle__post-deck"><?php echo esc_attr( $collection_deck ); ?></p>
							<?php } ?>
						</div>
						<div class="home-editors-picks-middle__post-button">
							<a href="<?php echo esc_url( get_custom_post_permalink( $collection->ID ) ); ?>" class="home-editors-picks-middle__post-explore-url button">
								<?php esc_html_e( 'Explore collection', 'square' ); ?>
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-4 home-editors-picks-right">
				<?php
				get_template_part(
					'template-parts/common/card-large-image',
					'card-large-image',
					array(
						'post_obj'    => get_post( $right_column_post_id ),
						'image_size'  => 376,
						'image_ratio' => '1x1',
						'modifier'    => 'home-editors-picks-right__post',
					)
				);
				?>
			</div>
		</div>
	</div>
</div>
