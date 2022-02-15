<?php

define('ROOT',str_replace('index.php','',$_SERVER['SCRIPT_FILENAME']));
define('APP_ROOT',str_replace('api/v1/index.php','',$_SERVER['SCRIPT_FILENAME']));
include_once(APP_ROOT.'config.php'); 
require_once(APP_ROOT.'app/AppModel.php');
require_once(APP_ROOT.'app/AppController.php');
require_once(APP_ROOT.'app/AppView.php');


if( isset($_REQUEST['q']) )
{
	$params = explode("/",$_REQUEST['q']);
	$module = !empty($params[0]) ? $params[0] : null;
	$id = !empty($params[1]) ? $params[1] : null;
}

if( file_exists(ROOT.'modules/'.$module.'/Controller.php') ){
	include_once(ROOT.'modules/'.$module.'/Controller.php');
	$moduleController = $module.'Controller';
	
	$moduleController = ucfirst($moduleController);
	if( class_exists($moduleController) ){		
		$bean = new $moduleController();
	}
	else{
		die('API Controller for module '. $module .' not exist');
	}
}
else{
	die('API Module '.$module.' not found');
}
