<?php

use Perique\Loader\Loader;

class CoreLoaderTest extends WP_UnitTestCase {

	public $loader;

	/**
	 * Setup tests with an instance of the loader.
	 *
	 * @return void
	 */
	public function setUp() {
		$this->loader = Loader::boot();
	}

	/**
	 * Registers the hooks.
	 *
	 * @return void
	 */
	private function register_hooks() {
		$this->loader->register_hooks();
	}

	public function test_is_loader_loaded() {
		$this->assertInstanceOf(
			'Perique\Loader\Loader',
			Loader::boot()
		);
	}

	public function test_action_can_be_added_to_loader() {

		// Test Global Action
		$this->loader->action(
			'test_action',
			function() {
				return 3;
			}
		);

		$this->register_hooks();

		$this->assertTrue(
			has_action( 'test_action' )
		);

	}

	/**
	 * Test that ajax calls can be added to the loader.
	 *
	 * @return void
	 */
	public function test_ajax_calls_can_be_added_to_loader() {

		// Check defaults are used for both public & private
		$this->loader->ajax(
			'ajax_test_all',
			function() {
				return true;
			}
		);

		// Test only adding to public
		$this->loader->ajax(
			'ajax_test_public',
			function() {
				return true;
			},
			true,
			false
		);

		// Test only adding to private.
		$this->loader->ajax(
			'ajax_test_private',
			function() {
				return true;
			},
			false,
			true
		);

		// Register all hooks.
		$this->register_hooks();

		// Test the public & private.
		$this->assertTrue(
			has_action( 'wp_ajax_ajax_test_all' )
		);
		$this->assertTrue(
			has_action( 'wp_ajax_nopriv_ajax_test_all' )
		);

		// Test public
		$this->assertFalse(
			has_action( 'wp_ajax_ajax_test_public' )
		);
		$this->assertTrue(
			has_action( 'wp_ajax_nopriv_ajax_test_public' )
		);

		// Test private
		$this->assertTrue(
			has_action( 'wp_ajax_ajax_test_private' )
		);
		$this->assertFalse(
			has_action( 'wp_ajax_nopriv_ajax_test_private' )
		);
	}

	public function test_shortcodes_can_be_added() {
		$this->loader->shortcode(
			'testShortCode',
			function( $atts ) {
				echo $atts['text'];
			}
		);

		$this->loader->register_hooks();

		// Check the shortcode returns yes.
		ob_start();
		do_shortcode( "[testShortCode text='yes']" );
		$this->assertTrue(
			ob_get_contents() === 'yes'
		);
		ob_end_clean();
	}
}
