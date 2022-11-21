<?php
/**
 * All the SEO custom features required for the site
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'square_settings_mapping_post_ids' ) ) {
	/**
	 * Get the mapping post ids
	 *
	 * @param string $post_type type of the post based on category.
	 *
	 * @return array
	 */
	function square_settings_mapping_post_ids( string $post_type ): array {
		$categories = surge_category_group_by_slug( $post_type );
		$post_ids   = array();
		foreach ( $categories as $category_slug ) {
			// for mapping each podcast category has same named post.
			$mapping_post = get_post_id_by_slug( $category_slug );
			if ( $mapping_post ) {
				$post_ids[] = $mapping_post;
			}
		}

		return $post_ids;
	}
}

if ( ! function_exists( 'square_permalink_routing' ) ) {
	/**
	 * Filters the permalink for a post.
	 *
	 * @param string  $permalink permalink of a post.
	 * @param WP_Post $post post object.
	 *
	 * @return string
	 */
	function square_permalink_routing( string $permalink, WP_Post $post ): string {

		// default permalink.
		$categories    = get_the_category( $post->ID );
		$category_slug = '';
		foreach ( $categories as $category ) {
			if ( in_array( $category->slug, PRIMARY_CATEGORIES, true ) ) {
				$category_slug = $category->slug;
				break;
			}
		}

		$permalink = trailingslashit( home_url( "/{$category_slug}/{$post->post_name}/" ) );

		// podcast permalink routing.
		$podcasts_categories = surge_category_group_by_slug( 'podcasts' );
		if ( has_category( $podcasts_categories, $post->ID ) ) {
			$podcast_category = '';
			foreach ( $categories as $category ) {
				if ( in_array( $category->slug, $podcasts_categories, true ) ) {
					if ( 'podcasts' !== $category->slug ) {
						$podcast_category = $category->slug;
						break;
					}
				}
			}

			if ( $podcast_category ) {
				$permalink = trailingslashit( home_url( "/podcasts/{$podcast_category}/{$post->post_name}/" ) );
			}
		}

		// collections permalink routing.
		$collections_categories = surge_category_group_by_slug( 'collections' );
		if ( has_category( $collections_categories, $post->ID ) && in_array( get_post( $post->ID )->post_name, $collections_categories, true ) ) {
			$collection_category = '';
			foreach ( $categories as $category ) {
				if ( in_array( $category->slug, $collections_categories, true ) ) {
					if ( 'collections' !== $category->slug ) {
						$collection_category = $category->slug;
						break;
					}
				}
			}

			if ( $collection_category ) {
				$permalink = trailingslashit( home_url( "/collections/{$collection_category}/{$post->post_name}/" ) );
			}
		}

		// tools permalink routing.
		if ( has_category( 'tools', $post->ID ) ) {
			$permalink = trailingslashit( home_url( "/tools/{$post->post_name}/" ) );
		}

		// case studies permalink routing.
		if ( has_category( 'case-studies', $post->ID ) ) {
			$permalink = trailingslashit( home_url( "/case-studies/{$post->post_name}/" ) );
		}

		return $permalink;
	}

	add_filter( 'post_link', 'square_permalink_routing', 10, 2 );
}

if ( ! function_exists( 'get_seo_exclude_post_ids' ) ) {
	/**
	 * This will return all the excluded post ids for seo
	 *
	 * @return array
	 */
	function get_seo_exclude_post_ids(): array {
		/**
		 * Add before launch
		 */
		$podcast_posts_ids      = square_settings_mapping_post_ids( 'podcasts' );
		$collections_posts_ids  = square_settings_mapping_post_ids( 'collections' );
		$cta_post_ids           = surge_get_post_ids_by_category( 'hub-cta' );
		$product_post_ids       = surge_get_post_ids_by_category( 'hub-products' );
		$hub_settings_posts_ids = surge_get_post_ids_by_category( 'hub-settings' );

		return array(
			...$podcast_posts_ids,
			...$cta_post_ids,
			...$collections_posts_ids,
			...$product_post_ids,
			...$hub_settings_posts_ids,
		);
	}

	add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', 'get_seo_exclude_post_ids' );
}

if ( ! function_exists( 'square_seo_posts_robots' ) ) {
	/**
	 * Set noindex, nofollow on specific posts
	 *
	 * @param string $robots index or noindex.
	 *
	 * @return string
	 */
	function square_seo_posts_robots( string $robots ): string {
		if ( is_single( get_seo_exclude_post_ids() ) ) {
			$robots = 'noindex, nofollow';
		}
		return $robots;
	}
	add_filter( 'wpseo_robots', 'square_seo_posts_robots' );
}

if ( ! function_exists( 'square_template_redirects' ) ) {
	/**
	 * Redirect mapping content to homepage
	 *
	 * @return void
	 */
	function square_template_redirects():void {
		if ( is_single( get_seo_exclude_post_ids() ) || is_category( 'hub-cta' ) || is_category( 'hub-products' ) || is_category( 'hub-settings' ) ) {
			wp_safe_redirect( home_url() );
			exit;
		}
	}
	add_action( 'template_redirect', 'square_template_redirects' );
}

if ( ! function_exists( 'square_get_current_settings_post_id' ) ) {
	/**
	 * This will return the current settings post id from request.
	 *
	 * @param string|null $custom_page_name modify the current page.
	 *
	 * @return bool|int
	 */
	function square_get_current_settings_post_id( string $custom_page_name = null ): bool|int {
		global $wp;
		$slug = parse_slug_from_url( home_url( $wp->request ) );
		$slug = $custom_page_name ? $custom_page_name : ( '' === $slug ? 'homepage' : $slug );

		return get_post_id_by_slug( "hub-settings-{$slug}" );
	}
}

if ( ! function_exists( 'square_wpseo_meta' ) ) {
	/**
	 * Get yoast meta key value
	 *
	 * @param string $key key of the meta field.
	 *
	 * @return string
	 */
	function square_wpseo_meta( string $key ): string {
		return get_post_meta( square_get_current_settings_post_id(), $key, true ) ?? '';
	}
}

if ( ! function_exists( 'square_wpseo_custom_title' ) ) {
	/**
	 * Filter WordPress title
	 *
	 * @param string $title meta title of the page.
	 *
	 * @return string
	 */
	function square_wpseo_custom_title( string $title ): string {
		if ( is_home() || is_category() ) {
			$meta_title = square_wpseo_meta( '_yoast_wpseo_title' );

			if ( $meta_title ) {
				$sept      = WPSEO_Utils::get_title_separator();
				$site_name = get_bloginfo( 'name' );

				return "{$meta_title} {$sept} {$site_name}";
			}
		}

		return $title;
	}

	add_filter( 'wpseo_title', 'square_wpseo_custom_title' );
	add_filter( 'wpseo_opengraph_title', 'square_wpseo_custom_title' );
}

if ( ! function_exists( 'square_wpseo_custom_description' ) ) {
	/**
	 * Filter WordPress description
	 *
	 * @param string $description meta description of the page.
	 *
	 * @return string
	 */
	function square_wpseo_custom_description( string $description ): string {
		if ( is_home() || is_category() ) {
			$meta_desc = square_wpseo_meta( '_yoast_wpseo_metadesc' );
			if ( $meta_desc ) {
				return $meta_desc;
			}
		}

		return $description;
	}

	add_filter( 'wpseo_metadesc', 'square_wpseo_custom_description' );
	add_filter( 'wpseo_opengraph_desc', 'square_wpseo_custom_description' );
}

// Filter WordPress Schema.
add_filter(
	'wpseo_schema_graph',
	function ( $data, $context ) {
		foreach ( $data as $key => $value ) {
			if ( 'CollectionPage' === $value['@type'] ) {
				$meta_title = square_wpseo_meta( '_yoast_wpseo_title' );
				$meta_desc  = square_wpseo_meta( '_yoast_wpseo_metadesc' );

				if ( $meta_title ) {
					$sept                 = WPSEO_Utils::get_title_separator();
					$site_name            = get_bloginfo( 'name' );
					$data[ $key ]['name'] = "{$meta_title} {$sept} {$site_name}";
				}

				if ( $meta_desc ) {
					$data[ $key ]['description'] = $meta_desc;
				}
			}
		}

		return $data;
	},
	10,
	2
);

if ( ! function_exists( 'square_wpseo_rel_next' ) ) {
	/**
	 * Remove rel="next" or rel="prev" since it's a ajax based site
	 *
	 * @param string $link link of the next link.
	 *
	 * @return string|bool
	 */
	function square_wpseo_rel_link( string $link ): string|bool {
		if ( is_home() || is_archive() || is_author() || is_search() ) {
			return false;
		}

		return $link;
	}

	add_filter( 'wpseo_next_rel_link', 'square_wpseo_rel_link' );
	add_filter( 'wpseo_prev_rel_link', 'square_wpseo_rel_link' );
}

if ( ! function_exists( 'convert_to_welcome_cdn' ) ) {
	/**
	 * Convert all welcome images to CDN image.
	 *
	 * @param string $cdn_url url of the image cdn.
	 *
	 * @return string
	 */
	function convert_to_welcome_cdn( string $cdn_url ): string {
		if ( ! str_contains( $cdn_url, 'cdn' ) ) {
			$cdn_url = preg_replace( '/images/i', 'images-cdn', $cdn_url );
			$cdn_url = preg_replace( '/newscred/i', 'welcomesoftware', $cdn_url );
		}

		return $cdn_url;
	}
}

if ( ! function_exists( 'square_post_opengraph_image' ) ) {
	/**
	 * Update og:image
	 *
	 * @param string $img custom og:image.
	 *
	 * @return string
	 */
	function square_post_opengraph_image( string $img ): string {
		if ( is_single() ) {
			global $post;
			$custom_og_image = get_post_meta( $post->ID, 'Custom OG Image', true );
			$img             = $custom_og_image ? convert_to_welcome_cdn( $custom_og_image ) . '?width=1200' : get_post_meta( $post->ID, 'nc-image', true ) . '?width=1200';
		}
		return $img;
	}
	add_filter( 'wpseo_opengraph_image', 'square_post_opengraph_image' );
}

if ( ! function_exists( 'square_post_opengraph_title' ) ) {
	/**
	 * Update og:title
	 *
	 * @param string $title custom og:title.
	 *
	 * @return string
	 */
	function square_post_opengraph_title( string $title ): string {
		if ( is_single() ) {
			global $post;
			$custom_og_title = get_post_meta( $post->ID, 'Custom OG Title', true );
			if ( $custom_og_title ) {
				return $custom_og_title;
			}
		}

		return $title;
	}

	add_filter( 'wpseo_opengraph_title', 'square_post_opengraph_title' );
}

if ( ! function_exists( 'square_post_opengraph_desc' ) ) {
	/**
	 * Update og:description
	 *
	 * @param string $desc custom og:description.
	 *
	 * @return string
	 */
	function square_post_opengraph_desc( string $desc ): string {
		if ( is_single() ) {
			global $post;
			$custom_og_desc = get_post_meta( $post->ID, 'Custom OG Description', true );
			if ( $custom_og_desc ) {
				return $custom_og_desc;
			}
		}

		return $desc;
	}

	add_filter( 'wpseo_opengraph_desc', 'square_post_opengraph_desc' );
}

if ( ! function_exists( 'square_page_opengraph_image' ) ) {
	/**
	 * Add custom og:image for all pages except posts and homepage
	 *
	 * @param object $object object of the og:image.
	 *
	 * @return void
	 */
	function square_page_opengraph_image( object $object ): void {
		if ( ! is_single() ) {
			$og_image_default_flag = true;
			if ( is_home() || is_category() ) {
				$nc_image = get_post_meta( square_get_current_settings_post_id(), 'nc-image', true );

				if ( $nc_image ) {
					$object->add_image( convert_to_welcome_cdn( $nc_image ) . '?width=1200' );
					$og_image_default_flag = false;
				}
			}
			if ( $og_image_default_flag ) {
				$nc_image = get_post_meta( square_get_current_settings_post_id( 'homepage' ), 'nc-image', true );
				$nc_image = convert_to_welcome_cdn( $nc_image );

				if ( $nc_image ) {
					$object->add_image( convert_to_welcome_cdn( $nc_image ) . '?width=1200' );
				}
			}
		}
	}

	add_action( 'wpseo_add_opengraph_images', 'square_page_opengraph_image', 12, 1 );
}
