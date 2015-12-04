<?php

/**
 * Base Class
 *
 * @category   WebApp.Base
 * @package    class.base.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 * 
 */

class Base
{
	const		name_space		= 'WebApp.Base';
	const		version			= '1.0.0';
	
	public	$parent;

	// MySQL Database Handles
	public	$mySQL_r;		// Read Handle
	public	$mySQL_w;		// Write Handle
	
	function __construct($parent){
		$this->__init($parent);
	}
	
	public function __init($parent){
		$this->parent = $parent;
		$this->mySQL_r = $parent->mySQL_r;
		$this->mySQL_w = $parent->mySQL_w;
		if(is_callable(array($parent, 'debug'), false)){
			$this->parent->debug('***** '.$this::name_space.' *****');
			$this->parent->debug($this::name_space.': Version '.$this::version);
		}elseif(is_callable(array($parent->parent, 'debug'), false)){
			$this->parent->parent->debug('***** '.$this::name_space.' *****');
			$this->parent->parent->debug($this::name_space.': Version '.$this::version);
		}else{
			$this->parent->parent->parent->debug('***** '.$this::name_space.' *****');
			$this->parent->parent->parent->debug($this::name_space.': Version '.$this::version);
		}
	}
}
?>