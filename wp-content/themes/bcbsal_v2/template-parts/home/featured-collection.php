<?php
/**
 * Featured collection section of the home template
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$collection_post_ids = get_post_ids_by_urls( $args['collection'] );

get_template_part(
	'template-parts/common/featured-slider',
	'featured-slider',
	array(
		'post_ids' => $collection_post_ids,
		'modifier' => 'home-featured-collection',
	)
);
