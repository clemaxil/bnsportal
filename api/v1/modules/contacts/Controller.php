<?php

include_once('View.php');
include_once('Model.php');

class ContactsController extends AppController
{
	private $requestMethod;
	private $requestBody;
	private $requestParams;
	private $id;
	
	public function __construct(){
		parent::__construct();
		$this->dataView = array();

		$this->apiUserValidation();

		$this->requestMethod = $this->verb = $_SERVER['REQUEST_METHOD'];
		$this->requestBody = json_decode(file_get_contents("php://input"));
		parse_str($_SERVER['QUERY_STRING'], $this->requestParams);
		$queryModuleArr = explode("/",$this->requestParams['q']);
		if( !empty($queryModuleArr[1]) ){ 
			$this->id = $queryModuleArr[1];
		}	
		
		switch($this->requestMethod){
			case "POST":
				$this->create();
				break;
			case "GET":
				$this->read();
				break;
			case "PUT":
				$this->update();
				break;
			case "DELETE":
				$this->delete();
				break;
			default:
				$this->default();
				break;
		}		
		return true;
	}




	private function apiUserValidation()
	{
		$headers = getallheaders();
        $bearer = str_replace('Bearer ','',$headers['Authorization']);

		$ContactsModel = new ContactsModel();	
		$row = $ContactsModel->getContactByApiKey($bearer);
        if($row!==false || $bearer == "ivioRT6DehAqKXrWWiUk27CZZq3b2IzoDBxBim58PQGJBNHUcedTSIWvgwYw"){
			return true;
        }

		$this->dataView['api_response_method'] = $this->requestMethod;
		$this->dataView['api_response_code'] = "401";
		$this->dataView['api_response_json'] = array(
			"status"=>$this->dataView['api_response_code'],
			"message"=>"Unauthorized",
		);
		$view = new ContactsView();
		$view->default($this->dataView);
	}





	private function create()
	{
		if(!empty($this->id)){
			$this->error($this->requestMethod,"400","Bad Request, the id field must not be defined");
		}
		elseif( empty($this->requestBody->email) ){
			$this->error($this->requestMethod,"200","Bad Request, email field is mandatory ".print_r($this->requestBody,1));
		}
		else{
			$ContactsModel = new ContactsModel();		
			$contact = $ContactsModel->getByEmail($this->requestBody->email);
			if( !empty($contact) ){
				$this->error($this->requestMethod,"400","Bad Request, email already exists ".$this->requestBody->email);
			}
			else{
				$contact = $ContactsModel->create($this->requestBody);				
				$this->dataView['api_response_method'] = $this->requestMethod;
				$this->dataView['api_response_code'] = "200";				
				$this->dataView['api_response_json'] = array(
															"status"=>$this->dataView['api_response_code'],
															"message"=>"OK",
														);
				$this->dataView['api_response_json']['data'] = array( 'count'=>1, 'elements'=>array($contact));
				$view = new ContactsView();
				$view->default($this->dataView);
			}
		}				
	}


	private function update()
	{
		if(empty($this->id)){
			$this->error($this->requestMethod,"400","Bad Request, id field is mandatory");
		}
		else{
			$ContactsModel = new ContactsModel();
			$contact = $ContactsModel->update($this->id, $this->requestBody);				
			$this->dataView['api_response_method'] = $this->requestMethod;
			$this->dataView['api_response_code'] = "200";				
			$this->dataView['api_response_json'] = array(
														"status"=>$this->dataView['api_response_code'],
														"message"=>"OK",
													);
			$this->dataView['api_response_json']['data'] = array( 'count'=>1, 'elements'=>array($contact));
			$view = new ContactsView();
			$view->default($this->dataView);
		}		
	}



	private function read()
	{
		
		if(!empty($this->id)){
			$ContactsModel = new ContactsModel();
			$contact = $ContactsModel->getById($this->id);
			$this->dataView['api_response_method'] = $this->requestMethod;
			$this->dataView['api_response_code'] = "200";				
			$this->dataView['api_response_json'] = array(
														"status"=>$this->dataView['api_response_code'],
														"message"=>"OK",
													);
			$this->dataView['api_response_json']['data'] = array( 'count'=>1, 'elements'=>array($contact));			
		}
		else{
			$fields = ( !empty($this->requestParams['fields']) ) ? $this->requestParams['fields'] : array('*');
			$filters = ( !empty($this->requestParams['filters']) ) ? $this->requestParams['filters'] : array('1'=>'eq.1');
			$sortby = ( !empty($this->requestParams['sortby']) ) ? $this->requestParams['sortby'] : 'id';
			$order = ( !empty($this->requestParams['order']) ) ? strtoupper($this->requestParams['order']) : 'ASC';
			$limit = ( !empty($this->requestParams['limit']) ) ? $this->requestParams['limit'] : '50';
			$offset = ( !empty($this->requestParams['limit']) ) ? $this->requestParams['offset'] : '0';


			$fieldsList = '';
			foreach($fields as $field=>$value){
				$fieldsList = $fieldsList.$value.', ';
			}
			$fieldsList = substr($fieldsList,0,-2);
			
			$where = '';
			foreach($filters as $field=>$value){
				$where = $where . $field .$this->setFilterCondition($value)." AND ";
			}
			$where = substr($where,0,-5);

			if(!is_numeric($limit)){
				$limit = 50;
			}
	
			if(!is_numeric($offset)){
				$offset = 0;
			}

			switch($order){
				case 'ASC':
					break;
				case 'DESC':
					break;
				default:
					$order = 'ASC';
					break;
			}


			$ContactsModel = new ContactsModel();
			$contactCountAll = $ContactsModel->countAll($where);
			$contacts = $ContactsModel->read($fieldsList, $where, $sortby, $order, $limit, $offset);

			$error = '';
			if( isset($contactCountAll['error']) ){
				$error = $contactCountAll['error'];
				$total = 0; 
				$count = 0;
				$elements = '';
			}
			elseif( isset($contacts['error']) ){
				$error = $contacts['error'];
				$total = 0; 
				$count = 0;
				$elements = '';
			}
			else{
				$error = '';
				$total = $contactCountAll['total']; 
				$count = count($contacts);
				$elements = $contacts;
			}
			

			$this->dataView['api_response_method'] = $this->requestMethod;
			$this->dataView['api_response_code'] = "200";				
			$this->dataView['api_response_json'] = array(
														"status"=>$this->dataView['api_response_code'],
														"message"=>"OK",
													);
			$this->dataView['api_response_json']['data'] = array( 'error'=>$error,
																	'total'=>$total, 
																	'limit'=>$limit, 
																	'offset'=>$offset, 
																	'count'=>$count, 
																	'elements'=>$elements);
		}
		$view = new ContactsView();
		$view->default($this->dataView);
	}



	private function delete()
	{
		if(empty($this->id)){
			$this->error($this->requestMethod,"400","Bad Request, id field is mandatory");
		}
		else{
			$ContactsModel = new ContactsModel();
			$contact = $ContactsModel->delete($this->id);				
			$this->dataView['api_response_method'] = $this->requestMethod;
			$this->dataView['api_response_code'] = "200";				
			$this->dataView['api_response_json'] = array(
														"status"=>$this->dataView['api_response_code'],
														"message"=>"OK",
													);
			$this->dataView['api_response_json']['data'] = array( 'count'=>1, 'elements'=>array($contact));
			
			$view = new ContactsView();
			$view->default($this->dataView);
		}		
	}




	private function error($method,$code,$message)
	{
		$this->dataView['api_response_method'] = $method;
		$this->dataView['api_response_code'] = $code;
		$this->dataView['api_response_json'] = array(
			"status"=>$this->dataView['api_response_code'],
			"message"=>$message,
		);
		$view = new ContactsView();
		$view->default($this->dataView);
	}


	
	private function default()
	{
		$this->error($this->requestMethod,"405","Method Not Allowed");
	}
	
	

    private function setFilterCondition($filterValue){
        $filterTab = explode(".",$filterValue);
        switch($filterTab[0]){
            case("eq"):
                $output = " = '".$filterTab[1]."'";
                break;
            case("gte"):
                $output = " >= '".$filterTab[1]."'";
                break;
            case("gt"):
                $output = " > '".$filterTab[1]."'";
                break;
            case("lte"):
                $output = " <= '".$filterTab[1]."'";
                break;
            case("lt"):
                $output = " < '".$filterTab[1]."'";
                break;
            case("neq"):
                $output = " != '".$filterTab[1]."'";
                break;
            case("like"):
                $output = " like ('".$filterTab[1]."')";
                break;
            case("in"):
                $output = " in ('".$filterTab[1]."')";
                break;
            default:
                $output = " ".$filterTab[0]." '".$filterTab[1]."'";
                break;
        }
        return $output;
    }

}