<?php
/**
 * News Controller Class
 *
 * @category   Module.News
 * @package    news/controller.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
 */

class PageController extends BasePageController
{
	const name_space	= 'Module.News';
	const version		= '1.0.0';

	function _getFilename(){
		$pagefile = __LIBDIR__.'/modules/'.$this->cat1.'/pages/';
		if($this->cat2!=''){
			$pagefile.= $this->cat2;
			if($this->cat3!=''&&$this->cat2!='article'&&$this->cat2!='event'){
				$pagefile.= '_'.$this->cat3;
			}
		}else{
			$pagefile.= 'home';
		}
		return $pagefile;
	}
}
?>