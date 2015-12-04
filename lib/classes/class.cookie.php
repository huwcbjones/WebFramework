<?php

/**
 * Cookie Handler
 *
 * @category   WebApp.Cookie
 * @package    class.cookie.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 * 
 */

class Cookie
{
	const	name_space	= 'WebApp.Cookie';
	const	version		= '1.0.0';
	
	public static function set($opt, $value, $time=0, $path='/', $domain='', $secure=false, $httponly=true){
		if($time != 0){
			$time = time()+ $time;
		}
		if($domain==''){
			$domain = Server::get('Server_Name');
		}
		if($domain=='localhost'){
			$domain = NULL;
		}
		if(ip2long($domain)){
			$domain = NULL;
		}
		setcookie($opt, $value, $time, $path, $domain, $secure, $httponly);
	}
	
	public static function get($opt){
		if(array_key_exists($opt, $_COOKIE)){
			return $_COOKIE[$opt];
		}
		return NULL;
	}

	public static function getAll(){
		if(count($_COOKIE)!=0){
			return $_COOKIE;
		}
		return NULL;
	}
	
	public static function del($opt, $path='/', $domain=''){
		if($domain==''){
			$domain = Server::get('Server_Name');
		}
		if($domain=='localhost'){
			$domain = NULL;
		}
		setcookie($opt, '', time()-3600, $path, $domain);
	}
}
?>
