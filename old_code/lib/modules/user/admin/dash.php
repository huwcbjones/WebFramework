<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">Users &amp; Groups Dashboard</h1>
    <div class="row placeholders">
<?php if($this->accessAdminPage(2)){ ?>
      <div class="col-xs-6 col-sm-3"><a href="/admin/user/user_add?r=d">
        <img class="img-responsive" alt="New User" src="/images/user/icon/user_add.png">
        <h4>New User</h4>
        <span class="text-muted">Add a new user</span>
      </a></div>
<?php } ?>
<?php if($this->accessAdminPage(1)){ ?>
      <div class="col-xs-6 col-sm-3"><a href="/admin/user/user_view">
        <img class="img-responsive" alt="View Users" src="/images/user/icon/user_view.png">
        <h4>View Users</h4>
        <span class="text-muted">View all the users</span>
      </a></div>
<?php } ?>
<?php if($this->accessAdminPage(12)){ ?>
      <div class="col-xs-6 col-sm-3"><a href="/admin/user/group_add?r=d">
        <img class="img-responsive" alt="New Group" src="/images/user/icon/group_add.png">
        <h4>New Group</h4>
        <span class="text-muted">Add a new group</span>
      </a></div>
<?php } ?>
<?php if($this->accessAdminPage(11)){ ?>
      <div class="col-xs-6 col-sm-3"><a href="/admin/user/group_view">
        <img class="img-responsive" alt="View Groups" src="/images/user/icon/group_view.png">
        <h4>View Groups</h4>
        <span class="text-muted">View all the groups</span>
      </a></div>
<?php } ?>
    </div>
  </div>
</div>
<div class="row">
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
<?php if($this->accessAdminPage(11)||$this->accessAdminPage(12)||$this->accessAdminPage(13)){ ?>
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
<?php } ?>
</div>