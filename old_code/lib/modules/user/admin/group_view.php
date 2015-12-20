<div class="row pane">
  <div class="col-xs-12">
    <div class="row">
<?php
// Add Group BTN
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->accessAdminPage(12)){
	print('          <a class="btn btn-xs btn-block btn-success" href="group_add?r=v">New&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-plus-sign"></span></a>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

// Delete Group BTN
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->inGroup(13, true)){
	print('          <button class="btn btn-xs btn-danger btn-block disabled group_need_check" onclick="delete_mod()">Delete&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-trash"></span></button>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

// Edit Group BTN
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->accessAdminPage(13)){
	print('          <button class="btn btn-xs btn-primary btn-block disabled" id="edit_btn">Edit&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-edit"></span></button>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

// Enable Group BTN
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->accessAdminPage(13)){
	print('          <button class="btn btn-xs btn-success btn-block disabled group_need_check" onclick="enable_mod()">Enable&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-ok-sign"></span></button>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

// Disable Group BTN
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->accessAdminPage(13)){
	print('          <button class="btn btn-xs btn-danger btn-block disabled group_need_check" onclick="disable_mod()">Disable&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-ban-circle"></span></button>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);
?>
    </div><br />
    <div class="row">
      <div class="col-xs-12 table-responsive">
<?php
$group_view = $page->getPlugin('table', array('groups'));
$group_view
	->setIndent('        ')
	->addClass('table-bordered')
	->addClass('table-hover')
	->addClass('table-striped')
	->sort(true)
	->sticky(true)
	->pager(true);
	
$thead = array();
if($this->accessAdminPage(13)||$this->inGroup(13, true)){
	$thead['selectAll'] = Table::addTHeadCell('<input type="checkbox" id="selectAll" />', false);
}
$thead['ID']		= Table::addTHeadCell('ID');
$thead['Name']		= Table::addTHeadCell('Name');
$thead['Desc']		= Table::addTHeadCell('Description');
$thead['En']		= Table::addTHeadCell('<abbr title="Enabled">En</abbr>');
$thead['NP']		= Table::addTHeadCell('<abbr title="Number of Pages">NP</abbr>');
$thead['T']		= Table::addTHeadCell('<abbr title="Group Type">T</abbr>');
if($this->accessAdminPage(13)){
	$thead['Edit']	= Table::addTHeadCell('Edit', false);
}
$group_view->addHeader($thead);

$group_query = $mySQL_r->prepare(
"
SELECT `core_groups`.`GID`, `name`, `desc`, COUNT(`PID`), `en`, `type`
FROM `core_groups`
LEFT JOIN `core_gpage` ON `core_gpage`.`GID`=`core_groups`.`GID` GROUP BY `core_groups`.`GID`
"
);
$group_query->execute();
$group_query->bind_result($GID,$g_name,$desc,$pages,$enabled,$type);
$groups = array();
	
while($group_query->fetch()){
	if($this->accessAdminPage(13)||$this->inGroup(13, true)) $row['select']	= Table::addCell('<input class="groups_check" type="checkbox" value="'.$GID.'" name="group[]" />');
	$row['ID']		= Table::addCell($GID);
	$row['name']	= Table::addCell($g_name, 'i_'.$GID);
	$row['desc']= Table::addCell($desc);
	$row['en']		= Table::addCell(Form::toggleLink($this, $enabled, '', 6, array(
			's'=>array(
				'h'=>'Click to disable group.',
				'i'=>'ok-sign',
				'u'=>'/action/user/group_disable?g'.$GID,
				'c'=>'processData(this.href);return false;'
			),
			'f'=>array(
				'h'=>'Click to enable group.',
				'i'=>'ban-circle',
				'u'=>'/action/user/group_enable?g='.$GID,
				'c'=>'processData(this.href);return false;'
			)
		)
	));
	$row['np']		= Table::addCell($pages);
	if($type=='p'){
		$row['t']		= Table::addCell('<abbr title="Primary Group">P</abbr>');
	}else{
		$row['t']		= Table::addCell('<abbr title="Secondary Group">S</abbr>');
	}
	
	if($this->accessAdminPage(13)){
	$row['edit']	= Table::addCell('<a href="group_edit/'.$GID.'"><span class="'.B_ICON.' '.B_ICON.'-edit"></span></a>');
	}
	$group_view->addRow($row);
}

$group_view->build();
print $group_view->getTable();
?>
        <script type="text/javascript">
$(function() {
	$("#selectAll").click(function(){
		$(".groups_check").prop('checked', this.checked);
		if(this.checked){
			$(".group_need_check").removeClass("disabled");
		}else{
			$("#edit_btn").addClass("disabled");
			$(".group_need_check").addClass("disabled");
		}
	});
	$(".groups_check").change(function(){
		var check = ($('.groups_check').filter(":checked").length == $('.groups_check').length);
		$('#selectAll').prop("checked", check);
		if($('.groups_check').filter(":checked").length>0){
			if($('.groups_check').filter(":checked").length==1){
				$("#edit_btn").removeClass("disabled");
			}else{
				$("#edit_btn").addClass("disabled");
			}
			$(".group_need_check").removeClass("disabled");
		}else{
			$("#edit_btn").addClass("disabled");
			$(".group_need_check").addClass("disabled");
		}
	});
	$("#edit_btn").click(function(e){
		var groups = $('.groups_check').filter(":checked")
		if(groups.length==1){
			var group_id = groups.first().val();
			document.location.href = "group_edit/"+group_id;
		}else if(groups.length>1){
			alert("Please select one group only to edit");
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
$delete_modal = $page->getPlugin('modalconf', array('delete', 'group', WebApp::action('user','group_del', true), 'post'));
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

$enable_modal = $page->getPlugin('modalconf', array('enable', 'group', WebApp::action('user','group_enable', true), 'post'));
$enable_modal->addDefaultConfig();
$enable_modal
	->setDefaultContent()
	->SetDefaultModal()
	->setRightBtn('success','Enable','ok-sign')
	->build();
print $enable_modal->getModal();

$disable_modal = $page->getPlugin('modalconf', array('disable', 'group', WebApp::action('user','group_disable', true), 'post'));
$disable_modal->addDefaultConfig();
$disable_modal
	->setDefaultContent()
	->SetDefaultModal()
	->setRightBtn('danger','Disable','ban-circle')
	->build();
print $disable_modal->getModal();
?>