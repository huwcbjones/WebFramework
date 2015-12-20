<?php
$page->createTitle();
print $page->getHeader();
if(!array_key_exists('s', $_GET)) $_GET['s'] = 0;
if(!array_key_exists('n', $_GET)) $_GET['n'] = 30;

?>
<div class="row pane">
  <div class="col-xs-12">
     <div class="row">
<?php
print('        <div class="col-md-2 col-xs-3">'.PHP_EOL);
print('          <a class="btn btn-xs btn-default btn-block" href="">Close&nbsp;&nbsp;&nbsp;<span class="' .B_ICON.' '.B_ICON.'-remove-sign"></span></a>'.PHP_EOL);
print('        </div>'.PHP_EOL);

if($user->inGroup(1)){
print('        <div class="col-md-2 col-xs-3">'.PHP_EOL);
print('          <a class="btn btn-xs btn-block btn-danger" href="#" id="events_del" onclick="delete_mod()">Delete Event(s)&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-trash"></span></a>'.PHP_EOL);
print('        </div>'.PHP_EOL);	
}
if($user->inGroup(1)){
print('        <div class="col-md-2 col-xs-3">'.PHP_EOL);
print('          <a class="btn btn-xs btn-block btn-danger" href="#" id="events_del" onclick="obliterate_mod()">Clear Event Log&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-trash"></span></a>'.PHP_EOL);
print('        </div>'.PHP_EOL);	
}
?>
    </div><br />
    <div class="row">
      <div class="col-xs-12">
<?php
$event_view = new Table('events');
$event_view->addClass('table-striped');
$event_view->addClass('table-hover');
$event_view->addClass('table-bordered');
$event_view->setIndent('        ');
$event_view->sort(true);
$event_view->pager(true);
$event_view->sticky(true);
$thead = array();
if($user->inGroup(1)) $thead['selectAll'] = Table::addTHeadCell('<input type="checkbox" id="selectAll" />', false);
$thead['ID']		= Table::addTHeadCell('ID');
$thead['Name']		= Table::addTHeadCell('Time');
$thead['Username']	= Table::addTHeadCell('User');
$thead['Email']		= Table::addTHeadCell('<abbr title="Client IP Address">IP</abbr>');
$thead['En']		= Table::addTHeadCell('<abbr title="Requested URI">URI</abbr>');
$thead['Act']		= Table::addTHeadCell('Event');
$event_view->addHeader($thead);

$event_query = $mySQL['r']->prepare("SELECT `id`, `time`,`uri`,`user_id`,`user_ip`,`event` FROM `evt_log` ORDER BY `time` DESC LIMIT ?, ?");
$event_query->bind_param('ii', $_GET['s'], $_GET['n']);
$event_query->bind_result($event_id, $time, $uri, $user_id ,$user_ip, $event);
$event_query->execute();
$event_query->store_result();

$user_id = '';
$user_query = $mySQL['r']->prepare("SELECT `name` FROM `core_users` WHERE `id`=?");
$user_query->bind_param('i',$user_id);
while($event_query->fetch()){
	$user_query->bind_result($username);
	$user_query->execute();
	$user_query->store_result();
	$user_query->fetch();
	$user_query->free_result();
	if($user->inGroup(1)) $row['select']	= Table::addCell('<input class="events_check" type="checkbox" value="'.$event_id.'" name="event[]" />');
	$row['ID']		= Table::addCell(str_pad($event_id, 4, 0, STR_PAD_LEFT), 'i_'.$event_id, '', '', true);
	$row['time']	= Table::addCell(date("d/m/Y H:i:s", strtotime($time)), '', '', true);
	$row['user_id']	= Table::addCell($username, '', '', true);
	$row['user_ip']	= Table::addCell($user_ip, '', '', true);
	$row['uri']		= Table::addCell($uri, '', '', true);
	$row['event']	= Table::addCell($event);
	$event_view->addRow($row);
}
$event_query->free_result();

$event_view->build();
print $event_view->getTable();
?>
        <script type="text/javascript">
        $(function() {
			$("#events_del").addClass("disabled");
			$("#selectAll").click(function(){
				$(".events_check").prop("checked", this.checked);
				if(this.checked){
					$("#events_del").removeClass("disabled");
				}else{
					$("#events_del").addClass("disabled");
				}
			});
			$(".events_check").change(function(){
				var events_check = $(".events_check");
				var check = (events_check.filter(":checked").length == events_check.length);
				$('#selectAll').prop("checked", check);
				if(events_check.filter(":checked").length>0){
					$("#events_del").removeClass("disabled");
				}else{
					$("#events_del").addClass("disabled");
				}
			});
        })
        </script>
      </div>
    </div>
  </div>
</div>

<?php
$confs = new FormExtras();
if($user->inGroup(1)) $confs->addDeleteConfModal('events','/act/eventlog_del');
if($user->inGroup(1)) $confs->addCustomConfModal('obliterate', 'trash', 'events', '/act/eventlog_clear', 'Are you sure you want to obliterate the event log?', false, true);
$confs->build();
print $confs->getExtra();

?>