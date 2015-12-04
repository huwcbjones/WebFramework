<?php

/**
 * Base Admin Page Controller
 *
 * @category   WebApp.Base
 * @package    base.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 *
 */

class BaseAdminPageController extends BasePageController
{
	const		name_space		= 'WebApp.Base.PageAdminController';
	const		version			= '1.0.0';
	
	function _getFilename(){
		$pagefile = __LIBDIR__.'/modules/'.WebApp::get('cat2').'/admin/';
		if(WebApp::get('cat3')!==NULL){
			$pagefile.= WebApp::get('cat3');
			if(WebApp::get('cat4')!==NULL){
				$pagefile.= '_'.WebApp::get('cat4');
			}
		}else{
			$pagefile.= 'dash';
		}
		return $pagefile;
	}
}
?>