<?php
/**
 * Most recent section of the home template
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// posts query.
$recent_posts = new WP_Query(
	array(
		'posts_per_page' => 6,
		'post_status'    => 'publish',
	)
);

$total = $recent_posts->found_posts;

if ( $total > 0 ) { ?>
<div class="home-most-recent">
	<div class="container">
		<h2 class="sq-section-title"><?php esc_html_e( 'Most Recent', 'square' ); ?></h2>
		<?php
		while ( $recent_posts->have_posts() ) {
			$recent_posts->the_post();
			get_template_part(
				'template-parts/common/list-item',
				'list-item',
				array(
					'image_size' => 103,
					'modifier'   => 'home-most-recent__post',
				)
			);
		}
		wp_reset_postdata();
		?>
	</div>
</div>
	<?php
}
