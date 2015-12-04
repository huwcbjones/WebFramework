<?php

/**
 * Sessions Handler
 *
 * Handles the module to $_SESSION mappnig
 *
 * @category   WebApp.Session
 * @package    class.session.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones	
 */

/*
 * 
 */

class Session
{
	const name_space = 'WebApp.Session';
	const version = '1.0.0';
	
	public static function start()
	{
		if(!headers_sent()){
			return session_start();
		}
		return false;
	}
	public static function getID()
	{
		return session_id();
	}
	
	public static function regen()
	{
		if(!isset($_SESSION)){
			return NULL;
		}
		session_regenerate_id(true);
	}
	
	public static function set($namespace, $opt, $value)
	{
		if(!isset($_SESSION)){
			return NULL;
		}
		$namespace = base64_encode($namespace);
		$opt = base64_encode($opt);
		$_SESSION[$namespace][$opt] = $value;
	}
	
	public static function getAll($namespace)
	{
		if(!isset($_SESSION)){
			return array();
		}
		$namespace = base64_encode($namespace);
		if (array_key_exists($namespace, $_SESSION)) {
			$session = array();
			foreach($_SESSION[$namespace] as $id=>$value){
				$session[base64_decode($id)] = $value;
			}
			return $session;
		}else{
			return array();
		}
	}
	public static function get($namespace, $opt)
	{
		$namespace = base64_encode($namespace);
		$opt = base64_encode($opt);
		if(!isset($_SESSION)){
			return NULL;
		}
		if (!array_key_exists($namespace, $_SESSION)) {
			return NULL;
		}
		
		if (array_key_exists($opt, $_SESSION[$namespace])) {
			return $_SESSION[$namespace][$opt];
		}
		return NULL;
	}
	
	public static function del($namespace, $opt)
	{
		if(!isset($_SESSION)){
			return NULL;
		}
		$namespace = base64_encode($namespace);
		$opt = base64_encode($opt);
		unset($_SESSION[$namespace][$opt]);
	}
}

?>