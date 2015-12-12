<div class="row pane">
  <div class="col-xs-12">
	  <div class="row">
		<div class="col-xs-12">
		  <h2>Forgotten Details</h2>
		</div>
	  </div>
	  <div class="row">
		<div class="col-xs-12">
		  <p>Enter your username, email address or both, then click 'Recover'.
			Your account will be deactivated and an activation email will be sent to your email address.
			This email will confirm your username and have a link to reactivate your account and change your password.
		  </p>
		</div>
	  </div>
<?php
$recoverBtn = array('s'=>B_T_PRIMARY, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'recover\', this, \'recover\')'), 'ic'=>'repeat');
$form = $page->getPlugin('form', array('recover', WebApp::action('user', 'recover', true), 'post'));
$form
	->setColumns(2, 8, 2)
	->setIndent('  ')
	->addTextfield(
		'Username',
		'username',
		'',
		array('t'=>'Enter your username', 'p'=>'Username'),
		array(
			't'=>'text',
			'v'=>true,
			'vo'=>'validateOn:["blur","change"]',
			'vm'=>array()
		)
	)
	->addTextfield(
		'Email',
		'email',
		'',
		array('t'=>'Enter your email address', 'p'=>'example@example.com'),
		array(
			't'=>'email',
			'v'=>true,
			'vm'=>array(
				'textfieldInvalidFormatMsg'=>array('m'=>'Not a valid email address.','s'=>'danger')
			),
			'vo'=>'validateOn:["blur","change"]',
		)
	)
	->addBtnLine(array('recover'=>$recoverBtn));
$form->build();
print $form->getForm();
?>
</div>