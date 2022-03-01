<?php

$startTime = microtime(true);

define('APP_ENTRY', 'myApp');

//Under Maintenance ?
if (file_exists(__DIR__ . '/maintenance.php')) {
	require __DIR__ . '/maintenance.php';
	exit();
}

$app_config = include __DIR__ . '/config.php';
session_name($app_config['session_key']);
session_start();

//PSR4 autoload
require __DIR__ . '/vendor/autoload.php';
//Helper make url
require_once(__DIR__ . '/helpers/appHelperUrl.php');
require_once(__DIR__ . '/helpers/appHelperRole.php');


//Init request
$_REQUEST['module'] = $app_config['module_default'];
$_REQUEST['view'] = 'index';
$_REQUEST['id'] = '';
$_REQUEST['lang'] = $app_config['lang_default'];

if (isset($_REQUEST['q'])) {
	$params = explode("/", $_REQUEST['q']);
	$_REQUEST['lang'] = !empty($params[0]) ? $params[0] : null;
	$_REQUEST['module'] = !empty($params[1]) ? $params[1] : null;
	$_REQUEST['view'] = !empty($params[2]) ? $params[2] : null;
	$_REQUEST['id'] = !empty($params[3]) ? $params[3] : null;
}



//Get module
$module_dir = __DIR__ . '/modules/' . $_REQUEST['module'];
if (is_dir($module_dir)) {

	//Verify Languages module and application
	if (
		!file_exists($module_dir . '/' . 'languages/' . $_REQUEST['lang'] . '.php')
		|| !file_exists(__DIR__ . '/languages/' . $_REQUEST['lang'] . '.php')
	) {
		die('App lang or/and Module (' . $_REQUEST['module'] . ') lang not exist');
	}

	//Get Module Controller
	if (file_exists($module_dir . '/Controller.php')) {
		include $module_dir . '/Controller.php';
	} else {
		die('Module Controller not exist');
	}
} else {
	die('Module directory not exist');
}


//Controller
$moduleController = ucfirst($_REQUEST['module'] . 'Controller');
if (class_exists($moduleController)) {
	$bean = new $moduleController();
} else {
	die('Controller for module ' . $_REQUEST['module'] . ' not exist');
}


//Wiew
$_REQUEST['view'] = (empty($_REQUEST['view'])) ? 'index' : $_REQUEST['view'];
$_REQUEST['view'] = appHelperUrl_dasheToCamel($_REQUEST['view']);

if (method_exists($bean, $_REQUEST['view'])) {
	$view = $_REQUEST['view'];
	echo $bean->$view();
} else {
	if ($module == 'auth') { //session time closed
		echo $bean->index();
	} else {
		die('View ' . $_REQUEST['view'] . ' for module ' . $_REQUEST['module'] . ' not exist');
	}
}


if ($app_config['env'] === 'dev') {
	echo "<!-- ";
	echo "<br>";
	echo "<hr>";
	echo "<h4>DEBUG TOOL</h4>";
	echo "<hr>";
	echo "<br><b>REQUEST:</b><br>";
	echo "<pre>";
	echo print_r($_REQUEST);
	echo "</pre>";
	echo "<br>";

	echo "<br><b>SESSION:</b><br>";
	echo "<pre>";
	echo print_r($_SESSION);
	echo "</pre>";
	echo "<br><b>POST:</b><br>";
	echo "<pre>";
	echo print_r($_POST);
	echo "</pre>";
	echo "<br><b>SERVER:</b><br>";
	echo "<pre>";
	echo print_r($_SERVER);
	echo "</pre>";
	echo " -->";
}
