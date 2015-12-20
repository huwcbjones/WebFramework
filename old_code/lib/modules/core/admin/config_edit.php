<?php
$config = $this->parent->parent->config->config;

$closeBtn = array('a'=>array('t'=>'url', 'a'=>'config_view'), 'ic'=>'remove-sign');
$saveBtn = array('s'=>B_T_SUCCESS, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'config_edit\', this, \'save\', \'checkSave\')'), 'ic'=>'floppy-disk');
$applyBtn = array('s'=>B_T_PRIMARY, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'config_edit\', this, \'apply\', \'checkSave\')'), 'ic'=>'ok-sign');
$form = $page->getPlugin('form', array('config_edit', WebApp::action('core', 'config_edit', true), 'post', 'application/x-www-form-urlencoded', 'checkSave'));

$checkSave = 'function checkSave(){'.PHP_EOL;
$checkSave.= '  var conf = confirm("Are you sure you wish to save the configuration?\\nThis may break the website!");'.PHP_EOL;
$checkSave.= '  if(conf){'.PHP_EOL;
$checkSave.= '    return true;'.PHP_EOL;
$checkSave.= '  }else{'.PHP_EOL;
$checkSave.= '    return false;'.PHP_EOL;
$checkSave.= '  }'.PHP_EOL;
$checkSave.= '}'.PHP_EOL;

$form
	->setColumns(3, 5)
	->setIndent('    ')
	->addScript($checkSave)
	->addHTML('<h3>Core Settings</h3>',false)
	->addButtonGroup(
		'Display Errors',
		'core_errors',
		array(
			array(
				'i'=>'errorsY',
				's'=>B_T_FAIL,
				'v'=>1,
				'l'=>'Yes&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-warning-sign"></span>',
				'c'=>$config['core']['errors']
			),
			array(
				'i'=>'errorsN',
				's'=>B_T_SUCCESS,
				'v'=>0,
				'l'=>'No *',
				'c'=>not($config['core']['errors'])
			)
		),
		array('t'=>'Display PHP errors on pages?')
	)
	->addButtonGroup(
		'Maintenance',
		'core_maintenance',
		array(
			array(
				'i'=>'maintenanceY',
				's'=>B_T_FAIL,
				'v'=>1,
				'l'=>'Yes&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-warning-sign"></span>',
				'c'=>$config['core']['maintenance']
			),
			array(
				'i'=>'maintenanceN',
				's'=>B_T_SUCCESS,
				'v'=>0,
				'l'=>'No *',
				'c'=>not($config['core']['maintenance'])
			)
		),
		array('t'=>'If you enable maintenance mode, only Administrators and above will be able to log in. No pages will be publicly acessible (useful for updating modules)')
	)
	->addButtonGroup(
		'Debug Log',
		'core_debug',
		array(
			array(
				'i'=>'debugY',
				's'=>B_T_WARNING,
				'v'=>1,
				'l'=>'Yes',
				'c'=>$config['core']['debug']
			),
			array(
				'i'=>'debugN',
				's'=>B_T_SUCCESS,
				'v'=>0,
				'l'=>'No *',
				'c'=>not($config['core']['debug'])
			)
		),
		array('t'=>'Enabling debug mode prints a log of events at the bottom of the page/as part of the JSON array to aid the debugging of features.')
	)
	->addButtonGroup(
		'HTTPS Available',
		'core_https_a',
		array(
			array(
				'i'=>'httpsaY',
				's'=>B_T_SUCCESS,
				'v'=>1,
				'l'=>'Yes *',
				'c'=>$config['core']['https']['a']
			),
			array(
				'i'=>'httpsaN',
				's'=>B_T_WARNING,
				'v'=>0,
				'l'=>'No',
				'c'=>not($config['core']['https']['a'])
			)
		),
		array('t'=>'Lets the site know whether it can use HTTPS for sensitive information exchange.')
	)
	->addButtonGroup(
		'HTTPS Force',
		'core_https_f',
		array(
			array(
				'i'=>'httpsfY',
				's'=>B_T_SUCCESS,
				'v'=>1,
				'l'=>'Yes *',
				'c'=>$config['core']['https']['f']
			),
			array(
				'i'=>'httpsyN',
				's'=>B_T_WARNING,
				'v'=>0,
				'l'=>'No',
				'c'=>not($config['core']['https']['f'])
			)
		),
		array('t'=>'If HTTPS is available, then it is recommended to force HTTPS on for the entire site, however, you can disable this if required.')
	)
	->addTextField(
		'CDN Server',
		'core_cdn',
		$config['core']['cdn'],
		array('t'=>'Use / to disable the CDN server, or enter the address for server (including the http(s)://)', 'p'=>'Default: /'),
		array(
			'r'=>true,
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'A CDN Server is required.','s'=>'danger')
			),
			'vo'=>'validateOn:["blur"]'
		)
	)
	->addHTML('<h3>Database Settings</h3>',false)
	->addTextField(
		'Database',
		'mysql_db',
		$config['mysql']['db'],
		array('t'=>'The database the site uses, advisable not to change this!', 'p'=>'Default: bwsc'),
		array(
			'r'=>true,
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'A database name is required.','s'=>'danger')
			),
			'vo'=>'validateOn:["blur"]'
		)
	)
	->addHTML('<h4>Read Server</h4>',false)
	->addTextField(
		'User',
		'mysql_r_user',
		$config['mysql']['r']['user'],
		array('t'=>'The user with read access to the database', 'p'=>'Default: bwsc'),
		array(
			'r'=>true,
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'A user is required.','s'=>'danger')
			),
			'vo'=>'validateOn:["blur"]'
		)
	)
	->addPasswordField(
		'Password',
		'mysql_r_pass',
		$config['mysql']['r']['pass'],
		array('t'=>'The password for user with read access to the database. Leave blank to not change','p'=>'Default: ')
	)
	->addTextField(
		'Host',
		'mysql_r_host',
		$config['mysql']['r']['serv'],
		array('t'=>'The server hosting the database (can be IP or name, as long as the name resolves)', 'p'=>'Default: localhost'),
		array(
			'r'=>true,
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'A host is required.','s'=>'danger')
			),
			'vo'=>'validateOn:["blur"]'
		)
	)
	->addTextField(
		'Port',
		'mysql_r_port',
		$config['mysql']['r']['port'],
		array('t'=>'The port number the host listens on', 'p'=>'Default: 3306'),
		array(
			'r'=>true,
			'v'=>true,
			'vt'=>'integer',
			'vm'=>array(
				'textfieldInvalidFormatMsg'=>array('m'=>'Port must be an integer', 's'=>'danger'),
				'textfieldRequiredMsg'=>array('m'=>'A port is required.','s'=>'danger')
			),
			'vo'=>'validateOn:["blur"]'
		)
	)
	->addHTML('<h4>Write Server</h4>',false)
	->addTextField(
		'User',
		'mysql_w_user',
		$config['mysql']['w']['user'],
		array('t'=>'The user with full access to the database', 'p'=>'Default: bwsc'),
		array(
			'r'=>true,
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'A user is required.','s'=>'danger')
			),
			'vo'=>'validateOn:["blur"]'
		)
	)
	->addPasswordField(
		'Password',
		'mysql_w_pass',
		$config['mysql']['w']['pass'],
		array('t'=>'The password for user with full access to the database. Leave blank not to change', 'p'=>'Default: ')
	)
	->addTextField(
		'Host',
		'mysql_w_host',
		$config['mysql']['w']['serv'],
		array('t'=>'The server hosting the database (can be IP or name, as long as the name resolves)', 'p'=>'Default: localhost'),
		array(
			'r'=>true,
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'A host is required.','s'=>'danger')
			),
			'vo'=>'validateOn:["blur"]'
		)
	)
	->addTextField(
		'Port',
		'mysql_w_port',
		$config['mysql']['w']['port'],
		array('t'=>'The port number the host listens on', 'p'=>'Default: 3306'),
		array(
			'r'=>true,
			'v'=>true,
			'vt'=>'integer',
			'vm'=>array(
				'textfieldInvalidFormatMsg'=>array('m'=>'Port must be an integer', 's'=>'danger'),
				'textfieldRequiredMsg'=>array('m'=>'A port is required.','s'=>'danger')
			),
			'vo'=>'validateOn:["blur"]'
		)
	)
	->addHTML('<h3>reCAPTCHA Settings</h3>', false)
	->addTextField(
		'Public Key',
		'reCAPTCHA_pub',
		$config['reCAPTCHA']['pub'],
		array('t'=>'The public key used for the reCAPTCHA service.')
	)
	->addTextField(
		'Private Key',
		'reCAPTCHA_priv',
		$config['reCAPTCHA']['priv'],
		array('t'=>'The private key used for the reCAPTCHA service.')
	)
	->addBtnLine(array('close'=>$closeBtn, 'save'=>$saveBtn, 'apply'=>$applyBtn))
	->build();
?>
<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">System Configuration</h1>
    <?php print $form->getForm(); ?>
  </div>
</div>