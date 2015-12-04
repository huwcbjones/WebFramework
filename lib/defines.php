<?php

/**
 * Definitions
 *
 * Contains all constant definitions
 *
 * @category   WebApp.Definitions
 * @package    defines.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones	
 */

define('DATE_Long', 'l jS F Y', true);
define('DATET_Long', 'l jS F Y, H:i', true);
define('DATE_Short', 'd/m/Y', true);
define('DATET_Short', 'd/m/Y H:i:s', true);
define('DATET_SQL', 'Y-m-d H:i:s', true);
define('DATET_BKUP', 'Ymd_His', true);

define('__EXECDIR__', dirname(dirname(__file__)));
define('__LIBDIR__', __EXECDIR__ . DIRECTORY_SEPARATOR . 'lib', true);
define('__CTRLDIR__', __LIBDIR__ . DIRECTORY_SEPARATOR . 'controllers', true);
define('__MODULE__', __LIBDIR__ . DIRECTORY_SEPARATOR . 'modules', true);
define('__PLUGIN__', __LIBDIR__ . DIRECTORY_SEPARATOR . 'plugins', true);
define('__TEMP__', __EXECDIR__ . DIRECTORY_SEPARATOR . 'temp', true);
define('__BACKUP__', __EXECDIR__ . DIRECTORY_SEPARATOR . 'backup', true);

define('B_ICON', 'glyphicon', true);
define('B_T_Primary', 'primary', true);
define('B_T_Muted', 'muted', true);
define('B_T_Fail', 'danger', true);
define('B_T_Warning', 'warning', true);
define('B_T_Info', 'info', true);
define('B_T_Success', 'success', true);
define('B_T_Default', 'default', true);

// Action Statuses
define('_action_success', 1, true);
for ($i = 1; $i <= 10; $i++) {
	define('_action_fail_' . $i, (9 + $i), true);
}
define('_action_privilege', 20, true);
define('_action_unspec', 30, true);
define('_action_fail_auth', 40, true);
?>