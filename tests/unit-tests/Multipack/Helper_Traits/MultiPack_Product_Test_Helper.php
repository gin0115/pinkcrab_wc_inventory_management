<?php

/**
 * Helper trait for products in MuliPack tests.
 *
 * @package PinkCrab/WooMan
 */


use PinkCrab\InventoryManagment\MultiPack\MultiPack_Config;

trait MultiPack_Product_Test_Helper {

	/**
	 * Sets the main starting stock for all products.
	 */
	public $starting_stock = 3;

	/**
	 * Creates the test product.
	 *
	 * @return WC_Product
	 */
	public function create_test_variable_product(): \WC_Product {

		// Create primary test product.
		$product = \WC_Helper_Product::create_variation_product();
		$product->set_manage_stock( true );
		$product->set_props( array( 'manage_stock' => true ) );
		$product->set_stock_quantity( $this->starting_stock );
		$product->set_backorders( 'no' );
		$product->set_price( 12.99 );
		$product->save();

		// Add the pack modifiers.
		foreach ( $product->get_available_variations() as $key => $variation ) {
			update_post_meta(
				$variation['variation_id'],
				MultiPack_Config::WC_SETTINGS_DEFAULT_MULTIPLIER_KEY,
				( $key + 1 )
			);
		}

		return $product;
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
