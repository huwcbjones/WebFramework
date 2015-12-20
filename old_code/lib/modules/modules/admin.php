<?php
/**
 * Modules Admin Controller Class
 *
 * @category   Module.Modules.Admin
 * @package    modules/admin.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
 */

class AdminPageController extends BaseAdminPageController
{
	const name_space	= 'Module.Modules';
	const version		= '1.0.0';

	function _getFilename(){
		$pagefile = __MODULE__.'/modules/admin/';
		if(WebApp::get('cat3')!==NULL){
			$pagefile.= WebApp::get('cat3');
			if(
				(WebApp::get('cat4')!==NULL&&WebApp::get('cat3')=='install')
				||(WebApp::get('cat5')!==NULL&&WebApp::get('cat4')!==NULL&&WebApp::get('cat3')=='update')
				||(WebApp::get('cat4')!==NULL&&WebApp::get('cat3')=='uninstall')
				){
				$pagefile.= '_fromdir';
			}
		}else{
			$pagefile.= 'dash';
		}
		return $pagefile;
	}
}
?>