<?php
if(WebApp::get('cat4')===NULL||is_numeric(WebApp::get('cat4'))===false){
	$page->setStatus(404);
}
$menuID = intval(WebApp::get('cat4'));

$menu_query = $this->mySQL_r->prepare(
"SELECT `MID`, `position`, `parent`, `PID`, `dropdown`, `module_id` FROM `core_menu`
INNER JOIN `core_pages`
ON `core_pages`.`ID`=`core_menu`.`PID`
WHERE `MID`=?");
$menu_query->bind_param('i', $menuID);
$menu_query->execute();
$menu_query->store_result();

if($menu_query->num_rows != 1){
	$page->setStatus(404);
	return;
}
$menu_query->bind_result($menuID, $position, $parent, $PID, $dropdown, $module_id);
$menu_query->fetch();

// Get Module info
$module_query = $mySQL_r->prepare("SELECT `namespace`,`module_id` FROM `core_modules` ORDER BY `name` ASC");
$module_query->execute();
$module_query->bind_result($mname,$mod_id);
$module_data = array();
while($module_query->fetch()){
	$module_data[$mod_id] = $mname;
}
$modules = array('-1'=>array('v'=>'-1', 'n'=>'Select a module', 's'=>true, 'd'=>true));

foreach($module_data as $k=>$v){
	$modules[$k]['v'] = $k;
	$modules[$k]['n'] = $v;
	if($k==$module_id){
		$modules[$k]['s'] = true;
	}else{
		$modules[$k]['s'] = false;
	}
	$modules[$k]['d'] = false;
}


$closeBtn = array('a'=>array('t'=>'url', 'a'=>'../menu_view'), 'ic'=>'remove-sign');
$saveBtn = array('s'=>B_T_SUCCESS, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'menu_add\', this, \'save\')'), 'ic'=>'floppy-disk');
$form = $page->getPlugin('form', array('menu_add', WebApp::action('core', 'menu_add', true), 'post'));

$form
	->setColumns(3, 9)
	->setIndent('    ')
	->addHTML('<br />')
	->addSelect(
		'Module',
		'module',
		$modules,
		array('t'=>'The module for the menu item.'),
		array(
			'v'=>true,
			'vm'=>array(
				'selectRequiredMsg'=>array('m'=>'A module is required.','s'=>'danger')
			),
			'vo'=>'validateOn:["blur"]',
			'r'=>true
		)
	)
	->addSelect2(
		'Page',
		'PID',
		'',
		array('t'=>'The page the menu item will link to. * denotes admin page'),
		array(
			'r'=>true
		)
	)
	->addButtonGroup(
		'Dropdown',
		'dropdown',
		array(
			array(
				'i'=>'dropdownY',
				's'=>B_T_SUCCESS,
				'v'=>1,
				'l'=>'Yes',
				'c'=>$dropdown
			),
			array(
				'i'=>'dropdownN',
				's'=>B_T_FAIL,
				'v'=>0,
				'l'=>'No',
				'c'=>!$dropdown
			)
		),
		array('t'=>'Is this menu item going to hold more items?')
	)
	->addBtnLine(array('close'=>$closeBtn, 'save'=>$saveBtn));
$form->build();
?>
<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">Edit Menu Item</h1>
    <ul class="nav nav-tabs nav-justified" role="tablist">
      <li class="active"><a href="#item" role="tab" data-toggle="tab">Item</a></li>
<?php
print '      <li';
if(!$dropdown) print ' class="disabled"';
print '><a href="#';
if($dropdown) print 'dropdown';
print '" role="tab"';
if($dropdown) print ' data-toggle="tab"';
print '>Dropdown</a></li>'.PHP_EOL;
?>
    </ul>
    <div class="tab-content">
      <div class="tab-pane active" id="item">
        <div class="row">
          <div class="col-xs-12">
<?php print $form->getForm();?>
          </div>
        </div>
      </div>
      <div class="tab-pane" id="dropdown"><br />
     <div class="row">
<?php
// Add BLock BTN
print('        <div class="col-lg-2 col-xs-4">'.PHP_EOL);
if($this->accessAdminPage(61)){
	print('          <a class="btn btn-xs btn-block btn-success" href="../menu_addsub/'.$menuID.'">New&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-plus-sign"></span></a>'.PHP_EOL);
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
$menu = $page->getPlugin('table', array('submenu'));
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
WHERE `parent`=?
ORDER BY `position` ASC");
$menu_query->bind_param('i', $menuID);
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
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
var module_id = '';
$('[name="module"]').change(function(e) {
    $( "select option:selected" ).each(function() {
      module_id = $( this ).val();
    });
});
$('[name="PID"]').select2({
	multiple: false,
	placeholder: "Search for pages",
	minimumInputLength: 0,
	ajax: {
		url: "/ajax/core/menu_pages",
		dataType: 'json',
		data: function(term, page){
			return {
				q: term,
				m: module_id
			}
		},
		results: function (data, page){
			return {results: data.data.pages}
		}
	}
});
</script>