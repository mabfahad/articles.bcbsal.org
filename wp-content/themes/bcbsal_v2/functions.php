<?php
/**
 * Square functions and definitions
 *
 * @package square
 * @since 1.0.0
 */

use JetBrains\PhpStorm\ArrayShape;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Declare global variables
 */
global $sq;

/**
 * Load all functionalities from Surge & Square
 */
require_once trailingslashit( get_template_directory() ) . 'inc/surge/surge.php';
require_once trailingslashit( get_template_directory() ) . 'inc/square/class-square-shortcode.php';
require_once trailingslashit( get_template_directory() ) . 'inc/square/constants.php';
require_once trailingslashit( get_template_directory() ) . 'inc/square/search-helpers.php';
require_once trailingslashit( get_template_directory() ) . 'ajax-functions.php';
require_once trailingslashit( get_template_directory() ) . 'inc/square/seo.php';

if ( ! isset( $content_width ) ) {
	/* Sets max image width inserted into a post */
	$content_width = 700;
}

if ( ! function_exists( 'image_url' ) ) {
	/**
	 * Get images from assets folder
	 *
	 * @param string $file_name Name of the image file including extension.
	 * @param bool   $convert_webp Convert the image to webp if browser supported.
	 *
	 * @return string
	 */
	function image_url( string $file_name, bool $convert_webp = true ): string {
		if ( $convert_webp && isset( $_SERVER['HTTP_ACCEPT'] ) ) {
			$has_webp_support = stripos( esc_url_raw( wp_unslash( $_SERVER['HTTP_ACCEPT'] ) ), 'image/webp' );

			if ( $has_webp_support ) {
				$file_name = str_replace( array( '.png', '.jpg', '.jpeg' ), '.webp', $file_name );
			}
		}

		return get_stylesheet_directory_uri() . "/assets/images/{$file_name}";
	}
}

if ( ! function_exists( 'read_time' ) ) {
	/**
	 * Get the reading time of a post
	 *
	 * @param int|null $post_id post id.
	 *
	 * @return string
	 */
	function read_time( int $post_id = null ): string {
		$post_id    = $post_id ? $post_id : get_the_ID();
		$word_count = str_word_count( wp_strip_all_tags( $post_id ? get_post( $post_id )->post_content : get_the_content() ) );
		$ttr        = ( round( $word_count / 275 ) < 1 ) ? 1 : round( $word_count / 275 );

		if ( has_category( surge_category_group_by_slug( 'podcasts' ), $post_id ) ) {
			$read_time = get_post_meta( $post_id, 'Read Time', true );

			return ( $read_time ? $read_time : $ttr ) . ' min';
		}

		return $ttr . ' min read';
	}
}

if ( ! function_exists( 'limit_text' ) ) {
	/**
	 * This will limit/trim text by word and add ellipsis at the end of it
	 *
	 * @param string $text Text to truncate.
	 * @param int    $limit Number of words to limit the text to.
	 * @param bool   $flag Adds ellipsis.
	 *
	 * @return string
	 */
	function limit_text( string $text, int $limit, bool $flag = true ): string {
		$text = wp_strip_all_tags( $text );
		if ( str_word_count( $text, 0 ) > $limit ) {
			$words        = explode( ' ', $text );
			$trimmed_text = array_slice( $words, 0, $limit );

			if ( $flag ) {
				return implode( ' ', $trimmed_text ) . ' ...';
			}

			return implode( ' ', $trimmed_text );
		}

		return $text;
	}
}

if ( ! function_exists( 'limit_text_by_characters' ) ) {
	/**
	 * Limit/trim text by character count
	 *
	 * @param string $text Text to truncate.
	 * @param int    $limit Number of characters to limit the text to.
	 *
	 * @return string
	 */
	function limit_text_by_characters( string $text, int $limit ): string {
		$text = wp_strip_all_tags( $text );

		return mb_strimwidth( $text, 0, $limit, '...' );
	}
}

if ( ! function_exists( 'nc_source' ) ) {
	/**
	 * Get source of the article from CMP
	 *
	 * @return string
	 */
	function nc_source(): string {
		return get_post_meta( get_the_ID(), 'nc-source', true );
	}
}

if ( ! function_exists( 'nc_author' ) ) {
	/**
	 * Get author of the article from CMP
	 *
	 * @return string
	 */
	function nc_author(): string {
		return get_post_meta( get_the_ID(), 'nc-author', true );
	}
}

if ( ! function_exists( 'nc_image' ) ) {
	/**
	 * Get the featured image from NewsCred CDN
	 *
	 * @param int      $width Width of the image.
	 * @param int|null $height Height of the image.
	 * @param int      $quality Quality of the image.
	 * @param int|null $post_id Specify post ID if needed.
	 *
	 * @return string
	 */
	function nc_image( int $width, int $height = null, int $quality = 100, int $post_id = null ): string {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}
		// Cdn image url.
		$cdn_url = get_post_meta( $post_id, 'nc-image', true );

		if ( $cdn_url ) {
			$cdn_url = convert_to_welcome_cdn( $cdn_url );

			// Return modified image.
			return $height ? $cdn_url . '?width=' . $width . '&height=' . $height . '&q=' . $quality : $cdn_url . '?width=' . $width . '&q=' . $quality;
		}

		// Add fallback image.
		return image_url( 'placeholder-image.png' );
	}
}

if ( ! function_exists( 'nc_image_by_ratio' ) ) {
	/**
	 * This will return cropped image URL based on ratio
	 *
	 * @param int      $width width of the image.
	 * @param bool     $retina_enabled enable retina support.
	 * @param string   $custom_ratio hardcoded ratio.
	 * @param int|null $post_id id of the post.
	 *
	 * @return string
	 */
	function nc_image_by_ratio( int $width, bool $retina_enabled = true, string $custom_ratio = '', int $post_id = null ): string {
		if ( $retina_enabled ) {
			$width = $width * 2;
		}

		// get image ratio from welcome custom fields.
		$ratio = get_post_meta( $post_id ? $post_id : get_the_ID(), 'Image Ratio', true );

		if ( '' !== $custom_ratio ) {
			$ratio = $custom_ratio;
		}

		if ( '3x4' === $ratio ) {
			$height = ceil( ( $width / 3 ) * 4 );
		} elseif ( '3x2' === $ratio ) {
			$height = ceil( ( $width / 3 ) * 2 );
		} else {
			$height = $width;
		}

		return nc_image( $width, $height, 80, $post_id ? $post_id : get_the_ID() );
	}
}

if ( ! function_exists( 'get_post_category' ) ) {
	/**
	 * This will return the category of the post
	 *
	 * @param int|null $post_id specific post id.
	 *
	 * @return object
	 */
	function get_post_category( int $post_id = null ): object {

		$categories = $post_id ? get_the_category( $post_id ) : get_the_category( get_the_ID() );
		$category   = (object) array();

		foreach ( $categories as $cat ) {
			if ( ! in_array( $cat->slug, IGNORE_CATEGORIES, true ) ) {
				$category_parent = get_ancestors( $cat->term_id, 'category' );
				$category        = ( ! empty( $category_parent ) ) ? get_category( end( $category_parent ) ) : $cat;
				break;
			}
		}

		return $category;
	}
}

if ( ! function_exists( 'get_post_topics' ) ) {
	/**
	 * This will return the tags array of the post
	 *
	 * @param int|null $post_id id of the post.
	 *
	 * @return array|bool
	 */
	function get_post_topics( int $post_id = null ): array|bool {
		return get_the_terms( $post_id ? $post_id : get_the_ID(), 'post_tag' );
	}
}

if ( ! function_exists( 'the_author_image' ) ) {
	/**
	 * This will echo the author image
	 *
	 * @param int    $author_id ID of the author.
	 * @param string $size size of the image.
	 *
	 * @return void
	 */
	function the_author_image( int $author_id, string $size = 'thumbnail' ): void {
		if ( function_exists( 'mt_profile_img' ) ) {
			mt_profile_img(
				$author_id,
				array(
					'size' => $size,
					'attr' => array( 'alt' => get_userdata( $author_id )->display_name ),
					'echo' => true,
				)
			);
		} else {
			echo '<img src="' . esc_url( get_avatar_url( $author_id ) ) . '" alt="' . esc_attr( get_userdata( $author_id )->display_name ) . '" />';
		}
	}
}

if ( ! function_exists( 'parse_slug_from_url' ) ) {
	/**
	 * Parse the URL to get slug or path
	 *
	 * @param string $url full url.
	 *
	 * @return string
	 */
	function parse_slug_from_url( string $url ): string {
		// check if user accidentally inserted a URL instead of slug.
		// then parse the URL to get the slug.
		if ( preg_match( '|^http(s)?://[a-z\d-]+(.[a-z\d-]+)*(:\d+)?(/.*)?$|i', $url ) ) {
			return basename( wp_parse_url( $url, PHP_URL_PATH ) );
		}

		return $url;
	}
}

if ( ! function_exists( 'get_post_id_by_slug' ) ) {
	/**
	 *  Helper function to get post id by slug
	 *
	 * @param string $slug slug of the post.
	 * @param string $status status of the post.
	 *
	 * @return int|bool
	 */
	function get_post_id_by_slug( string $slug, string $status = 'publish' ): int|bool {
		$post = get_posts(
			array(
				'name'                => parse_slug_from_url( $slug ),
				'post_type'           => 'post',
				'post_status'         => $status,
				'allow_ignored_posts' => true,
				'numberposts'         => 1,
			)
		);

		if ( $post ) {
			return $post[0]->ID;
		}

		return false;
	}
}

if ( ! function_exists( 'get_post_ids_by_urls' ) ) {
	/**
	 * This will return post ids by parsing URLs
	 *
	 * @param string $urls list of URLs seperated by |.
	 *
	 * @return array
	 */
	function get_post_ids_by_urls( string $urls ): array {
		$urls     = array_values( array_filter( array_map( 'trim', explode( '|', $urls ) ) ) );
		$post_ids = array();
		foreach ( $urls as $url ) {
			$post_ids[] = get_post_id_by_slug( parse_slug_from_url( $url ) );
		}

		return $post_ids;
	}
}

if ( ! function_exists( 'add_id_to_headings' ) ) {
	/**
	 * Adds id property to h2 inside content for ToC.
	 *
	 * @param string $content The post content.
	 *
	 * @return string
	 */
	function add_id_to_headings( string $content ): string {
		if ( get_post_meta( get_the_ID(), 'Enable Table of Contents', true ) !== 'Yes' ) {
			$content = preg_replace_callback(
				'/<h2.*?>(.*?)<\/h\d>/ims',
				function( $matches ) {
					return '<h2 id="' . sanitize_title( $matches[1] ) . '">' . $matches[1] . '</h2>';
				},
				$content
			);
		}
		return $content;
	}
	add_filter( 'the_content', 'add_id_to_headings' );
}

if ( ! function_exists( 'get_content_type_icon' ) ) {
	/**
	 * Get icons based on the content type
	 *
	 * @param int $post_id id of the post.
	 *
	 * @return string
	 */
	function get_content_type_icon( int $post_id ): string {
		$image_name = '';

		if ( ! is_category( surge_category_group_by_slug( 'podcasts' ) ) && has_category( surge_category_group_by_slug( 'podcasts' ), $post_id ) ) {
			$image_name = 'icon-podcast.svg';
		} elseif ( has_category( 'tools', $post_id ) && ! is_category( 'tools' ) ) {
			$image_name = 'icon-tool.svg';
		}

		return $image_name ? image_url( $image_name ) : '';
	}
}

if ( ! function_exists( 'get_content_type_class' ) ) {
	/**
	 * Get class name based on the content type
	 *
	 * @param int $post_id id of the post.
	 *
	 * @return string
	 */
	function get_content_type_class( int $post_id ): string {
		$class_name = '';

		if ( has_category( surge_category_group_by_slug( 'podcasts' ), $post_id ) ) {
			$class_name = 'sq-category__icon-podcast';
		} elseif ( has_category( 'tools', $post_id ) ) {
			$class_name = 'sq-category__icon-tool';
		}

		return $class_name;
	}
}

if ( ! function_exists( 'get_post_title' ) ) {
	/**
	 * This will return the modified title of the post
	 *
	 * @param int|null $post_id id of the post.
	 *
	 * @return string
	 */
	function get_post_title( int $post_id = null ): string {
		$post_id = $post_id ? $post_id : get_the_ID();

		$title = get_the_title( $post_id );

		// customize the title for podcasts.
		$podcasts_categories = surge_category_group_by_slug( 'podcasts' );
		if ( has_category( $podcasts_categories, $post_id ) ) {
			$categories       = get_the_category( $post_id );
			$podcast_category = '';
			foreach ( $categories as $category ) {
				if ( in_array( $category->slug, $podcasts_categories, true ) ) {
					$podcast_category = $category->name;
					break;
				}
			}

			// season and episode of the podcast.
			$season  = (int) substr( get_post_meta( $post_id, 'Season', true ), -1 );
			$season  = $season > 9 ? 'SN.' . $season : 'SN.0' . $season;
			$episode = (int) substr( get_post_meta( $post_id, 'Episode', true ), -1 );
			$episode = $episode > 9 ? 'EP.' . $episode : 'EP.0' . $episode;

			if ( ! is_category( surge_category_group_by_slug( 'podcasts' ) ) ) {
				return '<span class="sq-title__podcast">' . esc_attr( $podcast_category ) . ' — ' . esc_attr( $season ) . '/' . esc_attr( $episode ) . '</span> ' . esc_attr( $title );
			} elseif ( ! in_array( get_post( $post_id )->post_name, surge_category_group_by_slug( 'podcasts' ), true ) ) {
				return '<span class="sq-title__podcast">' . esc_attr( $season ) . '/' . esc_attr( $episode ) . '</span> ' . esc_attr( $title );
			}
		}

		// customize the title for case studies.
		if ( has_category( 'case-studies', $post_id ) ) {
			$business_name = get_post_meta( $post_id, 'Business Name', true );
			return '<span class="sq-title__case-studies">' . esc_attr( $business_name ) . ' —</span> ' . esc_attr( $title );
		}

		// customize the title for products.
		if ( has_category( 'hub-products', $post_id ) ) {
			$product_title = get_the_title( $post_id );
			if ( str_contains( $product_title, 'Square' ) ) {
				$product_title = str_replace( 'Square', '', $product_title );

				return '<span class="sq-title__product">Square</span> ' . esc_attr( $product_title );
			}

			return $product_title;
		}

		return $title;
	}
}

if ( ! function_exists( 'get_custom_post_permalink' ) ) {
	/**
	 * This will return the permalink for category based pages.
	 *
	 * @param int|null $post_id id of the post.
	 *
	 * @return string
	 */
	function get_custom_post_permalink( int $post_id = null ): string {

		$slug = get_post_field( 'post_name', $post_id ? $post_id : get_the_ID() );

		// check if any category exists with same slug.
		if ( in_array(
			$slug,
			array(
				...surge_category_group_by_slug( 'podcasts', true ),
				...surge_category_group_by_slug( 'collections', true ),
			),
			true
		) ) {
			return get_category_link( get_category_by_slug( $slug ) );
		}

		return get_permalink( $post_id ? $post_id : get_the_ID() );
	}
}

if ( ! function_exists( 'get_post_deck' ) ) {
	/**
	 * This will return the deck of the post
	 *
	 * @param int|null $post_id id of the post.
	 *
	 * @return string
	 */
	function get_post_deck( int $post_id = null ): string {
		return get_post_meta( $post_id ? $post_id : get_the_ID(), 'Deck', true ) ?? '';
	}
}

if ( ! function_exists( 'get_post_authors' ) ) {
	/**
	 * Get author profiles by parsing nc-author. Returns multiple profiles if exist.
	 *
	 * @param int $post_id id of the post.
	 *
	 * @return object|array
	 */
	function get_post_authors( int $post_id ): object|array {
		$nc_author_meta = get_post_meta( $post_id, 'nc-author', true );
		$nc_authors     = str_replace( ',', ' and', $nc_author_meta );
		$authors        = array();
		if ( $nc_authors ) {
			if ( strpos( $nc_authors, 'and' ) ) {
				$author_names = array_map( 'trim', explode( 'and', $nc_authors ) );
			} else {
				$author_names[] = trim( $nc_authors );
			}
			foreach ( $author_names as $name ) {
				if ( get_user_id_by_name( $name ) ) {
					$user_id = get_user_id_by_name( $name );
					if ( $user_id ) {
						$authors[] = get_user_by( 'id', $user_id );
					}
				}
			}
		}
		return $authors;
	}
}

if ( ! function_exists( 'get_user_id_by_name' ) ) {
	/**
	 * Get user ID by name,
	 *
	 * @param string $name name of the user.
	 *
	 * @return int|bool
	 */
	function get_user_id_by_name( string $name ): int|bool {
		$user_query = new WP_User_Query(
			array(
				'search'         => '*' . esc_attr( $name ) . '*',
				'search_columns' => array( 'user_nicename', 'display_name' ),
			)
		);

		$user = $user_query->get_results();

		if ( $user && is_array( $user ) ) {
			return $user[0]->ID;
		}

		return false;
	}
}

if ( ! function_exists( 'check_post_type' ) ) {
	/**
	 * This will check whether the post is article type
	 *
	 * @param string   $post_type post type based on category.
	 * @param string   $template type of the template e.g. single, category, tag, etc.
	 * @param int|null $post_id post id for single type.
	 *
	 * @return bool
	 */
	function check_post_type( string $post_type, string $template = 'single', int $post_id = null ):bool {
		if ( 'podcasts' === $post_type || 'case-studies' === $post_type || 'tools' === $post_type || 'collections' === $post_type ) {
			$categories = surge_category_group_by_slug( $post_type );

			if ( 'single' === $template ) {
				if ( has_category( $categories, $post_id ) ) {
					return true;
				}
			} elseif ( 'category' === $template ) {
				if ( is_category( $categories ) ) {
					return true;
				}
			}
		} elseif ( 'articles' === $post_type ) {
			$podcasts    = surge_category_group_by_slug( 'podcasts' );
			$collections = surge_category_group_by_slug( 'collections' );

			$exclude_categories = array( 'case-studies', 'tools', ...$podcasts, ...$collections );

			if ( ! has_category( $exclude_categories, $post_id ) ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'get_taxonomy_meta' ) ) {
	/**
	 * Get category or tags name with icon
	 *
	 * @param string $class_name class name to modify design.
	 * @param int    $post_id post id.
	 *
	 * @return void
	 */
	function get_taxonomy_meta( string $class_name, int $post_id ): void {
		// [page] [max number of tags] [type].
		// Tag 1 Category.
		// Search 1 Category.
		// Tools 1 Topic.
		// Homepage 1 Category.
		// Podcasts 1 Topic.
		// Collections 1 Category.
		// Collection Pages 1 Category.
		// Case Studies 1 Category.
		// Category Pages 1 Topic.
		global $sq;
		?>
		<div class="sq-taxonomy-wrap">
		<?php
		if ( is_home() || is_author() || is_category( 'podcasts' ) || is_category( 'case-studies' ) || is_tag() || is_search() || is_404() || is_category( surge_category_group_by_slug( 'collections' ) ) || true === $sq['show_taxonomy_category'] ) {
			$category = get_post_category( $post_id );
			?>
				<a class="<?php echo esc_attr( $class_name ); ?> sq-taxonomy__category" href="<?php echo esc_url( get_category_link( $category ) ); ?>">
					<?php
					if ( isset( $category->name ) ) {
						echo esc_attr( $category->name ); }
					?>
				</a>
			<?php
		} elseif ( is_category( PRIMARY_CATEGORIES ) || is_category( surge_category_group_by_slug( 'podcasts', true ) ) || is_category( 'tools' ) || true === $sq['show_taxonomy_tag'] ) {
			$topics = get_post_topics( $post_id );
			?>
				<ul class="<?php echo esc_attr( $class_name ); ?> sq-taxonomy__tags">
				<?php
				foreach ( $topics as $topic ) {
					if ( in_array( $topic->slug, TAG_TOPICS, true ) ) {
						?>
						<li class="sq-taxonomy__tags-tag">
							<a href="<?php echo esc_url( get_tag_link( $topic->term_id ) ); ?>" rel="tag"><?php echo esc_html( $topic->name ); ?></a>
						</li>
						<?php
					}
				}
				?>
				</ul>
			<?php
		}

		if ( get_content_type_icon( $post_id ) ) {
			?>
				<div class="sq-taxonomy__icon <?php echo esc_attr( get_content_type_class( $post_id ) ); ?>"><img src="<?php echo esc_url( get_content_type_icon( $post_id ) ); ?>" alt="-"></div>
			<?php } ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'get_inner_html' ) ) {
	/**
	 * Returns the inner HTML content of a DOM element
	 *
	 * @param object|null $element the DOM element.
	 *
	 * @return string
	 */
	function get_inner_html( object $element = null ): string {
		// @codingStandardsIgnoreStart
		$doc  = $element->ownerDocument;
		$html = '';
		if ( $element ) {
			foreach ( $element->childNodes as $node ) {
				// @codingStandardsIgnoreEnd
				$html .= $doc->saveHTML( $node );
			}
		}

		return $html;
	}
}

if ( ! function_exists( 'get_custom_fields' ) ) {
	/**
	 * This will return all the requested custom fields
	 *
	 * @param int   $post_id post id.
	 * @param array $keys key of the custom fields.
	 *
	 * @return array
	 */
	function get_custom_fields( int $post_id, array $keys ): array {
		$custom_fields = array();

		foreach ( $keys as $key ) {
			$custom_fields[ $key ] = get_post_meta( $post_id, $key, true ) ?? false;
		}

		return $custom_fields;
	}
}

if ( ! function_exists( 'get_popular_article_ids' ) ) {
	/**
	 * This will return the array of popular post ids from settings page.
	 *
	 * @param string $page_name slug/custom name excluding prefix. e.g. hub-settings-homepage so just pass homepage.
	 *
	 * @return array
	 */
	function get_popular_article_ids( string $page_name ): array {
		$settings_post_id = get_post_id_by_slug( "hub-settings-{$page_name}" );
		$popular_articles = get_post_meta( $settings_post_id, 'Most Popular', true );

		return get_post_ids_by_urls( $popular_articles ) ?? array();
	}
}

if ( ! function_exists( 'get_square_products' ) ) {
	/**
	 * This will return the array of square products.
	 *
	 * @param string $product_ids_field mapping ids separated with pipe (|).
	 *
	 * @return array
	 */
	function get_square_products( string $product_ids_field ): array {
		$mapping_ids = array_map( 'trim', explode( '|', $product_ids_field ) );
		$products    = array();

		foreach ( $mapping_ids as $product_id ) {
			$product = get_posts(
				array(
					'numberposts'         => 1,
					'category'            => get_category_by_slug( 'hub-products' )->term_id,
					// @codingStandardsIgnoreLine
					'meta_query'     => array(
						array(
							'key'     => 'Product ID',
							'value'   => $product_id,
							'compare' => 'LIKE',
						),
					),
					'allow_ignored_posts' => true,
					'post_status'         => 'publish',
				)
			);

			if ( $product ) {
				$products[] = $product[0];
			}
		}

		return $products;
	}
}

if ( ! function_exists( 'get_feature_flags' ) ) {
	/**
	 * This will return the current status of the feature.
	 *
	 * @param string $feature_flags feature flags string.
	 * @param array  $flags each flag key.
	 *
	 * @return array
	 */
	function get_feature_flags( string $feature_flags, array $flags ): array {
		$flags_status = array();
		foreach ( $flags as $flag ) {
			if ( $feature_flags ) {
				$flags_status[ $flag ] = stristr( $feature_flags, $flag );
			} else {
				$flags_status[ $flag ] = false;
			}
		}

		return $flags_status;
	}
}

if ( ! function_exists( 'filter_global_query' ) ) {
	/**
	 * This will filter the default query
	 *
	 * @param mixed $query params.
	 *
	 * @return mixed
	 */
	function filter_global_query( mixed $query ): mixed {
		global $wp;

		if ( ! is_admin() && ! ( 'post-sitemap.xml' === basename( $wp->request ) ) && ! $query->get( 'allow_ignored_posts' ) && ( is_home() || is_archive() || is_author() || is_404() ) ) {
			// avoid infinite loop.
			remove_filter( 'pre_get_posts', 'filter_global_query', 1 );
			$exclude_posts = get_seo_exclude_post_ids();
			add_filter( 'pre_get_posts', 'filter_global_query', 1 );

			$query->set( 'post__not_in', $exclude_posts );
		}

		return $query;
	}

	add_filter( 'pre_get_posts', 'filter_global_query', 1 );
}

if ( ! function_exists( 'get_tag_meta_by_category' ) ) {
	/**
	 * Get Tag meta data by tags and categories
	 *
	 * @param string $tag_slug tag slug.
	 * @param int    $category_id category id.
	 *
	 * @return object
	 */
	function get_tag_meta_by_category( string $tag_slug, int $category_id ): object {
		$tag         = get_term_by( 'slug', $tag_slug, 'post_tag' );
		$posts_query = new WP_Query(
			array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				// @codingStandardsIgnoreLine
				'tax_query'      => array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'category',
						'field'    => 'slug',
						'terms'    => MAPPING_CATEGORIES,
						'operator' => 'NOT IN',
					),
					array(
						'relation' => 'AND',
						array(
							'taxonomy' => 'category',
							'field'    => 'term_id',
							'terms'    => array( $category_id ),
							'operator' => 'IN',
						),
						array(
							'taxonomy' => 'post_tag',
							'field'    => 'term_id',
							'terms'    => array( $tag->term_id ),
							'operator' => 'IN',
						),
					),
				),
			)
		);

		wp_reset_postdata();

		return (object) array(
			'name'        => $tag->name,
			'slug'        => $tag->slug,
			'term_id'     => $tag->term_id,
			'posts_count' => $posts_query->found_posts,
		);
	}
}

if ( ! function_exists( 'get_category_meta_by_tag' ) ) {
	/**
	 * Get Category meta data by categories and tag
	 *
	 * @param string $category_slug category slug.
	 * @param int    $tag_id tag id.
	 *
	 * @return object
	 */
	function get_category_meta_by_tag( string $category_slug, int $tag_id ): object {
		$category    = get_term_by( 'slug', $category_slug, 'category' );
		$posts_query = new WP_Query(
			array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				// @codingStandardsIgnoreLine
				'tax_query'      => array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'category',
						'field'    => 'slug',
						'terms'    => MAPPING_CATEGORIES,
						'operator' => 'NOT IN',
					),
					array(
						'relation' => 'AND',
						array(
							'taxonomy' => 'category',
							'field'    => 'term_id',
							'terms'    => array( $category->term_id ),
							'operator' => 'IN',
						),
						array(
							'taxonomy' => 'post_tag',
							'field'    => 'term_id',
							'terms'    => array( $tag_id ),
							'operator' => 'IN',
						),
					),
				),
			)
		);

		wp_reset_postdata();

		return (object) array(
			'name'        => $category->name,
			'slug'        => $category->slug,
			'term_id'     => $category->term_id,
			'posts_count' => $posts_query->found_posts,
		);
	}
}

if ( ! function_exists( 'square_content_table' ) ) {
	/**
	 * Wrap the table with css class
	 *
	 * @param string $content post content.
	 *
	 * @return string
	 */
	function square_content_table( string $content ): string {

		// find all tables.
		preg_match_all( '/<table.*?>(.*?)<\/table>/si', $content, $tables );

		if ( count( $tables ) > 0 ) {
			foreach ( $tables[0] as $table ) {
				// get column count.
				$dom = new DOMDocument();
				$dom->loadHTML( $table );
				$headers = $dom->getElementsByTagName( 'th' );
				$class   = count( $headers ) > 2 ? 'sq-table sq-table--multicol' : 'sq-table';

				// update content.
				$content = str_replace( $table, '<div class="' . $class . '">' . $table . '</div>', $content );
			}
		}

		return $content;
	}

	add_filter( 'the_content', 'square_content_table', 9 );
}
