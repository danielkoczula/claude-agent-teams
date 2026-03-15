<?php
/**
 * Registers the portfolio-category taxonomy.
 *
 * @package WLC\Blocks
 */

namespace WLC\Blocks\Services\Taxonomies;

defined( 'ABSPATH' ) || exit;

/**
 * Handles registration of the Portfolio Category taxonomy.
 */
class PortfolioCategoryTaxonomy {

	/**
	 * Registers the taxonomy.
	 *
	 * @return void
	 */
	public function register(): void {
		register_taxonomy(
			'portfolio-category',
			'wlc-portfolio',
			[
				'labels'            => [
					'name'              => __( 'Portfolio Categories', 'wlc-blocks' ),
					'singular_name'     => __( 'Portfolio Category', 'wlc-blocks' ),
					'search_items'      => __( 'Search Categories', 'wlc-blocks' ),
					'all_items'         => __( 'All Categories', 'wlc-blocks' ),
					'parent_item'       => __( 'Parent Category', 'wlc-blocks' ),
					'parent_item_colon' => __( 'Parent Category:', 'wlc-blocks' ),
					'edit_item'         => __( 'Edit Category', 'wlc-blocks' ),
					'update_item'       => __( 'Update Category', 'wlc-blocks' ),
					'add_new_item'      => __( 'Add New Category', 'wlc-blocks' ),
					'new_item_name'     => __( 'New Category Name', 'wlc-blocks' ),
					'menu_name'         => __( 'Categories', 'wlc-blocks' ),
				],
				'hierarchical'      => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => [ 'slug' => 'portfolio-category' ],
			]
		);
	}
}
