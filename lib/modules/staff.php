<?php
/**
 * Staff Classes
 *
 * @category   Staff
 * @package    staff.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */
class Staff{

	protected $mySQL;
	private $mode;
	private $staff;

	function __construct($link){
		$this->mySQL = $link;
	}

	function setMode($mode){
		$this->mode = $mode;
	}
	
	function addStaff(){
		$staff_query = $this->mySQL['r']->query("SELECT * from `staff` WHERE `cat` IN ('".$this->mode."','b')");
		if($staff_query!==false&&$staff_query->num_rows!=0){
			while($staff = $staff_query->fetch_array()){
				if(!file_exists($staff['image'])){$staff['image'] = 'images/default.gif';}
				$this->staff[$staff['ID']] = $staff;
			}
		}
	}

	function getStaff(){
		return $this->staff;
	}
}
?>