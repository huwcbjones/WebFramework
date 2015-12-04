<?php
$config = $this->parent->parent->config->config;

$closeBtn = array('a'=>array('t'=>'url', 'a'=>'../'), 'ic'=>'remove-sign');
$form = $page->getPlugin('form', array('system_info', WebApp::action('core', 'system_info', true), 'post'));

$form
	->setColumns(3, 9)
	->setIndent('    ')
	->addHTML('<h3>Web Server Info</h3>',false)
	->addTextField(
		'IP',
		'server_addr',
		Server::get('Server_Addr'),
		array('t'=>'IP Address of the server'),
		array('ro'=>true)
	)
	->addTextField(
		'Name',
		'server_name',
		Server::get('Server_Name'),
		array('t'=>'Server host name'),
		array('ro'=>true)
	)
	->addTextField(
		'Software',
		'server_software',
		Server::get('Server_Software'),
		array('t'=>'Software powering the server'),
		array('ro'=>true)
	)
	->addTextField(
		'Signature',
		'server_signature',
		GUMP::xss_clean(array(Server::get('Server_Signature')))[0],
		array('t'=>''),
		array('ro'=>true)
	)
	->addTextField(
		'Port',
		'server_port',
		Server::get('Server_Port'),
		array('t'=>'Port the server is listening on'),
		array('ro'=>true)
	)
	->addTextField(
		'Gateway Interface',
		'server_gateway',
		Server::get('Gateway_Interface'),
		array('t'=>'How the web server is communicating with PHP'),
		array('ro'=>true)
	)
	->addTextField(
		'Protocol',
		'protocol',
		Server::get('Server_Protocol'),
		array('t'=>'Protocol used to communicate between the web server and client'),
		array('ro'=>true)
	)
	->addTextField(
		'PHP Version',
		'php_version',
		phpversion(),
		array('t'=>'Software powering the website'),
		array('ro'=>true)
	)
	->addTextField(
		'HTTPS',
		'server_https',
		Server::get('HTTPS'),
		array('t'=>'HTTPS Status of the server'),
		array('ro'=>true)
	)
	->addHTML('<h3>Database</h3>',false)
	->addHTML('<h4>Read Server</h4>',false)
	->addTextField(
		'Connection',
		'mysqlr_con',
		$this->mySQL_r->host_info,
		array('t'=>''),
		array('ro'=>true)
	)
	->addTextField(
		'Version',
		'mysqlr_ver',
		$this->mySQL_r->server_info,
		array('t'=>''),
		array('ro'=>true)
	)
	->addTextField(
		'Client',
		'mysqlr_cli',
		$this->mySQL_r->client_info,
		array('t'=>''),
		array('ro'=>true)
	)
	->addHTML('<h4>Write Server</h4>',false)
	->addTextField(
		'Connection',
		'mysqlw_con',
		$this->mySQL_w->host_info,
		array('t'=>''),
		array('ro'=>true)
	)
	->addTextField(
		'Version',
		'mysqlw_ver',
		$this->mySQL_w->server_info,
		array('t'=>''),
		array('ro'=>true)
	)
	->addTextField(
		'Client',
		'mysqlw_cli',
		$this->mySQL_w->client_info,
		array('t'=>''),
		array('ro'=>true)
	);
	
	$form->build();
?>
<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">System Information</h1>
    <?php print $form->getForm(); ?>
  </div>
</div>
