<?php

declare(strict_types=1);

/**
 * Handles all depenedency injection rules and config.
 *
 * @package PinkCrab\InventoryManagment
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @since 0.1.0
 */

use PinkCrab\InventoryManagment\Application\Config;

return array(
	Config::class => array(
		'constructParams' => array( \wp_upload_dir() ),
		'shared'          => true,
	),
);
