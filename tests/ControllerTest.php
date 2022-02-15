<?php
define('APP_ENTRY', 'myApp');

use App\Controller;
use PHPUnit\Framework\TestCase;


class ControllerTest extends TestCase
{
	/**
	 * 
	 * @return void 
	 */
	public function testControllerMinimumPhpVersion()
	{
		$mainController = new Controller();
		$this->assertMatchesRegularExpression('#CORE OK#', $mainController->phpVersionCheck(), 'Main Controller Core version check');
	}

	/**
	 * 
	 * @return void 
	 */
	public function testControllerMinimumPhpVersionFalse()
	{
		$mainController = new Controller();
		$this->assertMatchesRegularExpression('#CORE ERROR: php version must be higher than 7.3#', $mainController->phpVersionCheck('72'), 'Main Controller Core version false check');
	}

	/**
	 * 
	 * @return void 
	 */
	public function testControllerCleanInputString()
	{
		$mainController = new Controller();
		$this->assertEquals('myvalue', $mainController->cleanInputString("<script>myvalue</script>"), 'Main Controller cleanInputString check');
	}

	/**
	 * 
	 * @return void 
	 */
	public function testControllerLoadHelpers()
	{
		$mainController = new Controller();
		$this->assertTrue($mainController->loadHelpers(), 'Main Controller load helpers check');
	}

	/**
	 * 
	 * @return void 
	 */
	public function testControllerGeneratePassword()
	{
		$mainController = new Controller();
		$this->assertEquals(12, strlen($mainController->generatePassword(12, 'luds')), 'Main Controller generate password');
	}
}
