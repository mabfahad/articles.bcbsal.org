<?php
/**
 * All Surge functionalities
 * Surge is an optimized framework to build WordPress websites.
 *
 * @package surge
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'surge_theme_setup' ) ) {
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @ref https://codex.wordpress.org/Plugin_API/Action_Reference/after_setup_theme
	 * @return void
	 */
	function surge_theme_setup(): void {
		// Add theme support for Automatic Feed Links.
		add_theme_support( 'automatic-feed-links' );

		// Add theme support for Featured Images.
		add_theme_support( 'post-thumbnails' );

		// Add theme support for document title tag.
		add_theme_support( 'title-tag' );

		// Add theme support for HTML5 Semantic Markup.
		add_theme_support( 'html5', array( 'search-form', 'caption' ) );
	}

	add_action( 'after_setup_theme', 'surge_theme_setup' );
}

if ( ! function_exists( 'surge_versioned_style' ) ) {
	/**
	 * Enqueues stylesheet with WordPress and adds version number that is a timestamp of the file modified date
	 * This should ensure that users always have the current version of the file, and that the CDN is properly updated
	 *
	 * @param string     $name name of the stylesheet.
	 * @param string     $path path of the stylesheet.
	 * @param bool|array $dependencies dependencies of the stylesheet.
	 * @param string     $media media for which this stylesheet has been defined.
	 *
	 * @return void
	 */
	function surge_versioned_style( string $name, string $path, bool|array $dependencies = false, string $media = 'all' ): void {
		if ( ! is_admin() ) {
			wp_enqueue_style( $name, get_template_directory_uri() . $path, $dependencies, filemtime( get_template_directory() . $path ), $media );
		}
	}
}

if ( ! function_exists( 'surge_versioned_script' ) ) {
	/**
	 * Enqueues script with WordPress and adds version number that is a timestamp of the file modified date
	 * This should ensure that users always have the current version of the script, and that the CDN is properly updated
	 *
	 * @param string     $name name of the stylesheet.
	 * @param string     $path path of the stylesheet.
	 * @param bool|array $dependencies dependencies of the stylesheet.
	 * @param bool       $in_footer Whether to call this in the footer.
	 */
	function surge_versioned_script( string $name, string $path, bool|array $dependencies = false, bool $in_footer = false ): void {
		if ( ! is_admin() ) {
			wp_enqueue_script( $name, get_template_directory_uri() . $path, $dependencies, filemtime( get_template_directory() . $path ), $in_footer );
		}
	}
}

if ( ! function_exists( 'is_production' ) ) {
	/**
	 * Check whether the hub environment is local or production.
	 *
	 * @return bool
	 */
	function is_production(): bool {
		return file_exists( get_template_directory() . '/assets/bundle.css' );
	}
}

/* Load bundle.js */
surge_versioned_script( 'bundle', '/assets/bundle.js', array( 'jquery' ), true );

/* Load main.css in production */
if ( is_production() ) {
	surge_versioned_style( 'bundle', '/assets/bundle.css', false );
}

if ( ! function_exists( 'surge_remove_jquery' ) ) {
	/**
	 * Remove default jQuery from WordPress
	 */
	function surge_remove_jquery(): void {
		if ( ! is_admin() ) {
			wp_deregister_script( 'jquery' );
			wp_register_script( 'jquery', false, array(), '1.0', false );
		}
	}

	add_action( 'init', 'surge_remove_jquery' );
}

if ( ! function_exists( 'surge_register_primary_menu' ) ) {
	/**
	 * It will add "Navigation Primary" checkbox in menu section
	 *
	 * @return void
	 */
	function surge_register_primary_menu(): void {
		register_nav_menu( 'nav-primary', __( 'Navigation Primary' ) );
	}

	add_filter( 'init', 'surge_register_primary_menu' );
}

if ( ! function_exists( 'surge_primary_menu' ) ) {
	/**
	 * Primary menu
	 * call load_primary_menu();
	 *
	 * Make sure to check the "Navigation Primary" checkbox in menu section
	 */
	function surge_primary_menu(): void {
		$menu_name = 'nav-primary';
		if ( has_nav_menu( $menu_name ) ) {
			wp_nav_menu(
				array(
					'menu'            => $menu_name,
					'theme_location'  => $menu_name,
					'depth'           => 10,
					'container_class' => 'nav-navbar',
					'fallback_cb'     => false,
					'walker'          => new Surge_Walker_Nav_Menu(),
				)
			);
		} else {
			echo 'Please set up your menu';
		}
	}
}

if ( ! function_exists( 'surge_custom_menu' ) ) {
	/**
	 * This will return the menu as li
	 *
	 * @param string $menu name of the menu.
	 *
	 * @return void
	 */
	function surge_custom_menu( string $menu ): void {
		wp_nav_menu(
			array(
				'menu'       => $menu,
				'container'  => '',
				'items_wrap' => '%3$s',
			)
		);
	}
}

if ( ! function_exists( 'surge_category_group_by_slug' ) ) {
	/**
	 * This will return the array of category group
	 *
	 * @param string $parent_category_slug slug of the parent category.
	 * @param bool   $no_parent return group without parent category.
	 *
	 * @return array
	 */
	function surge_category_group_by_slug( string $parent_category_slug, bool $no_parent = false ): array {
		$categories_object = get_categories(
			array( 'parent' => get_cat_ID( $parent_category_slug ) )
		);

		$categories = array();
		foreach ( $categories_object as $category ) {
			$categories[] = $category->slug;
		}

		if ( $no_parent ) {
			return $categories ?? array();
		}

		return array( $parent_category_slug, ...$categories ) ?? array();
	}
}

if ( ! function_exists( 'surge_get_post_ids_by_category' ) ) {
	/**
	 * This will return post ids of the category
	 *
	 * @param string $category slug of the category.
	 *
	 * @return array
	 */
	function surge_get_post_ids_by_category( string $category ): array {
		return get_posts(
			array(
				'posts_per_page'      => -1,
				'allow_ignored_posts' => true,
				'category'            => get_category_by_slug( $category )->term_id,
				'fields'              => 'ids',
			)
		);
	}
}

if ( ! function_exists( 'surge_extra_user_contact_fields' ) ) {
	/**
	 * Add user fields
	 *
	 * @param array $user_contactmethods Existing user contact method fields.
	 *
	 * @return array $user_contactmethods
	 */
	function surge_extra_user_contact_fields( array $user_contactmethods ): array {
		$user_contactmethods['title']       = 'Title';
		$user_contactmethods['expert_area'] = 'Expert Area';
		$user_contactmethods['company']     = 'Company';
		$user_contactmethods['twitter']     = 'Twitter';
		$user_contactmethods['linkedin']    = 'LinkedIn';
		$user_contactmethods['facebook']    = 'Facebook';
		return $user_contactmethods;
	}
	add_filter( 'user_contactmethods', 'surge_extra_user_contact_fields' );
}

if ( ! function_exists( 'surge_wpseo_canonical_exclude' ) ) {
	/**
	 * Remove yoast plugin canonical link from single
	 *
	 * @param bool|string $canonical The canonical URL.
	 *
	 * @return bool|string
	 *
	 * @ref https://yoast.com/wordpress/plugins/seo/api/
	 */
	function surge_wpseo_canonical_exclude( bool|string $canonical ): bool|string {
		global $post;
		if ( is_single() ) {
			$canonical = false;
		}

		return $canonical;
	}

	add_filter( 'wpseo_canonical', 'surge_wpseo_canonical_exclude' );
}

if ( ! function_exists( 'surge_wpseo_canonical_overwrite' ) ) {
	/**
	 * Get the canonical link either from WordPress permalink or from custom field
	 *
	 * @param string $canonical Canonical URL.
	 *
	 * @return string
	 *
	 * @ref https://yoast.com/wordpress/plugins/seo/api/
	 */
	function surge_wpseo_canonical_overwrite( string $canonical ): string {
		global $post;
		if ( is_single() ) {
			$nc_link = get_post_meta( get_the_ID(), 'nc-link', true );
			if ( ! empty( $nc_link ) ) {
				return $nc_link;
			} else {
				return get_the_permalink();
			}
		}

		return $canonical;
	}

	add_filter( 'wpseo_canonical', 'surge_wpseo_canonical_overwrite' );
}

if ( ! function_exists( 'surge_optimize_content_image' ) ) {
	/**
	 * Optimize the images in content those are coming from Welcome Software Image CDN
	 * This will check the image dimension, if the size is already define then it will use the same dimension
	 * image from Welcome Software CDN otherwise it will call the content width size to make sure the max width of the
	 * images.
	 *
	 * @param string $content The post content.
	 *
	 * @return string
	 */
	function surge_optimize_content_image( string $content ): string {

		// Find all the images of welcome software.
		preg_match_all( '/<img.*src=".*image.*.welcomesoftware.com[^"].*>/i', $content, $all_cdn_images );

		// Get the global content max width.
		global $content_width;

		// Iterate through all images.
		foreach ( $all_cdn_images[0] as $image ) {

			// Get the image width.
			preg_match( '/(width)=("[^"]*")/i', $image, $width );

			// Default image size.
			$image_width = $content_width;

			// If the width exist.
			if ( isset( $width[2] ) ) {
				// If the width is less than container width.
				// Use the min width.
				$img_width = round( trim( $width[2], '"' ) );
				if ( $img_width < $content_width ) {
					// Define the existing width.
					$image_width = $img_width;
				}
			}

			// Get the source of the image.
			preg_match( '/src=".*image.*.welcomesoftware.com[^"]*/i', $image, $src );

			$images_src = array();
			foreach ( $src as $url ) {
				$size = getimagesize( substr( $url, 5 ) );
				if ( 'image/gif' !== $size['mime'] ) {
					$images_src[] = $url;
				}
			}
			$src = $images_src;

			// Check the cdn url if it already has width query.
			if ( ! strpos( '?width=', $src[0] ) ) {
				// Add the content max width.
				$content = str_replace( $src[0], $src[0] . '?width=' . $image_width, $content );
			}
		}

		return $content;
	}

	add_filter( 'the_content', 'surge_optimize_content_image' );
}

if ( ! function_exists( 'surge_disable_emojis' ) ) {
	/**
	 * Disable the emoji's
	 *
	 * @return void
	 */
	function surge_disable_emojis(): void {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		add_filter( 'tiny_mce_plugins', 'surge_disable_emojis_tinymce' );
		add_filter( 'wp_resource_hints', 'surge_disable_emojis_remove_dns_prefetch', 10, 2 );
	}

	add_action( 'init', 'surge_disable_emojis' );
}

if ( ! function_exists( 'surge_disable_emojis_tinymce' ) ) {
	/**
	 * Filter function used to remove the tinymce emoji plugin.
	 *
	 * @param array|null $plugins TinyMCE plugins.
	 *
	 * @return array
	 */
	function surge_disable_emojis_tinymce( ?array $plugins ): array {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		} else {
			return array();
		}
	}
}

if ( ! function_exists( 'surge_disable_emojis_remove_dns_prefetch' ) ) {
	/**
	 * Remove emoji CDN hostname from DNS prefetching hints.
	 *
	 * @param array  $urls URLs to print for resource hints.
	 * @param string $relation_type The relation type the URLs are printed for.
	 *
	 * @return array Difference between the two arrays.
	 */
	function surge_disable_emojis_remove_dns_prefetch( array $urls, string $relation_type ): array {
		if ( 'dns-prefetch' === $relation_type ) {
			/** This filter is documented in wp-includes/formatting.php */
			$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );

			$urls = array_diff( $urls, array( $emoji_svg_url ) );
		}

		return $urls;
	}
}

if ( ! function_exists( 'surge_remove_attr_filter' ) ) {
	/**
	 * Remove id and class from menu
	 *
	 * @param mixed $var Menu variables.
	 *
	 * @return string|array
	 */
	function surge_remove_attr_filter( mixed $var ): string|array {
		return is_array( $var ) ? array_intersect( $var, array( 'current-menu-item' ) ) : '';
	}

	add_filter( 'nav_menu_item_id', 'surge_remove_attr_filter', 100, 1 );
}

if ( ! function_exists( 'surge_remove_unused_libraries' ) ) {
	/**
	 * Surge Remove Unused header links from loading on the frontend
	 *
	 * @return void
	 */
	function surge_remove_unused_libraries(): void {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'wc-block-style' );
		wp_dequeue_style( 'mpp_gutenberg' );
		wp_dequeue_script( 'mpp_gutenberg_tabs' );
	}

	add_action( 'wp_enqueue_scripts', 'surge_remove_unused_libraries', 100 );
}

if ( ! function_exists( 'surge_deregister_scripts' ) ) {
	/**
	 * Remove wp-embed from all except single page
	 *
	 * @return void
	 */
	function surge_deregister_scripts(): void {
		if ( ! is_admin() && ! is_singular() ) {
			wp_dequeue_script( 'wp-embed' );
		}
	}

	add_action( 'wp_footer', 'surge_deregister_scripts' );
}

if ( ! function_exists( 'surge_disable_feed' ) ) {
	/**
	 * Cleanup header
	 *
	 * @return void
	 */
	function surge_disable_feed(): void {
		wp_safe_redirect( home_url( '/' ) );
		exit();
	}

	add_action( 'do_feed_rss2_comments', 'surge_disable_feed', 1 );
	add_action( 'do_feed_atom_comments', 'surge_disable_feed', 1 );
}

/**
 * Cleanup WordPress header
 */
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
add_filter( 'the_generator', '__return_false' );
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );


if ( ! function_exists( 'surge_remove_global_styles' ) ) {
	/**
	 * Remove Global Styles
	 *
	 * @return void
	 */
	function surge_remove_global_styles(): void {
		remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
	}

	add_action( 'init', 'surge_remove_global_styles' );
}

/**
 * Remove ellipses from excerpt
 */
add_filter( 'excerpt_more', '__return_false' );

if ( ! function_exists( 'surge_authorised_rest_access' ) ) {
	/**
	 * Restrict REST API access
	 *
	 * @param mixed $result REST API result.
	 *
	 * @return mixed
	 */
	function surge_authorised_rest_access( mixed $result ): mixed {
		if ( ! is_user_logged_in() ) {
			wp_safe_redirect( home_url( '/' ) );
			exit();
		}

		return $result;
	}

	add_filter( 'rest_authentication_errors', 'surge_authorised_rest_access' );
}
