<div class="row pane">
  <div class="col-sm-12 col-md-11 col-lg-11 col-lg-offset-1 col-md-offset-1">
    <h1 class="page-header">Change my Password</h1>
<?php
$cancelBtn = array('s'=>B_T_DEFAULT, 'a'=>array('t'=>'url', 'a'=>'../'), 'ic'=>'remove');
$changeBtn = array('s'=>B_T_PRIMARY, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'change_mod();'), 'ic'=>'ok');
$form = $page->getPlugin('form', array('password', WebApp::action('user', 'password', true), 'post'));
$form->setIndent('  ')
	->addPasswordField(
		'Old Password',
		'o_pwd',
		'',
		array('t'=>'Enter your old password', 'p'=>'Old Password'),
		array(
			'r'=>true
		)
	)
	->addPasswordField(
		'New Password',
		'n_pwd',
		'',
		array('t'=>'Enter your new password', 'p'=>'New Password'),
		array(
			'r'=>true,
			'v'=>true,
			'w'=>true
		)
	)
	->addTextField(
		'Confirm Password',
		'c_pwd',
		'',
		array('t'=>'Confirm your new password.','p'=>'Confirm Password'),
		array(
			't'=>'password',
			'vt'=>'confirm',
			'v'=>true,
			'vm'=>array(
				'confirmInvalidMsg'=>array('m'=>'Passwords do not match.','s'=>'danger')
			),
			'vc'=>'password\:\:n_pwd',
			'vo'=>'validateOn:["blur"]'
		)
	)
	->addBtnLine(array('cancel'=>$cancelBtn, 'change'=>$changeBtn));
$form->build();
print $form->getForm();

$checkMod = $page->getPlugin('modalconf', array('change', 'password', WebApp::action('user','password', true), 'post'));
$checkMod
		->setContent('<p>Are you sure you want to change your password?</p>')
		->setDefaultModal()
		->setRightBtn('primary','Change','ok', 'button', 'processForm(\'password\', this, \'change\');$("#change_passwords").modal("hide");')
		->addShowScript()
		->build();
print $checkMod->getModal();
?>
  </div>
</div>