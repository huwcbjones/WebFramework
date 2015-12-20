<?php
if(WebApp::get('cat4')===NULL||is_numeric(WebApp::get('cat4'))===false){
	$page->setStatus(404);
}
$GID = intval(WebApp::get('cat4'));
$group_query = $mySQL_r->prepare("SELECT `GID`,`type`,`name`,`desc`,`en` FROM `core_groups` WHERE `GID`=?");
$gpage_query = $mySQL_r->prepare("SELECT `PID` FROM `core_gpage` WHERE `GID`=?");

$group_query->bind_param('i',$GID);
$group_query->execute();
$group_query->bind_result($GID,$type,$name,$desc,$enabled);
$group_query->store_result();

if($group_query->num_rows==1){
	$group_query->fetch();
	$gpages = array();
	$gpage_query->bind_param('i',$GID);
	$gpage_query->execute();
	$gpage_query->bind_result($PID);
	$gpage_query->store_result();
	while($gpage_query->fetch()){
		$gpages[] = $PID;
	}
}else{
	$page->setStatus(404);
}


// Group Types
$types['p']['v'] = 'p';
$types['p']['n'] = 'Primary';
if($type=='p'){
	$types['p']['s'] = true;
}else{
	$types['p']['s'] = false;
}
$types['p']['d'] = 0;

$types['s']['v'] = 's';
$types['s']['n'] = 'Secondary';
if($type=='s'){
	$types['s']['s'] = true;
}else{
	$types['s']['s'] = false;
}
$types['s']['d'] = 0;

// Get Available Pages
$pages = array();
$pages_query = $this->mySQL_r->prepare("SELECT `ID`, `title`, `cat1` FROM `core_pages` WHERE `cat1`!='admin' ORDER BY `cat1` ASC, `id` ASC");
$pages_query->bind_result($pID, $pTitle, $pModule);
$pages_query->execute();
$pages_query->store_result();
while($pages_query->fetch()){
	$db_pages[$pModule][$pID] = $pTitle;
}
$pages_query->free_result();

$pages_query = $this->mySQL_r->prepare("SELECT `ID`, `title`, `cat2` FROM `core_pages` WHERE `cat1`='admin' ORDER BY `cat2` ASC");
$pages_query->bind_result($aID, $aTitle, $aModule);
$pages_query->execute();
$pages_query->store_result();
while($pages_query->fetch()){
	if($aModule == ''){
		$db_pages['Site Administration'][$aID] = $aTitle;
	}else{
		$db_pages[$aModule.' - Administration'][$aID] = $aTitle;
	}
}
$pages_query->free_result();

$pages = array();
foreach($db_pages as $module=>$data){
	foreach($data as $ID=>$title){
		$pdata = array();
		$pdata['i'] = $ID;
		$pdata['l'] = $title;
		$pdata['v'] = $ID;
		$pdata['d'] = false;
		if(in_array($ID, $gpages)){
			$pdata['c'] = true;
		}else{
			$pdata['c'] = false;
		}
		$pages[ucfirst($module)][$ID] = $pdata;
	}
}
ksort($pages);

$closeBtn = array('a'=>array('t'=>'url', 'a'=>'../group_view'), 'ic'=>'remove-sign');
$saveBtn = array('s'=>B_T_SUCCESS, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'group_edit\', this, \'save\')'), 'ic'=>'floppy-disk');
$applyBtn = array('s'=>B_T_PRIMARY, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'group_edit\', this, \'apply\')'), 'ic'=>'ok-sign');
$form = $page->getPlugin('form', array('group_edit', WebApp::action('user', 'group_edit', true), 'post'));
$form->setIndent('    ');
$form->addBtnLine(array('close'=>$closeBtn, 'save'=>$saveBtn, 'apply'=>$applyBtn));
$form->addTextField(
	'Group ID',
	'id',
	$GID,
	array('t'=>'ID of Group (Unique)'),
	array('ro'=>true)
);
$form->addTextField(
	'Name',
	'name',
	$name,
	array('t'=>'Name of Group (Unique)', 'p'=>'Group Name'),
	array(
		'v'=>true,
		'vm'=>array(
			'textfieldRequiredMsg'=>array('m'=>'A Group name is required.', 's'=>B_T_FAIL),
			'textfieldMinCharsMsg'=>array('m'=>'A Group name is required.', 's'=>B_T_FAIL),
			'textfieldMaxCharsMsg'=>array('m'=>'Group name is limited to 100 chars.', 's'=>B_T_FAIL)
		),
		'vo'=>'minChars: 0, maxChars: 100, validateOn:["blur"]',
		'r'=>true
	)
);
$form->addTextArea(
	'Description',
	'desc',
	$desc,
	3,
	array('t'=>'Description of Group', 'p'=>'Group Description'),
	array(
		'v'=>true,
		'vm'=>array(
			'textareaMaxCharsMsg'=>array('m'=>'Group description is limited to 250 chars.', 's'=>B_T_FAIL)
		),
		'vo'=>'maxChars: 250, useCharacterMasking:false, validateOn:["blur", "change"]',
		'c'=>true,
		'r'=>false
	)
);
$form->addSelect(
	'Type',
	'type',
	$types,
	array('t'=>'Primary Groups are the base group from which additional privileges are added via Secondary Groups.'),
	array(
		'r'=>true,
		'v'=>true,
		'vm'=>array('selectRequiredMsg selectInvalidMsg'=>array('s'=>B_T_FAIL, 'm'=>'A group type is required.')),
		'vo'=>'validateOn:["blur"]'
	)
);
$form->addButtonGroup(
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
	array('t'=>'Disabling a group automatically logs all users that belong to that group out and they cannot log back in.')	// Help Text
);
$form->addCollapseOptGrid(
	'check',
	'Pages',
	'pages[]',
	$pages,
	array('t'=>'A checked box indicates that this group is able to access that page. Please note, when calculating page access privileges, TTP (True Takes Priority) is used. This means if a user is granted access from one group and barred from another, they will still be able to access that page.'),
	array(
		'r'=>true,
		'v'=>false
	)
);

$form->addBtnLine(array('close'=>$closeBtn, 'save'=>$saveBtn, 'apply'=>$applyBtn));
$form->build();
?>

<div class="row pane">
  <div class="col-xs-12">
<?php print $form->getForm() ?>
  </div>
</div>