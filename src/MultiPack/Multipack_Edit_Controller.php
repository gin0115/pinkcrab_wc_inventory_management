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

use PinkCrab\Core\Interfaces\Registerable;
use PinkCrab\Core\Services\Registration\Loader;

class Multipack_Edit_Controller implements Registerable {

	/**
	 * Registers all hook and filter call.
	 *
	 * @param Loader $loader
	 * @return void
	 */
	public function register( Loader $loader ): void {
		# code...
	}
}
