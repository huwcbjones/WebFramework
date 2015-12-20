<?php

/**
 * WebApp Autoloader
 *
 * Autoloads base classes, dependencies and plugins
 *
 * @category   WebApp.Autoload
 * @package    autoload.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones	
 */

/** INCLUDES
 *	__LIBDIR__/base.d/*
 *	__LIBDIR__/classes/*
 *  __MODULE__/*
 */

/**
 * ClassAutoloader()
 * 
 * @param mixed $classname
 * @return
 */
function ClassAutoloader($classname)
{
	global $debug;
	//Can't use __DIR__ as it's only in PHP 5.3+
	if (is_readable(__CTRLDIR__ . '/' . strtolower($classname) . '.php')) {
		$debug->debug('WebApp.Autoload: Loading controller: ' . strtolower($classname) .
			'.php');
		require __CTRLDIR__ . '/' . strtolower($classname) . '.php';
		return true;
	}
	if (is_readable(__LIBDIR__ . '/' . strtolower($classname) . '.php')) {
		$debug->debug('WebApp.Autoload: Loading dependency: ' . strtolower($classname) .
			'.php');
		require __LIBDIR__ . '/' . strtolower($classname) . '.php';
		return true;
	}
	$filename = 'class.' . strtolower($classname) . '.php';
	if (is_readable(__LIBDIR__ . '/base.d/' . $filename)) {
		$debug->debug('WebApp.Autoload: Loading dependency: /base.d/' . $filename);
		require __LIBDIR__ . '/base.d/' . $filename;
		return true;
	}
	if (is_readable(__LIBDIR__ . '/classes/' . $filename)) {
		$debug->debug('WebApp.Autoload: Loading dependency: /classes/' . $filename);
		require __LIBDIR__ . '/classes/' . $filename;
		return true;
	}

	$filename = strtolower($classname) . '/plugin.php';
	if (is_readable(__PLUGIN__ . '/' . $filename)) {
		require __PLUGIN__ . '/' . $filename;
		$debug->debug('WebApp.Autoload: Loading plugin: ' . $filename);
	}
}

if (version_compare(PHP_VERSION, '5.1.2', '>=')) {
	//SPL autoloading was introduced in PHP 5.1.2
	if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
		spl_autoload_register('ClassAutoloader', true, true);
	} else {
		spl_autoload_register('ClassAutoloader');
	}
} else {
	/**
	 * Fall back to traditional autoload for old PHP versions
	 * @param string $classname The name of the class to load
	 */
	/**
	 * __autoload()
	 * 
	 * @param mixed $classname
	 * @return
	 */
	function __autoload($classname)
	{
		ClassAutoloader($classname);
	}
}

?>