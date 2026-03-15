<?php
/**
 * Registers the technology taxonomy.
 *
 * @package WLC\Blocks
 */

namespace WLC\Blocks\Services\Taxonomies;

defined( 'ABSPATH' ) || exit;

/**
 * Handles registration of the Technology taxonomy.
 */
class TechnologyTaxonomy {

	/**
	 * Registers the taxonomy.
	 *
	 * @return void
	 */
	public function register(): void {
		register_taxonomy(
			'technology',
			'wlc-portfolio',
			[
				'labels'            => [
					'name'          => __( 'Technologies', 'wlc-blocks' ),
					'singular_name' => __( 'Technology', 'wlc-blocks' ),
					'search_items'  => __( 'Search Technologies', 'wlc-blocks' ),
					'all_items'     => __( 'All Technologies', 'wlc-blocks' ),
					'edit_item'     => __( 'Edit Technology', 'wlc-blocks' ),
					'update_item'   => __( 'Update Technology', 'wlc-blocks' ),
					'add_new_item'  => __( 'Add New Technology', 'wlc-blocks' ),
					'new_item_name' => __( 'New Technology Name', 'wlc-blocks' ),
					'menu_name'     => __( 'Technologies', 'wlc-blocks' ),
				],
				'hierarchical'      => false,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => [ 'slug' => 'technology' ],
			]
		);
	}
}
