<?php
/**
 * Handles data preparation for the Portfolio Grid block.
 *
 * @package WLC\Blocks
 */

namespace WLC\Blocks\Services\Blocks;

defined( 'ABSPATH' ) || exit;

/**
 * Controller for the wlc/portfolio-grid block.
 * Queries posts server-side with active filters from URL params.
 */
class PortfolioGridController {

	/**
	 * Returns paginated, filtered posts and taxonomy terms for the block.
	 *
	 * @param array $attributes    Block attributes from the editor.
	 * @param array $filters       Active filters: activeCategory, activeTech, currentPage.
	 * @return array{
	 *   posts: array,
	 *   categories: array,
	 *   technologies: array,
	 *   postsPerPage: int,
	 *   currentPage: int,
	 *   totalPages: int,
	 *   totalPosts: int,
	 *   activeCategory: string,
	 *   activeTech: string
	 * }
	 */
	public function getData( array $attributes, array $filters = [] ): array {
		$posts_per_page  = isset( $attributes['postsPerPage'] ) ? absint( $attributes['postsPerPage'] ) : 9;
		$active_category = sanitize_key( $filters['activeCategory'] ?? '' );
		$active_tech     = sanitize_key( $filters['activeTech'] ?? '' );
		$current_page    = max( 1, absint( $filters['currentPage'] ?? 1 ) );

		$query_args = [
			'post_type'      => 'wlc-portfolio',
			'posts_per_page' => $posts_per_page,
			'paged'          => $current_page,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		];

		$tax_query = [];

		if ( $active_category ) {
			$tax_query[] = [
				'taxonomy' => 'portfolio-category',
				'field'    => 'slug',
				'terms'    => $active_category,
			];
		}

		if ( $active_tech ) {
			$tax_query[] = [
				'taxonomy' => 'technology',
				'field'    => 'slug',
				'terms'    => $active_tech,
			];
		}

		if ( count( $tax_query ) > 1 ) {
			$tax_query['relation'] = 'AND';
		}

		if ( ! empty( $tax_query ) ) {
			$query_args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery
		}

		$query = new \WP_Query( $query_args );

		$posts = [];
		foreach ( $query->posts as $post ) {
			$thumbnail_url = '';
			if ( has_post_thumbnail( $post->ID ) ) {
				$thumbnail_url = (string) get_the_post_thumbnail_url( $post->ID, 'large' );
			}

			$categories   = $this->getPostTerms( $post->ID, 'portfolio-category' );
			$technologies = $this->getPostTerms( $post->ID, 'technology' );

			$posts[] = [
				'id'           => $post->ID,
				'title'        => get_the_title( $post->ID ),
				'excerpt'      => wp_strip_all_tags( get_the_excerpt( $post->ID ) ),
				'permalink'    => (string) get_permalink( $post->ID ),
				'thumbnailUrl' => $thumbnail_url,
				'categories'   => $categories,
				'technologies' => $technologies,
			];
		}

		$total_pages = max( 1, (int) $query->max_num_pages );
		$total_posts = (int) $query->found_posts;

		wp_reset_postdata();

		return [
			'posts'          => $posts,
			'categories'     => $this->getAllTerms( 'portfolio-category' ),
			'technologies'   => $this->getAllTerms( 'technology' ),
			'postsPerPage'   => $posts_per_page,
			'currentPage'    => $current_page,
			'totalPages'     => $total_pages,
			'totalPosts'     => $total_posts,
			'activeCategory' => $active_category,
			'activeTech'     => $active_tech,
		];
	}

	/**
	 * Returns terms assigned to a specific post for a given taxonomy.
	 *
	 * @param int    $post_id  The post ID.
	 * @param string $taxonomy The taxonomy slug.
	 * @return array<int, array{id: int, name: string, slug: string}>
	 */
	private function getPostTerms( int $post_id, string $taxonomy ): array {
		$terms = get_the_terms( $post_id, $taxonomy );
		if ( ! $terms || is_wp_error( $terms ) ) {
			return [];
		}
		return array_map(
			fn( \WP_Term $term ) => [
				'id'   => $term->term_id,
				'name' => $term->name,
				'slug' => $term->slug,
			],
			$terms
		);
	}

	/**
	 * Returns all terms for a given taxonomy.
	 *
	 * @param string $taxonomy The taxonomy slug.
	 * @return array<int, array{id: int, name: string, slug: string}>
	 */
	private function getAllTerms( string $taxonomy ): array {
		$terms = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => false ] );
		if ( is_wp_error( $terms ) ) {
			return [];
		}
		return array_map(
			fn( \WP_Term $term ) => [
				'id'   => $term->term_id,
				'name' => $term->name,
				'slug' => $term->slug,
			],
			$terms
		);
	}
}
