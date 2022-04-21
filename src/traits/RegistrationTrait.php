<?php
declare(strict_types=1);

namespace App\traits;

require(__DIR__ . '/../../helpers/appHelperRole.php');

use App\View;
use App\Webservice;
use Exception;

trait RegistrationTrait
{
	/**
	 * registrations view with custom fieds and score(notes)
	 * @return void 
	 * @throws Exception 
	 */
	public function inscrit()
	{
		global $app_config;

		$dataView['id'] = $_REQUEST['id'];
		$dataView['lang'] = $_REQUEST['lang'];
		$dataView['module'] = $_REQUEST['module'];
		$dataView['error_fatal'] = 0;

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$data = urlencode(json_encode($_POST));

			//bns_portalfield
			$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServicePortalCapture";
			$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'];
			$webserviceUrl .= "&bns_action=portalSetFieldsForRegistration";
			$webserviceUrl .= "&session_id=" . $dataView['id'];
			$webserviceUrl .= "&contact_id=" . $_SESSION['user_id_ext'];
			$webserviceUrl .= "&role=".json_decode($_SESSION['user_roles'])[0];
			$webserviceUrl .= "&agency_code=" . $app_config['sugar_bns_company_code'];
			$webserviceUrl .= "&id_registration=" . $_POST['registrationid'];
			$webserviceUrl .= "&data=" . $data;
			$webserviceResult = Webservice::http($webserviceUrl);
			

			//notes
			foreach ($_POST as $key => $val) {
				if (strpos($key, "note_") !== false) {
					$noteTab = explode('_', $key);
					$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServicePortalCapture";
					$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'];
					$webserviceUrl .= "&bns_action=portalSetNotesForRegistration";
					$webserviceUrl .= "&id=" . $noteTab[1];						
					$webserviceUrl .= "&score=" . $val;
					Webservice::http($webserviceUrl);
					
				}
			}

			$_SESSION['flash'] = array('type'=>'warning','message'=>'Error: '.print_r($webserviceResult,true));
			if ($webserviceResult == 1) {
				$_SESSION['flash'] = array('type'=>'success','message'=>'Vos modifications ont bien été enregistrées');
			}

		}


		//details, dates, inscrits
		$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServicePortalCapture";
		$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'];
		$webserviceUrl .= "&bns_action=portalSessionGetRegistration";
		$webserviceUrl .= "&session_id=" . $dataView['id'];
		$webserviceUrl .= "&contact_id=" . $_SESSION['user_id_ext'];
		$webserviceUrl .= "&role=".json_decode($_SESSION['user_roles'])[0];
		$webserviceUrl .= "&agency_code=" . $app_config['sugar_bns_company_code'];
		$webserviceObj = Webservice::http($webserviceUrl);

		if (gettype($webserviceObj) == "object") {

			$dataView['registrations'] = $webserviceObj->registrations;
			$dataView['contacts'] = $webserviceObj->contacts;
			$dataView['notes'] = $webserviceObj->notes;	


			//############### bns_portalfields
			if(appHelperRole_isGranted('former')){
				if (count($webserviceObj->portalfields->session) > 0) {

					//tri des inscription name par ordre alphabetique
					foreach ($dataView['registrations'] as $registration) {
						$name_pos = strrpos($registration->name, '-');
						$registration->name = ltrim(substr($registration->name, $name_pos));
						$registration->name = str_replace('- ', '', $registration->name);
						$name_tab = explode(' ', $registration->name);
						$name_tab_reversed = array_reverse($name_tab);
						$registration->name = implode($name_tab_reversed);
					}
					$columns = array_column($dataView['registrations'], 'name');
					array_multisort($columns, SORT_ASC, $dataView['registrations']);

					foreach ($webserviceObj->registrations as $registration) {

						$contactId = $registration->contact_id_c;

						foreach ($webserviceObj->portalfields->registration as $field) {
							$dataView['registration_fields'][$contactId][] = $this->setfieldType(
								$fieldLabel = $field->name,
								$fieldName = $field->portailfield,
								$fieldType = $field->sugar_field_type,
								$fieldReadOnly = $field->portailfield_readonly,
								$fieldOptions = @$field->sugar_field_options_list,
								$fieldValue = $registration->{$field->portailfield}
							);
						}
					}
				}
			}

		} else {
			$_SESSION['flash'] = array('type'=>'danger','message'=>'Webservice not found. '.print_r($webserviceObj,true));
			$dataView['error_fatal'] = 1;
		}


		$view = new View();
		$view->setView('modules/'.$dataView['module']. '/templates/inscrit.php');
		echo $view->render($dataView);
	}
}
