<?php

use Perique\ServiceWorker\ServiceWorker;

class CoreServiceWorker extends WP_UnitTestCase {

    public function test_ServiceWorker_exists() {
		$this->assertInstanceOf(
			'Perique\ServiceWorker\ServiceWorker',
			ServiceWorker::_boot()
		);
    }
    
    public function test_can_create_new_worker() {
        ServiceWorker::_register('test', ['test']);
		$this->assertInternalType(
			'array',
			ServiceWorker::test()
		);
	}
}