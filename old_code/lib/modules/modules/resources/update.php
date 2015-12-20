<?php

/**
 * Updater Class
 *
 * @category   WebApp.Base.Updater
 * @package    update.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
*
*/

class Updater extends BaseAction
{
	const name_space = 'Module.Modules';
	const version = '1.0.0';

	protected $module_ns = '';
	protected $module_dir = '';
	protected $version = '';
	protected $backup = 0;
	protected $update_dir = '';
	public $mySQL_r; // Read Handle
	public $mySQL_w; // Read Handle

	/**
	 * Updater::__construct()
	 * 
	 * @param mixed $parent
	 * @return
	 */
	function __construct($parent)
	{
		ignore_user_abort(true);
		$this->parent = $parent;
		$this->mySQL_r = $parent->mySQL_r;
		$this->mySQL_w = $parent->mySQL_w;

		// Include uninstaller so we can remove objects we need
		require_once dirname(__file__) . '/uninstall.php';

		// Include the installer so we can replace the old objects with new
		require_once dirname(__file__) . '/install.php';

		$this->parent->parent->debug('***** ' . $this::name_space . ' - Updater *****');
		$this->parent->parent->debug($this::name_space . ': Version ' . $this::version);
	}

	// public preUpdate() - Prepares the environment and files for updating
	/**
	 * Updater::preUpdate()
	 * 
	 * @return
	 */
	public function preUpdate()
	{
		$conf = WebApp::post('conf');
		$module = WebApp::post('mod');
		$page = WebApp::post('page');
		$mode = WebApp::post('method');
		if ($conf != 1) {
			Session::set($this::name_space, 'msg', 'You haven\'t confirmed this action!');
			$this->parent->parent->addHeader('Location', '/admin/modules/update/' . $module);
			return new ActionResult($this, '/admin/modules/update/' . $module, 0, '',
				B_T_FAIL);
		}
		// Check which mode we are operating in
		if ($mode == 'zip') {

			// Get the ZIP file
			$file = $this->parent->parent->files('zip_file');

			// Deal with upload errors
			switch($file) {
					// Failed to upload (we couldn't find it)
				case _ACTION_FAIL_1:
					$this->parent->parent->debug($this::name_space .
						': Module package failed to upload.');
					Session::set($this::name_space, 'msg', 'Module package failed to upload.');
					$this->parent->parent->addHeader('Location', '/admin/modules/update/' . $module);
					return new ActionResult($this, '/admin/modules/update/' . $module, 0,
						'Module package failed to upload.', B_T_FAIL);
					break;
					// No file was uploaded
				case _ACTION_FAIL_2:
					$this->parent->parent->debug($this::name_space .
						': No module package was uploaded to update!');
					Session::set($this::name_space, 'msg',
						'No module package was uploaded to update!');
					$this->parent->parent->addHeader('Location', '/admin/modules/update/' . $module);
					return new ActionResult($this, '/admin/modules/update/' . $module, 0,
						'No module package was uploaded to update!', B_T_FAIL);
					break;
					// Uploade was too large
				case _ACTION_FAIL_3:
					$this->parent->parent->debug($this::name_space .
						': Module was larger than the max upload size');
					Session::set($this::name_space, 'msg',
						'Module was larger than the max upload size!');
					$this->parent->parent->addHeader('Location', '/admin/modules/update/' . $module);
					return new ActionResult($this, '/admin/modules/update/' . $module, 0,
						'Module was larger than the max upload size!', B_T_FAIL);
					break;
					// File wasn't in whitelist/was in blacklist
				case _ACTION_FAIL_4:
					$this->parent->parent->debug($this::name_space . ': Incorrect module format!');
					$this->parent->parent->addHeader('Location', '/admin/modules/update/' . $module);
					return new ActionResult($this, '/admin/modules/update/' . $module, 0,
						'Incorrect module format!', B_T_FAIL);
					break;
					// For some reason we couldn't move the uploaded file from the system temp dir into our temp dir (__EXECDIR__/temp)
				case _ACTION_FAIL_5:
					$this->parent->parent->debug($this::name_space .
						': Could not access module package.');
					Session::set($this::name_space, 'msg', 'Could not access module package!');
					$this->parent->parent->addHeader('Location', '/admin/modules/update/' . $module);
					return new ActionResult($this, '/admin/modules/update/' . $module, 0,
						'Could not access module package.', B_T_FAIL);
					break;
					// Something else went wrong with the upload - probably left for future php updates
				case _ACTION_UNSPEC:
					$this->parent->parent->debug($this::name_space .
						': Something went wrong with the upload, try again');
					Session::set($this::name_space, 'msg',
						'Something went wrong with the upload, try again!');
					$this->parent->parent->addHeader('Location', '/admin/modules/update/' . $module);
					return new ActionResult($this, '/admin/modules/update/' . $module, 0,
						'Something went wrong with the upload, try again', B_T_FAIL);
					break;

					// There were no errors so we can continue
				default:

					// Extract the zip file
					$file = $this->extractZip($file);

					// Use the temp dir (from the extraction)
					if ($file === false) {
						// The uploaded wasn't a zip, so give the user a message to say so
						Session::set($this::name_space, 'msg', 'Failed to extract zip file!');

						// Now we send them back to the update page so they can select the correct file (hopefully)
						$this->parent->parent->addHeader('Location', '/admin/modules/update/' . $module);
						return new ActionResult($this, '/admin/modules/update/' . $module, 0,
							'Failed to extract zip file!', B_T_FAIL);
					}
					// Create a random reference hash
					$hash = ranString(4);

					// Set the session variables
					Session::set($this::name_space, 'update_from_' . $hash . '_dir', $file);
					Session::set($this::name_space, 'update_from_' . $hash . '_ns', $module);
					Session::set($this::name_space, 'update_from_' . $hash . '_page', $page);

					// Navigate to the new page
					$this->parent->parent->addHeader('Location', '/admin/modules/update/' . $module .
						'/' . $hash);
					// We still need to return what we are doing to the controller (don't remove... took ages to work out why it crashed here!)
					return new ActionResult($this, '/admin/modules/update/' . $module . '/' . $hash,
						1, '', B_T_SUCCESS);
			}

			// We are updating from a directory so we can bypass the zip extraction bits and bobs
		} elseif ($mode == 'dir') {

			// Get the full directory path
			$file = __EXECDIR__ . WebApp::post('directory');

			// Create a random reference hash
			$hash = ranString(4);

			// Set the session variables
			Session::set($this::name_space, 'update_from_' . $hash . '_dir', $file);
			Session::set($this::name_space, 'update_from_' . $hash . '_ns', $module);
			Session::set($this::name_space, 'update_from_' . $hash . '_page', $page);

			// Navigate to the new page
			$this->parent->parent->addHeader('Location', '/admin/modules/update/' . $module .
				'/' . $hash);
			// We still need to return what we are doing to the controller [don't remove... yup, same mistake twice :-)]
			return new ActionResult($this, '/admin/modules/update/' . $module . '/' . $hash,
				1, '', B_T_SUCCESS);
		}
	}

	// public extractZip(str $location) - Extracts the zip from the uploaded file and double checks to make sure it's there
	/**
	 * Updater::extractZip()
	 * 
	 * @param mixed $location
	 * @return
	 */
	public function extractZip($location)
	{
		$location = WebApp::extractZip($location);
		if ($location !== false) {
			return $location;
		} else {
			return false;
		}
	}

	/* public update(int $step=NULL)
	* Performs a module update step
	*
	* Update Reference Steps:
	* (1) _checkModule()		[302]
	* (2) _backUp()			[422]
	* (3) _updatePayload()		[482]
	* (4) _updateModule()		[496]
	* (5) _updatePages()		[557]
	* (6) _updateAdmin()		[584]
	* (7) _updateGroups()		[609]
	* (8) _
	*/
	/**
	 * Updater::update()
	 * 
	 * @param mixed $step
	 * @return
	 */
	public function update($step = null)
	{
		ignore_user_abort(true);
		$hash = WebApp::get('id');
		$location = Session::get($this::name_space, 'update_from_' . $hash . '_dir');
		$module_ns = Session::get($this::name_space, 'update_from_' . $hash . '_ns');

		if ($location !== null) {

			$this->module_ns = $module_ns;
			$this->module_dir = __MODULE__ . '/' . strtolower($module_ns);
			$this->update_dir = $location;


			if ($this->_getCurrentDetails()) {
				$this->uninstaller = new Uninstaller($this->parent, array(
					'dir' => $this->module_dir,
					'ns' => $this->module_ns,
					'id' => $this->module_id));
				$this->installer = new Installer($this->parent, array(
					'dir' => $this->update_dir,
					'ns' => $this->module_ns,
					'id' => $this->module_id));

				$this->parent->parent->debug($this::name_space . ': Module namespace is "' . $this->
					module_ns . '".');
				$this->parent->parent->debug($this::name_space .
					': Temporary module directory is "' . str_replace(__EXECDIR__, '', $location) .
					'".');
				$this->parent->parent->debug($this::name_space . ': Module directory is "' .
					str_replace(__EXECDIR__, '', $this->module_dir) . '".');
				$this->step = $step;
				if ($step === null) {
					return new ActionResult($this, '/admin/modules/update/', 0,
						'Failed to process update step!', B_T_FAIL, array('status' => 0, 'msg' =>
							'Failed to process update step!'));
				} else {
					$this->parent->parent->debug("Step $step");
					switch($step) {
						case 1:
							$state = $this->_checkModule();
							break;
						case 2:
							$state = $this->_checkModule();
							if ($state->data['status']) {
								$this->parent->parent->debug("backing up");
								$state = $this->_backUp();
							}
							break;
						case 3:
							$state = $this->_checkModule();
							if ($state->data['status']) {
								Session::set($this::name_space, 'update_from_' . WebApp::get('id') . '_inprog', true);
								$state = $this->_updatePayload();
							}
							break;
						case 4:
							$state = $this->_checkModule();
							if ($state->data['status']) {
								$state = $this->_updateModule();
							}
							break;
						case 5:
							$state = $this->_checkModule();
							if ($state->data['status']) {
								$state = $this->_updatePages();
							}
							break;
						case 6:
							$state = $this->_checkModule();
							if ($state->data['status']) {
								$state = $this->_updateAdmin();
							}
							break;
						case 7:
							$state = $this->_checkModule();
							if ($state->data['status']) {
								$state = $this->_updateGroups();
							}
							break;
						case 8:
							$state = $this->_checkModule();
							if ($state->data['status']) {
								$state = $this->_updateCron();
							}
							break;
						case 9:
							$state = $this->_checkModule();
							if ($state->data['status']) {
								$state = $this->_moduleUpdater();
							}
							break;
						case 10:
							$state = $this->_checkModule();
							if ($state->data['status']) {
								$state = $this->_cleanUpdate();
							}
							break;
						default:
							$state = new ActionResult($this, '/admin/modules/update', 0,
								'Failed to perform update step!<br />Error: <code>Update step not found</code>',
								B_T_FAIL);
					}
					if ($state->data['status'] === 0) {
						$this->_revertUpdate();
					}
					return $state;
				}
			} else {
				return new ActionResult($this, '/admin/modules/', 0,
					'Failed to update module - couldn\'t find module in database.', B_T_FAIL);
			}
		} else {
			return new ActionResult($this, '/admin/modules/', 0, 'Invalid updater ID!',
				B_T_FAIL);
		}
	}

	// private _getCurrentDetails() - Gets the current modules details from the database
	/**
	 * Updater::_getCurrentDetails()
	 * 
	 * @return
	 */
	private function _getCurrentDetails()
	{
		// Get version number and backup
		$detail_q = $this->mySQL_r->prepare("SELECT `module_id`, `version`,`backup` FROM `core_modules` WHERE `namespace`=?");
		$detail_q->bind_param('s', $this->module_ns);
		$detail_q->execute();
		$detail_q->store_result();
		if ($detail_q->num_rows == 1) {
			$detail_q->bind_result($module_id, $version, $backup);
			while ($detail_q->fetch()) {
				$version = explode('.', $version);
				$v['version'] = $version[0];
				$v['major'] = $version[1];
				$v['minor'] = $version[2];
				$this->version = $v;
				$this->backup = $backup;
				$this->module_id = $module_id;
			}
			return true;
		} else {
			return false;
		}
	}

	// private _checkModule() - Checks the uploaded module and loads the new module.xml
	/**
	 * Updater::_checkModule()
	 * 
	 * @return
	 */
	private function _checkModule()
	{
		$this->parent->parent->debug($this::name_space . ': Checking "module.xml"...');

		// Also make sure we can find the module.xml, otherwise it's mission abort
		if (file_exists($this->update_dir . '/module.xml')) {

			// Create the module.xml DOMDocument object
			$this->module = new DOMDocument;

			// Get the contents of module.xml
			$xml = file_get_contents($this->update_dir . '/module.xml');

			// Check that we got the contents
			if ($xml !== false) {

				// Now load this into the DOMDocument object - we are suppressing errors so if there is an XML
				// error, we can handle it ourselves properly without the script crashing to a halt
				if (@$this->module->loadXML($xml)) {

					// Fetch the core node
					$core = $this->module->getElementsByTagName('core');

					// We should check that there is core definition, otherwise the module is broken
					if ($core->length == 1) {

						// We need to pass a DOMElement to XMLCut
						$core = $core->item(0);

						// Fetch the namespace node, this should match the namespace for our current module, otherwise
						// something has gone wrong
						if ($this->module_ns != XMLCut::fetchTagValue($core, 'namespace')) {

							// Yeah, something went wrong, we'll return the erro back to the controller
							return new ActionResult($this, '/admin/modules/update/', 0,
								'Update package does not match currently installed module!', B_T_FAIL, array('status' =>
									0, 'msg' => 'Update package does not match currently installed module!'));

							// Right, we're in the clear, time to carry on :)
						} else {

							// Now we double check the version to make sure we are not updating to an older version...
							// Get the new version
							$version = XMLCut::fetchTagValue($core, 'version');
							// Split it up into version, major, minor
							$version = explode('.', $version);
							if (($this->version['version'] <= $version[0] && $this->version['major'] <= $version[1]
								//&&$this->version['minor']<$version[2]
								&& $this->version['minor'] <= $version[2]
								// Temporary hack so I can debug the updater by calling steps manually
								) // The following line is used to check whether the update is in progress (so that the script
								// doesn't reject the update because we've already updated the modules table so the 2 version
							// numbers match exactly
							|| (Session::get($this::name_space, 'update_from_' . WebApp::get('id') .
								'_inprog') === true)) {
								// Move onto the next step
								return new ActionResult($this, '/admin/modules/update/', 0,
									'Backing up module...', B_T_SUCCESS, array(
									'step' => 2,
									'status' => 1,
									'msg' => 'Backing up module...'));
							} else {
								// Version is the same or older, so we tell the user
								return new ActionResult($this, '/admin/modules/update/', 0,
									'Update package has an older (or the same) version of the module!', B_T_FAIL,
									array('status' => 0, 'msg' =>
										'Update package has an older (or the same) version of the module!'));
							}
						}
					} else {
						$this->parent->parent->debug($this::name_space .
							': Failed to load "module.xml", no core definition!');
					}
				} else { // if(@$this->module->loadXML($xml))
					// We failed to load the XML... just means theres some errors in it which meant it couldn't be parsed
					$this->parent->parent->debug($this::name_space .
						': Failed to load "module.xml", invalid XML!');
				}
			}
		} else { // if(file_exists($this->update_dir.'/module.xml'))
			// Yup, couldn't find the module.xml, somewhere the dev of the module cocked up lol (probably me tbh)
			$this->parent->parent->debug($this::name_space . ': Couldn\'t find "module.xml"!');
		}
		// Return generic couldn't load the module update, everything else will be logged in the debug log, so we can find the error if we want
		return new ActionResult($this, '/admin/modules/update/', 0,
			'Failed to load module update!', B_T_FAIL, array('status' => 0, 'msg' =>
				'Failed to load module update!'));
	}

	// private _backUp() - Backs up a module so that we can easily reinstall it if things go pear shaped
	/**
	 * Updater::_backUp()
	 * 
	 * @return
	 */
	private function _backUp()
	{
		$this->parent->parent->debug($this::name_space . ': Backing up module dir...');

		// Create the temp dir path
		$tempDir = __TEMP__ . '/' . strtolower($this->module_ns) . '.bak';

		// Now if the directory already exists, remove it
		if (file_exists($tempDir) && is_dir($tempDir))
			WebApp::rmDir($tempDir);

		// (Re)create the temp dir
		mkdir($tempDir);

		// Now we copy the module files to the payload dir
		if (rcopy($this->module_dir, $tempDir . '/payload')) {

			// Copy the module.xml as we'll need it for restoration
			rename($tempDir . '/payload/module.xml', $tempDir . '/module.xml');
			$this->parent->parent->debug($this::name_space . ': Copied module to __TEMP__/"' .
				strtolower($this->module_ns) . '.bak"!');

			// If the module specified the backup flag when installed, we can now run the backup() function for it
			if ($this->backup) {
				@include ($this->module_dir . '/backup.php');
				if (is_callable('backup')) {
					backup();
				} else {
					$this->parent->parent->debug($this::name_space . ': Couldn\t find backup()!');
				}
			}
			// With that done, we can move onto the next step
			return new ActionResult($this, '/admin/modules/update/', 0,
				'Updating module files...', B_T_SUCCESS, array(
				'step' => 3,
				'status' => 1,
				'msg' => 'Updating module files...'));
		} else { // if(rcopy($this->module_dir, $tempDir.'/payload'))
			// Yup, couldn't back it up
			$this->parent->parent->debug($this::name_space . ': Failed to backup module!');
		}
		// Return generic couldn't back up the module
		return new ActionResult($this, '/admin/modules/update/', 0,
			'Failed to back up the module!', B_T_FAIL, array('status' => 0, 'msg' =>
				'Failed to back up the module!'));
	}

	// private _updatePayload() - Updates the payload with the new content
	/**
	 * Updater::_updatePayload()
	 * 
	 * @return
	 */
	private function _updatePayload()
	{
		$this->parent->parent->debug($this::name_space . ': Copying payload...');

		// Remove the existing module dir
		WebApp::rmDir($this->module_dir);

		// Copy the new payload [borrowing method from installer... tee-hee, pure laziness ;) ]
		if ($this->installer->_copyPayload()->data['status']) {
			return new ActionResult($this, '/admin/modules/update/', 0,
				'Reregistering module...', B_T_FAIL, array(
				'step' => 4,
				'status' => 1,
				'msg' => 'Reregistering module...'));
		}
		return new ActionResult($this, '/admin/modules/update/', 0,
			'Failed to copy payload!', B_T_FAIL, array('status' => 0, 'msg' =>
				'Failed to copy payload!'));
	}

	// private _updateModule() - Updates the modules registration
	/**
	 * Updater::_updateModule()
	 * 
	 * @return
	 */
	private function _updateModule()
	{
		$this->parent->parent->debug($this::name_space .
			': Updating module registration...');

		// Get the core details
		$core = $this->module->getElementsByTagName('core')->item(0);

		// Loop throught the new data saved 20 lines of code. No really, I had one of the foreach
		// block code for each of the variables :/
		foreach(array(
			'name',
			'version',
			'author',
			'authorUrl',
			'description',
			'copyright') as $var) {
			$$var = XMLCut::fetchTagValue($core, $var);
			if ($$var === null)
				$$var = '';

			$this->parent->parent->debug($this::name_space . ': Module ' . ucfirst($var) .
				': "' . $$var . '"');
		}
		// Do the same for the boolean variables
		foreach(array('backup', 'uninstall') as $var) {
			$$var = XMLCut::fetchTagValue($this->module, $var);
			$$var = $$var !== '' ? 1 : 0;
			$this->parent->parent->debug($this::name_space . ': Module ' . ucfirst($var) .
				': "' . $$var . '"');
		}

		// Update the database
		$update_query = $this->mySQL_w->prepare("UPDATE `core_modules` SET
			`name`=?, `version`=?, `description`=?, `author`=?, `authorUrl`=?, `copyright`=?, `backup`=?, `uninstall`=?
			WHERE `namespace`=?");

		// Check the that I didn't make a mistake in the query
		if ($update_query !== false) {
			$update_query->bind_param('ssssssiis', $name, $version, $description, $author, $authorUrl,
				$copyright, $backup, $uninstall, $this->module_ns);
			$update_query->execute();

			// Return success as the update will happen and we don't need to check for how many rows were affected
			return new ActionResult($this, '/admin/modules/install/', 0, 'Updating files...',
				B_T_SUCCESS, array(
				'step' => 5,
				'status' => 1,
				'msg' => 'Updating pages...'));
		} else {
			$this->parent->parent->debug($this::name_space . ': Register query failed!');
		}
		// Return failed to update registration
		return new ActionResult($this, '/admin/modules/update/', 0,
			'Failed to update module registration!', B_T_FAIL, array('status' => 0, 'msg' =>
				'Failed to update module registration!'));
	}

	// private _updatePages() - Updates the modules pages
	/**
	 * Updater::_updatePages()
	 * 
	 * @return
	 */
	private function _updatePages()
	{
		$this->parent->parent->debug($this::name_space . ': Updating pages...');
		
		if(Session::get($this::name_space, 'update_from_' . WebApp::get('id') . '_page')!=1){
			// Uninstall pages
			if ($this->uninstaller->_unregisterPages()->data['status'] != 1) {
				return new ActionResult($this, '/admin/modules/install/', 0,
				'Failed to unregister pages!', B_T_FAIL, array('status' => 0, 'msg' =>
					'Failed to unregister pages!'));
			}
		}
		// Now we can include the installer and register the pages
		if ($this->installer->_registerPages($this->module)->data['status']) {
			return new ActionResult($this, '/admin/modules/install/', 0,
				'Updating administration...', B_T_SUCCESS, array(
				'step' => 6,
				'status' => 1,
				'msg' => 'Updating administration...'));
		}
		return new ActionResult($this, '/admin/modules/install/', 0,
			'Failed to register pages!', B_T_FAIL, array('status' => 0, 'msg' =>
				'Failed to register pages!'));
	}

	// private _updateAdmin() - Updates the admin pages and menus
	/**
	 * Updater::_updateAdmin()
	 * 
	 * @return
	 */
	private function _updateAdmin()
	{
		$this->parent->parent->debug($this::name_space .
			': Reregistering admin pages and menus...');
		if(Session::get($this::name_space, 'update_from_' . WebApp::get('id') . '_page')!=1){
			// Uninstall pages
			if ($this->uninstaller->_unregisterAdmin()->data['status'] != 1) {
				return new ActionResult(
					$this,
					'/admin/modules/update/',
					0,
					'Failed to unregister admin!',
					B_T_FAIL,
					array(
						'status' => 0,
						'msg' => 'Failed to unregister admin!'
					)
				);
			}
		}else{
			if (!$this->uninstaller->_unregisterAdminMenu()) {
				return new ActionResult(
					$this,
					'/admin/modules/update/',
					0,
					'Failed to unregister admin menu!',
					B_T_FAIL,
					array(
						'status' => 0,
						'msg' => 'Failed to unregister admin menu!'
					)
				);
			}
		}
		
		if ($this->installer->_registerAdmin($this->module)->data['status']) {
			return new ActionResult($this, '/admin/modules/update/', 0, 'Updating groups...',
				B_T_SUCCESS, array(
				'step' => 7,
				'status' => 1,
				'msg' => 'Updating groups...'));

		} else {
			$this->parent->parent->debug($this::name_space . ': Failed to reinstall admin!');
		}
		
		return new ActionResult($this, '/admin/modules/update/', 0,
			'Failed to update admin!', B_T_FAIL, array('status' => 0, 'msg' =>
				'Failed to update admin!'));
	}

	// private _updateGroups() - Removes, then reinstalls the groups
	/**
	 * Updater::_updateGroups()
	 * 
	 * @return
	 */
	private function _updateGroups()
	{
		$this->parent->parent->debug($this::name_space . ': Updating groups...');

		// Unregister the groups
		if ($this->uninstaller->_unregisterGroups()->data['status']) {

			// Put the new ones in
			if ($this->installer->_registerGroups($this->module)->data['status']) {

				return new ActionResult($this, '/admin/modules/update/', 0,
					'Updating cron jobs...', B_T_SUCCESS, array(
					'status' => 1,
					'msg' => 'Updating cron jobs...',
					'step' => 8));
			} else {
				$this->parent->parent->debug($this::name_space . ': Failed to reinstall groups!');
			}
		} else {
			$this->parent->parent->debug($this::name_space . ': Failed to uninstall groups!');
		}
		return new ActionResult($this, '/admin/modules/update/', 0,
			'Failed to update groups!', B_T_FAIL, array('status' => 0, 'msg' =>
				'Failed to update groups!'));
	}

	private function _updateCron()
	{
		$this->parent->parent->debug($this::name_space . ': Updating cron jobs...');

		// Unregister the groups
		if ($this->uninstaller->_unregisterCron()->data['status']) {

			// Put the new ones in
			if ($this->installer->_registerCron($this->module)->data['status']) {

				return new ActionResult($this, '/admin/modules/update/', 0,
					'Running module updater...', B_T_SUCCESS, array(
					'status' => 1,
					'msg' => 'Running module updater...',
					'step' => 9));
			} else {
				$this->parent->parent->debug($this::name_space . ': Failed to reinstall cron jobs!');
			}
		} else {
			$this->parent->parent->debug($this::name_space . ': Failed to uninstall cron jobs!');
		}
		return new ActionResult($this, '/admin/modules/update/', 0,
			'Failed to update cron jobs!', B_T_FAIL, array('status' => 0, 'msg' =>
				'Failed to update cron job!'));
	}
	
	// private _moduleUpdater() - Runs the module update script
	/**
	 * Updater::_moduleUpdater()
	 * 
	 * @return
	 */
	private function _moduleUpdater()
	{
		$this->parent->parent->debug($this::name_space . ': Finding update.php...');

		// Find the script
		if (file_exists($this->update_dir . '/update.php')) {
			$this->parent->parent->debug($this::name_space . ': Found update.php!');

			// Include it
			require_once $this->update_dir . '/update.php';
			$this->parent->parent->debug($this::name_space . ': Calling update()...');

			// Execute it
			if (update($this)) {
				$this->parent->parent->debug($this::name_space .
					': update() successfully executed!');
				return new ActionResult($this, '/admin/modules/install/', 0,
					'Cleaning up update...', B_T_FAIL, array(
					'step' => 10,
					'status' => 1,
					'msg' => 'Cleaning up update...'));
			} else {
				$this->parent->parent->debug($this::name_space .
					': install() did not execute successfully! Reverting...');
				revertInstall($this);
				return new ActionResult($this, '/admin/modules/install/', 0,
					'Failed to install module!', B_T_FAIL, array('status' => 0, 'msg' =>
						'Failed to update module!'));
			}
		} else {
			$this->parent->parent->debug($this::name_space .
				': No update.php provided, finishing installation...');
			return new ActionResult($this, '/admin/modules/install/', 0, 'Cleaning up...',
				B_T_FAIL, array(
				'step' => 10,
				'status' => 1,
				'msg' => 'Cleaning up update...'));
		}
	}

	// private _cleanUpdate() - Cleans up after the update
	/**
	 * Updater::_cleanUpdate()
	 * 
	 * @return
	 */
	private function _cleanUpdate()
	{
		$this->parent->parent->logEvent($this::name_space, 'Successfully updated module '.$this->module_ns);
		if (WebApp::rmDir($this->update_dir)) {
			$tempDir = __TEMP__ . '/' . strtolower($this->module_ns) . '.bak';
			if (WebApp::rmDir($tempDir)) {
				return new ActionResult($this, '/admin/modules/install/', 1, 'Update complete!',
					B_T_SUCCESS, array(
					'step' => 11,
					'status' => 1,
					'msg' => 'Update complete!'));
			}
		}
		return new ActionResult($this, '/admin/modules/install/', 0,
			'Failed to clean up update, but module was still updated!', B_T_WARNING, array(
			'step' => 11,
			'status' => 1,
			'msg' => 'Failed to clean up update, but module was still updated!'));
	}

	/**
	 * Updater::_revertUpdate()
	 * 
	 * @return
	 */
	private function _revertUpdate()
	{
		$this->parent->parent->debug($this::name_space . ': Reverting update...');
		/*require_once dirname(__FILE__).'/uninstall.php';
		require_once 'uninstall.php';
		$uninstaller = new Uninstaller($this->parent);
		if($this->step===NULL){
		return false;
		}
		$fetch_query = $this->mySQL_r->prepare("SELECT `module_id` FROM `core_modules` WHERE `namespace`=?");
		if($fetch_query!==false){
		$fetch_query->bind_param('s', $this->namespace);
		$fetch_query->bind_result($MOD_ID);
		$fetch_query->execute();
		$fetch_query->store_result();
		if($fetch_query->num_rows==1){
		while($fetch_query->fetch()){
		$mod_id = $MOD_ID;
		}
		}else{
		$mod_id = NULL;
		}
		$fetch_query->free_result();
		}
		$dir = __MODULE__.'/'.strtolower($this->namespace);
		$details = array(
		'dir'=>$dir,
		'id'=>$mod_id,
		'ns'=>$this->namespace
		);
		switch($this->step){
		case 7:
		case 6:
		$uninstaller->uninstall(3, $details);
		case 5:
		$uninstaller->uninstall(4, $details);
		case 4:
		$uninstaller->uninstall(6, $details);
		case 3:
		$uninstaller->uninstall(7, $details);
		case 2:
		$uninstaller->uninstall(8, $details);
		case 1:
		$this->parent->parent->debug($this::name_space.': Removing "'.str_replace($_SERVER['DOCUMENT_ROOT'],'', $this->update_dir).'"');
		WebApp::rmDir($this->update_dir);
		break;
		}*/

	}

}

?>