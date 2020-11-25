<?php

declare(strict_types=1);

/**
 * Handles all depenedency injection rules and config.
 *
 * @package PinkCrab\InventoryManagment
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @since 0.1.0
 */

use PC_Vendor\GuzzleHttp\Psr7\ServerRequest;
use PinkCrab\InventoryManagment\Application\Config;
use PC_Vendor\Psr\Http\Message\ServerRequestInterface;
use PinkCrab\InventoryManagment\MultiPack\Multipack_Edit_Controller;

return array(
	Config::class                    => array(
		'constructParams' => array( \wp_upload_dir() ),
		'shared'          => true,
	),
	Multipack_Edit_Controller::class => array(
		'substitutions' => array(
			ServerRequestInterface::class => ServerRequest::fromGlobals(),
		),
	),
);
