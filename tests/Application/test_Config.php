<?php

use PinkCrab\Core\App;
use PinkCrab\Core\Services\Dice\WP_Dice;
use PinkCrab\InventoryManagment\Application\Config;

/**
 * Account function tests
 *
 * @package WooCommerce\Tests\Account
 */

/**
 * Class Functions.
 */
class Test_Application extends WC_Unit_Test_Case {

	protected $app;
	protected $config;

	public function setUp() {
		if ( ! $this->app ) {
			$this->app    = App::getInstance();
			$this->config = $this->app->get( 'config' );
		}
	}

	public function testCanInitialiseApp() {
		$this->assertInstanceOf( Config::class, $this->app->call( 'config' ) );
	}

	public function testCanGetRestNamespace() {
		$this->assertNotEmpty( $this->config->rest_namespace() );
	}

	public function testCanGetPaths() {
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
