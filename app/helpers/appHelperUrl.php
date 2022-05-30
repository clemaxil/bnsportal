<?php


function appHelperUrl_link($module,$action,$id='',$other='',$lang=''){

	global $app_config;
	if(empty($lang)){
		global $lang;
	}
	

	$module = strtolower($module);
	$action = strtolower($action);

	/*if(!empty($_SESSION['user_lang'])){
		$lang = $_SESSION['user_lang'];
	}*/

	if($app_config['url_rewriting']==1){
		$uri = WEB_ROOT.''.$lang;
		$uri .= '/'.$module;
		$uri .= '/'.$action;
		if(!empty($id)){ $uri .= '/'.$id; }
		if(!empty($other)){ $uri .= '?'.$other; }
	}
	else{
		$uri = WEB_ROOT.'index.php?q='.$lang;
		$uri .= '/'.$module;
		$uri .= '/'.$action;
		if(!empty($id)){ $uri .= '/'.$id; }
		if(!empty($other)){ $uri .= '&'.$other; }
	}

	return $uri;
}


function appHelperUrl_dasheToCamel($string){

	return lcfirst(str_replace('-', '', ucwords($string, "-")));

}


function appHelperUrl_redirect($module,$action,$id='',$other='',$lang=''){

	header('Location: '.appHelperUrl_link($module,$action,$id,$other,$lang));

}