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

use PinkCrab\Core\Interfaces\Registerable;
use PinkCrab\Core\Services\Registration\Loader;
use PinkCrab\InventoryManagment\Settings\WooCommece_Settings;
use PinkCrab\InventoryManagment\MultiPack\MultiPack_Helper_Trait;
use WC_Product, WC_Product_Variation, WC_Order_Item_Product, WC_Order;

class Multipack_Cart_Controller implements Registerable {

	/**
	 * @method int product_packsize_modifer()
	 * @method int get_max_qty_for_product()
	 * @method string managed_stock_product()
	 * @method int  get_modified_stock_level()
	 */
	use MultiPack_Helper_Trait;

	/**
	 * Hook loader.
	 *
	 * @param \PinkCrab\Core\Services\Registration\Loader $loader
	 * @return void
	 */
	public function register( Loader $loader ): void {
		// If we are using the multipack modifier.
		if ( WooCommece_Settings::allow_multipack() ) {
			$loader->front_filter( 'woocommerce_product_get_stock_quantity', array( $this, 'set_max_input_value' ), 10, 2 );
			$loader->front_filter( 'woocommerce_product_variation_get_stock_quantity', array( $this, 'set_max_input_value' ), 10, 2 );
			$loader->filter( 'woocommerce_add_cart_item_data', array( $this, 'set_packsize_as_cart_item_meta' ), 10, 3 );
			$loader->filter( 'woocommerce_get_item_data', array( $this, 'display_pack_size_in_cart' ), 10, 2 );
		}
	}

	/**
	 * Ensures that the max input value in the cart takes into account multipack
	 *
	 * @param int|null $stock
	 * @param WC_Product|null $product
	 * @return int|null
	 */
	public function set_max_input_value( $stock = 0, ?WC_Product $product = null ): ?int {

		if ( is_a( $product, WC_Product_Variation::class ) && $stock === 0 ) {
			return 1;
		}

		return is_cart()
			? (int) floor( $stock / $this->product_packsize_modifer( $product ) )
			: $stock;
	}

	/**
	 * Sets the packsize to cart item meta.
	 *
	 * @param array $cart_item_meta
	 * @param int $product_id
	 * @param int|null $variation_id
	 * @return void
	 */
	public function set_packsize_as_cart_item_meta( array $cart_item_meta, int $product_id, ?int $variation_id = null ) {

		// If variation.
		if ( $variation_id && $variation_id >= 1 ) {
			$pack_size = $this->packsize_modifier_from_id( $variation_id );
		} elseif ( $product_id && $product_id >= 1 ) {
			$pack_size = $this->packsize_modifier_from_id( $variation_id );
		} else {
			$pack_size = 1;
		}

		// If packsize is greater than 1, add in meta.
		if ( $pack_size > 1 ) {
			$pack_size_template = WooCommece_Settings::cart_item_pack_size_template();
			$cart_item_meta[ WooCommece_Settings::CART_MULTIPACK_SIZE_META ]
				= str_replace( '{pack_size}', (string) $pack_size, $pack_size_template );
		}

		return $cart_item_meta;
	}

	/**
	 * Display the packsize in the cart.
	 *
	 * @param array $item_data
	 * @param array $cart_item_data
	 * @return array
	 */
	public function display_pack_size_in_cart( array $item_data, array $cart_item_data ): array {
		if ( array_key_exists( WooCommece_Settings::CART_MULTIPACK_SIZE_META, $cart_item_data ) ) {
			$item_data[] = array(
				'key'     => __( 'Packsize', 'pc_invman' ),
				'value'   => $cart_item_data[ WooCommece_Settings::CART_MULTIPACK_SIZE_META ],
				'display' => '',
			);
		}

		return $item_data;
	}
}
