<?php
if(!isset($_GET['m'])) $page->errorCode = 404;
include_once($_SERVER['DOCUMENT_ROOT']."/lib/modules/meet.php");
include_once($_SERVER['DOCUMENT_ROOT']."/lib/docs.php");
$doc = new Doc($mySQL);
$meet = new Meet($mySQL);
$meet->setID($_GET['m']);
if($meet->createMeet()===false) $page->errorCode = 404;

$page->setTitle($meet->getTitle().' - Add Docs');
$page->createTitle();
print $page->getHeader();
if(isset($_GET['msg'])){
	$alert = new Alert();
	$alert->printAlerts();
}

if(!isset($_GET['e'])){
	$_GET['e'] = 1;
}elseif($_GET['e']<1){
	$_GET['e'] = 1;
}

?>

<div class="row pane">
  <div class="col-xs-12">
    <form class="form-horizontal" id="comp_add" method="post" action="/act/comp_add-docs">
      <div class="row">
        <div class="col-sm-12 col-md-11 col-lg-11 col-lg-offset-1 col-md-offset-1">
          <div class="form-group">
            <div class="col-sm-2">
              <label>Progress
                <?php Tooltip::helpTooltip('Your progress through the meet wizard.') ?>
              </label>
            </div>
            <div class="col-sm-10">
              <div class="row">
                <div class="col-xs-12">
                  <div class="progress progress-striped active">
                    <div id="progress-bar" class="progress-bar" role="progressbar" aria-valuenow="28" aria-valuemin="0" aria-valuemax="100" style="width: 41%"> <span id="progress-bar-sr" class="sr-only">28% Complete</span> </div>
                  </div>
                </div>
              </div>
              <div class="row text-center">
                <div class="col-xs-2"> <span class="text-<?php print B_T_MUTED ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
                <div class="col-xs-2"> <span class="text-<?php print B_T_MUTED ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
                <div class="col-xs-2"> <span class="text-<?php print B_T_PRIMARY ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
                <div class="col-xs-2"> <span class="text-<?php print B_T_DEFAULT ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
                <div class="col-xs-2"> <span class="text-<?php print B_T_DEFAULT ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
                <div class="col-xs-2"> <span class="text-<?php print B_T_DEFAULT ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
              </div>
              <div class="row text-center">
                <div class="col-xs-2 text-<?php print B_T_MUTED ?>">
                  <p>Set-Up Meet</p>
                </div>
                <div class="col-xs-2 text-<?php print B_T_MUTED ?>">
                  <p>Add Notes</p>
                </div>
                <div class="col-xs-2 text-<?php print B_T_PRIMARY ?>">
                  <p>Add Documents</p>
                </div>
                <div class="col-xs-2 text-<?php print B_T_DEFAULT ?>">
                  <p>Add Sessions</p>
                </div>
                <div class="col-xs-2 text-<?php print B_T_DEFAULT ?>">
                  <p>Add Events</p>
                </div>
                <div class="col-xs-2 text-<?php print B_T_DEFAULT ?>">
                  <p>Res. Service</p>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-6">
              <a tabindex="<?php $tabindex++;print $tabindex;?>" class="btn btn-large btn-<?php print B_T_DEFAULT ?> btn-block" href="/admin/competitions/comp_view">Close&nbsp;&nbsp;&nbsp;<span class="<?php print B_ICON.' '.B_ICON ?>-remove-sign"></span></a>
            </div>
            <div class="col-sm-6">
              <button onClick="return openModal()" id="saveContBtn" class="btn btn-large btn-<?php print B_T_SUCCESS ?> btn-block" type="submit">Save Documents&nbsp;&nbsp;&nbsp;<span class="<?php print B_ICON.' '.B_ICON ?>-floppy-disk"></span></button>
            </div>
          </div>
<?php

print $doc->getDocRowHeader();
$query = $mySQL['r']->prepare("SELECT `ID` FROM `core_files` ORDER BY `title` ASC");
$query->execute();
$query->store_result();
if($query->num_rows!=0){
	$query->bind_result($ID);
	while($query->fetch()){
		$doc->setID($ID);
		$doc->createVariables();
		$doc->createDocRow(true);
		print $doc->getDocRow();
		$doc->clear(true);
	}
}
?>
        </div>
      </div>
<?php
	$deleteConf = new Modal();
	$deleteConf->setID('saveCont');
	$deleteConf->setBody('Do you wish to save and continue to set up sessions or save and return to the View Competitions page?');
	$deleteConf->setTitle('Save?');
	$deleteConf->setCentre('default','Save &amp; Close','floppy-disk','button','$(\'#saveOpt\').val(\'back\');document.getElementById(\'comp_add\').submit();');
	$deleteConf->setRight('success','Save &amp; Continue','floppy-save','submit','$(\'#saveOpt\').val(\'cont\');');
	print $deleteConf->getModal();
?>
      <input type="hidden" name="ID" value="<?php print $_GET['m']?>"/>
      <input type="hidden" name="saveOpt" value="cont" id="saveOpt" />
    </form>
  </div>
</div>
<script type="text/javascript">
var completion = 0;
function openModal(){
	$("#saveCont").modal('show');
	return false;
}
</script> 