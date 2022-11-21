<?php
/**
 * Tools module for archive page
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$section_desc = $args['section_description'] ?? '';

// tools query.
$tools_category = get_category_by_slug( 'tools' );
$tools          = new WP_Query(
	array(
		'posts_per_page' => 3,
		'post_status'    => 'publish',
		'cat'            => $tools_category->term_id,
	)
);

$total = $tools->found_posts;

if ( $total > 0 ) { ?>
<div class="archive-tools">
	<div class="container">
		<h2 class="sq-section-title"><?php esc_html_e( 'Tools', 'square' ); ?> <a href="<?php echo esc_url( get_category_link( $tools_category ) ); ?>">View all</a></h2>
		<?php if ( $section_desc ) { ?>
			<p class="archive-tools__description"><?php echo esc_html( $section_desc ); ?></p>
		<?php } ?>
		<div class="row archive-tools__posts">
			<?php
			// @codingStandardsIgnoreLine
			while ( $tools->have_posts() ) {
				$tools->the_post();
				?>
				<div class="col-sm-4">
				<?php
				get_template_part(
					'template-parts/common/card-top-image',
					'card-top-image',
					array(
						'image_size'  => 376,
						'image_ratio' => '3x2',
						'modifier'    => 'archive-tools__post card-top-image--top-mobile',
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
