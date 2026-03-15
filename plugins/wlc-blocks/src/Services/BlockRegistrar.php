<?php
/**
 * Scans the build directory and registers all blocks.
 *
 * @package WLC\Blocks
 */

namespace WLC\Blocks\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Registers all blocks found in the build/blocks directory.
 */
class BlockRegistrar {

	/**
	 * Registers all blocks by scanning the build directory.
	 *
	 * @return void
	 */
	public function register(): void {
		$blocks_dir = plugin_dir_path( dirname( __DIR__ ) ) . 'build/blocks/';

		if ( ! is_dir( $blocks_dir ) ) {
			return;
		}

		foreach ( glob( $blocks_dir . '*', GLOB_ONLYDIR ) as $block_dir ) {
			if ( file_exists( $block_dir . '/block.json' ) ) {
				register_block_type( $block_dir );
			}
		}
	}
}
