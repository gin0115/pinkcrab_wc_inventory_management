<?php

/**
 * Tests for WooCommerce_Settings_Controller
 *
 * @package PinkCrab/WooMan
 */

use PinkCrab\Core\App;
use PinkCrab\InventoryManagment\Settings\WooCommece_Settings;
use PinkCrab\InventoryManagment\Settings\WooCommerce_Settings_Controller;

class test_WooCommerce_Settings_Controller extends WC_Unit_Test_Case {

	/**
	 * The apps container
	 *
	 * @var PinkCrab\Core\App
	 */
	protected $app;

	/**
	 * Sets the app container if its not already.
	 *
	 * @return void
	 */
	public function setUp(): void {
		if ( ! $this->app ) {
			$this->app = App::getInstance();
		}
	}

	/**
	 * Tests the settings tab is registered.
	 *
	 * @action woocommerce_settings_tabs_array
	 * @method void WooCommerce_Settings_Controller::output()
	 * @return void
	 */
	public function test_tab_added(): void {
		$tabs = apply_filters( 'woocommerce_settings_tabs_array', array() );
		$this->assertArrayHasKey( WooCommece_Settings::TAB_KEY, $tabs );
		$this->assertContains( WooCommece_Settings::TAB_LABEL, $tabs );
	}

	/**
	 * Tests the controller renders the fields.
	 *
	 * @action woocommerce_settings_{tab_key}
	 * @method void WooCommerce_Settings_Controller::output()
	 * @return void
	 */
	public function test_can_output_fields(): void {

		// Capture output.
		ob_start();
			$this->app::make( WooCommerce_Settings_Controller::class )->output();
		$output = ob_get_contents();
		ob_end_clean();

		// Check fields are defined.
		$this->assertStringContainsString(
			sprintf( 'id="%s"', WooCommece_Settings::ALLOW_MULTIPACK_MODIFIER ),
			$output
		);
	}

	/**
	 * Tests the controllers save hook, callback is firing.
	 *
	 * @action woocommerce_settings_save_{tab_key}
	 * @method void WooCommerce_Settings_Controller::save()
	 * @return void
	 */
	public function test_can_save_settings_fields(): void {

		// Disable multipack, trigger test.
		$_POST[ WooCommece_Settings::ALLOW_MULTIPACK_MODIFIER ] = 'no';
		$this->app::make( WooCommerce_Settings_Controller::class )->save();
		$this->assertEquals( 'no', get_option( WooCommece_Settings::ALLOW_MULTIPACK_MODIFIER ) );

		// Revert.
		$_POST[ WooCommece_Settings::ALLOW_MULTIPACK_MODIFIER ] = 'yes';
		$this->app::make( WooCommerce_Settings_Controller::class )->save();
		$this->assertEquals( 'yes', get_option( WooCommece_Settings::ALLOW_MULTIPACK_MODIFIER ) );

		// House keeping.
		unset( $_POST[ WooCommece_Settings::ALLOW_MULTIPACK_MODIFIER ] );
	}
}


