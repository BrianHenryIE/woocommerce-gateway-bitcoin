<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    nullcorps/woocommerce-gateway-bitcoin
 */

namespace Nullcorps\WC_Gateway_Bitcoin\Frontend;

use Nullcorps\WC_Gateway_Bitcoin\API\API_Interface;
use Nullcorps\WC_Gateway_Bitcoin\API\Settings_Interface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 *
 */
class Frontend {
	use LoggerAwareTrait;

	protected Settings_Interface $settings;

	protected API_Interface $api;

	public function __construct( API_Interface $api, Settings_Interface $settings, LoggerInterface $logger ) {
		$this->setLogger( $logger );
		$this->settings = $settings;
		$this->api      = $api;
	}

	/**
	 * Register the stylesheets for the frontend-facing side of the site.
	 *
	 * @hooked wp_enqueue_scripts
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles(): void {

		$order_id = isset( $GLOBALS['order-received'] ) ? $GLOBALS['order-received'] : ( isset( $GLOBALS['view-order'] ) ? $GLOBALS['view-order'] : null );

		if ( empty( $order_id ) ) {
			return;
		}

		$version = $this->settings->get_plugin_version();

		wp_enqueue_style( 'nullcorps-wc-gateway-bitcoin', plugin_dir_url( __FILE__ ) . 'css/woocommerce-gateway-bitcoin.css', array(), $version, 'all' );

	}

	/**
	 * Register the JavaScript for the frontend-facing side of the site.
	 *
	 * @hooked ?wp_enqueue_scripts
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts(): void {

		$order_id = isset( $GLOBALS['order-received'] ) ? $GLOBALS['order-received'] : ( isset( $GLOBALS['view-order'] ) ? $GLOBALS['view-order'] : null );

		if ( empty( $order_id ) ) {
			return;
		}

		$order = wc_get_order( $order_id );

		if ( ! ( $order instanceof \WC_Order ) ) {
			return;
		}

		$version = $this->settings->get_plugin_version();

		wp_enqueue_script( 'nullcorps-wc-gateway-bitcoin', plugin_dir_url( __FILE__ ) . 'js/woocommerce-gateway-bitcoin.js', array( 'jquery' ), $version, true );

		$order_details = $this->api->get_order_details( $order );

		$order_details_json = wp_json_encode( $order_details, JSON_PRETTY_PRINT );

		$ajax_data      = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( self::class ),
		);
		$ajax_data_json = wp_json_encode( $ajax_data, JSON_PRETTY_PRINT );

		$script = <<<EOD
var nullcorps_ajax_data = $ajax_data_json;
var nullcorps_bitcoin_order_details = $order_details_json;
EOD;

		wp_add_inline_script(
			'nullcorps-wc-gateway-bitcoin',
			$script,
			'before'
		);

	}

}