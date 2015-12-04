<?php
/**
 * Event Log Action Class
 *
 * @category   Module.Core.Action.Eventlog
 * @package    core/resources/eventlog.action.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */

class EventlogAction extends BaseAction
{
	const	 name_space	 = 'Module.Core';
	const	 version	 = '1.0.1';
	
	public function clear(){
		$truncate = $this->mySQL_w->prepare("TRUNCATE `core_log`");
		$truncate->execute();
		$this->parent->parent->logEvent($this::name_space, 'Truncated the event log');
		return new ActionResult($this, '/admin/core/event_log', 1, 'Cleared the event log!', B_T_SUCCESS);
	}
	
	public function delete(){
		$events	= (WebApp::post('events')===NULL)?	array()	:strgetcsv(WebApp::post('events'));
		
		if(count($events)==0){
			return new ActionResult($this, '/admin/core/event_log', 0, 'No events(s) were selected!', B_T_FAIL);
		}
		
		foreach($events as $event){
			$validated = GUMP::is_valid(array('evt'=>$event), array('evt'=>'integer'));
			if($validated!==true){
				return new ActionResult($this, '/admin/core/event_log', 0, 'No events(s) were selected!', B_T_FAIL); 
			}
		}

		$delete = $this->mySQL_w->prepare("DELETE FROM `core_log` WHERE `id`=?");
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
				'/admin/core/event_log',
				1,
				'Successfully deleted selected event(s)!',
				B_T_SUCCESS
			);
		}else{
			$this->parent->parent->logEvent($this::name_space, 'Deleted some events '.csvgetstr($events));
			return new ActionResult(
				$this,
				'/admin/core/event_log',
				1,
				'Successfully deleted '.$affected_rows.'/'.count($events).' selected events(s)!<br /><small>Possible cause: <code>Unknown</code></small>',
				B_T_WARNING
			);
		}
	}
}
?>