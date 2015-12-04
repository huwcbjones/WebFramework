<?php

/**
 * Config Action Class
 *
 * @category   Module.Core.Config.option
 * @package    core/resources/config.action.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

class ConfigAction extends BaseAction
{
	const	 name_space	 = 'Module.Core';
	const	 version	 = '1.0.0';
	
	public function save(){
		
		if(WebApp::post('mysql_r_pass')==='') WebApp::post('mysql_r_pass', $this->parent->parent->config->config['mysql']['r']['pass']);
		if(WebApp::post('mysql_w_pass')==='') WebApp::post('mysql_r_pass', $this->parent->parent->config->config['mysql']['w']['pass']);
		
		$gump = new GUMP();
		
		$gump->validation_rules(array(
			'core_errors'=>'required|boolean',
			'core_maintenance'=>'required|boolean',
			'core_debug'=>'required|boolean',
			'core_https_a'=>'required|boolean',
			'core_https_f'=>'required|boolean',
			'core_cdn'=>'required',
			'mysql_db'=>'required',
			'mysql_r_user'=>'required',
			'mysql_r_host'=>'required',
			'mysql_r_port'=>'required|integer',
			'mysql_w_user'=>'required',
			'mysql_w_host'=>'required',
			'mysql_w_port'=>'required|integer',
			'reCAPTCHA_pub'=>'required|alpha_dash',
			'reCAPTCHA_priv'=>'required|alpha_dash'
		));
		
		$gump->filter_rules(array(
			'core_cdn'=>'trim|urlencode'
		));
		
		$valid_data = $gump->run($_POST);
		
		if($valid_data === false){
			return new ActionResult(
				$this,
				'/admin/core/config_edit',
				0,
				'Failed to save config!<br />Error: <code>Please check you have completed all fields as instructed.</code>',
				B_T_FAIL
			); 
		}
		$configFile = fopen(__LIBDIR__.'/config.inc.php','w');
		if(fwrite($configFile, $this->getFile($valid_data))){
			fclose($configFile);
			return new ActionResult(
				$this,
				'/admin/core/config_view',
				1,
				'Succeesfully saved config!',
				B_T_SUCCESS
			);
		}else{
			fclose($configFile);
			return new ActionResult(
				$this,
				'/admin/core/config_edit',
				0,
				'Failed to save config!',
				B_T_SFAIL
			);
		}
	}
	
	function getFile($valid_data){
		$file = $this->_getHeader();
		$file.= <<< EOT
// Display Errors [Default: Disabled]
\$config['core']['errors']      = {$valid_data['core_errors']};

// Debug Mode [Default: Disabled]
\$config['core']['debug']       = {$valid_data['core_debug']};

// Maintenance  [Default: Disabled]
\$config['core']['maintenance'] = {$valid_data['core_maintenance']};

// Database [Default: bwsc]
\$config['mysql']['db']         = '{$valid_data['mysql_db']}';

/*
* Read Settings
*/
// Username[Default: root]
\$config['mysql']['r']['user']  = '{$valid_data['mysql_r_user']}';

// Password [Default: ]
\$config['mysql']['r']['pass']  = '{$valid_data['mysql_r_pass']}';

// Server [Default: localhost]
\$config['mysql']['r']['serv']  = '{$valid_data['mysql_r_host']}';

// Port [Default: 3306]
\$config['mysql']['r']['port']  = '{$valid_data['mysql_r_port']}';

/*
* Write Settings
*/
// Username[Default: root]
\$config['mysql']['w']['user']  = '{$valid_data['mysql_w_user']}';

// Password [Default: ]
\$config['mysql']['w']['pass']  = '{$valid_data['mysql_w_pass']}';

// Server [Default: localhost]
\$config['mysql']['w']['serv']  = '{$valid_data['mysql_w_host']}';

// mySQL_R_DBPort [Default: 3306]
\$config['mysql']['w']['port']  = '{$valid_data['mysql_w_port']}';

/*
* reCAPTCHA Keys
* Used for reCAPTCHA Authentication
*/
\$config['reCAPTCHA']['pub']    = '{$valid_data['reCAPTCHA_pub']}';
\$config['reCAPTCHA']['priv']   = '{$valid_data['reCAPTCHA_priv']}';
EOT;
		$file.= "?>\r\n";
		return $file;
	}
	
	private function _getHeader(){
		$time = date(DATET_LONG, time());
		return <<< EOT
<?php

/**
 * Web Application Config File
 *
 * The Web Application Configuration
 *
 * @category   WebApp.Config
 * @package    config.inc.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 * @generated  {$time}
 */


EOT;

	}
}
?>