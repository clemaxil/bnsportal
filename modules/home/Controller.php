<?php

if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

use App\View;
use App\Controller;

include_once(__DIR__ . '/../../helpers/appHelperUrl.php');

/**
 * Class Homepage
 * @package Homepage
 */
class HomeController extends Controller
{
	/**
	 * Homepage  
	 * @return string
	 */
	public function index(): string
	{
		if (empty($_SESSION['user_id'])) {
			appHelperUrl_redirect($_REQUEST['lang'], 'auth', 'index');
			return ('user must be connected');
		}

		$view = new View();
		$view->setView(__DIR__ . '/templates/default.php');
		return $view->render([]);
	}
}
