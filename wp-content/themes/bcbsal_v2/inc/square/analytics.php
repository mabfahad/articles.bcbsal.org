<?php
/**
 * All the analytics functionalities
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( '_get_onetrust_active_groups' ) ) {
	/**
	 * Get the OneTrust consent groups from cookie value of OptanonConsent
	 *
	 * @return string
	 */
	function _get_onetrust_active_groups(): string {
		// extract consent group from cookie `OptanonConsent`.
		$optanon_consent_cookie_string = isset( $_COOKIE['OptanonConsent'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['OptanonConsent'] ) ) : '';
		if ( ! $optanon_consent_cookie_string ) {
			return 'no consent data';
		}

		$cookie_data_as_array     = explode( '&', $optanon_consent_cookie_string );
		$group_cookie_data_string = '';

		foreach ( $cookie_data_as_array as $key => $value ) {
			if ( str_contains( $value, 'groups=' ) ) {
				$group_cookie_data_string = $value;
				break;
			}
		}

		// i.e 'groups=C0001:1,C0002:1,C0003:1,C0004:0'.
		if ( ! $group_cookie_data_string ) {
			return 'no consent data';
		}

		$group_preferences_string_as_array = explode( ',', explode( '=', $group_cookie_data_string )[1] );
		$one_trust_active_groups           = array();

		foreach ( $group_preferences_string_as_array as $group ) {
			// @codingStandardsIgnoreLine
			[ $group_id, $consent_state ] = explode( ':', $group );

			if ( 1 === intval( $consent_state ) ) {
				$one_trust_active_groups[] = $group_id;
			}
		}

		return implode( ',', $one_trust_active_groups );
	}
}

if ( ! function_exists( 'square_after_first_head_tag' ) ) {
	/**
	 * Add tracking just after first <head> tag
	 *
	 * @return void
	 */
	function square_after_first_head_tag(): void {
		// @codingStandardsIgnoreStart
		// get OneTrust active groups.
		$active_groups = _get_onetrust_active_groups();

		// Google Analytics group.
		if ( str_contains( $active_groups, 'C0002' ) ) {
			if ( 'production' === get_environment() ) { ?>
				<!-- Global site tag (gtag.js) - Google Analytics -->
				<script async src="https://www.googletagmanager.com/gtag/js?id=UA-9517040-46"></script>
				<script>
					window.dataLayer = window.dataLayer || [];
					function gtag(){dataLayer.push(arguments);}
					gtag('js', new Date());

					gtag('config', 'UA-9517040-46');
				</script>
				<?php } else { ?>
				<!-- Global site tag (gtag.js) - Google Analytics -->
				<script async src="https://www.googletagmanager.com/gtag/js?id=UA-9517040-51"></script>
				<script>
					window.dataLayer = window.dataLayer || [];
					function gtag(){dataLayer.push(arguments);}
					gtag('js', new Date());

					gtag('config', 'UA-9517040-51');
				</script>
				<?php
				}
		}

		// Google Tag Manager group.
		if ( str_contains( $active_groups, 'C0004' ) ) {
			if ( 'production' === get_environment() || 'staging' === get_environment() ) {
				?>
				<!-- Google Tag Manager -->
				<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
							new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
						j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
						'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
					})(window,document,'script','gtmDataLayer','GTM-PS2PXZD');</script>
				<!-- End Google Tag Manager -->
				<?php
			}
		} // @codingStandardsIgnoreEnd
	}
}

if ( ! function_exists( 'square_before_last_head_tag' ) ) {
	/**
	 * Load necessary content just before ending </head> tag
	 *
	 * @return void
	 */
	function square_before_last_head_tag(): void {
		// @codingStandardsIgnoreStart
		?>
		<!-- OneTrust -->
		<script src="https://cdn.cookielaw.org/scripttemplates/otSDKStub.js" type="text/javascript" charset="UTF-8" data-domain-script="<?php echo esc_attr( SQ_ONE_TRUST_KEYS[ get_environment() ] ); ?>"></script>

		<!-- SQ MarTech -->
		<script src="https://martech-<?php echo esc_attr( get_environment() ); ?>-c.squarecdn.com/data-api.js" type="text/javascript"></script>

		<!-- Marketo -->
		<script src="https://www.workwithsquare.com/js/forms2/js/forms2.min.js"></script>

		<?php if ( is_single() && has_shortcode( get_the_content(), 'sq-knotch' ) ) { ?>
			<!-- Knotch -->
			<script src="https://www.knotch-cdn.com/ktag/latest/ktag.min.js?accountId=dc11198f-c7cf-44e1-b8cf-2df10fc784fe" async></script>
			<script	src="https://www.knotch-cdn.com/unit/latest/knotch.min.js" data-account="8bdb0b1f-95d3-4dd8-80ec-537d0f48310a" async></script>
		<?php }	// @codingStandardsIgnoreEnd
	}
}

if ( ! function_exists( 'get_cookie_preferences_text' ) ) {
	/**
	 * This will return the cookie preferences copy based on GEO
	 *
	 * @return string
	 */
	function get_cookie_preferences_text(): string {
		$geo = isset( $_COOKIE['squareGeo'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['squareGeo'] ) ) : '';

		if ( str_contains( $geo, 'US' ) || str_contains( $geo, 'CA' ) ) {
			return 'Opt-Out of Interest-Based Advertising';
		} elseif ( str_contains( $geo, 'GB' ) || str_contains( $geo, 'FR' ) || str_contains( $geo, 'ES' ) || str_contains( $geo, 'IE' ) ) {
			return 'Cookie Preferences';
		}

		return '';
	}
}
