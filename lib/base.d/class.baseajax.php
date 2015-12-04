<?php

/**
 * Base Ajax
 *
 * @category   WebApp.Base.Controller.Ajax
 * @package    base.ajax.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
 *
 */

class BaseAjax extends BaseController
{
	const	 name_space	 = 'WebApp.Base.Controller.Ajax';
	const	 version	 = '1.0.0';

	public $result = '';

	function processAjax(){
		$ajax = WebApp::get('cat3');
		if(WebApp::get('cat4') !== NULL){
			$ajax.= '_'.WebApp::get('cat4');
		}
		if(is_callable(array($this,$ajax))){
			$this->result = $this->$ajax();
			return true;
		}else{
			$this->result = new
			ActionResult(
				$this,
				$_SERVER['REQUEST_URI'],
				0,
				'Whoops, something went wrong with that action and we\'re trying to fix it. <br />Error: <code>Ajax not found</code>'
			);
			return false;
		}
	}
	
	function returnResult($url, $redirect, $msg, $type='', $data=array()){
		if($msg!=''){
			$alert = $this->parent->getPlugin('alert');
			$msg = $alert->setAlert($msg, $type)->getAlert();
		}
		$result['url']		= $url;
		$result['status']	= $redirect;
		$result['msg']		= $msg;
		$result['data']		= $data;
		return $result;
	}
}
?>