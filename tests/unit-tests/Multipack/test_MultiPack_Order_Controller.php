<?php

/**
 * Tests for Multipack_Cart_Controller
 *
 * @package PinkCrab/WooMan
 */

use PinkCrab\Core\App;
use MultiPack_Product_Helper;
use Automattic\Jetpack\Constants;
use PinkCrab\InventoryManagment\Settings\WooCommece_Settings;
use PinkCrab\InventoryManagment\MultiPack\MultiPack_Helper_Trait;

// Include helper trait
require_once 'Helper_Traits/MultiPack_Product_Helper.php';

/**
 * Class Functions.
 */
class test_MultiPack_Order_Controller extends WC_Unit_Test_Case {

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
	use MultiPack_Product_Helper, MultiPack_Helper_Trait;

	/**
	 * Sets the app container if its not already.
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();
		if ( ! $this->app ) {
			$this->app              = App::getInstance();
			$this->product_variable = $this->create_test_variable_product();
		}
		WC()->cart->empty_cart();
	}

	/**
	 * TearDown.
	 */
	public function tearDown() {
		parent::tearDown();
		WC()->cart->empty_cart();
		$this->product_variable->set_stock_quantity( $this->starting_stock );
		$this->product_variable->save();
	}

	public function test_packsize_is_taken_into_account_in_stock_deduction() {
		$this->product_variable->set_stock_quantity( 12 );
		$this->product_variable->save();

		// Add 3rd Variation (3 Pack)
		$variations = $this->product_variable->get_available_variations();

		// Add to cart.
		$cart_item_key1 = WC()->cart->add_to_cart(
			$variations[2]['variation_id'],
			3,
			0
		);

		$this->assertIsString( $cart_item_key1 );
		$this->assertEquals( true, WC()->cart->check_cart_items() );

		// Complete the checkout and get the order
		$order = $this->do_complete_order_successfully();
		$order->payment_complete();

		// Check stock is deducted by 9 (3 * 3)
		$this->assertEquals( 3, $this->get_total_stock( $this->product_variable ) );

		// Attempt again.
		WC()->cart->empty_cart();

		$cart_item_key2 = WC()->cart->add_to_cart(
			$variations[2]['variation_id'],
			3,
			0
		);

		$order = $this->do_complete_order_successfully();
		$this->assertWPError( $order );

	}

	/**
	 * Completes a checkout and returns the WC_Order instance.
	 *
	 * @return WC_Order|WP_Error
	 */
	public function do_complete_order_successfully() {
		$checkout = WC_Checkout::instance();
		$order_id = $checkout->create_order(
			array(
				'billing_email'  => 'a@b.com',
				'payment_method' => 'dummy_payment_gateway',
			)
		);
		return ! is_wp_error( $order_id ) ? new WC_Order( $order_id ) : $order_id;
	}
}
