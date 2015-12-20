<?php
if(!isset($_GET['m'])) $page->errorCode = 404;
include_once($_SERVER['DOCUMENT_ROOT'] . "/lib/modules/meet.php");
$meet = new Meet($mySQL);
$meet->setID($_GET['m']);
if($meet->createMeet()===false) $page->errorCode = 404;

$page->setTitle($meet->getTitle().' - Results Service');
$page->createTitle();
print $page->getHeader();
if(isset($_GET['msg'])){
	$alert = new Alert();
	$alert->printAlerts();
}
?>

<div class="row pane">
  <div class="col-sm-12 col-md-11 col-lg-11 col-lg-offset-1 col-md-offset-1">
    <form class="form-horizontal" id="comp_add" method="post" action="/old_code/act/comp_add-res">
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
                <div id="progress-bar" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 93%"> <span id="progress-bar-sr" class="sr-only">90% Complete</span> </div>
              </div>
            </div>
          </div>
          <div class="row text-center">
            <div class="col-xs-2"> <span class="text-<?php print B_T_MUTED ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
            <div class="col-xs-2"> <span class="text-<?php print B_T_MUTED ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
            <div class="col-xs-2"> <span class="text-<?php print B_T_MUTED ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
            <div class="col-xs-2"> <span class="text-<?php print B_T_MUTED ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
            <div class="col-xs-2"> <span class="text-<?php print B_T_MUTED ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
            <div class="col-xs-2"> <span class="text-<?php print B_T_PRIMARY ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
          </div>
          <div class="row text-center">
            <div class="col-xs-2 text-<?php print B_T_MUTED ?>">
              <p>Set-Up Meet</p>
            </div>
            <div class="col-xs-2 text-<?php print B_T_MUTED ?>">
              <p>Add Notes</p>
            </div>
            <div class="col-xs-2 text-<?php print B_T_MUTED ?>">
              <p>Add Documents</p>
            </div>
            <div class="col-xs-2 text-<?php print B_T_MUTED ?>">
              <p>Add Sessions</p>
            </div>
            <div class="col-xs-2 text-<?php print B_T_MUTED ?>">
              <p>Add Events</p>
            </div>
            <div class="col-xs-2 text-<?php print B_T_PRIMARY ?>">
              <p>Res. Service</p>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="col-sm-2">
          <label>Create Results Service</label>
        </div>
        <div class="col-sm-10">
          <div class="btn-group" data-toggle="buttons">
            <label class="btn btn-default" id="btn-en-yes">
              <input type="radio" name="resServ" id="e_enableY" value="1"  tabindex="<?php $tabindex++;print $tabindex;?>"/>  Yes <span class="<?php print B_ICON.' '.B_ICON ?>-ok-sign"></span>
            </label>
            <label class="btn btn-danger active" id="btn-en-no">
              <input type="radio" name="resServ" id="e_enableN" value="0" checked tabindex="<?php $tabindex++;print $tabindex;?>"/> No <span class="<?php print B_ICON.' '.B_ICON ?>-remove-sign"></span>
            </label>
          </div>
        </div>
      </div>
      <div class="form-group">
        <span id="s_meet">
          <div class="col-sm-2">
            <label for="meet">Meet *
              <?php Tooltip::helpTooltip('Meet Catagory. (Required) (Ask the webmaster if you would like another catagory)') ?>
            </label>
            <strong>
              <span class="textfieldRequiredMsg textfieldMinCharsMsg text-danger"><br />Required</span>
            </strong>
          </div>
          <div class="col-sm-10">
            <select class="form-control" name="meet" id="meet" disabled>
              <option value="-1" disabled selected>Meet Catagory</option>
<?php
	foreach($meet->options['res-series'] as $v=>$series){
		print('              <option value="'.$v.'">'.$series.'</option>'.PHP_EOL);
	}
?>
            </select>
          </div>
        </span>
      </div>
      <div class="form-group">
        <span id="s_series">
          <div class="col-sm-2">
            <label for="series">Series
              <?php Tooltip::helpTooltip('The series, normally yyyy or yyyy-dd. (Required)') ?>
            </label>
            <strong>
              <span class="textfieldRequiredMsg text-danger"><br />Required</span>
            </strong>
          </div>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="series" value="<?php print date("Y"); ?>" disabled/>
          </div>
        </span>
      </div>
      <div class="form-group"> <span id="s_n_e">
        <div class="col-sm-2">
          <label for="n_e">Text
            <?php Tooltip::helpTooltip('This textbox allows you to customise the text displayed on the competition results page. (Recommended)') ?>
          </label>
        </div>
        <div class="col-sm-10">
          <textarea class="form-control" id="text" rows="3" disabled></textarea>
        </div>
        </span> </div>
      <div class="form-group">
        <div class="col-sm-2">

        </div>
        <div class="col-sm-10">
          <button class="btn btn-success disabled" id="serviceEnable" tabindex="<?php $tabindex++;print $tabindex;?>">  Enable All Services <span class="<?php print B_ICON.' '.B_ICON ?>-ok-sign"></span></button>
          <button class="btn btn-danger disabled" id="serviceDisable"  tabindex="<?php $tabindex++;print $tabindex;?>">  Disable All Services <span class="<?php print B_ICON.' '.B_ICON ?>-remove-sign"></span></button>
        </div>
      </div>
      <div class="form-group">
        <div class="col-sm-2">
          <label>Download Results
            <?php Tooltip::helpTooltip('Sets download options. If you want the site to create downloads on-the-fly, set to automagic, if you are going to upload PDFs, set to PDF Upload, otherwise, set to disabled.') ?>
          </label>
        </div>
        <div class="col-sm-10">
          <div class="btn-group" data-toggle="buttons">
            <label class="btn btn-default disabled" id="e_r_d-A" disabled>
              <input type="radio" name="e_r_d" value="compile" tabindex="<?php $tabindex++;print $tabindex;?>"/>  Automagically <span class="<?php print B_ICON.' '.B_ICON ?>-cog"></span>
            </label>
            <label class="btn btn-default disabled" id="e_r_d-P" disabled>
              <input type="radio" name="e_r_d" value="pdf" tabindex="<?php $tabindex++;print $tabindex;?>"/>  PDF Upload <span class="<?php print B_ICON.' '.B_ICON ?>-upload"></span>
            </label>
            <label class="btn btn-danger disabled active" id="e_r_d-N" disabled>
              <input type="radio" name="e_r_d" value="off" checked tabindex="<?php $tabindex++;print $tabindex;?>"/> Disabled <span class="<?php print B_ICON.' '.B_ICON ?>-remove-sign"></span>
            </label>
          </div>
        </div>
      </div>
<?php
	foreach($meet->options['res-services'] as $v=>$service){
		$tabindex++;
		print('      <div class="form-group">'.PHP_EOL);
		print('        <div class="col-sm-2">'.PHP_EOL);
		print('          <label>'.$service['title'].PHP_EOL);
		Tooltip::helpTooltip($service['help']);
		print('          </label>'.PHP_EOL);
		print('        </div>'.PHP_EOL);
		print('        <div class="col-sm-10">'.PHP_EOL);
		print('          <div class="btn-group" data-toggle="buttons">'.PHP_EOL);
		print('            <label class="btn btn-success disabled active" id="e_r_s_'.$v.'-Y" disabled>'.PHP_EOL);
		print('              <input type="radio" name="e_r_s['.$v.']" value="1" checked tabindex="'.$tabindex.'"/>  Enabled <span class="'. B_ICON.' '.B_ICON.'-ok-sign"></span>'.PHP_EOL);
		print('            </label>'.PHP_EOL);
		print('            <label class="btn btn-default disabled" id="e_r_s_'.$v.'-N" disabled>'.PHP_EOL);
		$tabindex++;
		print('              <input type="radio" name="e_r_s['.$v.']" value="0" tabindex="'.$tabindex.'"/> Disabled <span class="'.B_ICON.' '.B_ICON.'-remove-sign"></span>'.PHP_EOL);
		print('            </label>'.PHP_EOL);
		print('          </div>'.PHP_EOL);
		print('        </div>'.PHP_EOL);
		print('      </div>'.PHP_EOL);
	}
?>
      
      <div class="form-group">
        <div class="col-sm-6">
          <a tabindex="<?php $tabindex++;print $tabindex;?>" class="btn btn-large btn-default btn-block" href="<?php print $page->prevPage?>">Close&nbsp;&nbsp;&nbsp;<span class="<?php print B_ICON.' '.B_ICON ?>-remove-sign"></span></a>
        </div>
        <div class="col-sm-6">
          <button onClick="return openModal()" id="saveContBtn" class="btn btn-large btn-success btn-block" type="submit">Save Meet&nbsp;&nbsp;&nbsp;<span class="<?php print B_ICON.' '.B_ICON ?>-floppy-disk"></span></button>
        </div>
      </div>
      <input type="hidden" name="meet" id="e_meet" value=""/>
      <input type="hidden" name="series" id="e_series" value=""/>
      <input type="hidden" name="text" id="e_text" value=""/>
      <input type="hidden" name="ID" value="<?php print $_GET['m']?>"/>
      <?php
	  $deleteConf = new Modal();
	  $deleteConf->setID('saveCont');
	  $deleteConf->setBody('Do you wish to save this meet\'s results service settings?');
	  $deleteConf->setTitle('Save?');
	  $deleteConf->setRight('success','Finish','floppy-disk','submit');
	  print $deleteConf->getModal();
?>
    </form>
  </div>
</div>
<script type="text/javascript">
var completion = 0;
function openModal(){
	if(Spry.Widget.Form.validate(document.getElementById("comp_add"))){
		$("#saveCont").modal('show');
	}
	return false;
}
var meet = new Spry.Widget.ValidationSelect("s_meet", {validateOn:["blur", "change"], invalidValue:"*"});
var series = new Spry.Widget.ValidationTextField("s_series", "none", {validateOn:["blur", "change"]});
$("#btn-en-yes").click(function(){
	$("#btn-en-yes").addClass('btn-success');
	$("#btn-en-no").addClass('btn-default');
	$("#btn-en-yes").removeClass('btn-default');
	$("#btn-en-no").removeClass('btn-danger');
	$("#serviceEnable").removeAttr('disabled');
	$("#serviceEnable").removeClass('disabled');
	$("#serviceDisable").removeAttr('disabled');
	$("#serviceDisable").removeClass('disabled');
	$("#e_r_d-P").removeClass('disabled');
	$("#e_r_d-P").removeAttr('disabled');
	$("#e_r_d-A").removeClass('disabled');
	$("#e_r_d-A").removeAttr('disabled');
	$("#e_r_d-N").removeClass('disabled');
	$("#e_r_d-N").removeAttr('disabled');
<?php
	foreach($meet->options['res-services'] as $v=>$service){
		print('	$("#e_r_s_'.$v.'-Y").removeClass(\'disabled\');'.PHP_EOL);
		print('	$("#e_r_s_'.$v.'-Y").removeAttr(\'disabled\');'.PHP_EOL);
		print('	$("#e_r_s_'.$v.'-N").removeClass(\'disabled\');'.PHP_EOL);
		print('	$("#e_r_s_'.$v.'-N").removeAttr(\'disabled\');'.PHP_EOL);
	}
?>
	$("#meet").removeAttr('disabled');
	$("#series").removeAttr('disabled');
	$("#text").removeAttr('disabled');
	$("#e_series").val($("#series").val());
	$("#e_text").val($("#text").val());
});
$("#btn-en-no").click(function(){
	$("#btn-en-yes").addClass('btn-default');
	$("#btn-en-no").addClass('btn-danger');
	$("#btn-en-yes").removeClass('btn-success');
	$("#btn-en-no").removeClass('btn-default');
	$("#serviceEnable").attr('disabled','disabled');
	$("#serviceEnable").addClass('disabled');
	$("#serviceDisable").attr('disabled','disabled');
	$("#serviceDisable").addClass('disabled');
	$("#meet").attr('disabled','disabled');
	$("#series").attr('disabled','disabled');
	$("#text").attr('disabled','disabled');
	$("#e_r_d-P").attr('disabled','disabled');
	$("#e_r_d-P").addClass('disabled');
	$("#e_r_d-A").attr('disabled','disabled');
	$("#e_r_d-A").addClass('disabled');
	$("#e_r_d-N").attr('disabled','disabled');
	$("#e_r_d-N").addClass('disabled');

<?php
	foreach($meet->options['res-services'] as $v=>$service){
		print('	$("#e_r_s_'.$v.'-Y").addClass(\'disabled\');'.PHP_EOL);
		print('	$("#e_r_s_'.$v.'-Y").attr(\'disabled\',\'disabled\');'.PHP_EOL);
		print('	$("#e_r_s_'.$v.'-N").addClass(\'disabled\');'.PHP_EOL);
		print('	$("#e_r_s_'.$v.'-N").attr(\'disabled\',\'disabled\');'.PHP_EOL);
	}
?>
	$("#e_meet").val('');
	$("#e_series").val('');
	$("#e_text").val('');
});
$("#serviceEnable").click(function(){
	$("#serviceEnable").removeClass('btn-success');
	$("#serviceEnable").addClass('btn-default');
	$("#serviceDisable").removeClass('btn-default');
	$("#serviceDisable").addClass('btn-danger');

<?php
	print('	$("#e_r_d-A").click();'.PHP_EOL);
	foreach($meet->options['res-services'] as $v=>$service){

		print('	$("#e_r_s_'.$v.'-Y").click();'.PHP_EOL);
	}
?>
	return false;
});
$("#serviceDisable").click(function(){
	$("#serviceDisable").removeClass('btn-danger');
	$("#serviceDisable").addClass('btn-default');
	$("#serviceEnable").removeClass('btn-default');
	$("#serviceEnable").addClass('btn-success');
<?php
	print('	$("#e_r_d-N").click();'.PHP_EOL);
	foreach($meet->options['res-services'] as $v=>$service){

		print('	$("#e_r_s_'.$v.'-N").click();'.PHP_EOL);
	}
?>
	return false;
});
$("#e_r_d-P").click(function(){
	$("#e_r_d-A").removeClass('btn-success');
	$("#e_r_d-A").removeClass('btn-default');
	$("#e_r_d-P").removeClass('btn-warning');
	$("#e_r_d-P").removeClass('btn-default');
	$("#e_r_d-N").removeClass('btn-danger');
	$("#e_r_d-N").removeClass('btn-default');
	$("#e_r_d-P").addClass('btn-warning');
	$("#e_r_d-A").addClass('btn-default');
	$("#e_r_d-N").addClass('btn-default');
});
$("#e_r_d-A").click(function(){
	$("#e_r_d-A").removeClass('btn-success');
	$("#e_r_d-A").removeClass('btn-default');
	$("#e_r_d-P").removeClass('btn-warning');
	$("#e_r_d-P").removeClass('btn-default');
	$("#e_r_d-N").removeClass('btn-danger');
	$("#e_r_d-N").removeClass('btn-default');
	$("#e_r_d-P").addClass('btn-default');
	$("#e_r_d-A").addClass('btn-success');
	$("#e_r_d-N").addClass('btn-default');
});
$("#e_r_d-N").click(function(){
	$("#e_r_d-A").removeClass('btn-success');
	$("#e_r_d-A").removeClass('btn-default');
	$("#e_r_d-P").removeClass('btn-warning');
	$("#e_r_d-P").removeClass('btn-default');
	$("#e_r_d-N").removeClass('btn-danger');
	$("#e_r_d-N").removeClass('btn-default');
	$("#e_r_d-P").addClass('btn-default');
	$("#e_r_d-A").addClass('btn-default');
	$("#e_r_d-N").addClass('btn-danger');
});
<?php
	foreach($meet->options['res-services'] as $v=>$service){
		print('$("#e_r_s_'.$v.'-Y").click(function(){'.PHP_EOL);
		print('	$("#e_r_s_'.$v.'-Y").addClass(\'btn-success\');'.PHP_EOL);
		print('	$("#e_r_s_'.$v.'-N").addClass(\'btn-default\');'.PHP_EOL);
		print('	$("#e_r_s_'.$v.'-Y").removeClass(\'btn-default\');'.PHP_EOL);
		print('	$("#e_r_s_'.$v.'-N").removeClass(\'btn-danger\');'.PHP_EOL);
		print('});'.PHP_EOL);
		print('$("#e_r_s_'.$v.'-N").click(function(){'.PHP_EOL);
		print('	$("#e_r_s_'.$v.'-Y").addClass(\'btn-default\');'.PHP_EOL);
		print('	$("#e_r_s_'.$v.'-N").addClass(\'btn-danger\');'.PHP_EOL);
		print('	$("#e_r_s_'.$v.'-Y").removeClass(\'btn-success\');'.PHP_EOL);
		print('	$("#e_r_s_'.$v.'-N").removeClass(\'btn-default\');'.PHP_EOL);
		print('});'.PHP_EOL);
	}
?>
$("#meet").change(function(){
	$("#e_meet").val($("#meet").val());
});
$("#series").change(function(){
	$("#e_series").val($("#series").val());
});

</script> 
