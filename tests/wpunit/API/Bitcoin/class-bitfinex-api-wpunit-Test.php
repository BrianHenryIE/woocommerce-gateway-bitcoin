<?php
/**
 * Integration test to capture data for wpunit test.
 */

namespace Nullcorps\WC_Gateway_Bitcoin\API\Bitcoin;

use BrianHenryIE\ColorLogger\ColorLogger;

/**
 * @coversDefaultClass \Nullcorps\WC_Gateway_Bitcoin\API\Bitcoin\Bitfinex_API
 */
class Bitfinex_API_Integration_Test extends \Codeception\TestCase\WPTestCase {

	/**
	 * @covers ::get_exchange_rate
	 * @covers ::__construct
	 */
	public function test_query_api():void {

		$logger = new ColorLogger();

		$sut = new Bitfinex_API( $logger );

		$request_response = array(
			'body'     => '[["tBTCUSD",40990,8.671964370000001,40991,9.711412020000001,-189,-0.0046,40990,2292.72205775,41499,40542]]',
			'response' =>
				array(
					'code'    => 200,
					'message' => 'OK',
				),
		);

		add_filter(
			'pre_http_request',
			function() use ( $request_response ) {
				return $request_response;
			}
		);

		$result = $sut->get_exchange_rate( 'usd' );

		$this->assertEquals( '40990', $result );

	}
}