<?php
/**
 * Core Cron Class
 *
 * @category   Module.Core.Cron
 * @package    core/cron.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */

class CronController extends BaseCron
{
	const	 name_space	 = 'Module.Core';
	const	 version	 = '1.0.0';
	
	public function removeBans(){
		$result = $this->mySQL_w->query("DELETE FROM `core_ip` WHERE NOW()>DATE_ADD(`time`, INTERVAL `length` DAY)");
		if($result !== false){
			$msg = 'Removed expired bans';
		}else{
			$msg = 'Failed to remove expired bans';
		}
		return new CronResult(
			$this,
			$result,
			$msg
		);
	}
	
	public function protectIdle(){
		$result = $this->mySQL_w->query(
"UPDATE `core_sessions`
SET `auth`=1
WHERE NOW()>DATE_ADD(`lpr`, INTERVAL (SELECT `value` FROM `core_options` WHERE `name`='core_idle') SECOND)");
		if($result !== false){
			$msg = 'Protected idle accounts';
		}else{
			$msg = 'Failed to protect idle accounts';
		}
		return new CronResult(
			$this,
			$result,
			$msg
		);
	}
	
	public function cleanseTemp(){
		rrmdir(__TEMP__);
		mkdir(__TEMP__);
		return new CronResult(
			$this,
			true,
			'Cleaned the temp folder'
		);
	}
}
?>