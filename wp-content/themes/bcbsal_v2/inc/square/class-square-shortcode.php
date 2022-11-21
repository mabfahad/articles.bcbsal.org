<?php
/**
 * Square: Shortcode library
 *
 * @package Square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Square_Shortcode
 *
 * Square shortcodes for content hubs.
 *
 * @package square
 * @since 1.0.0
 */
class Square_Shortcode {
	/**
	 * Shortcode to handle knotch
	 *
	 * @return string
	 */
	public static function square_knotch(): string {
		return '<div class="knotch_placeholder"></div>';
	}

	/**
	 * Shortcode to define disclaimer
	 *
	 * @param array  $atts all the shortcode attributes.
	 * @param string $content nested shortcode content.
	 *
	 * @return string
	 */
	public static function square_disclaimer( $atts, string $content ): string {
		return '<div class="sq-disclaimer">' . do_shortcode( $content ) . '</div>';
	}

	/**
	 * Shortcode to handle cite
	 *
	 * @param array  $atts all the shortcode attributes.
	 * @param string $content nested shortcode content.
	 *
	 * @return string
	 */
	public static function square_cite( $atts, string $content ): string {
		return '<div class="sq-cite">' . do_shortcode( $content ) . '</div>';
	}

	/**
	 * Shortcode to define notes
	 *
	 * @param array  $atts all the shortcode attributes.
	 * @param string $content nested shortcode content.
	 *
	 * @return string
	 */
	public static function square_notes( $atts, string $content ): string {
		return '<div class="sq-notes"><strong>Editor’s note</strong>' . do_shortcode( $content ) . '</div>';
	}

	/**
	 * Shortcode to load YouTube from Welcome
	 *
	 * @param array  $atts all the shortcode attributes.
	 * @param string $content nested shortcode content.
	 *
	 * @return string
	 */
	public static function square_youtube_video( $atts, string $content ): string {
		if ( isset( $atts['id'] ) && $atts['id'] ) {
			return '<iframe class="sq-youtube" width="560" height="315" src="https://www.youtube.com/embed/' . trim( $atts['id'] ) . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
		}
	}

	/**
	 * Shortcode to define full-width items
	 *
	 * @param array  $atts all the shortcode attributes.
	 * @param string $content nested shortcode content.
	 *
	 * @return string
	 */
	public static function square_full_width( $atts, string $content ): string {
		return '<div class="sq-full-width">' . do_shortcode( $content ) . '</div>';
	}

	/**
	 * Shortcode to define Callout item
	 *
	 * @param array  $atts all the shortcode attributes.
	 * @param string $content nested shortcode content.
	 *
	 * @return string
	 */
	public static function square_callout( $atts, string $content ): string {
		return '<blockquote class="sq-callout">' . $content . '</blockquote>';
	}

	/**
	 * Shortcode to define Pullquote item
	 *
	 * @param array  $atts all the shortcode attributes.
	 * @param string $content nested shortcode content.
	 *
	 * @return string
	 */
	public static function square_pullquote( array $atts, string $content ): string {
		$output = '<blockquote class="sq-pullquote"><p>' . $content . '&rdquo;</p>';
		if ( isset( $atts['name'] ) && $atts['name'] ) {
			$output .= '<cite><span class="cite-name">' . $atts['name'] . '</span>';
			if ( isset( $atts['title'] ) && $atts['title'] ) {
				$output .= '<span class="cite-title"> &rarr; ' . $atts['title'] . '</span>';
			}
			$output .= '</cite>';
		}
		$output .= '</blockquote>';
		return $output;
	}

	/**
	 * Shortcode to define Image with caption
	 *
	 * @param array  $atts all the shortcode attributes.
	 * @param string $content nested shortcode content.
	 *
	 * @return string
	 */
	public static function square_image( array $atts, string $content ): string {
		$output = '<figure class="sq-image-caption">' . $content;
		if ( isset( $atts['caption'] ) && $atts['caption'] ) {
			$output .= '<figcaption>' . $atts['caption'] . '</figcaption>';
		}
		$output .= '</figure>';
		return $output;
	}

	/**
	 * Shortcode to define Gallery item
	 *
	 * @param array  $atts all the shortcode attributes.
	 * @param string $content nested shortcode content.
	 *
	 * @return string
	 */
	public static function square_gallery( array $atts, string $content ): string {
		preg_match_all( '/<img.*?src="[^"]+".*?>/', $content, $images, PREG_PATTERN_ORDER );
		$items = array();
		foreach ( $images[0] as $img ) {
			$items[] = '<figure>' . $img . '</figure>';
		}
		$output = '<figure class="sq-gallery sq-gallery--' . count( $items ) . '-items"><div class="sq-gallery__inner">' . implode( '', $items );
		if ( isset( $atts['caption'] ) && $atts['caption'] ) {
			$output .= '<figcaption>' . $atts['caption'] . '</figcaption>';
		}
		$output .= '</div></figure>';
		return $output;
	}

	/**
	 * Shortcode to define get started
	 *
	 * @return string
	 */
	public static function square_get_started(): string {
		ob_start();
		get_template_part( 'template-parts/common/get-started', 'get-started', array( 'modifier' => 'sq-get-started' ) );
		return ob_get_clean();
	}

	/**
	 * Shortcode to define left-aligned CTA
	 *
	 * @param mixed $atts all the shortcode attributes.
	 *
	 * @return string|null
	 */
	public static function square_cta( mixed $atts ): ?string {
		if ( isset( $atts['id'] ) ) {
			$cta = get_posts(
				array(
					'numberposts'         => 1,
					'category'            => get_category_by_slug( 'hub-cta' )->term_id,
					// @codingStandardsIgnoreLine
					'meta_query'          => array(
						array(
							'key'     => 'CTA ID',
							'value'   => esc_attr( $atts['id'] ),
							'compare' => 'LIKE',
						),
					),
					'allow_ignored_posts' => true,
					'post_status'         => 'publish',
				)
			);
		} else {
			$article_cat = get_post_category()->term_id;
			$cta         = get_posts(
				array(
					'post_type'      => 'post',
					'post_status'    => 'publish',
					'posts_per_page' => 1,
					'category__and'  => array( get_category_by_slug( 'hub-cta' )->term_id, $article_cat ),
				)
			);
		}

		$cta_id          = $cta[0]->ID ?? 0;
		$cta_image       = nc_image_by_ratio( 272, true, '1x1', $cta_id );
		$cta_button_text = get_post_meta( $cta_id, 'CTA Button Text', true );
		$cta_url         = get_post_meta( $cta_id, 'CTA URL', true );

		if ( $cta_id ) {
			ob_start(); ?>

			<?php if ( isset( $atts['inline'] ) ) { ?>
				<div class="sq-cta <?php echo esc_attr( 'sq-cta--inline' ); ?>">
					<a href="<?php echo esc_url( $cta_url ); ?>" target="_blank" rel="noopener noreferrer" class="sq-cta__image">
						<img src="<?php echo esc_url( $cta_image ); ?>" alt="<?php echo esc_html( get_the_title( $cta_id ) ); ?>">
					</a>
					<div class="sq-cta__text">
						<h3 class="sq-cta__title">
							<a href="<?php echo esc_url( $cta_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( get_the_title( $cta_id ) ); ?></a>
						</h3>
						<a href="<?php echo esc_url( $cta_url ); ?>" target="_blank" rel="noopener noreferrer" class="sq-cta__image sq-cta__image--mobile">
							<img src="<?php echo esc_url( $cta_image ); ?>" alt="<?php echo esc_html( get_the_title( $cta_id ) ); ?>">
						</a>
						<div class="sq-cta__button">
							<a href="<?php echo esc_url( $cta_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button--light"><?php echo esc_html( $cta_button_text ? $cta_button_text : 'Learn more' ); ?> -/^</a>
						</div>
					</div>
				</div>
			<?php } else { ?>
				<div class="sq-cta">
					<a href="<?php echo esc_url( $cta_url ); ?>" target="_blank" rel="noopener noreferrer" class="sq-cta__image">
						<img src="<?php echo esc_url( $cta_image ); ?>" alt="<?php echo esc_html( get_the_title( $cta_id ) ); ?>">
					</a>
					<h3 class="sq-cta__title">
						<a href="<?php echo esc_url( $cta_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( get_the_title( $cta_id ) ); ?></a>
					</h3>
					<div class="sq-cta__button">
						<a href="<?php echo esc_url( $cta_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button--light"><?php echo esc_html( $cta_button_text ? $cta_button_text : 'Learn more' ); ?> -/^</a>
					</div>
				</div>
				<?php
			}

			return ob_get_clean();
		}

		return null;
	}

	/**
	 * Shortcode to define large number data viz
	 *
	 * @param array  $atts all the shortcode attributes.
	 * @param string $content nested shortcode content.
	 *
	 * @return string
	 */
	public static function square_data_viz( array $atts, string $content ): string {
		ob_start();
		if ( isset( $atts['big-text'] ) ) {
			?>
			<div class="sq-data-viz">
				<p class="sq-data-viz__big-text"><?php echo esc_html( $atts['big-text'] ); ?></p>
				<?php if ( $content ) { ?>
					<p class="sq-data-viz__small-text"><?php echo esc_html( $content ); ?></p>
				<?php } ?>
			</div>
			<?php
		}
		return ob_get_clean();
	}

	/**
	 * Shortcode to define key takeaways
	 *
	 * @param array  $atts all the shortcode attributes.
	 * @param string $content nested shortcode content.
	 *
	 * @return string
	 */
	public static function square_key_takeaways( $atts, string $content ): string {
		ob_start();
		if ( $content ) {
			?>
			<div class="sq-key-takeaways">
				<h2 class="sq-key-takeaways__title">Key takeaways</h2>
				<?php
				// @codingStandardsIgnoreLine
				echo $content;
				?>
			</div>
			<?php
		}
		return ob_get_clean();
	}

	/**
	 * Shortcode to parse table for left-aligned image and right-aligned text
	 *
	 * @param array  $atts all the shortcode attributes.
	 * @param string $content nested shortcode content.
	 *
	 * @return string
	 */
	public static function square_image_left( $atts, string $content ): string {
		ob_start();
		if ( $content ) {
			$data = array();
			$dom  = new DOMDocument();
			$dom->loadHTML( $content );
			// @codingStandardsIgnoreStart
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput       = false;
			// @codingStandardsIgnoreEnd
			$rows = $dom->getElementsByTagName( 'table' )->item( 0 )->getElementsByTagName( 'tr' );
			?>
			<div class="sq-image-left-rows">
				<?php foreach ( $rows as $row ) { ?>
					<div class="sq-image-left">
					<?php
					$cols = $row->getElementsByTagName( 'td' );
						// @codingStandardsIgnoreLine ?>
						<div class="sq-image-left__image"><?php echo htmlspecialchars_decode( get_inner_html( $cols->item( 0 ) ) ); ?></div>
						<div class="sq-image-left__content">
						<?php
						// @codingStandardsIgnoreLine
						echo htmlspecialchars_decode( get_inner_html( $cols->item( 1 ) ) );
						?>
						</div>
					</div>
				<?php } ?>
			</div>
			<?php
		}
		return ob_get_clean();
	}
}

/**
 * Register all shortcodes
 *
 * @package square
 * @since 1.0.0
 */

// Shortcode: [sq-disclaimer].
add_shortcode( 'sq-disclaimer', array( 'Square_Shortcode', 'square_disclaimer' ) );

// Shortcode: [sq-knotch].
add_shortcode( 'sq-knotch', array( 'Square_Shortcode', 'square_knotch' ) );

// Shortcode: [sq-cite].
add_shortcode( 'sq-cite', array( 'Square_Shortcode', 'square_cite' ) );

// Shortcode: [sq-notes].
add_shortcode( 'sq-notes', array( 'Square_Shortcode', 'square_notes' ) );

// Shortcode: [sq-full-width].
add_shortcode( 'sq-full-width', array( 'Square_Shortcode', 'square_full_width' ) );

// Shortcode: [sq-callout].
add_shortcode( 'sq-callout', array( 'Square_Shortcode', 'square_callout' ) );

// Shortcode: [sq-pullquote].
add_shortcode( 'sq-pullquote', array( 'Square_Shortcode', 'square_pullquote' ) );

// Shortcode: [sq-image].
add_shortcode( 'sq-image', array( 'Square_Shortcode', 'square_image' ) );

// Shortcode: [sq-gallery].
add_shortcode( 'sq-gallery', array( 'Square_Shortcode', 'square_gallery' ) );

// Shortcode: [sq-cta].
add_shortcode( 'sq-cta', array( 'Square_Shortcode', 'square_cta' ) );

// Shortcode: [sq-get-started].
add_shortcode( 'sq-get-started', array( 'Square_Shortcode', 'square_get_started' ) );

// Shortcode: [sq-data-viz].
add_shortcode( 'sq-data-viz', array( 'Square_Shortcode', 'square_data_viz' ) );

// Shortcode: [sq-key-takeaways].
add_shortcode( 'sq-key-takeaways', array( 'Square_Shortcode', 'square_key_takeaways' ) );

// Shortcode: [sq-image-left].
add_shortcode( 'sq-image-left', array( 'Square_Shortcode', 'square_image_left' ) );

// Shortcode: [sq-youtube].
add_shortcode( 'sq-youtube', array( 'Square_Shortcode', 'square_youtube_video' ) );
