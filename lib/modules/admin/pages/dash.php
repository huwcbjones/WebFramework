<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">Modules</h1>
    <div class="row">
<?php
$module_query = $mySQL_r->prepare(
"SELECT `core_admin`.`ID`, `core_pages`.`ID`, `core_pages`.`title`, `core_pages`.`cat2` FROM `core_admin`
INNER JOIN `core_pages` ON `core_admin`.`PID`=`core_pages`.`id`
WHERE `parent` IS NULL
ORDER BY `core_pages`.`title` ASC");
$module_query->bind_result($p_ID, $p_PID, $p_title, $p_cat2);
$module_query->execute();

$tile_number['xs'] = 0;
$tile_number['sm'] = 0;
$tile_number['md'] = 0;
$tile_number['lg'] = 0;

while($module_query->fetch()){
	if($p_cat2!==NULL && $user->can_accessPage($p_PID)){
		print '     <div class="col-xs-4 col-sm-3 col-md-2"><a href="/admin/'.$p_cat2.'">'.PHP_EOL;
		print '      <img src="/images/'.$p_cat2.'/icon.png" class="img-responsive" alt="'.$p_title.'">'.PHP_EOL;
		print '      <h4 class="text-center">'.$p_title.'</h4>'.PHP_EOL;
		print '    </a></div>'.PHP_EOL;
		$tile_number['xs'] += 4;
		$tile_number['sm'] += 3;
		$tile_number['md'] += 2;
		$tile_number['lg'] += 2;
	}
	foreach($tile_number as $type=>$tile){
		if($tile >= 12){
			print '     <div class="clearfix visible-'.$type.'"></div>'.PHP_EOL;
			$tile_number[$type] = 0;
		}
	}
}
?>
    </div>
  </div>
</div>
<div class="row pane">
  <div class="col-xs-12">
    <h2 class="sub-header">Logged In Users</h2>
<?php
$users = $page->getPlugin('table', array('users'));
$users
	->setIndent('          ')
	->addClass('table-striped');
$thead = array();
$thead['n']			= Table::addTHeadCell('#');
$thead['Name']		= Table::addTHeadCell('Name');
$thead['Group']		= Table::addTHeadCell('Group');
$thead['Username']	= Table::addTHeadCell('Username');
$thead['last']		= Table::addTHeadCell('Last Activity');
if($page->inGroup(2020)){
	$thead['logout']	= Table::addTHeadCell('Logout');
}
$users->addHeader($thead);
$user_query = $mySQL_r->prepare(
"SELECT `user`, CONCAT(`f_name`, ' ', `s_name`), `core_groups`.`name`, `username`, MAX(`lpr`) FROM `core_sessions`
INNER JOIN `core_users` ON `user`=`core_users`.`id`
LEFT JOIN `core_groups` ON `p_group`=`GID`
GROUP BY `user` ORDER BY `lpr`");
$user_query->execute();
$user_query->bind_result($user_id, $name, $group, $username, $lpr);
$user_query->store_result();

$n = 1;
while($user_query->fetch()){
	$row = array();
	$row['n']		= Table::addCell($n);
	$row['name']	= Table::addCell($name);
	$row['group']	= Table::addCell($group);
	$row['user']	= Table::addCell($username);
	$row['last']	= Table::addCell(date(DATET_SHORT, strtotime($lpr)));
	if($page->inGroup(2020)){
		$row['logout']	= Table::addTHeadCell('<a href="#" onclick="processData(\'/action/user/session_destroym/'.$user_id.'\')"><span class="'.B_ICON.' '.B_ICON.'-log-out"</a>');
	}
	$users->addRow($row);
	$n++;
}
$users->build();
print $users->getTable();
?>
    </div>
</div>
