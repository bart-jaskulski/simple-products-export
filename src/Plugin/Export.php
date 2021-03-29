<?php
/**
 * File taking care of exporting CSV.
 *
 * @package WPDesk\SimpleProductsExport
 */

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

	/**
	 * Inject dependencies into object.
	 *
	 * @param  Data_Interface $data Object implementing Data_Interface.
	 * @param  Writer         $writer PHP League CSV instance.
	 */
	public function __construct( Data_Interface $data, Writer $writer ) {
		$this->data   = $data;
		$this->writer = $writer;

		$this->write_to_file();
	}

	/**
	 * Check if user can export file and write it for export.
	 *
	 * @return void
	 */
	public function write_to_file() {
		if ( isset( $_POST['page'] ) && 1 === absint( $_POST['page'] ) ) { // phpcs:ignore
			$this->insert_header( $this->data->get_header() );
		}

		$this->insert_rows( $this->data->get_rows() );
	}

	/**
	 * Puts header into CSV file.
	 *
	 * @param array $header Array of column headings.
	 * @return void
	 */
	protected function insert_header( $header ) {
		$this->writer->insertOne( $header );
	}

	/**
	 * Puts all content into CSV file.
	 *
	 * @param  array $rows Array of rows to insert.
	 * @return void
	 */
	protected function insert_rows( $rows ) {
		$this->writer->insertAll( $rows );
	}
}
