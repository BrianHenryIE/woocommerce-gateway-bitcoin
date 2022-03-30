<?php
/**
 * The plugin page output of the plugin.
 * Adds a "Settings" link
 * Adds an "Orders" link when Filter WooCommerce Orders by Payment Method plugin is installed.
 *
 * @package    nullcorps/woocommerce-gateway-bitcoin
 */

namespace Nullcorps\WC_Gateway_Bitcoin\Admin;

use Nullcorps\WC_Gateway_Bitcoin\API\API_Interface;
use WC_Payment_Gateway;

/**
 *
 */
class Plugins_Page {

	protected API_Interface $api;

	public function __construct( API_Interface $api ) {
		$this->api = $api;
	}

	/**
	 * Adds 'Settings' link to the configuration under WooCommerce's payment gateway settings page.
	 *
	 * @hooked plugin_action_links_{plugin basename}
	 *
	 * @param string[] $links_array The links that will be shown below the plugin name on plugins.php (usually "Deactivate").
	 *
	 * @return string[]
	 * @see \WP_Plugins_List_Table::display_rows()
	 */
	public function add_settings_action_link( array $links_array ): array {

		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			return $links_array;
		}

		$bitcoin_gateways = $this->api->get_bitcoin_gateways();

		if ( 1 === count( $bitcoin_gateways ) ) {
			// If there is only one gateway instance, link directly to it.
			$gateway = array_pop( $bitcoin_gateways );
			$section = '&section=' . $gateway->id;
		} else {
			// If there is more than one, link to the WooCommerce / Settings / Payments page filtered to the class type.
			$section = '&class=nullcorps-wc-gateway-bitcoin';
		}

		$setting_link   = admin_url( "admin.php?page=wc-settings&tab=checkout{$section}" );
		$plugin_links   = array();
		$plugin_links[] = '<a href="' . $setting_link . '">' . __( 'Settings', 'nullcorps-wc-gateway-bitcoin' ) . '</a>';

		return array_merge( $plugin_links, $links_array );
	}

	/**
	 * Adds 'Orders' link if Filter WooCommerce Orders by Payment Method plugin is installed.
	 *
	 * @hooked plugin_action_links_{plugin basename}
	 *
	 * @param string[] $links_array The links that will be shown below the plugin name on plugins.php (usually "Deactivate").
	 *
	 * @return string[]
	 * @see \WP_Plugins_List_Table::display_rows()
	 */
	public function add_orders_action_link( array $links_array ): array {

		$plugin_links = array();

		/**
		 * Add an "Orders" link to a filtered list of orders if the Filter WooCommerce Orders by Payment Method plugin is installed.
		 *
		 * @see https://www.skyverge.com/blog/filtering-woocommerce-orders/
		 */
		if ( is_plugin_active( 'wc-filter-orders-by-payment/filter-wc-orders-by-gateway.php' ) && class_exists( WC_Payment_Gateway::class ) ) {

			$params = array(
				'post_type'                  => 'shop_order',
				'_shop_order_payment_method' => 'bitcoin_gateway',
			);

			$orders_link    = add_query_arg( $params, admin_url( 'edit.php' ) );
			$plugin_links[] = '<a href="' . $orders_link . '">' . __( 'Orders', 'nullcorps-wc-gateway-bitcoin' ) . '</a>';
		}

		return array_merge( $plugin_links, $links_array );
	}

}