<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">Unlock session</h1>
    <p class="lead">Your session has been locked. To continue, you must enter your password.</p>
<?php
$logoutBtn = array('s'=>B_T_DEFAULT, 'a'=>array('t'=>'url', 'a'=>'/action/user/logout'), 'ic'=>'log-out');
$continueBtn = array('s'=>B_T_PRIMARY, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'auth\', this, \'ok\');'), 'ic'=>'ok');
$form = $page->getPlugin('form', array('auth', WebApp::action('user', 'auth', true), 'post'));
$form->setIndent('  ')
	->setColumns(0, 12)
	->addPasswordField(
		'Password',
		'pwd',
		'',
		array('t'=>'', 'p'=>'Password'),
		array(
			'r'=>true
		)
	)
	->addHiddenField(
		'url',
		WebApp::get('r')
	)
	->addBtnLine(array('Logout'=>$logoutBtn, 'Continue'=>$continueBtn));
$form->build();
print $form->getForm();
?>
  </div>
</div>