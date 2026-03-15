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
 */
class PortfolioGridController {

	/**
	 * Returns all data needed to render the block.
	 *
	 * @param array $attributes Block attributes from the editor.
	 * @return array{posts: array, categories: array, technologies: array, postsPerPage: int}
	 */
	public function getData( array $attributes ): array {
		$posts_per_page = isset( $attributes['postsPerPage'] ) ? absint( $attributes['postsPerPage'] ) : 9;

		$query = new \WP_Query( [
			'post_type'      => 'wlc-portfolio',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		] );

		$posts = [];
		foreach ( $query->posts as $post ) {
			$thumbnail_url = '';
			if ( has_post_thumbnail( $post->ID ) ) {
				$thumbnail_url = (string) get_the_post_thumbnail_url( $post->ID, 'large' );
			}

			$categories   = $this->getPostTerms( $post->ID, 'portfolio-category' );
			$technologies = $this->getPostTerms( $post->ID, 'technology' );

			$posts[] = [
				'id'            => $post->ID,
				'title'         => get_the_title( $post->ID ),
				'excerpt'       => wp_strip_all_tags( get_the_excerpt( $post->ID ) ),
				'permalink'     => (string) get_permalink( $post->ID ),
				'thumbnailUrl'  => $thumbnail_url,
				'categories'    => $categories,
				'technologies'  => $technologies,
				'categorySlugs' => array_column( $categories, 'slug' ),
				'techSlugs'     => array_column( $technologies, 'slug' ),
			];
		}

		wp_reset_postdata();

		return [
			'posts'        => $posts,
			'categories'   => $this->getAllTerms( 'portfolio-category' ),
			'technologies' => $this->getAllTerms( 'technology' ),
			'postsPerPage' => $posts_per_page,
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
