<?php
/**
 * Object containing the plugin settings.
 *
 * @package    brianhenryie/bh-wc-bitcoin-gateway
 */

namespace BrianHenryIE\WC_Bitcoin_Gateway\API;

use BrianHenryIE\WC_Bitcoin_Gateway\Frontend\Frontend_Assets;
use BrianHenryIE\WC_Bitcoin_Gateway\WP_Logger\Logger_Settings_Trait;
use BrianHenryIE\WC_Bitcoin_Gateway\WP_Logger\WooCommerce_Logger_Settings_Interface;
use BrianHenryIE\WC_Bitcoin_Gateway\Settings_Interface;
use Psr\Log\LogLevel;

/**
 * Plain object pulling setting from wp_options.
 */
class Settings implements Settings_Interface, WooCommerce_Logger_Settings_Interface {
	use Logger_Settings_Trait;

	/**
	 * The minimum severity of logs to record.
	 *
	 * TODO: Pull from settings.
	 *
	 * @see LogLevel
	 *
	 * @return string
	 */
	public function get_log_level(): string {
		return LogLevel::DEBUG;
	}

	/**
	 * Plugin name for use by the logger in friendly messages printed to WordPress admin UI.
	 *
	 * @see Logger
	 *
	 * @return string
	 */
	public function get_plugin_name(): string {
		return 'Bitcoin Gateway';
	}

	/**
	 * The plugin slug is used by the logger in file and URL paths.
	 *
	 * @return string
	 */
	public function get_plugin_slug(): string {
		return 'bh-wc-bitcoin-gateway';
	}

	/**
	 * The plugin basename is used by the logger to add the plugins page action link.
	 * (and maybe for PHP errors)
	 *
	 * @see Logger
	 *
	 * @return string
	 */
	public function get_plugin_basename(): string {
		return defined( 'BH_WC_BITCOIN_GATEWAY_BASENAME' ) ? BH_WC_BITCOIN_GATEWAY_BASENAME : 'bh-wc-bitcoin-gateway/bh-wc-bitcoin-gateway.php';
	}

	/**
	 * The plugin version, as used in caching JS and CSS assets.
	 *
	 * @return string
	 */
	public function get_plugin_version(): string {
		return defined( 'BH_WC_BITCOIN_GATEWAY_VERSION' ) ? BH_WC_BITCOIN_GATEWAY_VERSION : '1.3.3';
	}

	/**
	 * Return the URL of the base of the plugin.
	 * TODO: check plugin_dir_url is the correct function.
	 *
	 * @used-by Frontend_Assets::enqueue_scripts()
	 * @used-by Frontend_Assets::enqueue_styles()
	 */
	public function get_plugin_url(): string {
		return defined( 'BH_WC_BITCOIN_GATEWAY_URL' ) ? BH_WC_BITCOIN_GATEWAY_URL : plugin_dir_url( $this->get_plugin_basename() );
	}

	public function get_xpub( string $gateway_id = 'bitcoin_gateway' ): string {
		$saved_settings = get_option( 'woocommerce_' . $gateway_id . '_settings', array() );
		return $saved_settings['xpub'] ?? '';
	}
}