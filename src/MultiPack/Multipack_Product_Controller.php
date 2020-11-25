<?php

declare(strict_types=1);
/**
 * Handles all product interations for multipack.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\InventoryManagment
 */

namespace PinkCrab\InventoryManagment\MultiPack;

use WP_Post, WC_Product;
use PinkCrab\Core\Interfaces\Registerable;
use PinkCrab\Core\Services\Registration\Loader;
use PinkCrab\InventoryManagment\MultiPack\MultiPack_Config;
use PinkCrab\InventoryManagment\MultiPack\MultiPack_Helper_Trait;

class Multipack_Product_Controller implements Registerable {

	use MultiPack_Helper_Trait;

	/**
	 * Registers all hook and filter call.
	 *
	 * @param Loader $loader
	 * @return void
	 */
	public function register( Loader $loader ): void {
		$loader->filter( 'woocommerce_product_is_in_stock', array( $this, 'is_in_stock' ), 10, 2 );
	}

	/**
	 * Checks if enough stock to account for packsize multiplier.
	 *
	 * @param bool $in_stock
	 * @param WC_Product $product
	 * @return bool
	 */
	public function is_in_stock( bool $in_stock, WC_Product $product ): bool {
		return $product->get_total_stock() >= $this->product_packsize_modifer( $product );
	}
}
