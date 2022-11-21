<?php
/**
 * Masonry article layout in category archive
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$section_title  = $args['section_title'] ?? '';
$modifier       = $args['modifier'] ?? '';
$image_size     = $args['image_size'] ?? 103;
$tags_group     = $args['tags_group'] ?? '';
$tags           = $args['tags'] ?? array();
$per_page_posts = $args['per_page'] ?? 6;
$load_more_text = $args['load_more_text'] ?? 'Load more articles';
$cta            = $args['cta'] ?? array();

// posts query.
$offset   = $per_page_posts;
$articles = new WP_Query(
	array(
		'posts_per_page'   => $per_page_posts,
		'post_status'      => 'publish',
		'category__not_in' => array_map( 'get_cat_ID', MAPPING_CATEGORIES ),
		'cat'              => get_queried_object()->term_id,
	)
);

$total = $articles->found_posts;

if ( $total > 0 ) { ?>
<div class="archive-most-recent <?php echo esc_attr( $modifier ); ?>">
	<div class="container">
		<h2 class="sq-section-title"><?php echo esc_attr( $section_title ); ?></h2>
		<?php
		get_template_part(
			'template-parts/archive/filter-by-tags',
			'filter-by-tags',
			array(
				'modifier'    => 'archive-most-recent-filter',
				'total'       => $total,
				'tags'        => $tags,
				'tags_group'  => $tags_group,
				'category_id' => get_queried_object()->term_id,
			)
		);
		?>
		<div class="archive-most-recent__posts filter-by-tags__posts" data-tag="all" data-per-page="<?php echo esc_attr( $per_page_posts ); ?>" data-category="<?php echo esc_attr( get_queried_object()->term_id ); ?>" data-total="<?php echo esc_attr( $total ); ?>" data-offset="<?php echo esc_attr( $offset ); ?>">
			<?php
			$post_count = 1;
			while ( $articles->have_posts() ) {
				$articles->the_post();

				get_template_part(
					'template-parts/common/list-item',
					'list-item',
					array(
						'image_width' => $image_size,
						'modifier'    => 'archive-most-recent__post filter-by-tags__post',
					)
				);

				if ( array_key_exists( $cta['text'] ) && 6 === $post_count ) {
					get_template_part(
						'template-parts/common/contact-sales',
						'contact-sales',
						array(
							'text'   => $cta['text'],
							'button' => $cta['button'],
							'image'  => $cta['image'],
						)
					);
				}

				$post_count++;
				?>
			<?php } wp_reset_postdata(); ?>
		</div>

		<?php if ( $total > $per_page_posts ) { ?>
		<div class="archive-most-recent-load-more filter-by-tags-load-more">
			<button type="button" class="archive-most-recent-load-more__button button">
				<?php echo esc_attr( $load_more_text ); ?>
			</button>
		</div>
		<?php } ?>
	</div>
</div>
	<?php
}
