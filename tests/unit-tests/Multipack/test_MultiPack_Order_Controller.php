<?php

/**
 * Tests for Multipack_Cart_Controller
 *
 * @package PinkCrab/WooMan
 */

use PinkCrab\Core\App;
use Automattic\Jetpack\Constants;
use PinkCrab\InventoryManagment\Settings\WooCommece_Settings;
use PinkCrab\InventoryManagment\MultiPack\MultiPack_Helper_Trait;

// Include helper trait
require_once 'Helper_Traits/MultiPack_Product_Test_Helper.php';

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
	use MultiPack_Product_Test_Helper, MultiPack_Helper_Trait;

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

	/**
	 * Tests that an variation can be ordered.
	 *
	 * @return void
	 */
	public function test_packsize_is_taken_into_account_in_stock_deduction(): void {
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

		// Check the note with adjustment is created.
		$notes             = wc_get_order_notes(
			array(
				'order_id' => $order->get_id(),
				'type'     => 'internal',
			)
		);
		$reduction_notices = array_filter(
			$notes,
			function( $e ) {
				return str_contains( $e->content, '<b>Stock Changes</b>' )
				&& str_contains( $e->content, '(Packsize: 3) 12 &rarr; 3' );
			}
		);
		$this->assertCount( 1, $reduction_notices );

		// Check the order item meta is set.
		$items = $order->get_items();
		foreach ( $items as $item ) {
			$this->assertTrue( $item->meta_exists( WooCommece_Settings::CART_MULTIPACK_SIZE_META ) );
			$this->assertEquals( 3, $item->get_meta( WooCommece_Settings::CART_MULTIPACK_SIZE_META, 'FAILED' ) );
		}

		// Attempt again, but expect error as not enough stock (3 left)!
		WC()->cart->empty_cart();

		$cart_item_key2 = WC()->cart->add_to_cart(
			$variations[2]['variation_id'],
			3,
			0
		);

		$order = $this->do_complete_order_successfully();
		$this->assertWPError( $order );

	}

	
}
