<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">Backup Modules</h1>
<?php
$table = $page->getPlugin('table', array('modules'));
$table
	->setIndent(8)
	->addClass('table-bordered')
	->addClass('table-hover')
	->addClass('table-striped')
	->sort(true);
$table->addHeader(array(
	Table::addTHeadCell('ID'),
	Table::addTHeadCell('Name'),
	Table::addTHeadCell('Namespace'),
	Table::addTHeadCell('Installed On'),
	Table::addTHeadCell('', false)
));
if($this->inGroup(1)){
	$module_query = $mySQL_r->prepare("SELECT `module_id`, `name`, `namespace`, `install_date` FROM `core_modules` ORDER BY `install_date` DESC");
}else{
	$module_query = $mySQL_r->prepare("SELECT `module_id`, `name`, `namespace`, `install_date` FROM `core_modules` WHERE `uninstall`='1' ORDER BY `install_date` DESC");
}
$module_query->execute();
$module_query->bind_result($mod_id, $name, $namespace, $installed);
$module_query->store_result();
while($module_query->fetch()){
	$row   = array();
	$row[] = Table::addCell($mod_id);
	$row[] = Table::addCell($name);
	$row[] = Table::addCell($namespace);
	$row[] = Table::addCell(date(DATET_SHORT, strtotime($installed)));
	$row[] = Table::addCell('<a href="#" onclick="processData(\'/action/modules/backup?m='.$mod_id.'\')">Backup</a>');
	$table->addRow($row);
}

$table->build();
print $table->getTable();
?>
  </div>
</div>