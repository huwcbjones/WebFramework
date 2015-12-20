<?php
if(WebApp::get('r')=='d'){
	$closeBtn = array('a'=>array('t'=>'url', 'a'=>'./'), 'ic'=>'remove-sign');
}elseif(WebApp::get('r')=='v'){
	$closeBtn = array('a'=>array('t'=>'url', 'a'=>'article_view'), 'ic'=>'remove-sign');
}else{
	$closeBtn = array('a'=>array('t'=>'url', 'a'=>'./'), 'ic'=>'remove-sign');
}
$saveBtn = array('s'=>B_T_SUCCESS, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'article_add\', this, \'add\', \'updateEditor\')'), 'ic'=>'floppy-disk');
$form = $page->getPlugin('form', array('article_add', WebApp::action('news', 'article_add', true), 'post'));
$form
	->setIndent('    ')
	->setColumns(2, 10)
	->addTextField(
		'Title',
		'title',
		'',
		array('t'=>'Article Title', 'p'=>'Title'),
		array(
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'A Title is required.', 's'=>B_T_FAIL),
				'textfieldMinCharsMsg'=>array('m'=>'A Title is required.', 's'=>B_T_FAIL),
				'textfieldMaxCharsMsg'=>array('m'=>'Title is limited to 250 chars.', 's'=>B_T_FAIL)
			),
			'vo'=>'minChars: 0, maxChars: 250, validateOn:["blur"]',
			'r'=>true
		)
	)
	->addDateTime(
		'Publish From',
		'p_from',
		'',
		array('t'=>'Date/time to start publishing, leave blank for always')
	)
	->addDateTime(
		'Publish To',
		'p_to',
		'',
		array('t'=>'Date/time to finish publishing, leave blank for always')
	)
	->addTextArea(
		'Article',
		'article',
		'',
		25,
		array('t'=>'The article'),
		array('ck'=>true, 'ckt'=>'Full')
	)
	->addBtnLine(array('Close'=>$closeBtn, 'Save'=>$saveBtn));
$form->build();
?>

<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">New Article</h1>
<?php
print $form->getForm();
?>
  </div>
</div>

<script type="text/javascript">
function updateEditor(){for ( instance in CKEDITOR.instances ) {CKEDITOR.instances[instance].updateElement();}return true;}
</script>
