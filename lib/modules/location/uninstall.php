<?php
/**
 * Location Uninstaller
 *
 * @category   Module.Location.Uninstall
 * @package    uninstall.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
 *
 */

function uninstall($ctrl){
	$sql = file_get_contents(dirname(__FILE__).'/uninstall.sql');
	if($ctrl->mySQL_w->multi_query($sql)!==false){
		return true;
	}else{
		return false;
	}
}
?>