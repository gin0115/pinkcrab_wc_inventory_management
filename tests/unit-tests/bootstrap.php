<?php
/**
 * PHPUnit bootstrap file
 */

// Composer autoloader must be loaded before WP_PHPUNIT__DIR will be available
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Give access to tests_add_filter() function.
require_once getenv( 'WP_PHPUNIT__DIR' ) . '/includes/functions.php';

tests_add_filter(
	'muplugins_loaded',
	function() {
		// test set up, plugin activation, etc.
	}
);

// Start up the WP testing environment.
require getenv( 'WP_PHPUNIT__DIR' ) . '/includes/bootstrap.php';

/**
 * Call protected/private method of a class.
 *
 * @param object &$object    Instantiated object that we will run method on.
 * @param string $method_name Method name to call.
 * @param array  $parameters Array of parameters to pass into method.
 *
 * @return mixed Method return.
 */
function invokePrivateMethod( &$object, $method_name, array $parameters = array() ) {
	$reflection = new \ReflectionClass( get_class( $object ) );
	$method     = $reflection->getMethod( $method_name );
	$method->setAccessible( true );

	return $method->invokeArgs( $object, $parameters );
}

if ( ! function_exists( 'array_key_last' ) ) {
	function array_key_last( $array ) {
		end( $array );
		return key( $array );
	}
}

define( 'WP_PHPUNIT__PLUGIN_DIR', str_replace( '/WPUnit', '/plugin.php', getcwd() ) );
define( 'WP_PHPUNIT__CORE_DIR', str_replace( '/src/Core', '/plugin.php', getcwd() ) );
define( 'WP_PHPUNIT__MOCK_DIR', getcwd() . '/mock' );

// Load the plugin.
require_once WP_PHPUNIT__PLUGIN_DIR;
