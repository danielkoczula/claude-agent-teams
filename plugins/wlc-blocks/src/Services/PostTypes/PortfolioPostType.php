<?php
/**
 * Registers the wlc-portfolio custom post type.
 *
 * @package WLC\Blocks
 */

namespace WLC\Blocks\Services\PostTypes;

defined( 'ABSPATH' ) || exit;

/**
 * Handles registration of the Portfolio custom post type.
 */
class PortfolioPostType {

	/**
	 * Registers the custom post type.
	 *
	 * @return void
	 */
	public function register(): void {
		register_post_type(
			'wlc-portfolio',
			[
				'labels'            => [
					'name'               => __( 'Portfolio', 'wlc-blocks' ),
					'singular_name'      => __( 'Portfolio Item', 'wlc-blocks' ),
					'add_new'            => __( 'Add New', 'wlc-blocks' ),
					'add_new_item'       => __( 'Add New Portfolio Item', 'wlc-blocks' ),
					'edit_item'          => __( 'Edit Portfolio Item', 'wlc-blocks' ),
					'new_item'           => __( 'New Portfolio Item', 'wlc-blocks' ),
					'view_item'          => __( 'View Portfolio Item', 'wlc-blocks' ),
					'search_items'       => __( 'Search Portfolio', 'wlc-blocks' ),
					'not_found'          => __( 'No portfolio items found', 'wlc-blocks' ),
					'not_found_in_trash' => __( 'No portfolio items found in trash', 'wlc-blocks' ),
				],
				'public'            => true,
				'show_in_rest'      => true,
				'supports'          => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
				'rewrite'           => [ 'slug' => 'portfolio' ],
				'has_archive'       => 'portfolio',
				'menu_icon'         => 'dashicons-portfolio',
				'menu_position'     => 5,
				'show_in_nav_menus' => true,
			]
		);
	}
}
