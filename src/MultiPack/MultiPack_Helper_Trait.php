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
}
