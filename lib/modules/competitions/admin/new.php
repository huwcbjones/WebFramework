<?php
$page->createTitle();
print $page->getHeader();
if(isset($_GET['msg'])){
	$alert = new Alert();
	$alert->printAlerts();
}
?>
<link href="/css/bootstrap.css" type="text/css" />
<div class="row pane">
  <div class="col-sm-12 col-md-11 col-lg-11 col-lg-offset-1 col-md-offset-1">
    <form class="form-horizontal" id="comp_add" method="post" action="/act/comp_add">
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
                <div id="progress-bar" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 7%"> <span id="progress-bar-sr" class="sr-only">0% Complete</span> </div>
              </div>
            </div>
          </div>
          <div class="row text-center">
            <div class="col-xs-2"> <span class="text-<?php print B_T_PRIMARY ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
            <div class="col-xs-2"> <span class="text-<?php print B_T_DEFAULT ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
            <div class="col-xs-2"> <span class="text-<?php print B_T_DEFAULT ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
            <div class="col-xs-2"> <span class="text-<?php print B_T_DEFAULT ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
            <div class="col-xs-2"> <span class="text-<?php print B_T_DEFAULT ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
            <div class="col-xs-2"> <span class="text-<?php print B_T_DEFAULT ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
          </div>
          <div class="row text-center">
            <div class="col-xs-2 text-<?php print B_T_PRIMARY ?>">
              <p>Set-Up Meet</p>
            </div>
            <div class="col-xs-2 text-<?php print B_T_DEFAULT ?>">
              <p>Add Notes</p>
            </div>
            <div class="col-xs-2 text-<?php print B_T_DEFAULT ?>">
              <p>Add Documents</p>
            </div>
            <div class="col-xs-2 text-<?php print B_T_DEFAULT ?>">
              <p>Add Events</p>
            </div>
            <div class="col-xs-2 text-<?php print B_T_DEFAULT ?>">
              <p>Add Sessions</p>
            </div>
            <div class="col-xs-2 text-<?php print B_T_DEFAULT ?>">
              <p>Set-Up Res. Service</p>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group">
        <span id="s_title">
          <div class="col-sm-2">
            <label for="title">Title *
              <?php Tooltip::helpTooltip('Title of Meet. (Required)(Unique)') ?>
            </label>
            <strong>
              <span class="textfieldRequiredMsg textfieldMinCharsMsg text-danger"><br />Required</span>
            </strong>
          </div>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="title" id="title" placeholder="Meet Title" />
          </div>
        </span>
      </div>
      <div class="form-group">
        <span id="s_d_start">
          <div class="col-sm-2">
            <label for="d_start">Start Date *
              <?php Tooltip::helpTooltip('The first day of the meet. (Required)') ?>
            </label>
            <strong>
              <span class="textfieldRequiredMsg text-danger"><br />Required</span>
            </strong>
          </div>
          <div class="col-sm-10">
            <input type="date" class="form-control" name="d_start" id="d_start" value="<?php print date("Y-m-d",strtotime('1 month')); ?>"/>
          </div>
        </span>
      </div>
      <div class="form-group">
        <span id="s_d_finish">
          <div class="col-sm-2">
            <label for="d_finish">Finish Date *
              <?php Tooltip::helpTooltip('The last day of the meet. (Required)') ?>
            </label>
            <strong>
              <span class="textfieldRequiredMsg text-danger"><br />Required</span>
            </strong>
          </div>
          <div class="col-sm-10">
            <input type="date" class="form-control" name="d_finish" id="d_finish" value="<?php print date("Y-m-d",strtotime('1 month')); ?>"/>
          </div>
        </span>
      </div>
      <div class="form-group">
        <span id="s_location">
          <div class="col-sm-2">
            <label for="location">Location * <?php Tooltip::helpTooltip('The location of the event. Select a stored location, or select \'Custom\' and enter the location in the text box on the right. (Required)') ?></label>
            <strong> <span class="selectRequiredMsg selectInvalidMsg text-danger"> Required</span></strong>
          </div>
          <div class="col-sm-5">
            <select class="form-control" id="loc_s" tabindex="<?php $tabindex++;print $tabindex;?>">
              <option value="-1" selected disabled>Choose a Location</option>
<?php

include_once($_SERVER['DOCUMENT_ROOT']."/lib/modules/location.php");
$loca = new Location($mySQL);
$loca->getLocations();
foreach($loca->location as $ID){
	$loca->getLocation($ID);
	print('              <option value="'.$loca->ID.'">'.$loca->name.' ('.$loca->address['city'].')</option>'.PHP_EOL);
}

?>
             <option value="c">Custom...</option>
           </select>
          </div>
          <div class="col-sm-5">
            <input type="text" class="form-control hidden" name="location" id="location" placeholder="Custom Location" value="<?php print $location?>" tabindex="<?php $tabindex++;print $tabindex;?>"/>
          </div>
        </span>
      </div>
      <div class="form-group">
        <span id="s_licence">
          <div class="col-sm-2">
            <label for="licence">Licence Number
              <?php Tooltip::helpTooltip('Licence number for licenced competitions. (Recommended)') ?>
            </label>
            <strong>
              <span class="textfieldRequiredMsg text-danger"><br />Required</span>
            </strong>
          </div>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="licence" id="licence" placeholder="Licence Number" />
          </div>
          </span>
        </div>
      <div class="form-group">
        <span id="s_d_from">
          <div class="col-sm-2">
            <label for="d_from">Display From *
              <?php Tooltip::helpTooltip('The date the meet is displayed from. (Midnight on that day) (Must be enabled) (Required)') ?>
            </label>
            <strong>
              <span class="textfieldRequiredMsg text-danger"><br />Required</span> 
            </strong>
          </div>
          <div class="col-sm-10">
            <input type="date" class="form-control" name="d_from" id="d_from" placeholder="Display From" value="<?php print date("Y-m-d"); ?>"/>
          </div>
        </span>
      </div>
      <div class="form-group">
        <span id="s_d_until">
          <div class="col-sm-2">
            <label for="d_until">Display Until *
              <?php Tooltip::helpTooltip('The date the meet is displayed until. (23:59:59 on that day) (Must be enabled) (Required)') ?>
            </label>
            <strong> <span class="textfieldRequiredMsg  text-danger"><br />
            Required</span> </strong> </div>
          <div class="col-sm-10">
            <input type="date" class="form-control" name="d_until" id="d_until" value="<?php print date("Y-m-d",strtotime('1 month'));?>" placeholder="Display Until"/>
          </div>
        </span>
      </div>
      <div class="form-group">
        <div class="col-sm-6">
          <a tabindex="<?php $tabindex++;print $tabindex;?>" class="btn btn-large btn-default btn-block" href="<?php print $page->prevPage?>">Close&nbsp;&nbsp;&nbsp;<span class="<?php print B_ICON.' '.B_ICON ?>-remove-sign"></span></a>
        </div>
        <div class="col-sm-6">
          <button onClick="return openModal()" id="saveContBtn" class="btn btn-large btn-success btn-block" type="submit">Save Meet&nbsp;&nbsp;&nbsp;<span class="<?php print B_ICON.' '.B_ICON ?>-floppy-disk"></span></button>
        </div>
      </div>
      <?php
	  $deleteConf = new Modal();
	  $deleteConf->setID('saveCont');
	  $deleteConf->setBody('Do you wish to save this meet and continue to add notes (continue wizard) or save this meet and return to the View Competitions page?');
	  $deleteConf->setTitle('Save?');
	  $deleteConf->setCentre('default','Save &amp; Close','floppy-disk','button','$(\'#saveOpt\').val(\'back\');document.getElementById(\'comp_add\').submit();');
	  $deleteConf->setRight('success','Save &amp; Continue','floppy-save','submit','$(\'#saveOpt\').val(\'cont\');');
	  print $deleteConf->getModal();
?>
      <input type="hidden" name="saveOpt" value="cont" id="saveOpt" />
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
var title = new Spry.Widget.ValidationTextField("s_title", "none", {validateOn:["blur"]});
var licence = new Spry.Widget.ValidationTextField("s_licence", "none", {isRequired:false,validateOn:["blur"]});
var d_from = new Spry.Widget.ValidationTextField("s_d_from", "date", {format:"yyyy-mm-dd", validateOn:["blur", "change"], useCharacterMasking:true});
var d_until = new Spry.Widget.ValidationTextField("s_d_until", "date", {format:"yyyy-mm-dd", validateOn:["blur", "change"], useCharacterMasking:true});
var d_start = new Spry.Widget.ValidationTextField("s_d_start", "date", {format:"yyyy-mm-dd", validateOn:["blur", "change"], useCharacterMasking:true});
var d_finish = new Spry.Widget.ValidationTextField("s_d_finish", "date", {format:"yyyy-mm-dd", validateOn:["blur", "change"], useCharacterMasking:true});
var s_location = new Spry.Widget.ValidationSelect("s_location", {validateOn:["blur", "change"], invalidValue:"-1"});
$("#loc_s").change(function(e){
    if($('#loc_s').val()=='c'){
		$("#location").removeClass('hidden');
		$("#location").val('');		
	}else{
		$("#location").addClass('hidden');
		$("#location").val('%'+$('#loc_s').val()+'%');
	}
});
$("#d_start").change(function(e) {
    if($("#d_start").val()>$("#d_finish").val()){
		$("#d_finish").val($("#d_start").val());
	}
	if($("#d_finish").val()>$("#d_until").val()){
		$("#d_until").val($("#d_finish").val());
	}
});
$("#d_finish").change(function(e) {
    if($("#d_finish").val()>$("#d_start").val()){
		$("#d_start").val($("#d_finish").val());
	}
	$("#d_until").val($("#d_finish").val());
});
</script> 
