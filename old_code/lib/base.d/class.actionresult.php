<?php

/**
 * Action Result
 *
 * @category   WebApp.Base.Action.Result
 * @package    class.actionresult.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
 *
 */

class ActionResult extends Base
{
	const	 name_space	 = 'WebApp.Base.Action.Result';
	const	 version	 = '1.0.0';

	public $id		= 0;
	public $url		= '';
	public $msg		= '';
	public $status	= 0;
	public $msgType	= '';
	public $data	= array();
	public $result	= array();
	public $form	= array();
	
	function __construct($parent, $url, $status, $msg, $type='', $data=array()){
		$this->parent 	= $parent;
		$this->id		= microtime(true);
		$this->url		= $url;
		$this->status	= $status;
		$this->msg		= $msg;
		$this->msgStr	= $msg;
		$this->msgType	= $type;
		if(array_key_exists('form', $data)){
			$this->form	= $data['form'];
			unset($data['form']);
		}
		$this->data		= $data;
		$this->createResult();
	}
	
	function getResult(){
		return $this->result;
	}
	
	function createResult(){
		if($this->msg!=''){
			if(is_object($this->parent)&&is_callable(array($this->parent, 'getPlugin'))){
				$alert = $this->parent->getPlugin('alert');
			}elseif(is_object($this->parent->parent)&&is_callable(array($this->parent->parent, 'getPlugin'))){
				$alert = $this->parent->parent->getPlugin('alert');
			}
		}
		if(isset($alert)){
			$msg = $alert->setAlert($this->msg, $this->msgType, $this->id)->getAlert();
		}else{
			$msg = $this->msg;
		}
		$result['url']		= $this->url;
		$result['status']	= $this->status;
		$result['msg']		= $msg;
		$result['msgStr']	= $this->msgStr;
		$result['data']		= $this->data;
		$result['id']		= base64_encode($this->id);
		if(count($this->form)!=0){
			$result['form']		= $this->form;
		}
		if($this->status==1){
			Session::set('status_msg', $this->id, $msg);
		}
		$this->result		= $result;
	}
}
?>