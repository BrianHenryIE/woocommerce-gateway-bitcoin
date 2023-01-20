<?php
/**
 * Add the payment gateway to WooCommerce's list of gateways.
 *
 * @package    brianhenryie/bh-wc-bitcoin-gateway
 */

namespace BrianHenryIE\WC_Bitcoin_Gateway\WooCommerce;

use BrianHenryIE\WC_Bitcoin_Gateway\API_Interface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use WC_Payment_Gateway;
use WC_Payment_Gateways;

/**
 * Add the payment gateway's class name to WooCommerce's list of gateways it will
 * later instantiate.
 */
class Payment_Gateways {
	use LoggerAwareTrait;

	protected API_Interface $api;

	public function __construct( API_Interface $api, LoggerInterface $logger ) {
		$this->setLogger( $logger );
		$this->api = $api;
	}

	/**
	 * Add the Gateway to WooCommerce.
	 *
	 * @hooked woocommerce_payment_gateways
	 *
	 * @param string[] $gateways The payment gateways registered with WooCommerce.
	 *
	 * @return string[]
	 * @see WC_Payment_Gateways::init()
	 */
	public function add_to_woocommerce( array $gateways ): array {

		if ( ! $this->api->is_server_has_dependencies() ) {
			return $gateways;
		}

		$gateways[] = Bitcoin_Gateway::class;

		return $gateways;
	}

	/**
	 * TODO: Remove. This is now implemented in the Duplicate Payment Gateways plugin.
	 *
	 * When linking to WooCommerce/Settings/Payments from plugins.php, filter to only instances of this gateway.
	 *
	 * The plugins.php code checks for multiple instances of the gateway, then uses the `class=bh-wc-bitcoin-gateway`
	 * parameter on the Settings link to invoke this function.
	 *
	 * i.e. `wp-admin/admin.php?page=wc-settings&tab=checkout&class=bh-wc-bitcoin-gateway`.
	 *
	 * TODO: Is this hook right?
	 *
	 * @hooked woocommerce_payment_gateways
	 *
	 * @param array<string|WC_Payment_Gateway> $gateways WC_Payment_Gateway subclass instance or class names of payment gateways registered with WooCommerce.
	 *
	 * @return array<string|WC_Payment_Gateway>
	 * @see WC_Payment_Gateways::init()
	 */
	public function filter_to_only_bitcoin_gateways( array $gateways ): array {

		global $current_tab;

		if ( 'checkout' !== $current_tab || ! isset( $_GET['class'] ) || 'bh-wc-bitcoin-gateway' !== wp_unslash( $_GET['class'] ) ) {
			return $gateways;
		}

		$bitcoin_gateways = array();
		foreach ( $gateways as $gateway ) {

			// Only handling one level of superclass. TODO: `class_parents()`.
			if ( Bitcoin_Gateway::class === $gateway
				|| ( $gateway instanceof Bitcoin_Gateway )
				|| ( is_string( $gateway ) && class_exists( $gateway ) && get_parent_class( $gateway ) === Bitcoin_Gateway::class ) ) {
				$bitcoin_gateways[] = $gateway;
			}
		}

		return $bitcoin_gateways;
	}

	/**
	 * Set the PSR logger on each gateway instance.
	 *
	 * @hooked woocommerce_available_payment_gateways
	 *
	 * @param array<string, WC_Payment_Gateway> $available_gateways The gateways to be displayed on the checkout.
	 *
	 * @return array<string, WC_Payment_Gateway>
	 */
	public function add_logger_to_gateways( array $available_gateways ): array {

		foreach ( $available_gateways as $gateway ) {
			if ( $gateway instanceof Bitcoin_Gateway ) {
				$gateway->setLogger( $this->logger );
			}
		}

		return $available_gateways;
	}
}