<?php

/**
 * Option Action Class
 *
 * @category   Module.Core.Action.option
 * @package    core/resources/option.action.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

class OptionAction extends BaseAction
{
	const	 name_space	 = 'Module.Core';
	const	 version	 = '1.0.1';
	
	public function add(){
		$gump = new GUMP();
		
		$gump->validation_rules(array(
			'name'=>'required|min_len,1',
			'value'=>'required|min_len,1'
		));
		
		$gump->filter_rules(array(
			'name'=>'trim|sanitize_string',
			'value'=>'trim',
			'desc'=>'trim|sanitize_string'
		));
		
		$valid_data = $gump->run($_POST);
		
		if($valid_data === false){
			return new ActionResult(
				$this,
				'/admin/core/option_add',
				0,
				'Failed to add option!<br />Error: <code>Please check you have completed all fields as instructed.</code>',
				B_T_FAIL
			); 
		}
		
		$add_query = $this->mySQL_w->prepare("INSERT INTO `core_options` (`name`, `value`, `desc`) VALUES (?, ?, ?)");
		if($add_query === false){
			return new ActionResult(
				$this,
				'/admin/core/option_add',
				0,
				'Failed to add option.<br/>Error: <code>Update query failed</code>',
				B_T_FAIL
			);
		}
		$add_query->bind_param('sss', $valid_data['name'], $valid_data['value'], $valid_data['desc']);
		$add_query->execute();
		$add_query->store_result();
		if($add_query->affected_rows == 1){
			return new ActionResult(
				$this,
				'/admin/core/option_view',
				1,
				'Succeesfully added option!',
				B_T_SUCCESS
			);
		}else{
			return new ActionResult(
				$this,
				'/admin/core/option_add',
				0,
				'Failed to add option!',
				B_T_FAIL
			);
		}
	}
	
	public function edit(){
		
		$gump = new GUMP();
		
		$gump->validation_rules(array(
			'id'=>'required|integer|min_len,1',
			'name'=>'required',
			'value'=>'required'
		));
		
		$gump->filter_rules(array(
			'id'=>'trim|whole_number',
			'name'=>'trim|sanitize_string',
			'value'=>'trim',
			'desc'=>'trim|sanitize_string'
		));
		
		$valid_data = $gump->run($_POST);
		
		if($valid_data === false){
			return new ActionResult(
				$this,
				'/admin/core/option_edit',
				0,
				'Failed to edit option!<br />Error: <code>Please check you have completed all fields as instructed.</code>',
				B_T_FAIL
			); 
		}
		$update_query = $this->mySQL_w->prepare("UPDATE `core_options` SET `value`=?, `desc`=? WHERE `id`=? AND `name`=?");
		if($update_query === false){
			return new ActionResult(
				$this,
				'/admin/core/option_view',
				0,
				'Failed to edit option.<br/>Error: <code>Update query failed</code>',
				B_T_FAIL
			);
		}
		$update_query->bind_param('ssis', $valid_data['value'], $valid_data['desc'], $valid_data['id'], $valid_data['name']);
		$update_query->execute();
		if($update_query->affected_rows==1){
			return new ActionResult(
				$this,
				'/admin/core/option_view',
				1,
				'Succeesfully edited option!',
				B_T_SUCCESS
			);
		}else{
			return new ActionResult(
				$this,
				'/admin/core/option_view',
				1,
				'Tried to edit option, but there was nothing to change!',
				B_T_INFO
			);
		}
	}
	
	public function delete(){
		$options	= (WebApp::post('options')===NULL)?	array()	:strgetcsv(WebApp::post('options'));
		if(count($options)==0){
			return new ActionResult($this, '/admin/core/option_view', 0, 'No option(s) were selected!', B_T_FAIL);
		}
		
		foreach($options as $option){
			$validated = GUMP::is_valid(array('opt'=>$option), array('opt'=>'integer'));
			if($validated!==true){
				return new ActionResult($this, '/admin/core/option_view', 0, 'No option(s) were selected!', B_T_FAIL); 
			}
		}
		$delete = $this->mySQL_w->prepare("DELETE FROM `core_options` WHERE `id`=?");
		$affected_rows = 0;
		foreach($options as $id){
			$delete->bind_param('i', $id);
			$delete->execute();
			$delete->store_result();
			$affected_rows += $delete->affected_rows;
		}
		
		if($affected_rows == count($options)){
			$this->parent->parent->logEvent($this::name_space, 'Deleted options: '.csvgetstr($options));
			return new ActionResult(
				$this,
				'/admin/core/option_view',
				1,
				'Successfully deleted selected option(s)!',
				B_T_SUCCESS
			);
		}else{
			$this->parent->parent->logEvent($this::name_space, 'Deleted some options: '.csvgetstr($options));
			return new ActionResult(
				$this,
				'/admin/core/option_view',
				1,
				'Successfully deleted '.$affected_rows.'/'.count($options).' selected option(s)!<br /><small>Possible cause: <code>Unknown</code></small>',
				B_T_WARNING
			);
		}
	}
}
?>