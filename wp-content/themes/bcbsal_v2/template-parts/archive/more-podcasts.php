<?php
/**
 * More Collections module
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$modifier          = $args['modifier'] ?? '';
$section_title     = $args['section_title'] ?? '';
$selected_podcasts = $args['selected_podcasts'] ? get_post_ids_by_urls( $args['selected_podcasts'] ) : array();
$featured_podcast  = $args['featured'] ?? '';

// all podcasts.
if ( ! $selected_podcasts ) {
	$post_ids = array_map(
		function ( $post ) use ( $featured_podcast ) {
			$skip = $featured_podcast ? parse_slug_from_url( $featured_podcast ) : '';

			if ( $featured_podcast && $post !== $skip ) {
				return get_post_id_by_slug( $post );
			}
		},
		surge_category_group_by_slug( 'podcasts', true )
	);

	$offset   = 6;
	$podcasts = new WP_Query(
		array(
			'posts_per_page'      => $offset,
			'post_status'         => 'publish',
			'allow_ignored_posts' => true,
			'post__in'            => $post_ids,
		)
	);
} else {
	// posts query.
	$offset   = 3;
	$podcasts = new WP_Query(
		array(
			'posts_per_page'      => $offset,
			'post__in'            => $selected_podcasts,
			'post_status'         => 'publish',
			'allow_ignored_posts' => true,
			'order'               => 'post__in',
		)
	);
}

$total = $podcasts->found_posts;

if ( $total > 0 ) { ?>
<div class="archive-more-podcasts <?php echo esc_attr( $modifier ); ?>">
	<div class="container">
		<h2 class="sq-section-title"><?php echo esc_attr( $section_title ); ?></h2>
		<div class="row archive-more-podcasts-posts" data-offset="<?php echo esc_attr( $offset ); ?>" data-total="<?php echo esc_attr( $total ); ?>" data-featured="<?php echo esc_attr( $featured_podcast ); ?>">
			<?php
			while ( $podcasts->have_posts() ) {
				$podcasts->the_post();
				?>
				<div class="col-sm-4">
					<?php
					get_template_part(
						'template-parts/common/card-no-meta',
						'card-no-meta',
						array(
							'image_size' => 375,
							'modifier'   => 'archive-more-podcasts-posts__post',
						)
					);
					?>
				</div>
				<?php
			} wp_reset_postdata();
			?>
		</div>
		<?php if ( $total > 6 ) { ?>
			<div class="archive-more-podcasts-load-more">
				<button class="archive-more-podcasts-load-more__button button">Load more podcasts</button>
			</div>
		<?php } ?>
	</div>
</div>
	<?php
}
