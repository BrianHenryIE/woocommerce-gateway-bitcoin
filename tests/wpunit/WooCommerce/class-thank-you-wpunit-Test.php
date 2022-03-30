<?php

namespace Nullcorps\WC_Gateway_Bitcoin\WooCommerce;

use BrianHenryIE\ColorLogger\ColorLogger;
use Codeception\Stub\Expected;
use Nullcorps\WC_Gateway_Bitcoin\API\API_Interface;
use Nullcorps\WC_Gateway_Bitcoin\API\Settings_Interface;

/**
 * @coversDefaultClass \Nullcorps\WC_Gateway_Bitcoin\WooCommerce\Thank_You
 */
class Thank_You_WPUnit_Test extends \Codeception\TestCase\WPTestCase {

	/**
	 * @covers ::print_instructions
	 * @covers ::__construct
	 */
	public function test_add_instructions(): void {

		$api = $this->makeEmpty(
			API_Interface::class,
			array(
				'is_order_has_bitcoin_gateway' => Expected::once(
					function( int $order_id ) {
						return true;
					}
				),
				'get_order_details'            => Expected::once(
					function( $order ) {
						return array();
					}
				),
			)
		);

		$sut = new Thank_You( $api );

		$order = new \WC_Order();
		$order->set_payment_method( 'bitcoin' );
		$order_id = $order->save();

		add_filter(
			'wc_get_template',
			function() {
				throw new \Exception();
			}
		);

		$e = null;
		try {
			$sut->print_instructions( $order_id );
		} catch ( \Exception $exception ) {
			$e = $exception;
		}

		// Is there a better way to say wc_get_template was called?
		$this->assertNotNull( $e );

	}


	/**
	 * @covers ::print_instructions
	 */
	public function test_add_instructions_order_not_for_this_gateway(): void {

		$api = $this->makeEmpty(
			API_Interface::class,
			array(
				'is_order_has_bitcoin_gateway' => Expected::once(
					function( int $order_id ) {
						return false;
					}
				),
				'get_order_details'            => Expected::never(),
			)
		);

		$sut = new Thank_You( $api );

		$order_id = 123;

		$sut->print_instructions( $order_id );
	}

}