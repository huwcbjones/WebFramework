<?php
if(!isset($_GET['m'])) $page->errorCode = 404;
include_once($_SERVER['DOCUMENT_ROOT'] . "/lib/modules/meet.php");
$meet = new Meet($mySQL);
$meet->setID($_GET['m']);
if($meet->createMeet()===false) $page->errorCode = 404;

$page->setTitle($meet->getTitle().' - Add Events');
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
    <form class="form-horizontal" id="comp_add" method="post" action="/old_code/act/comp_add-events">
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
                    <div id="progress-bar" class="progress-bar" role="progressbar" aria-valuenow="56" aria-valuemin="0" aria-valuemax="100" style="width: 76%"> <span id="progress-bar-sr" class="sr-only">56% Complete</span> </div>
                  </div>
                </div>
              </div>
              <div class="row text-center">
                <div class="col-xs-2"> <span class="text-<?php print B_T_MUTED ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
                <div class="col-xs-2"> <span class="text-<?php print B_T_MUTED ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
                <div class="col-xs-2"> <span class="text-<?php print B_T_MUTED ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
                <div class="col-xs-2"> <span class="text-<?php print B_T_MUTED ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
                <div class="col-xs-2"> <span class="text-<?php print B_T_PRIMARY ?> <?php print B_ICON.' '.B_ICON ?>-record"></span> </div>
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
                <div class="col-xs-2 text-<?php print B_T_MUTED ?>">
                  <p>Add Sessions</p>
                </div>
                <div class="col-xs-2 text-<?php print B_T_PRIMARY ?>">
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
              <a data-toggle="modal" href="#delEvt" tabindex="<?php $tabindex++;print $tabindex;?>" class="btn btn-large btn-<?php print B_T_FAIL ?> btn-block" <?php if($_GET['e']<=1){ print 'disabled="disabled"';} ?>>Remove Event&nbsp;&nbsp;&nbsp;<span class="<?php print B_ICON.' '.B_ICON ?>-minus-sign"></span></a>
            </div>
            <div class="col-sm-3">
              <a data-toggle="modal" href="#addEvt" tabindex="<?php $tabindex++;print $tabindex;?>" class="btn btn-large btn-<?php print B_T_PRIMARY ?> btn-block">Add Event&nbsp;&nbsp;&nbsp;<span class="<?php print B_ICON.' '.B_ICON ?>-plus-sign"></span></a>
            </div>
            <div class="col-sm-3">
              <button onClick="return openModal()" id="saveContBtn" class="btn btn-large btn-<?php print B_T_SUCCESS ?> btn-block" type="submit">Save Events&nbsp;&nbsp;&nbsp;<span class="<?php print B_ICON.' '.B_ICON ?>-floppy-disk"></span></button>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">
<?php
print('          <div class="form-group">'.PHP_EOL);
print('            <div class="col-xs-1"><p><b><abbr title="Session Number">S Num</abbr> ');
Tooltip::helpTooltip('Session Number (Required)');
print('</b></p>'.PHP_EOL);
print('            </div>'.PHP_EOL);
print('            <div class="col-xs-1"><p><b><abbr title="Event Number">Num</abbr> ');
Tooltip::helpTooltip('Event Number (Required)');
print('</b></p>'.PHP_EOL);
print('            </div>'.PHP_EOL);
print('            <div class="col-xs-2"><p><b><abbr title="Gender">Gen</abbr> ');
Tooltip::helpTooltip('Gender (Required)');
print('</b></p>'.PHP_EOL);
print('            </div>'.PHP_EOL);
print('            <div class="col-xs-2"><p><b>Distance ');
Tooltip::helpTooltip('Distance of Event (In metres) (Required)');
print('</b></p>'.PHP_EOL);
print('            </div>'.PHP_EOL);
print('            <div class="col-xs-2"><p><b>Stroke ');
Tooltip::helpTooltip('Event Stroke (Required)');
print('</b></p>'.PHP_EOL);
print('            </div>'.PHP_EOL);
print('            <div class="col-xs-2"><p><b>Round ');
Tooltip::helpTooltip('Round of Event (Required)');
print('</b></p>'.PHP_EOL);
print('            </div>'.PHP_EOL);
print('            <div class="col-xs-1"><p><b><abbr title="Lower Age">L Age</abbr> ');
Tooltip::helpTooltip('Lower Age Limit (0 = Unlimited) (Required)');
print('</b></p>'.PHP_EOL);
print('            </div>'.PHP_EOL);
print('            <div class="col-xs-1"><p><b><abbr title="Upper Age">U Age</abbr> ');
Tooltip::helpTooltip('Upper Age Limit (0 = Unlimited) (Required)');
print('</b></p>'.PHP_EOL);
print('            </div>'.PHP_EOL);
print('          </div>'.PHP_EOL);
$ses_query = $mySQL['r']->prepare("SELECT `SID`,`number` FROM `comp_session` WHERE `MID`=? ORDER BY `number` ASC");
$ses_query->bind_param('s',$_GET['m']);
$ses_query->execute();
$ses_query->store_result();
$sessions = array();
if($ses_query->num_rows!=0){
	$ses_query->bind_result($SID,$number);
	while($ses_query->fetch()){
		$sessions[$SID] = $number;
	}
}
for($num=1;$num<=$_GET['e'];$num++){
	print('          <div class="form-group">'.PHP_EOL);
	print('            <div class="col-xs-1"><span id="s_e'.$num.'_n_s">'.PHP_EOL);
	print('              <select class="form-control" name="e['.$num.'][n][s]">'.PHP_EOL);
	foreach($sessions as $ID=>$number){
		print('                <option value="'.$ID.'">'.$number.'</option>'.PHP_EOL);
	}
	print('              </select>'.PHP_EOL);
	print('            </span></div>'.PHP_EOL);
	print('            <div class="col-xs-1"><span id="s_e'.$num.'_n_e">'.PHP_EOL);
	print('              <input class="form-control" type="number" name="e['.$num.'][n][e]" value="'.$num.'" min="1">'.PHP_EOL);
	print('            </span></div>'.PHP_EOL);
	print('            <div class="col-xs-2"><span id="s_e'.$num.'_g">'.PHP_EOL);
	print('              <select class="form-control" name="e['.$num.'][g]">'.PHP_EOL);
	foreach($meet->options['gender'] as $key=>$value){
		print('                <option value="'.$key.'"');
		if($num%2==1&&$key=='f') print ' selected';
		if($num%2==0&&$key=='m') print ' selected';
		print('>'.$value.'</option>'.PHP_EOL);
	}
	print('              </select>'.PHP_EOL);
	print('            </span></div>'.PHP_EOL);
	print('            <div class="col-xs-2"><span id="s_e'.$num.'_d">'.PHP_EOL);
	print('              <select class="form-control" name="e['.$num.'][d]">'.PHP_EOL);
	print('                <option value="-1" selected disabled>Distance</option>'.PHP_EOL);
	foreach($meet->options['distances'] as $type=>$distance){
		print('                <optgroup label="'.$type.'">'.PHP_EOL);
		foreach($distance as $dist){
			print('                  <option value="'.$dist.'">'.$dist.'m</option>'.PHP_EOL);
		}
		print('                </optgroup>'.PHP_EOL);
	}
	print('              </select>'.PHP_EOL);
	print('            </span></div>'.PHP_EOL);
	print('            <div class="col-xs-2"><span id="s_e'.$num.'_s">'.PHP_EOL);
	print('              <select class="form-control" name="e['.$num.'][s]">'.PHP_EOL);
	print('                <option value="-1" selected disabled>Stroke</option>'.PHP_EOL);
	foreach($meet->options['strokes'] as $type=>$stroke){
		print('                <optgroup label="'.$type.'">'.PHP_EOL);
		foreach($stroke as $v=>$s){
			print('                  <option value="'.$v.'">'.$s.'</option>'.PHP_EOL);
		}
		print('                </optgroup>'.PHP_EOL);
	}
	print('              </select>'.PHP_EOL);
	print('            </span></div>'.PHP_EOL);
	print('            <div class="col-xs-2"><span id="s_e'.$num.'_r">'.PHP_EOL);
	print('              <select class="form-control" name="e['.$num.'][r]">'.PHP_EOL);
	print('                <option value="-1" selected disabled>Round</option>'.PHP_EOL);
	foreach($meet->options['rounds'] as $v=>$round){
		print('                <option value="'.$v.'">'.$round.'</option>'.PHP_EOL);
	}
	print('              </select>'.PHP_EOL);
	print('            </span></div>'.PHP_EOL);
	print('            <div class="col-xs-1"><span id="s_e'.$num.'_a_l">'.PHP_EOL);
	print('              <input type="number" class="form-control" name="e['.$num.'][a][l]" value="0">'.PHP_EOL);
	print('            </span></div>'.PHP_EOL);
	print('            <div class="col-xs-1"><span id="s_e'.$num.'_a_u">'.PHP_EOL);
	print('              <input type="number" class="form-control" name="e['.$num.'][a][u]" value="0">'.PHP_EOL);
	print('            </span></div>'.PHP_EOL);
	print('          </div>'.PHP_EOL);
}
?>
        </div>
      </div>
<?php
	$deleteConf = new Modal();
	$deleteConf->setID('saveCont');
	$deleteConf->setBody('Do you wish to save and continue to set up results service or save and return to the View Competitions page?');
	$deleteConf->setTitle('Save?');
	$deleteConf->setCentre('default','Save &amp; Close','floppy-disk','button','$(\'#saveOpt\').val(\'back\');document.getElementById(\'comp_add\').submit();');
	$deleteConf->setRight('success','Save &amp; Continue','floppy-save','submit','$(\'#saveOpt\').val(\'cont\');');
	print $deleteConf->getModal();
	
	$addEvt = new Modal();
	$addEvt->setID('addEvt');
	if((int)$_GET['e']>50){
		$delEvt = 10;
	}elseif((int)$_GET['e']<5){
		$delEvt = 2;
	}else{
		$delEvt = 5;
	}
	$addEvt->setBody('<label>How many events do you wish to add?</label><input class="form-control" type="number" value="5" id="addNum"/>');
	$addEvt->setTitle('Add Events');
	$addEvt->setCentre(B_T_PRIMARY,'Add One Event','plus-sign','button','addEvt(1)');
	$addEvt->setRight(B_T_PRIMARY,'Add Events','plus-sign','button','addEvt($(\'#addNum\').val())');
	print $addEvt->getModal();
	
	$addEvt = new Modal();
	$addEvt->setID('delEvt');
	$addEvt->setBody('<label>How many events do you wish to remove?</label><input class="form-control" type="number" value="'.$delEvt.'" id="delNum"/>');
	$addEvt->setTitle('Remove Events');
	$addEvt->setCentre(B_T_FAIL,'Remove All Events','minus-sign','button','delEvt('.$_GET['e'].')');
	$addEvt->setRight(B_T_WARNING,'Remove Event(s)','minus-sign','button','delEvt($(\'#delNum\').val())');
	print $addEvt->getModal();
?>
      <input type="hidden" name="ID" value="<?php print $_GET['m']?>"/>
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
function addEvt(num){
	num = <?php print $_GET['e']?>+parseInt(num);
	window.location.replace('/admin/competitions/comp_add-events?m=<?php print $_GET['m']?>&e='+num);
}
function delEvt(num){
	num = <?php print $_GET['e']?>-parseInt(num);
	if(num<1) num =1;
	window.location.replace('/admin/competitions/comp_add-events?m=<?php print $_GET['m']?>&e='+num);
}
<?php
for($num=1;$num<=$_GET['e'];$num++){
	print('var s_e'.$num.'_n_s = new Spry.Widget.ValidationSelect("s_e'.$num.'_n_s", {validateOn:["blur","change"]});'.PHP_EOL);
	print('var s_e'.$num.'_n_e = new Spry.Widget.ValidationTextField("s_e'.$num.'_n_e", "integer", {validateOn:["blur","change"]});'.PHP_EOL);
	print('var s_e'.$num.'_g = new Spry.Widget.ValidationSelect("s_e'.$num.'_g", {validateOn:["blur","change"]});'.PHP_EOL);
	print('var s_e'.$num.'_d = new Spry.Widget.ValidationSelect("s_e'.$num.'_d", {validateOn:["blur","change"]});'.PHP_EOL);
	print('var s_e'.$num.'_s = new Spry.Widget.ValidationSelect("s_e'.$num.'_s", {validateOn:["blur","change"]});'.PHP_EOL);
	print('var s_e'.$num.'_r = new Spry.Widget.ValidationSelect("s_e'.$num.'_r", {validateOn:["blur","change"]});'.PHP_EOL);
	print('var s_e'.$num.'_a_l = new Spry.Widget.ValidationTextField("s_e'.$num.'_a_l", "integer", {validateOn:["blur","change"]});'.PHP_EOL);
	print('var s_e'.$num.'_a_u = new Spry.Widget.ValidationTextField("s_e'.$num.'_a_u", "integer", {validateOn:["blur","change"]});'.PHP_EOL);
}
?>
</script> 