<?php

use PinkCrab\Core\App;
use PinkCrab\Core\Services\Dice\WP_Dice;
use PinkCrab\InventoryManagment\Application\Config;

/**
 * Tests for Multipack_Product_Controller
 *
 * @package PinkCrab/WooMan
 */

/**
 * Class Functions.
 */
class Test_Multipack_Product_Controller extends WC_Unit_Test_Case {

    protected $app;
	protected $config;

	public function setUp() {
		if ( ! $this->app ) {
			$this->app    = App::getInstance();
			$this->config = $this->app->get( 'config' );
		}
	}

    public function testCanGetModifiedStock()
    {
        $product = \WC_Helper_Product::create_variation_product();
        dump($product);
    }
}