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

		$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServiceSessionCapture";
		$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=portalSessionGetDate&id=" . $dataView['id'];
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
