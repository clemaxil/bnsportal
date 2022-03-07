<?php
declare(strict_types=1);

namespace App\traits;

use App\View;

trait DownloadTrait
{

	public function download()
	{
		$dataView = array();
		$dataView['id'] = $_REQUEST['id'];
		$dataView['lang'] = $_REQUEST['lang'];
		$dataView['module'] = $_REQUEST['module'];
		$dataView['document_name'] = $_GET['document_name'];
		
		$dataView['session_id'] = (empty($_SESSION['session_id'])) ? 'No session id' : $_SESSION['session_id'];
		$dataView['session_name'] = (empty($_SESSION['session_name'])) ? 'No session name' : $_SESSION['session_name'];

	
		$view = new View();
		$view->setView('modules/'.$dataView['module']. '/templates/download.php');
		echo $view->render($dataView);
	}

}