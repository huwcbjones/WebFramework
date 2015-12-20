<div class="row pane">
  <div class="col-xs-12">
     <div class="row">
<?php
// Add BLock BTN
print('        <div class="col-lg-2 col-xs-4">'.PHP_EOL);
if($this->accessAdminPage(41)){
	print('          <a class="btn btn-xs btn-block btn-success" href="ipblock_add">New&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-plus-sign"></span></a>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

// Delete Option BTN
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
print('        </div>'.PHP_EOL);

?>
    </div><br />
    <div class="row">
      <div class="col-xs-12">
<?php
$block = $page->getPlugin('table', array('blocks'));
$block
	->addClass('table-striped')
	->addClass('table-hover')
	->addClass('table-bordered')
	->setIndent('        ')
	->sort(true);
	
$thead = array();
if($this->accessAdminPage(42)||$this->inGroup(43, true)) $thead['selectAll'] = Table::addTHeadCell('<input type="checkbox" id="selectAll" />', false);
$thead['ID']		= Table::addTHeadCell('ID');
$thead['on']		= Table::addTHeadCell('On');
$thead['by']		= Table::addTHeadCell('By');
$thead['ip']		= Table::addTHeadCell('IP');
$thead['expires']	= Table::addTHeadCell('Expires');
$thead['left']		= Table::addTHeadCell('<abbr title="Days left">Left</abbr>');
$thead['reason']	= Table::addTHeadCell('Reason');
if($this->accessAdminPage(42)) $thead['edit'] = Table::addTHeadCell('Edit', false);
$block->addHeader($thead);

$block_query = $mySQL_r->prepare(
"SELECT `core_ip`.`id`, `time`, CONCAT(`f_name`, ' ', `s_name`, ' (', `username`, ')'), INET_NTOA(`ip`), DATE_ADD(`time`, INTERVAL `length` DAY) AS `expires`, DATEDIFF(DATE_ADD(`time`, INTERVAL `length` DAY), NOW()) AS `left`, `reason`
FROM `core_ip`
LEFT JOIN `core_users`
ON `core_users`.`id`=`user_id`
HAVING `expires`>NOW()
ORDER BY `left` ASC");
$block_query->bind_result($block_id, $time, $by, $ip, $expires, $left, $reason);
$block_query->execute();
$block_query->store_result();

while($block_query->fetch()){
	if($this->accessAdminPage(42)||$this->inGroup(43, true)) $row['select']	= Table::addCell('<input class="blocks_check" type="checkbox" value="'.$block_id.'" name="block[]" />');
	$row['ID']		= Table::addCell($block_id);
	$row['on']		= Table::addCell(date(DATE_LONG, strtotime($time)));
	$row['by']		= Table::addCell($by);
	$row['ip']		= Table::addCell($ip, 'i_'.$block_id);
	$row['expires']	= Table::addCell(date(DATET_LONG, strtotime($expires)));
	$row['left']	= Table::addCell($left);
	$row['reason']	= Table::addCell($reason);
	if($this->accessAdminPage(42)) $row['edit']		= Table::addCell('<a href="ipblock_edit/'.$block_id.'"><span class="'.B_ICON.' '.B_ICON.'-edit"></span></a>');
	$block->addRow($row);
}
$block_query->free_result();

$block->build();
print $block->getTable();
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
