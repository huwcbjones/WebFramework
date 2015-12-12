<?php
// Get Primary Group data
$p_group_query = $mySQL_r->prepare("SELECT `GID`,`name` FROM `core_groups` WHERE `type`='p' AND `GID`!='0' AND `GID`!='1' ORDER BY `GID` ASC");
$p_group_query->execute();
$p_group_query->bind_result($GID,$gname);
$p_group_data = array();
while($p_group_query->fetch()){
  $p_group_data[$GID] = $gname;
}
$p_groups = array();

foreach($p_group_data as $k=>$v){
	$p_groups[$k]['v'] = $k;
	$p_groups[$k]['n'] = $v;
	if($k==3){
		$p_groups[$k]['s'] = true;
	}else{
		$p_groups[$k]['s'] = false;
	}
	$p_groups[$k]['d'] = false;
}

if(WebApp::get('r')=='d'){
	$closeBtn = array('a'=>array('t'=>'url', 'a'=>'./'), 'ic'=>'remove-sign');
}elseif(WebApp::get('r')=='v'){
	$closeBtn = array('a'=>array('t'=>'url', 'a'=>'user_view'), 'ic'=>'remove-sign');
}else{
	$closeBtn = array('a'=>array('t'=>'url', 'a'=>'./'), 'ic'=>'remove-sign');
}
$addBtn = array('s'=>B_T_SUCCESS, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'user_add\', this, \'add\')'), 'ic'=>'floppy-disk');
$form = $page->getPlugin('form', array('user_add', WebApp::action('user','user_add', true), 'post'));
$form->setIndent('    ')
	->addTextField(
		'First Name',
		'f_name',
		'',
		array('t'=>'First Name of User', 'p'=>'First Name'),
		array(
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'A First Name is required.', 's'=>B_T_FAIL),
				'textfieldMinCharsMsg'=>array('m'=>'A First Name is required.', 's'=>B_T_FAIL),
				'textfieldMaxCharsMsg'=>array('m'=>'First Name is limited to 100 chars.', 's'=>B_T_FAIL)
			),
			'vo'=>'minChars: 0, maxChars: 100, validateOn:["blur"]',
			'r'=>true
		)
	)
	->addTextField(
		'Surname',
		's_name',
		'',
		array('t'=>'Surname of User', 'p'=>'Surname'),
		array(
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'A Surname is required.', 's'=>B_T_FAIL),
				'textfieldMinCharsMsg'=>array('m'=>'A Surname is required.', 's'=>B_T_FAIL),
				'textfieldMaxCharsMsg'=>array('m'=>'Surname is limited to 100 chars.', 's'=>B_T_FAIL)
			),
			'vo'=>'minChars: 0, maxChars: 100, validateOn:["blur"]',
			'r'=>true
		)
	)
	->addTextField(
		'Username',
		'username',
		'',
		array('t'=>'Username - used for logging in.', 'p'=>'Username'),
		array(
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'A Username is required.', 's'=>B_T_FAIL),
				'textfieldMinCharsMsg'=>array('m'=>'A Username is required.', 's'=>B_T_FAIL),
				'textfieldMaxCharsMsg'=>array('m'=>'Username are limited to 50 chars.', 's'=>B_T_FAIL)
			),
			'vo'=>'minChars: 0, maxChars: 50, validateOn:["blur"]',
			'r'=>true
		)
	)
	->addTextField(
		'Email Address',
		'email',
		'',
		array('t'=>'Email Address.', 'p'=>'someone@example.com'),
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
	)
	->addSelect(
		'Primary Group',
		'p_group',
		$p_groups,
		array('t'=>'The user\'s primary group from which add extra privileges can be added.'),
		array(
			'v'=>true,
			'vm'=>array(
				'selectRequiredMsg'=>array('m'=>'A primary group is required.','s'=>'danger')
			),
			'vo'=>'validateOn:["blur"]',
			'r'=>true
		)
	)
	//->addReCAPTCHA()
	->addBtnLine(array('close'=>$closeBtn, 'add'=>$addBtn));
$form->build();
?>

<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">New User</h1>
<?php print $form->getForm() ?>
  </div>
</div>
