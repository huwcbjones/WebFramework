<div class="row pane">
  <div class="col-xs-12">
    <div class="row">
<?php
// Add User BTN
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->accessAdminPage(2)){
	print('          <a class="btn btn-xs btn-block btn-success" href="user_add?r=v">New&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-plus-sign"></span></a>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

// Delete User BTN
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->inGroup(3, true)){
	print('          <button class="btn btn-xs btn-danger btn-block disabled user_need_check" onclick="delete_mod()">Delete&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-trash"></span></button>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

// Edit User BTN
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->accessAdminPage(3)){
	print('          <button class="btn btn-xs btn-primary btn-block disabled" id="edit_btn">Edit&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-edit"></span></button>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

// Enable User BTN
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->accessAdminPage(3)){
	print('          <button class="btn btn-xs btn-success btn-block disabled user_need_check" onclick="enable_mod()">Enable&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-ok-sign"></span></button>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

// Disable User BTN
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->accessAdminPage(3)){
	print('          <button class="btn btn-xs btn-danger btn-block disabled user_need_check" onclick="disable_mod()">Disable&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-ban-circle"></span></button>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

// Set PWD User BTN
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->accessAdminPage(3)){
	print('          <button class="btn btn-xs btn-warning btn-block disabled user_need_check" onclick="set_password_for_mod()">Set Password&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-lock"></span></button>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);
?>
    </div><br />
    <div class="row">
      <div class="col-xs-12">
<?php
$user_view= $page->getPlugin('table', array('users'));
$user_view
	->setIndent('        ')
	->addClass('table-bordered')
	->addClass('table-hover')
	->addClass('table-striped')
	->sort(true)
	->sticky(true);
$thead = array();
if($this->accessAdminPage(3)||$this->inGroup(3, true)){
	$thead['selectAll'] = Table::addTHeadCell('<input type="checkbox" id="selectAll" />', false);
}
$thead['ID']		= Table::addTHeadCell('ID');
$thead['Name']		= Table::addTHeadCell('Name');
$thead['Username']	= Table::addTHeadCell('Username');
$thead['Email']		= Table::addTHeadCell('Email');
$thead['En']		= Table::addTHeadCell('<abbr title="Enabled">E</abbr>');
$thead['Act']		= Table::addTHeadCell('<abbr title="Activated">A</abbr>');
$thead['Group']		= Table::addTHeadCell('Group');
if($this->accessAdminPage(3)){
	$thead['Edit']	= Table::addTHeadCell('Edit', false);
}
$user_view->addHeader($thead);

$user_query = $mySQL_r->prepare("SELECT `id`, CONCAT(`f_name`, ' ', `s_name`), `username`,`email`,`core_users`.`en`,`act_b`,`name` FROM `core_users` LEFT JOIN `core_groups` ON `core_users`.`p_group`=`core_groups`.`GID`");
$user_query->execute();
$user_query->bind_result($user_id, $name,$username,$email,$enabled,$activated,$group);
$user_query->store_result();
	
while($user_query->fetch()){
	if($this->accessAdminPage(3)||$this->inGroup(3, true)) $row['select']	= Table::addCell('<input class="users_check" type="checkbox" value="'.$user_id.'" name="user[]" />');
	$row['ID']		= Table::addCell($user_id);
	$row['name']	= Table::addCell($name, 'i_'.$user_id);
	$row['username']= Table::addCell($username);
	$row['email']	= Table::addCell($email);
	$row['en']		= Table::addCell(Form::toggleLink($this, $enabled, '', 3, array(
			's'=>array(
				'h'=>'Click to disable user.',
				'i'=>'ok-sign',
				'u'=>'/action/user/user_disable?u='.$user_id,
				'c'=>'processData(this.href);return false;'
			),
			'f'=>array(
				'h'=>'Click to enable user.',
				'i'=>'ban-circle',
				'u'=>'/action/user/user_enable?u='.$user_id,
				'c'=>'processData(this.href);return false;'
			)
		)
	));
	$row['act']		= Table::addCell(Form::toggleLink($this, $activated, '', 3, array(
			's'=>array(
				'i'=>'ok-sign',
			),
			'f'=>array(
				'i'=>'ban-circle',
			)
		)
	));
	$row['group']	= Table::addCell($group);
	if($this->accessAdminPage(3)){
	$row['edit']	= Table::addCell('<a href="user_edit/'.$user_id.'"><span class="'.B_ICON.' '.B_ICON.'-edit"></span></a>');
	}
	$user_view->addRow($row);
}

$user_view->build();
print $user_view->getTable();
?>
        <script type="text/javascript">
$(function() {
	$("#selectAll").click(function(){
		$(".users_check").prop('checked', this.checked);
		if(this.checked){
			$(".user_need_check").removeClass("disabled");
		}else{
			$("#edit_btn").addClass("disabled");
			$(".user_need_check").addClass("disabled");
		}
	});
	$(".users_check").change(function(){
		var check = ($('.users_check').filter(":checked").length == $('.users_check').length);
		$('#selectAll').prop("checked", check);
		if($('.users_check').filter(":checked").length>0){
			if($('.users_check').filter(":checked").length==1){
				$("#edit_btn").removeClass("disabled");
			}else{
				$("#edit_btn").addClass("disabled");
			}
			$(".user_need_check").removeClass("disabled");
		}else{
			$("#edit_btn").addClass("disabled");
			$(".user_need_check").addClass("disabled");
		}
	});
	$("#edit_btn").click(function(e){
		var users = $('.users_check').filter(":checked")
		if(users.length==1){
			var user_id = users.first().val();
			document.location.href = "user_edit/"+user_id;
		}else if(users.length>1){
			alert("Please select one user only to edit");
		}
		return false;
	});
	
})
        </script>
      </div>
    </div>
  </div>
</div>

<?php
$delete_modal = $page->getPlugin('modalconf', array('delete', 'user', WebApp::action('user','user_delete', true), 'post'));
$delete_modal->addDefaultConfig();
$delete_modal->form->addPasswordField(
	'Your Password',
	'pwd',
	'',
	array('t'=>'Your password','p'=>'Your Password'),
	array(
		't'=>'password',
		'w'=>true,
		'r'=>true
	)
);
$delete_modal
	->setDefaultContent()
	->SetDefaultModal()
	->setRightBtn('danger','Delete','trash')
	->build();
print $delete_modal->getModal();

$enable_modal = $page->getPlugin('modalconf', array('enable', 'user', WebApp::action('user','user_enable', true), 'post'));
$enable_modal->addDefaultConfig();
$enable_modal
	->setDefaultContent()
	->SetDefaultModal()
	->setRightBtn('success','Enable','ok-sign')
	->build();
print $enable_modal->getModal();

$disable_modal = $page->getPlugin('modalconf', array('disable', 'user', WebApp::action('user','user_disable', true), 'post'));
$disable_modal->addDefaultConfig();
$disable_modal
	->setDefaultContent()
	->SetDefaultModal()
	->setRightBtn('danger','Disable','ban-circle')
	->build();
print $disable_modal->getModal();

$setpassword_modal = $page->getPlugin('modalconf', array('set password for', 'user', WebApp::action('user','setpassword', true), 'post'));
$setpassword_modal->addDefaultConfig();
$setpassword_modal->form
	->setColumns(4, 5, 3)
	->addPasswordField(
		'New Password',
		'n_pwd',
		'',
		array('t'=>'The users\' new password','p'=>'New Password'),
		array(
			't'=>'password',
			'v'=>true,
			'w'=>true,
			'r'=>true
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
			'vc'=>'user_set_password_for_form\:\:n_pwd',
			'vo'=>'validateOn:["blur", "change"]'
		)
	);
$setpassword_modal
	->setDefaultContent()
	->SetDefaultModal()
	->setRightBtn('warning','Set Password','lock')
	->build();
print $setpassword_modal->getModal();
?>