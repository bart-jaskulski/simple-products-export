<?php
/**
 * Adds admin UI interface for user.
 *
 * @package WPDesk\SimpleProductsExport
 */

namespace WPDesk\SimpleProductsExport;

/**
 * Class for adding admin UI page.
 */
class Admin {

	/**
	 * Register submenu page.
	 *
	 * @return void
	 */
	public function add_products_export_page() {
		\add_submenu_page(
			'edit.php?post_type=product',
			__( 'Export products', 'simple-products-export' ),
			__( 'Export products', 'simple-products-export' ),
			'export',
			'export-products',
			array( $this, 'render' )
		);
	}

	/**
	 * Render HTML template for export page.
	 *
	 * @return void
	 */
	public function render() {
		include trailingslashit( dirname( __DIR__, 2 ) ) . 'templates/export.php'; // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctionParameters.dirname_levelsFound
	}
}
