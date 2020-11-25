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

use WC_Product;
use PinkCrab\Core\Interfaces\Registerable;
use PinkCrab\Core\Services\Registration\Loader;
use PinkCrab\InventoryManagment\Settings\WooCommece_Settings;
use PinkCrab\InventoryManagment\MultiPack\MultiPack_Helper_Trait;

class Multipack_Product_Controller implements Registerable {

	/**
	 * @method int product_packsize_modifer()
	 * @method int get_max_qty_for_product()
	 * @method string managed_stock_product()
     * @method int  get_modified_stock_level()
	 */
	use MultiPack_Helper_Trait;

	/**
	 * Registers all hook and filter call.
	 *
	 * @param Loader $loader
	 * @return void
	 */
	public function register( Loader $loader ): void {
		// If we are using the multipack modifier.
		if ( WooCommece_Settings::allow_multipack() ) {
			$loader->front_filter( 'woocommerce_product_is_in_stock', array( $this, 'is_in_stock' ), 10, 2 );
			$loader->front_filter( 'woocommerce_get_stock_html', array( $this, 'stock_level_html' ), 10, 2 );
			$loader->front_filter( 'woocommerce_quantity_input_max', array( $this, 'set_max_input_value' ), 30, 2 );
			$loader->front_filter( 'woocommerce_available_variation', array( $this, 'set_variation_max_input_value' ), 10, 3 );
		}
	}

	/**
	 * Checks if enough stock to account for packsize multiplier.
	 * If the item allows backorder, just use the WC in_stock value (true)
	 *
	 * @param bool $in_stock
	 * @param WC_Product $product
	 * @return bool
	 */
	public function is_in_stock( bool $in_stock, WC_Product $product ): bool {
		return $this->managed_stock_product( $product ) && ! $product->backorders_allowed()
			? $product->get_total_stock() >= $this->product_packsize_modifer( $product )
			: $in_stock;
	}

	/**
	 * Defines the string value of the current stock.
	 *
	 * @param string $stock
	 * @param WC_Product $product
	 * @return string
	 */
	public function stock_level_html( string $stock, WC_Product $product ): string {

		$availability     = $product->get_availability();
		$product_modifier = $this->product_packsize_modifer( $product );
		$modified_stocks  = $this->get_modified_stock_level( $product );

		// If out of stock and its backorderable but also out of stock.
		if ( $availability['class'] === 'out-of-stock'
			&& ! $product->backorders_allowed()
		) {
			return $stock;
		}

		// If backorderable and modified stocks are less than 0.
		if ( $product->backorders_allowed() && $modified_stocks <= 0 ) {
			// Based on if notify customer.
			return $product->backorders_require_notification()
				? '<p class="stock available-on-backorder">Available on backorder</p>'
				: '<p class="stock in-stock">In stock</p>';
		}

		// If in stock, work out the modifed stock level.
		return $modified_stocks <= 0
			? '<p class="stock out-of-stock">Out of stock</p>'
			: sprintf(
				"<p class='stock in-stock'>%d %sin stock</p>",
				$modified_stocks,
				$product_modifier > 1 ? " x {$product_modifier} pack " : ''
			);
	}

	/**
	 * Sets the input forms max value.
	 *
	 * @param string $value
	 * @param WC_Product $product
	 * @return string
	 */
	public function set_max_input_value( string $value, WC_Product $product ):string {
		return (string) $this->get_max_qty_for_product( $product );
	}

	/**
	 * Sets the max orerable qty for a variation.
	 *
	 * @param array $attributes
	 * @param WC_Product $parent
	 * @param WC_Product $variation
	 * @return array
	 */
	public function set_variation_max_input_value( array $attributes, WC_Product $parent, WC_Product $variation ): array {
		$max                   = $this->get_max_qty_for_product( $variation );
		$attributes['max_qty'] = $max <= 1 ? 1 : $max;
		return $attributes;
	}
}

