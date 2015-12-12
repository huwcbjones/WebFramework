<?php
$page->createTitle();
print $page->getHeader();
if(isset($_GET['e'])){
	$events_query = $mySQL['r']->prepare("SELECT `ID`,`title`,`starts`,`ends`,`notes`,`link`,`enable`,`location`,`allDay` FROM `news_events` WHERE `ID`=?");
	$events_query->bind_param('i',$_GET['e']);
	$events_query->execute();
	$events_query->bind_result($ID,$title,$starts,$finish,$details,$link,$enabled,$location,$allDay);
	$events_query->store_result();
	if($events_query->num_rows==1){
		$events_query->fetch();
	}else{
		$page->errorCode = 404;
	}
}else{
	$page->errorCode = 404;
}
if(isset($_GET['msg'])){
	$alert = new Alert();
	$alert->printAlerts();
}

include_once($_SERVER['DOCUMENT_ROOT']."/lib/modules/location.php");
$loca = new Location($mySQL);
$loca->getLocations();
$locations = array();
foreach($loca->location as $ID){
	$loca->getLocation($ID);
	$locations[$ID]['v'] = $ID;
	$locations[$ID]['n'] = $loca->name.' ('.$loca->address['city'].')';
	if(substr($location,1,strlen($location)-1)==$ID){
		$locations[$ID]['s'] = true;
	}else{
		$locations[$ID]['s'] = false;
	}
	$locations[$ID]['d'] = 0;
}
$locations['other']['v'] = 'o';
$locations['other']['n'] = 'Other Location...';
if(substr($location,0,1)!='%'){
	$locations['other']['s'] = true;
}else{
	$locations['other']['s'] = false;
}
$locations['other']['d'] = 0;


$closeBtn = array('a'=>array('t'=>'url', 'a'=>'event_view'), 'ic'=>'remove-sign');
$saveBtn = array('s'=>B_T_SUCCESS, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(this, \'save\')'), 'ic'=>'floppy-disk');
$applyBtn = array('s'=>B_T_PRIMARY, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(this, \'apply\')'), 'ic'=>'ok-sign');
$action = 'https://'.$_SERVER['HTTP_HOST'].'/act/event_edit';

$allDayBtns = '$("#btn-alldayY").click(function(){'.PHP_EOL;
$allDayBtns.= '  $("#event_edit\\\\:\\\\:start_t").attr("disabled", "disabled");'.PHP_EOL;
$allDayBtns.= '  $("#event_edit\\\\:\\\\:finish_t").attr("disabled", "disabled");'.PHP_EOL;
$allDayBtns.= '});'.PHP_EOL;
$allDayBtns.= '$("#btn-alldayN").click(function(){'.PHP_EOL;
$allDayBtns.= '  $("#event_edit\\\\:\\\\:start_t").removeAttr("disabled");'.PHP_EOL;
$allDayBtns.= '  $("#event_edit\\\\:\\\\:finish_t").removeAttr("disabled");'.PHP_EOL;
$allDayBtns.= '});'.PHP_EOL;

$locationScript = 'var jQloc_s = $("#event_edit\\\\:\\\\:loc_s");'.PHP_EOL;
$locationScript.= 'jQloc_s.change(function(){'.PHP_EOL;
$locationScript.= '    if(jQloc_s.val()=="o"){'.PHP_EOL;
$locationScript.= '        $("#event_edit\\\\:\\\\:loc_t").parent().parent().removeClass("hidden");'.PHP_EOL;
$locationScript.= '        $("#event_edit\\\\:\\\\:loc_t").val("");	'.PHP_EOL;	
$locationScript.= '    }else{'.PHP_EOL;
$locationScript.= '        $("#event_edit\\\\:\\\\:loc_t").parent().parent().addClass("hidden");'.PHP_EOL;
$locationScript.= '        $("#event_edit\\\\:\\\\:loc_t").val("%"+jQloc_s.val()+"%");'.PHP_EOL;
$locationScript.= '    }'.PHP_EOL;
$locationScript.= '});'.PHP_EOL;

$form = new Form('event_edit',  $action, 'post');
$form->setIndent('    ');
$form->addScript($allDayBtns);
$form->addScript($locationScript);
$form->addBtnLine(array('close'=>$closeBtn, 'save'=>$saveBtn, 'apply'=>$applyBtn));
$form->addTextField(
	'ID',
	'id',
	$ID,
	array(
		't'=>'ID of Event',
	),
	array(
		'ro'=>true
	)
);
$form->addTextField(
	'Title',
	'title',
	$title,
	array(
		't'=>'Title of Event (Unique)',
		'p'=>'Event Title'
	),
	array(
		'r'=>true,
		'v'=>true,
		'vm'=>array(
			'textfieldRequiredMsg'=>array('m'=>'An Event Title is required.', 's'=>B_T_FAIL),
			'textfieldMinCharsMsg'=>array('m'=>'An Event Title is required.', 's'=>B_T_FAIL),
			'textfieldMaxCharsMsg'=>array('m'=>'Event title is limited to 100 chars.', 's'=>B_T_FAIL)
		),
		'vo'=>'minChars: 0, maxChars: 100, validateOn:["blur"]'
	)
);		
$form->addButtonGroup(
	'Published',
	'enable',
	array(
		array(
			'i'=>'enabledY',
			's'=>B_T_SUCCESS,
			'v'=>1,
			'l'=>'Yes <span class="'.B_ICON.' '.B_ICON.'-eye-open"></span>',
			'c'=>$enabled
		),
		array(
			'i'=>'enabledN',
			's'=>B_T_FAIL,
			'v'=>0,
			'l'=>'No <span class="'.B_ICON.' '.B_ICON.'-eye-close"></span>',
			'c'=>not($enabled),
		)
	),
	array('t'=>'Publish an event to view it on the site.')
);
$form->addTextField(
	'Start Date',
	'start_d',
	substr($starts,0,10),
	array('t'=>'Date event starts', 'p'=>'Start Date'),
	array(
		'r'=>true,
		'v'=>true,
		't'=>'date',
		'vm'=>array(
			'textfieldRequiredMsg'=>array('m'=>'An Event Start Date is required.', 's'=>B_T_FAIL),
			'textfieldInvalidFormatMsg'=>array('m'=>'Incorrect date format.', 's'=>B_T_FAIL),
		),
		'vo'=>'validateOn:["blur"], format: "yyyy-mm-dd"'
	)
);
$form->addTextField(
	'Start Time',
	'start_t',
	substr($starts,11),
	array('t'=>'Time event starts', 'p'=>'Start Time'),
	array(
		'r'=>true,
		'v'=>true,
		't'=>'time',
		'vm'=>array(
			'textfieldRequiredMsg'=>array('m'=>'An Event Start Time is required.', 's'=>B_T_FAIL),
			'textfieldInvalidFormatMsg'=>array('m'=>'Incorrect time format.', 's'=>B_T_FAIL),
		),
		'vo'=>'validateOn:["blur"], format: "HH:mm:ss"'
	)
);
$form->addTextField(
	'Finish Date',
	'finish_d',
	substr($finish,0,10),
	array('t'=>'Date event finishes', 'p'=>'Finish Date'),
	array(
		'r'=>true,
		'v'=>true,
		't'=>'date',
		'vm'=>array(
			'textfieldRequiredMsg'=>array('m'=>'An Event Finish Date is required.', 's'=>B_T_FAIL),
			'textfieldInvalidFormatMsg'=>array('m'=>'Incorrect date format.', 's'=>B_T_FAIL),
		),
		'vo'=>'validateOn:["blur"], format: "yyyy-mm-dd"'
	)
);
$form->addTextField(
	'Finish Time',
	'finish_t',
	substr($finish,11),
	array('t'=>'Time event finishes', 'p'=>'Finishes Time'),
	array(
		'r'=>true,
		'v'=>true,
		't'=>'time',
		'vm'=>array(
			'textfieldRequiredMsg'=>array('m'=>'An Event Finish Time is required.', 's'=>B_T_FAIL),
			'textfieldInvalidFormatMsg'=>array('m'=>'Incorrect time format.', 's'=>B_T_FAIL),
		),
		'vo'=>'validateOn:["blur"], format: "HH:mm:ss"'
	)
);
$form->addButtonGroup(
	'All Day',
	'allday',
	array(
		array(
			'i'=>'alldayY',
			's'=>B_T_SUCCESS,
			'v'=>1,
			'l'=>'Yes',
			'c'=>$allDay
		),
		array(
			'i'=>'alldayN',
			's'=>B_T_FAIL,
			'v'=>0,
			'l'=>'No',
			'c'=>not($allDay)
		)
	),
	array('t'=>'Is the event all day?')
);
$form->addSelect(
	'Location',
	'loc_s',
	$locations,
	array('t'=>'The location of the event. Select a stored event, or use an \'other\' location.'),
	array(
		'r'=>true,
		'v'=>true,
		'vo'=>'validateOn:["blur"]',
		'vm'=>array(
			
		)
	)
);
$form->addTextField(
	'Other Location',
	'loc_t',
	$location,
	array('t'=>'Enter an \'Other\' location here'),
	array(
		'classes'=>array(
			'hidden'
		)
	)
);
$form->addTextArea(
	'Details',
	'details',
	$details,
	6,
	array('t'=>'Details for the event (Recommended unless linking event)', 'p'=>'Details to follow here...'),
	array(
		'vm'=>array(
			'textareaMaxCharsMsg'=>array('m'=>'Event details are limited to 500 chars.', 's'=>B_T_FAIL)
		),
		'vo'=>'maxChars: 500, useCharacterMasking:false, validateOn:["blur", "change"]',
		'c'=>true,
		'v'=>true,
		'c'=>true
	)
);
$form->addTextField(
	'Link',
	'link',
	$link,
	array('t'=>'Link to event details when user clicks on event in calendar', 'p'=>'http(s)://www.example.com'),
	array(
		'r'=>false,
		'v'=>true,
		't'=>'url',
		'vm'=>array(
			'textfieldInvalidFormatMsg'=>array('m'=>'Incorrect link format.', 's'=>B_T_FAIL),
		),
		'vo'=>'validateOn:["blur", "change"]'
	)
);
$form->addBtnLine(array('close'=>$closeBtn, 'save'=>$saveBtn, 'apply'=>$applyBtn));
$form->build();		
?>

<div class="row pane">
  <div class="col-sm-12 col-md-11 col-lg-11 col-lg-offset-1 col-md-offset-1">
<?php print $form->getForm() ?>
  </div>
</div>