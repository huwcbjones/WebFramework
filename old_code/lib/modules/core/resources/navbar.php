<?php

/**
 * Navbar Generator
 *
 * @category   WebApp.Core.Navbar
 * @package    /resources/navbar.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

class NavBar extends Base
{
	const	name_space	= 'WebApp.Core.Navbar';
	const	version		= '1.0.0';
	
	private $navbar = '';
	private $navbarItems = array();
	
	public function generate(){
		$this->navbarItems = $this->_getItems();
		$this->navbar = $this->_openNavbar();
		
		$this->_generateNavbar();
		
		$this->navbar.= $this->_closeNavbar();
	}
	
	
	function getNavbar(){
		return $this->navbar;
	}
	
	private function _getItems(){
		if(!$this->parent->parent->config->config['core']['database']){
			return array();
		}
		
		$this->parent->parent->debug($this::name_space.': Getting items');
		
		$parent = $this->_fetchParentItems();
		return $this->_fetchChildItems($parent);
	}
	
	private function _fetchParentItems(){
		$item_query = $this->mySQL_r->prepare("SELECT `MID`, `PID`, `dropdown`,`divider`, `title`, `cat1`, `cat2`, `cat3` FROM `core_menu` INNER JOIN `core_pages` ON `PID`=`ID` WHERE `parent` IS NULL ORDER BY `position` ASC");
		$item_query->execute();
		$item_query->store_result();
		
		$this->parent->parent->debug($this::name_space.': There are '.$item_query->num_rows.' main menu items');
		
		$user = $this->parent->parent->user;
		
		$item_query->bind_result($ID, $PID, $dd, $div, $title, $cat1, $cat2, $cat3);
		
		$items = array();
		while($item_query->fetch()){
			if(!$user->can_accessPage($PID)){
				break;
			}
			$href = '/';
			for($i=1;$i<=3;$i++){
				if(${'cat'.$i} === NULL){
					break;
				}
				$href.= ${'cat'.$i}.'/';
			}
			
			$items[$ID]['PID']		= $PID;
			$items[$ID]['DD']		= $dd;
			$items[$ID]['div']		= $div;
			$items[$ID]['title']	= $title;
			$items[$ID]['href']		= $href;
			$items[$ID]['cat1']		= $cat1;
			$items[$ID]['cat2']		= $cat2;
			$items[$ID]['cat3']		= $cat3;
			$items[$ID]['children'] = array();
		}
		return $items;
	}
	
	private function _fetchChildItems($parents){
		$item_query = $this->mySQL_r->prepare("SELECT `MID`, `PID`, `divider`, `title`, `cat1`, `cat2`, `cat3` FROM `core_menu` LEFT JOIN `core_pages` ON `PID`=`ID` WHERE `parent`=? ORDER BY `position` ASC");
		$item_query->bind_result($ID, $PID, $div, $title, $cat1, $cat2, $cat3); 
		$user = $this->parent->parent->user;
		foreach($parents as $P_ID=>$parentItem){
			if($PID===NULL) $PID = -1;
			if(!$parentItem['DD'] || !$user->can_accessPage($PID)){
				continue;
			}
			
			$item_query->bind_param('i', $P_ID);
			$item_query->execute();
			$item_query->store_result();
			$this->parent->parent->debug($this::name_space.': There are '.$item_query->num_rows.' sub menu items for ID '.$P_ID);
			$items = array();
			while($item_query->fetch()){
				$href = '/';
				for($i=1;$i<=3;$i++){
					if(${'cat'.$i} === NULL || ${'cat'.$i} == '*'){
						break;
					}
					$href.= ${'cat'.$i}.'/';
				}
				
				$items[$ID]['PID']		= $PID;
				$items[$ID]['div']		= $div;
				$items[$ID]['title']	= $title;
				$items[$ID]['href']		= $href;
				$items[$ID]['cat1']		= $cat1;
				$items[$ID]['cat2']		= $cat2;
				$items[$ID]['cat3']		= $cat3;
			}
			$parents[$P_ID]['children'] = $items;
		}
		return $parents;
	}
	
	
	private function _openNavbar(){
		$this->parent->parent->debug($this::name_space.': Opening navbar');
		
		$cdn =$this->parent->getCDN();
		
		if(WebApp::get('cat1')=='admin'&&$this->parent->parent->user->is_loggedIn()){
			$navbar = '<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">'.PHP_EOL;
		}else{
			$navbar = '<nav class="navbar navbar-default navbar-fixed-top" role="navigation">'.PHP_EOL;
		}
		$navbar.= '  <div class="container">'.PHP_EOL;
		$navbar.= '    <div class="navbar-header">'.PHP_EOL;
		$navbar.= '      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".nav-collapse">'.PHP_EOL;
		for($i=1;$i<=3;$i++){
			$navbar.= '        <span class="icon-bar"></span>'.PHP_EOL;
		}
		$navbar.= '      </button>'.PHP_EOL;
		$navbar.= '      <a class="navbar-brand" href="/">BWSC</a>'.PHP_EOL;
		$navbar.= '    </div>'.PHP_EOL;
		$navbar.= '    <div class="nav-collapse collapse navbar-collapse">'.PHP_EOL;
		$navbar.= '      <ul class="nav navbar-nav navbar-right" id="logos">'.PHP_EOL;
		$navbar.= '        <li><div class="text-center logo"><a href="/"><img src="'.$cdn.'images/core/logo/BWSCLOGO_x50.png" /></a></div></li>'.PHP_EOL;
		if(WebApp::get('cat1')=='admin'&&$this->parent->parent->user->is_loggedIn()){
			$navbar.= '        <li class="hidden-sm"><div class="text-center logo"><a href="/"><img src="'.$cdn.'images/core/logo/jordanslogo_inv.png"/></a></div></li>'.PHP_EOL;
		}else{
			$navbar.= '        <li class="hidden-sm"><div class="text-center logo"><a href="/"><img src="'.$cdn.'images/core/logo/jordanslogo.png"/></a></div></li>'.PHP_EOL;
		}
		$navbar.= '      </ul>'.PHP_EOL;
		$navbar.= '      <ul class="nav navbar-nav navbar-left">'.PHP_EOL;
		
		return $navbar;
	}
	
	private function _closeNavbar(){
		$this->parent->parent->debug($this::name_space.': Closing navbar');
		$navbar = '      </ul>'.PHP_EOL;
		$navbar.= '    </div>'.PHP_EOL;
		$navbar.= '  </div>'.PHP_EOL;
		$navbar.= '</nav>'.PHP_EOL;
		return $navbar;
	}
	
	private function _generateNavbar(){
		$this->parent->parent->debug($this::name_space.': Generating navbar...');
		
		// It is strongly suggested that you do not 'play' with this chunk of code... doing so will cause complete misery and brain-ache for a long time
		if(count($this->navbarItems)==0){
			return;
		}
		
		$user = $this->parent->parent->user;
		
		$navbar = '';
		
		foreach($this->navbarItems as $parent){
			if(
				$parent['DD'] == 0
				||count($parent['children']) == 0
			){
				$navbar.= $this->_getItem($parent);
				continue;
			}
			
			$navbar.= $this->_getDropdown($parent);
			foreach($parent['children'] as $child){
				if($child['PID'] == NULL && $child['div']){
					$navbar .= $this->_getDivider();
					continue;
				}
				if($child['div']){
					$navbar.= $this->_getDivider();
					continue;
				}
				$navbar .= $this->_getItem($child);
			}
			$navbar .= $this->_closeDropdown();
		}
		$this->navbar.= $navbar;
		// You are free to fiddle now! I hope that didn't cause too much trouble :P
	}
	
	private function _closeLI(){
		return '        </li>'.PHP_EOL;
	}
	
	private function _closeUL(){
		return '        </ul>'.PHP_EOL;
	}
	
	private function _getDivider(){
		$this->parent->parent->debug($this::name_space.': Adding divider');
		return '        <li class="divider"></li>'.PHP_EOL;
	}
	
	private function _getItem($item){
		$this->parent->parent->debug($this::name_space.': Adding item');
		$page = $this->parent;
		
		$element = '        <li';
		if(
			WebApp::get('cat1')		== $item['cat1']
			&&WebApp::get('cat2')	== $item['cat2']
		){
			$element .= ' class="active"';
		}
		$element .= '><a href="'.$item['href'].'">'.$item['title'].'</a></li>'.PHP_EOL;
		return $element;
	}

	private function _getDropdown($item){
		$this->parent->parent->debug($this::name_space.': Adding dropdown');
		$page = $this->parent;
		
		$element = '        <li class="dropdown';
		if(
			WebApp::get('cat1')			== $item['cat1']
			||(
				WebApp::get('cat2')		== $item['cat2']
				&&WebApp::get('cat1')	== $item['cat1']
			)
		){
			$element .= ' active';
		}
		$element .= '"><a href="'.$item['href'].'" class="dropdown-toggle" data-toggle="dropdown">'.$item['title'].' <b class="caret"></b></a>'.PHP_EOL;
		$element .= '        <ul class="dropdown-menu">'.PHP_EOL;
		return $element;
	}
	
	private function _closeDropdown(){
		$this->parent->parent->debug($this::name_space.': Closing dropdown');
		$page = $this->parent;
		$element = $this->_closeUL();
		$element.= $this->_closeLI();
		return $element;
	}
}

?>