<?php

use Perique\View\View;
use PHPUnit\Framework\Error\Notice;

class CoreViewTest extends WP_UnitTestCase {

	public $template;

	/**
	 * Define the mock template on init.
	 *
	 * @return void
	 */
	public function setUp() {
		$this->template = WP_PHPUNIT__MOCK_DIR . '/View/test.potato';
	}

	/**
	 * Check that a tempalte can be rendered to HTML.
	 *
	 * @return void
	 */
	public function test_can_render_values_and_variable_in_template() {

		$html = View::template( $this->template )
		->data( 'testVal', 'foo' )
		->data(
			'testFunc',
			function( $x ) {
				return $x / 2;
			}
		)
		->html();

		$this->assertTrue( $html === 'foo-1' );
	}

	/**
	 * Test to ensure a missing file throws an error.
	 *
	 * @return void
	 */
	public function test_check_template_not_found_throws_error() {

		try {
			$html = View::template( 'nofilehere' )
			->data( 'testVal', 'foo' )
			->data(
				'testFunc',
				function( $x ) {
					return $x / 2;
				}
			)
			  ->html();
		} catch ( \Throwable $th ) {
			$this->assertTrue( $th->getMessage() === 'Template not found' );
		}
	}
}
