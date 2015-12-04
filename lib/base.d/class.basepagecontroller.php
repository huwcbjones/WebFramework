<?php

/**
 * Base Page Controller
 *
 * @category   WebApp.Base.Controller.Page
 * @package    class.basepagecontroller.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 * 
 */

class BasePageController extends BaseController
{
	const		name_space		= 'WebApp.Base.PageController';
	const		version			= '1.0.0';

	public function processPage($cat1='', $cat2='', $cat3='', $cat4=''){
		$pagefile = $this->_getFilename($cat1, $cat2, $cat3, $cat4);
		if(file_exists($pagefile.'.php')){
			$this->parent->parent->debug($this::name_space.': Loading file "'.str_replace(__LIBDIR__, '', $pagefile.'.php').'"...');
			$this->parent->setContent($this->_processPage($pagefile.'.php'));
			return true;
		}else{
			$this->parent->parent->debug($this::name_space.': Failed to load page file "'.str_replace(__LIBDIR__, '', $pagefile).'.php"!');
			return false;
		}
	}
	function _getFilename($cat1='', $cat2='', $cat3='', $cat4=''){
		if($cat1 == ''){
			for ($i = 1; $i <= 4; $i++) {
				${'cat'.$i} = WebApp::get('cat' . $i);
			}
		}
		
		$pagefile = __LIBDIR__.'/modules/'.$cat1.'/pages/';
		if($cat2!==NULL && $cat2 !== ''){
			$pagefile.= $cat2;
			if($cat3!==NULL && $cat3 !== ''){
				$pagefile.= '_'.$cat3;
			}
		}else{
			$pagefile.= 'home';
		}
		return $pagefile;
	}
	function _processPage($file){
		$page = $this->parent;
		$user = $this->parent->parent->user;
		$mySQL_r = $this->parent->mySQL_r;
		$mySQL_w = $this->parent->mySQL_w;
		$this->parent->parent->debug($this::name_space.': Generating page...');
		ob_start();
		include $file;
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}

?>