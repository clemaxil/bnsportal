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
	

	use \App\traits\DownloadTrait;
	use \App\traits\DocumentTrait;

	/**
	 * iframe fo view and download pdf
	 * @return void 
	 */
	/*
	public function download()
	{
		global $app_config;

		$this->dataView['id'] = $_REQUEST['id'];
		$this->dataView['lang'] = $_REQUEST['lang'];
		$this->dataView['document_id'] = $_GET['document_id'];
		$this->dataView['document_name'] = $_GET['document_name'];
		$this->dataView['document_directory'] = $_GET['document_directory'];

		$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServiceSessionDateCapture";
		$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=getDetailsIdForPortal&id=" . $this->dataView['id'];
		$webserviceResult = file_get_contents($webserviceUrl);
		$webserviceObj = json_decode($webserviceResult);

		$this->dataView['session'] = $webserviceObj->session;

		$upload_dir = 'upload/session/' . $this->dataView['session']->id . '/'
			. $_SESSION['user_id_ext'] . '/'
			. $this->dataView['document_directory'] . '/';


		if ($this->dataView['document_directory'] != "myfiles") {

			$filename = $upload_dir . $this->dataView['document_name'];

			if (file_exists($filename) && ((time() - filemtime($filename)) >= 60 * 60 * 24)) {
				unlink($filename);
			}


			if (!file_exists($filename)) {
				$webserviceUrlSurvey = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServiceSurveySummarySendCapture";
				$webserviceUrlSurvey .= "&key=" . $app_config['sugar_webservice_key'] . "&survey_id=" . $this->dataView['document_id'];

				$pdf_content = file_get_contents($webserviceUrlSurvey);
				$fp = fopen($filename, 'a');
				fwrite($fp, $pdf_content);
				fclose($fp);
			}
		}

		$view = new View();
		$view->setView(__DIR__ . '/templates/download.php');
		echo $view->render($this->dataView);
	}
	*/


	/**
	 * list view date_session
	 * 
	 * @return void 
	 * @throws Exception 
	 */
	public function date()
	{
		global $app_config;

		$this->dataView['id'] = $_REQUEST['id'];
		$this->dataView['lang'] = $_REQUEST['lang'];
		$this->dataView['error'] = '';


		//details, dates, inscrits
		$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServiceSessionDateCapture";
		$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=getDetailsIdForPortal&id=" . $this->dataView['id'];
		$webserviceObj = Webservice::http($webserviceUrl);

		if (!empty($webserviceObj->session->id)) {
			$this->dataView['dates'] = $webserviceObj->dates;
			$this->dataView['session'] = $webserviceObj->session;
			$this->dataView['account'] = $webserviceObj->account;
			$this->dataView['registrations'] = $webserviceObj->registrations;
			$this->dataView['contacts'] = $webserviceObj->contacts;
			$this->dataView['formateurs'] = $webserviceObj->formateurs;
		} else {
			$this->dataView['error'] = 1;
			$this->dataView['error-message'] = "Get webservice not found.";
		}

		$view = new View();
		$view->setView(__DIR__ . '/templates/date.php');
		echo $view->render($this->dataView);
	}




	/**
	 * registrations view with score(notes)
	 * 
	 * @return void 
	 * @throws Exception 
	 */
	public function inscrit()
	{
		global $app_config;
		$this->dataView['id'] = $_REQUEST['id'];
		$this->dataView['lang'] = $_REQUEST['lang'];
		$this->dataView['error'] = '';

		//details, dates, inscrits
		$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServiceSessionDateCapture";
		$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=getDetailsIdForPortal&id=" . $this->dataView['id'];
		$webserviceObj = Webservice::http($webserviceUrl);


		if (!empty($webserviceObj->session->id)) {
			$this->dataView['dates'] = $webserviceObj->dates;
			$this->dataView['session'] = $webserviceObj->session;
			$this->dataView['account'] = $webserviceObj->account;
			$this->dataView['registrations'] = $webserviceObj->registrations;
			$this->dataView['contacts'] = $webserviceObj->contacts;
			$this->dataView['formateurs'] = $webserviceObj->formateurs;
			$this->dataView['notes'] = $webserviceObj->notes;
		} else {
			$this->dataView['error'] = 1;
			$this->dataView['error-message'] = "Get webservice not found.";
		}
		

		$view = new View();
		$view->setView(__DIR__ . '/templates/inscrit.php');
		echo $view->render($this->dataView);
	}



	/**
	 * get all PDF documents avaible on crm & docs submitted
	 * 
	 * @return void 
	 * @throws Exception 
	 */
	/*public function document()
	{
		global $app_config;
		$this->dataView['id'] = $_REQUEST['id'];
		$this->dataView['lang'] = $_REQUEST['lang'];
		$this->dataView['error'] = '';

		//details, dates, inscrits
		$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServiceSessionDateCapture";
		$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=getDetailsIdForPortal&id=" . $this->dataView['id'];
		$webserviceObj = Webservice::http($webserviceUrl);

		$this->dataView['session'] = $webserviceObj->session;

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


			if (!is_dir($upload_dir . $this->dataView['session']->id)) {
				mkdir($upload_dir . $this->dataView['session']->id, 0777);
			}

			if (!is_dir($upload_dir . $this->dataView['session']->id . '/' . $_SESSION['user_id_ext'])) {
				mkdir($upload_dir . $this->dataView['session']->id . '/' . $_SESSION['user_id_ext'], 0777);
			}

			if (!is_dir($upload_dir . $this->dataView['session']->id . '/' . $_SESSION['user_id_ext'] . '/myfiles')) {
				mkdir($upload_dir . $this->dataView['session']->id . '/' . $_SESSION['user_id_ext'] . '/myfiles', 0777);
			}

			if (!is_dir($upload_dir . $this->dataView['session']->id . '/' . $_SESSION['user_id_ext'] . '/allfiles')) {
				mkdir($upload_dir . $this->dataView['session']->id . '/' . $_SESSION['user_id_ext'] . '/allfiles', 0777);
			}


			if ($_FILES['formFile']['error'] > 0) {
				$upload_message = '<p class="text-danger">' . $phpFileUploadErrors[$_FILES['formFile']['error']] . '</p>';
			} else {
				if ($_FILES['formFile']['type'] != 'application/pdf') {
					$upload_message = '<p class="text-danger">Error: File type, pdf only</p>';
				} else {
					$tmp_name = $_FILES["formFile"]["tmp_name"];
					$name = basename($_FILES["formFile"]["name"]);
					move_uploaded_file($tmp_name, $upload_dir . $this->dataView['session']->id . '/' . $_SESSION['user_id_ext'] . '/myfiles/' . $name);
					$upload_message = '<p class="text-success">' . $phpFileUploadErrors[$_FILES['formFile']['error']] . '</p>';

					//creation de la note avec le fichier dans le crm
					$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServiceSessionDateCapture";
					$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=setDocumentForPortal";
					$webserviceUrl .= "&session_date_id=" . $this->dataView['id'] . "&user_id=" . $_SESSION['user_id_ext'];
					$webserviceUrl .= "&url=" . urlencode($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . str_replace('index.php', '', $_SERVER['SCRIPT_NAME']) . 'upload/session/' . $this->dataView['session']->id . '/' . $_SESSION['user_id'] . '/myfiles/' . $name) . "&user_id=" . $_SESSION['user_id_ext'];
					$webserviceUrl .= "&name=" . basename($_FILES["formFile"]["name"]);
					Webservice::http($webserviceUrl);
				}
			}
			$this->dataView['upload_message'] = $upload_message;
		}


		


		$view = new View();
		$view->setView(__DIR__ . '/templates/document.php');
		echo $view->render($this->dataView);
	}*/



	/**
	 * View detail session
	 * @return void 
	 * @throws Exception 
	 */
	public function detail()
	{
		global $app_config;
		$this->dataView['id'] = $_REQUEST['id'];
		$this->dataView['lang'] = $_REQUEST['lang'];
		$this->dataView['error'] = 0;


		//details, dates, inscrits		
		$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServiceSessionDateCapture";
		$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=getDetailsIdForPortal&id=" . $this->dataView['id'];
		$webserviceObj = Webservice::http($webserviceUrl);

		if (!empty($webserviceObj->session->id)) {
			$this->dataView['dates'] = $webserviceObj->dates;
			$this->dataView['session'] = $webserviceObj->session;
			$this->dataView['account'] = $webserviceObj->account;
			$this->dataView['registrations'] = $webserviceObj->registrations;
			$this->dataView['contacts'] = $webserviceObj->contacts;
			$this->dataView['formateurs'] = $webserviceObj->formateurs;
		} else {
			$this->dataView['error'] = 1;
			$this->dataView['error-message'] = "Get webservice not found.";
		}


		$view = new View();
		$view->setView(__DIR__ . '/templates/detail.php');
		echo $view->render($this->dataView);
	}






}
