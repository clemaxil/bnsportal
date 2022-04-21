<?php

if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

use App\Model;
use App\View;
use App\Controller;

use App\Webservice;
require(__DIR__ . '/../../helpers/appHelperRole.php');

include_once(__DIR__ . '/../../helpers/appHelperUrl.php');

/**
 * Class Homepage
 * @package Homepage
 */
class HomeController extends Controller
{
	/**
	 * 
	 * @var array
	 */
	private $dataView;
	
	
	/**
	 * Homepage  
	 * @return string
	 */
	public function index(): string
	{
		global $app_config;
		$this->dataView['error'] = 0;

		if (empty($_SESSION['user_id'])) {
			appHelperUrl_redirect($_REQUEST['lang'], 'auth', 'index');
			return ('user must be connected');
		}


		$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServicePortalCapture";
		$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=getSessionsCount";
		$webserviceUrl .= "&contact_id=" . $_SESSION['user_id_ext'];
		$webserviceUrl .= "&agence_id=" . $app_config['sugar_bns_company_code'];
		$webserviceUrl .= "&role=".json_decode($_SESSION['user_roles'])[0];
		$webserviceObj = Webservice::curl($webserviceUrl);
		if (!empty($webserviceObj)) {
			$this->dataView['sessions'] = $webserviceObj;
		} else {
			$this->dataView['error'] = 1;
			$this->dataView['error-message'] = "Get webservice not found.";
		}

		$view = new View();
		$view->setView(__DIR__ . '/templates/default.php');
		return $view->render($this->dataView);
	}
}
