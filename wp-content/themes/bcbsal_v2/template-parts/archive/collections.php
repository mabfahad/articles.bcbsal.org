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

$modifier             = $args['modifier'] ?? '';
$section_title        = $args['section_title'] ?? '';
$selected_collections = $args['selected_collections'] ? get_post_ids_by_urls( $args['selected_collections'] ) : array();
$featured_collection  = $args['featured'] ?? '';

// all collections.
if ( ! $selected_collections ) {
	$post_ids = array_map(
		function ( $post ) use ( $featured_collection ) {
			$skip = $featured_collection ? parse_slug_from_url( $featured_collection ) : '';

			if ( $featured_collection && $post !== $skip ) {
				return get_post_id_by_slug( $post );
			}
		},
		surge_category_group_by_slug( 'collections', true )
	);

	$offset      = 6;
	$collections = new WP_Query(
		array(
			'posts_per_page'      => $offset,
			'post_status'         => 'publish',
			'allow_ignored_posts' => true,
			'post__in'            => $post_ids,
		)
	);
} else {
	// posts query.
	$offset      = 3;
	$collections = new WP_Query(
		array(
			'posts_per_page'      => $offset,
			'post__in'            => $selected_collections,
			'post_status'         => 'publish',
			'allow_ignored_posts' => true,
			'order'               => 'post__in',
		)
	);
}

$total = $collections->found_posts;

if ( $total > 0 ) { ?>
<div class="archive-collections <?php echo esc_attr( $modifier ); ?>">
	<div class="container">
		<h2 class="sq-section-title"><?php echo esc_attr( $section_title ); ?></h2>
		<div class="row archive-collections-posts" data-offset="<?php echo esc_attr( $offset ); ?>" data-total="<?php echo esc_attr( $total ); ?>" data-featured="<?php echo esc_attr( $featured_collection ); ?>">
			<?php
			while ( $collections->have_posts() ) {
				$collections->the_post();
				?>
				<div class="col-sm-4">
					<?php
					get_template_part(
						'template-parts/common/card-no-meta',
						'card-no-meta',
						array(
							'image_size' => 375,
							'modifier'   => 'archive-collections-posts__post',
						)
					);
					?>
				</div>
				<?php
			} wp_reset_postdata();
			?>
		</div>
		<?php if ( $total > 6 ) { ?>
			<div class="archive-collections-load-more">
				<button class="archive-collections-load-more__button button">Load more collections</button>
			</div>
		<?php } ?>
	</div>
</div>
	<?php
}
