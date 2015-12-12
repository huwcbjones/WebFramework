<?php

/**
 * Uninstallation Module
 *
 * @category   WebApp.Base.Uninstaller
 * @package    uninstall.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
*
*/

class Uninstaller extends BaseAction
{
	const name_space = 'Module.Modules';
	const version = '1.0.0';

	protected $module_dir = '';
	protected $module_id = '';
	protected $module_ns = '';
	public $mySQL_r; // Read Handle
	public $mySQL_w; // Read Handle

	/**
	 * Uninstaller::__construct()
	 * 
	 * @param mixed $parent
	 * @param mixed $details
	 * @return
	 */
	function __construct($parent, $details = array())
	{
		ignore_user_abort(true);
		$this->parent = $parent;
		$this->mySQL_r = $parent->mySQL_r;
		$this->mySQL_w = $parent->mySQL_w;
		$this->parent->parent->debug('***** ' . $this::name_space .
			' - Uninstaller *****');
		$this->parent->parent->debug($this::name_space . ': Version ' . $this::version);

		ignore_user_abort(true);

		// Get uninstall details
		if (count($details) == 0) {
			$hash = WebApp::get('id');
			$module_dir = Session::get($this::name_space, 'uninstall_from_' . $hash . '_dir');
			$this->module_dir = $module_dir;
			$this->module_id = Session::get($this::name_space, 'uninstall_from_' . $hash .
				'_id');
			$this->module_ns = Session::get($this::name_space, 'uninstall_from_' . $hash .
				'_ns');
		} else {
			$this->module_dir = $details['dir'];
			$this->module_id = $details['id'];
			$this->module_ns = $details['ns'];
		}
	}

	// public preUninstall() - Prepares the environment for the uninstall
	/**
	 * Uninstaller::preUninstall()
	 * 
	 * @return
	 */
	public function preUninstall()
	{
		// Get the module details
		$module = WebApp::get('ns');

		// Get the module ID
		$mod_query = $this->mySQL_r->prepare("SELECT `namespace`, `module_id` FROM `core_modules` WHERE `namespace`=?");
		$mod_query->bind_param('s', $module);
		$mod_query->execute();
		$mod_query->store_result();

		if ($mod_query->num_rows == 1) {

			$mod_query->bind_result($namespace, $module_id);

			// Workout the module dir
			while ($mod_query->fetch()) {
				$mod_dir = __MODULE__ . '/' . strtolower($namespace);
				$mod_id = $module_id;
			}

			// Generate a reference hash
			$hash = ranString(4);

			// Set the session variables
			Session::set($this::name_space, 'uninstall_from_' . $hash . '_ns', $namespace);
			Session::set($this::name_space, 'uninstall_from_' . $hash . '_dir', $mod_dir);
			Session::set($this::name_space, 'uninstall_from_' . $hash . '_id', $mod_id);

			// Navigate to the uninstall page
			$this->parent->parent->addHeader('Location', '/admin/modules/uninstall/' . $hash);
			return new ActionResult($this, '/admin/modules/uninstall/' . $hash, 1, '',
				B_T_SUCCESS);

		} else {

			// We couldn't find the module
			$msg = 'Failed to find module, please ask the administrator to perform a manual uninstall. (MOD_NS: ' .
				$module . ')';
			Session::set($this::name_space, 'msg', $msg);
			$this->parent->parent->addHeader('Location', '/admin/modules/uninstall/');
			return new ActionResult($this, '/admin/modules/uninstall/', 0, $msg, B_T_FAIL);
		}
	}

	// The big function that handles the uninstallation steps
	// The uninstall steps are public so that they can be called from the Updater class
	// but otherwise all the uninstall steps have to be called direct through this object.
	// This allows the update script (or others) to bypass the pre-module checks (which would be
	// bad! Well, it is, but we just want to borrow the methods because we're lazy)
	// Well now you know why! :) (Btw, that applies to the methods in the other resource files)
	/**
	 * Uninstaller::uninstall()
	 * 
	 * @param mixed $step
	 * @return
	 */
	public function uninstall($step = null)
	{

		if ($this->module_dir !== null) {
			$this->parent->parent->debug($this::name_space . ': Module directory is "' .
				str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->module_dir) . '".');
			$this->parent->parent->debug($this::name_space . ': Module namespace is "' . $this->
				module_ns . '".');
			$this->parent->parent->debug($this::name_space . ': Module id is "' . $this->
				module_id . '".');
			$this->step = $step;
			if ($step === null) {
				return new ActionResult($this, '/admin/modules/uninstall/', 0,
					'Failed to process uninstallation step!', B_T_FAIL, array('status' => 0, 'msg' =>
						'Failed to process uninstallation step!'));
			} else {
				switch($step) {
					case 1:
						$state = new ActionResult($this, '/admin/modules/uninstall/', 0,
							'Uninstalling module using module uninstaller...', B_T_FAIL, array(
							'step' => 2,
							'status' => 1,
							'msg' => 'Uninstalling module using module uninstaller...'));
						break;
					case 2:
						$state = $this->_uninstallModule();
						break;
					case 3:
						$state = $this->_unregisterGroups();
						break;
					case 4:
						$state = $this->_unregisterCron();
						break;
					case 5:
						$state = $this->_unregisterAdmin();
						break;
					case 6:
						//$state = $this->_unregisterMenus();
						return new ActionResult($this, '/admin/modules/uninstall/', 0,
								'Unregistering pages...', B_T_FAIL, array(
								'step' => 6,
								'status' => 1,
								'msg' => 'Unregistering pages...'));
						break;
					case 7:
						$state = $this->_unregisterPages();
						break;
					case 8:
						$state = $this->_unregisterModule();
						break;
					case 9:
						$state = $this->_removeModule();
						$this->_clearUninstallID();
						break;
				}
				$state->data['step'] = $step + 1;
				if ($state->data['status'] == 0) $this->_clearUninstallID();
				return $state;
			}
		} else {
			return new ActionResult($this, '/admin/modules/uninstall/', 0,
				'Invalid uninstaller ID!', B_T_FAIL);
		}
	}

	/**
	 * Uninstaller::_clearUninstallID()
	 * 
	 * @return
	 */
	private function _clearUninstallID()
	{
		$hash = WebApp::get('id');
		Session::del($this::name_space, 'uninstall_from_' . $hash . '_id');
		Session::del($this::name_space, 'uninstall_from_' . $hash . '_dir');
		Session::del($this::name_space, 'uninstall_from_' . $hash . '_ns');
	}
	/**
	 * Uninstaller::_uninstallModule()
	 * 
	 * @return
	 */
	public function _uninstallModule()
	{
		$this->parent->parent->debug($this::name_space . ': Finding uninstall.php...');
		if (!file_exists($this->module_dir . '/uninstall.php')) {
			return new ActionResult(
				$this,
				'/admin/modules/uninstall/',
				0,
				'Unregistering groups...',
				B_T_FAIL,
				array(
					'status' => 1,
					'msg' => 'Unregistering groups...'
				)
			);
		}
		if (!@include_once $this->module_dir . '/uninstall.php') {
			return new ActionResult(
				$this,
				'/admin/modules/uninstall/',
				0,
				'Failed to process module uninstaller!',
				B_T_FAIL,
				array(
					'status' => 0,
					'msg' => 'Failed to process module uninstaller!'
				)
			);
		}
		$this->parent->parent->debug($this::name_space . ': Found uninstall.php!');
		$this->parent->parent->debug($this::name_space . ': Installing..');
		
		if (!is_callable('uninstall')) {
			$this->parent->parent->debug($this::name_space . ': Couldn\'t find uninstall()!');
			return new ActionResult(
				$this,
				'/admin/modules/uninstall/',
				0,
				'Unregistering groups...',
				B_T_FAIL,
				array(
					'status' => 1,
					'msg' => 'Unregistering groups...'
				)
			);
		}
		if (uninstall($this)) {
			$this->parent->parent->debug($this::name_space . ': Uninstalled!');
			return new ActionResult(
				$this,
				'/admin/modules/uninstall/',
				0,
				'Unregistering groups...',
				B_T_FAIL,
				array(
					'status' => 1,
					'msg' => 'Unregistering groups...'
				)
			);
		}
		$this->parent->parent->debug($this::name_space . ': Failed...');
		return new ActionResult(
			$this,
			'/admin/modules/uninstall/',
			0,
			'Failed to process module uninstaller!',
			B_T_FAIL,
			array(
				'status' => 0,
				'msg' => 'Failed to process module uninstaller!'
			)
		);
	}

	public function _unregisterCron()
	{
		if ($this->module_id === null) {
			$this->parent->parent->debug($this::name_space .
				': Skipping cron jobs... no module ID was provided');
			return new ActionResult($this, '/admin/modules/uninstall/', 0,
				'Unregistering groups...', B_T_FAIL, array(
				'step' => 4,
				'status' => 1,
				'msg' => 'Unregistering groups...'));
		} else {
			$this->parent->parent->debug($this::name_space . ': Unregistering cron jobs...');
			$check_query = $this->mySQL_r->prepare("SELECT `ID` FROM `core_cron` WHERE `mod_id`=?");
			$unregister_query = $this->mySQL_w->prepare("DELETE FROM `core_cron` WHERE `mod_id`=?");
			if (in_array(false, array(
				$check_query,
				$unregister_query)) === false) {
				$unregister_query->bind_param('i', $this->module_id);
				$unregister_query->execute();
				
				usleep(40000);
				$check_query->bind_param('i', $this->module_id);
				$check_query->execute();
				$check_query->store_result();
				if ($check_query->num_rows == 0) {
					return new ActionResult($this, '/admin/modules/uninstall/', 0,
						'Unregistering from groups...', B_T_FAIL, array(
						'step' => 4,
						'status' => 1,
						'msg' => 'Unregistering from groups...'));
				} else {
					$this->parent->parent->debug($this::name_space . ': Failed to unregister cron jobs!');
				}
			} else {
				$this->parent->parent->debug($this::name_space . ': Check query failed!');
			}
			return new ActionResult($this, '/admin/modules/uninstall/', 0,
				'Failed to unregister cron jobs!', B_T_FAIL, array('status' => 0, 'msg' =>
					'Failed to unregister cron jobs!'));
		}
	}
	
	/**
	 * Uninstaller::_unregisterGroups()
	 * 
	 * @return
	 */
	public function _unregisterGroups()
	{
		if ($this->module_id === null) {
			$this->parent->parent->debug($this::name_space .
				': Skipping groups... no module ID was provided');
			return new ActionResult($this, '/admin/modules/uninstall/', 0,
				'Unregistering from site admininstration...', B_T_FAIL, array(
				'step' => 5,
				'status' => 1,
				'msg' => 'Unregistering from site admininstration...'));
		} else {
			$this->parent->parent->debug($this::name_space . ': Unregistering groups...');
			$check_query = $this->mySQL_r->prepare("SELECT `GID` FROM `core_groups` WHERE `GID` BETWEEN ? AND ?");
			$unregister_query = $this->mySQL_w->prepare("DELETE FROM `core_groups` WHERE `GID` BETWEEN ? AND ?");
			$user_cleanse = $this->mySQL_w->prepare("DELETE FROM `core_sgroup` WHERE `group` BETWEEN ? AND ?");
			if (in_array(false, array(
				$check_query,
				$unregister_query,
				$user_cleanse)) === false) {
				$start = ($this->module_id * 1000);
				$end = (($this->module_id + 1) * 1000) - 1;
				$this->parent->parent->debug($this::name_space . ': Deleting from "' . $start .
					'" to "' . $end . '"...');
				$unregister_query->bind_param('ii', $start, $end);
				$unregister_query->execute();
				
				usleep(250000);
				$check_query->bind_param('ii', $start, $end);
				$check_query->execute();
				$check_query->store_result();
				if ($check_query->num_rows == 0) {
					$user_cleanse->bind_param('ii', $start, $end);
					$user_cleanse->execute();
					$user_cleanse->free_result();
					
					return new ActionResult($this, '/admin/modules/uninstall/', 0,
						'Unregistering from site admininstration...', B_T_FAIL, array(
						'step' => 5,
						'status' => 1,
						'msg' => 'Unregistering from site admininstration...'));
				} else {
					$this->parent->parent->debug($this::name_space . ': Failed to register groups!');
				}
			} else {
				$this->parent->parent->debug($this::name_space . ': Check query failed!');
			}
			return new ActionResult($this, '/admin/modules/uninstall/', 0,
				'Failed to unregister groups!', B_T_FAIL, array('status' => 0, 'msg' =>
					'Failed to unregister groups!'));
		}
	}

	/**
	 * Uninstaller::_unregisterAdmin()
	 * 
	 * @return
	 */
	public function _unregisterAdmin()
	{
		$this->parent->parent->debug($this::name_space .
			': Unregistering admin pages and menus...');

		if ($this->_unregisterAdminPages()) {
			$this->parent->parent->debug($this::name_space . ': Unregistered admin pages!');
			return new ActionResult($this, '/admin/modules/uninstall/', 0,
				'Unregistering pages from menu...', B_T_FAIL, array(
				'status' => 1,
				'msg' => 'Unregistering pages from menus...'));
		} else {
			$this->parent->parent->debug($this::name_space .
				': Failed to register admin pages!');
		}

		return new ActionResult($this, '/admin/modules/uninstall/', 0,
			'Failed to unregister administration!', B_T_FAIL, array('status' => 0, 'msg' =>
				'Failed to unregister administration!'));
	}

	/**
	 * Uninstaller::_unregisterAdminPages()
	 * 
	 * @return
	 */
	public function _unregisterAdminPages()
	{
		$check_query = $this->mySQL_r->prepare("SELECT `ID` FROM `core_pages` WHERE `ID` BETWEEN ? AND ?");
		$unregister_query = $this->mySQL_w->prepare("DELETE FROM `core_pages` WHERE `ID` BETWEEN ? AND ?");
		if ($check_query !== false) {
			if ($unregister_query !== false) {
				$ns = strtolower($this->module_ns);
				
				$id_l = 1000000 + ($this->module_id * 1000);
				$id_u = 1000000 + (($this->module_id + 1) * 1000) - 1;
				$unregister_query->bind_param('ii', $id_l, $id_u);
				$unregister_query->execute();

				$check_query->bind_param('ii', $id_l, $id_u);
				$check_query->execute();
				$check_query->store_result();
				if ($check_query->num_rows == 0) {
					return true;
				}
			} else {
				$this->parent->parent->debug($this::name_space .
					': Admin pages unregister query failed!');
			}
		} else {
			$this->parent->parent->debug($this::name_space . ': Check query failed!');
		}
		return false;
	}
	/**
	 * Uninstaller::_unregisterAdminMenu()
	 * 
	 * @return
	 */
	public function _unregisterAdminMenu()
	{
		$check_query = $this->mySQL_w->prepare("SELECT `ID` FROM `core_admin` WHERE `namespace`=?");
		$unregister_query = $this->mySQL_w->prepare("DELETE FROM `core_admin` WHERE `namespace`=?");
		if ($check_query !== false) {
			if ($unregister_query !== false) {
				$unregister_query->bind_param('s', $this->module_ns);
				$unregister_query->execute();
				
				$check_query->bind_param('s', $this->module_ns);
				$check_query->execute();
				$check_query->store_result();
				if ($check_query->num_rows == 0) {
					return true;
				}
			} else {
				$this->parent->parent->debug($this::name_space .
					': Admin menu unregister query failed!');
			}
		} else {
			$this->parent->parent->debug($this::name_space . ': Check query failed!');
		}
		return false;
	}

	/**
	 * Uninstaller::_unregisterMenus()
	 * 
	 * @return
	 */
	public function _unregisterMenus()
	{
		if ($this->module_id === null) {
			$this->parent->parent->debug($this::name_space .
				': Skipping menu... no module ID was provided');
			return new ActionResult($this, '/admin/modules/uninstall/', 0,
				'Unregistering pages...', B_T_FAIL, array(
				'step' => 7,
				'status' => 1,
				'msg' => 'Unregistering pages...'));
		} else {
			$this->parent->parent->debug($this::name_space . ': Unregistering menu...');
			$check_query = $this->mySQL_r->prepare("SELECT `MID` FROM `core_menu` WHERE `PID` BETWEEN ? AND ?");
			$unregister_query = $this->mySQL_w->prepare("DELETE FROM `core_menu` WHERE `PID` BETWEEN ? AND ?");
			if ($check_query !== false) {
				if ($unregister_query !== false) {
					$start = ($this->module_id * 1000);
					$end = (($this->module_id + 1) * 1000) - 1;
					$unregister_query->bind_param('ii', $start, $end);
					$unregister_query->execute();

					$check_query->bind_param('ii', $start, $end);
					$check_query->execute();
					$check_query->store_result();
					if ($check_query->num_rows == 0) {
						$start = 1000000 + ($this->module_id * 1000);
						$end = 1000000 + (($this->module_id + 1) * 1000) - 1;
						$unregister_query->bind_param('ii', $start, $end);
						$unregister_query->execute();

						$check_query->bind_param('ii', $start, $end);
						$check_query->execute();
						$check_query->store_result();
						if ($check_query->num_rows == 0) {
							return new ActionResult($this, '/admin/modules/uninstall/', 0,
								'Unregistering pages...', B_T_FAIL, array(
								'step' => 7,
								'status' => 1,
								'msg' => 'Unregistering pages...'));
						} else {
							$this->parent->parent->debug($this::name_space .
								': Failed to unregister pages from menu!');
						}
					} else {
						$this->parent->parent->debug($this::name_space .
							': Failed to unregister pages from menu!');
					}
				} else {
					$this->parent->parent->debug($this::name_space .
						': Menu page unregister query failed!');
				}
			} else {
				$this->parent->parent->debug($this::name_space . ': Check query failed!');
			}
			return new ActionResult($this, '/admin/modules/uninstall/', 0,
				'Failed to unregister pages from menu!', B_T_FAIL, array('status' => 0, 'msg' =>
					'Failed to unregister pages from menu!'));
		}
	}

	/**
	 * Uninstaller::_unregisterPages()
	 * 
	 * @return
	 */
	public function _unregisterPages()
	{
		$this->parent->parent->debug($this::name_space . ': Unregistering pages...');
		$check_query = $this->mySQL_r->prepare("SELECT `ID` FROM `core_pages` WHERE `ID` BETWEEN ? AND ?");
		$unregister_query = $this->mySQL_w->prepare("DELETE FROM `core_pages` WHERE `ID` BETWEEN ? AND ?");
		if ($check_query !== false) {
			if ($unregister_query !== false) {
				$ns = strtolower($this->module_ns);
				
				$id_l = ($this->module_id * 1000);
				$id_u = (($this->module_id + 1) * 1000) - 1;
				$unregister_query->bind_param('ii', $id_l, $id_u);
				$unregister_query->execute();
				
				usleep(400000);
				$check_query->bind_param('ii', $id_l, $id_u);
				$check_query->execute();
				$check_query->store_result();
				if ($check_query->num_rows == 0) {
					return new ActionResult($this, '/admin/modules/uninstall/', 0,
						'Unregistering module...', B_T_FAIL, array(
						'step' => 8,
						'status' => 1,
						'msg' => 'Unregistering module...'));
				} else {
					$this->parent->parent->debug($this::name_space . ': Failed to unregister pages!');
				}
			} else {
				$this->parent->parent->debug($this::name_space .
					': Page unregister query failed!');
			}
		} else {
			$this->parent->parent->debug($this::name_space . ': Check query failed!');
		}
		return new ActionResult($this, '/admin/modules/uninstall/', 0,
			'Failed to unregister pages!', B_T_FAIL, array('status' => 0, 'msg' =>
				'Failed to unregister pages!'));
	}

	/**
	 * Uninstaller::_unregisterModule()
	 * 
	 * @return
	 */
	public function _unregisterModule()
	{
		$this->parent->parent->debug($this::name_space . ': Unregistering module...');
		$check_query = $this->mySQL_r->prepare("SELECT `module_id` FROM `core_modules` WHERE `namespace`=?");
		$unregister_query = $this->mySQL_w->prepare("DELETE FROM `core_modules` WHERE `namespace`=?");
		if ($check_query !== false) {
			if ($unregister_query !== false) {
				$unregister_query->bind_param('s', $this->module_ns);
				$unregister_query->execute();

				$check_query->bind_param('s', $this->module_ns);
				$check_query->execute();
				$check_query->store_result();
				if ($check_query->num_rows == 0) {
					$reset_fetch_query = $this->mySQL_r->query("SELECT MAX(`module_id`) + 1 FROM `core_modules`");
					$auto_inc = array_values($reset_fetch_query->fetch_assoc());
					$reset_query = $this->mySQL_w->query("ALTER TABLE `core_modules` AUTO_INCREMENT = " .
						$auto_inc[0]);
					return new ActionResult($this, '/admin/modules/uninstall/', 0,
						'Removing module files...', B_T_FAIL, array(
						'step' => 9,
						'status' => 1,
						'msg' => 'Removing module files...'));
				} else {
					$this->parent->parent->debug($this::name_space .
						': Failed to unregister module!');
				}
			} else {
				$this->parent->parent->debug($this::name_space .
					': Module unregister query failed!');
			}
		} else {
			$this->parent->parent->debug($this::name_space . ': Check query failed!');
		}
		return new ActionResult($this, '/admin/modules/uninstall/', 0,
			'Failed to unregister module!', B_T_FAIL, array('status' => 0, 'msg' =>
				'Failed to unregister module!'));
	}

	/**
	 * Uninstaller::_removeModule()
	 * 
	 * @return
	 */
	public function _removeModule()
	{
		$this->parent->parent->logEvent($this::name_space, 'Successfully uninstalled module '.$this->module_ns);
		if (WebApp::rmDir($this->module_dir)) {
			if (!is_dir($this->module_dir)) {
				return new ActionResult($this, '/admin/modules/uninstall/', 1,
					'Uninstallation complete! Module has been removed.', B_T_SUCCESS, array(
					'step' => 10,
					'status' => 1,
					'msg' => 'Uninstallation complete! Module has been removed.'));
			} else {
				$this->parent->parent->debug($this::name_space .
					': Module directory still exists at "' . $this->module_dir . '"');
			}
		}
		return new ActionResult($this, '/admin/modules/uninstall/', 1,
			'Failed to remove module directory, but module was still uninstalled!',
			B_T_WARNING, array(
			'step' => 10,
			'status' => 1,
			'msg' => 'Failed to remove module directory, but module was still uninstalled!'));
	}
}

?>