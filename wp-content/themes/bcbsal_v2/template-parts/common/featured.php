<?php
/**
 * Site Featured Slider
 *
 * @package bcbsal_v2
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<section class="banner-section">
	<section aria-label="Newest Photos">
		<div class="carousel" data-carousel>
			<button class="carousel-button prev" data-carousel-button="prev">
				<img src="<?php echo esc_url( image_url( 'arrow-prev.png' ) ); ?>" alt="">
			</button>
			<button class="carousel-button next" data-carousel-button="next">
				<img src="<?php echo esc_url( image_url( 'arrow-next.png' ) ); ?>" alt="">
			</button>
			<ul data-slides>
				<li class="slide" data-active>
					<img src="<?php echo esc_url( image_url( 'slider-img-1.jpg' ) ); ?>" alt="Nature Image #1">
					<div class="slider-content">
						<a href="#">Article</a>
						<h4>Featured | Wellness</h4>
						<h6>How To Find The Best Doctors For You</h6>
						<p>by Lucy Maher</p>
					</div>
				</li>
				<li class="slide">
					<img src="<?php echo esc_url( image_url( 'slider-img-2.jpg' ) ); ?>" alt="Nature Image #2">
					<div class="slider-content">
						<a href="#">Article</a>
						<h4>Featured | Insurance Education</h4>
						<h6>Tax Breaks If You're Self-Employed</h6>
						<p>by Kimberly Lankford, Contributing Editor, and Kiplinger's Personal Finance</p>
					</div>
				</li>
				<li class="slide">
					<img src="<?php echo esc_url( image_url( 'slider-img-3.jpg' ) ); ?>" alt="Nature Image #3">
					<div class="slider-content">
						<a href="#">Article</a>
						<h4>Featured | Insurance Education</h4>
						<h6>6 Tips to Help Organize Your Finances</h6>
						<p>by Kate Silver</p>
					</div>
				</li>
			</ul>
		</div>
	</section>
</section>
