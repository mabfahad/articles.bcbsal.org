<?php
/**
 * Case studies module for archive page
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$section_desc = $args['section_description'] ?? '';

// tools query.
$case_studies_category = get_category_by_slug( 'case-studies' );
$case_studies          = new WP_Query(
	array(
		'posts_per_page' => 3,
		'post_status'    => 'publish',
		'cat'            => $case_studies_category->term_id,
	)
);

$total = $case_studies->found_posts;

if ( $total > 0 ) { ?>
<div class="archive-case-studies">
	<div class="container">
		<h2 class="sq-section-title"><?php esc_html_e( 'Case Studies', 'square' ); ?> <a href="<?php echo esc_url( get_category_link( $case_studies_category ) ); ?>">View all</a></h2>
		<?php if ( $section_desc ) { ?>
			<p class="archive-case-studies__description"><?php echo esc_html( $section_desc ); ?></p>
		<?php } ?>
		<div class="row archive-case-studies__posts">
			<?php
			// @codingStandardsIgnoreLine
			while ( $case_studies->have_posts() ) { ?>
				<div class="col-sm-4">
				<?php
				$case_studies->the_post();
				?>
				<?php
				get_template_part(
					'template-parts/common/card-top-image',
					'card-top-image',
					array(
						'image_size' => 103,
						'modifier'   => 'archive-case-studies__post card-top-image--top-mobile',
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
