<?php
$closeBtn = array('a'=>array('t'=>'url', 'a'=>'option_view'), 'ic'=>'remove-sign');
$saveBtn = array('s'=>B_T_SUCCESS, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'option_add\', this, \'save\')'), 'ic'=>'floppy-disk');
$form = $page->getPlugin('form', array('option_add', WebApp::action('core', 'option_add', true), 'post'));

$form
	->setIndent('    ')
	->setColumns(2, 6)
	->addTextField(
		'Option Name',
		'name',
		'',
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
			'r'=>true
		)
	)
	->addTextArea(
		'Value',
		'value',
		'',
		6,
		array('t'=>'Value for option.', 'p'=>'Option'),
		array(
			'v'=>true,
			'vm'=>array(
				'textareaRequiredMsg'=>array('m'=>'An option value is required.', 's'=>B_T_FAIL),
				'textareaMinCharsMsg'=>array('m'=>'An option value is required.', 's'=>B_T_FAIL),
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
		'',
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
	->addBtnLine(array('close'=>$closeBtn, 'save'=>$saveBtn));
$form->build();
?>

<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">New Option</h1>
    <?php print $form->getForm();?>
  </div>
</div>