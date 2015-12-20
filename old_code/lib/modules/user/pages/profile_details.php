<div class="row pane">
  <div class="col-sm-12 col-md-11 col-lg-11 col-lg-offset-1 col-md-offset-1">
    <h1 class="page-header">Edit my Details</h1>
<?php
$userID = $page->parent->user->getUserID();
$user_query = $mySQL_r->prepare("SELECT `username`,`f_name`,`s_name`,`email`,`pwd_reset` FROM `core_users` WHERE `id`=?");
if($user_query===false){
	print ('<h2>Failed to load details!</h2>'.PHP_EOL);
}else{
	$user_query->bind_param('i', $userID);
	$user_query->execute();
	$user_query->bind_result($username, $f_name, $s_name, $email, $passwords);
	$user_query->store_result();
	$user_query->fetch();
$cancelBtn = array('s'=>B_T_DEFAULT, 'a'=>array('t'=>'url', 'a'=>'../'), 'ic'=>'remove');
$changeBtn = array('s'=>B_T_PRIMARY, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'details\', this, \'change\');'), 'ic'=>'floppy-disk');
$form = $page->getPlugin('form', array('details', WebApp::action('user', 'change_details', true), 'post'));
$form->setIndent('  ')
	->addHiddenField(
		'userid',
		$userID,
		'userid'
	)
	->addTextField(
		'First Name',
		'f_name',
		$f_name,
		array('t'=>'Your First Name', 'p'=>'First Name'),
		array(
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'A First Name is required.','s'=>'danger'),
				'textfieldMinCharsMsg'=>array('m'=>'A First Name is required.', 's'=>'danger'),
				'textfieldMaxCharsMsg'=>array('m'=>'First Name is limited to 100 characters.', 's'=>'danger')
			),
			'vo'=>'minChars: 0, maxChars: 100, validateOn:["blur"]',
			'd'=>false,
			'r'=>true
		)
	)
	->addTextField(
		'Surname',
		's_name',
		$s_name,
		array('t'=>'Your Surname.', 'p'=>'Surname'),
		array(
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'A Surname is required.','s'=>'danger'),
				'textfieldMinCharsMsg'=>array('m'=>'A Surname is required.', 's'=>'danger'),
				'textfieldMaxCharsMsg'=>array('m'=>'Surname is limited to 100 characters.', 's'=>'danger')
			),
			'vo'=>'minChars: 0, maxChars: 100, validateOn:["blur"]',
			'd'=>false,
			'r'=>true
		)
	)
	->addTextField(
		'Username',
		'username',
		$username,
		array('t'=>'Username. Used for logging in.', 'p'=>'Username'),
		array('v'=>false,'d'=>false,'ro'=>true)
	)
	/*->addHiddenField(
		'old_email',
		$email,
		'old_email'
	)
	->addTextField(
		'Email Address',
		'email',
		$email,
		array('t'=>'Email Address. (Unique)', 'p'=>'someone@example.com'),
		array(
			't'=>'email',
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'An email is required.','s'=>'danger'),
				'textfieldInvalidFormatMsg'=>array('m'=>'Not a valid email address.','s'=>'danger'),
			),
			'vo'=>'validateOn:["blur","change"]',
			'd'=>false,
			'r'=>true
		)
	)*/
	->addBtnLine(array('cancel'=>$cancelBtn, 'Save'=>$changeBtn));
$form->build();
print $form->getForm();

/*$checkMod = $page->getPlugin('modalconf', array('change', 'email', WebApp::action('user','e,ao;', true), 'post'));
$checkMod
		->setContent('<p>Are you sure you want to change your email address?<br />If so, your account will be deactivated and a reactivation email will be sent to the new address.</p>')
		->setDefaultModal()
		->setTitle('Change Email Address?')
		->setRightBtn('primary','Change','ok', 'button', 'processForm(\'details\', this, \'change\');$(\'#change_emails\').modal(\'hide\');')
		->addShowScript()
		->build();
print $checkMod->getModal();*/
}
?>
  </div>
</div>
<?php
/*<script type="text/javascript">
function checkEmail(){
	if($("#old_email").val()!=$("#details\\:\\:email").val()){
		$("#change_emails").modal("show");
		return false;
	}
	return true;
}
</script>*/
?>