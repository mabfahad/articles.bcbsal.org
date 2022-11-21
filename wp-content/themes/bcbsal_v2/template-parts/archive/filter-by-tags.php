<?php
/**
 * Filter module in category archive
 *
 * @package square
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$modifier    = $args['modifier'] ?? '';
$total       = $args['total'];
$category_id = $args['category_id'];
$tags_group  = $args['tags_group'];
$tags_list   = array();

foreach ( $args['tags'] as $tag_slug ) {
	$tags_list[] = get_tag_meta_by_category( $tag_slug, $category_id );
}
?>

<div class="filter-by-tags <?php echo esc_attr( $modifier ); ?>">
	<button type="button" data-tag-id="all" data-total-posts="<?php echo esc_attr( $total ); ?>" class="filter-by-tags__topic filter-by-tags__topic-all filter-by-tags__popup-toggle filter-by-tags__topic--active">
		<span class="filter-by-tags__topic--name">All</span>
		<span class="filter-by-tags__topic--count">
			<?php echo esc_attr( $total ); ?>
		</span>
		<img src="<?php echo esc_url( image_url( 'icon-caret-white.svg' ) ); ?>" alt="-">
	</button>
	<div class="filter-by-tags__topics filter-by-tags__topics--desktop">
		<button type="button" data-tag-id="all" data-total-posts="<?php echo esc_attr( $total ); ?>" class="filter-by-tags__topic filter-by-tags__topic-all filter-by-tags__topic--active">
			<span class="filter-by-tags__topic--name">All</span>
			<span class="filter-by-tags__topic--count">
				<?php echo esc_attr( $total ); ?>
			</span>
		</button>
		<?php
		foreach ( $tags_list as $index => $t ) {
			if ( 0 === $t->posts_count ) {
				continue;
			}
			?>
			<button type="button" data-tag-id="<?php echo esc_attr( $t->term_id ); ?>" data-total-posts="<?php echo esc_attr( $t->posts_count ); ?>" class="filter-by-tags__topic filter-by-tags__topic-<?php echo esc_attr( $t->term_id ); ?>">
				<span class="filter-by-tags__topic--name"><?php echo esc_attr( $t->name ); ?></span>
				<span class="filter-by-tags__topic--count"><?php echo esc_attr( $t->posts_count ); ?></span>
			</button>
			<?php if ( 4 === $index ) { ?>
				<button type="button" class="filter-by-tags__view-all">See more <?php echo esc_attr( $tags_group ); ?></button>
				<?php
			}
		}
		?>
	</div>
	<div class="filter-by-tags__popup">
		<h3 class="filter-by-tags__popup--title">Filter by <?php echo esc_attr( $tags_group ); ?></h3>
		<div class="filter-by-tags__topics filter-by-tags__topics--mobile">
			<button type="button" data-tag-id="all" data-total-posts="<?php echo esc_attr( $total ); ?>" class="filter-by-tags__topic filter-by-tags__topic-all filter-by-tags__topic--active">
				<span class="filter-by-tags__topic--name">All</span>
				<span class="filter-by-tags__topic--count">
					<?php echo esc_attr( $total ); ?>
				</span>
			</button>
			<?php
			foreach ( $tags_list as $t ) {
				if ( 0 === $t->posts_count ) {
					continue;
				}
				?>
				<button type="button" data-tag-id="<?php echo esc_attr( $t->term_id ); ?>" data-total-posts="<?php echo esc_attr( $t->posts_count ); ?>" class="filter-by-tags__topic filter-by-tags__topic-<?php echo esc_attr( $t->term_id ); ?>">
					<span class="filter-by-tags__topic--name"><?php echo esc_attr( $t->name ); ?></span>
					<span class="filter-by-tags__topic--count"><?php echo esc_attr( $t->posts_count ); ?></span>
				</button>
			<?php } ?>
		</div>
		<button class="filter-by-tags__popup--close" aria-label="Close">
			<img src="<?php echo esc_url( image_url( 'icon-close-black.svg' ) ); ?>" alt="-">
		</button>
	</div>
</div>
