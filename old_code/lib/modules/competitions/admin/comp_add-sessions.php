<?php
if(!isset($_GET['m'])) $page->errorCode = 404;
include_once($_SERVER['DOCUMENT_ROOT'] . "/lib/modules/meet.php");
$meet = new Meet($mySQL);
$meet->setID($_GET['m']);
if($meet->createMeet()===false) $page->errorCode = 404;

$dates = $meet->getDates();
$page->setTitle($meet->getTitle().' - Add Sessions');
$page->createTitle();
print $page->getHeader();
if(isset($_GET['msg'])){
	$alert = new Alert();
	$alert->printAlerts();
}
if(!isset($_GET['s'])){
	$_GET['s'] = 1;
}elseif($_GET['s']<1){
	$_GET['s'] = 1;
}
?>
<script src="/SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="/SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css">


<div class="row pane">
  <div class="col-sm-12 col-md-11 col-lg-11 col-lg-offset-1 col-md-offset-1">
    <form class="form-horizontal" id="comp_add" method="post" action="/old_code/act/comp_add-session">
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
                <div id="progress-bar" class="progress-bar" role="progressbar" aria-valuenow="42" aria-valuemin="0" aria-valuemax="100" style="width: 59%"> <span id="progress-bar-sr" class="sr-only">42% Complete</span> </div>
              </div>
            </div>
          </div>
          <div class="row text-center">
            <div class="col-xs-2"> <span class="text-<?php print B_T_MUTED ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
            <div class="col-xs-2"> <span class="text-<?php print B_T_MUTED ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
            <div class="col-xs-2"> <span class="text-<?php print B_T_MUTED ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
            <div class="col-xs-2"> <span class="text-<?php print B_T_PRIMARY ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
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
            <div class="col-xs-2 text-<?php print B_T_MUTED ?>">
              <p>Add Documents</p>
            </div>
            <div class="col-xs-2 text-<?php print B_T_PRIMARY ?>">
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
        <div class="col-sm-3">
          <a tabindex="<?php $tabindex++;print $tabindex;?>" class="btn btn-large btn-<?php print B_T_DEFAULT ?> btn-block" href="/admin/competitions/comp_view">Close&nbsp;&nbsp;&nbsp;<span class="<?php print B_ICON.' '.B_ICON ?>-remove-sign"></span></a>
        </div>
        <div class="col-sm-3">
          <button tabindex="<?php $tabindex++;print $tabindex;?>" class="btn btn-large btn-<?php print B_T_FAIL ?> btn-block" onClick="window.location.replace('/admin/competitions/comp_add-sessions?m=<?php print $_GET['m']?>&s=<?php print ($_GET['s']-1); ?>');return false;" <?php if($_GET['s']<=1){ print 'disabled="disabled"';} ?>>Remove Session&nbsp;&nbsp;&nbsp;<span class="<?php print B_ICON.' '.B_ICON ?>-minus-sign"></span></button>
        </div>
        <div class="col-sm-3">
          <button tabindex="<?php $tabindex++;print $tabindex;?>" class="btn btn-large btn-<?php print B_T_PRIMARY ?> btn-block" onClick="window.location.replace('/admin/competitions/comp_add-sessions?m=<?php print $_GET['m']?>&s=<?php print ($_GET['s']+1); ?>');return false;">Add Session&nbsp;&nbsp;&nbsp;<span class="<?php print B_ICON.' '.B_ICON ?>-plus-sign"></span></button>
        </div>
        <div class="col-sm-3">
          <button onClick="return openModal()" id="saveContBtn" class="btn btn-large btn-<?php print B_T_SUCCESS ?> btn-block" type="submit">Save Sessions&nbsp;&nbsp;&nbsp;<span class="<?php print B_ICON.' '.B_ICON ?>-floppy-disk"></span></button>
        </div>
      </div>
      <div class="row">
<?php
for($num=1;$num<=$_GET['s'];$num++){
	print('        <div class="col-md-6">'.PHP_EOL);
	print('          <fieldset id="session'.$num.'">'.PHP_EOL);
	print('            <legend>Session '.$num.'</legend>'.PHP_EOL);
	print('            <div class="form-group">'.PHP_EOL);
	print('              <span id="s_s'.$num.'_n">'.PHP_EOL);
	print('                <div class="col-sm-6">'.PHP_EOL);
	print('                  <label for="s['.$num.'][n]">Session Number'.PHP_EOL);
	Tooltip::helpTooltip('Session Number. (Required)');
	print('                  </label>'.PHP_EOL);
	print('                </div>'.PHP_EOL);
	print('                <span class="textfieldRequiredMsg"></span>'.PHP_EOL);
	print('                <div class="col-sm-6">'.PHP_EOL);
	print('                  <input type="number" class="form-control" name="s['.$num.'][n]"  value="'.$num.'" />'.PHP_EOL);
	print('                </div>'.PHP_EOL);
	print('              </span>'.PHP_EOL);
	print('            </div>'.PHP_EOL);
	print('            <div class="form-group">'.PHP_EOL);
	print('              <span id="s_s'.$num.'_d">'.PHP_EOL);
	print('                <div class="col-sm-6">'.PHP_EOL);
	print('                  <label for="s['.$num.'][d]">Date of Session'.PHP_EOL);
	Tooltip::helpTooltip('Date of session. (Required)');
	print('                  </label>'.PHP_EOL);
	print('                </div>'.PHP_EOL);
	print('                <div class="col-sm-6">'.PHP_EOL);
	print('                  <input type="date" class="form-control" name="s['.$num.'][d]" value="');
	if($num==1){print($dates['s']);}elseif($num==$_GET['s']){print($dates['f']);}
	print('"/>'.PHP_EOL);
	print('                </div>'.PHP_EOL);
	print('              </span>'.PHP_EOL);
	print('            </div>'.PHP_EOL);
	print('            <div class="form-group">'.PHP_EOL);
	print('              <span id="s_s'.$num.'_t_w">'.PHP_EOL);
	print('                <div class="col-sm-6">'.PHP_EOL);
	print('                  <label for="s['.$num.'][t][w]">Warm Up Time'.PHP_EOL);
	Tooltip::helpTooltip('Time warm up starts. (Required)');
	print('                  </label>'.PHP_EOL);
	print('                </div>'.PHP_EOL);
	print('                <div class="col-sm-6">'.PHP_EOL);
	print('                  <input type="time" class="form-control" name="s['.$num.'][t][w]"  value="18:00" />'.PHP_EOL);
	print('                </div>'.PHP_EOL);
	print('              </span>'.PHP_EOL);
	print('            </div>'.PHP_EOL);
	print('            <div class="form-group">'.PHP_EOL);
	print('              <span id="s_s'.$num.'_t_i">'.PHP_EOL);
	print('                 <div class="col-sm-6">'.PHP_EOL);
	print('                  <label for="s['.$num.'][t][i]">Sign In By'.PHP_EOL);
	Tooltip::helpTooltip('Time swimmers must be signed in by. (Required)');
	print('                  </label>'.PHP_EOL);
	print('                </div>'.PHP_EOL);
	print('                <div class="col-sm-6">'.PHP_EOL);
	print('                  <input type="time" class="form-control" name="s['.$num.'][t][i]"  value="18:15" />'.PHP_EOL);
	print('                </div>'.PHP_EOL);
	print('              </span>'.PHP_EOL);
	print('            </div>'.PHP_EOL);
	print('            <div class="form-group">'.PHP_EOL);
	print('              <span id="s_s'.$num.'_t_s">'.PHP_EOL);
	print('                <div class="col-sm-6">'.PHP_EOL);
	print('                  <label for="s['.$num.'][t][s]">Start Time'.PHP_EOL);
	Tooltip::helpTooltip('Time session starts. (Required)');
	print('                  </label>'.PHP_EOL);
	print('                </div>'.PHP_EOL);
	print('                <div class="col-sm-6">'.PHP_EOL);
	print('                  <input type="time" class="form-control" name="s['.$num.'][t][s]"  value="19:00" />'.PHP_EOL);
	print('                </div>'.PHP_EOL);
	print('              </span>'.PHP_EOL);
	print('            </div>'.PHP_EOL);
	print('          </fieldset>'.PHP_EOL);
	print('        </div>'.PHP_EOL);
}
?>
      </div>
      <?php
	  $deleteConf = new Modal();
	  $deleteConf->setID('saveCont');
	  $deleteConf->setBody('Do you wish to save and contiune to add events or save and return to the View Competitions page?');
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
function openModal(){
	if(Spry.Widget.Form.validate(document.getElementById("comp_add"))){
		$("#saveCont").modal('show');
	}
	return false;
}
<?php
for($num=1;$num<=$_GET['s'];$num++){
	print('var s_s_'.$num.'_number = new Spry.Widget.ValidationTextField("s_s'.$num.'_n", "integer", {validateOn:["blur","change"]});'.PHP_EOL);
	print('var s_s_'.$num.'_d = new Spry.Widget.ValidationTextField("s_s'.$num.'_d", "date", {validateOn:["blur","change"], format:"yyyy-mm-dd"});'.PHP_EOL);
	print('var s_s_'.$num.'_t_w = new Spry.Widget.ValidationTextField("s_s'.$num.'_t_w", "time", {validateOn:["blur","change"]});'.PHP_EOL);
	print('var s_s_'.$num.'_t_i = new Spry.Widget.ValidationTextField("s_s'.$num.'_t_i", "time", {validateOn:["blur","change"]});'.PHP_EOL);
	print('var s_s_'.$num.'_t_s = new Spry.Widget.ValidationTextField("s_s'.$num.'_t_s", "time", {validateOn:["blur","change"]});'.PHP_EOL);
}
?>
</script> 