<?php

use Plugin\Models\Test;

class CoreModelTest extends WP_UnitTestCase {

	protected $testModel;

	public function setUp() {
		$this->testModel = new Test();
	}

	/**
	 * Check you can call an instance of the test model.
	 *
	 * @return void
	 */
	public function test_can_create_test_model() {
		$this->assertInstanceOf(
			'Plugin\Models\Test',
			$this->testModel
		);
	}

	/**
	 * Check that the properties are set based on schema.
	 *
	 * @return void
	 */
	public function test_the_properties_are_defined() {
		$this->assertTrue( property_exists( $this->testModel, 'id' ) );
		$this->assertTrue( property_exists( $this->testModel, 'int' ) );
		$this->assertTrue( property_exists( $this->testModel, 'text' ) );
		$this->assertTrue( property_exists( $this->testModel, 'int' ) );
	}

	/**
	 * Test the model can be outputted as an array.
	 *
	 * @return void
	 */
	public function test_can_output_to_array() {

		$this->setTestProperties();

		$array = $this->testModel->toArray();

		$this->assertTrue( is_array( $this->testModel->toArray() ) );
		// Check the values
		$this->assertTrue( $array['id'] === 2 );
		$this->assertTrue( $array['int'] === 12 );
		$this->assertTrue( $array['text'] === 'foo' );
		$this->assertTrue( $array['float'] === 2.2 );

		$this->resetTestProperties();
	}

	/**
	 * Test that we can output a models values as a JSON.
	 *
	 * @return void
	 */
	public function test_can_output_as_JSON() {
		$this->setTestProperties();
		$this->assertTrue( $this->testModel->toJSON() === '{"id":2,"int":12,"text":"foo","float":2.2}' );
		$this->resetTestProperties();
	}

	/**
	 * Test the model can be outputted an as object.
	 *
	 * @return void
	 */
	public function test_can_output_as_object() {

		$this->setTestProperties();
		$obj = $this->testModel->toObject();

		$this->assertTrue( is_object( $obj ) );
		$this->assertTrue( $obj->id === 2 );
		$this->assertTrue( $obj->int === 12 );
		$this->assertTrue( $obj->text === 'foo' );
		$this->assertTrue( $obj->float === 2.2 );

		$this->resetTestProperties();
	}

	/**
	 * Sets some default values.
	 *
	 * @return void
	 */
	private function setTestProperties() {
		$this->testModel->id    = 2;
		$this->testModel->int   = 12;
		$this->testModel->text  = 'foo';
		$this->testModel->float = 2.2;
	}

	/**
	 * Restet the values to null.
	 *
	 * @return void
	 */
	private function resetTestProperties() {
		$this->testModel->id    = null;
		$this->testModel->int   = null;
		$this->testModel->text  = null;
		$this->testModel->float = null;
	}
}
