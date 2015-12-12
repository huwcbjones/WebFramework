<?php
/**
 * System
 *
 *
 * @category   Core.System
 * @package    system.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */
 
class System {
	protected $mySQL;
	
	function __construct($link){
		$this->mySQL = $link;
	}
	
	function addOption(){
	}
	
	function editOption(){
	}
	
	function delOption(){
	}
	
	function addPage(){
	}
	
	function editPage(){
	}
	
	function delPage(){
	}
	
	function updateConfig(){
	}
}
?>