<?php
/**
 * Server Handler
 *
 * Handles the $_SERVER mapping
 *
 * @category   WebApp.Server
 * @package    class.server.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones	
 */

/*
 * 
 */

class Server
{
	const	name_space	= 'WebApp.Server';
	const	version		= '1.0.0';
	
	public static function get($opt){
		$opt = strtoupper($opt);
		if(array_key_exists($opt, $_SERVER)){
			return $_SERVER[$opt];
		}
		return NULL;
	}

	public static function getAll(){
		if(count($_SERVER)!=0){
			return $_SERVER;
		}
		return NULL;
	}
}
?>