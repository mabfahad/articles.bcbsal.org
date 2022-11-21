<?php
/**
 * All the constants required for the site
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

const SLIDE_MENU_CATEGORIES = 'Slide Menu - Categories';
const SLIDE_MENU_POST_TYPES = 'Slide Menu - Post Types';
const SLIDE_MENU_PAGES      = 'Slide Menu - Pages';
const SQ_SITE_NAME          = 'BCBSAl';
const PRIMARY_MENU          = 'Primary Menu';
const SECONDARY_MENU        = 'Secondary Menu';

const WHITELISTED_HTML = array(
	'span' => array(
		'class' => array(),
	),
	'div'  => array(
		'class' => array(),
	),
	'h2'   => array(
		'class' => array(),
	),
	'br'   => array(),
	'a'    => array(
		'target' => array(),
		'rel'    => array(),
		'href'   => array(),
	),
);

define(
	'IGNORE_CATEGORIES',
	array(
		'uncategorized',
		'hub-cta',
		'hub-products',
		'hub-settings',
		'case-studies',
		'tools',
		...surge_category_group_by_slug( 'podcasts' ),
		...surge_category_group_by_slug( 'collections' ),
	)
);

const LANDING_PAGES = array(
	'podcasts',
	'collections',
	'case-studies',
	'tools',
);

const MAPPING_CATEGORIES = array(
	'hub-cta',
	'hub-products',
	'hub-settings',
);

const PRIMARY_CATEGORIES = array(
	'starting-your-business',
	'reaching-customers',
	'selling-anywhere',
	'managing-your-finances',
	'operating-your-business',
	'growing-your-team',
);

const TAG_TOPICS = array(
	'automation',
	'ecommerce',
	'marketing',
	'sales',
	'staff-retention',
	'leadership',
	'cash-flow',
	'taxes',
	'social-commerce',
	'multiple-locations',
	'trends',
	'mobile-commerce',
	'business-growth',
	'fulfillment',
	'financing',
	'inventory',
	'supply-chain',
	'hiring',
	'technology',
);

const TAG_CONTENT_TYPES = array(
	'article',
	'guide',
	'research',
	'ebook',
	'template',
	'calculator',
	'infographic',
	'podcast',
	'video',
	'case-study',
	'tool',
	'collection',
	'series',
	'webinar',
);

const TAG_INDUSTRIES = array(
	'food-and-beverage',
	'beauty-and-personal-care',
	'health-and-fitness',
	'home-and-repair',
	'retail',
	'nonprofit',
	'entertainment',
	'multiple-industries',
	'professional-services',
);

const TAG_BUSINESS_TYPES = array(
	'content-creator',
	'side-hustle',
	'franchise',
	'enterprise',
);

const TAG_SQUARE_SOLUTIONS = array(
	'square-savings',
	'square-loans',
	'square-appointments',
	'square-inventory-management',
	'square-online',
	'square-online-checkout',
	'square-payments',
	'square-photo-studio',
	'square-register',
	'square-stand',
	'square-terminal',
	'square-virtual-terminal',
	'square-customer-directory',
	'square-gift-cards',
	'square-loyalty',
	'square-marketing',
	'square-messages',
	'square-crew',
	'square-payroll',
	'square-team-management',
	'square-restaurant-kds',
	'square-restaurant-pos',
	'square-retail-pos',
	'square-point-of-sale',
	'square-reader',
	'square-invoices',
	'square-checking',
);
