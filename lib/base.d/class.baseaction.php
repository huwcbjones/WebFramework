<?php

/**
 * Base Action
 *
 * @category   WebApp.Base.Action
 * @package    class.baseaction.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 *
 */

class BaseAction extends BaseController
{
	const	 name_space	 = 'WebApp.Base.Action';
	const	 version	 = '1.0.0';

	public $result = '';
	
	function processAction(){
		$action = WebApp::get('cat3');
		if(is_callable(array($this,$action))){
			$this->result = $this->$action();
			return true;
		}else{
			$this->result = new
			ActionResult(
				$this,
				Server::get('Request_URI'),
				0,
				'Whoops, something went wrong with that action and we\'re trying to fix it. <br />Error: <code>Action not found: "'.Server::get('Request_URI').'"</code>'
			);
			return false;
		}
	}
}
?>