<?php

declare(strict_types=1);

/**
 * Holds all classes which are to be loaded on initalisation.
 *
 * @package PinkCrab\InventoryManagment
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @since 0.1.0
 */

use PinkCrab\InventoryManagment\Settings\WooCommerce_Settings_Controller;


return array(
	WooCommerce_Settings_Controller::class,
);
