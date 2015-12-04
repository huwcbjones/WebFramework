<?php

/**
 * Base Web Application Framework
 *
 * @category   WebApp
 * @package    class.basewebapp.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 * 
 */

class BaseWebApp
{
	const name_space = 'WebApp';
	const version = '1.0.0';

	// Child objects - anything here is required for the Web App
	public $logger;
	public $user;
	public $config;

	// MySQL Database Handles
	public $mySQL_r; // Read Handle
	public $mySQL_w; // Write Handle

	protected $headers = array();

	// Properties
	public $https = false;

	// Content
	public $content = '';
	public $ctrl;
	
	public $http_status = 200;

	/**
	 * WebApp::__construct()
	 * 
	 * @return
	 */
	function __construct()
	{
		$this->debug('***** WebApp *****');
		$this->debug($this::name_space . ': Version ' . $this::version);
		
		$this->debug($this::name_space.': Session status '.strval(Session::start()));
		
		$this->debug($this::name_space . ': Loading config...');
		$this->config = new ConfigManager($this);
		$this->config->loadConfig();
		$this->debug($this::name_space . ': Config loaded!');
		if ($this->config->config['core']['errors']) {
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
		}
		$this->debug = $this->config->config['core']['debug'];

		
	}

	public function run(){
		global $debug;
		
		// Connect to the database
		$this->debug($this::name_space . ': Connecting to database...');
		if ($this->_dbConnect() === false) {
			$this->debug($this::name_space . ': Connection failed!');
			if(is_CLI()){
				return false;
			}
		} else {
			$this->debug($this::name_space . ': Connection successful!');
			$this->config->__init($this);
			$this->config->loadOptions();
			
		}
		
		$this->debug($this::name_space . ': Creating user controller...');
		$this->user = new UserController($this);
		
		$this->debug($this::name_space . ': Creating event logger...');
		$this->logger = new Logger($this);
		
		if(is_CLI()){
			$this->debug($this::name_space . ': Setting up CLI...');
			$this->CLI();
		}else{
			$this->Web();
		}
	}
	
	public function CLI(){
		global $debug;
		$this->_loadCron();
		if($this->config->config['core']['debug']){
			foreach($debug->debugLog as $msg){
				echo ' # '.$msg."\n";
			}
		}
		return $this;
	}
	
	public function Web(){
		$this->debug($this::name_space . ': Checking for IP block...');
		$ipBan = new IpBan($this);
		if(!$ipBan->check()){
			$this->_ipBlock();
		}
		
		$this->debug($this::name_space . ': Sanitising input...');
		$this->_sanitise();
		
		$this->debug($this::name_space . ': Generating catagories...');
		$this->_genPageCats();

		$this->debug($this::name_space . ': Setting HTTPS status');
		$this->_setHTTPS();

		$file_size_max = $this->config->getOption('file_size_max');
		ini_set('upload_max_filesize', $file_size_max);
		ini_set('post_max_size', $file_size_max);
		
		$this->debug($this::name_space . ': Selecting Mode...');
		$class = 'Page';
		switch($this::get('cat1')) {
			case 'fonts':
			case 'js':
			case 'css':
				$class = 'File';
				break;
			case 'images':
				$class = 'Image';
				break;
			case 'action':
				$class = 'Action';
				break;
			case 'ajax':
				$class = 'Ajax';
				break;
			case 'feed':
				$class = 'Feed';
				break;
		}
		
		
		$method = 'set'.$class;
		$this->debug($this::name_space . ': MODE: '.strtoupper($class));
		$this->ctrl = new $class($this);
		
		
		$this->ctrl->$method();
		$this->ctrl->execute();
		$this->output();
	}
	
	/**
	 * WebApp::output()
	 * 
	 * @return
	 */
	function output()
	{
		$this->debug($this::name_space . ': Setting headers');
		http_response_code($this->http_status);
		if (count($this->headers) != 0) {
			foreach($this->headers as $h => $v) {
				header($h . ': ' . $v);
			}
		}
		$this->debug($this::name_space . ': Printing page');
		$content = $this->content;

		$this->debug($this::name_space . ': Page printed!');
		$page = $this->ctrl;
		if ($this->debug) {

			$noLog = true;
			switch($page::name_space) {
				case 'WebApp.Page':
					$content .= '<!-- START DEBUG LOG -->' . PHP_EOL;
					$content .= '<!--' . PHP_EOL;
					$noLog = false;
					break;
				case 'WebApp.Action':
				case 'WebApp.Ajax':
					$content = json_decode($content, true);
					$noLog = false;
					break;
				default:
			}
			global $debug;
			if (!$noLog) {
				$debug->compile();
			}
			switch($page::name_space) {
				case 'WebApp.Page':
					$content .= implode('', $debug->debugLog);
					$content .= '-->' . PHP_EOL;
					$content .= '<!-- END DEBUG LOG -->' . PHP_EOL;

					break;
				case 'WebApp.Action':
				case 'WebApp.Ajax':
					$content['debug'] = $debug->debugLog;
					$content = json_encode($content);
					break;
				default:
			}
		}
		print $content;
	}
	
	/**
	 * WebApp::__destruct()
	 * 
	 * @return
	 */
	function __destruct()
	{
		if (is_object($this->config)) {
			if (!$this->config->config['core']['database']) {
				if (is_object($this->mySQL_r)) {
					$this->mySQL_r->close();
				}
				if (is_object($this->mySQL_w)) {
					$this->mySQL_w->close();
				}
			}
		}
	}


	/**
	 * WebApp::_dbConnect()
	 * 
	 * @return
	 */
	private function _dbConnect()
	{
		$mySQLr = mysqli_init();
		$mySQLw = mysqli_init();

		$mySQLr->options(MYSQLI_OPT_CONNECT_TIMEOUT, 0.2);
		$mySQLw->options(MYSQLI_OPT_CONNECT_TIMEOUT, 0.2);

		// Get MySQL DB Name
		$db = $this->config->config['mysql']['db'];
		// Get MySQL Read Config
		$conf['r'] = $this->config->config['mysql']['r'];

		// Get MySQL Write Config
		$conf['w'] = $this->config->config['mysql']['w'];

		@$mySQLw->real_connect($conf['w']['serv'], $conf['w']['user'], $conf['w']['pass'],
			$db, intval($conf['w']['port']));
		if ($mySQLw->connect_errno === 0) {
			@$mySQLr->real_connect($conf['r']['serv'], $conf['r']['user'], $conf['r']['pass'],
				$db, intval($conf['r']['port']));
			if ($mySQLr->connect_errno == 0) {
				$this->mySQL_r = $mySQLr;
				$this->mySQL_w = $mySQLw;
				$this->config->config['core']['database'] = true;
				return true;
			} else {
				@$mySQLr->real_connect($conf['w']['serv'], $conf['w']['user'], $conf['w']['pass'],
					$db, intval($conf['w']['port']));
				if($mySQLr->connect_errno == 0){
					$this->debug($this::name_space . ': Connection to read server failed... using write server for reading!');
					$this->mySQL_r = $mySQLr;
					$this->mySQL_w = $mySQLw;
					$this->config->config['core']['database'] = true;
					return true;
				}else{
					$this->debug($this::name_space . ': Connection failed!');
					$this->debug($this::name_space . ': MySQLr Error MSG: ' . $mySQLr->
						connect_error);
					$this->debug($this::name_space . ': MySQLw Error MSG: ' . $mySQLw->
						connect_error);
					$this->config->config['core']['database'] = false;
					return false;
				}
			}
		} else {
			$this->debug($this::name_space . ': Connection failed!');
			$this->debug($this::name_space . ': MySQLw Error MSG: ' . $mySQLr->
				connect_error);
			$this->config->config['core']['database'] = false;
			return false;
		}
	}

	/**
	 * WebApp::_ipBlock()
	 * 
	 * @return
	 */
	private function _ipBlock(){
		http_response_code(403);
		print ($this->config->getOption('ip_blocked'));
		exit();
	}
	
	public function _loadCron(){
		$this->debug($this::name_space . ': Loading Cron Manager...');
		require_once __CTRLDIR__ . '/cron.php';
		$this->cron = new Cron($this);
	}
	
	/**
	 * WebApp::_setHTTPS()
	 * 
	 * @return
	 */
	private function _setHTTPS()
	{
		if (
			$this->https !== true
			&& (
				$this->config->config['core']['https']['f']
				|| (
					$this->config->config['core']['https']['a']
					&& $this->user->is_loggedIn()
				)
			)
		) {
			$location = 'https://' . Server::get('HTTP_Host') . Server::get('Request_URI');
			$this->debug($this::name_space . ': HTTPS turned on... follow link: ' . $location);
			if (!$this->debug) {
				header('Location: ' . $location);
				exit();
			}
		} else {
			$this->debug($this::name_space . ': HTTPS left as it is.');
		}
	}

	/**
	 * WebApp::_sanitise()
	 */
	private function _sanitise(){
		$gump = new GUMP();
		$_POST = $gump->sanitize($_POST);
		$_GET = $gump->sanitize($_GET);
	}
	
	/**
	 * WebApp::_genPageCats()
	 * 
	 * @return
	 */
	private function _genPageCats()
	{

		// Calculate Requested URL
		$url = 'http';
		if (
			$this->config->config['core']['https']['a']
			&& (
				Server::get('HTTPS') !== null
				&& Server::get('HTTPS') === 'on'
				|| Server::get('Server_Port') == 443
			)
		) {
			$this->https = true;
			$this->debug($this::name_space . ': HTTPS is ON.');
			$url .= 's';
		}
		$url .= '://';

		// Break it up into component parts
		$url = parse_url($url . Server::get('HTTP_Host') . Server::get('Request_URI'));

		// Break apart the path to get cats
		$cats = explode('/', $url['path']);

		// Remove index.php from cats
		if (array_search('index.php', $cats) !== false) {
			unset($cats[array_search('index.php', $cats)]);
		}

		// Trim whitespace
		while ('' === reset($cats)) {
			array_shift($cats);
		}
		while ('' === end($cats)) {
			array_pop($cats);
		}
		
		$gump = new GUMP();
		$cats = $gump->sanitize($cats);
		
		// Dump catagories into $_GET for easy access later
		$cats = array_values($cats);
		$this->debug($this::name_space . ': ' . count($cats) . ' cats');
		for ($c = 0; $c < count($cats); $c++) {
			$this::get('cat' . ($c + 1), $cats[$c]);
			$this->{'cat' . ($c + 1)} = $cats[$c];
			$this->debug($this::name_space . ': cat' . ($c + 1) . ' = ' . $cats[$c]);
		}

		if ($this::get('cat1') == '') {
			$this::get('cat1', 'core');
			$this->cat1 = 'core';
		}
	}

	public static function forceRedirect($uri){
		if (
			  strpos(Server::get('request_uri'), "action")	=== false
			&&strpos(Server::get('request_uri'), "ajax")	=== false
			&&strpos(Server::get('request_uri'), "css")		=== false
			&&strpos(Server::get('request_uri'), "font")	=== false
			&&strpos(Server::get('request_uri'), "js")		=== false
			&&strpos(Server::get('request_uri'), "image")	=== false
			
			&&strpos(Server::get('request_uri'), parse_url($uri, PHP_URL_PATH))		=== false
		) {
			header('Location: '.$uri);
			exit();
		}
	}
	/**
	 * WebApp::logEvent()
	 * 
	 * @param mixed $ns
	 * @param mixed $event
	 * @return
	 */
	public function logEvent($ns, $event)
	{
		return $this->logger->logEvent($ns, $event);
	}

	/**
	 * WebApp::clearQuery()
	 * 
	 * @param mixed $dbLink
	 * @return
	 */
	public static function clearQuery($dbLink)
	{
		while ($dbLink->more_results() && $dbLink->next_result()) {
			$extraResult = $dbLink->use_result();
			if ($extraResult instanceof mysqli_result) {
				$extraResult->free();
			}
		}
	}

	/**
	 * WebApp::debug()
	 * 
	 * @param mixed $text
	 * @return
	 */
	public function debug($text)
	{
		global $debug;
		$debug->debug($text, true);
	}

	/**
	 * WebApp::addHeader()
	 * 
	 * @param mixed $header
	 * @param mixed $value
	 * @return
	 */
	public function addHeader($header, $value)
	{
		$this->headers[ucfirst($header)] = $value;
	}

	/**
	 * @param mixed $key
	 * @param mixed $set
	 * @return
	 */
	public static function get($key, $set = null)
	{
		if ($set !== null) {
			$_GET[$key] = $set;
		}
		if (array_key_exists($key, $_GET)) {
			return trim($_GET[$key]);
		} else {
			return null;
		}
	}
	/**
	 * WebApp::post()
	 * 
	 * @param mixed $key
	 * @param mixed $set
	 * @return
	 */
	public static function post($key, $set = null)
	{
		if ($set !== null) {
			$_POST[$key] = $set;
		}
		if (array_key_exists($key, $_POST)) {
			if(is_array($_POST[$key])){
				return array_trim($_POST[$key]);
			}else{
				return trim($_POST[$key]);
			}
		} else {
			return null;
		}
	}

	/**
	 * WebApp::files()
	 * 
	 * @param mixed $key
	 * @return
	 */
	public function files($key)
	{
		$max_file_size = $this->config->getOption('file_size_max');
		$mime_whitelist = strgetcsv($this->config->getOption('file_mime_white'));
		$mime_blacklist = strgetcsv($this->config->getOption('file_mime_black'));
		$file_ext_white = strgetcsv($this->config->getOption('file_ext_white'));
		if (!isset($_FILES[$key]['error']) || is_array($_FILES[$key]['error'])) {
			$this->debug($this::name_space . ': File from upload failed!');
			return _ACTION_FAIL_1;
		}
		switch($_FILES[$key]['error']) {
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_NO_FILE:
				return _ACTION_FAIL_2;
				break;
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				return _ACTION_FAIL_3;
			default:
				return _ACTION_UNSPEC;
		}
		if ($_FILES[$key]['size'] > $max_file_size) {
			return _ACTION_FAIL_3;
		}
		$fileinfo = new finfo(FILEINFO_MIME_TYPE);
		$finfo = $fileinfo->file($_FILES[$key]['tmp_name']);
		$pathinfo = pathinfo($_FILES[$key]['name']);
		if (
				(
					array_search($finfo, $mime_whitelist, true) === false
					&&array_search($pathinfo['extension'], $file_ext_white, true) === false
					&&array_search($_FILES[$key]['type'], $mime_whitelist, true) === false
				)
			||(
				array_search($finfo, $mime_blacklist, true) !== false
				&&array_search($_FILES[$key]['type'], $mime_blacklist, true) !== false
			)
		){
			return _ACTION_FAIL_4;
		}
		$tempFile = __EXECDIR__ . '/temp/' . ranString(32);
		while (file_exists($tempFile)) {
			$tempFile = __EXECDIR__ . '/temp/' . ranString(32);
		}
		if (!move_uploaded_file($_FILES[$key]['tmp_name'], $tempFile)) {
			return _ACTION_FAIL_5;
		}
		return $tempFile;
	}

	/**
	 * WebApp::action()
	 * 
	 * @param mixed $controller
	 * @param mixed $action
	 * @param bool $ajax
	 * @return
	 */
	public static function action($controller, $action, $ajax = true)
	{
		return array(
			'controller' => $controller,
			'action' => $action,
			'ajax' => $ajax);
	}

	/**
	 * WebApp::mkTmpDir()
	 * 
	 * @return
	 */
	public static function mkTmpDir()
	{
		$dirname = ranString(8);
		if (mkdir(__EXECDIR__ . '/temp/' . $dirname)) {
			if (is_dir(__EXECDIR__ . '/temp/' . $dirname)) {
				return __EXECDIR__ . '/temp/' . $dirname;
			}
		}
		return false;
	}

	/**
	 * WebApp::rmDir()
	 * 
	 * @param mixed $dirname
	 * @return
	 */
	public static function rmDir($dirname)
	{
		if (file_exists($dirname)) {
			if (is_dir($dirname)) {
				return rrmdir($dirname);
			}
		}
		return false;
	}

	/**
	 * WebApp::extractZip()
	 * 
	 * @param mixed $location
	 * @return
	 */
	public static function extractZip($location)
	{
		if (file_exists($location)) {
			$zip = new ZipArchive;
			if ($zip->open($location) === true) {
				$temp = self::mkTmpDir();
				if ($temp !== false) {
					$zip->extractTo($temp);
					$zip->close();
					unlink($location);
					return $temp;
				} else {
					unlink($temp);
				}
			}
		}
		return false;
	}
	
	public function login($user, $pass){
		return $this->user->cliLogon($user, $pass);
	}
	public function logout(){
		return $this->user->cliLogout();
	}
	public function changeUser($user, $pass = '', $id = NULL){
		return $this->user->cliChangeUser($user, $pass, $id);
	}
	
	public function is_loggedIn(){
		return $this->user->is_loggedIn();
	}
}

?>
