<div class="row pane">
  <div class="col-xs-12">
     <div class="row">
<?php
// Add Option BTN
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->accessAdminPage(11)){
	print('          <a class="btn btn-xs btn-block btn-success" href="option_add">New&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-plus-sign"></span></a>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

// Delete Option BTN
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->inGroup(1)){
	print('          <button class="btn btn-xs btn-danger btn-block disabled option_need_check" onclick="delete_mod()">Delete&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-trash"></span></button>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

// Edit Option BTN
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->accessAdminPage(12)){
	print('          <button class="btn btn-xs btn-primary btn-block disabled" id="edit_btn">Edit&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-edit"></span></button>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

?>
    </div><br />
    <div class="row">
      <div class="col-xs-12">
<?php
$option_view = $page->getPlugin('table', array('options'));
$option_view
	->addClass('table-striped')
	->addClass('table-hover')
	->addClass('table-bordered')
	->setIndent('        ')
	->sort(true)
	->pager(true)
	->sticky(true);
	
$thead = array();
if($this->accessAdminPage(12)) $thead['selectAll'] = Table::addTHeadCell('<input type="checkbox" id="selectAll" />', false);
$thead['ID']		= Table::addTHeadCell('ID');
$thead['Name']		= Table::addTHeadCell('Name');
$thead['Value']		= Table::addTHeadCell('Value');
$thead['Desc']		= Table::addTHeadCell('Description');
if($this->accessAdminPage(12)) $thead['edit'] = Table::addTHeadCell('Edit', false);
$option_view->addHeader($thead);

$option_query = $mySQL_r->prepare("SELECT `id`, `name`,`value`,`desc` FROM `core_options` ORDER BY `id` ASC");
$option_query->bind_result($option_id, $name, $value, $desc);
$option_query->execute();
$option_query->store_result();

while($option_query->fetch()){
	if(strlen($value)>48){
		$value = substr($value, 0, 47).'&hellip;';
	}
	if($this->accessAdminPage(12)) $row['select']	= Table::addCell('<input class="options_check" type="checkbox" value="'.$option_id.'" name="option[]" />');
	$row['ID']		= Table::addCell($option_id, '', '', '', true);
	$row['name']	= Table::addCell($name, 'i_'.$option_id, '', true);
	$row['value']	= Table::addCell($value, '', '');
	$row['desc']	= Table::addCell($desc);
	if($this->accessAdminPage(12)) $row['edit']		= Table::addCell('<a href="option_edit/'.$option_id.'"><span class="'.B_ICON.' '.B_ICON.'-edit"></span></a>');
	$option_view->addRow($row);
}
$option_query->free_result();

$option_view->build();
print $option_view->getTable();
?>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$(function() {
	$("#selectAll").click(function(){
		$(".options_check").prop('checked', this.checked);
		if(this.checked){
			$(".option_need_check").removeClass("disabled");
		}else{
			$("#edit_btn").addClass("disabled");
			$(".option_need_check").addClass("disabled");
		}
	});
	$(".options_check").change(function(){
		var check = ($('.options_check').filter(":checked").length == $('.options_check').length);
		$('#selectAll').prop("checked", check);
		if($('.options_check').filter(":checked").length>0){
			if($('.options_check').filter(":checked").length==1){
				$("#edit_btn").removeClass("disabled");
			}else{
				$("#edit_btn").addClass("disabled");
			}
			$(".option_need_check").removeClass("disabled");
		}else{
			$("#edit_btn").addClass("disabled");
			$(".option_need_check").addClass("disabled");
		}
	});
	$("#edit_btn").click(function(e){
		var options = $('.options_check').filter(":checked")
		if(options.length==1){
			var option_id = options.first().val();
			document.location.href = "option_edit/"+option_id;
		}else if(options.length>1){
			alert("Please select one option only to edit");
		}
		return false;
	});
})
</script>
<?php
$delete_mod = $page->getPlugin('modalconf', array('delete', 'option', WebApp::action('core','option_delete', true), 'post'));
$delete_mod->addDefaultConfig();
$delete_mod
	->setDefaultContent()
	->SetDefaultModal()
	->setRightBtn('danger','Delete','trash')
	->build();
print $delete_mod->getModal();
?>