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

	/**
	 * Returns the if MultiPack modifier is active.
	 *
	 * @return bool
	 */
	public static function allow_multipack(): bool {
		return get_option( self::ALLOW_MULTIPACK_MODIFIER ) === 'yes';
	}


}
