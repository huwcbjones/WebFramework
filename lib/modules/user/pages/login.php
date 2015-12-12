<?php
$loginBtn = array('s'=>B_T_PRIMARY, 'a'=>array('t'=>'submit'), 'ic'=>'login');
$registerBtn = array('a'=>array('t'=>'url', 'a'=>'/user/register'));
$forgotBtn = array('a'=>array('t'=>'url', 'a'=>'/user/recover'), 'l'=>'Forgot Details?');
$return = (WebApp::get('r')!==NULL)?WebApp::get('r'):'';
$form = $page->getPlugin('form', array('u_login', WebApp::action('user', 'login'), 'post'));
$form->setIndent('  ')
	->setAutofill(true)
	->setColumns(3, 6)
	->addHiddenField('r', $return)
	->addHTML('    <div class="col-xs-12">'.PHP_EOL.'      <h2 class="form-signin-heading">'.$page->getTitle().'</h2>'.PHP_EOL.'    </div>'.PHP_EOL)
	->addTextField(
		'Username/Email', 																		// Label
		'user', 																				// Name
		'',																						// Value
		array('t'=>'Your username or email address.', 'p'=>'Username/Email'),					// Help Text
		array(																					// Options
			'r'=>true,																			// Required
			'v'=>true,																			// Validate
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'A Username or Email is required.','s'=>'danger')
			),
			'vo'=>'validateOn:["blur", "change"]'
		)
	)
	->addPasswordField(
		'Password',
		'pwd',
		'',
		array('t'=>'Your password','p'=>'Password'),
		array(
			't'=>'password',
			'r'=>true
		)
	)
	->addBtnLine(array('login'=>$loginBtn))
	->addBtnLine(array('register'=>$registerBtn, 'forgot'=>$forgotBtn))
	->build();
?>

<div class="row pane">
  <div class="col-sm-12 col-md-11 col-lg-11 col-lg-offset-1 col-md-offset-1">
<?php print $form->getForm(); ?>
  </div>
</div>
