<?php

use PinkCrab\Core\App;

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

# if this file is called directly, abort
if (!defined('WPINC')) {
	die;
}



/**
 * Absolute path for plugin.
 */
define('PC_POS_PLUGIN_DIR', untrailingslashit(dirname(__FILE__)));

/**
 * Sets the key used for the transient cache.
 */
if (!defined('PC_TRANSIENT_CACHE_KEY')) {
	define('PC_TRANSIENT_CACHE_KEY', 'FgPosSync');
}

/**
 * Set the base uploads dir as a constant.
 */
if (!defined('PC_UPLOAD_BASE_PATH')) {
	$upload = wp_upload_dir();
	define('PC_UPLOAD_BASE_PATH', $upload['basedir']);
}

/**
 * Defines the base rest namespace.
 */
if (!defined('FgPosSync_REST_NAMESPACE')) {
	define('FgPosSync_REST_NAMESPACE', 'FgPosSync/v1');
}

/**
 * Defines the base rest namespace.
 */
if (!defined('FgPosSync_REST_KEY')) {
	define('FgPosSync_REST_KEY', 'Cg67SdY$h$0Ke8P%!0KMbfMDZb$3*Rna*KXqA7$loc13f3ystrhLSLd$#E^B&e$L');
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap.php';

add_action('init', function () {
	
	add_filter('jhjkhkj', function ($e) {
		dump($e);
	});
	
	dump(App::config()->path('assets_url'));
});


