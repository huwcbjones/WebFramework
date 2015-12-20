<?php

/**
 * Base Page Controller
 *
 * @category   WebApp.Base.Controller
 * @package    class.basecontroller.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 *
 */

class BaseController extends Base
{
	const		name_space		= 'WebApp.Base.Controller';
	const		version			= '1.0.0';
	
	public		$MOD_ID;
	
	function __construct($parent){
		$this->parent = $parent;
		$this->mySQL_r = $parent->mySQL_r;
		$this->mySQL_w = $parent->mySQL_w;
		$this->parent->parent->debug('***** '.$this::name_space.' *****');
		$this->parent->parent->debug($this::name_space.': Version '.$this::version);
		
		if(!$this->parent->parent->config->config['core']['database']){
			return;
		}

		$module_query = $this->mySQL_r->prepare("SELECT `module_id` FROM `core_modules` WHERE `namespace`=?");
		$namespace = str_replace(array('Module.','.Controller'),'', $this::name_space);
		$module_query->bind_param('s', $namespace);
		$module_query->execute();
		$module_query->store_result();
		if($module_query->num_rows!=1){
			$this->parent->parent->debug($this::name_space.': Cannot find module... Is the module registered properly?');
			$this->parent->setStatus(500);
			return;
		}

		$module_query->bind_result($module_id);
		while($module_query->fetch()){
			$this->MOD_ID = $module_id;
		}
		$module_query->free_result();
		
	}
	function inGroup($groupID, $moduleGroup=false, $superadmin=true){
		if($moduleGroup){
			$groupID = ($this->MOD_ID*1000)+$groupID;
		}
		return $this->parent->parent->user->inGroup($groupID, $superadmin);
	}
	function accessPage($pageID){
		$pageID = ($this->MOD_ID*1000)+$pageID;
		return $this->parent->parent->user->can_accessPage($pageID);
	}
	function accessAdminPage($pageID){
		$pageID = pow(10, 6)+($this->MOD_ID*1000)+$pageID;
		return $this->parent->parent->user->can_accessPage($pageID);
	}
}

?>