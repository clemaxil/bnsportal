<?php
if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

use App\Model;
use App\View;
use App\Controller;

use App\Webservice;
require(__DIR__ . '/../../helpers/appHelperRole.php');

/**
 * Class Session for learner and administrator (RH)
 * @package MyCalendar
 */
class SessionController extends Controller
{
	/**
	 * 
	 * @var array
	 */
	protected $dataView;



	/**
	 * Session view with event
	 * @return string
	 */
	public function index(): string
	{
		global $app_config;

		if (empty($_SESSION['user_id'])) {
			appHelperUrl_redirect($_REQUEST['lang'], 'auth', 'index');
			return ('user must be connected');
		}

		if( !appHelperRole_isGranted('learner') && !appHelperRole_isGranted('administrator') ){
			appHelperUrl_redirect($_REQUEST['lang'], 'auth', 'index');
			return ('user must be granted');
		}
		else{
			
			$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServicePortalCapture";
			$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=getSessions";
			$webserviceUrl .= "&contact_id=" . $_SESSION['user_id_ext'];
			$webserviceUrl .= "&agence_id=" . $_SESSION['user_id_ext'];
			$webserviceUrl .= "&role=". $_SESSION['user_roles'];
			//Bug voir jerome
			//$webserviceObj = Webservice::curl($webserviceUrl);
			$webserviceObj = json_decode('[{"id":"5136e734-71f2-11ec-ac2b-0050569c3446","name":"Formation management - "}]');
			
			
			$view = new View();
			$view->setView(__DIR__ . '/templates/default.php');
			return $view->render([]);
		}

	}

}
