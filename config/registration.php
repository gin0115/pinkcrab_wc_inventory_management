<?php

declare(strict_types=1);

/**
 * Holds all classes which are to be loaded on initalisation.
 *
 * @package PinkCrab\InventoryManagment
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @since 0.1.0
 */

use PinkCrab\InventoryManagment\MultiPack\Multipack_Cart_Controller;
use PinkCrab\InventoryManagment\MultiPack\Multipack_Edit_Controller;
use PinkCrab\InventoryManagment\MultiPack\Multipack_Order_Controller;
use PinkCrab\InventoryManagment\MultiPack\Multipack_Product_Controller;
use PinkCrab\InventoryManagment\Settings\WooCommerce_Settings_Controller;
use PinkCrab\InventoryManagment\MultiPack\Multipack_Manual_Order_Controller;

return array(

	// General.
	WooCommerce_Settings_Controller::class,

	// Multipack.
	Multipack_Edit_Controller::class,
	Multipack_Product_Controller::class,
	Multipack_Cart_Controller::class,
	Multipack_Order_Controller::class,
	Multipack_Manual_Order_Controller::class,
);
