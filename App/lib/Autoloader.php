<?php
/**
 * @author      Huw Jones <huwcbjones@gmail.com>
 * @date        31/12/2015
 */

namespace WebApp\AutoLoader;

use WebApp\Debugger;

function checkDir($directory, $className)
{
    return is_readable($directory . DIRECTORY_SEPARATOR . $className . '.php');
}

function ClassAutoLoader($className)
{
    $directories = array(__LIBDIR__, __CTRLDIR__, __BASEDIR__);
    foreach ($directories as $directory) {
        if (checkDir($directory, $className)) {
            Debugger::log('Loading ' . $className . '...', __NAMESPACE__);
            require($directory . DIRECTORY_SEPARATOR . $className . '.php');
            return true;
        }
    }

    return false;
}

if (version_compare(PHP_VERSION, '5.1.2', '>=')) {
    //SPL autoloading was introduced in PHP 5.1.2
    if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
        spl_autoload_register('WebApp\AutoLoader\ClassAutoLoader', true, true);
    } else {
        spl_autoload_register('WebApp\AutoLoader\ClassAutoLoader');
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
     */
    function __autoload($classname)
    {
        ClassAutoLoader($classname);
    }
}
