<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">System Dashboard</h1>
    <div class="row placeholders">
<?php if($this->accessAdminPage(1)){ ?>
      <div class="col-xs-6 col-sm-2"><a href="/admin/core/event_log">
        <img class="img-responsive" alt="Event Log" src="/images/core/icon/event_log.png">
        <h4>Event Log</h4>
        <span class="text-muted">View the event log</span>
      </a></div>
<?php } ?>
<?php if($this->accessAdminPage(40)){ ?>
      <div class="col-xs-6 col-sm-2"><a href="/admin/core/ipblock_view">
        <img class="img-responsive" alt="Block IPs" src="/images/core/icon/ip_block.png">
        <h4>Block IPs</h4>
        <span class="text-muted">View blocked IP addresses</span>
      </a></div>
<?php } ?>
<?php if($this->accessAdminPage(50)){ ?>
      <div class="col-xs-6 col-sm-2"><a href="/admin/core/cron_view">
        <img class="img-responsive" alt="Cron Jobs" src="/images/core/icon/cron.png">
        <h4>Cron Jobs</h4>
        <span class="text-muted">View cron jobs</span>
      </a></div>
<?php } ?>
<?php if($this->accessAdminPage(10)){ ?>
      <div class="col-xs-6 col-sm-2"><a href="/admin/core/option_view">
        <img class="img-responsive" alt="System Options" src="/images/core/icon/system_options.png">
        <h4>System Options</h4>
        <span class="text-muted">View the system options</span>
      </a></div>
<?php } ?>
<?php if($this->accessAdminPage(20)){ ?>
      <div class="col-xs-6 col-sm-2"><a href="/admin/core/config_view">
        <img class="img-responsive" alt="System Config" src="/images/core/icon/system_config.png">
        <h4>System Config</h4>
        <span class="text-muted">View the system config</span>
      </a></div>
<?php } ?>
<?php if($this->accessAdminPage(30)){ ?>
      <div class="col-xs-6 col-sm-2"><a href="/admin/core/system_info">
        <img class="img-responsive" alt="System Info" src="/images/core/icon/system_info.png">
        <h4>System Info</h4>
        <span class="text-muted">View the system info</span>
      </a></div>
<?php } ?>
    </div>
  </div>
</div>
<div class="row">
<?php if($this->accessAdminPage(1)){ ?>
  <div class="col-sm-6">
    <div class="row pane">
      <div class="col-xs-12">
        <h2 class="sub-header">Recent Events</h2>
          <div class="table-responsive">
<?php
$events = $page->getPlugin('table', array('events'));
$events
	->setIndent('          ')
	->addClass('table-striped');
$thead = array();
$thead['n']			= Table::addTHeadCell('#');
$thead['username']	= Table::addTHeadCell('Username');
$thead['event']		= Table::addTHeadCell('Event');

$events->addHeader($thead);
$event_query = $mySQL_r->prepare(
"SELECT `core_log`.`id`, `username`, `event` FROM `core_log`
LEFT JOIN `core_users` ON `user_id`=`core_users`.`id`
ORDER BY `time` DESC LIMIT 0, 10");
$event_query->execute();
$event_query->bind_result($event_id, $username, $event);
$event_query->store_result();

while($event_query->fetch()){
	$row = array();
	$row['n']			= Table::addCell($event_id);
	$row['username']	= Table::addCell($username);
	$row['event']		= Table::addCell($event);
	$events->addRow($row);
}
$events->build();
print $events->getTable();
?>
        </div>
      </div>
    </div>
  </div>
<?php } ?>
<?php if($this->accessAdminPage(8)){ ?>
  <div class="col-sm-6">
    <div class="row pane">
      <div class="col-xs-12">
        <h2 class="sub-header">Latest Blocked IPs</h2>
<?php
$ips = $page->getPlugin('table', array('ips'));
$ips
	->setIndent('          ')
	->addClass('table-striped');
$thead = array();
$thead['n']			= Table::addTHeadCell('#');
$thead['ip']		= Table::addTHeadCell('IP');
$thead['until']		= Table::addTHeadCell('Until');

$ips->addHeader($thead);

$ip_query = $mySQL_r->prepare(
"
SELECT `id`, INET_NTOA(`IP`), DATE_ADD(`time`, INTERVAL `length` DAY)
FROM `core_ip`
ORDER BY `time` DESC
"
);
$ip_query->execute();
$ip_query->bind_result($ban_id, $ip, $until);
$ip_query->store_result();

while($ip_query->fetch()){
	$row = array();
	$row['ID']			= Table::addCell($ban_id);
	$row['ip']			= Table::addCell($ip);
	$row['until']		= Table::addCell(date(DATET_LONG, strtotime($until)));
	$ips->addRow($row);
}
$ips->build();
print $ips->getTable();
?>
      </div>
    </div>
  </div>
<?php } ?>
</div>