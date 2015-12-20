<div class="row pane">
  <div class="col-xs-12">
    <div class="row">
	
<?php
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->accessAdminPage(1)){
	print('          <a class="btn btn-xs btn-block btn-success" href="add">Add Location&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-plus-sign"></span></a>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

// Delete User BTN
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->inGroup(3, true)){
	print('          <button class="btn btn-xs btn-danger btn-block disabled location_need_check" onclick="delete_mod()">Delete Location(s)&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-trash"></span></button>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

// Edit User BTN
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->accessAdminPage(2)){
	print('          <button class="btn btn-xs btn-primary btn-block disabled" id="edit_btn">Edit&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-edit"></span></button>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);
?>
    </div><br />
    <div class="row">
      <div class="col-xs-12">
<?php
$table = $page->getPlugin('table', array('locations'));
$table
	->setIndent(6)
	->addClass('table-bordered')
	->addClass('table-hover')
	->addClass('table-striped')
	->sort(true);
$thead = array();
if($this->accessAdminPage(2)||$this->inGroup(2, true)){
	$thead['selectAll'] = Table::addTHeadCell('<input type="checkbox" id="selectAll" />', false);
}
	$thead[] = Table::addTHeadCell('ID');
	$thead[] = Table::addTHeadCell('Name');
	$thead[] = Table::addTHeadCell('City');
	$thead[] = Table::addTHeadCell('County');
	$thead[] = Table::addTHeadCell('Map');
	if($this->accessAdminPage(2)) $thead[] = Table::addTHeadCell('Edit',false);
	$thead[] = Table::addTHeadCell('View',false);
$table->addHeader($thead);
$module_query = $mySQL_r->prepare("SELECT `id`, `name`, `city`, `county`, `map` FROM `location` ORDER BY `county` ASC, `city` ASC");
$module_query->execute();
$module_query->bind_result($id, $name, $city, $county, $map);
$module_query->store_result();
while($module_query->fetch()){
	$row   = array();
	if($this->accessAdminPage(2)||$this->inGroup(2, true)) $row['select']	= Table::addCell('<input class="locations_check" type="checkbox" value="'.$id.'" name="location[]" />');
	$row[] = Table::addCell($id);
	$row[] = Table::addCell($name, 'i_'.$id);
	$row[] = Table::addCell($city);
	$row[] = Table::addCell($county);
	$row[] = Table::addCell(Form::toggleLink($this, $map, '', '', array(
			's'=>array(
				'i'=>'ok',
			),
			'f'=>array(
				'i'=>'remove',
			)
		)
	));
	if($this->accessAdminPage(2)){
		$row[] = Table::addCell('<a href="/admin/location/edit/'.$id.'"><span class="'.B_ICON.' '.B_ICON.'-edit"></span></a>');
	}
	$row[] = Table::addCell('<a href="/location/'.$id.'" target="_blank"><span class="'.B_ICON.' '.B_ICON.'-new-window"></span></a>');
	$table->addRow($row);
}

$table->build();
print $table->getTable();
?>
<script type="text/javascript">
$(function() {
	$("#selectAll").click(function(){
		$(".locations_check").prop('checked', this.checked);
		if(this.checked){
			$(".location_need_check").removeClass("disabled");
		}else{
			$("#edit_btn").addClass("disabled");
			$(".location_need_check").addClass("disabled");
		}
	});
	$(".locations_check").change(function(){
		var check = ($('.locations_check').filter(":checked").length == $('.locations_check').length);
		$('#selectAll').prop("checked", check);
		if($('.locations_check').filter(":checked").length>0){
			if($('.locations_check').filter(":checked").length==1){
				$("#edit_btn").removeClass("disabled");
			}else{
				$("#edit_btn").addClass("disabled");
			}
			$(".location_need_check").removeClass("disabled");
		}else{
			$("#edit_btn").addClass("disabled");
			$(".location_need_check").addClass("disabled");
		}
	});
	$("#edit_btn").click(function(e){
		var locations = $('.locations_check').filter(":checked")
		if(locations.length==1){
			var id = locations.first().val();
			document.location.href = "edit/"+id;
		}else if(locations.length>1){
			alert("Please select one location only to edit");
		}
		return false;
	});
	
})
</script>
      <div>
    </div>
  </div>
</div>
<?php
if($this->inGroup(2, true)){
$delete_modal = $page->getPlugin('modalconf', array('delete', 'location', WebApp::action('location','delete', true), 'post'));
$delete_modal->addDefaultConfig();
$delete_modal
	->setDefaultContent()
	->SetDefaultModal()
	->setRightBtn('danger','Delete','trash')
	->build();
print $delete_modal->getModal();
}
?>