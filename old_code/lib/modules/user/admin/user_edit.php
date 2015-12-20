<?php
if(WebApp::get('cat4')===NULL||is_numeric(WebApp::get('cat4'))===false){
	$page->setStatus(404);
}
$ID = intval(WebApp::get('cat4'));
$user_query = $mySQL_r->prepare("SELECT `username`,`f_name`,`s_name`,`email`,`p_group`, `en`, `chgPwd`, `act_b`, `pwd_reset` FROM `core_users` WHERE `id`=?");
$sgroup_query = $this->mySQL_r->prepare("SELECT `group`, `name` FROM `core_sgroup` INNER JOIN `core_groups` ON `GID`=`group` AND `user`=? ORDER BY `group` ASC");

$user_query->bind_param('i',$ID);
$sgroup_query->bind_param('i',$ID);

$user_query->bind_result($username,$f_name,$s_name,$email,$p_group, $enabled, $chgPwd, $activated, $pwd_reset);
$sgroup_query->bind_result($sg_ID, $sg_name);

$user_query->execute();

$user_query->store_result();

if($user_query->num_rows==1){
	$user_query->fetch();
	
	$sgroup_query->execute();
	$sgroup_query->store_result();
	$s_groups = array();
	$s_group_IDs = array();
	while($sgroup_query->fetch()){
		$s_groups[] = '			{id: \''.$sg_ID.'\', text:\''.$sg_name.'\'}';
		$s_group_IDs[] = $sg_ID;
	}
	$sgroup_query->free_result();
}else{
	$page->setStatus(404);
}

// Get Primary Group data
$p_group_query = $mySQL_r->prepare("SELECT `GID`,`name` FROM `core_groups` WHERE `type`='p' ORDER BY `GID` ASC");
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
	if($p_group==$k){
		$p_groups[$k]['s'] = true;
	}else{
		$p_groups[$k]['s'] = false;
	}
	$p_groups[$k]['d'] = 0;
}


// Check Password Field Script
$checkPwd = 'function checkPwd(){'.PHP_EOL;
$checkPwd.= '  if(document.getElementById("user_edit::n_pwd").value.length!=0){'.PHP_EOL;
$checkPwd.= '    var conf = confirm("Are you sure you wish to set the user\'s password?");'.PHP_EOL;
$checkPwd.= '    if(conf){'.PHP_EOL;
$checkPwd.= '      return true;'.PHP_EOL;
$checkPwd.= '    }else{'.PHP_EOL;
$checkPwd.= '      return false;'.PHP_EOL;
$checkPwd.= '    }'.PHP_EOL;
$checkPwd.= '  }else{'.PHP_EOL;
$checkPwd.= '    return true;'.PHP_EOL;
$checkPwd.= '  }'.PHP_EOL;
$checkPwd.= '}'.PHP_EOL;

$closeBtn = array('a'=>array('t'=>'url', 'a'=>'../user_view'), 'ic'=>'remove-sign');
$saveBtn = array('s'=>B_T_SUCCESS, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'user_edit\', this, \'save\', \'checkPwd\')'), 'ic'=>'floppy-disk');
$applyBtn = array('s'=>B_T_PRIMARY, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'user_edit\', this, \'apply\', \'checkPwd\')'), 'ic'=>'ok-sign');
$form = $page->getPlugin('form', array('user_edit', WebApp::action('user', 'user_edit', true), 'post'));

$form
	->setColumns(3, 9)
	->setIndent('    ')
	->addHTML('<br />')
	->addScript($checkPwd)
	->addTextField(
		'User ID',
		'id',
		$ID,
		array('t'=>'ID of User.', 'p'=>'ID'),
		array(
			'ro'=>true,
			'd'=>false
		)
	)
	->addTextField(
		'First Name',
		'f_name',
		$f_name,
		array('t'=>'First Name of User.', 'p'=>'First Name'),
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
		array('t'=>'Surname of User.', 'p'=>'Surname'),
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
		array('t'=>'Username. Used for logging in and identifying user.', 'p'=>'Username'),
		array('v'=>false,'d'=>false,'ro'=>true)
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
	)
	->addPasswordField(
		'New Password',
		'n_pwd',
		'',
		array('t'=>'Change the user\'s password','p'=>'New Password'),
		array(
			't'=>'password',
			'v'=>true,
			'w'=>true,
			'r'=>false
		)
	)
	->addTextField(
		'Confirm Password',
		'c_pwd',
		'',
		array('t'=>'Confirm user\'s new password.','p'=>'Confirm Password'),
		array(
			't'=>'password',
			'vt'=>'confirm',
			'v'=>true,
			'vm'=>array(
				'confirmInvalidMsg'=>array('m'=>'Passwords do not match.','s'=>'danger')
			),
			'vc'=>'user_edit\:\:n_pwd',
			'vo'=>'validateOn:["blur", "change"]'
		)
	)
	->addTextField(
		'Pasword Changes',
		'pwd_chgs',
		$pwd_reset,
		array('t'=>'Number of times user has had their password changed.'),
		array('ro'=>true)
	)
	->addButtonGroup(
		'Change Password',
		'chgPwd',
		array(
			array(
				'i'=>'chgPwdY',
				's'=>B_T_SUCCESS,
				'v'=>1,
				'l'=>'Yes',
				'c'=>$chgPwd
			),
			array(
				'i'=>'chgPwdN',
				's'=>B_T_FAIL,
				'v'=>0,
				'l'=>'No',
				'c'=>not($chgPwd)
			)
		),
		array('t'=>'Must user change password next time they request a page?')
	)
	->addButtonGroup(
		'Enabled',
		'enabled',
		array(
			array(
				'i'=>'enabledY',
				's'=>B_T_SUCCESS,
				'v'=>1,
				'l'=>'Yes',
				'c'=>$enabled
			),
			array(
				'i'=>'enabledN',
				's'=>B_T_FAIL,
				'v'=>0,
				'l'=>'No',
				'c'=>not($enabled)
			)
		),
		array('t'=>'Disabling a user automatically logs them out and they cannot log back in.')	// Help Text
	)
	->addButtonGroup(
		'Activated',
		'active',
		array(
			array(
				'i'=>'activeY',
				's'=>B_T_SUCCESS,
				'v'=>1,
				'l'=>'Yes',
				'c'=>$activated
			),
			array(
				'i'=>'activeN',
				's'=>B_T_FAIL,
				'v'=>0,
				'l'=>'No',
				'c'=>not($activated)
			)
		),
		array('t'=>'Has the user activated their account yet?'),
		array('d'=>true)
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
	->addSelect2(
		'Secondary Groups',
		's_group',
		csvgetstr($s_group_IDs),
		array('t'=>'The user\'s secondary groups which add extra privileges.'),
		array(
			'r'=>true
		)
	)
	->addBtnLine(array('close'=>$closeBtn, 'save'=>$saveBtn, 'apply'=>$applyBtn));
$form->build();

$session_query = $this->mySQL_r->prepare("SELECT `id`, `created`, INET_NTOA(`IP`), `lpr` FROM `core_sessions` WHERE `user`=?");
$session_query->bind_param('i', $ID);
$session_query->bind_result($sessID, $sessCreate,$sessIP, $sessLPR);
$session_query->execute();
$session_query->store_result();

$table = $page->getPlugin('table', array('sessions'));
$table
	->setIndent('  ')
	->addClass('table-bordered')
	->addClass('table-hover')
	->addClass('table-striped');
$thead = array();

$thead['ID']		= Table::addTHeadCell('ID');
$thead['Created']	= Table::addTHeadCell('Created');
$thead['IP']		= Table::addTHeadCell('IP');
$thead['LPR']		= Table::addTHeadCell('Last Page Request');
if($this->accessAdminPage(20)){
	$thead['destroy']	= Table::addTHeadCell('');
}
$table->addHeader($thead);

while($session_query->fetch()){
	$row['ID']		= Table::addCell($sessID);
	$row['Created']	= Table::addCell($sessCreate);
	$row['IP']		= Table::addCell($sessIP);
	$row['LPR']		= Table::addCell(date(DATET_SHORT, strtotime($sessLPR)));
	if($this->accessAdminPage(20)){
		$row['destroy']	= Table::addTHeadCell('<a href="#" onclick="processData(\'/action/user/session_destroy/'.$sessID.'\')">Destroy&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-remove-sign"</a>');
	}
	$table->addRow($row);
}
$table->build();
?>

<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">Edit User</h1>
    <ul class="nav nav-tabs nav-justified" role="tablist">
      <li class="active"><a href="#details" role="tab" data-toggle="tab">Details</a></li>
      <li><a href="#sessions" role="tab" data-toggle="tab">Sessions</a></li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane active" id="details">
        <div class="row">
          <div class="col-xs-12">
<?php print $form->getForm();?>
          </div>
        </div>
      </div>
      <div class="tab-pane" id="sessions">
<?php
if ($session_query->num_rows == 0){
	print '  <h4>User has not currently got any active sessions.</h4>'.PHP_EOL;
}else{
	print $table->getTable();
}
?>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
$('[name="s_group"]').select2({
	multiple: true,
	placeholder: "Search for Secondary Groups",
	minimumInputLength: 1,
	ajax: {
		url: "/ajax/user/secondary_groups",
		dataType: 'json',
		data: function(term, page){
			return {
				q: term
			}
		},
		results: function (data, page){
			return {results: data.data.groups}
		}
	},
	initSelection: function(item, callback){
		callback([
<?php
	print implode($s_groups, ','.PHP_EOL);
?>
			
		]);
		
	
	}
});
</script>