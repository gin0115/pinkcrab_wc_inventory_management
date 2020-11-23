<?php

use Perique\Utility\Config;

class CoreConfigTest extends WP_UnitTestCase {

	// Check the class exists and a static instance can be called.
	public function test_config_exists() {
		$this->assertInstanceOf(
			'Perique\Utility\Config',
			Config::boot( WP_PHPUNIT__PLUGIN_DIR . '/plugin-config.php' )
		);
	}

	public function test_can_get_first_tier_value() {
		$this->assertInternalType(
			'string',
			Config::get( 'database_driver' )
        );
        $this->assertInternalType(
			'array',
			Config::get( 'paths' )
		);
    }
    
    public function test_can_get_second_tier_value()
    {
        $this->assertInternalType(
			'string',
			Config::get( 'paths', 'base_path' )
        );
    }
}
