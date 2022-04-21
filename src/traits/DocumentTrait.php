<?php
declare(strict_types=1);

namespace App\traits;

use App\View;
use App\Webservice;

trait DocumentTrait
{
	function document(){
		global $app_config;
		
		$dataView = array();
		$dataView['id'] = $_REQUEST['id'];
		$dataView['module'] = $_REQUEST['module'];
		$dataView['lang'] = $_REQUEST['lang'];

		$dataView['upload_message'] = '';

		$dataView['session_numero'] = (empty($_SESSION['session_numero'])) ? 'No session numero' : $_SESSION['session_numero'];
		$dataView['session_name'] = (empty($_SESSION['session_name'])) ? 'No session name' : $_SESSION['session_name'];


		$upload_dir = 'upload/session/';
		if (!is_dir($upload_dir . $dataView['id'])) {
			mkdir($upload_dir . $dataView['id'], 0777);
		}

		if (!is_dir($upload_dir . $dataView['id'] . '/' . $_SESSION['user_id_ext'])) {
			mkdir($upload_dir . $dataView['id'] . '/' . $_SESSION['user_id_ext'], 0777);
		}
		

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {			

			$phpFileUploadErrors = array(
				0 => 'The file uploaded with success',
				1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
				2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
				3 => 'The uploaded file was only partially uploaded',
				4 => 'No file was uploaded',
				6 => 'Missing a temporary folder',
				7 => 'Failed to write file to disk.',
				8 => 'A PHP extension stopped the file upload.',
			);

			if ($_FILES['formFile']['error'] > 0) {
				$upload_message = '<p class="text-danger">' . $phpFileUploadErrors[$_FILES['formFile']['error']] . '</p>';
			} else {
				
				if ($_FILES['formFile']['type'] != 'application/pdf') {
					$upload_message = '<p class="text-danger">Error: File type, pdf only</p>';
				} else {
					$tmp_name = $_FILES["formFile"]["tmp_name"];
					$name = basename($_FILES["formFile"]["name"]);
					move_uploaded_file($tmp_name, $upload_dir . $dataView['id'] . '/' . $_SESSION['user_id_ext'] . '/' . $name);
					$upload_message = '<p class="text-success">' . $phpFileUploadErrors[$_FILES['formFile']['error']] . '</p>';

					//creation de la note avec le fichier dans le crm
					$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServicePortalCapture";
					$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'];
					$webserviceUrl .= "&bns_action=portalSetDocument";
					$webserviceUrl .= "&session_id=" . $dataView['id'];
					$webserviceUrl .= "&contact_id=" . $_SESSION['user_id_ext'];
					$webserviceUrl .= "&role=".json_decode($_SESSION['user_roles'])[0];
					$webserviceUrl .= "&agency_code=" . $app_config['sugar_bns_company_code'];
					$webserviceUrl .= "&url=" . urlencode($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . str_replace('index.php', '', $_SERVER['SCRIPT_NAME']) . 'upload/session/' . $dataView['id'] . '/' . $_SESSION['user_id_ext'] . '/_' . $name);
					$webserviceUrl .= "&name=" .urlencode($_FILES["formFile"]["name"]);
					//echo $webserviceUrl;
					// echo "<pre>";
					// print_r($_FILES);
					// echo "</pre>";
					//exit();
					Webservice::http($webserviceUrl);				}
			}
			$dataView['upload_message'] = $upload_message;
		}


		$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServicePortalCapture";
		$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'];
		$webserviceUrl .= "&bns_action=getDocumentList";
		$webserviceUrl .= "&session_id=" . $dataView['id'];
		$webserviceUrl .= "&contact_id=" . $_SESSION['user_id_ext'];
		$webserviceUrl .= "&role=".json_decode($_SESSION['user_roles'])[0];
		$webserviceUrl .= "&agency_code=" . $app_config['sugar_bns_company_code'];
		
		$webserviceObj = Webservice::http($webserviceUrl);	
		//echo print_r($webserviceObj,true);
		$dataView['documents'] = $webserviceObj;		


		//list uploads directory
		$dataView['uploads'] = [];
		if (is_dir($upload_dir . $dataView['id'] . '/' . $_SESSION['user_id_ext'])) {
			$cdir = scandir($upload_dir . $dataView['id'] . '/' . $_SESSION['user_id_ext']);
			foreach ($cdir as $key => $value) {
				if ('.' !== $value && '..' !== $value && substr($value, 0, 1)=="_") {
					$dataView['uploads'][] = array(
						'name' => $value,
						'link' => 'upload/session/' . $dataView['id'] . '/' . $_SESSION['user_id_ext'] . $value
					);
				}
			}
		}
		//$dataView['uploads'] = $uploads;

		$view = new View();
		$view->setView('modules/'.$dataView['module']. '/templates/document.php');
		echo $view->render($dataView);
	}

}