<?php

declare(strict_types=1);

/**
 * This bootstrap file is included into the __construct() method of the WC bootstrap file.
 *
 * Us this to add any plugins or make any changes to the test environment.
 */

use PinkCrab\InventoryManagment\Settings\WooCommece_Settings;


tests_add_filter(
	'muplugins_loaded',
	function() {
		require_once dirname( __DIR__, 1 ) . '/plugin.php';

		add_option( WooCommece_Settings::ALLOW_MULTIPACK_MODIFIER, 'yes' );
	}
);

// Helpers.
/**
 * str_contains pollyfill.
 */
if ( ! function_exists( 'str_contains' ) ) {
	function str_contains( $haystack, $needle ): bool {
		return strpos( $needle, $haystack ) !== false;
	}
}

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

/**
 * Get protected/private property from a class.
 *
 * @param object &$object    Instantiated object that we willget the propery
 * @param string $property Method name to call.
 *
 * @return mixed property value.
 */
function getPrivateProperty( &$object, string $property ) {
	$reflection    = new \ReflectionClass( get_class( $object ) );
	$propertyValue = $reflection->getProperty( $property );
	$propertyValue->setAccessible( true );
	return $propertyValue->getValue( $object );
}