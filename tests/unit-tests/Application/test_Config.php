<?php

use PinkCrab\Core\App;
use PinkCrab\Core\Services\Dice\WP_Dice;
use PinkCrab\InventoryManagment\Application\Config;

/**
 * Account function tests
 *
 * @package WooCommerce\Tests\Account
 */
class Test_Application extends WC_Unit_Test_Case {

	/**
	 * The apps container
	 *
	 * @var PinkCrab\Core\App
	 */
	protected $app;

	/**
	 * The app config object.
	 *
	 * @var PinkCrab\InventoryManagment\Application\Config
	 */
	protected $config;

	/**
	 * Sets the app container if its not already.
	 *
	 * @return void
	 */
	public function setUp(): void {
		if ( ! $this->app ) {
			$this->app    = App::getInstance();
			$this->config = $this->app->get( 'config' );
		}
	}

	/**
	 * Test that the app can be initalised.
	 *
	 * @return void
	 */
	public function test_can_get_app_instance(): void {
		$this->assertInstanceOf( Config::class, $this->app->call( 'config' ) );
	}

	/**
	 * Test the rest namespace is reutrned.
	 *
	 * @return void
	 */
	public function test_can_get_rest_namespace(): void {
		$this->assertNotEmpty( $this->config->rest_namespace() );
	}

	/**
	 * Tests that all paths can be retrieved.
	 *
	 * @return void
	 */
	public function test_can_get_paths() {
		$this->assertContains( 'woomandev', $this->config->path( 'plugin_path' ) );
		$this->assertContains( 'woomandev/views', $this->config->path( 'view_path' ) );
		$this->assertContains( 'woomandev/assets', $this->config->path( 'assets_path' ) );
		$this->assertContains( 'woomandev/assets', $this->config->path( 'assets_url' ) );
		$this->assertContains( 'uploads', $this->config->path( 'path' ) );
		$this->assertContains( 'uploads', $this->config->path( 'url' ) );
		$this->assertContains( 'uploads', $this->config->path( 'baseurl' ) );
		$this->assertContains( 'uploads', $this->config->path( 'baseurl' ) );
	}
}
