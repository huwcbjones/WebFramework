<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">News &amp; Events Dashboard</h1>
    <div class="row placeholders">
      <div class="col-xs-6 col-sm-2"><a href="/admin/news/article_add?r=d">
        <img class="img-responsive" alt="Add Articles" src="/images/news/icon/article_add.png">
        <h4>Add Article</h4>
        <span class="text-muted">Add a new article</span>
      </a></div>
      <div class="col-xs-6 col-sm-2"><a href="/admin/news/user_view">
        <img class="img-responsive" alt="View Articles" src="/images/news/icon/article_view.png">
        <h4>View Articles</h4>
        <span class="text-muted">View all the article</span>
      </a></div>
      <div class="col-xs-6 col-sm-2"><a href="/admin/news/event_add?r=d">
        <img class="img-responsive" alt="Add Event" src="/images/news/icon/event_add.png">
        <h4>Add Event</h4>
        <span class="text-muted">Add a new event</span>
      </a></div>
      <div class="col-xs-6 col-sm-2"><a href="/admin/news/event_view">
        <img class="img-responsive" alt="View Events" src="/images/news/icon/event_view.png">
        <h4>View Events</h4>
        <span class="text-muted">View all the events</span>
      </a></div>
      <div class="col-xs-6 col-sm-2"><a href="/admin/news/newsl_add?r=d">
        <img class="img-responsive" alt="Add Newsletter" src="/images/news/icon/newsl_add.png">
        <h4>Add Newsletter</h4>
        <span class="text-muted">Add a new newsletters</span>
      </a></div>
      <div class="col-xs-6 col-sm-2"><a href="/admin/news/newsl_view">
        <img class="img-responsive" alt="View Newsletters" src="/images/news/icon/newsl_view.png">
        <h4>View Newsletters</h4>
        <span class="text-muted">View all the newsletters</span>
      </a></div>
    </div>
  </div>
</div>
<?php /*<div class="row">
  <div class="col-sm-6">
    <div class="row pane">
      <div class="col-xs-12">
        <h2 class="sub-header">Logged in Users</h2>
          <div class="table-responsive">
<?php
$users= $page->getPlugin('table', array('users'));
$users
	->setIndent('          ')
	->addClass('table-striped');
$thead = array();
$thead['n']		= Table::addTHeadCell('#');
$thead['Name']		= Table::addTHeadCell('Name');
$thead['ID']		= Table::addTHeadCell('ID');
$thead['Username']	= Table::addTHeadCell('Username');

$users->addHeader($thead);
$user_query = $mySQL_r->prepare("SELECT DISTINCT `core_sessions`.`user`, CONCAT(`core_users`.`f_name`, ' ', `core_users`.`s_name`), `core_users`.`username` FROM `core_sessions` INNER JOIN `core_users` ON `core_sessions`.`user`=`core_users`.`id` ORDER BY `core_sessions`.`lpr`");
$user_query->execute();
$user_query->bind_result($user_id, $name, $username);
$user_query->store_result();

$n = 1;
while($user_query->fetch()){
	$row['n']			= Table::addCell($n);
	$row['name']		= Table::addCell($name);
	$row['ID']			= Table::addCell($user_id);
	$row['username']	= Table::addCell($username);
	$users->addRow($row);
	unset($row);
	$n++;
}
$users->build();
print $users->getTable();
?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6">
    <div class="row pane">
      <div class="col-xs-12">
        <h2 class="sub-header">Primary Groups</h2>
<?php
$groups= $page->getPlugin('table', array('groups'));
$groups
	->setIndent('          ')
	->addClass('table-striped');
$thead = array();
$thead['ID']		= Table::addTHeadCell('ID');
$thead['Name']		= Table::addTHeadCell('Name');
$thead['Pages']		= Table::addTHeadCell('Pages');

$groups->addHeader($thead);

$group_query = $mySQL_r->prepare(
"
SELECT `core_groups`.`GID`, `name`, COUNT(`PID`)
FROM `core_groups`
LEFT JOIN `core_gpage` ON `core_gpage`.`GID`=`core_groups`.`GID`
WHERE `type`='p' GROUP BY `core_groups`.`GID`
ORDER BY `name` ASC
"
);
$group_query->execute();
$group_query->bind_result($group_id, $name, $pages);
$group_query->store_result();

while($group_query->fetch()){
	$row['ID']			= Table::addCell($group_id);
	$row['name']		= Table::addCell($name);
	$row['pages']		= Table::addCell($pages);
	$groups->addRow($row);
	unset($row);
}
$groups->build();
print $groups->getTable();
?>
      </div>
    </div>
  </div>
</div>*/ ?>