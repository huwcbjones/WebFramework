<?php
$module_query = $mySQL_r->prepare("SELECT `module_id`, `name`, `namespace`, `version`, `install_date`, `author`, `authorUrl`, `description`, `backup`, `uninstall` FROM `core_modules` WHERE `namespace`=?");
if($module_query!==false){
	$namespace = WebApp::get('cat4');
	$module_query->bind_param('s', $namespace);
	$module_query->execute();
	$module_query->store_result();
	if($module_query->num_rows==1){
		$module_query->bind_result($module_id, $name, $namespace, $version, $installDate, $author, $authorUrl, $description, $backup, $uninstall);
		while($module_query->fetch()){
			$page->setTitle($name.': Details');
			$backup = ($backup==1)?'Yes':'No';
			$uninstall = ($uninstall==1)?'Yes':'No';
		}
	}else{
		$page->setStatus(404);
	}
}else{
	$page->setStatus(500);
}
?>
<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header"><?php print $name ?> Module</h1>
<?php
$form = $page->getPlugin('Form', array('module_details', WebApp::action('','',false), '', ''));

$form
	->setColumns(3, 9)
	->addTextField('Module ID', 'id', $module_id, array('t'=>'The ID of the module'),array('ro'=>true))
	->addTextField('Installed', 'installed', date(DATET_SHORT, strtotime($installDate)), array('t'=>'The date/time the module was installed'),array('ro'=>true))
	->addTextField('Name', 'name', $name, array('t'=>'The name of the module'),array('ro'=>true))
	->addTextField('Namespace', 'namespace', $namespace, array('t'=>'The namespace of the module'),array('ro'=>true))
	->addTextField('Version', 'version', $version, array('t'=>'The version of the module'),array('ro'=>true))
	->addTextArea('Description', 'desc', $description, 3, array('t'=>'Description of the module'),array('ro'=>true))
	->addTextField('Author', 'author', $author, array('t'=>'The module\'s author'),array('ro'=>true))
	->addTextField('Author\'s Website', 'authorUrl', $authorUrl, array('t'=>'The website of the author'),array('ro'=>true))
	->addTextField('Backup', 'backup', $backup, array('t'=>'Can the module be backed up?'),array('ro'=>true))
	->addTextField('Uninstall', 'uninstall', $uninstall, array('t'=>'Can the module be uninstalled?'),array('ro'=>true));
$btns = array();
if($this->inGroup(1)){
	$btns['Uninstall'] = array('s'=>B_T_FAIL, 'a'=>array('t'=>'url', 'a'=>'/action/modules/pre_uninstall?ns='.$namespace, 'oc'=>''), 'ic'=>'remove');
}
if($this->accessAdminPage(5)){
	$btns['Update'] = array('s'=>B_T_PRIMARY, 'a'=>array('t'=>'url', 'a'=>'/admin/modules/update/'.$namespace, 'oc'=>''), 'ic'=>'play-circle');
}

$form->addBtnLine($btns);
$form->build();
print $form->getForm();
?>
  </div>
</div>