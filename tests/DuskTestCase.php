<?php

namespace Tests;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase {
	use CreatesApplication;

	/**
	 * Prepare for Dusk test execution.
	 *
	 * @beforeClass
	 * @return void
	 */
	public static function prepare() {
		static::startChromeDriver();
	}

	/**
	 * Create the RemoteWebDriver instance.
	 *
	 * @return \Facebook\WebDriver\Remote\RemoteWebDriver
	 */
	protected function driver() {
		return RemoteWebDriver::create(
			'http://localhost:9515', DesiredCapabilities::chrome()
		);
	}

	public function setUp() {
		$database_root = dirname(dirname(__FILE__)) . '/database';
		copy("{$database_root}/empty_test_db.sqlite", "{$database_root}/test.sqlite");

		return parent::setUp();
	}
}
