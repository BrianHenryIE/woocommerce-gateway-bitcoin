<?php

namespace BrianHenryIE\WC_Bitcoin_Gateway\API\Blockchain;

use Codeception\TestCase\WPTestCase;

/**
 * @coversNothing
 */
class SoChain_API_Contract_Test extends WPTestCase {

	public function test_query_address(): void {

		$sut = new SoChain_API();

		$sent_to = $_ENV['EXAMPLE_GET_ADDRESS_BALANCE_ADDRESS'];

		$result = $sut->get_address_balance( $sent_to, 0 );

		$this->assertEquals( $_ENV['EXAMPLE_GET_ADDRESS_BALANCE_CONFIRMED_BALANCE'], $result['confirmed_balance'] );
	}

	public function test_transaction(): void {

		$sut = new SoChain_API();

		$sent_to = $_ENV['EXAMPLE_GET_ADDRESS_BALANCE_ADDRESS'];

		$result = $sut->get_transactions_received( $sent_to );

		$this->assertCount( 1, $result );
	}
}