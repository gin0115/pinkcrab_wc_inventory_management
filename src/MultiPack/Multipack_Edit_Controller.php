<?php

declare(strict_types=1);
/**
 * Handles all edit product interations for multipack.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\InventoryManagment
 */

namespace PinkCrab\InventoryManagment\MultiPack;

use WP_Post, WC_Product_Variable;
use PinkCrab\Core\Interfaces\Registerable;
use PC_Vendor\GuzzleHttp\Psr7\ServerRequest;
use PinkCrab\Core\Services\Registration\Loader;
use PC_Vendor\Psr\Http\Message\ServerRequestInterface;
use PinkCrab\InventoryManagment\MultiPack\MultiPack_Helper_Trait;

class Multipack_Edit_Controller implements Registerable {

	use MultiPack_Helper_Trait;

	/**
	 * Packsize Metakey
	 *
	 * @var string
	 */
	protected $pack_size_key = MultiPack_Config::WC_SETTINGS_DEFAULT_MULTIPLIER_KEY;

	/**
	 * HTTP Request
	 *
	 * @var \PC_Vendor\Psr\Http\Message\ServerRequestInterface
	 */
	protected $request;

	/**
	 * Creates an instance of the Multipack_Edit_Controller
	 *
	 * @param \PC_Vendor\Psr\Http\Message\ServerRequestInterface $request
	 */
	public function __construct( ServerRequestInterface $request ) {
		$this->request = $request;
	}

	/**
	 * Registers all hook and filter call.
	 *
	 * @param Loader $loader
	 * @return void
	 */
	public function register( Loader $loader ): void {

		// Simple Products
		$loader->action( 'woocommerce_product_options_stock_fields', array( $this, 'render_simple_pack_size_input' ) );
		$loader->action( 'woocommerce_process_product_meta', array( $this, 'save_pack_size' ) );

		// Variable Products
		$loader->action( 'woocommerce_variation_options_pricing', array( $this, 'render_variation_pack_size_input' ), 10, 3 );
		$loader->action( 'woocommerce_save_product_variation', array( $this, 'save_pack_size' ), 10, 2 );

	}

	/**
	 * Renders the input for simple products.
	 *
	 * @return void
	 */
	public function render_simple_pack_size_input():void {
		global $product_object;

		// Bail if variable product.
		if ( is_a( $product_object, WC_Product_Variable::class ) ) {
			return;
		}

		woocommerce_wp_text_input(
			array(
				'id'                => $this->pack_size_key,
				'value'             => $this->product_packsize_modifer( $product_object ),
				'placeholder'       => 'Packsize (Defualts to 1)',
				'label'             => __( 'Packsize', 'woocommerce' ),
				'desc_tip'          => true,
				'description'       => __( 'When this item is purchased what is packsize value used per item.', 'woocommerce' ),
				'type'              => 'number',
				'custom_attributes' => array(
					'step' => 'any',
				),
			)
		);
	}

	/**
	 * Renders the packsize input for variable producuts.
	 *
	 * @param int $loop
	 * @param array $variation_data
	 * @param WP_Post $variation
	 * @return void
	 */
	public function render_variation_pack_size_input( int $loop, array $variation_data, WP_Post $variation ): void {

		woocommerce_wp_text_input(
			array(
				'id'                => $this->pack_size_key . $loop,
				'name'              => $this->pack_size_key . "[{$loop}]",
				'value'             => $this->packsize_modifier_from_id( (int) $variation->ID ),
				'label'             => __( 'Packsize', 'woocommerce' ),
				'desc_tip'          => true,
				'description'       => __( 'When this item is purchased what is packsize value used per item.', 'woocommerce' ),
				'type'              => 'number',
				'custom_attributes' => array(
					'step' => 'any',
				),
			)
		);
	}

	/**
	 * Saves the passed packsize.
	 * Is used for both ajax and regular POST updates.
	 *
	 * @param int $post_id
	 * @return void
	 */
	public function save_pack_size( int $post_id, int $loop = 0 ): void {

		// Grab the post body from the request.
		$post = $this->request->getParsedBody();

		if ( ! empty( $post['product-type'] ) && ! empty( $post[ $this->pack_size_key ] ) ) {
			// Based on product type.
			switch ( sanitize_text_field( $post['product-type'] ) ) {

				case 'simple':
					update_post_meta( $post_id, $this->pack_size_key, \intval( $post[ $this->pack_size_key ] ) );
					break;

				case 'variable':
					foreach ( $post['variable_post_id'] as $key => $variation_id ) {
						update_post_meta( $variation_id, $this->pack_size_key, \intval( $post[ $this->pack_size_key ][ $key ] ) );
					}
					break;

				default:
					break;
			}
		}
	}
}
