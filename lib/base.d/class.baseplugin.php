<?php

/**
 * Base Plugin
 *
 * @category   WebApp.Base.Plugin
 * @package    class.baseplugin.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 *
 */

class BasePlugin extends Base
{
	const		name_space		= 'WebApp.BasePlugin';
	const		version			= '1.0.0';
	
	private		$_indent = '';
	
	protected function _setIndent($indent){
		if(!intval($indent)){
			$indent = strlen($indent);
		}
		$this->parent->parent->debug($this::name_space.': Set indent to '.$indent.' spaces');
		$space = '';
		for($s=1; $s<=$indent; $s++){$space.= ' ';}
		
		$this->_indent = $space;
		return $this;
	}
	function setIndent($indent){
		return $this->_setIndent($indent);
	}
}
?>