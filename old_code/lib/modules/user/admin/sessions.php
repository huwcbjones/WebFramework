<div class="row pane">
  <div class="col-xs-12">
     <div class="row">
<?php
print('        <div class="col-md-2 col-xs-3">'.PHP_EOL);
print('          <a class="btn btn-xs btn-default btn-block" href="">Close&nbsp;&nbsp;&nbsp;<span class="' .B_ICON.' '.B_ICON.'-remove-sign"></span></a>'.PHP_EOL);
print('        </div>'.PHP_EOL);

print('        <div class="col-md-2 col-xs-3">'.PHP_EOL);
print('        </div>'.PHP_EOL);

print('        <div class="col-md-2 col-xs-3">'.PHP_EOL);
print('          <button class="btn btn-xs btn-danger btn-block disabled session_need_check" onclick="destroy_mod()">Destroy&nbsp;&nbsp;&nbsp;<span 
class="'.B_ICON.' '.B_ICON.'-remove-sign"></span></button>'.PHP_EOL);
print('        </div>'.PHP_EOL);

print('        <div class="col-md-2 col-xs-3">'.PHP_EOL);
print('          <button class="btn btn-xs btn-primary btn-block disabled session_need_check" onclick="lock_mod()">Lock&nbsp;&nbsp;&nbsp;<span 
class="'.B_ICON.' '.B_ICON.'-lock"></span></button>'.PHP_EOL);
print('        </div>'.PHP_EOL);

?>

    </div><br />
    <div class="row">
      <div class="col-xs-12">
<?php
$session_view= $page->getPlugin('table', array('sessions'));
$session_view
	->setIndent('        ')
	->addClass('table-bordered')
	->addClass('table-hover')
	->addClass('table-striped')
	->sort(true)
	->sticky(true);
$thead = array();
$thead['selectAll']	= Table::addTHeadCell('<input type="checkbox" id="selectAll" />', '', false);
$thead['ID']		= Table::addTHeadCell('ID');
$thead['Name']		= Table::addTHeadCell('Name');
$thead['Username']	= Table::addTHeadCell('Username');
$thead['ip']		= Table::addTHeadCell('IP');
$thead['created']	= Table::addTHeadCell('Created');
$thead['lpr']		= Table::addTHeadCell('Last Page Request');
$thead['del']		= Table::addTHeadCell('Destroy', '', false);
$thead['lock']		= Table::addTHeadCell('Lock', '', false);
$session_view->addHeader($thead);

$session_query = $mySQL_r->prepare("SELECT `core_sessions`.`id`, CONCAT(`core_users`.`f_name`, ' ', `core_users`.`s_name`), `core_users`.`username`, INET_NTOA(`ip`), `created`, `lpr` FROM `core_sessions` INNER JOIN `core_users` ON `core_sessions`.`user`=`core_users`.`id` ORDER BY `core_users`.`id` ASC, `core_sessions`.`lpr` DESC");
$session_query->execute();
$session_query->bind_result($session_id, $name,$username,$ip,$created,$lpr);
$session_query->store_result();
	
while($session_query->fetch()){
	$row['check']		= Table::addCell('<input class="sessions_check" type="checkbox" value="'.$session_id.'" name="session[]" />');
	$row['ID']		= Table::addCell($session_id, 'i_'.$session_id);
	$row['name']		= Table::addCell($name);
	$row['username']	= Table::addCell($username);
	$row['ip']		= Table::addCell($ip);
	$row['created']		= Table::addCell(date(DATET_SHORT, strtotime($created)));
	$row['lpr']		= Table::addCell(date(DATET_SHORT, strtotime($lpr)));
	$row['del']		= Table::addCell('<a href="#" onclick="processData(\'/action/user/session_destroy/'.$session_id.'\')"><span class="'.B_ICON.' '.B_ICON.'-remove-sign"</a>');
	$row['lock']		= Table::addCell('<a href="#" onclick="processData(\'/action/user/session_lock/'.$session_id.'\')"><span class="'.B_ICON.' '.B_ICON.'-lock"</a>');
	$session_view->addRow($row);
}

$session_view->build();
print $session_view->getTable();
?>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
<?php
$check = new TableCheck;
$check
	->setType('session')
	->create();
print $check->getScript();
?>
</script>
<?php
$destroy_mod = $page->getPlugin('modalconf', array('destroy', 'session', WebApp::action('user', 'session_destroy?m=m', true), 'post'));
$destroy_mod->addDefaultConfig();
$destroy_mod
	->setDefaultContent()
	->setDefaultModal()
	->setRightBtn('danger', 'Destroy', 'remove-sign')
	->build();
print $destroy_mod->getModal();

$lock_mod = $page->getPlugin('modalconf', array('lock', 'session', WebApp::action('user', 'session_lock?m=m', true), 'post'));
$lock_mod->addDefaultConfig();
$lock_mod
	->setDefaultContent()
	->setDefaultModal()
	->setRightBtn('danger', 'Lock', 'lock')
	->build();
print $lock_mod->getModal();
?>
