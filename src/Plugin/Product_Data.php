<?php

namespace WPDesk\SimpleProductsExport;

use \WC_Product;
use \WP_Term;
use \Exception;

/**
 * Class comunicating with WC_Product_Query to retrieve products' data.
 */
class Product_Data implements Data_Interface {

	/**
	 * WC_Product_Query instance.
	 *
	 * @var \WC_Product_Query
	 */
	protected $query;

	/**
	 * Key-value pair of property name and corresponding column name.
	 *
	 * @var array
	 */
	protected $columns;

	public function __construct( \WC_Product_Query $query ) {
		$this->query = $query;

		$this->columns = array(
			'title'         => __( 'Product name', 'simple-products-export' ),
			'category_ids'  => __( 'Categories', 'simple-products-export' ),
			'sku'           => __( 'SKU', 'simple-products-export' ),
			'price'         => __( 'Price', 'simple-products-export' ),
			'regular_price' => __( 'Regular price', 'simple-products-export' ),
		);
	}

	/**
	 * Create row names from columns' array values.
	 *
	 * @return array
	 */
	public function get_header() {
		return array_values( $this->columns );
	}

	/**
	 * Retrieve all product rows to export with corresponding products variants.
	 *
	 * @return array
	 */
	public function get_rows() {
		$products_for_export = array();
		$product_query = $this->query->get_products();
		if ( empty( $product_query ) ) {
			wp_die( esc_html__( 'No products in the store.', 'simple-products-export' ) );
		}

		if ( is_array( $product_query ) ) { // PHPStan made me do this...
			foreach ( $product_query as $product ) {
				// Call for column fields for each children if needed.
				if ( $product->has_child() ) {
					foreach ( $product->get_children() as $variation_id ) {
						$variation_product     = \get_product( $variation_id );
						$products_for_export[] = $this->get_product_fields( $variation_product );
					}
				}
				$products_for_export[] = $this->get_product_fields( $product );
			}
		}

		return $products_for_export;
	}

	/**
	 * Prepare fields to be inserted into columns from WC_Product.
	 *
	 * @param WC_Product $product
	 * @return array
	 * @throws Exception Throws when method from column mapping not found in WC_Product class.
	 */
	private function get_product_fields( WC_Product $product ) {
		$product_data = array();
		foreach ( array_keys( $this->columns ) as $column ) {
			// Create funcion from object's columns array key.
			$getter = "get_$column";
			if ( ! \method_exists( $product, $getter ) ) {
				throw new Exception( "Can\'t get specified field. Method does not exist. Refine column keys." );
			}
			$column_field = $product->$getter();

			// Special case: convert category ids into names.
			if ( 'category_ids' === $column ) {
				$column_field = $this->get_category_names( $column_field );
			}

			$product_data[] = $column_field;
		}
		return $product_data;
	}

	/**
	 * Get names for categories and return them as concatenated string.
	 *
	 * @param array $cat_ids Array of category ids for current product.
	 * @return string
	 */
	private function get_category_names( $cat_ids ) {
		$cat_names = array();
		foreach ( $cat_ids as $cat_id ) {
			$category    = \get_term( $cat_id, 'product_cat' );
			if ( $category instanceof WP_Term ) {
				$cat_names[] = $category->name;
			}
		}
		return join( ', ', $cat_names );
	}
}
