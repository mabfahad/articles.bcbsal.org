<?php
/**
 * Products module for archive page
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$section_title = $args['section_title'] ?? '';
$section_desc  = $args['section_description'] ?? '';
$products      = get_square_products( $args['products'] );

if ( $products && count( $products ) > 0 ) { ?>
<div class="archive-products">
	<div class="container">
		<h2 class="sq-section-title"><?php echo esc_attr( $section_title ); ?>
			<?php if ( ! has_category( 'case-studies', get_the_ID() ) ) { ?>
				<a href="#">View all</a>
			<?php } ?>
		</h2>
		<?php if ( $section_desc ) { ?>
		<p class="archive-products__description"><?php echo esc_html( $section_desc ); ?></p>
		<?php } ?>
		<div class="row archive-products__posts">
			<?php
			// @codingStandardsIgnoreLine
			foreach ( $products as $post ) {
				setup_postdata( $post );
				?>
				<div class="col-md-4 col-sm-6">
					<?php
					get_template_part(
						'template-parts/common/card-product',
						'card-product',
						array(
							'image_size' => 104,
							'modifier'   => 'archive-products__post',
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
