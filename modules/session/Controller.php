<?php

declare(strict_types=1);

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
	private $dataView;


	use \App\traits\DetailTrait;
	use \App\traits\DateTrait;
	use \App\traits\RegistrationTrait;
	use \App\traits\DownloadTrait;
	use \App\traits\DocumentTrait;



	/**
	 * Session view with event
	 * @return string
	 */
	public function index(): string
	{
		global $app_config;
		$this->dataView['id'] = $_REQUEST['id'];
		$this->dataView['lang'] = $_REQUEST['lang'];
		$this->dataView['error'] = 0;

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
			$webserviceUrl .= "&role=". 'learner'; //TODO multi roles $_SESSION['user_roles'];
			//Bug voir jerome
			$webserviceObj = Webservice::curl($webserviceUrl);
			echo $webserviceUrl;
			$webserviceObj = json_decode('[{"id":"5136e734-71f2-11ec-ac2b-0050569c3446","name":"Formation management - "}]');
			if (!empty($webserviceObj)) {
				//traitement de champs du json pour injecter dans le template
			} else {
				$this->dataView['error'] = 1;
				$this->dataView['error-message'] = "Get webservice not found.";
			}
			
			$view = new View();
			$view->setView(__DIR__ . '/templates/default.php');
			return $view->render($this->dataView);
		}
	}	
	

}
