<?php
declare(strict_types=1);

namespace App\traits;

use App\Webservice;
use App\View;

trait DownloadTrait
{

	public function download()
	{
		global $app_config;
		$dataView = array();
		$dataView['id'] = $_REQUEST['id'];
		$dataView['lang'] = $_REQUEST['lang'];
		$dataView['module'] = $_REQUEST['module'];
		$dataView['document_name'] = $_GET['document_name'];
		
		$dataView['session_numero'] = (empty($_SESSION['session_numero'])) ? 'No session numero' : $_SESSION['session_numero'];
		$dataView['session_name'] = (empty($_SESSION['session_name'])) ? 'No session name' : $_SESSION['session_name'];

		if(isset($_GET['document_id']) && !empty($_GET['document_id'])){//automatic donwload
			$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServicePortalCapture";
			$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'];
			$webserviceUrl .= "&bns_action=getDocumentList";
			$webserviceUrl .= "&session_id=" . $dataView['id'];
			$webserviceUrl .= "&contact_id=" . $_SESSION['user_id_ext'];
			$webserviceUrl .= "&role=".json_decode($_SESSION['user_roles'])[0];
			$webserviceUrl .= "&agency_code=" . $app_config['sugar_bns_company_code'];
			
			$webserviceObj = Webservice::http($webserviceUrl);		
			$documents = $webserviceObj;
			foreach($documents as $document){
				if($document->id == $_GET['document_id']){
					$document_webserviceUrl = $document->download;
					$document_name = $document->name;
				}
			}

			$this->getDocument($document_webserviceUrl, $document_name, $dataView['id']);
		}
		elseif(isset($_GET['invoice_id']) && !empty($_GET['invoice_id'])){
			$webserviceUrl = $app_config['sugar_app_url'] . "/index.php?entryPoint=bnsWebServicePortalCapture";
			$webserviceUrl .= "&key=" . $app_config['sugar_webservice_key'] . "&bns_action=getInvoiceList";
			$webserviceUrl .= "&contact_id=" . $_SESSION['user_id_ext'];
			$webserviceUrl .= "&agence_id=" . $app_config['sugar_bns_company_code'];
			$webserviceUrl .= "&role=".json_decode($_SESSION['user_roles'])[0];
			$webserviceObj = Webservice::curl($webserviceUrl);
			
			foreach($webserviceObj as $invoice){
				if($invoice->id == $_GET['invoice_id']){
					$document_webserviceUrl = $invoice->download;
					$document_name = $invoice->name;
				}
			}

			$this->getDocument($document_webserviceUrl, $document_name, $dataView['id']);			
		}
	
		$view = new View();
		$view->setView('modules/'.$dataView['module']. '/templates/download.php');
		echo $view->render($dataView);

		// if(isset($_GET['document_id']) && !empty($_GET['document_id'])){// automatic donwload
		// 	\unlink('upload/session/' . $dataView['id'] . '/' . $_SESSION['user_id_ext'] . '/' .$dataView['document_name']);
        // }
	}


	private function getDocument($webserviceUrl, $documentName, $sessionId){
		global $app_config;
			
		$webserviceObj = Webservice::file($app_config['sugar_app_url'] . "/index.php?".$webserviceUrl);
		file_put_contents('upload/session/' . $sessionId . '/' . $_SESSION['user_id_ext'] . '/' . $documentName.'.pdf',$webserviceObj);
		return true;
	}

}