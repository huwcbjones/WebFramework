<?php

/**
 * Cron Result
 *
 * @category   WebApp.Base.Cron.Result
 * @package    class.cronresult.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 * 
 */

class CronResult extends Base
{
	const	 name_space	 = 'WebApp.Base.Cron.Result';
	const	 version	 = '1.0.0';

	public $msg		= '';
	public $status	= 0;
	
	function __construct($parent, $status, $msg){
		$this->parent 	= $parent;
		$this->status	= $status;
		$this->msg		= $msg;
	}
	
	function getResult(){
		return $this->result;
	}

}
?>