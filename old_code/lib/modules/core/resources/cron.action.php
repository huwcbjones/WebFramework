<?php

/**
 * Cron Action Class
 *
 * @category   Module.Core.Action.Cron
 * @package    core/resources/cron.action.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

class CronAction extends BaseAction
{
	const	 name_space	 = 'Module.Core';
	const	 version	 = '1.0.1';
	

	public function edit(){
		
		$gump = new GUMP();
		
		$gump->validation_rules(array(
			'id'=>'required|integer|min_len,1',
			'user'=>'required|integer',
			'enabled'=>'required|integer',
			'mins'=>'required',
			'hours'=>'required',
			'days'=>'required',
			'months'=>'required',
			'DoW'=>'required',
		));
		
		$gump->filter_rules(array(
			'id'=>'trim|whole_number'
		));
		
		$valid_data = $gump->run($_POST);
		
		if($valid_data === false){
			return new ActionResult(
				$this,
				'/admin/core/cron_view',
				0,
				'Failed to edit cron job!<br />Error: <code>Please check you have completed all fields as instructed.</code>',
				B_T_FAIL
			); 
		}
		
		foreach(array('mins', 'hours', 'days', 'months', 'DoW') as $var){
			if($valid_data[$var] === '*') $valid_data[$var] = NULL;
		}
		$update_query = $this->mySQL_w->prepare("UPDATE `core_cron` SET `enable`=?, `mins`=?, `hours`=?, `days`=?, `month`=?, `dow`=?, `user_id`=? WHERE `ID`=?");
		if($update_query === false){
			return new ActionResult(
				$this,
				'/admin/core/cron_view',
				0,
				'Failed to edit cron job.<br/>Error: <code>Update query failed</code>',
				B_T_FAIL
			);
		}
		$update_query->bind_param('iiiiiiii', $valid_data['enabled'], $valid_data['mins'], $valid_data['hours'], $valid_data['days'], $valid_data['months'], $valid_data['DoW'], $valid_data['user'], $valid_data['id']);
		$update_query->execute();
		if($update_query->affected_rows==1){
			return new ActionResult(
				$this,
				'/admin/core/cron_view',
				1,
				'Succeesfully edited cron job!',
				B_T_SUCCESS
			);
		}else{
			return new ActionResult(
				$this,
				'/admin/core/cron_view',
				1,
				'Tried to edit cron job, but there was nothing to change!',
				B_T_INFO
			);
		}
	}
	
	public function run(){
		$jobID = WebApp::get('j');
		$this->parent->parent->_loadCron();
		$cron = $this->parent->parent->cron;
		$cron->loadJobs();
		$job = $cron->getJob($jobID);
		
		if($job===false){
			return new ActionResult(
				$this,
				'/admin/core/cron_view',
				0,
				'Failed to run cron job!<br />Error: <code>Job wasn\'t found</code>',
				B_T_FAIL
			);
		}
		$result = $cron->runJob($jobID);
		if($result->status){
			return new ActionResult(
				$this,
				'/admin/core/cron_view',
				1,
				'Job ran successfully!',
				B_T_SUCCESS
			);
		}else{
			return new ActionResult(
				$this,
				'/admin/core/cron_view',
				1,
				'Failed to run job!<br /><Error: <code>'.$result->msg.'</code>',
				B_T_INFO
			);
		}
	}
	
	public function log_clear(){
		$truncate = $this->mySQL_w->prepare("TRUNCATE `core_cronlog`");
		$truncate->execute();
		$this->parent->parent->logEvent($this::name_space, 'Truncated the cron log');
		return new ActionResult($this, '/admin/core/cron_log', 1, 'Cleared the cron log!', B_T_SUCCESS);
	}
	
	public function log_delete(){
		$events	= (WebApp::post('events')===NULL)?	array()	:strgetcsv(WebApp::post('events'));
		
		if(count($events)==0){
			return new ActionResult($this, '/admin/core/cron_log', 0, 'No events(s) were selected!', B_T_FAIL);
		}
		
		foreach($events as $event){
			$validated = GUMP::is_valid(array('evt'=>$event), array('evt'=>'integer'));
			if($validated!==true){
				return new ActionResult($this, '/admin/core/cron_log', 0, 'No events(s) were selected!', B_T_FAIL); 
			}
		}

		$delete = $this->mySQL_w->prepare("DELETE FROM `core_cronlog` WHERE `id`=?");
		$affected_rows = 0;
		foreach($events as $id){
			$delete->bind_param('i', $id);
			$delete->execute();
			$delete->store_result();
			$affected_rows += $delete->affected_rows;
		}
		
		if($affected_rows == count($events)){
			$this->parent->parent->logEvent($this::name_space, 'Deleted events '.csvgetstr($events));
			return new ActionResult(
				$this,
				'/admin/core/cron_log',
				1,
				'Successfully deleted selected event(s)!',
				B_T_SUCCESS
			);
		}else{
			$this->parent->parent->logEvent($this::name_space, 'Deleted some events '.csvgetstr($events));
			return new ActionResult(
				$this,
				'/admin/core/cron_log',
				1,
				'Successfully deleted '.$affected_rows.'/'.count($events).' selected events(s)!<br /><small>Possible cause: <code>Unknown</code></small>',
				B_T_WARNING
			);
		}
	}
}
?>