<?php
$config = $this->parent->parent->config->config;

$closeBtn = array('a'=>array('t'=>'url', 'a'=>'../'), 'ic'=>'remove-sign');
$editBtn = array('s'=>B_T_PRIMARY, 'a'=>array('t'=>'url', 'a'=>'config_edit'), 'ic'=>'edit');
$form = $page->getPlugin('form', array('config_view', WebApp::action('core', 'config', true), 'post'));

$form
	->setColumns(3, 9)
	->setIndent('    ')
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
				'c'=>$config['core']['errors'],
				'd'=>true
			),
			array(
				'i'=>'errorsN',
				's'=>B_T_SUCCESS,
				'v'=>0,
				'l'=>'No *',
				'c'=>not($config['core']['errors']),
				'd'=>true
			)
		),
		array('t'=>'Display PHP errors on pages?'),
		array('d'=>true)
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
				'c'=>$config['core']['maintenance'],
				'd'=>true
			),
			array(
				'i'=>'maintenanceN',
				's'=>B_T_SUCCESS,
				'v'=>0,
				'l'=>'No *',
				'c'=>not($config['core']['maintenance']),
				'd'=>true
			)
		),
		array('t'=>'If you enable maintenance mode, only Administrators and above will be able to log in. No pages will be publicly acessible (useful for updating modules)'),
		array('d'=>true)
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
				'c'=>$config['core']['debug'],
				'd'=>true
			),
			array(
				'i'=>'debugN',
				's'=>B_T_SUCCESS,
				'v'=>0,
				'l'=>'No *',
				'c'=>not($config['core']['debug']),
				'd'=>true
			)
		),
		array('t'=>'Enabling debug mode prints a log of events at the bottom of the page/as part of the JSON array to aid the debugging of features.'),
		array('d'=>true)
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
				'c'=>$config['core']['https']['a'],
				'd'=>true
			),
			array(
				'i'=>'httpsaN',
				's'=>B_T_WARNING,
				'v'=>0,
				'l'=>'No',
				'c'=>not($config['core']['https']['a']),
				'd'=>true
			)
		),
		array('t'=>'Lets the site know whether it can use HTTPS for sensitive information exchange.'),
		array('d'=>true)
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
				'c'=>$config['core']['https']['f'],
				'd'=>true
			),
			array(
				'i'=>'httpsyN',
				's'=>B_T_WARNING,
				'v'=>0,
				'l'=>'No',
				'c'=>not($config['core']['https']['f']),
				'd'=>true
			)
		),
		array('t'=>'If HTTPS is available, then it is recommended to force HTTPS on for the entire site, however, you can disable this if required.'),
		array('d'=>true)
	)
	->addTextField(
		'CDN Server',
		'core_cdn',
		$config['core']['cdn'],
		array('t'=>'Use / to disable the CDN server, or enter the address for server (including the http(s)://)'),
		array('ro'=>true)
	)
	->addHTML('<h3>Database Settings</h3>',false)
	->addTextField(
		'Database',
		'mysql_db',
		$config['mysql']['db'],
		array('t'=>'The database the site uses, advisable not to change this!'),
		array('ro'=>true)
	)
	->addHTML('<h4>Read Server</h4>',false)
	->addTextField(
		'User',
		'mysql_r_user',
		$config['mysql']['r']['user'],
		array('t'=>'The user with read access to the database'),
		array('ro'=>true)
	)
	->addTextField(
		'Host',
		'mysql_r_host',
		$config['mysql']['r']['serv'],
		array('t'=>'The server hosting the database (can be IP or name, as long as the name resolves)'),
		array('ro'=>true)
	)
	->addTextField(
		'Port',
		'mysql_r_port',
		$config['mysql']['r']['port'],
		array('t'=>'The port number the host listens on'),
		array('ro'=>true)
	)
	->addHTML('<h4>Write Server</h4>',false)
	->addTextField(
		'User',
		'mysql_w_user',
		$config['mysql']['w']['user'],
		array('t'=>'The user with full access to the database'),
		array('ro'=>true)
	)
	->addTextField(
		'Host',
		'mysql_w_host',
		$config['mysql']['w']['serv'],
		array('t'=>'The server hosting the database (can be IP or name, as long as the name resolves)'),
		array('ro'=>true)
	)
	->addTextField(
		'Port',
		'mysql_w_port',
		$config['mysql']['w']['port'],
		array('t'=>'The port number the host listens on'),
		array('ro'=>true)
	)
	->addHTML('<h3>reCAPTCHA Settings</h3>', false)
	->addTextField(
		'Public Key',
		'reCAPTCHA_pub',
		$config['reCAPTCHA']['pub'],
		array('t'=>'The public key used for the reCAPTCHA service.'),
		array('ro'=>true)
	)
	->addTextField(
		'Private Key',
		'reCAPTCHA_priv',
		$config['reCAPTCHA']['priv'],
		array('t'=>'The private key used for the reCAPTCHA service.'),
		array('ro'=>true)
	);
	if($this->accessAdminPage(21)){
		$form->addBtnLine(array('close'=>$closeBtn, 'edit'=>$editBtn));
	}else{
		$form->addBtnLine(array('close'=>$closeBtn));
	}
	$form
		->addHTML('<small>* - Recommended setting</small>', false)
		->build();
?>
<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">System Configuration</h1>
    <?php print $form->getForm(); ?>
  </div>
</div>