<?php

declare(strict_types=1);

/**
 * Collection of helper fucntions
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\InventoryManagment
 */

namespace PinkCrab\InventoryManagment\MultiPack;

use WC_Product;
use PinkCrab\InventoryManagment\MultiPack\MultiPack_Config;

trait MultiPack_Helper_Trait {

	/**
	 * Reutrns the pack size modifer value from a product.
	 * Uses fallback if not defined.
	 *
	 * @param WC_Product $product
	 * @return int
	 */
	protected function product_packsize_modifer( WC_Product $product ): int {
		$modifier = $product->get_meta( MultiPack_Config::WC_SETTINGS_DEFAULT_MULTIPLIER_KEY );
		return ! empty( $modifier ) ? (int) $modifier : 1;
	}

	/**
	 * Reutrns the pack size modifer value from a product id.
	 * Uses fallback if not defined.
	 *
	 * @param int $product_id
	 * @return int
	 */
	protected function packsize_modifier_from_id( int $product_id ): int {
		$modifier = get_post_meta( $product_id, MultiPack_Config::WC_SETTINGS_DEFAULT_MULTIPLIER_KEY, true );
		return ! empty( $modifier ) ? (int) $modifier : 1;
	}

	/**
	 * Checks if the product has managed stocks.
	 * Used to decide if stock should be controlled.
	 *
	 * @param \WC_Product $product
	 * @return bool
	 */
	protected function managed_stock_product( WC_Product $product ): bool {

		// If the product isnt purchase able, return false.
		if ( ! $product->is_purchasable() ) {
			return false;
		}
		// If the product is managable.
		if ( is_bool( $product->get_manage_stock() ) && $product->get_manage_stock() ) {
			return true;
		}

		// If the variation doenst have tracked shipping, check parent.
		if ( $product->get_type() === 'variation' || $product->get_manage_stock() === 'parent' ) {
			return wc_get_product( $product->get_parent_id() )->get_manage_stock();
		}

		// Fallback to false.
		return false;
	}

	/**
	 * Gets the modified stock level for a product.
	 *
	 * @param \WC_Product $product
	 * @return int
	 */
	protected function get_modified_stock_level( WC_Product $product ): int {
		return (int) floor( $this->get_total_stock( $product ) / $this->product_packsize_modifer( $product ) );
	}

	/**
	 * Returns the max qty for a product, based on its modifier.
	 * Uses the highest variation stock level for variable (as per WC)
	 *
	 * @param \WC_Product $product
	 * @return int
	 */
	protected function get_max_qty_for_product( WC_Product $product ): int {

		// If backorderable return -1 (no limit) and let W`C handle.
		if ( $product->backorders_allowed() ) {
			return -1;
		}

		// If we have a variable product, return the max modified qty from all variations.
		if ( $product->get_type() === 'variable' ) {
			return max(
				array_map(
					function( array $e ): int {
						$variation = wc_get_product( $e['variation_id'] );
						return $this->get_modified_stock_level( $variation );
					},
					$product->get_available_variations()
				)
			);
		} else {
			// If not a variable, just return the modified value.
			return $this->get_modified_stock_level( $product );
		}
	}

	/**
	 * Polly fill for get_total_stock
	 *
	 * @param \WC_Product $product
	 * @return int
	 */
	public function get_total_stock( WC_Product $product ): ?int {
		switch ( $product->get_type() ) {
			case 'variation':
				return $product->get_stock_quantity();

			case 'variable':
				// Get the parent stocks.
				$parent        = wc_get_product( $product->get_id() );
				$parent_stocks = $parent->managing_stock()
					? $parent->get_stock_quantity( $parent ) : 0;

				return $parent_stocks + array_sum(
					array_map(
						function( $e ) {
							$variation = wc_get_product( $e );
							return $variation->managing_stock() === true
								? $variation->get_stock_quantity() ?? 0
								: 0;
						},
						$product->get_children()
					)
				);

			default:
				return 0;

		}
	}

}
