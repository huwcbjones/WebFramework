<?php
if(WebApp::get('cat4')!==NULL){
	$option_query = $mySQL_r->prepare(
"SELECT `core_ip`.`ID`,`time`, CONCAT(`f_name`, ' ', `s_name`, ' (', `username`, ')'),  INET_NTOA(`ip`), `length`,`reason` FROM `core_ip`
LEFT JOIN `core_users`
ON `user_id`=`core_users`.`id`
WHERE `core_ip`.`ID`=?
");
	if(GUMP::is_valid(array('id'=>WebApp::get('cat4')),array('id'=>'required|integer'))){
		$id = WebApp::get('cat4');
		$option_query->bind_param('i', $id);
		$option_query->execute();
		$option_query->bind_result($block_id, $time, $user, $ip, $length, $reason);
		$option_query->store_result();
		if($option_query->num_rows==1){
			$option_query->fetch();
		}else{
			$page->setStatus(404);
			return;
		}
	}else{
		$page->setStatus(404);
		return;
	}
}else{
	$page->setStatus(404);
	return;
}

$closeBtn = array('a'=>array('t'=>'url', 'a'=>'../ipblock_view'), 'ic'=>'remove-sign');
$saveBtn = array('s'=>B_T_SUCCESS, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'ipblock_edit\', this, \'save\')'), 'ic'=>'floppy-disk');
$applyBtn = array('s'=>B_T_PRIMARY, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'ipblock_edit\', this, \'apply\')'), 'ic'=>'ok-sign');
$form = $page->getPlugin('form', array('ipblock_edit', WebApp::action('core', 'ipblock_edit', true), 'post'));

$form
	->setColumns(2, 6)
	->setIndent('    ')
	->addTextField(
		'Block ID',
		'id',
		$block_id,
		array(),
		array(
			'ro'=>true,
		)
	)
	->addTextField(
		'Block Created',
		'time',
		date(DATET_LONG, strtotime($time)),
		array('t'=>'Time the block was created'),
		array(
			'ro'=>true,
		)
	)
	->addTextField(
		'User',
		'user',
		$user,
		array('t'=>'User that created the block.'),
		array(
			'ro'=>true,
		)
	)
	->addTextField(
		'IP Address',
		'ip',
		$ip,
		array('t'=>'IP Address to block.', 'p'=>'xyz.xyz.xyz.xyz'),
		array(
			'ro'=>true,
		)
	)
	->addTextField(
		'Length',
		'length',
		$length,
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
		$reason,
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
	->addBtnLine(array('close'=>$closeBtn, 'save'=>$saveBtn, 'apply'=>$applyBtn));
$form->build();
?>

<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">Edit Block</h1>
    <?php print $form->getForm();?>
  </div>
</div>