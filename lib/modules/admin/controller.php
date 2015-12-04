<?php
/**
 * Admin Controller Class
 *
 * @category   Module.Admin
 * @package    admin/controller.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
 */

class PageController extends BasePageController
{
	const name_space	= 'Module.Admin';
	const version		= '1.0.0';
	
	function __construct($parent){
		$this->parent = $parent;
			
		if (WebApp::get('cat2') === NULL) {
			$this->mySQL_r = $parent->mySQL_r;
			$this->mySQL_w = $parent->mySQL_w;
			
			$this->parent->parent->debug('***** '.$this::name_space.' *****');
			$this->parent->parent->debug($this::name_space.': Version '.$this::version);
			
			$module_query = $this->mySQL_r->prepare("SELECT `module_id` FROM `core_modules` WHERE `namespace`=?");
			$namespace = str_replace(array('Module.','.Controller'),'', $this::name_space);
			$module_query->bind_param('s', $namespace);
			$module_query->execute();
			$module_query->store_result();
			if($module_query->num_rows==1){
				$module_query->bind_result($module_id);
				while($module_query->fetch()){
					$this->MOD_ID = $module_id;
				}
				$module_query->free_result();
			}else{
				$this->parent->setStatus(500);
			}
			return;
		}
		if( !file_exists(__MODULE__ . '/'.WebApp::get('cat2').'/admin.php')){
			$this->parent->parent->debug($this::name_space.':  Could not find "admin.php"!');
			$this->parent->setStatus(404);
			return;
		}
		
		if( !@include_once __MODULE__ . '/'.WebApp::get('cat2').'/admin.php'){
			$this->parent->parent->debug($this::name_space.':  Could not access "admin.php"! Check r/w permissions');
			$this->parent->setStatus(404);
			return;
		}
		
		if(!class_exists('AdminPageController')){
			$this->parent->parent->debug($this::name_space.': Could not find AdminPageController class in "admin.php"!');
			$this->parent->setStatus(404);
			return;
		}
		
		$this->parent->parent->debug($this::name_space.': Splat!');
		$this->ctrl = new AdminPageController($parent);
	}
	
	public function processPage(){
		if(WebApp::get('cat2')!== NULL){
			$pagefile = $this->ctrl->_getFilename();
		}else{
			$pagefile = $this->_getFilename();
		}
		if(!file_exists($pagefile.'.php')){
			$this->parent->parent->debug($this::name_space.': Failed to load page file "'.str_replace(__LIBDIR__, '', $pagefile).'.php!');
			return false;
		}
		$this->parent->parent->debug($this::name_space.': Loading file "'.str_replace(__LIBDIR__, '', $pagefile.'.php').'"...');
		if(WebApp::get('cat2') !== NULL){
			$this->parent->setContent($this->ctrl->_processPage($pagefile.'.php'));
		}else{
			$this->parent->setContent($this->_processPage($pagefile.'.php'));
		}
		return true;
	}
	
	function _getFilename(){
		$pagefile = '';
		$pagefile = __MODULE__.'/admin/pages/dash';
		return $pagefile;
	}
}
?>