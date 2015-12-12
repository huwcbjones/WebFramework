<?php

/**
 * Installation Class
 *
 * @category   WebApp.Base.Installer
 * @package    install.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
*
*/

class Installer extends BaseAction
{
	const name_space = 'Module.Modules';
	const version = '1.0.0';

	protected $tempModule = '';
	protected $name = '';
	protected $namespace = '';
	public $mySQL_r; // Read Handle
	public $mySQL_w; // Read Handle

	/**
	 * Installer::__construct()
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
		$this->parent->parent->debug('***** ' . $this::name_space . ' - Installer *****');
		$this->parent->parent->debug($this::name_space . ': Version ' . $this::version);
		ignore_user_abort(true);

		if (count($details) == 0) {
			$hash = WebApp::get('id');
			$this->tempModule = Session::get($this::name_space, 'install_from' . $hash);
		} else {
			$this->tempModule = $details['dir'];
			$this->namespace = $details['ns'];
		}
	}

	/**
	 * Installer::preInstall()
	 * 
	 * @return
	 */
	public function preInstall()
	{
		// Get the details from post
		$mode = WebApp::post('method');

		// Check which mode we are operating in
		if ($mode == 'zip') {

			// Get the zip file
			$file = $this->parent->parent->files('zip_file');

			// Deal with upload errors
			switch($file) {
					// Failed to upload (we couldn't find it)
				case _ACTION_FAIL_1:
					$this->parent->parent->debug($this::name_space .
						': Module package failed to upload.');
					Session::set($this::name_space, 'msg', 'Module package failed to upload.');
					$this->parent->parent->addHeader('Location', '/admin/modules/install/');
					return new ActionResult($this, '/admin/modules/install', 0,
						'Module package failed to upload.', B_T_FAIL);
					break;
					// No file was uploaded
				case _ACTION_FAIL_2:
					$this->parent->parent->debug($this::name_space .
						': No module package was uploaded to install!');
					Session::set($this::name_space, 'msg',
						'No module package was uploaded to install!');
					$this->parent->parent->addHeader('Location', '/admin/modules/install/');
					return new ActionResult($this, '/admin/modules/install', 0,
						'No module package was uploaded to install!', B_T_FAIL);
					break;
					// Upload was too large
				case _ACTION_FAIL_3:
					$this->parent->parent->debug($this::name_space .
						': Module was larger than the max upload size');
					Session::set($this::name_space, 'msg',
						'Module was larger than the max upload size!');
					$this->parent->parent->addHeader('Location', '/admin/modules/install/');
					return new ActionResult($this, '/admin/modules/install', 0,
						'Module was larger than the max upload size!', B_T_FAIL);
					break;
					// File wasn't in whitelist/was in blacklist
				case _ACTION_FAIL_4:
					$this->parent->parent->debug($this::name_space . ': Incorrect module format!');
					Session::set($this::name_space, 'msg', 'Incorrect module format!');
					$this->parent->parent->addHeader('Location', '/admin/modules/install/');
					return new ActionResult($this, '/admin/modules/install', 0,
						'Incorrect module format!', B_T_FAIL);
					break;
					// For some reason we couldn't move the uploaded file from the system temp dir to our temp dir
				case _ACTION_FAIL_5:
					$this->parent->parent->debug($this::name_space .
						': Could not access module package.');
					Session::set($this::name_space, 'msg', 'Could not access module package!');
					$this->parent->parent->addHeader('Location', '/admin/modules/install/');
					return new ActionResult($this, '/admin/modules/install', 0,
						'Could not access module package.', B_T_FAIL);
					break;
					// Something else went wrong with the uplaod - probably left for future php updates
				case _ACTION_UNSPEC:
					$this->parent->parent->debug($this::name_space .
						': Something went wrong with the upload, try again');
					Session::set($this::name_space, 'msg',
						'Something went wrong with the upload, try again!');
					$this->parent->parent->addHeader('Location', '/admin/modules/install/');
					return new ActionResult($this, '/admin/modules/install', 0,
						'Something went wrong with the upload, try again', B_T_FAIL);
					break;

					// There were no erros so we can continue
				default:

					// Extract the zip file
					$file = $this->extractZip($file);

					// Use the temp dir (from the extraction)
					if ($file !== false) {

						// Generate a reference hash
						$hash = ranString(4);

						// Set the session reference
						Session::set($this::name_space, 'install_from' . $hash, $file);

						//Navigate to the instal page
						$this->parent->parent->addHeader('Location', '/admin/modules/install/' . $hash);
						// We still need to return an ActionResult object to the controller, otherwise it'll get its knickers in a twist
						return new ActionResult($this, '/admin/modules/install/' . $hash, 1,
							'', B_T_INFO);
					} else {

						// The uploaded file wasn't a zip, so give the user a message to see when they navigate
						Session::set($this::name_space, 'msg', 'Failed to extract zip file!');
						$this->parent->parent->addHeader('Location', '/admin/modules/install/');
						// Yet again we need to return an ActionResult object as stated above ^^
						return new ActionResult($this, '/admin/modules/install/', 0,
							'Failed to extract zip file!', B_T_FAIL);
					}
			}

			// We are installing from a directory, so we can skip the zip stuff and get straight to busines
		} elseif ($mode == 'dir') {

			// Get the full directory path
			$file = __EXECDIR__ . WebApp::post('directory');

			// Generate a reference hash
			$hash = ranString(4);

			// Set the install sesion stuff
			Session::set($this::name_space, 'install_from' . $hash, $file);

			// Navigate to the install page
			$this->parent->parent->addHeader('Location', '/admin/modules/install/' . $hash);
			// Yup, we are returning an ActionResult again... are you getting the message yet?
			return new ActionResult($this, '/admin/modules/install/' . $hash, 1, 'Installing module&hellip;',
				B_T_SUCCESS);
		}
	}

	// Extracts the zip from the uploaded file and double checks to make sure it's there
	// Tbh, I could move this to the BaseAction class to save a few lines of code everywhere this function appears
	// Future job ^^ when you get time of course :P
	/**
	 * Installer::extractZip()
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

	// The big function that handles the installation steps
	// The install steps are public so that they can be called from the Updater class
	// but otherwise all the install steps have to be called direct through this object.
	// This allows the update script (or others) to bypass the pre-module checks (which would be
	// bad if we are updating a module because the installer would exit the install because the module
	// is already installed! Well, it is, but we just want to borrow the methods because we're lazy
	// Well now you know why! :) (Btw, that applies to the methods in the other resource files)

	/**
	 * Installer::install()
	 * 
	 * @param mixed $step
	 * @return

	 */
	public function install($step = null)
	{

		// Check we actually have an install from dir - well you never know
		if ($this->tempModule === null) {
			return new ActionResult(
				$this,
				'/admin/modules/install/',
				0,
				'Invalid installer ID!',
				B_T_FAIL
			);
		} 
		$this->parent->parent->debug($this::name_space .
			': Temporary module directory is "' . str_replace(__EXECDIR__, '', $this->
			tempModule) . '".');

		$this->step = $step;

		// Check that we have an install step
		if ($step === null | trim($step) === '') {
			return new ActionResult(
				$this,
				'/admin/modules/install/',
				0,
				'Failed to process installation step!',
				B_T_FAIL,
				array(
					'status' => 0,
					'msg' => 'Failed to process installation step!'
				)
			);
		}
		switch($step) {
			case 1:
				if ($this->_loadModule()) {
					$state = new ActionResult(
						$this,
						'/admin/modules/install/',
						0,
						'Registering module...',
						B_T_SUCCESS,
						array(
							'status' => 1,
							'msg' => 'Registering module...'
						)
					);
				} else {
					$state = new ActionResult(
						$this,
						'/admin/modules/install/',
						0,
						'Failed to load module!',
						B_T_FAIL,
						array(
							'status' => 0,
							'msg' => 'Failed to load module!'
						)
					);
				}
				break;
			case 2:
				if ($this->_loadModule()) {
					$state = $this->_copyPayload();
				} else {
					$state = new ActionResult(
						$this,
						'/admin/modules/install/',
						0,
						'Failed to load module!',
						B_T_FAIL,
						array(
							'status' => 0,
							'msg' => 'Failed to load module!'
						)
					);
				}
				break;
			case 3:
				if ($this->_loadModule()) {
					$state = $this->_registerModule();
				} else {
					$state = new ActionResult(
						$this,
						'/admin/modules/install/',
						0,
						'Failed to load module!',
						B_T_FAIL,
						array(
							'status' => 0,
							'msg' => 'Failed to load module!'
						)
					);
				}
				break;
			case 4:
				if ($this->_loadModule()) {
					$state = $this->_registerPages();
				} else {
					$state = new ActionResult(
						$this,
						'/admin/modules/install/',
						0,
						'Failed to load module!',
						B_T_FAIL,
						array(
							'status' => 0,
							'msg' => 'Failed to load module!'
						)
					);
				}
				break;
			case 5:
				if ($this->_loadModule()) {
					$state = $this->_registerAdmin();
				} else {
					$state = new ActionResult(
						$this,
						'/admin/modules/install/',
						0,
						'Failed to load module!',
						B_T_FAIL,
						array(
							'status' => 0,
							'msg' => 'Failed to load module!'
						)
					);
				}
				break;
			case 6:
				if ($this->_loadModule()) {
					$state = $this->_registerGroups();
				} else {
					$state = new ActionResult(
						$this,
						'/admin/modules/install/',
						0,
						'Failed to load module!',
						B_T_FAIL,
						array(
							'status' => 0,
							'msg' => 'Failed to load module!'
						)
					);
				}
				break;
			case 7:
				if ($this->_loadModule()) {
					$state = $this->_registerCron();
				} else {
					$state = new ActionResult(
						$this,
						'/admin/modules/install/',
						0,
						'Failed to load module!',
						B_T_FAIL,
						array(
							'status' => 0,
							'msg' => 'Failed to load module!'
						)
					);
				}
				break;
			case 8:
				if ($this->_loadModule()) {
					$state = $this->_installModule();
				} else {
					$state = new ActionResult(
						$this,
						'/admin/modules/install/',
						0,
						'Failed to load module!',
						B_T_FAIL,
						array(
							'status' => 0,
							'msg' => 'Failed to load module!'
						)
					);
				}
				break;
			case 9:
				if ($this->_loadModule()) {
					$state = $this->_cleanInstall();
				} else {
					$state = new ActionResult(
						$this,
						'/admin/modules/install/',
						0,
						'Failed to load module!',
						B_T_FAIL,
						array(
							'status' => 0,
							'msg' => 'Failed to load module!'
						)
					);
				}
				break;
		} // switch($step)
		
		$state->data['step'] = $step + 1;
		if ($state->data['status'] === 0) $this->_revertInstall();
		
		return $state;
	}

	// public _loadModule() - Loads the new module
	/**
	 * Installer::_loadModule()
	 * 
	 * @return
	 */
	public function _loadModule()
	{
		$this->parent->parent->debug($this::name_space . ': Loading "module.xml"...');
		
		if (!file_exists($this->tempModule . '/module.xml')) {
			$this->parent->parent->debug($this::name_space . ': Couldn\'t find "module.xml"!');
			return false;
		}
		
		$this->module = new DOMDocument;
		
		$xml = file_get_contents($this->tempModule . '/module.xml');
		
		if ($xml === false) {
			return false;
		}
		// Suppressing errors so that the script doesn't commit suicide (which wouldn't be nice for our users)
		if (@$this->module->loadXML($xml)===false) {
			$this->parent->parent->debug($this::name_space . ': Failed to load "module.xml", invalid XML!');
			return false;
		} 

		$core = $this->module->getElementsByTagName('core');
		
		if ($core->length != 1) {
			$this->parent->parent->debug($this::name_space . ': Failed to load "module.xml", no core definition!');
			return false;
		}
		
		$core = $core->item(0);
		
		$this->name			= XMLCut::fetchTagValue($core, 'name');
		$this->namespace	= XMLCut::fetchTagValue($core, 'namespace');
		$this->parent->parent->debug($this::name_space . ': Module name is "' . $this->name . '"');
		$this->parent->parent->debug($this::name_space . ': Loaded "module.xml"!');
		return true;
		
	}

	/**
	 * Installer::_copyPayload()
	 * 
	 * @return
	 */
	public function _copyPayload()
	{
		$this->parent->parent->debug($this::name_space . ': Copying payload...');

		// Recursively copies the payload to the module dir
		if (!rcopy($this->tempModule . '/payload/', __MODULE__ . '/' . strtolower($this->namespace))) {
			$this->parent->parent->debug($this::name_space . ': Failed to copy payload!');
			return new ActionResult(
				$this,
				'/admin/modules/install/',
				0,
				'Failed to copy payload!',
				B_T_FAIL,
				array(
					'status' => 0,
					'msg' =>'Failed to copy payload!'
				)
			);
		}
		$this->parent->parent->debug($this::name_space . ': Copied payload!');

		// Copies the module.xml for later use (backing up)
		
		copy(
			$this->tempModule . '/module.xml',
			__MODULE__ . '/' . strtolower($this->namespace) . '/module.xml'
		);

		// Return the status
		return new ActionResult(
			$this,
			'/admin/modules/install/',
			0,
			'Registering pages...',
			B_T_SUCCESS,
			array(
				'status' => 1,
				'msg' => 'Registering pages...'
			)
		);
	}

	/**
	 * Installer::_registerModule()
	 * 
	 * @return
	 */
	public function _registerModule()
	{
		$this->parent->parent->debug($this::name_space . ': Checking database...');
		
		// Create check query to make sure the module doesn't already exist
		$check_query = $this->mySQL_r->prepare("SELECT `name` FROM `core_modules` WHERE `namespace`=?");
		
		if ($check_query === false) {
			$this->parent->parent->debug($this::name_space . ': Check query failed!');
			return new ActionResult(
				$this,
				'/admin/modules/install/',
				0,
				'Failed to register module!',
				B_T_FAIL,
				array(
					'status' => 0,
					'msg' => 'Failed to register module!'
				)
			);
		}
		// Set the query up
		$check_query->bind_param('s', $this->namespace);
		$check_query->execute();
		$check_query->store_result();
		
		// If a module already exists, then we will get 1 row back (unique column)
		if ($check_query->num_rows != 0) {
			$check_query->bind_result($name);
			
			while ($check_query->fetch()) {
				return new ActionResult(
					$this,
					'/admin/modules/install/',
					0,
					'Module with the same namespace (' . $name .
					') already exists! Try updating the module instead',
					B_T_FAIL,
					array(
						'status' => 0,
						'msg' => 'Module with the same namespace (' . $name .
						') already exists! Try updating the module instead'
					)
				);
			}
			$check_query->free_result();
		}
		
		// Loop through the new data (saved 20 lines of code... no really, I had one of the foreach
		// block code for each of the variables) :/
		foreach(array(
			'version',
			'author',
			'authorUrl',
			'description',
			'copyright') as $var
		) {
			$$var = XMLCut::fetchTagValue($this->module, $var);
			if ($$var === null) {
				$$var = '';
			}
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

		$this->parent->parent->debug($this::name_space . ': Registering module...');

		// Create the register query
		$register_query = $this->mySQL_w->prepare("INSERT INTO `core_modules`
		(
			`namespace`,
			`install_date`,
			`name`,
			`version`,
			`description`,
			`author`,
			`authorUrl`,
			`copyright`,
			`backup`,
			`uninstall`
		) VALUES (
			?,
			NOW(),
			?,
			?,
			?,
			?,
			?,
			?,
			?,
			?
		)");
	
		if ($register_query === false) {
			$this->parent->parent->debug($this::name_space . ': Register query failed!');
			return new ActionResult(
				$this,
				'/admin/modules/install/',
				0,
				'Failed to register module!',
				B_T_FAIL,
				array(
					'status' => 0,
					'msg' => 'Failed to register module!'
				)
			);
		}
		// Bind the arguments
		$register_query->bind_param('sssssssii',
			$this->namespace,
			$this->name,
			$version,
			$description,
			$author,
			$authorUrl,
			$copyright,
			$backup,
			$uninstall
		);
		
		$register_query->execute();
		$register_query->store_result();
		
		// We should have 1 new row now
		if ($register_query->affected_rows == 1) {
			return new ActionResult(
				$this,
				'/admin/modules/install/',
				0,
				'Copying payload...',
				B_T_SUCCESS,
				array(
					'status' => 1,
					'msg' => 'Copying payload...'
				)
			);
		} else {
			return new ActionResult(
				$this,
				'/admin/modules/install/',
				0,
				'Failed to register module, module may already exist in database',
				B_T_FAIL,
				array(
					'status' => 0,
					'msg' => 'Failed to register module, module may already exist in database'
				)
			);
		}
	}

	/**
	 * Installer::_registerPages()
	 * 
	 * @param mixed $xml
	 * @return
	 */
	public function _registerPages($xml = null)
	{
		$this->parent->parent->debug($this::name_space . ': Registering pages...');
		
		if ($xml === null) {
			$pages = $this->module->getElementsByTagName('page');
		} else {
			$pages = $xml->getElementsByTagName('page');
		}
		
		// Fetch the new module ID (we need this to create the pages)
		$fetch_query = $this->mySQL_r->prepare("SELECT `module_id` FROM `core_modules` WHERE `namespace`=?");
		if ($fetch_query === false) {
			$this->parent->parent->debug($this::name_space . ': Fetch query failed!');
			return new ActionResult(
				$this,
				'/admin/modules/install/',
				0,
				'Failed to register pages!',
				B_T_FAIL,
				array(
					'status' => 0,
					'msg' =>'Failed to register pages!'
				)
			);
		}
		// Create the register query
		$register_query = $this->mySQL_w->prepare(
"INSERT INTO `core_pages`
(`ID`,	`title`,	`cat1`,	`cat2`,	`cat3`,	`https`,	`desc`,	`css`,	`js`) VALUES
(?,		?,			?,		?,		?,		?,			?,		?,		?)
ON DUPLICATE KEY UPDATE
	`title`=VALUES(`title`),
	`cat1`=VALUES(`cat1`),
	`cat2`=VALUES(`cat2`),
	`cat3`=VALUES(`cat3`),
	`https`=VALUES(`https`),
	`desc`=VALUES(`desc`),
	`css`=VALUES(`css`),
	`js`=VALUES(`js`)
");
		if ($register_query === false) {
			$this->parent->parent->debug($this::name_space . ': Page register query failed!');
			return new ActionResult(
				$this,
				'/admin/modules/install/',
				0,
				'Failed to register pages!',
				B_T_FAIL,
				array(
					'status' => 0,
					'msg' =>'Failed to register pages!'
				)
			);
		}
		
		// Get the module ID number
		$fetch_query->bind_param('s', $this->namespace);
		$fetch_query->bind_result($MOD_ID);
		$fetch_query->execute();
		$fetch_query->store_result();
		if ($fetch_query->num_rows != 1) {
			$this->parent->parent->debug($this::name_space . ': Module isnt\'t registered!');
			return new ActionResult(
				$this,
				'/admin/modules/install/',
				0,
				'Failed to register pages!',
				B_T_FAIL,
				array(
					'status' => 0,
					'msg' =>'Failed to register pages!'
				)
			);
		}
		while ($fetch_query->fetch()) {
			$results = array();
			if ($pages->length > 0) {
				// Foreach of the pages in the xml, we register it
				foreach($pages as $page) {
					$id = ($MOD_ID * 1000) + (XMLCut::fetchTagValue($page, 'id'));
					$title = XMLCut::fetchTagValue($page, 'title');
					$cat1 = strtolower($this->namespace);
					$cat2 = XMLCut::fetchTagValue($page, 'cat2');
					if(trim($cat2) == '') $cat2 = NULL;
					$cat3 = XMLCut::fetchTagValue($page, 'cat3');
					if(trim($cat3) == '') $cat3 = NULL;
					$https = XMLCut::fetchTagValue($page, 'https');
					$https = ($https==true)? 1 : 0;
					$desc = XMLCut::fetchTagValue($page, 'desc');
					$css = XMLCut::fetchTagValue($page, 'css');
					$js = XMLCut::fetchTagValue($page, 'js');
					$register_query->bind_param('issssisss', $id, $title, $cat1, $cat2, $cat3, $https,
						$desc, $css, $js);
					$register_query->execute();
					$register_query->store_result();

					// Dump the result into an array
					if ($register_query->affected_rows == 1) {
						$this->parent->parent->debug($this::name_space . ': Registered page ID "' . $id .
							'"!');
						$results[] = true;
					} else {
						$this->parent->parent->debug($this::name_space . ': Registered page ID "' . $id .
							'"!');
						$results[] = false;
					}
				}
			} else {
				$results[] = true;
			}

			// If we inserted all the pages, we should have no falses in the array
			if (array_search(false, $results) !== true) {
				// We didn't, so next step
				return new ActionResult(
					$this,
					'/admin/modules/install/',
					0,
					'Registering module administration...',
					B_T_SUCCESS,
					array(
						'status' => 1,
						'msg' => 'Registering module administration...'
					)
				);
			} else {
				$this->parent->parent->debug($this::name_space . ': Failed to register pages!');
				return new ActionResult(
					$this,
					'/admin/modules/install/',
					0,
					'Failed to register pages!',
					B_T_FAIL,
					array(
						'status' => 0,
						'msg' =>'Failed to register pages!'
					)
				);
			}
		}
	}

	// public _registerAdmin(str $xml=NULL) - Registers the admin pages/menus
	/**
	 * Installer::_registerAdmin()
	 * 
	 * @param mixed $xml
	 * @return
	 */
	public function _registerAdmin($xml = null)
	{
		$this->parent->parent->debug($this::name_space .
			': Registering admin pages and menus...');

		// Fetch the new module ID (we need this to create the admin pages)
		$fetch_query = $this->mySQL_r->prepare("SELECT `module_id` FROM `core_modules` WHERE `namespace`=?");
		if ($fetch_query === false) {
			$this->parent->parent->debug($this::name_space . ': Fetch query failed!');
			return new ActionResult(
				$this,
				'/admin/modules/install/',
				0,
				'Failed to register administration!',
				B_T_FAIL,
				array(
					'status' => 0,
					'msg' => 'Failed to register administration!'
				)
			);
		}		// Get the module ID number
		$fetch_query->bind_param('s', $this->namespace);
		$fetch_query->bind_result($MOD_ID);
		$fetch_query->execute();
		$fetch_query->store_result();
		
		if ($fetch_query->num_rows != 1) {
			$this->parent->parent->debug($this::name_space . ': Module isnt\'t registered!');
			return new ActionResult(
				$this,
				'/admin/modules/install/',
				0,
				'Module isnt\'t registered!',
				B_T_FAIL,
				array(
					'status' => 0,
					'msg' => 'Module isnt\'t registered!'
				)
			);
		}
		
		while ($fetch_query->fetch()) {

			// Now we call the _registerdminPages and include the $xml in the args
			if (!$this->_registerAdminPages($MOD_ID, $xml)) {
				$this->parent->parent->debug($this::name_space . ': Failed to register admin pages!');
				return new ActionResult(
					$this,
					'/admin/modules/install/',
					0,
					'Failed to register admin pages!',
					B_T_FAIL,
					array(
						'status' => 0,
						'msg' => 'Failed to register admin pages!!'
					)
				);
			}

			// That worked well, now onto the menu items
			$this->parent->parent->debug($this::name_space . ': Registered admin pages!');

			// Now we call _registerAdminMenu and include the $xml in the args
			if (!$this->_registerAdminMenu($MOD_ID, $xml)) {
				$this->parent->parent->debug($this::name_space . ': Failed to register admin menus!');
				return new ActionResult(
					$this,
					'/admin/modules/install/',
					0,
					'Failed to register admin menus!',
					B_T_FAIL,
					array(
						'status' => 0,
						'msg' => 'Failed to register admin menus!'
					)
				);
			}
			// Return the success result
			$this->parent->parent->debug($this::name_space . ': Registered admin menus!');
			return new ActionResult(
				$this,
				'/admin/modules/install/',
				0,
				'Registering groups...',
				B_T_FAIL,
				array(
					'status' => 1,
					'msg' => 'Registering groups...'
				)
			);
		}
	}

	// pulic _registerAdminPages - Registers the admin pages
	/**
	 * Installer::_registerAdminPages()
	 * 
	 * @param mixed $MOD_ID
	 * @param mixed $xml
	 * @return
	 */
	public function _registerAdminPages($MOD_ID, $xml = null)
	{

		// Allows us to push an external source of xml in and register pages (from update.php)
		if ($xml === null) {
			$pages = $this->module->getElementsByTagName('admin');
		} else {
			$pages = $xml->getElementsByTagName('admin');
		}
		
		// Create the query
		$register_query = $this->mySQL_w->prepare(
"INSERT INTO `core_pages`
(`ID`,	`title`,	`cat1`,	`cat2`,	`cat3`,	`desc`,	`css`,	`js`) VALUES
(?,		?,			'admin',		?,		?,		?,		?,		?)
ON DUPLICATE KEY UPDATE
	`title`=VALUES(`title`),
	`cat1`=VALUES(`cat1`),
	`cat2`=VALUES(`cat2`),
	`cat3`=VALUES(`cat3`),
	`desc`=VALUES(`desc`),
	`css`=VALUES(`css`),
	`js`=VALUES(`js`)
");
		if ($register_query === false) {
			$this->parent->parent->debug($this::name_space . ': Admin page register query failed!');
			return false;
		}

		$results = array();

		// Check that we have pages to register
		if ($pages->length < 0) {
			return true;
		}
		// Loop through the pages and register them
		foreach($pages as $page) {

			// Work out the page details
			$id = 1000000 + ($MOD_ID * 1000) + (XMLCut::fetchTagValue($page, 'id'));
			$title = XMLCut::fetchTagValue($page, 'title');
			$cat2 = strtolower($this->namespace);
			// Hack to allow the admin dash to work!
			if($cat2 =='admin'){
				$cat2 = NULL;
			}
			$cat3 = XMLCut::fetchTagValue($page, 'cat');
			if($cat3 == '') $cat3 = NULL;
			$desc = XMLCut::fetchTagValue($page, 'desc');
			$css = XMLCut::fetchTagValue($page, 'css');
			$js = XMLCut::fetchTagValue($page, 'js');
			// Bind the params and register the page
			$register_query->bind_param('issssss', $id, $title, $cat2, $cat3, $desc,
				$css, $js);
			$register_query->execute();
			$register_query->store_result();

			// Dump the result into the results array
			if ($register_query->affected_rows == 1) {
				$this->parent->parent->debug($this::name_space . ': Registered admin page ID "' .
					$id . '"!');
				$results[] = true;
			} else {
				$this->parent->parent->debug($this::name_space . ': Registered admin page ID "' .
					$id . '"!');
				$results[] = false;
			}
		}

		// We shouldn't have any 'false' values in here if everything went to plan
		if (array_search(false, $results) !== true) {
			return true;
		} else {
			$this->parent->parent->debug($this::name_space . ': Failed to register admin pages!');
			return false;
		}
	}
	// public _registerAdminMenu - Registers the admin menu items
	/**
	 * Installer::_registerAdminMenu()
	 * 
	 * @param mixed $MOD_ID
	 * @param mixed $xml
	 * @return
	 */
	public function _registerAdminMenu($MOD_ID, $xml = null)
	{

		// Allows us to pump alternative xml into the function
		if ($xml === null) {
			$menu = $this->module->getElementsByTagName('menuitem');
		} else {
			$menu = $xml->getElementsByTagName('menuitem');
		}

		$ns = $this->namespace;

		// Create register queries
		$parent_query = $this->mySQL_w->prepare("INSERT INTO `core_admin`
			(`module_id`,	`parent`,	`PID`) VALUES
			(?,				NULL,		?)"
		);
		$register_query = $this->mySQL_w->prepare("INSERT INTO `core_admin`
			(`module_id`,	`parent`,	`PID`) VALUES
			(?,				?,			?)"
		);
		$fetch_query = $this->mySQL_w->prepare("SELECT `ID` FROM `core_admin` WHERE `module_id`=? AND `parent` IS NULL");
		// Check we didn't cock up the queries
		if ($fetch_query === false || $parent_query === false || $register_query === false) {
			$this->parent->parent->debug($this::name_space . ': Admin menu register query failed!');
			return false;
		}

		$results = array();

		// Calculate parent item details
		$PID = 1000000 + ($MOD_ID * 1000);

		// Bind params
		$parent_query->bind_param('ii', $MOD_ID, $PID);
		$parent_query->execute();
		$parent_query->free_result();

		$fetch_query->bind_param('i', $MOD_ID);
		$fetch_query->execute();
		$fetch_query->store_result();

		// Check we have a parent ID to bind the children to
		if ($fetch_query->num_rows != 1) {
			$this->parent->parent->debug($this::name_space . ': Failed to register main menu item!');
			$this->parent->parent->debug($this->mySQL_w->error);
			return false;
		}

		$fetch_query->bind_result($parent_MID);

		while ($fetch_query->fetch()) {
			// Check we have some menu items to register
			if ($menu->length < 0) {
				return true;
			}
			// Loop through and register them
			foreach($menu as $item) {

				// Get the details
				$PID = 1000000 + ($MOD_ID * 1000) + (XMLCut::fetchTagValue($item, 'id'));
				$link = XMLCut::fetchTagValue($item, 'link');
				;
				$register_query->bind_param('iii', $MOD_ID, $parent_MID, $PID);

				// Execute the query
				$register_query->execute();
				$register_query->store_result();

				// Dump the result into an array
				if ($register_query->affected_rows == 1) {
					$this->parent->parent->debug($this::name_space . ': Registered admin menu PID "' .
						$PID . '"!');
					$results[] = true;
				} else {
					$this->parent->parent->debug($this::name_space . ': Registered admin menu PID "' .
						$PID . '"!');
					$results[] = false;
				}
			}

			// Now we can check if there were any errors
			if (array_search(false, $results) !== true) {
				return true;
			} else {
				$this->parent->parent->debug($this::name_space . ': Failed to register admin menu!');
			}
		}
	}

	// public _registerGroups() - Registers groups for the module
	/**
	 * Installer::_registerGroups()
	 * 
	 * @param mixed $xml
	 * @return
	 */
	public function _registerGroups($xml = null)
	{
		$this->parent->parent->debug($this::name_space . ': Registering groups...');

		// Allows us to pump alternative xml into the function
		if ($xml === null) {
			$groups = $this->module->getElementsByTagName('group');
		} else {
			$groups = $xml->getElementsByTagName('group');
		}

		// Get the module ID so we can workout the group numbers
		$fetch_query = $this->mySQL_r->prepare("SELECT `module_id` FROM `core_modules` WHERE `namespace`=?");
		if ($fetch_query === false) {
			$this->parent->parent->debug($this::name_space . ': Fetch query failed!');
			return new ActionResult($this, '/admin/modules/install/', 0,
				'Failed to register groups!', B_T_FAIL, array('status' => 0, 'msg' =>
				'Failed to register groups!'));
		}

		// Create the register query
		$register_query = $this->mySQL_w->prepare("INSERT INTO `core_groups`
			(`GID`,	`name`,	`en`,	`type`, `desc`) VALUES
			(?,		?,		1,		's',	?)
		");
		$gpage_query = $this->mySQL_w->prepare("INSERT INTO `core_gpage` (`GID`,`PID`) VALUES(?,?)");

		if ($register_query === false) {
			$this->parent->parent->debug($this::name_space . ': Group register query failed!');
			return new ActionResult($this, '/admin/modules/install/', 0,
				'Failed to register groups!', B_T_FAIL, array('status' => 0, 'msg' =>
				'Failed to register groups!'));
		}
		
		// Get the module ID
		$fetch_query->bind_param('s', $this->namespace);
		$fetch_query->bind_result($MOD_ID);
		$fetch_query->execute();
		$fetch_query->store_result();
		if ($fetch_query->num_rows != 1) {
			$this->parent->parent->debug($this::name_space . ': Module isnt\'t registered!');
			return new ActionResult($this, '/admin/modules/install/', 0,
				'Failed to register groups!', B_T_FAIL, array('status' => 0, 'msg' =>
				'Failed to register groups!'));
		}
		while ($fetch_query->fetch()) {

			$results = array();

			// Check we have some groups to register
			if ($groups->length > 0) {
				$results[] = true;
			}
			// Loop throught the groups and register them
			foreach($groups as $group) {

				// Get the group details
				$id = ($MOD_ID * 1000) + (XMLCut::fetchTagValue($group, 'id'));
				$name = XMLCut::fetchTagValue($group, 'name');
				$desc = XMLCut::fetchTagValue($group, 'desc');
				$pages = array();

				// Loop throught the relative page IDs to create the absolute IDs
				$pgs = strgetcsv(XMLCut::fetchTagValue($group, 'pages'));
				foreach($pgs as $page) {
					$pages[] = ($MOD_ID * 1000) + $page;
				}
				$admin = strgetcsv(XMLCut::fetchTagValue($group, 'admin'));
				foreach($admin as $page) {
					$pages[] = 1000000 + ($MOD_ID * 1000) + $page;
				}
				
				// Bind the params
				$register_query->bind_param('iss', $id, $name, $desc);

				// Execute the query
				$register_query->execute();
				$register_query->store_result();

				foreach($pages as $PID){
					$this->parent->parent->debug($this::name_space . ': Registered PID "' . $PID . '" for "'.$id.'"!');
					$gpage_query->bind_param('ii', $id, $PID);
					$gpage_query->execute();
				}
				// Dump the result into an array
				if ($register_query->affected_rows == 1) {
					$this->parent->parent->debug($this::name_space . ': Registered group ID "' . $id . '"!');
					$results[] = true;
				} else {
					$this->parent->parent->debug($this::name_space . ': Registered group ID "' . $id . '"!');
					$results[] = false;
				}
			}

			// Now we should have no false values if everything went well
			if (array_search(false, $results) !== true) {
				return new ActionResult($this, '/admin/modules/install/', 0,
					'Processing module installer...', B_T_FAIL, array(
					'status' => 1,
					'msg' => 'Registering cron jobs...'));
			} else {
				$this->parent->parent->debug($this::name_space . ': Failed to register groups!');
			}
		} // while($fetch_query->fetch())
	}

	public function _registerCron($xml = NULL){
		$this->parent->parent->debug($this::name_space . ': Registering cron jobs...');

		// Allows us to pump alternative xml into the function
		if ($xml === null) {
			$jobs = $this->module->getElementsByTagName('cron');
		} else {
			$jobs = $xml->getElementsByTagName('cron');
		}

		// Get the module ID so we can workout the group numbers
		$fetch_query = $this->mySQL_r->prepare("SELECT `module_id` FROM `core_modules` WHERE `namespace`=?");
		if ($fetch_query === false) {
			$this->parent->parent->debug($this::name_space . ': Fetch query failed!');
			return new ActionResult(
				$this,
				'/admin/modules/install/',
				0,
				'Failed to register cron jobs!',
				B_T_FAIL,
				array(
					'status' => 0,
					'msg' => 'Failed to register cron jobs!'
				)
			);
		}

		// Create the register query
		$register_query = $this->mySQL_w->prepare("INSERT INTO `core_cron`
			(`mins`,	`hours`,	`days`,	`month`,	`dow`, 	`user_id`,	`mod_id`,	`action`,	`description`) VALUES
			(?,			?,			?,		?,			?,		-1,			?,			?,			?)
		");

		if ($register_query === false) {
			$this->parent->parent->debug($this::name_space . ': Cron job register query failed!');
			return new ActionResult(
				$this,
				'/admin/modules/install/',
				0,
				'Failed to register cron jobs!',
				B_T_FAIL,
				array(
					'status' => 0,
					'msg' => 'Failed to register cron jobs!'
				)
			);
		}
		
		// Get the module ID
		$fetch_query->bind_param('s', $this->namespace);
		$fetch_query->bind_result($MOD_ID);
		$fetch_query->execute();
		$fetch_query->store_result();
		if ($fetch_query->num_rows != 1) {
			$this->parent->parent->debug($this::name_space . ': Module isnt\'t registered!');
			return new ActionResult(
				$this,
				'/admin/modules/install/',
				0,
				'Failed to register cron jobs!',
				B_T_FAIL,
				array(
					'status' => 0,
					'msg' => 'Failed to register cron jobs!'
				)
			);
		}
		while ($fetch_query->fetch()) {

			$results = array();

			// Check we have some groups to register
			if ($jobs->length > 0) {
				$results[] = true;
			}
			// Loop throught the groups and register them
			foreach($jobs as $job) {

				// Get the group details
				foreach(array(
					'mins',
					'hours',
					'days',
					'month',
					'dow') as $var
				) {
					// Fetch the tag value
					$$var = XMLCut::fetchTagValue($job, $var);
					// Set it to NULL if it is *
					if ($$var == '*') {
						$$var = NULL;
					}
					
					$this->parent->parent->debug($this::name_space . ': Module ' . ucfirst($var) . ': "' . $$var . '"');
				}
				$action	= XMLCut::fetchTagValue($job, 'action');
				$desc	= XMLCut::fetchTagValue($job, 'description');
				$desc = ($desc===NULL)? '':$desc;
				
				// Bind the params
				$register_query->bind_param('iiiiiiss', $mins, $hours, $days, $month, $dow, $MOD_ID, $action, $desc);

				// Execute the query
				$register_query->execute();
				$register_query->store_result();

				// Dump the result into an array
				if ($register_query->affected_rows == 1) {
					$this->parent->parent->debug($this::name_space . ': Registered job!');
					$results[] = true;
				} else {
					$this->parent->parent->debug($this::name_space . ': Failed to register job!');
					$this->parent->parent->debug($this->mySQL_w->error);
					$results[] = false;
				}
			}

			// Now we should have no false values if everything went well
			if (array_search(false, $results) !== true) {
				return new ActionResult($this, '/admin/modules/install/', 0,
					'Processing module installer...', B_T_FAIL, array(
					'status' => 1,
					'msg' => 'Processing module installer...'));
			} else {
				$this->parent->parent->debug($this::name_space . ': Failed to register cron jobs!');
			}
		} // while($fetch_query->fetch())
	}
	
	/**
	 * Installer::_installModule()
	 * 
	 * @param mixed $install
	 * @return
	 */
	public function _installModule($install = null)
	{
		$this->parent->parent->debug($this::name_space . ': Finding install.php...');

		if ($install === null) {
			$install = $this->tempModule . '/install.php';
		}

		if (!file_exists($install)) {
			$this->parent->parent->debug($this::name_space .': No install.php provided, finishing installation...');
			return new ActionResult($this, '/admin/modules/install/', 0, 'Cleaning up...',
				B_T_SUCCESS, array(
				'step' => 9,
				'status' => 1,
				'msg' => 'Cleaning up...'));
		}
		$this->parent->parent->debug($this::name_space . ': Found install.php!');

		require_once $install;
		$this->parent->parent->debug($this::name_space . ': Calling install()...');

		// Checks the function exists
		if (!is_callable('install')) {
			revertInstall($this);
			return new ActionResult(
				$this,
				'/admin/modules/install/',
				0,
				'Failed to install module!',
				B_T_FAIL,
				array(
					'status' => 0,
					'msg' => 'Failed to install module!'
				)
			);
		}

		// Calls the function (either returns true for success, or false for fail
		if (install($this)) {
			$this->parent->parent->debug($this::name_space .
				': install() successfully executed!');
			return new ActionResult(
				$this,
				'/admin/modules/install/',
				0,
				'Cleaning up...',
				B_T_SUCCESS,
				array(
					'status' => 1,
					'msg' => 'Cleaning up...'
				)
			);
		} else {
			$this->parent->parent->debug($this::name_space . ': install() did not execute successfully! Reverting...');
			revertInstall($this);
			return new ActionResult(
				$this,
				'/admin/modules/install/',
				0,
				'Failed to install module!',
				B_T_FAIL,
				array(
					'status' => 0,
					'msg' => 'Failed to install module!'
				)
			);
		}
	}

	/**
	 * Installer::_cleanInstall()
	 * 
	 * @return
	 */
	private function _cleanInstall()
	{
		$this->parent->parent->logEvent($this::name_space, 'Successfully installed module '.$this->namespace);
		// Removes the temp dir
		if (WebApp::rmDir($this->tempModule)) {
			return new ActionResult(
				$this,
				'/admin/modules/install/',
				1,
				'Installation complete!',
				B_T_SUCCESS,
				array(
					'status' => 1,
					'msg' => 'Installation complete!'
				)
			);
		}
		return new ActionResult(
			$this,
			'/admin/modules/install/',
			1,
			'Failed to clean up installation, but module was still installed!',
			B_T_WARNING,
			array(
				'status' => 1,
				'msg' => 'Failed to clean up installation, but module was still installed!'
			)
		);
	}

	/**
	 * Installer::_revertInstall()
	 * 
	 * @return
	 */
	private function _revertInstall()
	{
		$this->parent->parent->debug($this::name_space . ': Reverting install...');

		// Include the uninstaller (lazy again)
		require_once dirname(__file__) . '/uninstall.php';
		$uninstaller = new Uninstaller($this->parent);

		if ($this->step === null) {
			return false;
		}

		// Fetch the module ID (if applicable) as the uninstaller needs it
		$fetch_query = $this->mySQL_r->prepare("SELECT `module_id` FROM `core_modules` WHERE `namespace`=?");
		if ($fetch_query !== false) {
			$fetch_query->bind_param('s', $this->namespace);
			$fetch_query->bind_result($MOD_ID);
			$fetch_query->execute();
			$fetch_query->store_result();
			if ($fetch_query->num_rows == 1) {
				while ($fetch_query->fetch()) {
					$mod_id = $MOD_ID;
				}
			} else {
				$mod_id = null;
			}
			$fetch_query->free_result();
		}

		// Work out the details
		$dir = __MODULE__ . '/' . strtolower($this->namespace);
		$details = array(
			'dir' => $dir,
			'id' => $mod_id,
			'ns' => $this->namespace
		);
		// Call the uninstall steps
		switch($this->step) {
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
				$this->parent->parent->debug($this::name_space . ': Removing "' . str_replace(__EXECDIR__,
					'', $this->tempModule) . '"');
				WebApp::rmDir($this->tempModule);
				break;
		}
	}
}

?>