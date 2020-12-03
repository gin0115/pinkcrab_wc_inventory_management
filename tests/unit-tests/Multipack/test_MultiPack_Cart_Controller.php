<?php

/**
 * Tests for Multipack_Cart_Controller
 *
 * @package PinkCrab/WooMan
 */

use PinkCrab\Core\App;
use Automattic\Jetpack\Constants;
use PinkCrab\InventoryManagment\Settings\WooCommece_Settings;

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
	 * WooCommerce Cart.
	 *
	 * @var WC_Cart
	 */
	protected $cart;

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
			$this->cart             = WC()->cart;
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
	}

	/**
	 * Tests that cart item & data is added when adding item to the cart.
	 *
	 * @filter woocommerce_add_cart_item_data
	 * @filter woocommerce_get_item_data
	 * @return void
	 */
	public function test_meta_is_added_to_mp_items_in_cart() {

		// Ensure enough stock.
		$this->product_variable->set_stock_quantity( 12 );
		$this->product_variable->save();

		// Add 3rd Variation (3 Pack)
		$variations = $this->product_variable->get_available_variations();

		// Add to cart.
		$this->cart->add_to_cart(
			$this->product_variable->get_id(),
			1,
			$variations[2]['variation_id']
		);

		// Check the item has the meta data added.
		$cart_item = array_values( $this->cart->get_cart() );
		$this->assertArrayHasKey( WooCommece_Settings::CART_MULTIPACK_SIZE_META, $cart_item[0] );

		// Check it is displayed.
		$this->assertStringContainsString(
			'<dd class="variation-Packsize"><p>3</p>',
			wc_get_formatted_cart_item_data( $cart_item[0] )
		);
	}


	/**
	 * Clears all custom defined constants.
	 *
	 * @return void
	 */
	public function tearDown(): void {
		// Reset product stock.
		$this->product_variable->set_stock_quantity( $this->starting_stock );
		$this->product_variable->save();

		// Clear any temp constants.
		Constants::clear_constants();

		// Empty the cart
		$this->cart->empty_cart();
	}
}
