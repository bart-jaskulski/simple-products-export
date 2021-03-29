<?php
/**
 * Plugin main class.
 *
 * @package WPDesk\SimpleProductsExport
 */

namespace WPDesk\SimpleProductsExport;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use SimpleProductsExportVendor\WPDesk_Plugin_Info;
use SimpleProductsExportVendor\WPDesk\PluginBuilder\Plugin\AbstractPlugin;
use SimpleProductsExportVendor\WPDesk\PluginBuilder\Plugin\HookableCollection;
use SimpleProductsExportVendor\WPDesk\PluginBuilder\Plugin\HookableParent;

/**
 * Main plugin class. The most important flow decisions are made here.
 *
 * @package WPDesk\SimpleProductsExport
 */
class Plugin extends AbstractPlugin implements LoggerAwareInterface, HookableCollection {
	use LoggerAwareTrait;
	use HookableParent;

	/**
	 * Admin UI class instance.
	 *
	 * @var Admin
	 */
	private $admin;

	/**
	 * Plugin constructor.
	 *
	 * @param WPDesk_Plugin_Info $plugin_info Plugin info.
	 */
	public function __construct( WPDesk_Plugin_Info $plugin_info ) {
		parent::__construct( $plugin_info );
		$this->setLogger( new NullLogger() );

		$this->plugin_url       = $this->plugin_info->get_plugin_url();
		$this->plugin_namespace = $this->plugin_info->get_text_domain();

		$this->admin = new Admin();
	}

	/**
	 * Initializes plugin external state.
	 *
	 * The plugin internal state is initialized in the constructor and the plugin should be internally consistent after creation.
	 * The external state includes hooks execution, communication with other plugins, integration with WC etc.
	 *
	 * @return void
	 */
	public function init() {
		parent::init();
	}

	/**
	 * Integrate with WordPress and with other plugins using action/filter system.
	 *
	 * @return void
	 */
	public function hooks() {
		parent::hooks();
		add_action( 'admin_menu', array( $this->admin, 'add_products_export_page' ) );
		add_action( 'wp_ajax_simple_products_export', array( $this, 'process_request' ) );
	}

	/**
	 * Enqueue script for AJAX exporting on admin page.
	 */
	public function admin_enqueue_scripts() {
		if ( ! empty( get_current_screen()->id ) && 'product_page_export-products' === get_current_screen()->id ) {
			wp_enqueue_script( 'simple-export-ajax', $this->get_plugin_assets_url() . 'js/simple-export-ajax.js', array( 'jquery' ), '1.1.0', true );
			wp_localize_script( 'simple-export-ajax', 'simplePluginExport', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ) );
		}
	}

	/**
	 * Creates Request object and dispatches processing export.
	 *
	 * @return void
	 */
	public function process_request() {
		$request = new Request();
		$request->process_request();
	}
}
