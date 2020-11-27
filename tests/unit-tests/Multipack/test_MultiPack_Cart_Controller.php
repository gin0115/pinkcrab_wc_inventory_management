<?php

/**
 * Tests for Multipack_Cart_Controller
 *
 * @package PinkCrab/WooMan
 */

use PinkCrab\Core\App;
use Automattic\Jetpack\Constants;

// Include helper trait
require_once 'Helper_Traits/MultiPack_Product_Helper.php';

/**
 * Class Functions.
 */
class Test_Multipack_Cart_Controller extends WC_Unit_Test_Case {

	/**
	 * The apps container
	 *
	 * @var PinkCrab\Core\App
	 */
	protected $app;

	/**
	 * The test product (has 6 variations.)
	 *
	 * @var WC_Product
	 */
	protected $product_variable;

	/**
	 * General Product helper test trait.
	 * method WC_Product create_test_variable_product()
	 */
	use MultiPack_Product_Helper;

    /**
	 * Sets the app container if its not already.
	 *
	 * @return void
	 */
	public function setUp(): void {
		if ( ! $this->app ) {
			$this->app              = App::getInstance();
			$this->product_variable = $this->create_test_variable_product();
		}
	}

	/**
	 * Tests that the max value is set in the cart.
	 *
	 * Uses the WC/JetPack Constants class to spoof is_cart()
	 *
	 * @filter woocommerce_product_get_stock_quantity
	 * @filter woocommerce_product_variation_get_stock_quantity
	 * @method void WC_Product::get_max_purchase_quantity()
	 * @return void
	 */
	public function test_can_set_max_value_in_cart(): void {

		// Mock is_cart();
		Constants::set_constant( 'WOOCOMMERCE_CART', true );

		$this->product_variable->set_stock_quantity( $this->starting_stock );
		$this->product_variable->save();

		$expected = array( 3, 1, 1, 0, 0, 0 );
		foreach ( $this->product_variable->get_available_variations( 'object' )
			as $key => $variation
		) {
			$this->assertEquals( $expected[ $key ], $variation->get_max_purchase_quantity() );
		}

		// Test with 12 in stock.
		$this->product_variable->set_stock_quantity( 12 );
		$this->product_variable->save();

		$expected = array( 12, 6, 4, 3, 2, 2 );
		foreach ( $this->product_variable->get_available_variations( 'object' )
			as $key => $variation
		) {
			$this->assertEquals( $expected[ $key ], $variation->get_max_purchase_quantity() );
		}

		// Reset
		$this->product_variable->set_stock_quantity( $this->starting_stock );
		$this->product_variable->save();

	}


	/**
	 * Clears all custom defined constants.
	 *
	 * @return void
	 */
	public function tearDown(): void {
		Constants::clear_constants();
	}
}
