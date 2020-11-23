<?php
declare(strict_types=1);

/**
 * Used to bootload the application.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @since 1.0.0
 */

use PinkCrab\Core\App;
use PinkCrab\Core\Controller;
use PinkCrab\Core\Services\Dice\Dice;
use PinkCrab\Core\Services\Dice\WP_Dice;
use PinkCrab\Core\Services\Registration\Loader;
use PinkCrab\Core\Services\ServiceContainer\Container;
use PinkCrab\Core\Services\Registration\Register_Loader;

// App setup
$config = require( 'plugin-config.php' );
$loader = Loader::boot();

// Setup the service container .
$container = new Container();
$container->set( 'di', WP_Dice::constructWith( new Dice() ) );

// Boot the app.
$app = App::init( $container );

// Add all DI rules and register the actions from loader.
add_action(
	'init',
	function() use ( $loader, $config, $app ) {

		// Add all DI rules.
		$app->call( 'di' )->addRules(
			apply_filters( 'fc_core_di_wiring_rules', $config ['di_wiring'] )
		);

		// Initalise all registerable classes.
		Register_Loader::initalise(
			apply_filters( 'fc_core_registration_rules', $config ['registration'] ),
			$loader
		);

		// Register Loader hooks.
		$loader->register_hooks();
	},
	-1
);

// Cleanup
unset( $app );
unset( $container );
unset( $loader );
unset( $config );
