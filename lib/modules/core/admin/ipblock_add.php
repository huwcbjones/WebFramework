<?php
$data = array('ip' => WebApp::get('ip'));
$validated = GUMP::is_valid($data, array('ip'=>'valid_ipv4'));
$ip = '';
if($validated){
	$ip = WebApp::get('ip');
}

$closeBtn = array('a'=>array('t'=>'url', 'a'=>'ipblock_view'), 'ic'=>'remove-sign');
$saveBtn = array('s'=>B_T_SUCCESS, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'ipblock_add\', this, \'save\')'), 'ic'=>'floppy-disk');
$form = $page->getPlugin('form', array('ipblock_add', WebApp::action('core', 'ipblock_add', true), 'post'));

$form
	->setColumns(2, 6)
	->setIndent('    ')
	->addTextField(
		'IP Address',
		'ip',
		$ip,
		array('t'=>'IP Address to block.', 'p'=>'xyz.xyz.xyz.xyz'),
		array(
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'An IP Address is required.','s'=>'danger'),
				'textfieldInvalidFormatMsg'=>array('m'=>'Please enter a valid IPV4 Adress.','s'=>'danger'),
			),
			'vo'=>'validateOn:["blur"]',
			'd'=>false,
			'r'=>true,
			'vt'=>'ip',
			't'=>'text'
		)
	)
	->addTextField(
		'Length',
		'length',
		'',
		array('t'=>'Length of block in days.', 'p'=>'Days to block'),
		array(
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'Length of block is required.','s'=>'danger'),
			),
			'vo'=>'validateOn:["blur"]',
			'd'=>false,
			'r'=>true,
			'vt'=>'integer',
			't'=>'number'
		)
	)
	->addTextField(
		'Reason',
		'reason',
		'',
		array('t'=>'Reason for block.', 'p'=>'Reason'),
		array(
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'A Reason is required.', 's'=>B_T_FAIL),
				'textfieldMinCharsMsg'=>array('m'=>'A Reason is required.', 's'=>B_T_FAIL),
				'textfieldMaxCharsMsg'=>array('m'=>'Reason is limited to 255 chars.', 's'=>B_T_FAIL)
			),
			'vo'=>'minChars: 0, maxChars: 255, validateOn:["blur"]',
			'r'=>true
		)
	)
	->addBtnLine(array('close'=>$closeBtn, 'save'=>$saveBtn));
$form->build();
?>

<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">New Block</h1>
    <?php print $form->getForm();?>
  </div>
</div>