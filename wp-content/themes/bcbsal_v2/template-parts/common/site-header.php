<?php
/**
 * Site default Footer
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$modifier = $args['modifier'] ?? '';
$is_black = 'site-header--black' === $modifier ?? false;
?>
<div id="header">
	<header class="site-header site-header--border-bottom <?php echo esc_attr( $modifier ); ?>">
		<div class="container desktop-header">
			<div class="row">
				<div class="col-md-4">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-header-site-logo">
						<?php if ( $is_black ) { ?>
							<img width="300" src="<?php echo esc_url( image_url( 'BCBS-Logo.png' ) ); ?>" alt="BCBSal">
						<?php } else { ?>
							<img width="300" src="<?php echo esc_url( image_url( 'BCBS-Logo.png' ) ); ?>" alt="BCBSal">
						<?php } ?>
					</a>
				</div>

				<div class="col-md-8 text-right">
					<ul class="site-header__menu">
						<?php surge_custom_menu( PRIMARY_MENU ); ?>
					</ul>
					<form role="search" method="get" class="search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<input type="text" placeholder="Search here" name="s" id="s">
						<img width="24" height="auto" src="<?php echo esc_url( image_url( 'icon-search.png' ) ); ?>" alt="Search">
					</form>
				</div>
			</div>
		</div>
		<div class="secondary-menu">
			<div class="container">
				<ul class="list">
					<li><span>myBlueCross</span>
						<ul class="list__dropdown">
							<?php surge_custom_menu( 'Secondary Menu' ); ?>
						</ul>
					</li>
					<li>Find a doctor</li>
				</ul>

				<a href="https://www.bcbsal.org/webapps/customeraccess/Dispatch?application=org.bcbsal.inet.customermgmt.CustomerSelfRegistrationApplication&amp;responsive=true" class="btn-custom">Register <span> for myBlueCross</span></a>
			</div><!-- ./container -->
		</div>
	</header>
</div>
