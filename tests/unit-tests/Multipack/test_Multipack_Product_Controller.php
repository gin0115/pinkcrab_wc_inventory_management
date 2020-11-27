<?php

/**
 * Tests for Multipack_Product_Controller
 *
 * @package PinkCrab/WooMan
 */

use PinkCrab\Core\App;
use PinkCrab\InventoryManagment\MultiPack\MultiPack_Config;
use PinkCrab\InventoryManagment\Settings\WooCommece_Settings;
use PinkCrab\InventoryManagment\MultiPack\Multipack_Product_Controller;

// Include helper trait
require_once 'Helper_Traits/MultiPack_Product_Helper.php';

/**
 * Class Functions.
 */
class Test_Multipack_Product_Controller extends WC_Unit_Test_Case {

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
	public function setUp() {
		if ( ! $this->app ) {
			$this->app              = App::getInstance();
			$this->product_variable = $this->create_test_variable_product();
		}
	}


	/**
	 * Tests the the is_in_stock callback is applied.
	 *
	 * @hook woocommerce_product_is_in_stock
	 * @return void
	 */
	public function test_is_in_stock() {

		// 3 instock, so 3 should be in stock and 3 out.
		$this->product_variable->set_stock_quantity( $this->starting_stock );
		$this->product_variable->save();

		foreach ( $this->product_variable->get_available_variations( 'object' )
			as $key => $variation
		) {

			if ( ( $key + 1 ) <= 3 ) {
				$this->assertTrue( $variation->is_in_stock() );
			} else {
				$this->assertFalse( $variation->is_in_stock() );
			}
		}

		// Set as 12 in stock, all 6 should pass.
		$this->product_variable->set_stock_quantity( 12 );
		$this->product_variable->save();
		foreach ( $this->product_variable->get_available_variations( 'object' )
			as $variation
		) {
			$this->assertTrue( $variation->is_in_stock() );
		}

		// Reset the test product.
		$this->product_variable->set_stock_quantity( $this->starting_stock );
		$this->product_variable->save();
	}

	/**
	 * Filters the stock formatted html using adjusted values based on pack size.
	 *
	 * @filter woocommerce_get_stock_html
	 * @method string MultiPack_Product_Controller::stock_level_html()
	 * @return void
	 */
	public function test_can_format_html_stock(): void {

		$expected = array(
			'<p class=\'stock in-stock\'>3 in stock</p>',
			'<p class=\'stock in-stock\'>1 x 2 pack in stock</p>',
			'<p class=\'stock in-stock\'>1 x 3 pack in stock</p>',
			'<p class="stock out-of-stock">Out of stock</p>',
			'<p class="stock out-of-stock">Out of stock</p>',
			'<p class="stock out-of-stock">Out of stock</p>',
		);

		foreach ( $this->product_variable->get_available_variations( 'object' )
			as $key => $variation
		) {
			$this->assertStringContainsString( $expected[ $key ], wc_get_stock_html( $variation ) );
		}
	}

	public function test_can_set_variation_max_purcahse_qty() {

		// WC sets max to 1 if out of stock and not purchaseable!
		$expected = array( 3, 1, 1, 1, 1, 1 );

		foreach ( $this->product_variable->get_available_variations( 'object' )
			as $key => $variation
		) {
			$this->assertEquals(
				$expected[ $key ],
				$this->product_variable->get_available_variation( $variation )['max_qty']
			);
		}
	}

}
