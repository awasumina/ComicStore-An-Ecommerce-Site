<?php
/**
* Plugin Name: Woo Partial.ly
* License: GPLv2 or later
* Plugin URI: https://partial.ly
* Version: 2.2.5
* Description: Add Partial.ly payment plans to your WooCommerce store
* Author: Partially Inc
* Author URI: https://partial.ly
* Requires at least: 4.4
* Tested up to: 6.2
* WC tested up to: 7.8
* WC requires at least: 2.6
*/

if ( ! defined( 'ABSPATH' ) ) exit;

define('PARTIALLY_PATH', untrailingslashit(plugin_dir_path( __FILE__ )) );

// make sure woocommerce exists
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if (is_plugin_active( 'woocommerce/woocommerce.php' )) {
    add_action('plugins_loaded', 'woocommerce_partially_init');
}

add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

function woocommerce_partially_init() {

    if ( ! class_exists('WC_Payment_Gateway'))  return;

    include_once PARTIALLY_PATH . "/classes/partially/gateway.php";

    include_once PARTIALLY_PATH . "/inc/notification-handler.php";

    if (is_admin()) {

    	include_once PARTIALLY_PATH . "/classes/partially/admin.php";

    	$partiallyAdmin = new Partially_Admin();
    }

    include_once PARTIALLY_PATH . "/inc/gateway.php";

    include_once PARTIALLY_PATH . "/inc/widget.php";

    include_once PARTIALLY_PATH . "/inc/checkout-button.php";

}
