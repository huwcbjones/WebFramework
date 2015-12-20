<?php

/**
 * Cron Constructer
 *
 * @category   WebApp.Cron
 * @package    cron.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
*
*/

class Cron extends BaseCtrl
{
	const name_space = 'WebApp.Cron';
	const version = '1.0.0';

	public $parent;
	// MySQL Database Handles
	public $mySQL_r; // Read Handle
	public $mySQL_w; // Write Handle

	private $ctrl;

	private $content = array();

	private $result;
	private $jobs = array();
	private $activeJobs = array();
	
	public function loadJobs(){
		if(!$this->inGroup(1050)){
			echo "You are not allowed to load cron jobs\n";
			return false;
		}
		$this->parent->debug($this::name_space.': Loading cron jobs...');
		$query = $this->mySQL_r->prepare(
"SELECT `ID`, `enable`, `mins`, `hours`, `days`, `month`, `dow`, `user_id`, `namespace`, `action`, `core_cron`.`description`, `last_run` FROM `core_cron`
INNER JOIN `core_modules`
ON `module_id`=`mod_id`");
		if($query === false) return false;
		$query->execute();
		$query->store_result();
		$query->bind_result($ID, $e, $i, $h, $d, $m, $dow, $user, $mod, $act, $desc, $lr);
		$jobs = array();
		while($query->fetch()){
			$jobs[$ID]['ID']			= $ID;
			$jobs[$ID]['enabled']		= $e;
			$jobs[$ID]['minutes']		= $i;
			$jobs[$ID]['hours']			= $h;
			$jobs[$ID]['days']			= $d;
			$jobs[$ID]['months']		= $m;
			$jobs[$ID]['dow']			= $dow;
			$jobs[$ID]['user']			= $user;
			$jobs[$ID]['module']		= strtolower($mod);
			$jobs[$ID]['action']		= $act;
			$jobs[$ID]['description']	= $desc;
			$jobs[$ID]['last_run']		= $lr;
		}
		$this->jobs = $jobs;
		$this->parent->debug($this::name_space.': Loaded '.count($this->jobs).' job(s)');
		return true;
	}
	
	public function loadActiveJobs(){
		if(!$this->inGroup(1050)){
			echo "You are not allowed to load cron jobs\n";
			return false;
		}
		$this->loadJobs();
		foreach($this->jobs as $job){
			if(!$this->_matchMinutes($job))	continue;
			if(!$this->_matchHours($job))	continue;
			if(!$this->_matchDays($job))	continue;
			if(!$this->_matchMonths($job))	continue;
			if(!$this->_matchDOW($job))		continue;
			if(!$job['enabled'])			continue;
			$this->activeJobs[$job['ID']] = $job;
		}
		echo 'Loaded '.count($this->activeJobs)." active job(s)\n";
		return true;
	}
	
	public function runJob($ID){
		unset($this->ctrl);
		$this->result = NULL;
		if(!$this->inGroup(1054)){
			$this->parent->debug($this::name_space.': Cannot run job, you are not allowed to do that');
			return new CronResult(
				$this,
				0,
				'Cannot run Cron Job, you are not allowed to do that!'
			);
		}
		if (!$this->parent->config->config['core']['database']) {
			$this->parent->debug($this::name_space.': Cannot run job in maintenance mode');
			return new CronResult(
				$this,
				0,
				'Cannot run Cron Job in maintenance mode!'
			);
		}
		if(!array_key_exists($ID, $this->jobs)){
			$this->parent->debug($this::name_space . ': Job with that ID doesn\'t exist');
			return new CronResult(
				$this,
				0,
				'Job with that ID doesn\'t exist'
			);
		}
		if (!file_exists(__MODULE__ . '/' . $this->jobs[$ID]['module'] . '/cron.php')) {
			$this->parent->debug($this::name_space . ':  Could not find "cron.php"!');
			return new CronResult(
				$this,
				0,
				'Could not find "cron.php"'
			);
		}

		if (!include_once __MODULE__ . '/' . $this->jobs[$ID]['module'] . '/cron.php') {
			$this->parent->debug($this::name_space . ':  Could not access "cron.php"! Check r/w permissions');
			return new CronResult(
				$this,
				0,
				'Could not access "cron.php"! Check r/w permissions'
			);
		}

		if (class_exists('CronController')) {
			$this->ctrl = new CronController($this);
			$this->parent->debug($this::name_space . ': CronController loaded');
		} else {
			$this->parent->debug($this::name_space . ': Could not find CronController class in "cron.php"!');
			return new CronResult(
				$this,
				0,
				'Could not find CronController class in "cron.php"!'
			);
		}
		
		
		if ($this->ctrl->processCron($this->jobs[$ID]['action']) !== false) {
			$this->parent->debug($this::name_space . ': Getting cron result');
			$this->logJob($ID, 'Ran successfully');
		} else {
			$this->logJob($ID, 'Job failed with error: '.$this->ctrl->result->msg);
			$this->parent->debug($this::name_space .
				': Error occurred whilst processing cron job...');
		}
		$this->result = $this->ctrl->result;
		$this->parent->debug($this::name_space . ': Cron executed!');
		$this->_updateJob($ID);
		return $this->result;
		
	}

	private function logJob($jobID, $event)
	{
		$this->parent->debug($this::name_space . ': Logging cron event to cron log...');
		$userID = $this->parent->user->getUserID();
		$event_log = $this->mySQL_w->prepare("INSERT INTO `core_cronlog` (`user_id`,`job_id`, `event`) VALUES(?,?,?)");
		$event_log->bind_param('iis', $userID, $jobID, $event);
		$event_log->execute();
		$event_log->store_result();
		if ($event_log->affected_rows == 1) {
			return true;
		} else {
			$this->parent->debug($this::name_space.': '.$this->mySQL_w->error);
			return false;

		}
		$event_log->free_result();
	}
	
	private function _updateJob($ID){
		$query = $this->mySQL_w->prepare("UPDATE `core_cron` SET `last_run`=NOW() WHERE `ID`=?");
		if($query === false){
			return false;
		}
		$query->bind_param('i', $ID);
		$query->execute();
		$query->store_result();
		return ($query->affected_rows===1);
	}
	
	public function listJobs($full = false){
		$mask = "|%5.5s |%-50.50s |\n";
		printf($mask, 'ID', 'Description');
		foreach($this->jobs as $job){
			printf($mask, $job['ID'], $job['description']);
		}
	}
	
	public function listActiveJobs($full = false){
		$mask = "|%5.5s |%-50.50s |\n";
		printf($mask, 'ID', 'Description');
		foreach($this->activeJobs as $job){
			printf($mask, $job['ID'], $job['description']);
		}
	}
	
	public function getActiveJobs(){
		return $this->activeJobs;
	}
	
	public function getJob($ID){
		if(array_key_exists($ID, $this->jobs)){
			return $this->jobs[$ID];
		}else{
			return false;
		}
	}
	
	private function _matchMinutes($job){
		$mins = $job['minutes'];
		if($mins === NULL) return true;
		return ($mins == intval(date('i')));
	}
	
	private function _matchHours($job){
		$hours = $job['hours'];
		if($hours === NULL) return true;
		return ($hours == date('G'));
	}
	
	private function _matchDays($job){
		$days = $job['days'];
		if($days === NULL) return true;
		return ($days == date('j'));
	}
	
	private function _matchMonths($job){
		$months = $job['months'];
		if($months === NULL) return true;
		return ($months == date('n'));
	}
	
	private function _matchDOW($job){
		$dow = $job['dow'];
		if($dow === NULL) return true;
		if($dow == 0) $dow = 7;
		return ($dow == date('N'));
	}
}

?>