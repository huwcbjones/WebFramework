<?php

/**
 * IP Block Action Class
 *
 * @category   Module.Core.Action.IpBLock
 * @package    core/resources/ipblock.action.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

class IpblockAction extends BaseAction
{
	const	 name_space	 = 'Module.Core';
	const	 version	 = '1.0.1';
	
	public function add(){
		$gump = new GUMP();
		
		$gump->validation_rules(array(
			'ip'=>'required|valid_ipv4',
			'length'=>'required|integer',
			'reason'=>'required'
		));
		
		$gump->filter_rules(array(
			'ip'=>'trim',
			'length'=>'trim|whole_number',
			'reason'=>'trim|sanitize_string'
		));
		
		$valid_data = $gump->run($_POST);
		
		if($valid_data === false){
			return new ActionResult(
				$this,
				'/admin/core/ipblock_add',
				0,
				'Failed to add block!<br />Error: <code>Please check you have completed all fields as instructed.</code>',
				B_T_FAIL
			); 
		}
		$ipblock = new IpBan($this->parent->parent);
		if($ipblock->ban($valid_data['reason'], $valid_data['length'], $valid_data['ip'])){
			return new ActionResult(
				$this,
				'/admin/core/ipblock_view',
				1,
				'Succeesfully added block!',
				B_T_SUCCESS
			);
		}else{
			return new ActionResult(
				$this,
				'/admin/core/ipblock_add',
				0,
				'Failed to add block!',
				B_T_FAIL
			);
		}
	}
	
	public function edit(){
		
		$gump = new GUMP();
		
		$gump->validation_rules(array(
			'id'=>'required|integer|min_len,1',
			'ip'=>'required|valid_ipv4',
			'length'=>'required|integer',
			'reason'=>'required'
		));
		
		$gump->filter_rules(array(
			'id'=>'trim|whole_number',
			'ip'=>'trim',
			'length'=>'trim|whole_number',
			'reason'=>'trim|sanitize_string'
		));
		
		$valid_data = $gump->run($_POST);
		
		if($valid_data === false){
			return new ActionResult(
				$this,
				'/admin/core/ipblock_view',
				0,
				'Failed to edit block!<br />Error: <code>Please check you have completed all fields as instructed.</code>',
				B_T_FAIL
			); 
		}
		$update_query = $this->mySQL_w->prepare("UPDATE `core_ip` SET `length`=?, `reason`=? WHERE `id`=? AND `ip`=INET_ATON(?)");
		if($update_query === false){
			return new ActionResult(
				$this,
				'/admin/core/ipblock_view',
				0,
				'Failed to edit block.<br/>Error: <code>Update query failed</code>',
				B_T_FAIL
			);
		}
		$update_query->bind_param('isis', $valid_data['length'], $valid_data['reason'], $valid_data['id'], $valid_data['ip']);
		$update_query->execute();
		if($update_query->affected_rows==1){
			return new ActionResult(
				$this,
				'/admin/core/ipblock_view',
				1,
				'Succeesfully edited block!',
				B_T_SUCCESS
			);
		}else{
			return new ActionResult(
				$this,
				'/admin/core/ipblock_view',
				1,
				'Tried to edit block, but there was nothing to change!',
				B_T_INFO
			);
		}
	}
	
	public function delete(){
		$blocks	= (WebApp::post('blocks')===NULL)?	array()	:strgetcsv(WebApp::post('blocks'));
		if(count($blocks)==0){
			return new ActionResult($this, '/admin/core/ipblock_view', 0, 'No block(s) were selected!', B_T_FAIL);
		}
		
		foreach($blocks as $block){
			$validated = GUMP::is_valid(array('blk'=>$block), array('blk'=>'integer'));
			if($validated!==true){
				return new ActionResult($this, '/admin/core/ipblock_view', 0, 'No block(s) were selected!', B_T_FAIL); 
			}
		}
		$delete = $this->mySQL_w->prepare("DELETE FROM `core_ip` WHERE `id`=?");
		$affected_rows = 0;
		foreach($blocks as $id){
			$delete->bind_param('i', $id);
			$delete->execute();
			$delete->store_result();
			$affected_rows += $delete->affected_rows;
		}
		
		if($affected_rows == count($blocks)){
			$this->parent->parent->logEvent($this::name_space, 'Unblocked IPs: '.csvgetstr($blocks));
			return new ActionResult(
				$this,
				'/admin/core/ipblock_view',
				1,
				'Successfully unblocked selected IPs!',
				B_T_SUCCESS
			);
		}else{
			$this->parent->parent->logEvent($this::name_space, 'Unblocked some IPs: '.csvgetstr($blocks));
			return new ActionResult(
				$this,
				'/admin/core/ipblock_view',
				1,
				'Successfully unblocked '.$affected_rows.'/'.count($blocks).' selected IP(s)!<br /><small>Possible cause: <code>Unknown</code></small>',
				B_T_WARNING
			);
		}
	}
}
?>