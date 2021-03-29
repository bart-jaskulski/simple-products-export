<?php
/**
	Plugin Name: Simple products export for WooCommerce
	Plugin URI: https://www.wpdesk.net/products/simple-products-export/
	Description: Simple products export for WooCommerce
	Product: Simple products export for WooCommerce
	Version: 1.1
	Author: WP Desk
	Author URI: https://www.wpdesk.net/
	Text Domain: simple-products-export
	Domain Path: /lang/

	@package \WPDesk\SimpleProductsExport

	Copyright 2016 WP Desk Ltd.

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/* THESE TWO VARIABLES CAN BE CHANGED AUTOMATICALLY */
$plugin_version           = '1.0.0';
$plugin_release_timestamp = '2021-03-25 08:49';

$plugin_name        = 'Simple products export for WooCommerce';
$plugin_class_name  = '\WPDesk\SimpleProductsExport\Plugin';
$plugin_text_domain = 'simple-products-export';
$product_id         = 'simple-products-export';
$plugin_file        = __FILE__;
$plugin_dir         = dirname( __FILE__ );

$requirements = [
	'php'     => '7.1',
	'wp'      => '5.7',
	'plugins' => [
		[
			'name'      => 'woocommerce/woocommerce.php',
			'nice_name' => 'WooCommerce',
			'version'   => '5.0',
		],
	],
];

require __DIR__ . '/vendor_prefixed/wpdesk/wp-plugin-flow/src/plugin-init-php52.php';
