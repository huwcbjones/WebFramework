<?php
if(WebApp::get('cat4')!==NULL){
	$data_query = $mySQL_r->prepare(
"SELECT `core_cron`.`ID`, `enable`, `mins`, `hours`, `days`, `month`, `dow`, `user_id`, `name`, `action`, `core_cron`.`description`, `last_run` FROM `core_cron`
LEFT JOIN `core_modules`
ON `core_modules`.`module_id` = `core_cron`.`mod_id`
WHERE `core_cron`.`ID`=?");
	if(GUMP::is_valid(array('id'=>WebApp::get('cat4')),array('id'=>'required|integer'))){
		$id = WebApp::get('cat4');
		$data_query->bind_param('i', $id);
		$data_query->execute();
		$data_query->bind_result($cron_id, $enabled, $mins, $hours, $days, $months, $dow, $user_id, $mod_name, $action, $desc, $last_run);
		$data_query->store_result();
		if($data_query->num_rows==1){
			$data_query->fetch();
		}else{
			$page->setStatus(404);
			return;
		}
	}else{
		$page->setStatus(404);
		return;
	}
}else{
	$page->setStatus(404);
	return;
}

// Get User data
$user_query = $mySQL_r->prepare("SELECT `ID`, CONCAT(`f_name`, ' ', `s_name`, ' (', `username`, ')') FROM `core_users` WHERE `p_group` BETWEEN 1 AND 2 ORDER BY `ID` ASC");
$user_query->execute();
$user_query->bind_result($ID,$name);
$user_data = array();
while($user_query->fetch()){
  $user_data[$ID] = $name;
}
$users = array();

foreach($user_data as $k=>$v){
	$users[$k]['v'] = $k;
	$users[$k]['n'] = $v;
	if($user_id==$k){
		$users[$k]['s'] = true;
	}else{
		$users[$k]['s'] = false;
	}
	$users[$k]['d'] = 0;
}

foreach(array('mins', 'hours', 'days', 'months', 'dow') as $var){
	if($$var === NULL) $$var = '*';
}
$closeBtn = array('a'=>array('t'=>'url', 'a'=>'../cron_view'), 'ic'=>'remove-sign');
$saveBtn = array('s'=>B_T_SUCCESS, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'cron_edit\', this, \'save\')'), 'ic'=>'floppy-disk');
$applyBtn = array('s'=>B_T_PRIMARY, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'cron_edit\', this, \'apply\')'), 'ic'=>'ok-sign');
$form = $page->getPlugin('form', array('cron_edit', WebApp::action('core', 'cron_edit', true), 'post'));

$form
	->setColumns(3, 6)
	->setIndent('    ')
	->addTextField(
		'Job ID',
		'id',
		$cron_id,
		array(),
		array(
			'ro'=>true,
		)
	)
	->addTextField(
		'Module',
		'module',
		$mod_name,
		array(),
		array(
			'ro'=>true,
		)
	)
	->addTextField(
		'Action',
		'action',
		$action.'()',
		array(),
		array(
			'ro'=>true,
		)
	)
	->addTextArea(
		'Description',
		'description',
		$desc,
		4,
		array('t'=>'Description of the job'),
		array(
			'ro'=>true
		)
	)
	->addSelect(
		'Run as user...',
		'user',
		$users,
		array('t'=>'The user to run the group as.'),
		array(
			'v'=>true,
			'vm'=>array(
				'selectRequiredMsg'=>array('m'=>'A User is required.','s'=>'danger')
			),
			'vo'=>'validateOn:["blur"]',
			'r'=>true
		)
	)
	->addTextField(
		'Last Run',
		'last_run',
		date(DATET_SHORT, strtotime($last_run)),
		array(
			't'=>'Last time the job was run'
		),
		array(
			'ro'=>true
		)
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
		array('t'=>'Is the job enabled? (Overrides time settings)')
	)
	->addTextField(
		'Minutes',
		'mins',
		$mins,
		array('t'=>'Minute of the hour to run job. Use * for every minute')
	)
	->addTextField(
		'Hours',
		'hours',
		$hours,
		array('t'=>'Hour of the day to run job. Use * for every hour')
	)
	->addTextField(
		'Days',
		'days',
		$days,
		array('t'=>'Day of the month to run job. Use * for every day')
	)
	->addTextField(
		'Months',
		'months',
		$months,
		array('t'=>'Month of the year to run job. Use * for every month')
	)
	->addTextField(
		'Day of Week',
		'DoW',
		$dow,
		array('t'=>'Day of the week to run job. Use * for every day')
	)
	->addBtnLine(array('close'=>$closeBtn, 'save'=>$saveBtn, 'apply'=>$applyBtn));
$form->build();
?>

<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">Edit Cron Job</h1>
    <?php print $form->getForm();?>
  </div>
</div>