<?php

/**
 * Initialisation file for the Web App
 *
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @date       30/12/2015
 */

namespace WebApp;

error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'Defines.php';
require 'Functions.php';
require 'Debugger.php';
Debugger::log(123);
Debugger::log("Meh!");
//require 'autoload.php';
