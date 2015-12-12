<?php
/**
 * Location Admin Controller Class
 *
 * @category   Module.Location.Admin
 * @package    location/admin.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
 */

class AdminPageController extends BaseAdminPageController
{
	const name_space	= 'Module.Location';
	const version		= '1.0.0';
	
	function _getFilename(){
		$pagefile = __MODULE__.'/location/admin/';
		if(WebApp::get('cat3')!==NULL){
			$pagefile.= WebApp::get('cat3');
		}else{
			$pagefile.= 'dash';
		}
		return $pagefile;
	}
}
?>