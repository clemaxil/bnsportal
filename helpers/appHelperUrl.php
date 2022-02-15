<?php

function appHelperUrl_href($lang, $module, $action, $id = '', $other = '', $anchor = '')
{
	//TODO configure for url_rewriting

	$uri = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
	$uri .= '?q=' . $lang;
	$uri .= '/' . $module;
	$uri .= '/' . $action;
	if (!empty($id)) {
		$uri .= '/' . $id;
	}
	if (!empty($other)) {
		$uri .= '&' . $other;
	}
	if (!empty($anchor)) {
		$uri .= '#' . $anchor;
	}

	return $uri;
}


function appHelperUrl_link($lang, $module, $action, $id = '', $other = '', $anchor = '')
{

	//TODO configure for url_rewriting
	$app_config = [];
	$app_config['url_rewriting'] = 0;



	$module = strtolower($module);
	$action = strtolower($action);

	if ($app_config['url_rewriting'] == 1) {
		$uri = $lang;
		$uri .= '/' . $module;
		$uri .= '/' . $action;
		if (!empty($id)) {
			$uri .= '/' . $id;
		}
		if (!empty($other)) {
			$uri .= '?' . $other;
		}
		if (!empty($anchor)) {
			$uri .= '#' . $anchor;
		}
	} else {
		$uri = 'index.php?q=' . $lang;
		$uri .= '/' . $module;
		$uri .= '/' . $action;
		if (!empty($id)) {
			$uri .= '/' . $id;
		}
		if (!empty($other)) {
			$uri .= '&' . $other;
		}
		if (!empty($anchor)) {
			$uri .= '#' . $anchor;
		}
	}

	return $uri;
}


function appHelperUrl_dasheToCamel($string)
{
	return lcfirst(str_replace('-', '', ucwords($string, "-")));
}


function appHelperUrl_redirect($lang, $module, $action, $id = '', $other = '', $anchor = '')
{
	header('Location: ' . appHelperUrl_link($lang, $module, $action, $id, $other, $anchor));
}
