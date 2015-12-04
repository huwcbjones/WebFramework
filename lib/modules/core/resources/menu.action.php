<?php

/**
 * Menu Action Class
 *
 * @category   Module.Core.Action.Menu
 * @package    core/resources/menu.action.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

class MenuAction extends BaseAction
{
	const	 name_space	 = 'Module.Core';
	const	 version	 = '1.0.1';
	
		public function add(){
		$gump = new GUMP();
		
		$gump->validation_rules(array(
			'module'=>'required|integer|min_len,1',
			'PID'=>'required|integer',
			'dropdown'=>'boolean',
		));
		
		$gump->filter_rules(array(
			'module'=>'trim|whole_number',
			'PID'=>'trim|whole_number',
			'dropdown'=>'trim'
		));
		
		$valid_data = $gump->run($_POST);
		
		if($valid_data === false){
			return new ActionResult(
				$this,
				'/admin/core/menu_add',
				0,
				'Failed to add menu item!<br />Error: <code>Please check you have completed all fields as instructed.</code>',
				B_T_FAIL
			); 
		}
		$max_query = $this->mySQL_r->query("SELECT MAX(`position`) FROM `core_menu`");
		if(!$max_query){
			return new ActionResult(
				$this,
				'/admin/core/menu_add',
				0,
				'Failed to add menu.<br/>Error: <code>Failed to get next free position</code>',
				B_T_FAIL
			);
		}
		$max = $max_query->fetch_row();
		$max = $max[0] + 1;
		$add_query = $this->mySQL_w->prepare("INSERT INTO `core_menu` (`position`, `parent`, `PID`, `dropdown`, `divider` ) VALUES (?, NULL, ?, ?, 0)");
		if(!$add_query){
			return new ActionResult(
				$this,
				'/admin/core/menu_add',
				0,
				'Failed to add menu.<br/>Error: <code>Insert query failed</code>',
				B_T_FAIL
			);
		}
		$add_query->bind_param('iii', $max, $valid_data['PID'], $valid_data['dropdown']);
		$add_query->execute();
		if($add_query->affected_rows==1){
			return new ActionResult(
				$this,
				'/admin/core/menu_view',
				1,
				'Succeesfully add menu item!',
				B_T_SUCCESS
			);
		}else{
			return new ActionResult(
				$this,
				'/admin/core/menu_view',
				0,
				'Tried to add menu item, but failed!',
				B_T_FAIL
			);
		}
	}
	
	public function addSub(){
		$gump = new GUMP();
		
		$gump->validation_rules(array(
			'module'=>'required|integer|min_len,1',
			'PID'=>'required|integer',
			'parent'=>'required|integer',
		));
		
		$gump->filter_rules(array(
			'module'=>'trim|whole_number',
			'PID'=>'trim|whole_number',
			'parent'=>'trim|whole_number'
		));
		
		$valid_data = $gump->run($_POST);
		
		if($valid_data === false){
			return new ActionResult(
				$this,
				'/admin/core/menu_add',
				0,
				'Failed to add menu sub menu item.<br />Error: <code>Please check you have completed all fields as instructed.</code>',
				B_T_FAIL
			); 
		}
		$max_query = $this->mySQL_r->query("SELECT MAX(`position`) FROM `core_menu`");
		$parent_query = $this->mySQL_r->prepare("SELECT `MID` FROM `core_menu` WHERE `MID`=?");
		if(!$parent_query){
				return new ActionResult(
				$this,
				'/admin/core/menu_add',
				0,
				'Failed to add sub menu item.<br/>Error: <code>Query to check parent item exists failed</code>',
				B_T_FAIL
			);
		}
		if(!$max_query){
			return new ActionResult(
				$this,
				'/admin/core/menu_add',
				0,
				'Failed to add sub menu item.<br/>Error: <code>Failed to get next free position</code>',
				B_T_FAIL
			);
		}
		$parent_query->bind_param('i', $valid_data['parent']);
		$parent_query->execute();
		$parent_query->store_result();
		if($parent_query->num_rows !=1){
			return new ActionResult(
				$this,
				'/admin/core/menu_add',
				0,
				'Failed to add sub menu item.<br/>Error: <code>Failed to check parent exists</code>',
				B_T_FAIL
			);
		}
		$max = $max_query->fetch_row();
		$max = $max[0] + 1;
		$add_query = $this->mySQL_w->prepare("INSERT INTO `core_menu` (`position`, `parent`, `PID`, `dropdown`, `divider` ) VALUES (?, ?, ?, 0, 0)");
		if(!$add_query){
			return new ActionResult(
				$this,
				'/admin/core/menu_add',
				0,
				'Failed to add menu.<br/>Error: <code>Insert query failed</code>',
				B_T_FAIL
			);
		}
		$add_query->bind_param('iii', $max, $valid_data['parent'], $valid_data['PID']);
		$add_query->execute();
		if($add_query->affected_rows==1){
			return new ActionResult(
				$this,
				'/admin/core/menu_edit/'.$valid_data['parent'].'/?d=dropdown',
				1,
				'Succeesfully add sub menu item!',
				B_T_SUCCESS
			);
		}else{
			return new ActionResult(
				$this,
				'/admin/core/menu_addsub/'.$valid_data['parent'],
				0,
				'Tried to add sub menu item, but failed!',
				B_T_FAIL
			);
		}
	}
	
	public function edit(){
	}
	
	public function up(){
		$MID	= (WebApp::get('cat4')===NULL)?	''	:WebApp::get('cat4');
		$up_query = $this->mySQL_w->prepare(
"UPDATE
	`core_menu` INNER JOIN (SELECT `position` FROM `core_menu` WHERE `MID`=?) curr
	ON `core_menu`.`position` IN (curr.`position`, curr.`position`-1)
SET
	`core_menu`.`position` = CASE WHEN `core_menu`.`position`=curr.`position`
		THEN curr.`position`-1 ELSE curr.`position` END;");
		if(!$up_query){
			return new ActionResult(
				$this,
				Server::get('HTTP_Referer'),
				0,
				'Failed to swap item position!<br />Error: <code>Query failed</code>',
				B_T_FAIL
			);
		}
		$up_query->bind_param('i', $MID);
		
		$up_query->execute();
		if($up_query->affected_rows == 2){
			return new ActionResult(
				$this,
				Server::get('HTTP_Referer'),
				1,
				'Swapped positions!',
				B_T_SUCCESS
			);
		}else{
			return new ActionResult(
				$this,
				Server::get('HTTP_Referer'),
				0,
				'Failed to swap positions!',
				B_T_FAIL
			);
		}
	}
	
	public function down(){
		$MID	= (WebApp::get('cat4')===NULL)?	''	:WebApp::get('cat4');
		$up_query = $this->mySQL_w->prepare(
"UPDATE
	`core_menu` INNER JOIN (SELECT `position` FROM `core_menu` WHERE `MID`=?) curr
	ON `core_menu`.`position` IN (curr.`position`, curr.`position`+1)
SET
	`core_menu`.`position` = CASE WHEN `core_menu`.`position`=curr.`position`+1
		THEN curr.`position` ELSE curr.`position`+1 END;");
		if(!$up_query){
			return new ActionResult(
				$this,
				Server::get('HTTP_Referer'),
				0,
				'Failed to swap item position!<br />Error: <code>Query failed</code>',
				B_T_FAIL
			);
		}
		$up_query->bind_param('i', $MID);
		
		$up_query->execute();
		if($up_query->affected_rows == 2){
			return new ActionResult(
				$this,
				Server::get('HTTP_Referer'),
				1,
				'Swapped positions!',
				B_T_SUCCESS
			);
		}else{
			return new ActionResult(
				$this,
				Server::get('HTTP_Referer'),
				0,
				'Failed to swap positions!',
				B_T_FAIL
			);
		}
	}
	
	public function remove(){
		$MID	= (WebApp::get('cat4')===NULL)?	''	:WebApp::get('cat4');
		$this->parent->parent->debug($this::name_space.': MID '.$MID);
		$remove_query = $this->mySQL_w->prepare("DELETE FROM `core_menu` WHERE `MID`=?");
		if(!$remove_query){
			return new ActionResult(
				$this,
				'/admin/core/menu_view',
				0,
				'Failed to remove menu item!<br />Error: <code>Query failed</code>',
				B_T_FAIL
			);
		}
		
		$remove_query->bind_param('i', $MID);
		$remove_query->execute();
		$remove_query->store_result();
		if($remove_query->affected_rows != 0){
			return new ActionResult(
				$this,
				'/admin/core/menu_view',
				1,
				'Removed menu item!',
				B_T_SUCCESS
			);
		}else{
			return new ActionResult(
				$this,
				'/admin/core/menu_view',
				0,
				'Failed to remove menu item!<br />Error: <code>Unknown</code>',
				B_T_FAIL
			);
		}
	}
	
	public function removeSub(){
	}
}
?>