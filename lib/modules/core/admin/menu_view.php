<div class="row pane">
  <div class="col-xs-12">
     <div class="row">
<?php
// Add BLock BTN
print('        <div class="col-lg-2 col-xs-4">'.PHP_EOL);
if($this->accessAdminPage(61)){
	print('          <a class="btn btn-xs btn-block btn-success" href="menu_add">New&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-plus-sign"></span></a>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

/*// Delete Option BTN
print('        <div class="col-lg-2 col-xs-4">'.PHP_EOL);
if($this->inGroup(43, true)){
	print('          <button class="btn btn-xs btn-danger btn-block disabled block_need_check" onclick="delete_mod()">Delete&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-trash"></span></button>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

// Edit Option BTN
print('        <div class="col-lg-2 col-xs-4">'.PHP_EOL);
if($this->accessAdminPage(42)){
	print('          <button class="btn btn-xs btn-primary btn-block disabled" id="edit_btn">Edit&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-edit"></span></button>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);*/

?>
    </div><br />
    <div class="row">
      <div class="col-xs-12">
<?php
$menu = $page->getPlugin('table', array('menu'));
$menu
	->addClass('table-striped')
	->addClass('table-hover')
	->addClass('table-bordered')
	->setIndent('        ');
	
$thead = array();
$thead['pos']		= Table::addTHeadCell('Position');
$thead['ID']		= Table::addTHeadCell('ID');
$thead['Page']		= Table::addTHeadCell('PID');
$thead['title']		= Table::addTHeadCell('Title');
$thead['dropdown']	= Table::addTHeadCell('Dropdown');
if($this->accessAdminPage(63)) $thead['up'] = Table::addTHeadCell('<abbr title="Move item up">U</abbr>');
if($this->accessAdminPage(63)) $thead['down'] = Table::addTHeadCell('<abbr title="Move item down">D</abbr>');
if($this->accessAdminPage(63)) $thead['edit'] = Table::addTHeadCell('Edit');
if($this->accessAdminPage(63)) $thead['delete'] = Table::addTHeadCell('Delete');
$menu->addHeader($thead);

$menu_query = $mySQL_r->prepare(
"SELECT `MID`, `position`, `PID`, `title`, `dropdown`, `divider`
FROM `core_menu`
LEFT JOIN `core_pages`
ON `core_pages`.`id`=`PID`
WHERE `parent`<=>NULL
ORDER BY `position` ASC");
$menu_query->bind_result($MID, $position, $PID, $title, $dropdown, $divider);
$menu_query->execute();
$menu_query->store_result();

$rown = 1;
while($menu_query->fetch()){
	$row['pos']			= Table::addCell($position);
	$row['ID']			= Table::addCell($MID);
	$row['page']		= Table::addCell($PID);
	$row['title']		= Table::addCell($title);
	$row['dropdown']	= Table::addCell(Form::toggleLink($this, $dropdown, '', '', array(
			's'=>array(
				'i'=>'ok',
			),
			'f'=>array(
				'i'=>'remove',
			)
		)
	));
	if($this->accessAdminPage(42)){
		if($rown == 1){
			$row['up']		= Table::addCell('');
		}else{
			$row['up']		= Table::addCell('<a href="#" onclick="processData(\'/action/core/menu_up/'.$MID.'\')"><span class="'.B_ICON.' '.B_ICON.'-chevron-up"></span></a>');
		}
		if($rown == $menu_query->num_rows){
			$row['down']		= Table::addCell('');
		}else{
			$row['down']		= Table::addCell('<a href="#" onclick="processData(\'/action/core/menu_down/'.$MID.'\')"><span class="'.B_ICON.' '.B_ICON.'-chevron-down"></span></a>');
		}
	}
	if($this->accessAdminPage(42)) $row['edit']		= Table::addCell('<a href="menu_edit/'.$MID.'"><span class="'.B_ICON.' '.B_ICON.'-edit"></span></a>');
	if($this->accessAdminPage(42)) $row['delete']	= Table::addCell('<a href="#" onclick="processData(\'/action/core/menu_remove/'.$MID.'\')"><span class="'.B_ICON.' '.B_ICON.'-remove-sign"></span></a>');
	$menu->addRow($row);
	$rown++;
}
$menu_query->free_result();

$menu->build();
print $menu->getTable();
?>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
<?php
$check = new TableCheck();
$check
	->setType('block')
	->addRequireOne('#edit_btn')
	->addRequireOneBtn('#edit_btn', 'ipblock_edit')
	->create();
print $check->getScript();
?>
</script>
<?php
$delete_mod = $page->getPlugin('modalconf', array('delete', 'block', WebApp::action('core','ipblock_delete', true), 'post'));
$delete_mod->addDefaultConfig();
$delete_mod
	->setDefaultContent()
	->setDefaultModal()
	->setRightBtn('danger','Delete','trash')
	->build();
print $delete_mod->getModal();
?>
