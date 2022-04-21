<?php
declare(strict_types=1);

namespace App\traits;

use App\View;
use App\Webservice;

trait DateTrait
{
	public function date()
	{
		global $app_config;

		$dataView['id'] = $_REQUEST['id'];
		$dataView['lang'] = $_REQUEST['lang'];
		$dataView['module'] = $_REQUEST['module'];
		$dataView['error_fatal'] = 0;

		$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServicePortalCapture";
		$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'];
		$webserviceUrl .= "&bns_action=portalSessionGetDate";
		$webserviceUrl .= "&session_id=" . $dataView['id'];
		$webserviceUrl .= "&contact_id=" . $_SESSION['user_id_ext'];
		$webserviceUrl .= "&role=".json_decode($_SESSION['user_roles'])[0];
		$webserviceUrl .= "&agency_code=" . $app_config['sugar_bns_company_code'];
		$webserviceObj = Webservice::http($webserviceUrl);

		if (gettype($webserviceObj) == "object") {
			$dataView['dates'] = $webserviceObj->dates;
			$dataView['account'] = $webserviceObj->account;
			$dataView['formateurs'] = $webserviceObj->formateurs;
		} else {
			$_SESSION['flash'] = array('type'=>'danger','message'=>'Webservice not found. '.print_r($webserviceObj,true));
			$dataView['error_fatal'] = 1;
		}

		$view = new View();
		$view->setView('modules/'.$dataView['module']. '/templates/date.php');
		echo $view->render($dataView);
	}

}
