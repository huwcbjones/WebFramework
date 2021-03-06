<?php
// Get Module info
$module_query = $mySQL_r->prepare("SELECT `namespace`,`module_id` FROM `core_modules` ORDER BY `name` ASC");
$module_query->execute();
$module_query->bind_result($mname,$module_id);
$module_data = array();
while($module_query->fetch()){
  $module_data[$module_id] = $mname;
}
$modules = array('-1'=>array('v'=>'-1', 'n'=>'Select a module', 's'=>true, 'd'=>true));

foreach($module_data as $k=>$v){
	$modules[$k]['v'] = $k;
	$modules[$k]['n'] = $v;
	$modules[$k]['s'] = false;
	$modules[$k]['d'] = false;
}


$closeBtn = array('a'=>array('t'=>'url', 'a'=>'menu_view'), 'ic'=>'remove-sign');
$saveBtn = array('s'=>B_T_SUCCESS, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'menu_add\', this, \'save\')'), 'ic'=>'floppy-disk');
$form = $page->getPlugin('form', array('menu_add', WebApp::action('core', 'menu_add', true), 'post'));

$form
	->setColumns(3, 9)
	->setIndent('    ')
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
				'c'=>false
			),
			array(
				'i'=>'dropdownN',
				's'=>B_T_FAIL,
				'v'=>0,
				'l'=>'No',
				'c'=>true
			)
		),
		array('t'=>'Is this menu item going to hold more items?')
	)
	->addBtnLine(array('close'=>$closeBtn, 'save'=>$saveBtn));
$form->build();
?>

<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">New Menu Item</h1>
    <?php print $form->getForm();?>
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