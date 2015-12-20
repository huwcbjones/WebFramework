<?php
/**
 * Location Action Class
 *
 * @category   Module.Location.Action
 * @package    location/action.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */

class ActionController extends BaseAction
{
	const	 name_space	 = 'Module.Location';
	const	 version	 = '1.0.0';

	public function add(){
		if(!$this->accessAdminPage(1)){
			$this->parent->parent->addHeader('Location', '/admin/location');
			return new ActionResult($this, '/admin/location', 1, 'You are not allowed to do that', B_T_FAIL);
		}
		$venue		= (WebApp::post('name')===NULL)?		''	:WebApp::post('name');
		$addr1		= (WebApp::post('addr1')===NULL)?		''	:WebApp::post('addr1');
		$addr2		= (WebApp::post('addr2')===NULL)?		''	:WebApp::post('addr2');
		$city		= (WebApp::post('city')===NULL)?		''	:WebApp::post('city');
		$county		= (WebApp::post('county')===NULL)?		''	:WebApp::post('county');
		$post		= (WebApp::post('post')===NULL)?		''	:WebApp::post('post');
		$phone		= (WebApp::post('phone')===NULL)?		''	:WebApp::post('phone');
		$ext		= (WebApp::post('ext')===NULL)?			''	:WebApp::post('ext');
		$maps		= (WebApp::post('maps')===NULL)?		1	:WebApp::post('maps');
		
		$required = array('venue'=>'Venue', 'addr1'=>'Address Line 1', 'city'=>'Town/City', 'county'=>'County', 'post'=>'Postcode', 'phone'=>'Phone Number');
		foreach($required as $key=>$value){
			if($$key == ''){
				return new ActionResult($this, '/admin/location/add', 0, $value.' is required.', B_T_FAIL);
			}
		}
		
		$add_query = $this->mySQL_w->prepare(
"INSERT INTO `location`
(`name`,	`address1`,	`address2`,	`city`,	`county`,	`post`,	`phone`,	`phone_ext`,	`map`) VALUES
(?,			?,			?,			?,		?,			?,		?,			?,				?)
");
		if($add_query === false){
			return new ActionResult($this, '/admin/location/add', 0, 'Failed to add location!', B_T_FAIL);
		}
		$add_query->bind_param('ssssssssi', $venue, $addr1, $addr2, $city, $county, $post, $phone, $ext, $maps);
		$add_query->execute();
		$add_query->store_result();
		if($add_query->affected_rows==1){
			return new ActionResult($this, '/admin/location', 1, 'Location added!', B_T_SUCCESS);
		}
		return new ActionResult($this, '/admin/location/add', 0, 'Failed to add location!', B_T_FAIL);
	}
	
	public function edit(){
		if(!$this->accessAdminPage(2)){
			$this->parent->parent->addHeader('Location', '/admin/location');
			return new ActionResult($this, '/admin/location', 1, 'You are not allowed to do that', B_T_FAIL);
		}
		$id			= (WebApp::post('id')===NULL)?			''	:WebApp::post('id');
		
		$check_query = $this->mySQL_r->prepare("SELECT `id` FROM `location` WHERE `id`=?");
		if($check_query === false){
			return new ActionResult($this, '/admin/location/edit/'.$id, 0, 'Check query failed', B_T_FAIL);
		}
		$check_query->bind_param('i', $id);
		$check_query->execute();
		$check_query->store_result();
		if($check_query->num_rows != 1){
			return new ActionResult($this, '/admin/location', 1, 'Location with that ID does not exists', B_T_FAIL);
		}
		
		$venue		= (WebApp::post('name')===NULL)?		''	:WebApp::post('name');
		$addr1		= (WebApp::post('addr1')===NULL)?		''	:WebApp::post('addr1');
		$addr2		= (WebApp::post('addr2')===NULL)?		''	:WebApp::post('addr2');
		$city		= (WebApp::post('city')===NULL)?		''	:WebApp::post('city');
		$county		= (WebApp::post('county')===NULL)?		''	:WebApp::post('county');
		$post		= (WebApp::post('post')===NULL)?		''	:WebApp::post('post');
		$phone		= (WebApp::post('phone')===NULL)?		''	:WebApp::post('phone');
		$ext		= (WebApp::post('ext')===NULL)?			''	:WebApp::post('ext');
		$maps		= (WebApp::post('maps')===NULL)?		1	:WebApp::post('maps');
		
		$required = array('venue'=>'Venue', 'addr1'=>'Address Line 1', 'city'=>'Town/City', 'county'=>'County', 'post'=>'Postcode', 'phone'=>'Phone Number');
		foreach($required as $key=>$value){
			if($$key == ''){
				return new ActionResult($this, '/admin/location/add', 0, $value.' is required.', B_T_FAIL);
			}
		}
		
		$update = $this->mySQL_w->prepare("UPDATE `location` SET `name`=?, `address1`=?, `address2`=?, `city`=?, `county`=?, `post`=?, `phone`=?, `phone_ext`=?, `map`=? WHERE `id`=?");
		if($update === false){
			return new ActionResult($this, '/admin/location/add', 0, 'Failed to save location!', B_T_FAIL);
		}
		$update->bind_param('ssssssssii', $venue, $addr1, $addr2, $city, $county, $post, $phone, $ext, $maps, $id);
		$update->execute();
		$update->store_result();
		if($update->affected_rows==1){
			return new ActionResult($this, '/admin/location', 1, 'Location saved!', B_T_SUCCESS);
		}
		return new ActionResult($this, '/admin/location/add', 0, 'Nothing to change', B_T_INFO);
	}
	
	function delete()
	{
		$locations	= (WebApp::post('locations')===NULL)?	array()	:strgetcsv(WebApp::post('locations'));
		
		if(count($locations)==0){
			return new ActionResult(
				$this,
				'/admin/location',
				0,
				'No locations(s) were selected!',
				B_T_FAIL,
				array('form'=>array('pwd'=>''))
			);
		}
		
		$check_query = $this->mySQL_w->prepare("SELECT `ID` FROM `location` WHERE `ID`=?");
		
		if($check_query === false){
			return new ActionResult(
				$this,
				'/admin/location',
				0,
				'Failed to delete location(s)!<br />Error: <code>Check query failed</code>',
				B_T_FAIL
			);
		}
		foreach($locations as $ID){
			$check_query->bind_param('i', $ID);
			$check_query->execute();
			$check_query->store_result();
			if($check_query->num_rows!=1){
				return new ActionResult(
					$this,
					'/admin/location',
					1,
					'Failed to delete location(s)!<br />Error: <code>Location doesn\'t exist</code>',
					B_T_INFO
				);
			}
		}
		$check_query->free_result();

		$delete_query = $this->mySQL_w->prepare("DELETE FROM `location` WHERE `id`=?");
		
		if($delete_query === false){
			return new ActionResult(
				$this,
				'/admin/location',
				0,
				'Failed delete location(s)!<br />Error: <code>Update query failed</code>',
				B_T_FAIL
			);
		}
		$affected_rows = 0;
		foreach($locations as $ID){
			$delete_query->bind_param('i', $ID);
			$delete_query->execute();
			$delete_query->store_result();
			$affected_rows += $delete_query->affected_rows;
		}

		if($affected_rows == count($locations)){
			$this->parent->parent->logEvent($this::name_space, 'Deleted '.csvgetstr($locations));
			return new ActionResult(
				$this,
				'/admin/location',
				1,
				'Successfully deleted selected location(s)!',
				B_T_SUCCESS
			);
		}else{
			$this->parent->parent->logEvent($this::name_space, 'Deleted some of '.csvgetstr($locations));
			return new ActionResult(
				$this,
				'/admin/location',
				1,
				'Successfully deleted '.$affected_rows.'/'.count($locations).' selected location(s)!<br /><small>Possible cause: <code>Location with that ID may not exist</code></small>',
				B_T_WARNING
			);
		}
	}
}
?>
