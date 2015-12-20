<?php
if(WebApp::get('cat4')===NULL){
	$page->setStatus(500);
}
?>
<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">Update <?php print WebApp::get('cat4');?> Module</h1>
    <p>Select which method you are going to use to update the module, then either browse for the zip file, or enter the path where the module update package is stored.</p>
<?php
$script = '$("input[name=method]", "#update").change(function(){'.PHP_EOL;
$script.= '  if($("input[name=method]:checked", "#update").val()=="zip"){'.PHP_EOL;
$script.= '    $("#update\\\\:\\\\:zip_file").removeAttr("disabled");'.PHP_EOL;
$script.= '    $("#update\\\\:\\\\:directory").attr("disabled","");'.PHP_EOL;
$script.= '  }else{'.PHP_EOL;
$script.= '    $("#update\\\\:\\\\:zip_file").attr("disabled","");'.PHP_EOL;
$script.= '    $("#update\\\\:\\\\:directory").removeAttr("disabled");'.PHP_EOL;
$script.= '  }'.PHP_EOL;
$script.= '});'.PHP_EOL;
$script.= '$("#update").submit(function(){'.PHP_EOL;
$script.= '  if($("input[name=method]:checked", "#update").val()=="zip"){'.PHP_EOL;
$script.= '    $("#update\\\\:\\\\:zip_file").removeAttr("disabled");'.PHP_EOL;
$script.= '    $("#update\\\\:\\\\:directory").attr("disabled","");'.PHP_EOL;
$script.= '  }else{'.PHP_EOL;
$script.= '    $("#update\\\\:\\\\:zip_file").attr("disabled","");'.PHP_EOL;
$script.= '    $("#update\\\\:\\\\:directory").removeAttr("disabled");'.PHP_EOL;
$script.= '  }'.PHP_EOL;
$script.= '});'.PHP_EOL;

$updateBtn = array('s'=>B_T_PRIMARY, 'a'=>array('t'=>'button', 'a'=>'submit'), 'ic'=>'update');

$confMsg = 'I accept that the module will be updated. This means that:</br>'.PHP_EOL.'<ul>'.PHP_EOL;
$confMsg.= '<li>Any changes to pages will be reset to default</li>'.PHP_EOL;
$confMsg.= '<li>All groups created by this module will be removed and reinstalled. This means that users with permission for this group will loose them (they will be removed from the group)</li>'.PHP_EOL;
$confMsg.= '<li>I may loose some data when updating the module</li>'.PHP_EOL;
$confMsg.= '</ul>';

$form = $page->getPlugin('form', array('update', WebApp::action('modules', 'pre_update', false), 'post', 'multipart/form-data', ''));
$form
	->setIndent(6)
	->addOptionGrid('radio', 'Update Method', 'method', array(
		array('i'=>'zip','l'=>'Upload Zip','v'=>'zip','c'=>false,'d'=>false),
		array('i'=>'dir','l'=>'From Directory','v'=>'dir','c'=>false,'d'=>false)
		), array('t'=>'Select the mode to update the module'), array())
	->addFileUpload('Zip upload' , 'zip_file', array('t'=>'Browse to the zip file containing the module','p'=>'C:\Users\Someone\module.zip'), array('d'=>true,'a'=>'application/zip'))
	->addTextField('Directory' , 'directory', '', array('t'=>'Path to the directory containing the module','p'=>'/temp/module'), array('d'=>true))
	->addConfirmCheck('Keep Page Access', 'page', 'Keep module page access', array('t'=>'Select if you wish groups to keep their access to this modules pages.'), array('v'=>true))
	->addConfirmCheck('Confirmation', 'conf', $confMsg, array('t'=>''),array('v'=>true,'r'=>true))
	->addHiddenField('mod', WebApp::get('cat4'))
	->addBtnLine(array('Update'=>$updateBtn))
	->addScript($script);
$form->build();
print $form->getForm();
?>
  </div>
</div>
