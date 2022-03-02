<?php
define('APP_ENTRY', 'myApp');

use PHPUnit\Framework\TestCase;

include(__DIR__ . '/../modules/session/Controller.php');

class SessionTest extends TestCase
{
	/**
	 * 
	 * @return void 
	 */
	public function testSessionControllerIndexNotConnected()
	{
		unset($_SESSION['user_id']);
		$session = new SessionController();
		$this->assertMatchesRegularExpression('#user must be connected#', $session->index(), 'Session index user not connexted');
	}


	/**
	 * 
	 * @return void 
	 */
	public function testSessionControllerIndexIsGranted()
	{
		$_SESSION['user_id'] = 1;
		$_SESSION['user_roles'] = '["learner2"]';
		$session = new SessionController();
		$this->assertMatchesRegularExpression('#user must be granted#', $session->index(), 'Session index must be granted (learner, administrator)');
	}


}