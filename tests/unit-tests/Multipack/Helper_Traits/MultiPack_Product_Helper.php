<?php

/**
 * Helper trait for products in MuliPack tests.
 *
 * @package PinkCrab/WooMan
 */


use PinkCrab\InventoryManagment\MultiPack\MultiPack_Config;

trait MultiPack_Product_Helper {

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

}
