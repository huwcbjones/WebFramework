<?php

/**
 * Index file for BWSC Website
 *
 *
 * @category   WebApp.Index
 * @package    index.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
* lib/webapp.php
*/

require 'lib/_init.php';
$webapp = new WebApp;
$webapp->run();
?>