<?php
/**
 * Collection module for archive page
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$modifier      = $args['modifier'] ?? '';
$section_title = $args['section_title'] ?? '';
$collection    = parse_slug_from_url( $args['collection'] );

// collection query.
$collection_posts = new WP_Query(
	array(
		'posts_per_page' => 3,
		'cat'            => get_category_by_slug( $collection )->term_id,
		'post_status'    => 'publish',
	)
);

$collection_post = get_post( get_post_id_by_slug( $collection ) );
$total           = $collection_posts->found_posts;

if ( $total > 0 ) { ?>
<div class="archive-collection <?php echo esc_attr( $modifier ); ?>">
	<div class="container">
		<h2 class="sq-section-title"><?php echo esc_attr( $section_title ); ?></h2>
		<div class="row">
			<div class="col-sm-6">
				<div class="archive-collection__content">
					<h3 class="archive-collection__title">
						<a href="<?php echo esc_url( get_custom_post_permalink( $collection_post->ID ) ); ?>"><?php echo esc_attr( $collection_post->post_title ); ?></a>
					</h3>
					<p class="archive-collection__description"><?php echo esc_html( get_post_deck( $collection_post->ID ) ); ?></p>
					<a href="<?php echo esc_url( get_custom_post_permalink( $collection_post->ID ) ); ?>" class="button archive-collection__button archive-collection__button--desktop">See full collection</a>
				</div>
			</div>
			<div class="col-sm-6">
				<?php
				while ( $collection_posts->have_posts() ) {
					$collection_posts->the_post();
					?>
					<?php
					get_template_part(
						'template-parts/common/list-item',
						'list-item',
						array(
							'image_size' => 103,
							'modifier'   => 'archive-collection-most-recent__post',
						)
					);
				} wp_reset_postdata();
				?>
				<a href="<?php echo esc_url( get_custom_post_permalink( $collection_post->ID ) ); ?>" class="button archive-collection__button archive-collection__button--mobile">See full collection</a>
			</div>
		</div>
	</div>
</div>
	<?php
}
