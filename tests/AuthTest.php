<?php
define('APP_ENTRY', 'myApp');

use PHPUnit\Framework\TestCase;

include(__DIR__ . '/../modules/auth/Controller.php');

class AuthTest extends TestCase
{
	/**
	 * 
	 * @return void 
	 */
	public function testAuthControllerIndexIsLogged()
	{
		$_SESSION['user_id'] = 1;
		$_REQUEST['lang'] = 'fr';
		$auth = new AuthController();
		$this->assertMatchesRegularExpression('#user is already logged in#', $auth->index(), 'Auth index user is logged');
	}

	/**
	 * 
	 * @return void 
	 */
	public function testAuthControllerIndexLogForm()
	{
		unset($_SESSION['user_id']);
		$_REQUEST['lang'] = 'fr';
		$auth = new AuthController();
		$this->assertMatchesRegularExpression('#main class="form-signin#', $auth->index(), 'Auth index log form');
	}

	/**
	 * 
	 * @return void 
	 */
	public function testAuthControllerLogout()
	{
		$auth = new AuthController();
		$this->assertTrue($auth->logout());
	}
}
