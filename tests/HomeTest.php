<?php
define('APP_ENTRY', 'myApp');

use PHPUnit\Framework\TestCase;

include(__DIR__ . '/../modules/home/Controller.php');

class HomeTest extends TestCase
{
	/**
	 * 
	 * @return void 
	 */
	public function testHomeUserNotConnected()
	{
		unset($_SESSION['user_id']);
		$home = new HomeController();
		$this->assertMatchesRegularExpression('#user must be connected#', $home->index(), 'Home index user not connexted');
	}

	/**
	 * 
	 * @return void 
	 */
	public function testHomeUserConnected()
	{
		$_SESSION['user_id'] = 1;
		$_REQUEST['lang'] = 'fr';
		$_REQUEST['module'] = 'home';
		$home = new HomeController();
		$this->assertMatchesRegularExpression('#<h1>Bienvenue sur votre portail formation</h1>#', $home->index(), 'Home index user id 1 home view');
	}
}
