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

// Read active filters from URL query params (sanitized).
$active_category = sanitize_key( $_GET['portfolio-category'] ?? '' ); // phpcs:ignore WordPress.Security.NonceVerification
$active_tech     = sanitize_key( $_GET['technology'] ?? '' );          // phpcs:ignore WordPress.Security.NonceVerification
$current_page    = max( 1, absint( $_GET['paged'] ?? 1 ) );            // phpcs:ignore WordPress.Security.NonceVerification

$data         = ( new \WLC\Blocks\Services\Blocks\PortfolioGridController() )->getData(
	$attributes,
	[
		'activeCategory' => $active_category,
		'activeTech'     => $active_tech,
		'currentPage'    => $current_page,
	]
);

$posts                  = $data['posts'];
$categories             = $data['categories'];
$technologies           = $data['technologies'];
$posts_per_page         = $data['postsPerPage'];
$total_pages            = $data['totalPages'];
$show_category_filter   = (bool) ( $attributes['showCategoryFilter'] ?? true );
$show_technology_filter = (bool) ( $attributes['showTechnologyFilter'] ?? true );
$unique_id              = wp_unique_id( 'wlc-portfolio-grid-' );
$current_url            = ( is_ssl() ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

/**
 * Build a filter URL preserving existing query params.
 *
 * @param string $param Param name to set/clear.
 * @param string $value Value; empty string removes param.
 * @return string
 */
$filter_url = function ( string $param, string $value ) use ( $current_url ): string {
	$url = remove_query_arg( 'paged', $current_url );
	return $value
		? esc_url( add_query_arg( $param, $value, $url ) )
		: esc_url( remove_query_arg( $param, $url ) );
};

$page_url = function ( int $page ) use ( $current_url ): string {
	return $page > 1
		? esc_url( add_query_arg( 'paged', $page, $current_url ) )
		: esc_url( remove_query_arg( 'paged', $current_url ) );
};

wp_interactivity_state(
	'wlc/portfolio-grid',
	[
		'currentPage' => $current_page,
		'totalPages'  => $total_pages,
	]
);
?>
<section
	<?php echo get_block_wrapper_attributes( [ 'class' => 'wlc-portfolio-grid', 'id' => esc_attr( $unique_id ) ] ); ?>
	data-wp-interactive="wlc/portfolio-grid"
	data-wp-router-region="<?php echo esc_attr( $unique_id ); ?>"
>
	<?php if ( $show_category_filter && ! empty( $categories ) ) : ?>
	<div class="wlc-portfolio-grid__filters">
		<div class="wlc-portfolio-grid__filter-row">
			<span class="wlc-portfolio-grid__filter-label"><?php esc_html_e( 'Kategoria:', 'wlc-blocks' ); ?></span>
			<div class="wlc-portfolio-grid__filter-buttons" role="group" aria-label="<?php esc_attr_e( 'Filtruj po kategorii', 'wlc-blocks' ); ?>">
				<a
					href="<?php echo $filter_url( 'portfolio-category', '' ); // phpcs:ignore ?>"
					class="wlc-portfolio-grid__filter-btn<?php echo '' === $active_category ? ' wlc-portfolio-grid__filter-btn--active' : ''; ?>"
					data-wp-on--click="actions.filterByCategory"
					aria-current="<?php echo '' === $active_category ? 'true' : 'false'; ?>"
				><?php esc_html_e( 'Wszystkie', 'wlc-blocks' ); ?></a>
				<?php foreach ( $categories as $cat ) : ?>
				<a
					href="<?php echo $filter_url( 'portfolio-category', $cat['slug'] ); // phpcs:ignore ?>"
					class="wlc-portfolio-grid__filter-btn<?php echo $cat['slug'] === $active_category ? ' wlc-portfolio-grid__filter-btn--active' : ''; ?>"
					data-wp-on--click="actions.filterByCategory"
					aria-current="<?php echo $cat['slug'] === $active_category ? 'true' : 'false'; ?>"
				><?php echo esc_html( $cat['name'] ); ?></a>
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
				<a
					href="<?php echo $filter_url( 'technology', '' ); // phpcs:ignore ?>"
					class="wlc-portfolio-grid__filter-btn<?php echo '' === $active_tech ? ' wlc-portfolio-grid__filter-btn--active' : ''; ?>"
					data-wp-on--click="actions.filterByTech"
					aria-current="<?php echo '' === $active_tech ? 'true' : 'false'; ?>"
				><?php esc_html_e( 'Wszystkie', 'wlc-blocks' ); ?></a>
				<?php foreach ( $technologies as $tech ) : ?>
				<a
					href="<?php echo $filter_url( 'technology', $tech['slug'] ); // phpcs:ignore ?>"
					class="wlc-portfolio-grid__filter-btn<?php echo $tech['slug'] === $active_tech ? ' wlc-portfolio-grid__filter-btn--active' : ''; ?>"
					data-wp-on--click="actions.filterByTech"
					aria-current="<?php echo $tech['slug'] === $active_tech ? 'true' : 'false'; ?>"
				><?php echo esc_html( $tech['name'] ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<?php if ( ( $show_category_filter && ! empty( $categories ) ) || ( $show_technology_filter && ! empty( $technologies ) ) ) : ?>
	<div class="wlc-portfolio-grid__divider" aria-hidden="true"></div>
	<?php endif; ?>

	<?php if ( ! empty( $posts ) ) : ?>
	<div class="wlc-portfolio-grid__grid">
		<?php foreach ( $posts as $post ) : ?>
		<article class="wlc-portfolio-card">
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
	<?php else : ?>
	<p class="wlc-portfolio-grid__empty">
		<?php esc_html_e( 'Brak wyników dla wybranych filtrów.', 'wlc-blocks' ); ?>
	</p>
	<?php endif; ?>

	<?php if ( $total_pages > 1 ) : ?>
	<nav
		class="wlc-portfolio-grid__pagination"
		aria-label="<?php esc_attr_e( 'Paginacja portfolio', 'wlc-blocks' ); ?>"
	>
		<a
			href="<?php echo $page_url( max( 1, $current_page - 1 ) ); // phpcs:ignore ?>"
			class="wlc-portfolio-grid__page-btn wlc-portfolio-grid__page-btn--arrow<?php echo 1 === $current_page ? ' wlc-portfolio-grid__page-btn--disabled' : ''; ?>"
			data-wp-on--click="actions.prevPage"
			aria-label="<?php esc_attr_e( 'Poprzednia strona', 'wlc-blocks' ); ?>"
			<?php echo 1 === $current_page ? 'aria-disabled="true"' : ''; ?>
		>←</a>
		<?php for ( $p = 1; $p <= $total_pages; $p++ ) : ?>
		<a
			href="<?php echo $page_url( $p ); // phpcs:ignore ?>"
			class="wlc-portfolio-grid__page-btn<?php echo $p === $current_page ? ' wlc-portfolio-grid__page-btn--active' : ''; ?>"
			data-wp-on--click="actions.goToPage"
			aria-label="<?php echo esc_attr( sprintf( __( 'Strona %d', 'wlc-blocks' ), $p ) ); ?>"
			aria-current="<?php echo $p === $current_page ? 'page' : 'false'; ?>"
		><?php echo esc_html( (string) $p ); ?></a>
		<?php endfor; ?>
		<a
			href="<?php echo $page_url( min( $total_pages, $current_page + 1 ) ); // phpcs:ignore ?>"
			class="wlc-portfolio-grid__page-btn wlc-portfolio-grid__page-btn--arrow<?php echo $current_page === $total_pages ? ' wlc-portfolio-grid__page-btn--disabled' : ''; ?>"
			data-wp-on--click="actions.nextPage"
			aria-label="<?php esc_attr_e( 'Następna strona', 'wlc-blocks' ); ?>"
			<?php echo $current_page === $total_pages ? 'aria-disabled="true"' : ''; ?>
		>→</a>
	</nav>
	<?php endif; ?>
</section>
