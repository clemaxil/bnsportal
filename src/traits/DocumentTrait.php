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
		$dataView['error'] = '';

		//details, dates, inscrits
		$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServiceSessionDateCapture";
		$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=getDetailsIdForPortal&id=" . $dataView['id'];
		$webserviceObj = Webservice::http($webserviceUrl);

		$dataView['session'] = $webserviceObj->session;

		$upload_dir = 'upload/session/';

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


			if (!is_dir($upload_dir . $dataView['session']->id)) {
				mkdir($upload_dir . $dataView['session']->id, 0777);
			}

			if (!is_dir($upload_dir . $dataView['session']->id . '/' . $_SESSION['user_id_ext'])) {
				mkdir($upload_dir . $dataView['session']->id . '/' . $_SESSION['user_id_ext'], 0777);
			}

			if (!is_dir($upload_dir . $dataView['session']->id . '/' . $_SESSION['user_id_ext'] . '/myfiles')) {
				mkdir($upload_dir . $dataView['session']->id . '/' . $_SESSION['user_id_ext'] . '/myfiles', 0777);
			}

			if (!is_dir($upload_dir . $dataView['session']->id . '/' . $_SESSION['user_id_ext'] . '/allfiles')) {
				mkdir($upload_dir . $dataView['session']->id . '/' . $_SESSION['user_id_ext'] . '/allfiles', 0777);
			}


			if ($_FILES['formFile']['error'] > 0) {
				$upload_message = '<p class="text-danger">' . $phpFileUploadErrors[$_FILES['formFile']['error']] . '</p>';
			} else {
				if ($_FILES['formFile']['type'] != 'application/pdf') {
					$upload_message = '<p class="text-danger">Error: File type, pdf only</p>';
				} else {
					$tmp_name = $_FILES["formFile"]["tmp_name"];
					$name = basename($_FILES["formFile"]["name"]);
					move_uploaded_file($tmp_name, $upload_dir . $dataView['session']->id . '/' . $_SESSION['user_id_ext'] . '/myfiles/' . $name);
					$upload_message = '<p class="text-success">' . $phpFileUploadErrors[$_FILES['formFile']['error']] . '</p>';

					//creation de la note avec le fichier dans le crm
					$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServiceSessionDateCapture";
					$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=setDocumentForPortal";
					$webserviceUrl .= "&session_date_id=" . $dataView['id'] . "&user_id=" . $_SESSION['user_id_ext'];
					$webserviceUrl .= "&url=" . urlencode($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . str_replace('index.php', '', $_SERVER['SCRIPT_NAME']) . 'upload/session/' . $dataView['session']->id . '/' . $_SESSION['user_id'] . '/myfiles/' . $name) . "&user_id=" . $_SESSION['user_id_ext'];
					$webserviceUrl .= "&name=" . basename($_FILES["formFile"]["name"]);
					Webservice::http($webserviceUrl);
				}
			}
			$dataView['upload_message'] = $upload_message;
		}


		//list uploads directory
		$uploads = [];
		if (is_dir($upload_dir . $dataView['id'] . '/' . $_SESSION['user_id_ext'] . '/allfiles')) {
			$cdir = scandir($upload_dir . $dataView['id'] . '/' . $_SESSION['user_id_ext'] . '/allfiles');
			foreach ($cdir as $key => $value) {
				if ('.' !== $value && '..' !== $value) {
					$uploads[] = array(
						'name' => $value,
						'link' => 'upload/session/' . $dataView['id'] . '/' . $_SESSION['user_id_ext'] . '/allfiles/' . $value
					);
				}
			}
		}
		$dataView['uploads'] = $uploads;

		$view = new View();
		$view->setView('modules/'.$dataView['module']. '/templates/document.php');
		echo $view->render($dataView);
	}

}