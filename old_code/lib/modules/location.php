<?php
/**
 * Location Classes
 *
 * @category   Location
 * @package    location.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */

/**
 * Location Item Class
 *
 * @category   Location.Item
 */

/*
 */
class Location{
	
	public $location = array();
	protected $mySQL;
	public $ID;
	public $name;
	public $address;
	public $phone;
	public $map;
	
	function __construct($link){
		$this->mySQL = $link;
	}
	
	function getLocations(){
		$loc_query = $this->mySQL['r']->prepare("SELECT `ID` FROM `location`");
		$loc_query->execute();
		$loc_query->store_result();
		if($loc_query->num_rows>0){
			$loc_query->bind_result($ID);
			while($loc_query->fetch()){
				$this->location[] = $ID;
			}
		}
	}
	
	function getLocation($ID){
		$this->ID = $ID;
		$loc_query = $this->mySQL['r']->prepare("SELECT `name`,`address1`,`address2`,`city`,`county`,`phone`,`post`,`phone_ext`,`map` FROM `location` WHERE `ID`=?");
		$loc_query->bind_param('i',$ID);
		$loc_query->execute();
		$loc_query->store_result();
		if($loc_query->num_rows==1){
			$loc_query->bind_result($name,$address1,$address2,$city,$county,$phone,$post,$phone_ext,$map);
			while($loc_query->fetch()){
				$this->name = $name;
				$this->address['line1'] = $address1;
				$this->address['line2'] = $address2;
				$this->address['city'] = $city;
				$this->address['county'] = $county;
				$this->address['post'] = $post;
				$this->phone['num'] = $phone;
				$this->phone['ext'] = $phone_ext;
				$this->map = $map;
			}
		}else{
			return false;
		}
	}
	
	function getData(){
		if(isset($this->ID)){
		$data['id'] = $this->ID;
		$data['name'] = $this->name;
		$data['address'] = $this->address;
		$data['phone'] = $this->phone;
		$data['map'] = $this->map;
		return $data;
		}else{return false;}
	}
}

?>