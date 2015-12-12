<?php
/**
 * User Admin Controller Class
 *
 * @category   Module.User.Admin
 * @package    user/admin.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
 */

class AdminPageController extends BaseAdminPageController
{
	const name_space	= 'Module.User';
	const version		= '1.0.0';

	function _getFilename(){
		$pagefile = __MODULE__.'/user/admin/';
		if(WebApp::get('cat3')!==NULL){
			$pagefile.= WebApp::get('cat3');
		}else{
			$pagefile.= 'dash';
		}
		return $pagefile;
	}
}
?>