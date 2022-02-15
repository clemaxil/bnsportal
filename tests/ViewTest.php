<?php
define('APP_ENTRY', 'myApp');

use App\View;
use PHPUnit\Framework\TestCase;


class ViewTest extends TestCase
{

	/**
	 * 
	 * @return void 
	 */
	public function testViewFlash()
	{
		$_REQUEST['module'] = 'auth';
		$_REQUEST['lang'] = 'fr';
		$mainView = new View();
		$mainView->setFlash('title', 'warning', true);
		$this->assertEquals('Authentification', $_SESSION['flash']['message'], 'Main View setFlash message check');
		$this->assertEquals('warning', $_SESSION['flash']['type'], 'Main View setFlash type check');
	}
}
