<?php

declare(strict_types=1);
/**
 * Handles all functionality for using MultiPack modifier in an Order
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\InventoryManagment
 */

namespace PinkCrab\InventoryManagment\MultiPack;

use PinkCrab\Core\Collection\Collection;
use PinkCrab\Core\Interfaces\Registerable;
use PinkCrab\Core\Services\Registration\Loader;
use PinkCrab\FunctionConstructors\Arrays as Arr;
use PinkCrab\FunctionConstructors\Strings as Str;
use PinkCrab\InventoryManagment\Settings\WooCommece_Settings;
use PinkCrab\InventoryManagment\MultiPack\MultiPack_Helper_Trait;
use WC_Order_Item_Product, WC_Order, WC_Meta_Data, WC_Order_Item;

class Multipack_Order_Controller implements Registerable {

	/**
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
			$loader->action( 'woocommerce_checkout_create_order_line_item', array( $this, 'add_packsize_to_order_item' ), 10, 4 );
			$loader->filter( 'woocommerce_display_item_meta', array( $this, 'replace_packsize_key_with_string_in_confirmation' ), 10, 3 );
			$loader->admin_filter( 'woocommerce_order_item_display_meta_key', array( $this, 'replace_packsize_key_with_string_in_order_admin' ), 10, 3 );
			$loader->filter( 'woocommerce_order_item_quantity', array( $this, 'adjust_order_item_quantity' ), 10, 3 );
			$loader->action( 'woocommerce_order_note_added', 'add_modified_stock_changes_note', 10, 2 );
			// Refund (AJAX)
			// $loader->action(
			// 	'woocommerce_create_refund',
			// 	function( $refund, &$args ) {
			// 		if ( $args['order_id'] > 0 ) {

			// 			$line_items = \wc_get_order( $args['order_id'] )->get_items();

			// 			foreach ( $line_items as $item_id => $item ) {
			// 				$args['line_items'][ $item_id ]['qty'] =
			// 					$item->meta_exists( WooCommece_Settings::CART_MULTIPACK_SIZE_META )
			// 						? (int) $item->get_meta( WooCommece_Settings::CART_MULTIPACK_SIZE_META, true ) * $item->get_quantity()
			// 						: $item->get_quantity();
			// 			}
			// 			// adie( $refund->get_items(), $args );
			// 		}
			// 	},
			// 	10,
			// 	2
			// );

			$loader->filter(
				'woocommerce_api_create_order_refund_data',
				function ( array $request, int $order_id, WC_Order $order ): array {
					dump( $request, $order_id, $order );
					die();
					return $request;
				},
				10,
				3
			);
			$loader->action(
				'woocommerce_admin_order_item_values',
				function( $product, $order_item, $item_id ) {
					if ( is_a( $order_item, WC_Order_Item_Product::class )
						&& $order_item->meta_exists( WooCommece_Settings::CART_MULTIPACK_SIZE_META )
					) {
						$order_item->set_quantity(
							(int) $order_item->get_meta( WooCommece_Settings::CART_MULTIPACK_SIZE_META, true ) * $order_item->get_quantity()
						);
					}
				},
				10,
				3
			);
		}
	}

	/**
	 * Adjust the qty to increase/decrease stock based on item meta.
	 *
	 * @param int $qty
	 * @param WC_Order $order
	 * @param WC_Order_Item_Product $order_item
	 * @return int
	 */
	public function adjust_order_item_quantity( int $qty, WC_Order $order, WC_Order_Item_Product $order_item ): int {
		dump( array( 'adjust_order_item_quantity', $qty, $order, $order_item ) );
		return $order_item->meta_exists( WooCommece_Settings::CART_MULTIPACK_SIZE_META )
			? (int) $order_item->get_meta( WooCommece_Settings::CART_MULTIPACK_SIZE_META, true ) * $qty
			: $qty;
	}

	/**
	 * Replaces the existing note, if it contains stock changes.
	 * The new note reflects the packsize modifier.
	 *
	 * @param int $note_id
	 * @param WC_Order $order
	 * @return void
	 */
	public function add_modified_stock_changes_note( int $note_id, WC_Order $order ): void {
		dump( get_comment_text( $note_id ) );
		if ( str_contains( get_comment_text( $note_id ), 'Stock levels reduced:' ) ) {

			// Get all valid & mapped stock change details.
			$changes = $this->compile_stock_change_note( $order );

			// If we have items stock to display.
			if ( ! $changes->is_empty() ) {
				// Delete the old note and add note..
				wp_delete_comment( $note_id, true );
				$order->add_order_note( $changes->unshift( '<b>Stock Changes</b>' )->join( '</br>' ) );
			}

			dd( $changes->join( '</br>' ) );
		}
	}

	/**
	 * Compiles the collection of new stock changes.
	 *
	 * @param WC_Order $order
	 * @return \PinkCrab\Core\Collection\Collection
	 */
	protected function compile_stock_change_note( WC_Order $order ): Collection {
		return Collection::from( $order->get_items() )
			->filter( // Only log items which have managed stock.
				function( $e ) {
					return (bool) $e->get_product()->managing_stock();
				}
			)
			->map( // Map into string for note content.
				function( $order_item ) {
					// Get the modifer from meta.
					$modifier = $order_item->meta_exists( WooCommece_Settings::CART_MULTIPACK_SIZE_META )
						? (int) $order_item->get_meta( WooCommece_Settings::CART_MULTIPACK_SIZE_META, true )
						: 1;
					// Use current stock as the basis.
					$product   = $order_item->get_product();
					$new_stock = $product->get_stock_quantity();

					return sprintf(
						'%s%s %d &rarr; %d',
						$product->get_formatted_name(),
						$modifier > 1 ? ( " (Packsize: {$modifier})" ) : '',
						$new_stock + ( $order_item->get_quantity() * $modifier ),
						$new_stock
					);
				}
			);
	}

		/**
	 * Adds the packsize to the order item.
	 *
	 * @param WC_Order_Item_Product $item
	 * @param string $cart_item_key
	 * @param array $cart_item_data
	 * @param WC_Order $order
	 * @return void
	 */
	public function add_packsize_to_order_item( WC_Order_Item_Product $item, string $cart_item_key, array $cart_item_data, WC_Order $order ) {
		if ( array_key_exists( WooCommece_Settings::CART_MULTIPACK_SIZE_META, $cart_item_data ) ) {
			$item->add_meta_data(
				WooCommece_Settings::CART_MULTIPACK_SIZE_META,
				$cart_item_data[ WooCommece_Settings::CART_MULTIPACK_SIZE_META ]
			);
		}
	}

	/**
	 * Replaces the multipack meta key with Packsize: string.
	 *
	 * @param string $html
	 * @param string $item
	 * @param array $args
	 * @return string
	 */
	public function replace_packsize_key_with_string_in_confirmation(
		string $html,
		WC_Order_Item_Product $item,
		array $args
	): string {
		// Only on the confirmation page.
		if ( is_order_received_page() ) {
			$html = $this->replace_multipack_meta_key_in_string( $html );
		}

		return $html;
	}

	/**
	 * Replaces the meta key with its sting value in order item meta (backend)
	 *
	 * @param string $display_key
	 * @param WC_Meta_Data $meta
	 * @param WC_Order_Item $order_item
	 * @return string
	 */
	public function replace_packsize_key_with_string_in_order_admin( string $display_key, WC_Meta_Data $meta, WC_Order_Item $order_item ):string {

		if ( $display_key === WooCommece_Settings::CART_MULTIPACK_SIZE_META ) {
			$display_key = $this->replace_multipack_meta_key_in_string( $display_key );
		}

		return $display_key;
	}

}
