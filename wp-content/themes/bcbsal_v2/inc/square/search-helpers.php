<?php
/**
 * All the search specific helper functions
 * These functions required the plugin https://www.relevanssi.com/
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'get_search_results' ) ) {
	/**
	 * This will search the content and return results.
	 *
	 * @param string $search_query search query.
	 *
	 * @return array
	 */
	#[ArrayShape(
		array(
			'search_for' => 'string',
			'post_ids'   => 'array',
		)
	)] function get_search_results( string $search_query ): array {
		// exclude posts from results.
		$exclude = array(
			...surge_get_post_ids_by_category( 'hub-cta' ),
			...surge_get_post_ids_by_category( 'hub-products' ),
			...surge_get_post_ids_by_category( 'hub-settings' ),
		);

		// prepare string for db query.
		$exclude_db_preparer = implode( ', ', array_fill( 0, count( $exclude ), '%d' ) );

		// search query terms.
		$search_query = sanitize_text_field( trim( $search_query ) );

		// convert search query to array.
		$keywords = explode( ' ', $search_query );

		// define variables for results.
		$similar_words     = array();
		$search_results    = array();
		$last_similar_word = '';

		// run query if results found.
		if ( $keywords ) {
			global $wpdb;

			// iterate through the keywords.
			foreach ( $keywords as $index => $keyword ) {
				// get the sql results.
				$like = $wpdb->esc_like( $keyword ) . '%';

				// @codingStandardsIgnoreStart
				$results = $wpdb->get_results(
				$wpdb->prepare(
						"SELECT doc, term FROM {$wpdb->prefix}relevanssi WHERE term LIKE %s AND doc NOT IN ({$exclude_db_preparer})",
						array(
							$like,
							...$exclude,
						)
					),
					OBJECT
				);
				// @codingStandardsIgnoreEnd

				// only pick the first result.
				if ( $results ) {
					$similar_words[]   = $results[0]->term;
					$last_similar_word = $results[0]->term;
					foreach ( $results as $result ) {
						if ( ! in_array( $result->doc, $search_results, true ) && $results[0]->term === $result->term ) {
							$search_results[] = $result->doc;
						}
					}
				}
			}
		}

		// beautify suggestions.
		// @todo experimental implementation.
		$total_keywords = count( $keywords );

		if ( $total_keywords > 1 ) {
			$similar_words = array();
			for ( $k = 0; $k < $total_keywords - 1; $k ++ ) {
				$similar_words[] = $keywords[ $k ];
			}
			$similar_words[] = $last_similar_word;
		}

		return array(
			'search_for' => implode( ' ', $similar_words ),
			'post_ids'   => $search_results,
		);
	}
}

if ( ! function_exists( 'get_search_taxonomies' ) ) {
	/**
	 * Get taxonomy meta by search query, categories and tags
	 *
	 * @param string $search_term search query.
	 * @param string $category_slug category slug.
	 * @param array  $tag_slugs slugs of the tags.
	 * @param bool   $is_tag to check taxonomy type.
	 *
	 * @return array|object|null
	 */
	function get_search_taxonomies( string $search_term, string $category_slug = '', array $tag_slugs = array(), bool $is_tag = false ): array|object|null {
		// @codingStandardsIgnoreStart
		global $wpdb;

		// exclude posts from results.
		$exclude = array(
			...surge_get_post_ids_by_category( 'hub-cta' ),
			...surge_get_post_ids_by_category( 'hub-products' ),
			...surge_get_post_ids_by_category( 'hub-settings' ),
			...surge_get_post_ids_by_category( 'uncategorized' ),
		);

		// prepare string for db query.
		$exclude_db_preparer = implode( ', ', array_fill( 0, count( $exclude ), '%d' ) );

		// convert search query to array.
		$keywords = explode( ' ', esc_html( $search_term ) );

		// like query condition.
		$like_cond    = '';
		$like_prepare = array();
		if ( count( $keywords ) > 0 ) {
			$like_cond  = '(';
			$like_count = 0;
			foreach ( $keywords as $keyword ) {
				if ( $like_count > 0 ) {
					$like_cond     .= ' OR term LIKE %s';
				} else {
					$like_cond     .= 'term LIKE %s';
				}
				$like_prepare[] = $wpdb->esc_like( $keyword ) . '%';
				$like_count ++;
			}
			$like_cond .= ') AND';
		}

		// tag query condition.
		$tags_cond = '';
		if ( count( $tag_slugs ) > 0 ) {
			foreach ( $tag_slugs as $t ) {
				if ( in_array(
					$t,
					array(
						...TAG_CONTENT_TYPES,
						...TAG_TOPICS,
						...TAG_INDUSTRIES,
						...TAG_SQUARE_SOLUTIONS,
					),
					true
				) ) {
					$tags_cond .= " AND object_id IN (SELECT object_id FROM {$wpdb->prefix}term_relationships WHERE term_taxonomy_id = (SELECT term_id FROM {$wpdb->prefix}terms WHERE slug='{$t}'))";
				}
			}
		}

		// category query condition.
		$category_cond = '';
		if ( $category_slug && in_array( $category_slug, PRIMARY_CATEGORIES, true ) ) {
			$category_cond = "AND object_id IN (SELECT object_id FROM {$wpdb->prefix}term_relationships WHERE term_taxonomy_id = (SELECT term_id FROM {$wpdb->prefix}terms WHERE slug='{$category_slug}'))";
		}

		// taxonomy type.
		$type = $is_tag ? 'post_tag' : 'category';
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT term.term_id, term.name, term.slug, (
					SELECT count(object_id)
					FROM {$wpdb->prefix}term_relationships
					WHERE term_taxonomy_id = term.term_id
					AND object_id IN (SELECT doc FROM {$wpdb->prefix}relevanssi WHERE {$like_cond} doc NOT IN ({$exclude_db_preparer})) {$tags_cond} {$category_cond}
					) posts_count FROM {$wpdb->prefix}term_taxonomy tax
					LEFT JOIN {$wpdb->prefix}terms term ON term.term_id = tax.term_id
					WHERE tax.taxonomy = '{$type}'",
				...$like_prepare,
				...$exclude
			),
			OBJECT
		);
		// @codingStandardsIgnoreEnd
	}
}


if ( ! function_exists( 'get_search_modal_post_html' ) ) {
	/**
	 * This will return the search modal card html
	 *
	 * @param Object      $post post object.
	 * @param string|null $type type of the content.
	 *
	 * @return string
	 */
	function get_search_modal_post_html( object $post, string|null $type = null ): string {
		if ( 'articles' === $type ) {
			$category = get_post_category( $post->ID );

			return '<div class="col-sm-3"><div class="card-no-image search-modal__card"><a class="sq-taxonomy__category" href="' . esc_url( get_category_link( $category ) ) . '">' . esc_attr( $category->name ) . '</a><h2 class="card-no-image__title"><a href="' . esc_url( get_custom_post_permalink( $post->ID ) ) . '">' . wp_kses( get_post_title( $post->ID ), WHITELISTED_HTML ) . '</a></h2><div class="card-no-image__meta sq-meta">' . get_the_date( 'M d, Y', $post ) . ' — ' . esc_attr( read_time( $post->ID ) ) . '</div></div></div>';
		}

		return '<div class="card-no-image search-modal__card"><h2 class="card-no-image__title"><a href="' . esc_url( get_custom_post_permalink( $post->ID ) ) . '">' . wp_kses( get_post_title( $post->ID ), WHITELISTED_HTML ) . '</a></h2></div>';
	}
}

if ( ! function_exists( 'get_search_excluded_category_ids' ) ) {
	/**
	 * This will return the excluded category ids
	 *
	 * @return array
	 */
	function get_search_excluded_category_ids(): array {
		$category_ids = array();

		foreach ( MAPPING_CATEGORIES as $slug ) {
			$category_ids[] = get_category_by_slug( $slug )->term_id;
		}

		return $category_ids;
	}
}

if ( ! function_exists( 'get_category_not_in_query' ) ) {
	/**
	 * This will return category exclude query args.
	 *
	 * @return array
	 */
	function get_category_not_in_query(): array {
		return array(
			'taxonomy' => 'category',
			'field'    => 'term_id',
			'terms'    => get_search_excluded_category_ids(),
			'operator' => 'NOT IN',
		);
	}
}

if ( ! function_exists( 'get_category_in_query' ) ) {
	/**
	 * This will return category query args.
	 *
	 * @param string $category category slug.
	 *
	 * @return array
	 */
	function get_category_in_query( string $category ): array {
		return array(
			'taxonomy' => 'category',
			'field'    => 'slug',
			'terms'    => array( $category ),
			'operator' => 'IN',
		);
	}
}

if ( ! function_exists( 'get_tags_in_query' ) ) {
	/**
	 * This will return tags query args.
	 *
	 * @param array $tag_slugs array of tag slugs.
	 *
	 * @return array
	 */
	function get_tags_in_query( array $tag_slugs ): array {
		return array(
			'taxonomy' => 'post_tag',
			'field'    => 'slug',
			'terms'    => $tag_slugs,
			'operator' => 'IN',
		);
	}
}


if ( ! function_exists( 'get_category_meta_by_search' ) ) {
	/**
	 * Get category meta by search query, categories and tag
	 *
	 * @param string $category_slug category slug.
	 * @param string $search_query search query.
	 * @param array  $tag_slugs slugs of the tags.
	 *
	 * @return object
	 * @deprecated use get_search_taxonomies(...)
	 */
	function get_category_meta_by_search( string $category_slug, string $search_query = '', array $tag_slugs = array() ): object {
		$category = get_term_by( 'slug', $category_slug, 'category' );

		$args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			's'              => esc_attr( $search_query ),
			'relevanssi'     => true,
		);

		if ( $tag_slugs ) {
			// @codingStandardsIgnoreLine
			$args['tax_query'] = array(
				'relation' => 'AND',
				get_category_in_query( $category->slug ),
				get_tags_in_query( $tag_slugs ),
			);
		} else {
			// @codingStandardsIgnoreLine
			$args['tax_query'] = array(
				'relation' => 'AND',
				get_category_in_query( $category->slug ),
			);
		}

		$posts_query = new WP_Query( $args );

		wp_reset_postdata();

		return (object) array(
			'name'        => $category->name,
			'slug'        => $category->slug,
			'term_id'     => $category->term_id,
			'posts_count' => $posts_query->found_posts,
		);
	}
}

if ( ! function_exists( 'get_tag_meta_by_search' ) ) {
	/**
	 * Get tag meta by search query, categories and tag
	 *
	 * @param string $tag_slug slugs of the tags.
	 * @param array  $selected_tags selected tags from filter.
	 * @param string $search_query search query.
	 * @param string $category_slug category slug.
	 *
	 * @return object
	 * @deprecated use get_search_taxonomies(...)
	 */
	function get_tag_meta_by_search( string $tag_slug, array $selected_tags = array(), string $search_query = '', string $category_slug = '' ): object {
		$post_tag = get_term_by( 'slug', $tag_slug, 'post_tag' );

		if ( count( $selected_tags ) > 0 ) {
			$selected_tags = array_diff( array( $post_tag->slug ), $selected_tags );
		}

		$args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
			's'              => esc_attr( $search_query ),
			'relevanssi'     => true,
		);

		if ( $category_slug && count( $selected_tags ) > 0 ) {
			// @codingStandardsIgnoreLine
			$args['tax_query'] = array(
				'relation' => 'AND',
				get_category_in_query( $category_slug ),
				get_tags_in_query( array( $post_tag->slug ) ),
				get_tags_in_query( $selected_tags ),
			);
		} elseif ( count( $selected_tags ) > 0 ) {
			// @codingStandardsIgnoreLine
			$args['tax_query'] = array(
				'relation' => 'AND',
				get_tags_in_query( array( $post_tag->slug ) ),
				get_tags_in_query( $selected_tags ),
			);
		} elseif ( $category_slug ) {
			// @codingStandardsIgnoreLine
			$args['tax_query'] = array(
				'relation' => 'AND',
				get_category_in_query( $category_slug ),
				get_tags_in_query( array( $post_tag->slug ) ),
			);
		} else {
			// @codingStandardsIgnoreLine
			$args['tax_query'] = array(
				'relation' => 'AND',
				get_tags_in_query( array( $post_tag->slug ) ),
			);
		}

		$posts_query = new WP_Query( $args );

		wp_reset_postdata();

		return (object) array(
			'name'        => $post_tag->name,
			'slug'        => $post_tag->slug,
			'term_id'     => $post_tag->term_id,
			'posts_count' => $posts_query->found_posts,
		);
	}
}

if ( ! function_exists( 'get_request_param' ) ) {
	/**
	 * Gets the request parameter.
	 *
	 * @param string $key The query parameter.
	 * @param string $default The default value to return if not found.
	 *
	 * @return string
	 */
	function get_request_param( string $key, string $default = '' ): string {
		// @codingStandardsIgnoreLine
		if ( empty( sanitize_text_field( wp_unslash( $_REQUEST[ $key ] ) ) ) ) {
			return $default;
		}

		// @codingStandardsIgnoreLine
		return wp_strip_all_tags( sanitize_text_field( wp_unslash( $_REQUEST[ $key ] ) ) );
	}
}
