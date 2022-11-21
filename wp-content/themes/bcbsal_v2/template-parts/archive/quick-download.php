<?php
/**
 * Quick download module for archive page
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$post_ids = get_post_ids_by_urls( $args['quick_downloads'] );

$tools = get_posts(
	array(
		'posts_per_page' => 4,
		'post__in'       => $post_ids,
		'post_status'    => 'publish',
		'order'          => 'post__in',
	)
);

if ( $tools && count( $tools ) > 0 ) { ?>
	<div class="archive-quick-download">
		<div class="container">
			<h2 class="sq-section-title"><?php esc_html_e( 'Download Templates', 'square' ); ?></h2>

			<div class="row archive-quick-download__posts">
				<?php
				// @codingStandardsIgnoreLine
				foreach ( $tools as $post ) {
					setup_postdata( $post );
					?>
					<div class="col-sm-3">
						<?php
						get_template_part(
							'template-parts/common/card-download',
							'card-download',
							array(
								'modifier' => 'archive-quick-download__post',
								'slug'     => $post->post_name,
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
