<?php
$page->createTitle();
print $page->getHeader();
if(isset($_GET['msg'])){
	$alert = new Alert();
	$alert->printAlerts();
}
include_once($_SERVER['DOCUMENT_ROOT']."/lib/modules/event.php");
$event = new EventItem($mySQL);
$event->setID($_GET['e']);
$event->createData();
if($event->data){
	$event->createEvent();
	print $event->getEvent();
}else{
	print('  <div class="row pane">'.PHP_EOL);
	print('    <div class="col-xs-12">'.PHP_EOL);
	print('      <h2>No event selected to preview!</h2>'.PHP_EOL);
	print('  </div>'.PHP_EOL);
	print('</div>'.PHP_EOL);
}
?>
<div class="row pane">
  <div class="col-xs-12">
    <form class="form-horizontal" action="<?php print $_SERVER['REQUEST_URI']?>" method="get">
      <div class="form-group">
        <div class="col-xs-4 text-right">
          <p><label for="e">Event to Preview <?php Tooltip::helpTooltip('Select the event you wish to preview and then click \'View\'. A <> after the event ID indicates the event is not displaying on the website.') ?></label></p>
        </div>
        <div class="col-xs-8">
          <select name="e" class="form-control">
            <option disabled="disabled" <?php if(!isset($_GET['e'])){print (' selected');} ?>>Select an Event</option>
          <?php
		  $event_query = $mySQL['r']->prepare("SELECT `title`,`ID`,`enable` FROM `news_events` WHERE `link`='' ORDER BY `ID`");
		  $event_query->execute();
		  $event_query->store_result();
		  $event_query->bind_result($title,$ID,$enabled);
		  while($event_query->fetch()){
			  print('            <option value="'.$ID.'"');
			  if(isset($_GET['e'])&&$_GET['e']==$ID){print (' selected');}
			  print('>'.$title.' - '.$ID);
			  if($enabled=="0"){print ' <>';}
			  print('</option>'.PHP_EOL);
		  }
		  $event_query->free_result();
		  ?>
          </select>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-10 col-sm-offset-1">
          <div class="form-group">
            <div class="col-sm-6">
              <a tabindex="<?php $tabindex++;print $tabindex;?>" class="btn btn-large btn-default btn-block" href="<?php print $page->prevPage?>">Close&nbsp;&nbsp;&nbsp;<span class="<?php print B_ICON.' '.B_ICON ?>-remove-sign"></span></a>
            </div>
            <div class="col-sm-6">
              <button class="btn btn-large btn-primary btn-block" type="submit">Preview&nbsp;&nbsp;&nbsp;<span class="<?php print B_ICON.' '.B_ICON ?>-eye-open"></span></button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>