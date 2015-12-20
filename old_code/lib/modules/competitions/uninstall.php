<?php
/**
 * Competitions Uninstaller
 *
 * @category   Module.Competitions.Install
 * @package    install.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
 *
 */


function uninstall($ctrl){
	$uninstall = file_get_contents(dirname(__FILE__).'/sql/uninstall.sql');
	if($ctrl->mySQL_w->multi_query($uninstall)!==false){
		WebApp::clearQuery($ctrl->mySQL_w);
		return true;
	}else{
		return false;
	}
}
?>