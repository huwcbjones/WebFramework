<?php

/**
 * Base Controller
 *
 * @category   WebApp.Base.Ctrl
 * @package    class.basectrl.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 * 
 */

class BaseCtrl extends Base
{
	const		name_space		= 'WebApp.Base.Ctrl';
	const		version			= '1.0.0';
	
	public function setStatus($httpStatusCode){
		$this->parent->debug($this::name_space.': HTTP Status changed to "'.$httpStatusCode.'"!');
		$this->parent->http_status = $httpStatusCode;
		$page['title'] = 'Error ' . $httpStatusCode;
		$page['num'] = -1;
		$page['desc'] = $this->parent->config->getOption('site_description');
		$page['intro'] = '';
		$page['data'] = '';
		$this->pageDetails = $page;
	}
	
	public function getStatus(){
		return $this->parent->http_status;
	}
	
	public function checkHTTPS($https)
	{
		$this->parent->debug($this::name_space . ': Checking HTTPS settings for page...');
		if (
			$this->parent->https !== true
			&& $this->parent->config->config['core']['https']['a']
			&& $https
		) {
			$location = 'https://' . Server::get('HTTP_Host') . Server::get('Request_URI');
			$this->parent->debug($this::name_space . ': HTTPS turned on... follow link: ' . $location);
			if (!$this->parent->debug) {
				header('Location: ' . $location);
				exit();
			}
		} else {
			$this->parent->debug($this::name_space . ': HTTPS left as it is.');
		}
	}
	
	public function getResource($module, $args=array()){
		$module = strtolower($module);
		$this->parent->debug($this::name_space.': Loading resource "/__MODULE__/'.$module.'/resource.php"');
		if(!file_exists(__MODULE__ . '/'.$module.'/resource.php')){
			$this->parent->debug($this::name_space.': Could not find "resource.php"!');
			return false;
		}

		if( !@include_once __MODULE__ . '/'.$module.'/resource.php'){
			$this->parent->debug($this::name_space.': Could not access "resource.php"! Check r/w permissions');
			return false;
		}

		$class = ucfirst($module).'Resource';
		if(!class_exists($class)){
			$this->parent->debug($this::name_space.': Could not find class "'.$class.'"!');
			return false;
		}

		$this->parent->debug($this::name_space.': Returning new "'.$class.'"');
		array_unshift($args, $this);
		$r = new ReflectionClass($class);
		$resource = $r->newInstanceArgs($args);
		return $resource;
	}
	
	public function getPlugin($plugin, $args=array()){
		$this->parent->debug($this::name_space.': Loading plugin "/__PLUGIN__/'.$plugin.'/plugin.php"');
		$class = ucfirst($plugin);
		if(!class_exists($class)){
			$this->parent->debug($this::name_space.': Could not find plugin class "'.$class.'"!');
			return false;
		}

		$this->parent->debug($this::name_space.': Returning new "'.$class.'"');
		array_unshift($args, $this);
		$r = new ReflectionClass($class);
		$resource = $r->newInstanceArgs($args);
		return $resource;
	}
	
	/**
	 * Page::getCDN()
	 * 
	 * @return
	 */
	public function getCDN()
	{
		return $this->parent->config->config['core']['cdn'];
	}
	
	public function inGroup($groupID, $superadmin=true){
		return $this->parent->user->is_inGroup($groupID, $superadmin);
	}
}

?>
