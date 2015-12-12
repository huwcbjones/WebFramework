<?php
/**
 * HTTP Status Error Generator
 *
 * @category   WebApp.Error
 * @package    __LIBDIR__/error.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 */
class Alert extends BasePlugin
{
	const		name_space		= 'Plugins.Bootstrap.Alert';
	const		version			= '1.0.0';
	
	protected $alert;
	protected $msg;
	protected $type = '';
	protected $id = '';
	protected $b64enc = true;
	
	function setAlert($msg, $type='', $id='', $b64enc = true){
		$this->msg = $msg;
		$this->type = $type;
		if($id==''){
			$id = microtime(true);
		}
		$this->id = $id;
		return $this;
	}
	
	function getAlert($container=true){
		$this->_processMsg();
		if($container){
			$this->alert = '<div class="row">'.PHP_EOL.$this->alert.PHP_EOL.'</div>'.PHP_EOL;
		}
		return $this->alert;
		
	}
	
	function getID(){
		return $this->id;
	}
	private function _processMsg(){
		$id = $this->id;
		if($this->b64enc){
			$id = base64_encode($id);
		}
		if($this->type=='') $this->type='danger';
		$message = '  <div class="alert alert-'.$this->type.' fade in" id="alert_'.$id.'">'.PHP_EOL;
		$message.= '    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.PHP_EOL;
		$message.= '    '.$this->msg.PHP_EOL;
		$message.= '  </div>'.PHP_EOL;
		$this->alert = $message;
	}
}
?>