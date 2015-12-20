<?php
/**
 * Modules Cron Class
 *
 * @category   Module.Modules.Cron
 * @package    modules/cron.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */

class CronController extends BaseCron
{
	const	 name_space	 = 'Module.Modules';
	const	 version	 = '1.0.0';
	
	public function backup(){
		$modules = $this->mySQL_r->query("SELECT `namespace`, `module_id` FROM `core_modules` WHERE `backup`=1");
		if(!$modules){
			return new CronResult(
				$this,
				false,
				'Failed to query database for modules to backup'
			);
		}
		$location = __BACKUP__.DIRECTORY_SEPARATOR.date(DATET_BKUP).DIRECTORY_SEPARATOR;
		require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'backup.php');

		$results = array();

		while($d = $modules->fetch_assoc()){
			$backup = new Backup($this->parent);
			if(!$backup->setLocation($location)){
				return new CronResult(
					$this,
					false,
					'Failed to create backup dir: '.DIRECTORY_SEPARATOR.'backup'.str_replace(__BACKUP__, '', $location.$d['namespace'])
				);
			}
			
			if(!$backup->setID($d['module_id'])){
				return new CronResult(
					$this,
					false,
					'Failed to setID for '.$d['namespace']
				);
			}
			$results[$d['namespace']] = $backup->backup();
			unset($backup);
		}

		$msg = '';
		$status = true;
		foreach($results as $ns=>$data){
			$msg.= '"'.$ns.'": '.$data['msg'].PHP_EOL;
			if(!$data['s']) $status = false;
		}
		
		if($status){
			$msg = 'Backup job was completed for all modules!';
		}else{
			$msg = 'Backup job was completed but failed for some/all modules. Details as follows:'.PHP_EOL.$msg;
		}
		
		return new CronResult(
			$this,
			$status,
			$msg
		);
	}
}
?>