<?php
define('APP_ENTRY', 'myApp');

use PHPUnit\Framework\TestCase;

include(__DIR__ . '/../modules/calendar/Controller.php');

class CalendarTest extends TestCase
{
	/**
	 * 
	 * @return void 
	 */
	public function testCalendarControllerIndexNotConnected()
	{
		unset($_SESSION['user_id']);
		$calendar = new CalendarController();
		$this->assertMatchesRegularExpression('#user must be connected#', $calendar->index(), 'Calendar index user not connexted');
	}

	/**
	 * 
	 * @return void 
	 */
	public function testCalendarControllerIndexConnected()
	{
		$_SESSION['user_id'] = 1;
		$calendar = new CalendarController();
		$this->assertMatchesRegularExpression('#<div id=\'calendar\'></div>#', $calendar->index(), 'Calendar index user connexted');
	}


	/**
	 * 
	 * @return void 
	 */
	public function testCalendarControllerSetfieldTypeTextarea()
	{
		$_SESSION['user_id'] = 1;
		$calendar = new CalendarController();
		$output = $calendar->setfieldType('testLabel', 'testName', 'text', '1', [], 'TestValue');
		$this->assertMatchesRegularExpression('#textarea#', $output, 'Calendar SetfieldType textarea');
	}

	/**
	 * 
	 * @return void 
	 */
	public function testCalendarControllerSetfieldTypeAddress()
	{
		$_SESSION['user_id'] = 1;
		$calendar = new CalendarController();
		$output = $calendar->setfieldType('testLabel', 'testName', 'address', '', [], 'TestValue');
		$this->assertMatchesRegularExpression('#input#', $output, 'Calendar SetfieldType address');
	}

	/**
	 * 
	 * @return void 
	 */
	public function testCalendarControllerSetfieldTypeDecimal()
	{
		$_SESSION['user_id'] = 1;
		$calendar = new CalendarController();
		$output = $calendar->setfieldType('testLabel', 'testName', 'decimal', '', [], 'TestValue');
		$this->assertMatchesRegularExpression('#input#', $output, 'Calendar SetfieldType decimal');
	}

	/**
	 * 
	 * @return void 
	 */
	public function testCalendarControllerSetfieldTypeInt()
	{
		$_SESSION['user_id'] = 1;
		$calendar = new CalendarController();
		$output = $calendar->setfieldType('testLabel', 'testName', 'int', '', [], 'TestValue');
		$this->assertMatchesRegularExpression('#input#', $output, 'Calendar SetfieldType int');
	}

	/**
	 * 
	 * @return void 
	 */
	public function testCalendarControllerSetfieldTypeVarchar()
	{
		$_SESSION['user_id'] = 1;
		$calendar = new CalendarController();
		$output = $calendar->setfieldType('testLabel', 'testName', 'varchar', '', [], 'TestValue');
		$this->assertMatchesRegularExpression('#input#', $output, 'Calendar SetfieldType varchar');
	}

	/**
	 * 
	 * @return void 
	 */
	public function testCalendarControllerSetfieldTypeFloat()
	{
		$_SESSION['user_id'] = 1;
		$calendar = new CalendarController();
		$output = $calendar->setfieldType('testLabel', 'testName', 'float', '', [], 'TestValue');
		$this->assertMatchesRegularExpression('#input#', $output, 'Calendar SetfieldType float');
	}

	/**
	 * 
	 * @return void 
	 */
	public function testCalendarControllerSetfieldTypePhone()
	{
		$_SESSION['user_id'] = 1;
		$calendar = new CalendarController();
		$output = $calendar->setfieldType('testLabel', 'testName', 'phone', '', [], 'TestValue');
		$this->assertMatchesRegularExpression('#input#', $output, 'Calendar SetfieldType phone');
	}

	/**
	 * 
	 * @return void 
	 */
	public function testCalendarControllerSetfieldTypeBool()
	{
		$_SESSION['user_id'] = 1;
		$calendar = new CalendarController();
		$output = $calendar->setfieldType('testLabel', 'testName', 'bool', '', [], '1');
		$this->assertMatchesRegularExpression('#input#', $output, 'Calendar SetfieldType bool');
	}

	/**
	 * 
	 * @return void 
	 */
	public function testCalendarControllerSetfieldTypeDefault()
	{
		$_SESSION['user_id'] = 1;
		$calendar = new CalendarController();
		$output = $calendar->setfieldType('testLabel', 'testName', '', '', [], '1');
		$this->assertMatchesRegularExpression('#::#', $output, 'Calendar SetfieldType default');
	}


	/**
	 * 
	 * @return void 
	 */
	public function testCalendarControllerSetfieldTypeEnum()
	{
		$_SESSION['user_id'] = 1;
		$calendar = new CalendarController();
		$output = $calendar->setfieldType('testLabel', 'testName', 'enum', '', [['name' => 'full_name']], 'name');
		$this->assertMatchesRegularExpression('#selected#', $output, 'Calendar SetfieldType enum');
	}


	/**
	 * 
	 * @return void 
	 */
	public function testCalendarControllerSetfieldTypeRadioEnum()
	{
		$_SESSION['user_id'] = 1;
		$calendar = new CalendarController();
		$output = $calendar->setfieldType('testLabel', 'testName', 'radioenum', '', [['name' => 'full_name']], 'name');
		$this->assertMatchesRegularExpression('#checked#', $output, 'Calendar SetfieldType radio enum');
	}
}
