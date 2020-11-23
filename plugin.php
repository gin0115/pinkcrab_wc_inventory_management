<?php

/**
 * @wordpress-plugin
 * Plugin Name:     FranceGallery Core
 * Plugin URI:      https://www.clappo.co.uk
 * Description:     Allows for easier management of stock for WooCommerce stores.
 * Version:         0.1.0
 * Author:          Glynn Quelch
 * Author URI:      https://www.clappo.co.uk
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     gqstock
 */

# if this file is called directly, abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

use PinkCrab\FGCore\App;
use PinkCrab\FGCore\Test2;
use PinkCrab\FGCore\Services\Dice\Dice;
use PinkCrab\FGCore\Services\Dice\WP_Dice;
use PinkCrab\FGCore\ServiceContainer\Container;
use PinkCrab\FGCore\Services\Registration\Loader;
use PinkCrab\FGCore\Services\Registration\Register_Loader;

require_once __DIR__ . '/vendor/autoload.php';

// App setup
$config = require( 'plugin-config.php' );
$loader = Loader::boot();

// Setup the service container .
$container = new Container();
$container->set( 'di', WP_Dice::constructWith( new Dice() ) );

// Boot the app.
$app = App::init( $container );

// Initalise all registerable classes.
Register_Loader::initalise(
	apply_filters( 'fc_core_registration', $config ['registration'] ),
	$loader
);

// Register all from loader.
add_action(
	'init',
	function() use ( $loader, $config, $app ) {
		// Add all DI rules.
		$app->call( 'di' )->addRules( $config['di_wiring'] );
		// Register Loader hooks.
		$loader->register_hooks();

		var_dump( $app::make( Test2::class ) );
	},
	-1
);

// Cleanup
unset( $app );
unset( $container );
unset( $loader );
unset( $config );
