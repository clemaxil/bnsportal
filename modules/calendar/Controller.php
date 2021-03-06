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
	use \App\traits\RegistrationTrait;
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
