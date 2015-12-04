<div class="row pane">
  <div class="col-xs-12">
     <div class="row">
<?php

print('        <div class="col-lg-2 col-xs-4">'.PHP_EOL);
print('        </div>'.PHP_EOL);

// Delete Job BTN
print('        <div class="col-lg-2 col-xs-4">'.PHP_EOL);
/*if($this->inGroup(53, true)){
	print('          <button class="btn btn-xs btn-danger btn-block disabled job_need_check" onclick="delete_mod()">Delete&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-trash"></span></button>'.PHP_EOL);
}*/
print('        </div>'.PHP_EOL);

// Edit Job BTN
print('        <div class="col-lg-2 col-xs-4">'.PHP_EOL);
if($this->accessAdminPage(52)){
	print('          <button class="btn btn-xs btn-primary btn-block disabled" id="edit_btn">Edit&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-edit"></span></button>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

print('        <div class="col-lg-2 col-xs-4">'.PHP_EOL);
if($this->accessAdminPage(53)){
	print('          <a href="cron_log" class="btn btn-xs btn-info btn-block">Log&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-list"></span></a>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);
?>
    </div><br />
    <div class="row">
      <div class="col-xs-12">
<?php
$jobs = $page->getPlugin('table', array('jobs'));
$jobs
	->addClass('table-striped')
	->addClass('table-hover')
	->addClass('table-bordered')
	->setIndent('        ')
	->sort(true);
	
$thead = array();
if($this->accessAdminPage(52)||$this->inGroup(53, true)) $thead['selectAll'] = Table::addTHeadCell('<input type="checkbox" id="selectAll" />', false);
$thead['ID']		= Table::addTHeadCell('ID');
$thead['task']		= Table::addTHeadCell('Task');
$thead['enable']	= Table::addTHeadCell('<abbr title="Enabled">En</abbr>');
$thead['lastrun']	= Table::addTHeadCell('Last Run');
$thead['desc']		= Table::addTHeadCell('Description');

if($this->accessAdminPage(52)) $thead['edit'] = Table::addTHeadCell('Edit', false);
if($this->inGroup(54, true)) $thead['run'] = Table::addTHeadCell('Run', false);
$jobs->addHeader($thead);

$job_query = $mySQL_r->prepare(
"SELECT `core_cron`.`ID`, `enable`, `namespace`, `action`, `core_cron`.`description`, `last_run` FROM `core_cron`
LEFT JOIN `core_modules`
ON `core_modules`.`module_id` = `core_cron`.`mod_id`
ORDER BY `last_run` DESC");
$job_query->bind_result($id, $enable, $module, $action, $desc, $last_run);
$job_query->execute();
$job_query->store_result();

while($job_query->fetch()){
	if($this->accessAdminPage(52)||$this->inGroup(53, true)) $row['select']	= Table::addCell('<input class="jobs_check" type="checkbox" value="'.$id.'" name="job[]" />');
	$row['ID']		= Table::addCell($id);
	$row['action']	= Table::addCell($module.'::'.$action.'()', 'i_'.$id, '', true);
	$row['enable']	= Table::addCell(Form::toggleLink($this, $enable, '', '', array(
			's'=>array(
				'i'=>'ok',
			),
			'f'=>array(
				'i'=>'remove',
			)
		)
	));
	if($last_run===NULL){
		$last_run = 'Never';
	}else{
		$last_run = date(DATET_SHORT, strtotime($last_run));
	}
	$row['lastrun']	= Table::addCell($last_run, '', '', true);
	$row['desc']	= Table::addCell($desc);
	if($this->accessAdminPage(52)) $row['edit']	= Table::addCell('<a href="cron_edit/'.$id.'"><span class="'.B_ICON.' '.B_ICON.'-edit"></span></a>');
	if($this->inGroup(54, true)) $row['run']			= Table::addCell('<a href="/action/core/cron_run?j='.$id.'" onclick="processData(this.href);return false;"><span class="'.B_ICON.' '.B_ICON.'-play"></span></a>');

	$jobs->addRow($row);
}
$job_query->free_result();

$jobs->build();
print $jobs->getTable();
?>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
<?php
$check = new TableCheck();
$check
	->setType('job')
	->addRequireOne('#edit_btn')
	->addRequireOneBtn('#edit_btn', 'cron_edit')
	->create();
print $check->getScript();
?>
</script>
<?php
$delete_mod = $page->getPlugin('modalconf', array('delete', 'job', WebApp::action('core','cron_delete', true), 'post'));
$delete_mod->addDefaultConfig();
$delete_mod
	->setDefaultContent()
	->SetDefaultModal()
	->setRightBtn('danger','Delete','trash')
	->build();
print $delete_mod->getModal();
?>