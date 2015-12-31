<?php

/**
 * Constant Definitions
 *
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @date       30/12/2015
 */

define('DATE_Short', 'd/m/Y', true);
define('DATE_Long', 'l jS F Y', true);

define('DATET_Short', 'd/m/Y H:i:s', true);
define('DATET_Long', 'l jS F Y, H:i', true);
define('DATET_SQL', 'Y-m-d H:i:s', true);
define('DATET_BACKUP', 'Ymd_His', true);

define('__EXECDIR__', dirname(dirname(__file__)));
define('__LIBDIR__', __EXECDIR__ . DIRECTORY_SEPARATOR . 'lib', true);
define('__CTRLDIR__', __LIBDIR__ . DIRECTORY_SEPARATOR . 'controllers', true);
define('__MODULE__', __LIBDIR__ . DIRECTORY_SEPARATOR . 'modules', true);
define('__PLUGIN__', __LIBDIR__ . DIRECTORY_SEPARATOR . 'plugins', true);
define('__TEMP__', __EXECDIR__ . DIRECTORY_SEPARATOR . 'temp', true);
define('__BACKUP__', __EXECDIR__ . DIRECTORY_SEPARATOR . 'backup', true);
