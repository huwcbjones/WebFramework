<?php
/**
 * News Uninstaller
 *
 * @category   Module.News.Uninstall
 * @package    uninstall.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
 *
 */

function uninstall($ctrl){
	$sql = file_get_contents(dirname(__FILE__).'/uninstall.sql');
	$ctrl->mySQL_w->multi_query($sql);
	return true;
}
?>