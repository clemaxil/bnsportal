<?php
define('APP_ENTRY', 'myApp');

use PHPUnit\Framework\TestCase;

include(__DIR__ . '/../modules/profil/Controller.php');

class ProfilTest extends TestCase
{

	/**
	 * 
	 * @return void 
	 */
	public function testProfilUserNotConnected()
	{
		$_SESSION['user_id'] = '';
		$profil = new ProfilController();
		$this->assertMatchesRegularExpression('#user must be connected#', $profil->index(), 'Profil index user not connexted');
	}

	/**
	 * 
	 * @return void 
	 */
	public function testProfilUserConnected()
	{
		$_SESSION['user_id'] = 1;
		$_SESSION['user_email'] = 'test@test.com';
		$_REQUEST['module'] = 'profil';
		$profil = new ProfilController();
		$this->assertMatchesRegularExpression('#test@test.com#', $profil->index(), 'Profil index user id 1 profil view');
	}
}
