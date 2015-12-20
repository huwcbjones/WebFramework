    <div class="col-sm-3 col-md-2 sidebar">
<?php

$parent_items = $mySQL_r->prepare(
"SELECT `core_admin`.`ID`, `core_pages`.`ID`, `core_pages`.`title`, `core_pages`.`cat2`, `core_pages`.`cat3`
FROM `core_admin`
INNER JOIN `core_pages` ON `core_admin`.`PID`=`core_pages`.`id`
INNER JOIN `core_modules` ON `core_admin`.`module_id`=`core_modules`.`module_id`
WHERE `parent` IS NULL
ORDER BY `namespace` ASC, `core_admin`.`ID` ASC");

$child_items = $mySQL_r->prepare(
"SELECT `core_admin`.`ID`, `core_pages`.`ID`, `core_pages`.`title`, `core_pages`.`cat2`, `core_pages`.`cat3`
FROM `core_admin`
INNER JOIN `core_pages` ON `core_admin`.`PID`=`core_pages`.`id`
WHERE `parent`=?
ORDER BY `core_admin`.`ID` ASC");
if($parent_items === false || $child_items === false){
	
}
$parent_items->execute();
$parent_items->store_result();
$page->parent->debug($this::name_space.': There are '.$parent_items->num_rows.' admin menu items');
if($parent_items->num_rows!=0){
	$parent_items->bind_result($p_ID, $p_PID, $p_title, $p_cat2, $p_cat3);
	while($parent_items->fetch()){
		// Create parent menu Item
		if($user->can_accessPage($p_PID)){
			$menu = '      <ul class="nav nav-sidebar">'.PHP_EOL;
			if(
				(
					$p_cat2==WebApp::get('cat2')
					||(
						$p_cat2=='admin' // Admin menu nav hack
						&&WebApp::get('cat2')===NULL
					)
				)
				&&$p_cat3==WebApp::get('cat3')
			){
				$menu.= '        <li class="active">';
			}else{
					$menu.= '        <li>';
			}
			$url = array();
			if($p_cat2!='admin'){
				$url[1] = 'admin';
			}
			$url[2] = $p_cat2;
			$url[3] = $p_cat3;
			
			$menu.= '<a href="'. urlimplode($url).'">'.$p_title.'</a></li>'.PHP_EOL;
	
			// Create children items
			if($p_cat2==WebApp::get('cat2')){
				$child_items->bind_param('i', $p_ID);
				$child_items->bind_result($c_ID, $c_PID, $c_title, $c_cat2, $c_cat3);
				$child_items->execute();
				$child_items->store_result();
				if($child_items->num_rows!=0){
					while($child_items->fetch()){
						if($user->can_accessPage($c_PID)){
							if(
								$c_cat2==WebApp::get('cat2')
								&&$c_cat3==WebApp::get('cat3')
							){
								$menu.= '        <li class="active">';
							}else{
								$menu.= '        <li>';
							}
							
							$menu.= '<a href="/admin/'.$c_cat2.'/'.$c_cat3.'">'.$c_title.'</a></li>'.PHP_EOL;
						}
					}
				}
			}
			$menu.= '      </ul>'.PHP_EOL;
			print $menu;
			$child_items->free_result();
		}
	}
}
print '      <ul class="nav nav-sidebar">'.PHP_EOL;
print '        <li><a class="text-muted" href="#" onclick="$(\'#logout_users\').modal(\'show\')">Log Out&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' 
'.B_ICON.'-log-out"></span></a></li>'.PHP_EOL;
print '      </ul>'.PHP_EOL;
?>
    </div>
<?php
$logout_conf = $page->getPlugin('modalconf', array('logout', 'user', WebApp::action('user', 'logout', false), 'post'));
$logout_conf->addDefaultConfig();
$logout_conf
//	->setDefaultContent()
	->setDefaultModal()
	->setContent('<p>Are you sure you want to logout?</p>')
	->setDefaultModal()
	->setTitle('Logout?')
	->setRightBtn('primary', 'Logout', 'log-out', 'button', 'document.location=\'/action/user/logout\'')
	->build();
print $logout_conf->getModal();
?>
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
      <div class="row">
        <div class="col-xs-12">
          <div id="alert_working" class="hidden">
<?php
$working = $page->getPlugin('alert');
$working->setAlert('Processing...', B_T_INFO, 'working', false);
print $working->getAlert();
?>
          </div>
        <div id="status_bar"></div>
        <?php
		foreach(Session::getAll('status_msg') as $id=>$message){
			print $message.PHP_EOL;
			Session::del('status_msg',$id);
		}
        ?>

