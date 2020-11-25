<?php
/**
 * @wordpress-plugin
 * Plugin Name:     PinkCrab Inventory Managment
 * Plugin URI:      https://www.clappo.co.uk
 * Description:     PinkCrab Inventory Managment. Makes stock checks, order picking and goods in a more enjoyable experience.
 * Version:         0.1.0
 * Author:          Glynn Quelch
 * Author URI:      https://www.clappo.co.uk
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     pc_invman
 */

use PinkCrab\Core\App;
use PinkCrab\InventoryManagment\WP\Activation;
use PinkCrab\InventoryManagment\WP\Deactivation;

if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap.php';

register_activation_hook( __FILE__, array( App::make( Activation::class ), 'activate' ) );
register_deactivation_hook( __FILE__, array( App::make( Deactivation::class ), 'deactivate' ) );
