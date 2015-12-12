<?php
$types['p']['v'] = 'p';
$types['p']['n'] = 'Primary';
$types['p']['s'] = false;
$types['p']['d'] = 0;

$types['s']['v'] = 's';
$types['s']['n'] = 'Secondary';
$types['s']['s'] = false;
$types['s']['d'] = 0;

$pages = array();
$pages_query = $this->mySQL_r->prepare("SELECT `ID`, `title`, `cat1` FROM `core_pages` WHERE `cat1`!='admin' ORDER BY `cat1` ASC");
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
		$pdata['c'] = false;
		$pages[ucfirst($module)][$ID] = $pdata;
	}
}
ksort($pages);
$next_gid_q = $mySQL_r->query("SHOW TABLE STATUS LIKE 'core_groups'");
$next_gid = mysqli_fetch_array($next_gid_q);
$next_gid = $next_gid['Auto_increment'];
mysqli_free_result($next_gid_q);

if(WebApp::get('r')=='d'){
	$closeBtn = array('a'=>array('t'=>'url', 'a'=>'./'), 'ic'=>'remove-sign');
}elseif(WebApp::get('r')=='v'){
	$closeBtn = array('a'=>array('t'=>'url', 'a'=>'group_view'), 'ic'=>'remove-sign');
}else{
	$closeBtn = array('a'=>array('t'=>'url', 'a'=>'./'), 'ic'=>'remove-sign');
}
$addBtn = array('s'=>B_T_SUCCESS, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'group_add\', this)'), 'ic'=>'floppy-disk');
$form = $page->getPlugin('form', array('group_add', WebApp::action('user', 'group_add', true), 'post'));
$form->setIndent('    ');
$form->addBtnLine(array('close'=>$closeBtn, 'add'=>$addBtn));
$form->addTextField(
	'Group ID',
	'id',
	$next_gid,
	array('t'=>'ID of Group (Unique)'),
	array(
		'r'=>true,
		't'=>'number',
		'vt'=>'integer',
		'v'=>true,
		'vm'=>array(
			'textfieldRequiredMsg'=>array('m'=>'A Group ID is required.', 's'=>B_T_FAIL),
			'textfieldInvalidFormatMsg'=>array('m'=>'please enter an integer.', 's'=>B_T_FAIL)
		),
		'vo'=>'validateOn:["blur"]',
	)
);
$form->addTextField(
	'Name',
	'name',
	'',
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
	'',
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
$form->addCollapseOptGrid(
	'check',
	'Pages',
	'pages[]',
	$pages,
	array(
		't'=>'Select the pages you wish this group to have access to.'
	));
$form->addBtnLine(array('close'=>$closeBtn, 'add'=>$addBtn));
$form->build();
?>

<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">New Group</h1>
<?php print $form->getForm() ?>
  </div>
</div>