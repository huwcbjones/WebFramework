<?php
if(!isset($_GET['m'])) $page->errorCode = 404;
include_once($_SERVER['DOCUMENT_ROOT']."/lib/modules/meet.php");
$meet = new Meet($mySQL);
$meet->setID($_GET['m']);
if($meet->createMeet()===false) $page->errorCode = 404;

$page->setTitle($meet->getTitle().' - Add Notes');
$page->createTitle();
print $page->getHeader();
if(isset($_GET['msg'])){
	$alert = new Alert();
	$alert->printAlerts();
}
?>

<div class="row pane">
  <div class="col-sm-12 col-md-11 col-lg-11 col-lg-offset-1 col-md-offset-1">
    <form class="form-horizontal" id="comp_add" method="post" action="/act/comp_add-notes">
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
                <div id="progress-bar" class="progress-bar" role="progressbar" aria-valuenow="17" aria-valuemin="0" aria-valuemax="100" style="width: 24%"> <span id="progress-bar-sr" class="sr-only">14% Complete</span> </div>
              </div>
            </div>
          </div>
          <div class="row text-center">
            <div class="col-xs-2"> <span class="text-<?php print B_T_MUTED ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
            <div class="col-xs-2"> <span class="text-<?php print B_T_PRIMARY ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
            <div class="col-xs-2"> <span class="text-<?php print B_T_DEFAULT ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
            <div class="col-xs-2"> <span class="text-<?php print B_T_DEFAULT ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
            <div class="col-xs-2"> <span class="text-<?php print B_T_DEFAULT ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
            <div class="col-xs-2"> <span class="text-<?php print B_T_DEFAULT ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
          </div>
          <div class="row text-center">
            <div class="col-xs-2 text-<?php print B_T_MUTED ?>">
              <p>Set-Up Meet</p>
            </div>
            <div class="col-xs-2 text-<?php print B_T_PRIMARY ?>">
              <p>Add Notes</p>
            </div>
            <div class="col-xs-2 text-<?php print B_T_DEFAULT ?>">
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
      <div class="form-group"> <span id="s_n_e">
        <div class="col-sm-2">
          <label for="n_e">Entry Notes
            <?php Tooltip::helpTooltip('Additional information regarding entries. (Recommended)') ?>
          </label>
        </div>
        <div class="col-sm-10">
          <textarea class="form-control" name="n_e" id="n_e" rows="3"></textarea><br />
        </div>
        </span> </div>
      <div class="form-group"> <span id="s_n_c">
        <div class="col-sm-2">
          <label for="n_e">Coaches' Notes
            <?php Tooltip::helpTooltip('Additional information regarding coaches. (Recommended)') ?>
          </label>
        </div>
        <div class="col-sm-10">
          <textarea class="form-control" name="n_c" id="n_c" rows="3"></textarea>
          <br />
        </div>
        </span> </div>
      <div class="form-group"> <span id="s_n_s">
        <div class="col-sm-2">
          <label for="n_s">Swimmers' Notes
            <?php Tooltip::helpTooltip('Additional information regarding swimmers. (Recommended)') ?>
          </label>
        </div>
        <div class="col-sm-10">
          <textarea class="form-control" name="n_s" id="n_s" rows="3"></textarea>
          <br />
        </div>
        </span> </div>
      <div class="form-group"> <span id="s_n_p">
        <div class="col-sm-2">
          <label for="n_p">Parents' Notes
            <?php Tooltip::helpTooltip('Additional information regarding parents. (Recommended)') ?>
          </label>
        </div>
        <div class="col-sm-10">
          <textarea class="form-control" name="n_p" id="n_p" rows="3"></textarea>
          <br />
        </div>
        </span> </div>
      <div class="form-group">
        <div class="col-sm-6">
          <a tabindex="<?php $tabindex++;print $tabindex;?>" class="btn btn-large btn-default btn-block" href="/admin/competitions/comp_view">Close&nbsp;&nbsp;&nbsp;<span class="<?php print B_ICON.' '.B_ICON ?>-remove-sign"></span></a>
        </div>
        <div class="col-sm-6">
          <button onClick="return openModal()" id="saveContBtn" class="btn btn-large btn-success btn-block" type="submit">Save Notes&nbsp;&nbsp;&nbsp;<span class="<?php print B_ICON.' '.B_ICON ?>-floppy-disk"></span></button>
        </div>
      </div>
      <?php
	  $deleteConf = new Modal();
	  $deleteConf->setID('saveCont');
	  $deleteConf->setBody('Do you wish to save the notes and continue add documents (continue wizard) or save the notes and return to the View Competitions page?');
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
var entry = CKEDITOR.replace('n_e',{
	height: '5em',
	toolbar: 'Basic',
	removePlugins: 'backup,floating-tools,elementspath,back-up',
	resize_enabled: false
});
var swimmer = CKEDITOR.replace('n_s',{
	height: '5em',
	toolbar: 'Basic',
	removePlugins: 'backup,floating-tools,elementspath,back-up',
	resize_enabled: false
});
var parents = CKEDITOR.replace('n_p',{
	height: '5em',
	toolbar: 'Basic',
	removePlugins: 'backup,floating-tools,elementspath,back-up',
	resize_enabled: false
});
var coach = CKEDITOR.replace('n_c',{
	height: '5em',
	toolbar: 'Basic',
	removePlugins: 'backup,floating-tools,elementspath,back-up',
	resize_enabled: false
});
var completion = 0;
function openModal(){
	$("#saveCont").modal('show');
	return false;
}
</script> 
