<?php
declare(strict_types=1);

namespace App\traits;

require(__DIR__ . '/../../helpers/appHelperRole.php');

use App\View;
use App\Webservice;

trait DetailTrait
{
	public function detail()
	{
		global $app_config;

		$dataView['id'] = $_REQUEST['id'];
		$dataView['lang'] = $_REQUEST['lang'];
		$dataView['module'] = $_REQUEST['module'];
		$dataView['error_fatal'] = 0;


		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$data = urlencode(json_encode($_POST));
			$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServiceSessionCapture";
			$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=portalSessionSetDetail&id=" . $dataView['id'] . "&data=" . $data;
			$webserviceResult = Webservice::http($webserviceUrl);
			$_SESSION['flash'] = array('type'=>'warning','message'=>'Error: '.print_r($webserviceResult,true));
			if ($webserviceResult == 1) {
				$_SESSION['flash'] = array('type'=>'success','message'=>'Vos modifications ont bien été enregistrées');
			}
		}
	
		$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServiceSessionCapture";
		$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=portalSessionGetDetail&id=" . $dataView['id'];
		$webserviceObj = Webservice::http($webserviceUrl);

		if (!empty($webserviceObj->session->id)) {
			$dataView['session'] = $webserviceObj->session;

			$_SESSION['session_numero'] = $dataView['session']->numero;
			$_SESSION['session_name'] = $dataView['session']->name;			
			
			if(appHelperRole_isGranted('former')){
				//############### bns_portalfields
				if (count($webserviceObj->portalfields->session) > 0) {
					foreach ($webserviceObj->portalfields->session as $field) {
						$dataView['session_fields'][] = $this->setfieldType(
							$fieldLabel = $field->name,
							$fieldName = $field->portailfield,
							$fieldType = $field->sugar_field_type,
							$fieldReadOnly = $field->portailfield_readonly,
							$fieldOptions = @$field->sugar_field_options_list,
							$fieldValue = $webserviceObj->session->{$field->portailfield}
						);
					}
				}
			}

		} else {
			$_SESSION['flash'] = array('type'=>'danger','message'=>'Webservice not found.');
			$dataView['error_fatal'] = 1;
		}
				

		$view = new View();
		$view->setView('modules/'.$dataView['module']. '/templates/detail.php');
		echo $view->render($dataView);
	}
}
