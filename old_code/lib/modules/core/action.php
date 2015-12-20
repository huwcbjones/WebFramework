<?php

/**
 * Core Action Class
 *
 * @category   Module.Core.Action
 * @package    core/action.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

class ActionController extends BaseAction
{
	const	 name_space	 = 'Module.Core';
	const	 version	 = '1.0.0';
	
	public function clear_status_msg(){
		$msg_id = WebApp::get('msg_id');
		if($msg_id === NULL){
			$msg_id = WebApp::post('msg_id');
		}
		if($msg_id === NULL){
			$this->parent->parent->debug($this::name_space.': MSG ID was not provided!');
			return new ActionResult(
				$this,
				'/',
				0,
				'Failed to clear status message. No ID found.',
				B_T_FAIL
			);
		}
		$msg_id = trim(str_replace('alert_', '', $msg_id));
		$msg_id = base64_decode($msg_id);
		Session::del('status_msg', $msg_id);
		$this->parent->parent->debug($this::name_space.': MSG ID "'.$msg_id.'" was '.((Session::get('status_msg', $msg_id)===NULL)?'':'not ').'cleared');
		return new ActionResult(
			$this,
			'/',
			0,
			'Cleared status message.',
			B_T_SUCCESS
		);
	}
	
	public function event_clear(){
		if($this->inGroup(1)){
			require dirname(__FILE__) . '/resources/eventlog.action.php';
			$eventlog = new EventlogAction($this->parent);
			return $eventlog->clear();
		}else{
			return new ActionResult($this, '/admin/core/event_log', 0, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function event_delete(){
		if($this->inGroup(1)){
			require dirname(__FILE__) . '/resources/eventlog.action.php';
			$eventlog = new EventlogAction($this->parent);
			return $eventlog->delete();
		}else{
			return new ActionResult($this, '/admin/core/event_log', 0, 'You are not allowed to do that', B_T_FAIL);
		}
	}

	public function ipblock_add(){
		if($this->accessAdminPage(41)){
			require dirname(__FILE__) . '/resources/ipblock.action.php';
			$ipblock = new IpblockAction($this->parent);
			return $ipblock->add();
		}else{
			return new ActionResult($this, '/admin/core/ipblock_view', 0, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function ipblock_edit(){
		if($this->accessAdminPage(42)){
			require dirname(__FILE__) . '/resources/ipblock.action.php';
			$ipblock = new IpblockAction($this->parent);
			return $ipblock->edit();
		}else{
			return new ActionResult($this, '/admin/core/ipblock_view', 0, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function ipblock_delete(){
		if($this->inGroup(43, true)){
			require dirname(__FILE__) . '/resources/ipblock.action.php';
			$ipblock = new IpblockAction($this->parent);
			return $ipblock->delete();
		}else{
			return new ActionResult($this, '/admin/core/ipblock_view', 0, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function option_add(){
		if($this->accessAdminPage(12)){
			require dirname(__FILE__) . '/resources/option.action.php';
			$option = new OptionAction($this->parent);
			return $option->add();
		}else{
			return new ActionResult($this, '/admin/core/option_view', 0, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function option_edit(){
		if($this->accessAdminPage(13)){
			require dirname(__FILE__) . '/resources/option.action.php';
			$option = new OptionAction($this->parent);
			return $option->edit();
		}else{
			return new ActionResult($this, '/admin/core/option_view', 0, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function option_delete(){
		if($this->inGroup(1)){
			require dirname(__FILE__) . '/resources/option.action.php';
			$option = new OptionAction($this->parent);
			return $option->delete();
		}else{
			return new ActionResult($this, '/admin/core/option_view', 0, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function config_edit(){
		if($this->accessAdminPage(21)){
			require dirname(__FILE__) . '/resources/config.action.php';
			$config = new ConfigAction($this->parent);
			return $config->save();
		}else{
			return new ActionResult($this, '/admin/core/config_view', 0, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function cron_edit(){
		if($this->accessAdminPage(52)){
			require dirname(__FILE__) . '/resources/cron.action.php';
			$cron = new CronAction($this->parent);
			return $cron->edit();
		}else{
			return new ActionResult($this, '/admin/core/cron_view', 0, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function cron_run(){
		if($this->inGroup(54, true)){
			require dirname(__FILE__) . '/resources/cron.action.php';
			$cron = new CronAction($this->parent);
			return $cron->run();
		}else{
			return new ActionResult($this, '/admin/core/cron_view', 0, 'You are not allowed to do that', B_T_FAIL);
		}
	}

	public function cron_logclear(){
		if($this->inGroup(1)){
			require dirname(__FILE__) . '/resources/cron.action.php';
			$cron = new CronAction($this->parent);
			return $cron->log_clear();
		}else{
			return new ActionResult($this, '/admin/core/cron_log', 0, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function cron_logdelete(){
		if($this->inGroup(1)){
			require dirname(__FILE__) . '/resources/cron.action.php';
			$cron = new CronAction($this->parent);
			return $cron->log_delete();
		}else{
			return new ActionResult($this, '/admin/core/cron_log', 0, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function menu_add(){
		if($this->accessAdminPage(61)){
			require dirname(__FILE__) . '/resources/menu.action.php';
			$menu = new MenuAction($this->parent);
			return $menu->add();
		}else{
			return new ActionResult($this, '/admin/core/menu_view', 0, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function menu_addSub(){
		if($this->accessAdminPage(62)){
			require dirname(__FILE__) . '/resources/menu.action.php';
			$menu = new MenuAction($this->parent);
			return $menu->addSub();
		}else{
			return new ActionResult($this, '/admin/core/menu_view', 0, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function menu_edit(){
		if($this->accessAdminPage(63)){
			require dirname(__FILE__) . '/resources/menu.action.php';
			$menu = new MenuAction($this->parent);
			return $menu->edit();
		}else{
			return new ActionResult($this, '/admin/core/menu_view', 0, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function menu_up(){
		if($this->accessAdminPage(63)){
			require dirname(__FILE__) . '/resources/menu.action.php';
			$menu = new MenuAction($this->parent);
			return $menu->up();
		}else{
			return new ActionResult($this, '/admin/core/menu_view', 0, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function menu_down(){
		if($this->accessAdminPage(63)){
			require dirname(__FILE__) . '/resources/menu.action.php';
			$menu = new MenuAction($this->parent);
			return $menu->down();
		}else{
			return new ActionResult($this, '/admin/core/menu_view', 0, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function menu_remove(){
		if($this->inGroup(63, true)){
			require dirname(__FILE__) . '/resources/menu.action.php';
			$menu = new MenuAction($this->parent);
			return $menu->remove();
		}else{
			return new ActionResult($this, '/admin/core/menu_view', 0, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function menu_removeSub(){
		if($this->inGroup(63, true)){
			require dirname(__FILE__) . '/resources/menu.action.php';
			$menu = new MenuAction($this->parent);
			return $menu->remove();
		}else{
			return new ActionResult($this, '/admin/core/menu_view', 0, 'You are not allowed to do that', B_T_FAIL);
		}
	}
}
?>