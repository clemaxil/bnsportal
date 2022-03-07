<?php
if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

use App\Model;
use App\View;
use App\Controller;

require_once(__DIR__ . '/includes/ics-parser-master/src/ICal/ICal.php');
require_once(__DIR__ . '/includes/ics-parser-master/src/ICal/Event.php');

use App\Webservice;
use ICal\ICal;

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
	private $dataView;


	use \App\traits\DetailTrait;
	use \App\traits\DateTrait;
	use \App\traits\DownloadTrait;
	use \App\traits\DocumentTrait;



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

		if( !appHelperRole_isGranted('former') ){
			appHelperUrl_redirect($_REQUEST['lang'], 'auth', 'index');
			return ('user must be granted');
		}
		else{

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
	}







	/**
	 * update fields and score/notes
	 * 
	 * @return void 
	 * @throws Exception 
	 */
	public function update_back()
	{
		global $app_config;

		$this->dataView['id'] = $_REQUEST['id'];
		$this->dataView['lang'] = $_REQUEST['lang'];

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$action = 'detail';
			$data = urlencode(json_encode($_POST));

			if ($_POST['tabname'] == 'session') {
				$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServiceSessionCapture";
				$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=portalSetFieldsForSession&id=" . $_POST['sessionid'] . "&data=" . $data;
				$webserviceResult = Webservice::http($webserviceUrl);
				$action = "detail";
			}

			$webserviceResult = '';
			if ($_POST['tabname'] == 'registrations') {
				$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServiceSessionCapture";
				$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=portalSetFieldsForRegistration&id=" . $_POST['registrationid'] . "&data=" . $data;
				$webserviceResult = Webservice::http($webserviceUrl);
				$action = "inscrit";

				//notes
				foreach ($_POST as $key => $val) {
					if (strpos($key, "note_") !== false) {
						$noteTab = explode('_', $key);
						$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServiceSessionCapture";
						$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=portalSetNote&id=" . $noteTab[1] . "&note=" . $val;
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
