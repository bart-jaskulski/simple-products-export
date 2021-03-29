<?php
/**
 * Simple template for admin users to export file with products.
 *
 * @package WPDesk\SimpleProductsExport
 */

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Simple products exporter', 'simple-products-export' ); ?></h1>
	<p><?php esc_html_e( 'To export all products from the store, click the button below.', 'simple-products-export' ); ?></p>
	<p><?php esc_html_e( 'Products will be saved as CSV file.', 'simple-products-export' ); ?></p>
	<form id="products-export-form" method="post">
		<?php wp_nonce_field( 'simple-products-export', '_wpdesk_spe' ); ?>
		<input type="hidden" name="action" value="simple_products_export">
		<?php submit_button( __( 'Export products', 'simple-products-export' ) ); ?>
	</form>
</div>
