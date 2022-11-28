<?php
/**
 * Site default Header
 *
 * @package bcbsal_v2
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$modifier = $args['modifier'] ?? '';
$is_black = 'site-header--black' === $modifier ?? false;
?>
<nav class="main-nav">
		<div class="main-nav-container">
			<div class="site-logo">
				<a href="./index.html">
					<img width="290px" class="site-img" src="<?php echo esc_url( image_url( 'BCBS-Logo.png' ) ); ?>" alt="" />
				</a>
			</div>
			<ul class="main-nav-ul">
				<?php surge_custom_menu( 'primary-menu' ); ?>
				<input class="input-text" type="text" placeholder="Search Here">

				<li id="search-text">
					<a><img width="25px" src="<?php echo esc_url( image_url( 'icon-search.png' ) ); ?>"/></a>
			</ul>
			<button class="main-nav-toggle-btn" id="main-nav-toogle-btn">
				<span>Menu</span>
				<img width="50px" src="<?php echo esc_url( image_url( 'icon-menu.png' ) ); ?>" alt="menu"/>
			</button>
		</div>
		<nav class="nav-bottom">
			<div class="container">
				<ul>
					<li>
						<div class="dropdown" id="dropdown">
							<a > <span>myBlueCross</span>  <img class="nav-bottom-img-1" src="<?php echo esc_url( image_url( 'down-arrow-1.png' ) ); ?>" alt="menu"/></a>
							<div class="dropdown-content">

								<div class="dropdown-div">
									<p>
										Account Summary
									</p>
									<p>Claim Statement</p>
									<p>ID Cards</p>
								</div>

							</div>
						</div>
					</li>
					<li>
						<a href="#">Find a Doctor</a>
					</li>
				</ul>
				<a class="register-btn" href="#">
					Register for myBlueCross
				</a>
			</div>
		</nav>
	</nav>
