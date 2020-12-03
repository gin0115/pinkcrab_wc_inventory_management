<?php

declare(strict_types=1);
/**
 * Holds all constants and settings for the WC Settings.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\InventoryManagment
 */

namespace PinkCrab\InventoryManagment\Settings;

class WooCommece_Settings {

	/**
	 * Options key used to denote if multipack modifier is active.
	 */
	public const ALLOW_MULTIPACK_MODIFIER = 'pc_invman_mp_modifier_enabled';
	public const CART_MULTIPACK_SIZE_META = 'pc_invman_cart_item_pack_size_meta';

	public const TAB_LABEL = 'Inventory Management';
	public const TAB_KEY   = 'pc_invman';

	/**
	 * Returns the if MultiPack modifier is active.
	 *
	 * @return bool
	 */
	public static function allow_multipack(): bool {
		return get_option( self::ALLOW_MULTIPACK_MODIFIER ) === 'yes';
	}

	/**
	 * Returns the current cart item pack size template for meta.
	 *
	 * @return string
	 */
	public static function cart_item_pack_size_template() {
		return get_option( self::CART_MULTIPACK_SIZE_META ) ?: '';
	}


}
