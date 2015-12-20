<?php

/**
 * Config Manager
 *
 * Manages the Site Config
 *
 * @category   WebApp.Config
 * @package    class.configmanager.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 * 
 */

class ConfigManager extends Base
{
	const name_space = 'WebApp.Config';
	const version = '1.0.0';

	public $config = array();
	private $options = array();

	public function loadConfig()
	{
		$config = $this->_loadDefaultConfig();

		// Load Custom Config
		$this->parent->debug($this::name_space . ': Checking for config file');
		if (file_exists(__LIBDIR__ . '/config.inc.php')) {
			$this->parent->debug($this::name_space . ': Loading config file');
			include_once __LIBDIR__ . '/config.inc.php';
		} else {
			$this->parent->debug($this::name_space . ': Config file  not found!');
		}

		$this->config = $config;

		// Don't use CDN for integrity if on HTTPS
		if (Server::get('HTTPS')!==NULL && Server::get('HTTPS') !== 'off' || Server::get('SERVER_PORT') ==
			443) {
			$this->config['core']['cdn'] = '/';
		}
	}

	public function loadOptions()
	{
		$this->parent->debug($this::name_space . ': Loading options from database');
		$option_query = $this->parent->mySQL_r->prepare("SELECT `name`, `value` FROM `core_options`");
		$option_query->execute();
		$option_query->store_result();
		$this->parent->debug($this::name_space . ': Loaded ' . $option_query->num_rows .
			' option(s)');
		if ($option_query->num_rows > 0) {
			$option_query->bind_result($k, $v);
			while ($option_query->fetch()) {
				$this->options[$k] = $v;
			}
		}
		$option_query->free_result();
	}


	// Returns an option, or null if it doesn't exist
	public function getOption($name)
	{
		$this->parent->debug($this::name_space . ': Getting option "' . $name . '"');
		if (array_key_exists($name, $this->options)) {
			return $this->options[$name];
		} else {
			return null;
		}
	}

	// Returns the default config array
	private function _loadDefaultConfig()
	{
		$config['core']['errors'] = false;
		$config['core']['database'] = false;
		$config['core']['maintenance'] = false;
		$config['core']['debug'] = false;
		$config['core']['https']['a'] = true; // HTTPS available?
		$config['core']['https']['f'] = false; // Force HTTPS?
		$config['core']['cdn'] = '/';
		$config['mysql']['db'] = 'bwsc';
		$config['mysql']['r']['user'] = 'root';
		$config['mysql']['r']['pass'] = '';
		$config['mysql']['r']['serv'] = 'localhost';
		$config['mysql']['r']['port'] = '3306';
		$config['mysql']['w']['user'] = 'bwsc';
		$config['mysql']['w']['pass'] = '';
		$config['mysql']['w']['serv'] = 'localhost';
		$config['mysql']['w']['port'] = '3306';
		$config['reCAPTCHA']['pub'] = '';
		$config['reCAPTCHA']['priv'] = '';
		$config['days'][0] = 'Sunday';
		$config['days'][1] = 'Monday';
		$config['days'][2] = 'Tuesday';
		$config['days'][3] = 'Wednesday';
		$config['days'][4] = 'Thursday';
		$config['days'][5] = 'Friday';
		$config['days'][6] = 'Saturday';
		$config['days'][7] = 'Sunday';
		return $config;
	}
}

?>