<?php
/**
 * Filter module in tag archive
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$modifier        = $args['modifier'] ?? '';
$total           = $args['total'];
$tag_id          = $args['tag_id'];
$categories_list = array();

foreach ( PRIMARY_CATEGORIES as $category_slug ) {
	$categories_list[] = get_category_meta_by_tag( $category_slug, $tag_id );
}
?>

<div class="filter-by-categories <?php echo esc_attr( $modifier ); ?>">
	<button type="button" data-category-id="all" data-total-posts="<?php echo esc_attr( $total ); ?>" class="filter-by-categories__topic filter-by-categories__topic-all filter-by-categories__popup-toggle filter-by-categories__topic--active">
		<span class="filter-by-categories__topic--name">All</span>
		<span class="filter-by-categories__topic--count">
			<?php echo esc_attr( $total ); ?>
		</span>
		<img src="<?php echo esc_url( image_url( 'icon-caret-white.svg' ) ); ?>" alt="-">
	</button>
	<div class="filter-by-categories__topics filter-by-categories__topics--desktop">
		<button type="button" data-category-id="all" data-total-posts="<?php echo esc_attr( $total ); ?>" class="filter-by-categories__topic filter-by-categories__topic-all filter-by-categories__topic--active">
			<span class="filter-by-categories__topic--name">All</span>
			<span class="filter-by-categories__topic--count">
				<?php echo esc_attr( $total ); ?>
			</span>
		</button>
		<?php
		foreach ( $categories_list as $index => $c ) {
			if ( 0 === $c->posts_count ) {
				continue;
			}
			?>
			<button type="button" data-category-id="<?php echo esc_attr( $c->term_id ); ?>" data-total-posts="<?php echo esc_attr( $c->posts_count ); ?>" class="filter-by-categories__topic filter-by-categories__topic-<?php echo esc_attr( $c->term_id ); ?>">
				<span class="filter-by-categories__topic--name"><?php echo esc_attr( $c->name ); ?></span>
				<span class="filter-by-categories__topic--count"><?php echo esc_attr( $c->posts_count ); ?></span>
			</button>
			<?php
		}
		?>
	</div>
	<div class="filter-by-categories__popup">
		<h3 class="filter-by-categories__popup--title">Filter by categories</h3>
		<div class="filter-by-categories__topics filter-by-categories__topics--mobile">
			<button type="button" data-category-id="all" data-total-posts="<?php echo esc_attr( $total ); ?>" class="filter-by-categories__topic filter-by-categories__topic-all filter-by-categories__topic--active">
				<span class="filter-by-categories__topic--name">All</span>
				<span class="filter-by-categories__topic--count">
					<?php echo esc_attr( $total ); ?>
				</span>
			</button>
			<?php
			foreach ( $categories_list as $c ) {
				if ( 0 === $c->posts_count ) {
					continue;
				}
				?>
				<button type="button" data-category-id="<?php echo esc_attr( $c->term_id ); ?>" data-total-posts="<?php echo esc_attr( $c->posts_count ); ?>" class="filter-by-categories__topic filter-by-categories__topic-<?php echo esc_attr( $c->term_id ); ?>">
					<span class="filter-by-categories__topic--name"><?php echo esc_attr( $c->name ); ?></span>
					<span class="filter-by-categories__topic--count"><?php echo esc_attr( $c->posts_count ); ?></span>
				</button>
			<?php } ?>
		</div>
		<button class="filter-by-categories__popup--close" aria-label="Close">
			<img src="<?php echo esc_url( image_url( 'icon-close-black.svg' ) ); ?>" alt="-">
		</button>
	</div>
</div>
