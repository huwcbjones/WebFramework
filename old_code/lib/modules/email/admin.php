<?php
/**
 * Email Admin Controller Class
 *
 * @category   Module.Email.Admin
 * @package    email/admin.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
 */

class AdminPageController extends BaseAdminPageController
{
	const name_space	= 'Module.Email';
	const version		= '1.0.0';

	function _getFilename(){
		return __MODULE__.'/email/admin/email';
	}
}
?>