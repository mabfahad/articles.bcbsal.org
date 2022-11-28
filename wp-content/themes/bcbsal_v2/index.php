<?php
/**
 * Index page template
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
get_template_part( 'template-parts/common/featured' );
get_template_part( 'template-parts/common/latest-articles' );
get_template_part( 'template-parts/common/social-media' );
get_footer();
