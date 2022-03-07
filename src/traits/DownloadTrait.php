<?php
declare(strict_types=1);

namespace App\traits;

trait DownloadTrait
{

	public function download()
	{
		global $app_config;

		$dataView = array();
		$dataView['id'] = $_REQUEST['id'];
		$dataView['lang'] = $_REQUEST['lang'];
		$dataView['document_id'] = $_GET['document_id'];
		$dataView['document_name'] = $_GET['document_name'];
		$dataView['document_directory'] = $_GET['document_directory'];

		$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServiceSessionDateCapture";
		$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'];
		$webserviceUrl .= "&bns_action=getDetailsIdForPortal&id=" . $dataView['id'];
		$webserviceResult = file_get_contents($webserviceUrl);
		$webserviceObj = json_decode($webserviceResult);

		$tdataView['session'] = $webserviceObj->session;

		$upload_dir = 'upload/session/' . $dataView['session']->id . '/'
			. $_SESSION['user_id_ext'] . '/'
			. $dataView['document_directory'] . '/';


		if ($dataView['document_directory'] != "myfiles") {

			$filename = $upload_dir . $dataView['document_name'];

			if (file_exists($filename) && ((time() - filemtime($filename)) >= 60 * 60 * 24)) {
				unlink($filename);
			}


			if (!file_exists($filename)) {
				$webserviceUrlSurvey = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServiceSurveySummarySendCapture";
				$webserviceUrlSurvey .= "&key=" . $app_config['sugar_webservice_key'] . "&survey_id=" . $dataView['document_id'];

				$pdf_content = file_get_contents($webserviceUrlSurvey);
				$fp = fopen($filename, 'a');
				fwrite($fp, $pdf_content);
				fclose($fp);
			}
		}

		
		$view = new View();
		$view->setView(__DIR__ . '/templates/download.php');
		echo $view->render($dataView);
	}

}