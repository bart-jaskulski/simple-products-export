<?php
/**
 * Interface for data exporting.
 *
 * @package WPDesk/SimpleProductsExport
 */

namespace WPDesk\SimpleProductsExport;

/**
 * Implement this interface if you want to provide data to export to CSV file.
 */
interface Data_Interface {

	/**
	 * One level array to insert as column names.
	 *
	 * @return array
	 */
	public function get_header();

	/**
	 * Function should return array of rows with items in array within.
	 *
	 * @return array Array of items to write to CSV file.
	 */
	public function get_rows();
}
