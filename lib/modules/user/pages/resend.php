<div class="row pane">
  <div class="col-xs-12">
    <div class="row">
      <div class="col-xs-12">
        <h2>Resend Activation</h2>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12">
        <p>Fill out the form below. You can enter either your username or email address, or both. Then click 'Resend' to resend the activation email for your account.<br />
        <small>(Please note that activation codes last for 7 days and that resending the activation email will void any other codes)</small></p>
<?php
$resendBtn = array('s'=>B_T_PRIMARY, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'resend\', this, \'resend\')'), 'ic'=>'send');
$form = $page->getPlugin('form', array('resend', WebApp::action('user', 'resend', true), 'post'));
$form
	->setIndent('        ')
	->setColumns(3,9)
	->addTextField(
		'Username',
		'user',
		'',
		array('t'=>'Your username', 'p'=>'username')
	)
	->addTextField(
		'Email',
		'email',
		'',
		array('t'=>'Your email address', 'p'=>'someone@example.com')
	)
	->addBtnLine(array('resend'=>$resendBtn));
$form->build();
print $form->getForm();
?>
      </div>
    </div>
  </div>
</div>