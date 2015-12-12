<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">Install Module</h1>
    <p>Select which method you are going to use to install the module, then either browse for the zip file, or enter the path where the module is stored.</p>
<?php
$script = '$("input[name=method]", "#install").change(function(){'.PHP_EOL;
$script.= '  if($("input[name=method]:checked", "#install").val()=="zip"){'.PHP_EOL;
$script.= '    $("#install\\\\:\\\\:zip_file").removeAttr("disabled");'.PHP_EOL;
$script.= '    $("#install\\\\:\\\\:directory").attr("disabled","");'.PHP_EOL;
$script.= '  }else{'.PHP_EOL;
$script.= '    $("#install\\\\:\\\\:zip_file").attr("disabled","");'.PHP_EOL;
$script.= '    $("#install\\\\:\\\\:directory").removeAttr("disabled");'.PHP_EOL;
$script.= '  }'.PHP_EOL;
$script.= '});'.PHP_EOL;
$script.= '$("#install").submit(function(){'.PHP_EOL;
$script.= '  if($("input[name=method]:checked", "#install").val()=="zip"){'.PHP_EOL;
$script.= '    $("#install\\\\:\\\\:zip_file").removeAttr("disabled");'.PHP_EOL;
$script.= '    $("#install\\\\:\\\\:directory").attr("disabled","");'.PHP_EOL;
$script.= '  }else{'.PHP_EOL;
$script.= '    $("#install\\\\:\\\\:zip_file").attr("disabled","");'.PHP_EOL;
$script.= '    $("#install\\\\:\\\\:directory").removeAttr("disabled");'.PHP_EOL;
$script.= '  }'.PHP_EOL;
$script.= '});'.PHP_EOL;

$installBtn = array('s'=>B_T_PRIMARY, 'a'=>array('t'=>'button', 'a'=>'submit'), 'ic'=>'install');

$form = $page->getPlugin('form', array('install', WebApp::action('modules', 'pre_install', false), 'post', 'multipart/form-data', ''));
$form
	->setIndent(6)
	->addOptionGrid('radio', 'Install Method', 'method', array(
		array('i'=>'zip','l'=>'Upload Zip','v'=>'zip','c'=>false,'d'=>false),
		array('i'=>'dir','l'=>'From Directory','v'=>'dir','c'=>false,'d'=>false)
		), array('t'=>'Select the mode to install the module'), array())
	->addFileUpload('Zip upload' , 'zip_file', array('t'=>'Browse to the zip file containing the module','p'=>'C:\Users\Someone\module.zip'), array('d'=>true))
	->addTextField('Directory' , 'directory', '', array('t'=>'Path to the directory containing the module','p'=>'/temp/module'), array('d'=>true))
	->addBtnLine(array('install'=>$installBtn))
	->addHTML('<iframe id="install_tgt" name="install_tgt" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>')
	->addScript($script);
$form->build();
print $form->getForm();
?>
  </div>
</div>
