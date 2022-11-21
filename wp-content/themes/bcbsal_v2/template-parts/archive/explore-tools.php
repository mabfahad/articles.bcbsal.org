<?php
/**
 * Explore more tools module for archive page
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// tools query.
$offset = 9;
$tools  = new WP_Query(
	array(
		'posts_per_page' => $offset,
		'post_status'    => 'publish',
		'cat'            => get_category_by_slug( 'tools' )->term_id,
	)
);

$total = $tools->found_posts;
?>

<div class="archive-explore-tools">
	<div class="container">
		<h2 class="sq-section-title"><?php esc_html_e( 'Explore tools', 'square' ); ?></h2>
		<?php if ( $total > 0 ) { ?>
		<div class="archive-explore-tools-posts" data-offset="<?php echo esc_attr( $offset ); ?>" data-total="<?php echo esc_attr( $total ); ?>">
			<?php
			while ( $tools->have_posts() ) {
				$tools->the_post();
				get_template_part(
					'template-parts/common/list-item',
					'list-item',
					array(
						'image_size' => 168,
						'modifier'   => 'archive-explore-tools-posts__post',
					)
				);
			}
			wp_reset_postdata();
		}
		?>
		</div>
		<?php if ( $total > 9 ) { ?>
			<div class="archive-explore-tools-load-more">
				<button class="archive-explore-tools-load-more__button button">Load more tools</button>
			</div>
		<?php } ?>
	</div>

</div>
