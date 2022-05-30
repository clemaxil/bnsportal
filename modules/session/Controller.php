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
		$this->dataView['status'] = (!empty($_REQUEST['status'])) ? $_REQUEST['status'] : '';
		$this->dataView['error'] = 0;

		if (empty($_SESSION['user_id'])) {
			appHelperUrl_redirect($_REQUEST['lang'], 'auth', 'index');
			return ('user must be connected');
		}

		if( !appHelperRole_isGranted('learner') && !appHelperRole_isGranted('administrator') && !appHelperRole_isGranted('client') ){
			appHelperUrl_redirect($_REQUEST['lang'], 'auth', 'index');
			return ('user must be granted');
		}
		else{
			
			$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServicePortalCapture";
			$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=getSessionsList";
			$webserviceUrl .= "&contact_id=" . $_SESSION['user_id_ext'];
			if( !empty($this->dataView['status'])){
				$webserviceUrl .= "&status=".$this->dataView['status'];
			}
			$webserviceUrl .= "&agence_id=" . $app_config['sugar_bns_company_code'];
			$webserviceUrl .= "&role=".json_decode($_SESSION['user_roles'])[0];
			$webserviceObj = Webservice::curl($webserviceUrl);
			
			if (!empty($webserviceObj)) {
				$this->dataView['sessions'] = $webserviceObj;
			} else {
				$this->dataView['error'] = 1;
				$this->dataView['error-message'] = "No session found or Get webservice not found.";
			}
			
			$view = new View();
			$view->setView(__DIR__ . '/templates/default.php');
			return $view->render($this->dataView);
		}
	}	
	

}
