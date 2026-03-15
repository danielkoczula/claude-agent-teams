<?php
/**
 * Plugin Name: WLC Blocks
 * Plugin URI:  https://wlc.agency
 * Description: Custom Gutenberg blocks for WLC Agency.
 * Version:     1.0.0
 * Author:      WLC Agency
 * Text Domain: wlc-blocks
 * Requires at least: 6.5
 * Requires PHP: 8.1
 *
 * @package WLC\Blocks
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/vendor/autoload.php';

add_action( 'init', function () {
	( new \WLC\Blocks\Services\PostTypes\PortfolioPostType() )->register();
	( new \WLC\Blocks\Services\Taxonomies\PortfolioCategoryTaxonomy() )->register();
	( new \WLC\Blocks\Services\Taxonomies\TechnologyTaxonomy() )->register();
	( new \WLC\Blocks\Services\BlockRegistrar() )->register();
} );
