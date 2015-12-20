<?php
/**
 * Core Page Controller Class
 *
 * @category   Module.Core
 * @package    core/controller.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */

class CorePageController extends BasePageController
{
	const name_space	= 'Module.Core';
	const version		= '1.0.0';
	
	function getHeader($parent){
		if(WebApp::get('cat1')=='admin'&&$this->parent->parent->user->is_loggedIn()){
			return $this->_processPage(__LIBDIR__.'/modules/admin/pages/header.php');
		}else{
			return $this->_processPage(__LIBDIR__.'/modules/core/pages/header.php');
		}
	}
	
	function getNavBar($parent){
		require_once __MODULE__ . '/core/resources/navbar.php';
		$navbar = new NavBar($parent);
		$navbar->generate();
		return $navbar->getNavbar();
	}
	
	function getStatusBar($parent){
		if(WebApp::get('cat1')=='admin'&&$this->parent->parent->user->is_loggedIn()){
			return $this->_processPage(__LIBDIR__.'/modules/admin/pages/nav.php');
		}else{
			return $this->_processPage(__LIBDIR__.'/modules/core/pages/status_bar.php');
		}
	}
	
	function getFooter($parent){
		if(WebApp::get('cat1')=='admin'&&$this->parent->parent->user->is_loggedIn()){
			return $this->_processPage(__LIBDIR__.'/modules/admin/pages/footer.php');
		}else{
			return $this->_processPage(__LIBDIR__.'/modules/core/pages/footer.php');
		}
	}
}

if(!class_exists('PageController')){
	class PageController extends CorePageController{
	}
}
?>