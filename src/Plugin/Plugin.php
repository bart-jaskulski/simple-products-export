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
use League\Csv\Writer;
use \SplTempFileObject;
use \WC_Product_Query;


/**
 * Main plugin class. The most important flow decisions are made here.
 *
 * @package WPDesk\SimpleProductsExport
 */
class Plugin extends AbstractPlugin implements LoggerAwareInterface, HookableCollection {
	use LoggerAwareTrait;
	use HookableParent;

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
		add_action( 'admin_menu', array( Admin::class, 'init_admin_ui' ) );
		add_action( 'admin_post_simple_products_export', array( $this, 'process_request' ) );
	}

	/**
	 * Create objects for getting and saving data, then send them to exporter and collect csv.
	 *
	 * @return void
	 */
	public function process_request() {
		$data   = new Product_Data( new WC_Product_Query( $this->query_arguments() ) );
		$writer = Writer::createFromFileObject( new SplTempFileObject() );

		$export = new Export( $data, $writer );
		$export->export_file();
		die;
	}

	private function query_arguments() {
		return array(
			'limit' => -1,
		);
	}
}
