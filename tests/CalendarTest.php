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
	public function testCalendarControllerFileExist()
	{
		$this->assertFileExists('modules/calendar/Controller.php', 'Calendar Controller File exist ?');
	}
}
