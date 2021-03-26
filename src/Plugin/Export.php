<?php

namespace WPDesk\SimpleProductsExport;

use League\Csv\Writer;

/**
 * Process export request by checking nonce and writing to CSV file.
 */
class Export {

	/**
	 * League Csv Writer instance.
	 *
	 * @var Writer
	 */
	protected $writer;

	/**
	 * Data class.
	 *
	 * @var Data_Interface
	 */
	protected $data;

	public function __construct( Data_Interface $data, Writer $writer ) {
		$this->data   = $data;
		$this->writer = $writer;
	}

	/**
	 * Check if user can export file and write it for export.
	 *
	 * @return void
	 */
	public function export_file() {
		if ( ! $this->is_request_valid() ) {
			return;
		}

		if ( ! current_user_can( 'export' ) ) {
			wp_die( esc_html__( 'You don\'t have permission to export files.', 'simple-products-export' ) );
		}

		$this->insert_header( $this->data->get_header() );
		$this->insert_rows( $this->data->get_rows() );

		$this->writer->output( $this->build_file_name() );
	}

	/**
	 * Create nice name with shop name and timestamp for export.
	 *
	 * @return string
	 */
	protected function build_file_name() {
		return sanitize_file_name( get_bloginfo( 'name' ) . '-' . __( 'Products', 'simple-products-export' ) . '-' . gmdate( 'Y-m-d-H-i' ) . '.csv' );
	}

	protected function insert_header( $header ) {
		$this->writer->insertOne( $header );
	}

	protected function insert_rows( $rows ) {
		$this->writer->insertAll( $rows );
	}

	/**
	 * Validate request's nonce.
	 *
	 * @return bool
	 */
	private function is_request_valid() {
		return ! empty( $_POST ) && check_admin_referer( 'simple-products-export' );
	}
}
