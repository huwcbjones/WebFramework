<?php

/**
 * Initiailsation
 *
 * @category   WebApp.Init
 * @package    _init.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones	
 */

/** INCLUDES
 *  __LIBDIR__/defines.php
 *	__LIBDIR__/debugger.php
 *	__LIBDIR__/autoload.php
 *	__LIBDIR__/functions.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'defines.php';
require 'debugger.php';
$debug = new Debugger();
require 'autoload.php';
require 'functions.php';

?>