<?php

declare(strict_types=1);
/**
 * Actiation hook event.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\InventoryManagment
 */

namespace PinkCrab\InventoryManagment\WP;

use PinkCrab\Core\App;
use PinkCrab\InventoryManagment\WP\Uninstalled;

class Activation {

	public function activate() {
		// Register unistall hook.
		register_uninstall_hook( __FILE__, array( App::make( Uninstalled::class ), 'uninstall' ) );
	}
}
