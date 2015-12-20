<?php
/**
 * Location Resource Class
 *
 * @category   Module.Location.Resource
 * @package    location/resource.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
 */

class LocationResource extends BaseResource
{
	const	name_space	= 'Module.Location';
	const	version		= '1.0.0';
	
	public	$ID			= '';
	public	$name		= '';
	public	$address	= array();
	public	$phone		= array();
	public	$map		= false;
	
	function parseLocation($location){
		$this->parent->parent->debug($this::name_space.': Parsing location "'.$location.'"');
		if(substr($location, 0, 1)=='%' && substr($location, -1)=='%'){
			$loc_id = substr($location, 1, strlen($location)-2);
			return $this->getLocation($loc_id);
		}else{
			return ucfirst($loaction);
		}
	}
	
	function getLocation($id){
		$this->ID = $id;
		$this->parent->parent->debug($this::name_space.': Fetching location "'.$id.'"');
		$loc_q = $this->parent->mySQL_r->prepare(
"SELECT
`name`,	`address1`,	`address2`,	`city`,	`county`,	`post`,	`phone`, `phone_ext`,	`map`
FROM `location` WHERE `ID`=?"
);
		if(!$loc_q){
			$this->parent->parent->debug($this::name_space.': MySQL Statement error!');
			return false;
		}
		$loc_q->bind_param('i',$id);
		$loc_q->execute();
		$loc_q->store_result();
		if($loc_q->num_rows==1){
			$loc_q->bind_result(
				$name,
				$address['line1'],		$address['line2'],		$address['city'],		$address['county'],		$address['postcode'],
				$phone['number'],		$phone['ext'],
				$map
			);
			while($loc_q->fetch()){
				$this->name		= $name;
				$this->address	= $address;
				$this->phone	= $phone;
				$this->map		= ($map==1);
			}
			$this->parent->parent->debug($this::name_space.': Fetched location "'.$name.'"!');
			return $this;
		}else{
			return false;
		}
	}
	
	function getComponent($component){
		switch(strtolower($component)){
			case 'id':
				return $this->ID;
				break;
			case 'name':
			case 'title':
				return $this->name;
				break;
			case 'address':
				return $this->address['line1'].PHP_EOL.$this->address['line2'];
				break;
			case 'city':
				return $this->address['city'];
				break;
			case 'county':
				return $this->address['county'];
				break;
			default:
				return $this;
		}
	}
}
?>