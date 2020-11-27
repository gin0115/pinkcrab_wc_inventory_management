<?php

declare(strict_types=1);
/**
 * Handles all functionality for using MultiPack modifier in the cart/check
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\InventoryManagment
 */

namespace PinkCrab\InventoryManagment\MultiPack;

use WC_Product, WC_Product_Variation;
use PinkCrab\Core\Interfaces\Registerable;
use PinkCrab\Core\Services\Registration\Loader;
use PinkCrab\InventoryManagment\Settings\WooCommece_Settings;
use PinkCrab\InventoryManagment\MultiPack\MultiPack_Helper_Trait;

class Multipack_Cart_Controller implements Registerable {

	/**
	 * @method int product_packsize_modifer()
	 * @method int get_max_qty_for_product()
	 * @method string managed_stock_product()
	 * @method int  get_modified_stock_level()
	 */
	use MultiPack_Helper_Trait;

	public function register( Loader $loader ): void {
		// If we are using the multipack modifier.
		if ( WooCommece_Settings::allow_multipack() ) {
			$loader->front_filter( 'woocommerce_product_get_stock_quantity', array( $this, 'set_max_input_value' ), 10, 2 );
			$loader->front_filter( 'woocommerce_product_variation_get_stock_quantity', array( $this, 'set_max_input_value' ), 10, 2 );
		}
	}

	/**
	 * Ensures that the max input value in the cart takes into account multipack
	 *
	 * @param int|null $stock
	 * @param WC_Product|null $product
	 * @return int|null
	 */
	public function set_max_input_value( ?int $stock = 0, ?WC_Product $product = null ): ?int {

		if ( is_a( $product, WC_Product_Variation::class ) && $stock === 0 ) {
			return 1;
		}

		return is_cart()
			? (int) floor( $stock / $this->product_packsize_modifer( $product ) )
			: $stock;
	}
}


