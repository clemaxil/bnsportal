<?php
if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

use App\Model;
use App\View;
use App\Controller;

require_once(__DIR__ . '/includes/ics-parser-master/src/ICal/ICal.php');
require_once(__DIR__ . '/includes/ics-parser-master/src/ICal/Event.php');

use App\Webservice;
use ICal\ICal;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

/**
 * Class Calendar for formers
 * @package MyCalendar
 */
class CalendarController extends Controller
{
	/**
	 * 
	 * @var array
	 */
	protected $dataView;



	/**
	 * Calendar view with event
	 * @return string
	 */
	public function index(): string
	{

		if (empty($_SESSION['user_id'])) {
			appHelperUrl_redirect($_REQUEST['lang'], 'auth', 'index');
			return ('user must be connected');
		}

		$this->dataView['error'] = 0;

		if (!empty($_SESSION['user_calendars'])) {
			$calendars = json_decode($_SESSION['user_calendars']);
			$i = 0;
			if (!is_array($calendars->data)) {
				$this->dataView['error'] = 1;
				$this->dataView['error-message'] = "No calendar found.";
			} else {
				foreach ($calendars->data as $calendar) {

					if (strpos($calendar->url, "_validate.ics") != false || strpos($calendar->url, "_meetings.ics") != false || strpos($calendar->url, "_calls.ics") != false) {
						try {
							$ical = new ICal($calendar->url, array(
								'defaultSpan'                 => 2,     // Default value
								'defaultTimeZone'             => 'UTC',
								'defaultWeekStart'            => 'MO',  // Default value
								'disableCharacterReplacement' => false, // Default value
								'filterDaysAfter'             => null,  // Default value
								'filterDaysBefore'            => null,  // Default value
								'skipRecurrence'              => false, // Default value
							));
						} catch (\Exception $e) {
							$this->dataView['error'] = 1;
							$this->dataView['error-message'] = "No calendar found.";
							continue;
						}

						$this->dataView['calendars'][$i]['config'] = array('url' => $calendar->url, 'color' => $calendar->color, 'textColor' => $calendar->textColor);
						$events = $ical->sortEventsWithOrder($ical->events());
						$this->dataView['calendars'][$i]['events'] = $events;
					}

					$i++;
				}
			}
		}
		$view = new View();
		$view->setView(__DIR__ . '/templates/default.php');
		return $view->render($this->dataView);
	}





	/**
	 * iframe fo view and download pdf
	 * @return void 
	 */
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
			. $_SESSION['user_id'] . '/'
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



	/**
	 * get all PDF documents avaible on crm & docs submitted
	 * 
	 * @return void 
	 * @throws TransportExceptionInterface 
	 * @throws RedirectionExceptionInterface 
	 * @throws ClientExceptionInterface 
	 * @throws ServerExceptionInterface 
	 * @throws Exception 
	 */
	public function document()
	{
		global $app_config;

		$this->dataView['id'] = $_REQUEST['id'];
		$this->dataView['lang'] = $_REQUEST['lang'];
		$this->dataView['save'] = '';
		$this->dataView['error'] = '';
		$this->dataView['upload_message'] = '';

		//details, dates, inscrits
		$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServiceSessionDateCapture";
		$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=getDetailsIdForPortal&id=" . $this->dataView['id'];
		$webserviceObj = Webservice::http($webserviceUrl);

		$this->dataView['session'] = $webserviceObj->session;


		$webserviceUrlSurvey = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServiceSurveySummarySendCapture";
		$webserviceUrlSurvey .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=getSurveyList&session_id=" . $webserviceObj->session->id;
		$webserviceObjSurvey = Webservice::file($webserviceUrlSurvey);

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

			if (!is_dir($upload_dir . $this->dataView['session']->id . '/' . $_SESSION['user_id'])) {
				mkdir($upload_dir . $this->dataView['session']->id . '/' . $_SESSION['user_id'], 0777);
			}

			if (!is_dir($upload_dir . $this->dataView['session']->id . '/' . $_SESSION['user_id'] . '/myfiles')) {
				mkdir($upload_dir . $this->dataView['session']->id . '/' . $_SESSION['user_id'] . '/myfiles', 0777);
			}

			if (!is_dir($upload_dir . $this->dataView['session']->id . '/' . $_SESSION['user_id'] . '/allfiles')) {
				mkdir($upload_dir . $this->dataView['session']->id . '/' . $_SESSION['user_id'] . '/allfiles', 0777);
			}


			if ($_FILES['formFile']['error'] > 0) {
				$upload_message = '<p class="text-danger">' . $phpFileUploadErrors[$_FILES['formFile']['error']] . '</p>';
			} else {
				if ($_FILES['formFile']['type'] != 'application/pdf') {
					$upload_message = '<p class="text-danger">Error: File type, pdf only</p>';
				} else {
					$tmp_name = $_FILES["formFile"]["tmp_name"];
					$name = basename($_FILES["formFile"]["name"]);
					move_uploaded_file($tmp_name, $upload_dir . $this->dataView['session']->id . '/' . $_SESSION['user_id'] . '/myfiles/' . $name);
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



		$this->dataView['sondages'] = $webserviceObjSurvey;

		//list uploads directory
		$uploads = [];
		if (is_dir($upload_dir . $this->dataView['session']->id . '/' . $_SESSION['user_id'] . '/myfiles')) {
			$cdir = scandir($upload_dir . $this->dataView['session']->id . '/' . $_SESSION['user_id'] . '/myfiles');
			foreach ($cdir as $key => $value) {
				if ('.' !== $value && '..' !== $value) {
					$uploads[] = array(
						'name' => $value,
						'link' => 'upload/session/' . $this->dataView['session']->id . '/' . $_SESSION['user_id'] . '/myfiles/' . $value
					);
				}
			}
		}
		$this->dataView['uploads'] = $uploads;



		$view = new View();
		$view->setView(__DIR__ . '/templates/document.php');
		echo $view->render($this->dataView);
	}








	/**
	 * view details session
	 * 
	 * @return void 
	 * @throws TransportExceptionInterface 
	 * @throws RedirectionExceptionInterface 
	 * @throws ClientExceptionInterface 
	 * @throws ServerExceptionInterface 
	 * @throws Exception 
	 */
	public function detail()
	{
		global $app_config;

		$this->dataView['id'] = $_REQUEST['id'];
		$this->dataView['lang'] = $_REQUEST['lang'];
		$this->dataView['save'] = '';
		$this->dataView['error'] = '';

		if (!empty($_REQUEST['save'])) {

			$this->dataView['save'] = $_REQUEST['save'];

			if ($this->dataView['save'] == "ok") {
				$this->dataView['save-message'] = 'Vos modifications ont bien été enregistrées';
			}

			if ($this->dataView['save'] == "false") {
				$this->dataView['save-message'] = 'Vos modifications n\'ont pas été enregistrées';
			}
		}


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


		//############### bns_portalfields
		if (count($webserviceObj->portalfields->session) > 0) {
			foreach ($webserviceObj->portalfields->session as $field) {
				$this->dataView['session_fields'][] = $this->setfieldType(
					$fieldLabel = $field->name,
					$fieldName = $field->portailfield,
					$fieldType = $field->sugar_field_type,
					$fieldReadOnly = $field->portailfield_readonly,
					$fieldOptions = @$field->sugar_field_options_list,
					$fieldValue = $webserviceObj->session->{$field->portailfield}
				);
			}

			// $myarray = array(
			// 	$field->name,
			// 	$field->portailfield,
			// 	$field->sugar_field_type,
			// 	@$field->sugar_field_options_list,
			// 	$webserviceObj->session->{$field->portailfield}
			// );
		}

		$view = new View();
		$view->setView(__DIR__ . '/templates/detail.php');
		echo $view->render($this->dataView);
	}



	/**
	 * list view date_session
	 * 
	 * @return void 
	 * @throws TransportExceptionInterface 
	 * @throws RedirectionExceptionInterface 
	 * @throws ClientExceptionInterface 
	 * @throws ServerExceptionInterface 
	 * @throws Exception 
	 */
	public function date()
	{
		global $app_config;

		$this->dataView['id'] = $_REQUEST['id'];
		$this->dataView['lang'] = $_REQUEST['lang'];
		$this->dataView['save'] = '';
		$this->dataView['error'] = '';

		if (!empty($_REQUEST['save'])) {

			$this->dataView['save'] = $_REQUEST['save'];

			if ($this->dataView['save'] == "ok") {
				$this->dataView['save-message'] = 'Vos modifications ont bien été enregistrées';
			}

			if ($this->dataView['save'] == "false") {
				$this->dataView['save-message'] = 'Vos modifications n\'ont pas été enregistrées';
			}
		}

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
	 * @throws TransportExceptionInterface 
	 * @throws RedirectionExceptionInterface 
	 * @throws ClientExceptionInterface 
	 * @throws ServerExceptionInterface 
	 * @throws Exception 
	 */
	public function inscrit()
	{
		global $app_config;

		$this->dataView['id'] = $_REQUEST['id'];
		$this->dataView['lang'] = $_REQUEST['lang'];
		$this->dataView['save'] = '';
		$this->dataView['error'] = '';


		if (!empty($_REQUEST['save'])) {

			$this->dataView['save'] = $_REQUEST['save'];

			if ($this->dataView['save'] == "ok") {
				$this->dataView['save-message'] = 'Vos modifications ont bien été enregistrées';
			}

			if ($this->dataView['save'] == "false") {
				$this->dataView['save-message'] = 'Vos modifications n\'ont pas été enregistrées';
			}
		}

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


		//############### bns_portalfields
		if (count($webserviceObj->portalfields->session) > 0) {

			//tri des inscription name par ordre alphabetique
			foreach ($this->dataView['registrations'] as $registration) {
				$name_pos = strrpos($registration->name, '-');
				$registration->name = ltrim(substr($registration->name, $name_pos));
				$registration->name = str_replace('- ', '', $registration->name);
				$name_tab = explode(' ', $registration->name);
				$name_tab_reversed = array_reverse($name_tab);
				$registration->name = implode($name_tab_reversed);
			}
			$columns = array_column($this->dataView['registrations'], 'name');
			array_multisort($columns, SORT_ASC, $this->dataView['registrations']);

			// echo "<!-- ";
			// echo "<pre>";
			// print_r($this->dataView['registrations']);
			// echo "</pre>";
			// echo " -->";

			foreach ($webserviceObj->registrations as $registration) {

				$contactId = $registration->contact_id_c;
				//echo "<br>bi : $contactId  ".$this->dataView['contacts']->$contactId->first_name;

				foreach ($webserviceObj->portalfields->registration as $field) {
					$this->dataView['registration_fields'][$contactId][] = $this->setfieldType(
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

		$view = new View();
		$view->setView(__DIR__ . '/templates/inscrit.php');
		echo $view->render($this->dataView);
	}



	/**
	 * update fields and score/notes
	 * 
	 * @return void 
	 * @throws TransportExceptionInterface 
	 * @throws RedirectionExceptionInterface 
	 * @throws ClientExceptionInterface 
	 * @throws ServerExceptionInterface 
	 * @throws Exception 
	 */
	public function update()
	{
		global $app_config;

		$this->dataView['id'] = $_REQUEST['id'];
		$this->dataView['lang'] = $_REQUEST['lang'];

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$action = 'detail';
			$data = urlencode(json_encode($_POST));

			if ($_POST['tabname'] == 'session') {
				$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServiceSessionDateCapture";
				$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=setPortalFieldsForSession&id=" . $_POST['sessionid'] . "&data=" . $data;
				$webserviceResult = Webservice::http($webserviceUrl);
				$action = "detail";
			}

			$webserviceResult = '';
			if ($_POST['tabname'] == 'registrations') {
				$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServiceSessionDateCapture";
				$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=setPortalFieldsForRegistration&id=" . $_POST['registrationid'] . "&data=" . $data;
				$webserviceResult = Webservice::http($webserviceUrl);
				$action = "inscrit";

				//notes
				foreach ($_POST as $key => $val) {
					if (strpos($key, "note_") !== false) {
						$noteTab = explode('_', $key);
						$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServiceSessionDateCapture";
						$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=setNoteForPortal&id=" . $noteTab[1] . "&note=" . $val;
						Webservice::http($webserviceUrl);
					}
				}
			}

			$save = "false";
			if ($webserviceResult == true) {
				$save = "ok";
			}
			appHelperUrl_redirect($this->dataView['lang'], 'calendar', $action, $this->dataView['id'], $other = 'save=' . $save);
		} else {
			appHelperUrl_redirect($this->dataView['lang'], 'calendar', 'detail', $this->dataView['id'], $other = 'save=ko');
		}
	}



	/**
	 * 
	 * @param mixed $fieldLabel 
	 * @param mixed $fieldName 
	 * @param mixed $fieldType 
	 * @param string $fieldReadOnly 
	 * @param array $fieldOptions 
	 * @param string $fieldValue 
	 * @return string 
	 */
	public function setfieldType($fieldLabel, $fieldName, $fieldType, $fieldReadOnly = '', $fieldOptions = [], $fieldValue = '')
	{
		$fieldDisabled = '';
		if ($fieldReadOnly == 1) {
			$fieldReadOnly = 'readonly="readonly"';
			$fieldDisabled = 'disabled';
		}

		switch ($fieldType) {
			case "text":
				$output = '<br>' . $fieldLabel . ': <br><textarea class="form-control" ' . $fieldReadOnly . ' name="' . $fieldName . '">' . $fieldValue . '</textarea>';
				break;
			case "address":
				$output = '<br>' . $fieldLabel . ': <br><input class="form-control" type="text" ' . $fieldReadOnly . ' name="' . $fieldName . '" value="' . $fieldValue . '">';
				break;
			case "decimal":
				$output = '<br>' . $fieldLabel . ': <br><input class="form-control" type="text" ' . $fieldReadOnly . ' name="' . $fieldName . '" value="' . $fieldValue . '">';
				break;
			case "int":
				$output = '<br>' . $fieldLabel . ': <br><input class="form-control" type="text" ' . $fieldReadOnly . ' name="' . $fieldName . '" value="' . $fieldValue . '">';
				break;
			case "varchar":
				$output = '<br>' . $fieldLabel . ': <br><input class="form-control" type="text" ' . $fieldReadOnly . ' name="' . $fieldName . '" value="' . $fieldValue . '">';
				break;
			case "float":
				$output = '<br>' . $fieldLabel . ': <br><input class="form-control" type="text" ' . $fieldReadOnly . ' name="' . $fieldName . '" value="' . $fieldValue . '">';
				break;
			case "phone":
				$output = '<br>' . $fieldLabel . ': <br><input class="form-control" type="text" ' . $fieldReadOnly . ' name="' . $fieldName . '" value="' . $fieldValue . '">';
				break;
			case "enum":
				$output = '<br>' . $fieldLabel . ': <br><select class="form-control" ' . $fieldDisabled . ' name="' . $fieldName . '">';
				foreach ($fieldOptions[0] as $key => $val) {
					$output .= '<option value="' . $key . '" ';
					if ($key == $fieldValue) {
						$output .= 'selected';
					}
					$output .= '>' . $val . '</option>';
				}
				$output .= '</select>';
				break;
			case "bool":
				$output = '<br>' . $fieldLabel . ': <br><input class="form-check-input" type="checkbox" ' . $fieldDisabled . ' name="' . $fieldName . '" ';
				if ($fieldValue == 1) {
					$output .= ' checked';
				}
				$output .= '>';
				break;
			case "radioenum":
				$output = '<br>' . $fieldLabel . ': <br>';
				foreach ($fieldOptions[0] as $key => $val) {
					$output .= ' <input class="form-check-input" type="radio" ' . $fieldDisabled . ' name="' . $fieldName . '" value="' . $key . '"';
					if ($key == $fieldValue) {
						$output .= ' checked';
					}
					$output .= '> ' . $val;
				}
				break;
			default:
				$output = '<br>' . $fieldLabel . ':: ' . $fieldValue;
				break;
		}

		return $output;
	}
}
