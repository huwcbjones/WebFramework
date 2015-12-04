<?php
if(WebApp::get('cat4')!==NULL){
	$option_query = $mySQL_r->prepare("SELECT `ID`,`name`,`value`,`desc` FROM `core_options` WHERE `ID`=?");
	$id = WebApp::get('cat4');
	$option_query->bind_param('i', $id);
	$option_query->execute();
	$option_query->bind_result($option_id,$name,$value,$desc);
	$option_query->store_result();
	if($option_query->num_rows==1){
		$option_query->fetch();
	}else{
		$page->setStatus(404);
	}
}else{
	$page->setStatus(404);
}

$closeBtn = array('a'=>array('t'=>'url', 'a'=>'../option_view'), 'ic'=>'remove-sign');
$saveBtn = array('s'=>B_T_SUCCESS, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'option_edit\', this, \'save\')'), 'ic'=>'floppy-disk');
$applyBtn = array('s'=>B_T_PRIMARY, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'option_edit\',this, \'apply\')'), 'ic'=>'ok-sign');
$form = $page->getPlugin('form', array('option_edit', WebApp::action('core', 'option_edit', true), 'post'));

$form
	->setColumns(2, 6)
	->setIndent('    ')
	->addTextField(
		'Option ID',
		'id',
		$option_id,
		array('t'=>'ID of Option.', 'p'=>'ID'),
		array(
			'ro'=>true
		)
	)
	->addTextField(
		'Option Name',
		'name',
		$name,
		array('t'=>'Option name.', 'p'=>'Name'),
		array(
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'A name is required.','s'=>'danger'),
				'textfieldMinCharsMsg'=>array('m'=>'A name is required.', 's'=>'danger'),
				'textfieldMaxCharsMsg'=>array('m'=>'Name is limited to 100 characters.', 's'=>'danger')
			),
			'vo'=>'minChars: 0, maxChars: 100, validateOn:["blur"]',
			'd'=>false,
			'ro'=>true
		)
	)
	->addTextArea(
		'Value',
		'value',
		$value,
		6,
		array('t'=>'Value for option.', 'p'=>'Option'),
		array(
			'v'=>true,
			'vm'=>array(
				'textareaMaxCharsMsg'=>array('m'=>'Option values are limited to 5000 characters.', 's'=>B_T_FAIL)
			),
			'vo'=>'maxChars: 5000, userCharacterMasking:false, validateOn:["blur", "change"]',
			'c'=>true,
			'r'=>true
		)
	)
	->addTextArea(
		'Description',
		'desc',
		$desc,
		3,
		array('t'=>'Description of Option', 'p'=>'Option Description'),
		array(
			'v'=>true,
			'vm'=>array(
				'textareaMaxCharsMsg'=>array('m'=>'Option description is limited to 250 chars.', 's'=>B_T_FAIL)
			),
			'vo'=>'maxChars: 250, useCharacterMasking:false, validateOn:["blur", "change"]',
			'c'=>true,
			'r'=>false
		)
	)
	->addBtnLine(array('close'=>$closeBtn, 'save'=>$saveBtn, 'apply'=>$applyBtn));
$form->build();
?>

<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">Edit Option</h1>
    <?php print $form->getForm();?>
  </div>
</div>