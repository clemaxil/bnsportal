<?php

if (!function_exists('appHelperRole_isGranted')) {

	/**
	 * 
	 * @param mixed $roleName 
	 * @return bool 
	 */
	function appHelperRole_isGranted($roleName){
		$roles = json_decode($_SESSION['user_roles']);
		if(in_array($roleName,$roles)){
			return true;
		}
		return false;
	}

}