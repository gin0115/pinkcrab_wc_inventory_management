<?php

declare(strict_types=1);
/**
 * Handles all functionality for using MultiPack modifier for manual orders and refunds.
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
use WC_Order_Item_Product, WC_Order, WC_Meta_Data, WC_Order_Item, WC_Product;

class Multipack_Manual_Order_Controller implements Registerable {

	/**
	 * @method int packsize_modifier_from_id()
	 */
	use MultiPack_Helper_Trait;


	/**
	 * Hook loader.
	 *
	 * @param \PinkCrab\Core\Services\Registration\Loader $loader
	 * @return void
	 */
	public function register( Loader $loader ): void {
		if ( WooCommece_Settings::allow_multipack() ) {
			$loader->admin_action( 'woocommerce_restock_refunded_item', array( $this, 'adjust_refund_item_stock_adjustments' ), 10, 5 );
			$loader->admin_action( 'woocommerce_ajax_add_order_item_meta', array( $this, 'add_packsize_to_ajax_order_item' ), 10, 3 );
			$loader->admin_action( 'woocommerce_saved_order_items', array( $this, 'adjust_order_item_stock_adjustments' ), 10, 2 );
		}
	}

	/**
	 * Handles the additional stock reductions and triggers adjustment notes.
	 *
	 * @param int $order_id
	 * @param array $items
	 * @return void
	 */
	public function adjust_order_item_stock_adjustments( int $order_id, array $items ): void {
		$order = wc_get_order( $order_id );

		foreach ( $items['order_item_id'] as $item_id ) {
			// Get the item.
			$item = \WC_Order_Factory::get_order_item( \absint( $item_id ) );

			if ( $item->meta_exists( WooCommece_Settings::CART_MULTIPACK_SIZE_META ) ) {

				$pack_size_modifier = $item->get_meta( WooCommece_Settings::CART_MULTIPACK_SIZE_META, true );
				$qty                = \absint( $items['order_item_qty'][ $item_id ] );
				$adjustment_qty     = ( $qty * $pack_size_modifier ) - $qty;
				$product            = $item->get_product();

				$ammended_stock = wc_update_product_stock( $product, $adjustment_qty, 'decrease' );
				$order->add_order_note( sprintf( __( '(Packsize Adjustment) Item #%1$s stock decreased from %2$s to %3$s.', 'woocommerce' ), $product->get_formatted_name(), $ammended_stock + $adjustment_qty, $ammended_stock ) );

			}
		}
	}

	/**
	 * Makes the stock level adjustment, if item uses the pack size multipiler.
	 *
	 * @param int $product_id
	 * @param int $old_stock
	 * @param int $new_stock
	 * @param WC_Order $order
	 * @param WC_Product $product
	 * @return void
	 */
	public function adjust_refund_item_stock_adjustments(
		int $product_id,
		int $old_stock,
		int $new_stock,
		WC_Order $order,
		WC_Product $product
	): void {
		foreach ( $order->get_items() as $order_item ) {
						// If the current product being processed.
			if ( is_a( $order_item, WC_Order_Item_Product::class )
					&& $order_item->meta_exists( WooCommece_Settings::CART_MULTIPACK_SIZE_META )
					&& ( $order_item->get_variation_id() == $product_id || $order_item->get_product_id() == $product_id )
				) {

				// Calcualte differences
				$inital_refuned_qty = $new_stock - $old_stock;
				$adjustment_qty     = ( $order_item->get_meta( WooCommece_Settings::CART_MULTIPACK_SIZE_META, true ) * $inital_refuned_qty ) - $inital_refuned_qty;

				// If there is an adjust qty, make stock change and set notice.
				if ( $adjustment_qty > 0 ) {
					$ammended_stock = wc_update_product_stock( $product, $adjustment_qty, 'increase' );
					$order->add_order_note( sprintf( __( '(Packsize Adjustment) Item #%1$s stock increased from %2$s to %3$s.', 'woocommerce' ), $product_id, $new_stock, $ammended_stock ) );
				}
			}
		}
	}

	/**
	 * Adds the meta field to the order items, when added in wp-admin via ajax.
	 *
	 * @param int $item_id
	 * @param [type] $item
	 * @param WC_Order $order
	 * @return void
	 */
	public function add_packsize_to_ajax_order_item( int $item_id, $item, WC_Order $order ): void {
		$cart_item = $order->get_item( $item_id );
		dump($cart_item);
		// If this is a product.
		if ( is_a( $cart_item, WC_Order_Item_Product::class ) ) {

			$pack_size = $this->packsize_modifier_from_id( $cart_item->get_product()->get_id() );
			if ( $pack_size > 1 ) {
				$pack_size_template = WooCommece_Settings::cart_item_pack_size_template();
				wc_update_order_item_meta(
					$item_id,
					WooCommece_Settings::CART_MULTIPACK_SIZE_META,
					str_replace( '{pack_size}', (string) $pack_size, $pack_size_template )
				);
			}
		}
	}
}
