<?php
/**
 * Portfolio Grid block — frontend template.
 *
 * @package WLC\Blocks
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner blocks content (unused).
 * @var WP_Block $block      Block instance.
 */

defined( 'ABSPATH' ) || exit;

$data         = ( new \WLC\Blocks\Services\Blocks\PortfolioGridController() )->getData( $attributes );
$posts        = $data['posts'];
$categories   = $data['categories'];
$technologies = $data['technologies'];
$posts_per_page           = $data['postsPerPage'];
$show_category_filter     = (bool) ( $attributes['showCategoryFilter'] ?? true );
$show_technology_filter   = (bool) ( $attributes['showTechnologyFilter'] ?? true );
$unique_id    = wp_unique_id( 'wlc-portfolio-grid-' );
$max_pages    = max( 1, (int) ceil( count( $posts ) / $posts_per_page ) );

wp_interactivity_state(
	'wlc/portfolio-grid',
	[
		'posts'          => $posts,
		'activeCategory' => '',
		'activeTech'     => '',
		'currentPage'    => 1,
		'postsPerPage'   => $posts_per_page,
	]
);
?>
<section
	<?php echo get_block_wrapper_attributes( [ 'class' => 'wlc-portfolio-grid', 'id' => esc_attr( $unique_id ) ] ); ?>
	data-wp-interactive="wlc/portfolio-grid"
	data-wp-context='<?php echo wp_json_encode( [ 'blockId' => $unique_id ] ); ?>'
>
	<noscript>
		<p class="wlc-portfolio-grid__noscript">
			<a href="<?php echo esc_url( home_url( '/portfolio' ) ); ?>"><?php esc_html_e( 'Zobacz wszystkie realizacje', 'wlc-blocks' ); ?></a>
		</p>
	</noscript>

	<?php if ( $show_category_filter && ! empty( $categories ) ) : ?>
	<div class="wlc-portfolio-grid__filters">
		<div class="wlc-portfolio-grid__filter-row">
			<span class="wlc-portfolio-grid__filter-label"><?php esc_html_e( 'Kategoria:', 'wlc-blocks' ); ?></span>
			<div class="wlc-portfolio-grid__filter-buttons" role="group" aria-label="<?php esc_attr_e( 'Filtruj po kategorii', 'wlc-blocks' ); ?>">
				<button
					class="wlc-portfolio-grid__filter-btn"
					data-wp-on--click="actions.filterByCategory"
					data-wp-class--wlc-portfolio-grid__filter-btn--active="state.isCategoryActive"
					data-wp-context='{"filterValue":""}'
				><?php esc_html_e( 'Wszystkie', 'wlc-blocks' ); ?></button>
				<?php foreach ( $categories as $cat ) : ?>
				<button
					class="wlc-portfolio-grid__filter-btn"
					data-wp-on--click="actions.filterByCategory"
					data-wp-class--wlc-portfolio-grid__filter-btn--active="state.isCategoryActive"
					data-wp-context='<?php echo wp_json_encode( [ 'filterValue' => $cat['slug'] ] ); ?>'
				><?php echo esc_html( $cat['name'] ); ?></button>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<?php if ( $show_technology_filter && ! empty( $technologies ) ) : ?>
	<div class="wlc-portfolio-grid__filters wlc-portfolio-grid__filters--tech">
		<div class="wlc-portfolio-grid__filter-row">
			<span class="wlc-portfolio-grid__filter-label"><?php esc_html_e( 'Technologia:', 'wlc-blocks' ); ?></span>
			<div class="wlc-portfolio-grid__filter-buttons" role="group" aria-label="<?php esc_attr_e( 'Filtruj po technologii', 'wlc-blocks' ); ?>">
				<button
					class="wlc-portfolio-grid__filter-btn"
					data-wp-on--click="actions.filterByTech"
					data-wp-class--wlc-portfolio-grid__filter-btn--active="state.isTechActive"
					data-wp-context='{"filterValue":""}'
				><?php esc_html_e( 'Wszystkie', 'wlc-blocks' ); ?></button>
				<?php foreach ( $technologies as $tech ) : ?>
				<button
					class="wlc-portfolio-grid__filter-btn"
					data-wp-on--click="actions.filterByTech"
					data-wp-class--wlc-portfolio-grid__filter-btn--active="state.isTechActive"
					data-wp-context='<?php echo wp_json_encode( [ 'filterValue' => $tech['slug'] ] ); ?>'
				><?php echo esc_html( $tech['name'] ); ?></button>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<?php if ( ( $show_category_filter && ! empty( $categories ) ) || ( $show_technology_filter && ! empty( $technologies ) ) ) : ?>
	<div class="wlc-portfolio-grid__divider" aria-hidden="true"></div>
	<?php endif; ?>

	<div class="wlc-portfolio-grid__grid">
		<?php foreach ( $posts as $post ) : ?>
		<article
			class="wlc-portfolio-card"
			data-wp-context='<?php echo wp_json_encode( [
				'postId'        => $post['id'],
				'categorySlugs' => $post['categorySlugs'],
				'techSlugs'     => $post['techSlugs'],
			] ); ?>'
			data-wp-class--wlc-portfolio-card--hidden="!state.isPostVisible"
		>
			<a href="<?php echo esc_url( $post['permalink'] ); ?>" class="wlc-portfolio-card__link">
				<div class="wlc-portfolio-card__image-wrap">
					<?php if ( ! empty( $post['thumbnailUrl'] ) ) : ?>
					<img
						src="<?php echo esc_url( $post['thumbnailUrl'] ); ?>"
						alt=""
						aria-hidden="true"
						class="wlc-portfolio-card__image"
						loading="lazy"
					/>
					<?php else : ?>
					<div class="wlc-portfolio-card__image-placeholder" aria-hidden="true"></div>
					<?php endif; ?>
				</div>
				<div class="wlc-portfolio-card__content">
					<div class="wlc-portfolio-card__tags">
						<?php foreach ( $post['technologies'] as $tech ) : ?>
						<span class="wlc-portfolio-card__tag wlc-portfolio-card__tag--tech"><?php echo esc_html( $tech['name'] ); ?></span>
						<?php endforeach; ?>
						<?php foreach ( $post['categories'] as $cat ) : ?>
						<span class="wlc-portfolio-card__tag wlc-portfolio-card__tag--category"><?php echo esc_html( $cat['name'] ); ?></span>
						<?php endforeach; ?>
					</div>
					<h3 class="wlc-portfolio-card__title"><?php echo esc_html( $post['title'] ); ?></h3>
					<?php if ( ! empty( $post['excerpt'] ) ) : ?>
					<p class="wlc-portfolio-card__excerpt"><?php echo esc_html( $post['excerpt'] ); ?></p>
					<?php endif; ?>
					<span class="wlc-portfolio-card__readmore" aria-hidden="true"><?php esc_html_e( 'Read more →', 'wlc-blocks' ); ?></span>
				</div>
			</a>
		</article>
		<?php endforeach; ?>
	</div>

	<p class="wlc-portfolio-grid__empty" data-wp-class--wlc-portfolio-grid__empty--hidden="state.hasVisiblePosts">
		<?php esc_html_e( 'Brak wyników dla wybranych filtrów.', 'wlc-blocks' ); ?>
	</p>

	<nav
		class="wlc-portfolio-grid__pagination"
		aria-label="<?php esc_attr_e( 'Paginacja portfolio', 'wlc-blocks' ); ?>"
		data-wp-class--wlc-portfolio-grid__pagination--hidden="!state.hasPagination"
	>
		<button
			class="wlc-portfolio-grid__page-btn wlc-portfolio-grid__page-btn--arrow"
			data-wp-on--click="actions.prevPage"
			data-wp-bind--disabled="state.isFirstPage"
			aria-label="<?php esc_attr_e( 'Poprzednia strona', 'wlc-blocks' ); ?>"
		>←</button>
		<div class="wlc-portfolio-grid__page-numbers">
			<?php for ( $p = 1; $p <= $max_pages; $p++ ) : ?>
			<button
				class="wlc-portfolio-grid__page-btn"
				data-wp-on--click="actions.goToPage"
				data-wp-class--wlc-portfolio-grid__page-btn--active="state.isCurrentPage"
				data-wp-class--wlc-portfolio-grid__page-btn--hidden="state.isPageHidden"
				data-wp-context='<?php echo wp_json_encode( [ 'pageNum' => $p ] ); ?>'
				aria-label="<?php echo esc_attr( sprintf( __( 'Strona %d', 'wlc-blocks' ), $p ) ); ?>"
			><?php echo esc_html( (string) $p ); ?></button>
			<?php endfor; ?>
		</div>
		<button
			class="wlc-portfolio-grid__page-btn wlc-portfolio-grid__page-btn--arrow"
			data-wp-on--click="actions.nextPage"
			data-wp-bind--disabled="state.isLastPage"
			aria-label="<?php esc_attr_e( 'Następna strona', 'wlc-blocks' ); ?>"
		>→</button>
	</nav>
</section>
