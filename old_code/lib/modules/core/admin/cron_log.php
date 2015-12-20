<?php
$start_record = (WebApp::get('s')===NULL)? 0 : WebApp::get('s');
$record_length = (WebApp::get('n')===NULL)? 30 : WebApp::get('n');
?>
<div class="row pane">
  <div class="col-xs-12">
     <div class="row">
<?php
if($this->inGroup(1)){
print('        <div class="col-md-2 col-xs-3">'.PHP_EOL);
print('          <a class="btn btn-xs btn-block btn-danger disabled" href="#" id="event_delete_btn" onclick="delete_mod()">Delete Event(s)&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-trash"></span></a>'.PHP_EOL);
print('        </div>'.PHP_EOL);	
}
if($this->inGroup(1)){
print('        <div class="col-md-2 col-xs-3">'.PHP_EOL);
print('          <a class="btn btn-xs btn-block btn-danger" href="#" onclick="clear_mod()">Clear Event Log&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-trash"></span></a>'.PHP_EOL);
print('        </div>'.PHP_EOL);	
}

print('        <div class="col-md-2 col-xs-3">'.PHP_EOL);
if($record_length>30){
print('          <a class="btn btn-xs btn-block btn-info" href="?s='.$start_record.'&amp;n='.($record_length-30).'">Show Less&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-minus"></span></a>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

print('        <div class="col-md-2 col-xs-3">'.PHP_EOL);
print('          <a class="btn btn-xs btn-block btn-info" href="?s='.$start_record.'&amp;n='.($record_length+30).'">Show More&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-plus"></span></a>'.PHP_EOL);
print('        </div>'.PHP_EOL);	

print('        <div class="col-md-2 col-xs-3">'.PHP_EOL);
if($start_record>30){
print('          <a class="btn btn-xs btn-block btn-default" href="?s='.($start_record-$record_length).'&amp;n='.($record_length).'"><span class="'.B_ICON.' '.B_ICON.'-chevron-left"></span>&nbsp;&nbsp;&nbsp;Previous Page</a>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

print('        <div class="col-md-2 col-xs-3">'.PHP_EOL);
print('          <a class="btn btn-xs btn-block btn-default" href="?s='.($start_record+$record_length).'&amp;n='.($record_length).'">Next Page&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-chevron-right"></span></a>'.PHP_EOL);
print('        </div>'.PHP_EOL);	
?>
    </div><br />
    <div class="row">
      <div class="col-xs-12">
<?php
$event_view = $page->getPlugin('table', array('events'));
$event_view
	->addClass('table-striped')
	->addClass('table-hover')
	->addClass('table-bordered')
	->setIndent('        ')
	->responsive(true)
	->sort(true)
	->sticky(true);
	
$thead = array();
if($this->inGroup(1)) $thead['selectAll'] = Table::addTHeadCell('<input type="checkbox" id="selectAll" />', false);
$thead['ID']		= Table::addTHeadCell('ID');
$thead['time']		= Table::addTHeadCell('Time');
$thead['user']		= Table::addTHeadCell('User');
$thead['job']		= Table::addTHeadCell('Job');
$thead['event']		= Table::addTHeadCell('Event');
$event_view->addHeader($thead);

$event_query = $mySQL_r->prepare(
"SELECT `core_cronlog`.`id`, `time`, CONCAT(`f_name`, ' ', `s_name`, ' (', `username`, ')'), `job_id`, CONCAT(`name`, '::', `action`), `event` FROM `core_cronlog`
LEFT JOIN `core_users` ON `user_id`=`core_users`.`id`
LEFT JOIN `core_cron` ON `job_id`=`core_cron`.`id`
LEFT JOIN `core_modules` ON `mod_id`=`module_id`
ORDER BY `time` DESC LIMIT ?, ?"
);
if($event_query!==false){
	$event_query->bind_param('ii', $start_record, $record_length);
	$event_query->bind_result($event_id, $time, $username , $jobID, $job, $event);
	$event_query->execute();
	$event_query->store_result();

	while($event_query->fetch()){
		if($this->inGroup(1)) $row['select']	= Table::addCell('<input class="events_check" type="checkbox" value="'.$event_id.'" name="cron[]" />');
		$row['ID']		= Table::addCell(str_pad($event_id, 4, 0, STR_PAD_LEFT), 'i_'.$event_id, '', '', true);
		$row['time']	= Table::addCell(date(DATET_SHORT, strtotime($time)), '', '', true);
		$row['user_id']	= Table::addCell($username, '', '', true);
		if($this->accessAdminPage(52)){
			$row['job']		= Table::addCell('<a href="cron_edit/'.$jobID.'" target="_blank" class="bstooltip" data-title="Click to edit job">'.$job.'</a>', '', '', true);
		}else{
			$row['job']		= Table::addCell($job, '', '', true);
		}
		$row['event']		= Table::addCell($event);
		$event_view->addRow($row);
	}
	$event_query->free_result();
}

$event_view->build();
print $event_view->getTable();
?>
<script type="text/javascript">
<?php
$check = new TableCheck();
$check
	->setType('event')
	->addRequire('#event_delete_btn')
	->create();
print $check->getScript();
?>
        </script>
      </div>
    </div>
  </div>
</div>

<?php
if($user->inGroup(1)){
	$delete_modal = $page->getPlugin('modalconf',  array('delete', 'event', WebApp::action('core','cron_logdelete', true), 'post'));
	$delete_modal
		->addDefaultConfig()
		->setDefaultContent()
		->setDefaultModal()
		->setRightBtn('warning','Delete','trash')
		->build();
	print $delete_modal->getModal();
	
	$obliterate = $page->getPlugin('modalconf',  array('clear', 'event', WebApp::action('core','cron_logclear', true), 'post'));
	$obliterate
		->addForm()
		->form->build();
	$obliterate
		->setContent('<p>Are you sure you want to clear the cron log?</p>'.$obliterate->form->getForm())
		->setDefaultModal()
		->setRightBtn('danger','Clear','trash')
		->addShowScript()
		->build();
	print $obliterate->getModal();
}
?>