<?php
/**
 * Podcasts module for archive page
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$section_desc = $args['section_description'] ?? '';
$podcasts_ids = get_post_ids_by_urls( $args['podcasts'] );

// posts query.
$podcasts = new WP_Query(
	array(
		'category__and'  => surge_category_group_by_slug( 'podcasts' ),
		'posts_per_page' => 4,
		'post__in'       => $podcasts_ids,
		'post_status'    => 'publish',
		'order'          => 'post__in',
	)
);

$total = $podcasts->found_posts;

if ( $total > 0 ) { ?>
<div class="archive-podcasts">
	<div class="container">
		<h2 class="sq-section-title"><?php esc_html_e( 'Podcast Episodes', 'square' ); ?> <a href="<?php echo esc_url( home_url( '/podcasts' ) ); ?>">View all</a></h2>
		<?php if ( $section_desc ) { ?>
			<p class="archive-podcasts__description"><?php echo esc_html( $section_desc ); ?></p>
		<?php } ?>
		<div class="row archive-podcasts__posts">
			<?php
			while ( $podcasts->have_posts() ) {
				?>
				<div class="col-md-6">
					<?php
					$podcasts->the_post();
					?>
					<?php
					get_template_part(
						'template-parts/common/card-left-image',
						'card-left-image',
						array(
							'image_size' => 103,
							'modifier'   => 'archive-podcasts__post',
						)
					);
					?>
				</div>
				<?php
			} wp_reset_postdata();
			?>
		</div>
	</div>
</div>
	<?php
}
