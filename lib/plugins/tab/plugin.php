<?php

/**
 * HTML Tab Pane Builder Class
 *
 * Creates tabs
 *
 * @category   Plugins.Bootstrap.Tab
 * @package    tab.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
*/
class Tab extends BasePlugin
{
	const name_space = 'Plugins.Bootstrap.Tab';
	const version = '1.0.0';

	private $_tab = '';
	private $_pages = array();
	private $_nav = '';
	private $_tabs = '';

	private $_built = false;

	function __construct($parent)
	{
		$this->parent = $parent;
		$this->parent->parent->debug('***** ' . $this::name_space . ' *****');
		$this->parent->parent->debug($this::name_space . ': Version ' . $this::version);
	}
	
	public function setIndent($indent){
		$this->_setIndent($indent);
		return $this;
	}
	
	public function addPage($id, $title, $active=false, $disabled=false, $content='')
	{
		$this->parent->parent->debug($this::name_space . ': Adding page ('.$id.')');
		$this->_pages[$id] = array(
			'title'		=>$title,
			'active'	=>$active,
			'disabled'	=>$disabled,
			'content'	=>$content
		);
		return $this;
	}
	
	public function addPageContent($id, $content){
		if(array_key_exists($id, $this->_pages)){
			$this->_pages[$id]['content'] = $content;
			return $this;
		}
		return false;
	}

	public function build()
	{
		$this->parent->parent->debug($this::name_space . ': Building tab panes...');

		$this->_nav = $this->_buildNav();
		
		$this->_tabs = $this->_buildTabs();

		$this->_tab = $this->_nav . $this->_tabs;

		$this->_built = true;
	}

	public function getTab()
	{
		if (!$this->_built) {
			trigger_error('Call Tab::build() before Tab::getTab()', E_USER_WARNING);
		}
		return $this->_tab;
	}

	private function _buildNav(){
		$nav = $this->_indent.'<ul class="nav nav-tabs nav-justified" role="tablist">'.PHP_EOL;
		
		foreach($this->_pages as $pageID=>$page){
			$unit = $this->_indent.'  <li';
			if($page['disabled']){
				$unit.= ' class="disabled"';
			}else{
				if($this->is_tabActive($pageID, $page['active'])){
					$unit.= ' class="active"';
				}
			}
			$unit.= '><a href="#'.$pageID.'" role="tab"';
			if(!$page['disabled']){
				$unit.= ' data-toggle="tab"';
			}
			$unit.= '>'.$page['title'].'</a></li>'.PHP_EOL;
			
			$nav.= $unit;
		}
		$nav.= $this->_indent.'</ul>'.PHP_EOL;
		return $nav;
	}
	
	private function _buildTabs(){
		$tabs = $this->_indent.'<div class="tab-content">'.PHP_EOL;
		
		foreach($this->_pages as $pageID=>$page){
			$unit = $this->_indent.'  <div role="tabpanel" class="tab-pane';
			if($this->is_tabActive($pageID, $page['active'])){
				$unit.= ' active';
			}
			$unit.= '" id="'.$pageID.'">'.PHP_EOL;
			$unit.= $page['content'];
			$unit.= $this->_indent.'  </div>'.PHP_EOL;
			$tabs.= $unit;
		}
		
		$tabs.= $this->_indent.'</div>'.PHP_EOL;
		return $tabs;
	}
	
	private function is_tabActive($pageID, $active){
		return (
			(
				WebApp::get('tp') === $pageID
			)
			||(
				$active
				&& (
					WebApp::get('tp') === null
					|| WebApp::get('tp') === $pageID
				)
			)
		);
	}

}

?>