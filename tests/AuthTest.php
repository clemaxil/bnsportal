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
	public function testAuthControllerIndexLoginForm()
	{
		unset($_SESSION['user_id']);
		$_REQUEST['lang'] = 'fr';
		$auth = new AuthController();
		$this->assertMatchesRegularExpression('#main class="form-signin#', $auth->index(), 'Auth index login form');
	}

	/**
	 * 
	 * @return void 
	 */
	// public function testAuthControllerIndexPost()
	// {
	// 	unset($_SESSION['user_id']);
	// 	$_REQUEST['lang'] = 'fr';
	// 	$_SERVER['REQUEST_METHOD'] = 'POST';
	// 	$_POST['email'] = 'test@test.com';
	// 	$_POST['password'] = '1234';
	// 	$auth = new AuthController();
	// 	$this->assertMatchesRegularExpression('#login form is posted#', $auth->index(), 'Auth index post form');
	// }


	/**
	 * 
	 * @return void 
	 */
	public function testAuthControllerLoginFailed()
	{
		$_POST['email'] = 'test@test.com';
		$_POST['password'] = '1234';
		$auth = new AuthController();
		$this->assertFalse($auth->login());
	}


	/**
	 * 
	 * @return void 
	 */
	public function testAuthControllerPasswordForgot()
	{
		$auth = new AuthController();
		$this->assertMatchesRegularExpression('#password-forgot#', $auth->passwordForgot(), 'Auth password forgot');
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
