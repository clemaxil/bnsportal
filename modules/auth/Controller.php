<?php
if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

use App\Controller;
use App\View;
use App\Model;
use Firebase\JWT\JWT;

include_once(__DIR__ . '/../../helpers/appHelperUrl.php');
include_once(__DIR__ . '/../../helpers/appHelperMail.php');

/**
 * Class Authentificate
 * @package Authentificate
 */
class AuthController extends Controller
{
	/**
	 * 
	 * @var array
	 */
	private $dataView;

	/**
	 * 
	 * @var string
	 */
	private $jwtSecret;

	public function __construct()
	{
		$this->jwtSecret = 'Blue*67_noTe2206';
	}


	/**
	 * 
	 * @return string 
	 * @throws PDOException 
	 */
	public function index()
	{
		if (!empty($_SESSION['user_id'])) {
			appHelperUrl_redirect($_REQUEST['lang'], 'home', 'index');
			return ('user is already logged in');
		} else {

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if ($this->login()) {
					appHelperUrl_redirect($_REQUEST['lang'], 'home', 'index');
				}
			}

			$view = new View();
			$view->setView(__DIR__ . '/templates/default.php');
			return $view->render([]);
		}
	}



	/**
	 * 
	 * @return string|void 
	 * @throws PDOException 
	 * @throws DomainException 
	 */
	public function passwordForgot()
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->passwordInitSendByEmail($_POST['email']);
			appHelperUrl_redirect($_REQUEST['lang'], 'auth', 'index');
		}

		$view = new View();
		$view->setView(__DIR__ . '/templates/passwordForgot.php');
		return $view->render([]);
	}


	/**
	 * 
	 * @return string 
	 * @throws PDOException 
	 */
	public function passwordNew()
	{
		$jwtdecoded_array = array();
		$jwtSecretKey = $this->jwtSecret;
		$this->dataView['newPassword'] = 'error';
		$jwtError = $jwtErrorMsg = '';

		try {
			$jwtDecoded = JWT::decode($_GET['jwt'], $jwtSecretKey, array('HS256'));
			$jwtdecoded_array = (array) $jwtDecoded;
		} catch (Exception $e) {
			$jwtError = 'error jwt';
			$jwtErrorMsg = "Error : " . $e->getMessage();
		}

		if ($jwtError == '') {
			if ($jwtdecoded_array['ip'] == $_SERVER['REMOTE_ADDR']) {
				$newPassword = $this->generatePassword(12, 'luds');
				$newPasswordHash = (string) password_hash($newPassword, PASSWORD_DEFAULT);
				$db = Model::getInstance();
				$db->update('contacts', $jwtdecoded_array['id'], array('password' => $newPasswordHash));
				$this->dataView['newPassword'] = $newPassword;
			} else {
				$this->dataView['jwtErrorMsg'] = "Error : JWT bad IP";
			}
		} else {
			$this->dataView['jwtErrorMsg'] = $jwtErrorMsg;
		}

		$view = new View();
		$view->setView(__DIR__ . '/templates/passwordNew.php');
		return $view->render($this->dataView);
	}



	/**
	 * 
	 * @return bool 
	 * @throws PDOException 
	 */
	public function login()
	{
		$email = $this->cleanInputString($_POST['email']);
		$password = $this->cleanInputString($_POST['password']);

		if (!empty($email) && !empty($password)) {
			$db = Model::getInstance();
			$result = $db->select("contacts", array('email' => $email));
			if ($result != false) {
				if (count($result) == 1) {
					if (password_verify($password, $result[0]['password'])) {
						foreach ($result[0] as $key => $val) {
							if ($key != "password") {
								$_SESSION['user_' . $key] = $val;
							}
						}
						$_SESSION['auth_verify'] = 1;
						return true;
					}
				}
			}
		}
		$_SESSION['auth_verify'] = 0;
		return false;
	}



	/**
	 * function logout, session destroy
	 *
	 * @return bool
	 */
	public function logout()
	{
		session_destroy();
		appHelperUrl_redirect($_REQUEST['lang'], 'home', 'index');
		return true;
	}


	/**
	 * function security clean input var
	 *
	 * @param string $email
	 * @return boolean
	 */
	public function passwordInitSendByEmail(string $email)
	{

		$view = new View();

		$db = Model::getInstance();
		$result = $db->select("contacts", array('email' => $this->cleanInputString($email)), 'id', 1);

		if ($result != false) {
			if (count($result) == 1) {
				if (!empty($result[0]['id'])) { //send email
					//JWT
					$iat = time();
					$exp = $iat + (2 * 60 * 60); //+2 hour
					$key = $this->jwtSecret;
					$payload = array(
						"sub" => "password-new",
						"id" => $result[0]['id'],
						"email" => $result[0]['email'],
						"ip" => $_SERVER['REMOTE_ADDR'],
						"iat" => $iat,
						"exp" => $exp
					);
					$jwt = JWT::encode($payload, $key);

					$this->dataView['link'] = appHelperUrl_href(
						$_REQUEST['lang'],
						'auth',
						'password-new',
						$result[0]['id'],
						'jwt=' . $jwt
					);

					$view->setView(__DIR__ . '/templates/passwordEmailSubject.php');
					$subject = $view->render($this->dataView);
					$view->setView(__DIR__ . '/templates/passwordEmailHtml.php');
					$body = $view->render($this->dataView);
					$view->setView(__DIR__ . '/templates/passwordEmailTxt.php');
					$altBody = $view->render($this->dataView);

					$resultMailer = appHelperMail_send(
						$_POST['email'],
						$result[0]['first_name'] . ' ' . $result[0]['last_name'],
						$subject,
						$body,
						$altBody
					);

					if ($resultMailer == 'OK') {
						$view->setFlash('password_new_send', 'success', true);
					} elseif ($resultMailer != 'OK') {
						$view->setFlash($resultMailer, 'warning');
					}
				}
			}
		}
		return true;
	}
}
