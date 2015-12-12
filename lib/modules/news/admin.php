<?php
/**
 * News Admin Controller Class
 *
 * @category   Module.News.Admin
 * @package    news/admin.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
 */

class AdminPageController extends BaseAdminPageController
{
	const name_space	= 'Module.News';
	const version		= '1.0.0';
	
	function _getFilename(){
		$pagefile = __MODULE__.'/news/admin/';
		if($this->cat3!=''){
			$pagefile.= $this->cat3;
		}else{
			$pagefile.= 'dash';
		}
		return $pagefile;
	}
}
?>