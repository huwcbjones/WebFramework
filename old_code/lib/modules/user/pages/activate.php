<div class="row pane">
  <div class="col-xs-12">
    <div class="row">
      <div class="col-xs-12">
        <h2>Activate Account</h2>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12">
        <p>To activate your account, copy and paste the activation code from your activation email and paste into the box below. Set your new password and click activate.</p>
        <p>If you did not get this email within 30 minutes, please <a href="/user/resend">click here</a> to resend the activation email.</p>
<?php
$activateBtn = array('s'=>B_T_PRIMARY, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'activate\', this, \'activate\')'), 'ic'=>'ok-sign');
$form = $page->getPlugin('form', array('activate', WebApp::action('user', 'activate', true), 'post'));
$form
	->setIndent('        ')
	->setColumns(3,6,3)
	->addTextField(
		'Code',
		'code',
		WebApp::get('code'),
		array('t'=>'Copy and paste your activation code here', 'p'=>'aaaabbbbccccddddeeeeffff11112222'),
		array(
			'r'=>true,
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
			'vc'=>'activate\:\:n_pwd',
			'vo'=>'validateOn:["blur"]'
		)
	)
	->addBtnLine(array('activate'=>$activateBtn));
$form->build();
print $form->getForm();
?>
      </div>
    </div>
  </div>
</div>