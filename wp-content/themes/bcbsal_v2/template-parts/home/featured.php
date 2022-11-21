<?php
/**
 * Featured hero section of the home template
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$modifier         = $args['modifier'] ?? '';
$image_size       = $args['image_size'] ?? 650;
$image_ratio      = $args['image_ratio'] ?? '3x2';
$featured_post_id = get_post_id_by_slug( $args['post'] );
$section_title    = $args['section_title'] ?? '';
?>

<div class="home-featured <?php echo esc_attr( $modifier ); ?>">
	<div class="container">
		<?php if ( $section_title ) { ?>
			<h2 class="sq-section-title"><?php echo esc_attr( $section_title ); ?></h2>
			<?php
		}
		get_template_part(
			'template-parts/common/card-large-image',
			'card-large-image',
			array(
				'post_obj'    => get_post( $featured_post_id ),
				'image_size'  => $image_size,
				'image_ratio' => $image_ratio,
				'modifier'    => 'home-featured__post',
			)
		);
		?>
	</div>
</div>

