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

/**
 * Class Functions.
 */
class Test_Multipack_Product_Controller extends WC_Unit_Test_Case {

	protected $app;
	protected $product_variable;

	public const STARTING_STOCK_VAL = 3;

	public function setUp() {
		if ( ! $this->app ) {
			$this->app = App::getInstance();
			$this->create_test_variable_product();
		}
	}

	/**
	 * Creates the test product.
	 *
	 * @return void
	 */
	public function create_test_variable_product(): void {

		// Create primary test product.
		$this->product_variable = \WC_Helper_Product::create_variation_product();
		$this->product_variable->set_manage_stock( true );
		$this->product_variable->set_stock_quantity( self::STARTING_STOCK_VAL );
		$this->product_variable->set_backorders( 'no' );
		$this->product_variable->save();

		// Add the pack modifiers.
		foreach ( $this->product_variable->get_available_variations() as $key => $variation ) {
			update_post_meta(
				$variation['variation_id'],
				MultiPack_Config::WC_SETTINGS_DEFAULT_MULTIPLIER_KEY,
				( $key + 1 )
			);
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
		$this->product_variable->set_stock_quantity( self::STARTING_STOCK_VAL );
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
		$this->product_variable->set_stock_quantity( self::STARTING_STOCK_VAL );
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
