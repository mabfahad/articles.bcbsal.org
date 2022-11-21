<?php
/**
 * Articles collection
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$articles = new WP_Query(
	array(
		'posts_per_page'   => -1,
		'post_status'      => 'publish',
		'category__not_in' => array_map( 'get_cat_ID', MAPPING_CATEGORIES ),
		'cat'              => get_queried_object()->term_id,
	)
);

$total = $articles->found_posts;

if ( $total > 0 ) { ?>
	<div class="archive-collection-articles">
		<div class="container">
			<div class="archive-collection-articles__posts">
				<?php
				$post_count = 1;
				while ( $articles->have_posts() ) {
					$articles->the_post();
					if ( 3 === $post_count ) {
						get_template_part(
							'template-parts/common/list-item-product',
							'list-item-product',
							array(
								'modifier'    => 'list-item-product',
								'image_width' => 168,
								'product'     => $args['product'] ?? '',
							)
						);
					}
					get_template_part(
						'template-parts/common/list-item',
						'list-item',
						array(
							'image_size' => 168,
							'modifier'   => 'archive-collection-articles__post',
							'has_deck'   => true,
						)
					);
					$post_count++;
					?>
				<?php } wp_reset_postdata(); ?>
			</div>
		</div>
	</div>
	<?php
}
