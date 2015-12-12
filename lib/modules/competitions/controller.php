<?php
/**
 * Competitions Controller Class
 *
 * @category   Module.Competitions
 * @package    competitions/controller.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
 */

class PageController extends BasePageController
{
	const name_space	= 'Module.Competitions.Controller';
	const version		= '1.0.0';

	function _getFilename(){
		$pagefile = __LIBDIR__.'/modules/'.$this->cat1.'/pages/';
		if($this->cat2!=''){
			$pagefile.= $this->cat2;
			if($this->cat3!=''&&$this->cat2!='meet'){
				$pagefile.= '_'.$this->cat3;
			}
		}else{
			$pagefile.= 'home';
		}
		return $pagefile;
	}
}
?>