<?php
/**
 * Site default footer
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// get cookie preferences copy.
$cookie_preferences_text = '';
?>
<footer class="site-footer">
	<div class="container">
		<div class="row site-footer-lead-gen">
			<div class="col-sm-7">
				<div class="site-footer-signup site-footer-signup--marketo">
					<h3 class="site-footer-signup__title">
						<?php echo esc_html( 'Get the best of ' . SQ_SITE_NAME . ', right in your inbox' ); ?>
					</h3>
					<form class="site-footer-signup__form newsletter-email__form" autocomplete="off" data-form-id="7927">
						<input class="site-footer-signup__form-input newsletter-email__form-input" aria-label="Email address" type="email" name="email" placeholder="Enter email address"  required />
						<button class="button site-footer-signup__button newsletter-email__form-button" type="submit">Subscribe</button>
						<img class="newsletter-email__form-success" width="18" height="19" src="<?php echo esc_url( image_url( 'icon-check-blue.svg' ) ); ?>" alt="✓">
					</form>
					<div class="newsletter-email__form-msg"></div>
					<form id="mktoForm_7927"></form>
				</div>
			</div>
			<div class="col-sm-5">
				<ul class="site-footer-categories">
					<?php surge_custom_menu( SLIDE_MENU_CATEGORIES ); ?>
				</ul>
			</div>
		</div>
		<div class="site-footer-site-logo">
			<img height="auto" width="100%" src="<?php echo esc_url( image_url( 'logo-tsq-white-temp.svg' ) ); ?>" alt="The Reader">
		</div>
		<div class="site-footer-disclaimer">
			The information provided on this publication is for general informational purposes only. While we strive to keep
			the information up to date, we make no representations or warranties of any kind about the completeness, accuracy,
			reliability, or suitability for your business, of the information provided or the views expressed herein. For
			specific advice applicable to your business, please contact a professional.
		</div>
		<div class="row site-footer-foot site-footer--border-top">
			<div class="col-sm-3">
				<a href="https://squareup.com/" class="site-footer-publication-logo">
					<img height="24" width="91" src="<?php echo esc_url( image_url( 'logo-square-white.svg' ) ); ?>" alt="Square">
				</a>
			</div>
			<div class="col-sm-9">
				<div class="site-footer-legal">
					<ul class="site-footer-legal__items">
						<li class="site-footer-legal__item site-footer-legal__item--copyright">© <?php echo esc_html( gmdate( 'Y' ) . ' Block, Inc.' ); ?></li>
						<li class="site-footer-legal__item"><a class="site-footer-legal__link" href="https://squareup.com/us/en/legal/general/privacy">Privacy Notice</a></li>
						<li class="site-footer-legal__item"><a class="site-footer-legal__link" href="https://squareup.com/us/en/legal/general/ua">Terms of Service</a></li>
						<?php if ( $cookie_preferences_text ) { ?>
							<li class="site-footer-legal__item"><button class="sq-cookie__preference"><?php echo esc_attr( $cookie_preferences_text ); ?></button></li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</footer>
