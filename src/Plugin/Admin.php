<?php

namespace WPDesk\SimpleProductsExport;

/**
 * Class for adding admin UI page.
 */
class Admin {

	private static $instance;

	public static function init_admin_ui() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		$this->add_products_export_page();
	}

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
		include trailingslashit( dirname( __DIR__, 2 ) ) . 'templates/export.php';
	}
}
