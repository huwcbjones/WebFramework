<?php
/**
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @date       30/12/2015
 */

namespace WebApp;

function is_CLI()
{
    return (php_sapi_name() === 'cli');
}

function getArgumentErrorMessage($function, $type, $given, $class = null)
{
    $msg = 'Argument passed to ';
    if($class !== null) {
        $msg .= $class . '::';
    }
    $msg .= $function . ' was not a ' . $type . ', ' . $given . ' given.';
    return $msg;
}