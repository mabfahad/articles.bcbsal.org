<?php
/**
 * Podcasts section of the home template
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

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
<div class="home-podcasts">
	<div class="container">
		<h2 class="sq-section-title"><?php esc_html_e( 'Podcast Episodes', 'square' ); ?> <a href="<?php echo esc_url( home_url( '/category/podcasts/' ) ); ?>">View all</a></h2>
		<div class="row">
			<?php
			while ( $podcasts->have_posts() ) {
				$podcasts->the_post();
				?>
				<div class="col-md-6">
				<?php get_template_part( 'template-parts/common/card-left-image', 'card-left-image', array( 'modifier' => 'home-podcasts__item' ) ); ?>
				</div>
				<?php
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</div>
	<?php
}
