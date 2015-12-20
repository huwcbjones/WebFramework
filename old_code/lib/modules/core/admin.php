<?php
/**
 * System Admin Controller Class
 *
 * @category   Module.Core
 * @package    core/admin.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
 */

class AdminPageController extends BaseAdminPageController
{
	const name_space	= 'Module.Core';
	const version		= '1.0.1';

	function _getFilename(){
		$pagefile = __MODULE__.'/core/admin/';
		if(WebApp::get('cat3')!==NULL){
			$pagefile.= WebApp::get('cat3');
		}else{
			$pagefile.= 'dash';
		}
		return $pagefile;
	}
}
?>