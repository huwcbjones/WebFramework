<?php
/**
 * Location Controller Class
 *
 * @category   Module.Location
 * @package    location/controller.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
 */

class PageController extends BasePageController
{
	const name_space	= 'Module.Location';
	const version		= '1.0.0';

	function _getFilename(){
		$pagefile = __LIBDIR__.'/modules/location/pages/';
		$pagefile.= 'home';
		return $pagefile;
	}

}
?>