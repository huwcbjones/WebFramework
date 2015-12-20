<?php
$page->createTitle();
print $page->getHeader();
if(isset($_GET['msg'])){
	$alert = new Alert();
	$alert->printAlerts();
}
$tabindex=1;
?>

<div class="row pane">
  <div class="col-sm-12 col-md-11 col-lg-11 col-lg-offset-1 col-md-offset-1">
    <form class="form-horizontal" id="event_add" method="post" action="/old_code/act/event_add">
      <div class="form-group">
        <span id="s_e_title">
          <div class="col-sm-2">
            <label for="e_title">Title * <?php Tooltip::helpTooltip('Title of Event. (Required)(Unique)') ?></label>
            <strong>
             <span class="textfieldRequiredMsg textfieldMinCharsMsg text-danger"> Required</span>
            </strong>
          </div>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="e_title" placeholder="Event Title"  tabindex="<?php $tabindex++;print $tabindex;?>"/>
          </div>
        </span>
      </div>
      <div class="form-group">
        <span id="s_s_d">
          <div class="col-sm-2">
            <label for="starts">Starts * <?php Tooltip::helpTooltip('The date/time the event starts (Required)') ?></label>
            <strong>
              <span class=" text-danger textfieldInvalidFormatMsg"><br />Invalid format</span>
            </strong>
          </div>
          <div class="col-sm-6">
            <input type="date" name="start_d" class="form-control" value="<?php print date("Y-m-d") ?>" tabindex="<?php $tabindex++;print $tabindex;?>"/>
          </div>
          <div class="col-sm-4">
            <input type="time" name="start_t" id="start_t" class="form-control" value="<?php print date("H:i") ?>:00"<?php if($allDay){print " disabled";}?> tabindex="<?php $tabindex++;print $tabindex;?>"/>
          </div>
        </span>
      </div>
      <div class="form-group">
        <span id="s_e_d">
          <div class="col-sm-2">
            <label for="ends">Ends * <?php Tooltip::helpTooltip('The date/time the event ends (Required)') ?></label>
            <strong>
              <span class=" text-danger textfieldInvalidFormatMsg"><br />Invalid format</span>
            </strong>
          </div>
          <div class="col-sm-6">
            <input type="date" name="end_d" class="form-control" value="<?php print date("Y-m-d") ?>" tabindex="<?php $tabindex++;print $tabindex;?>"/>
         </div>
          <div class="col-sm-4">
            <input type="time" name="end_t" id="end_t" class="form-control" value="<?php print date("H:i",strtotime('1 hour')) ?>:00" tabindex="<?php $tabindex++;print $tabindex;?>"/>
          </div>
        </span>
      </div>
      <div class="form-group">
        <div class="col-sm-2">
          <label>All Day * <?php Tooltip::helpTooltip('Is the event all day?') ?></label>
        </div>
        <div class="col-sm-10">
          <div class="btn-group" data-toggle="buttons">
            <label class="btn btn-default" id="btn-aD-yes">
              <input type="radio" name="e_allDay" id="e_allDayY" value="1" tabindex="<?php $tabindex++;print $tabindex;?>"/>  Yes
            </label>
            <label class="btn btn-danger" id="btn-aD-no">
              <input type="radio" name="e_allDay" id="e_allDayN" value="0" checked tabindex="<?php $tabindex++;print $tabindex;?>"/> No
            </label>
          </div>
        </div>
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

include_once($_SERVER['DOCUMENT_ROOT'] . "/lib/modules/location.php");
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
        <span id="s_notes">
          <div class="col-sm-2">
            <label for="notes">Details <?php Tooltip::helpTooltip('Enter the details for this event in the box to the right. (Recommended) (Max Chars 500)') ?></label><br />
            <strong>
              <span class="text-info">Number Chars: <span id="counts_notes">&nbsp;</span></span>
              <span class="textareaMaxCharsMsg text-danger"><br />Exceeded maximum number of characters.</span>
            </strong>
          </div>
          <div class="col-sm-10">
            <textarea class="form-control" name="notes" placeholder="Details" rows="4" tabindex="<?php $tabindex++;print $tabindex;?>"><?php print $notes?></textarea>
          </div>
        </span>
      </div>
      <div class="form-group">
        <div class="col-sm-6">
          <a tabindex="<?php $tabindex++;print $tabindex;?>" class="btn btn-large btn-default btn-block" href="<?php print $page->prevPage?>">Close&nbsp;&nbsp;&nbsp;<span class="<?php print B_ICON.' '.B_ICON ?>-remove-sign"></span></a>
        </div>
        <div class="col-sm-6">
          <button tabindex="<?php $tabindex++;print $tabindex;?>" class="btn btn-large btn-success btn-block" type="submit">Add Event&nbsp;&nbsp;&nbsp;<span class="<?php print B_ICON.' '.B_ICON ?>-floppy-disk"></span></button>
        </div>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript">
var s_notes = new Spry.Widget.ValidationTextarea("s_notes", {isRequired: false,validateOn:["blur", "change"], counterId:"counts_notes", minChars:0, maxChars:500, counterType:"chars_count"});
var s_location = new Spry.Widget.ValidationSelect("s_location", {validateOn:["blur", "change"], invalidValue:"-1"});
var e_title = new Spry.Widget.ValidationTextField("s_e_title", "none", {minChars:0, maxChars:100, validateOn:["blur"]});
var e_end_d = new Spry.Widget.ValidationTextField("s_e_d", "date", {format:"yyyy-mm-dd", validateOn:["blur", "change"], useCharacterMasking:true});
var e_start_d = new Spry.Widget.ValidationTextField("s_s_d", "date", {format:"yyyy-mm-dd", validateOn:["blur", "change"], useCharacterMasking:true});
$("#loc_s").change(function(e){
    if($('#loc_s').val()=='c'){
		$("#location").removeClass('hidden');
		$("#location").val('');		
	}else{
		$("#location").addClass('hidden');
		$("#location").val('%'+$('#loc_s').val()+'%');
	}
});
$("#btn-en-yes").click(function(){
	$("#btn-en-yes").addClass('btn-success');
	$("#btn-en-no").addClass('btn-default');
	$("#btn-en-yes").removeClass('btn-default');
	$("#btn-en-no").removeClass('btn-danger');
});
$("#btn-en-no").click(function(){
	$("#btn-en-yes").addClass('btn-default');
	$("#btn-en-no").addClass('btn-danger');
	$("#btn-en-yes").removeClass('btn-success');
	$("#btn-en-no").removeClass('btn-default');
});
$("#btn-aD-yes").click(function(){
	$("#btn-aD-yes").addClass('btn-success');
	$("#btn-aD-no").addClass('btn-default');
	$("#btn-aD-yes").removeClass('btn-default');
	$("#btn-aD-no").removeClass('btn-danger');
	$("#start_t").attr('disabled','disabled');
	$("#end_t").attr('disabled','disabled');
});
$("#btn-aD-no").click(function(){
	$("#btn-aD-yes").addClass('btn-default');
	$("#btn-aD-no").addClass('btn-danger');
	$("#btn-aD-yes").removeClass('btn-success');
	$("#btn-aD-no").removeClass('btn-default');
	$("#start_t").removeAttr('disabled');
	$("#end_t").removeAttr('disabled');
});
</script>
