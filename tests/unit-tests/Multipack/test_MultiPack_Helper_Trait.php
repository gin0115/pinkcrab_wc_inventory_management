<?php

/**
 * Tests for MultiPack_Helper_Trait
 *
 * @package PinkCrab/WooMan
 */

use PinkCrab\Core\App;
use Automattic\Jetpack\Constants;
use PinkCrab\InventoryManagment\MultiPack\MultiPack_Helper_Trait;

// Include helper trait
require_once 'Helper_Traits/MultiPack_Product_Helper.php';

/**
 * Tests the MultiPack_Helper_Trait
 *
 */
class Test_MultiPack_Helper_Trait extends WC_Unit_Test_Case {

	/**
	 * The apps container
	 *
	 * @var PinkCrab\Core\App
	 */
    protected $app;

    /**
	 * The test product (has 6 variations.)
	 *
	 * @var WC_Product
	 */
	protected $product_variable;

	/**
	 * General Product helper test trait.
	 * method WC_Product create_test_variable_product()
	 */
	use MultiPack_Product_Helper;

	/**
	 * The trait being tested!
	 * @method int MultiPack_Helper_Trait::product_packsize_modifer()
	 * @method bool MultiPack_Helper_Trait::managed_stock_product()
	 */
	use MultiPack_Helper_Trait;

    /**
	 * Sets the app container if its not already.
	 *
	 * @return void
	 */
	public function setUp() {
		if ( ! $this->app ) {
			$this->app              = App::getInstance();
			$this->product_variable = $this->create_test_variable_product();
		}
	}


	/**
	 * Tests that the pack modifier can be returns for a variation.
	 *
	 * Use both product_packsize_modifer() & packsize_modifier_from_id()
	 *
	 * @method int MultiPack_Helper_Trait::product_packsize_modifer()
	 * @return void
	 */
	public function test_can_return_the_pack_sze_modifier(): void {

		$expected = array( 1, 2, 3, 4, 5, 6 );

		foreach ( $this->product_variable->get_available_variations( 'object' )
			as $key => $variation
		) {
			// Using WC_Product object
			$this->assertEquals( $expected[ $key ], $this->product_packsize_modifer( $variation ) );

			// Using just product id.
			$this->assertEquals( $expected[ $key ], $this->packsize_modifier_from_id( $variation->get_id() ) );
		}
	}

	/**
	 * Test what we can determine if the product has managed stock
	 * Can be called as a simple, parent or child.
	 *
	 * @method bool MultiPack_Helper_Trait::managed_stock_product()
	 * @return void
	 */
	public function test_managed_stock_product(): void {
		$this->assertTrue( $this->managed_stock_product( $this->product_variable ) );
		$variations = $this->product_variable->get_available_variations( 'object' );
		$this->assertTrue( $this->managed_stock_product( $variations[0] ) );

	}

}

