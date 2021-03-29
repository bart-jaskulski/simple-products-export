<?php
/**
 * File taking care of exporting CSV.
 *
 * @package WPDesk\SimpleProductsExport
 */

namespace WPDesk\SimpleProductsExport;

use League\Csv\Writer;
use \WC_Product_Query;

/**
 * Process export request by checking nonce and writing to CSV file.
 */
class Request {

	/**
	 * Current page to process.
	 *
	 * @var int
	 */
	private $page;

	/**
	 * Limit of records in a batch.
	 *
	 * @var int
	 */
	private $limit;

	/**
	 * Name of current file.
	 *
	 * @var string
	 */
	private $filename;

	/**
	 * Validate request and prepare object.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->validate_request();

		// Nonce verification delegated to subsequent function.
		$this->page  = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1; // phpcs:ignore
		$this->limit = isset( $_POST['limit'] ) ? absint( $_POST['limit'] ) : -1; // phpcs:ignore

		$this->filename = $this->set_filename();
	}

	/**
	 * Process AJAX POST request.
	 *
	 * @return void
	 * @throws \Exception In case product query returns an array. Should get object.
	 */
	public function process_request() {
		$product_query  = new WC_Product_Query( $this->query_arguments() );
		$products_array = $product_query->get_products();
		if (
			! is_object( $products_array ) ||
			! property_exists( $products_array, 'products' ) ||
			! property_exists( $products_array, 'max_num_pages' )
		) {
			throw new \Exception();
		}

		$data = new Product_Data( $products_array->products );

		$file_dir = wp_upload_dir();
		$path     = trailingslashit( $file_dir['path'] ) . $this->filename;
		$writer   = Writer::createFromPath( $path, 'a+' );
		new Export( $data, $writer );

		$result = array(
			'current' => $this->page,
			'max'     => $products_array->max_num_pages,
		);

		// Expose download url only on last request from batch.
		if ( $this->page < $products_array->max_num_pages ) {
			$result['file'] = $this->filename;
		} else {
			$result['download'] = trailingslashit( $file_dir['url'] ) . $this->filename;
		}

		wp_send_json_success( $result );
	}

	/**
	 * Validate request's nonce. Collect and return errors in JSON response.
	 *
	 * @return void
	 */
	private function validate_request() {
		$errors = array();
		if ( ! empty( $_POST ) && ! check_ajax_referer( 'simple-products-export', '_wpdesk_spe' ) ) {
			$errors[] = __( 'Invalid nonce.', 'simple-products-export' );
		}

		if ( ! current_user_can( 'export' ) ) {
			$errors[] = __( 'You don\'t have permission to export files.', 'simple-products-export' );
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error( $errors );
		}
	}

	/**
	 * Determine whether put new filename or process current one.
	 * If creating new file build a nice name string with timestamp.
	 * There shouldn't be more than one unique request per second,
	 * then timestamp in format Y-m-d-H-i-s is enough for preventing file conflict.
	 *
	 * @return string
	 * @throws \Exception In case the value is not a string. Not very likely.
	 */
	private function set_filename() {
		$filename = '';
		if ( isset( $_POST['file'] ) ) { // phpcs:ignore
			$filename = wp_unslash( $_POST['file'] ); // phpcs:ignore
		} else {
			$filename = get_bloginfo( 'name' ) . '-' . __( 'products', 'simple-products-export' ) . '-' . gmdate( 'Y-m-d-H-i-s' ) . '.csv';
		}

		// PHPStan forces checking type because wp_unslash may return array...
		// Only if an array is given, which is not a point here.
		if ( is_string( $filename ) ) {
			return sanitize_file_name( $filename );
		}

		throw new \Exception();
	}

	/**
	 * Arguments to supply to WP_Product_Query
	 *
	 * @return array
	 */
	private function query_arguments() {
		return array(
			'limit'    => $this->limit,
			'page'     => $this->page,
			'paginate' => true,
		);
	}
}
