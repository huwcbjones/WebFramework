<?php
/**
 * Base Action
 *
 * @category   WebApp.Base.Cron
 * @package    class.basecron.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 *
 */

class BaseCron extends BaseCtrl
{
	const	 name_space	 = 'WebApp.Base.Action';
	const	 version	 = '1.0.0';

	public $result = '';

	function processCron($action){
		if(is_callable(array($this,$action))){
			$this->result = $this->$action();
			return $this->result->status;;
		}else{
			$this->result = new CronResult(
				$this,
				0,
				'Failed to run Cron Job, job not found.'
			);
			return false;
		}
	}
}
?>