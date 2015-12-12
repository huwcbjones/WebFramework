<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">Module Manager</h1>
<?php
$table = $page->getPlugin('table', array('modules'));
$table
	->setIndent(8)
	->addClass('table-bordered')
	->addClass('table-hover')
	->addClass('table-striped')
	->sort(true);
$thead = array();
	$thead[] = Table::addTHeadCell('ID');
	$thead[] = Table::addTHeadCell('Name');
	$thead[] = Table::addTHeadCell('Namespace');
	$thead[] = Table::addTHeadCell('Version', false);
	$thead[] = Table::addTHeadCell('Installed On');
	if($this->accessAdminPage(3)) $thead[] = Table::addTHeadCell('<abbr title="Backup">B</abbr>',false);
	if($this->accessAdminPage(2)) $thead[] = Table::addTHeadCell('<abbr title="Uninstall">U</abbr>',false);
	if($this->accessAdminPage(4)) $thead[] = Table::addTHeadCell('<abbr title="Details">D</abbr>',false);
$table->addHeader($thead);
$module_query = $mySQL_r->prepare("SELECT `module_id`, `name`, `namespace`, `version`, `install_date`, `backup`, `uninstall` FROM `core_modules` ORDER BY `uninstall` ASC, `module_id` ASC");
$module_query->execute();
$module_query->bind_result($mod_id, $name, $namespace, $version, $installed, $backup, $uninstall);
$module_query->store_result();	
while($module_query->fetch()){
	$row   = array();
	$row[] = Table::addCell($mod_id);
	$row[] = Table::addCell($name);
	$row[] = Table::addCell($namespace);
	$row[] = Table::addCell($version);
	$row[] = Table::addCell(date(DATET_SHORT, strtotime($installed)));
	if($backup==1&&$this->accessAdminPage(3)){
		$row[] = Table::addCell('<a href="#" onclick="processData(\'/action/modules/backup?m='.$mod_id.'\')"><span class="'.B_ICON.' '.B_ICON.'-export"></span></a>');
	}else{
		$row[] = Table::addCell('');
	}
	if($uninstall==1&&$this->accessAdminPage(2)){
		$row[] = Table::addCell('<a href="/action/modules/pre_uninstall?ns='.$namespace.'"><span class="text-danger '.B_ICON.' '.B_ICON.'-remove"></span></a>');
	}else{
		$row[] = Table::addCell('');
	}
	if($this->accessAdminPage(4)){
		$row[] = Table::addCell('<a href="/admin/modules/details/'.$namespace.'"><span class="'.B_ICON.' '.B_ICON.'-expand"></span></a>');
	}
	$table->addRow($row);
}

$table->build();
print $table->getTable();
?>
  </div>
</div>