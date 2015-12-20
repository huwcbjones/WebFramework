<?php
$page->createTitle();
print $page->getHeader();
if(isset($_GET['msg'])){
	$alert = new Alert();
	$alert->printAlerts();
}
require($_SERVER['DOCUMENT_ROOT'] . "/lib/modules/location.php");
?>
<div class="row pane">
  <div class="col-xs-12">
    <div class="row">
<?php
print('        <div class="col-md-2 col-xs-3">'.PHP_EOL);
print('          <a class="btn btn-xs btn-default btn-block" href="/admin/news">Close&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-remove-sign"></span></a>'.PHP_EOL);
print('        </div>'.PHP_EOL);

print('        <div class="col-md-2 col-xs-3">'.PHP_EOL);
if($user->accessPage(52)){
	 print('          <a href="/admin/news/event_add" class="btn btn-xs btn-block btn-success">Add Event&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-plus-sign"></span></a>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

print('        <div class="col-md-2 col-xs-3">'.PHP_EOL);
if($user->accessPage(54)){
	 print('          <a href="/admin/news/event_del" class="btn btn-xs btn-block btn-danger">Delete Event&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-trash"></span></a>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

print('        <div class="col-md-2 hidden-sm hidden-xs">'.PHP_EOL);
if($user->accessPage(55)){
	 print('          <a href="/admin/news/event_preview" class="btn btn-xs btn-block btn-primary">Preview Event&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-eye-open"></span></a>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);


print('        <div class="col-md-2 col-xs-3 pull-right">'.PHP_EOL);
if($user->accessPage(46)){
	 print('          <a href="/admin/news/article_view" class="btn btn-xs btn-block btn-info">View Articles&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-info-sign"></span></a>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);
?>
    </div><br />
    <div class="row">
      <div class="col-xs-12">
        <table class="table table-striped table-hover table-bordered" id="events">
          <thead>
            <th>ID</th>
            <th><abbr title="Published">P</abbr></th>
            <th>Title</th>
            <th>Location</th>
            <th>Starts</th>
            <th>Ends</th>
<?php
if($user->accessPage(53)) print('            <th></th>'.PHP_EOL);
?>
          </thead>
          <tbody>
<?php
$event_query = $mySQL['r']->prepare("SELECT `ID`,`title`,`location`,`starts`,`ends`,`enable` FROM `news_events` ORDER BY `ID`");
$event_query->execute();
$event_query->bind_result($ID,$title,$location,$starts,$ends,$enabled);
$event_query->store_result();
while($event_query->fetch()){
	print '            <tr>'.PHP_EOL;
	print '              <td>'.$ID.'</td>'.PHP_EOL;
	print '              <td><span class="hidden">'.$enabled.'</span>';
	print Form::toggleLink($enabled, '', 53,
		array(
			's'=>array(
				'h'=>'Click to unpublish event.',
				'i'=>'eye-open',
				'u'=>'/act/enable?cat='.$page->getSubCat().'&mode=event&i='.$ID.'&m=0"'
			),
			'f'=>array(
				'h'=>'Click to publish event.',
				'i'=>'eye-close',
				'u'=>'/act/enable?cat='.$page->getSubCat().'&mode=event&i='.$ID.'&m=1"'
			)
		)
	);
	print '</td>'.PHP_EOL;
	print '              <td>'.$title.'</td>'.PHP_EOL;
	if(substr($location,0,1)=='%'&&substr($location,-1,1)=='%'){
		$loca = new Location($mySQL);
		$loca->getLocation(substr($location,1,-1));
		print '              <td>'.$loca->name.'</td>'.PHP_EOL;
	}else{
		print '              <td>'.$location.'</td>'.PHP_EOL;
	}
	
	print '              <td>'.date("d/m/Y H:i",strtotime($starts)).'</td>'.PHP_EOL;
	print '              <td>'.date("d/m/Y H:i",strtotime($ends)).'</td>'.PHP_EOL;
	if($user->accessPage(53)){
		print '              <td><a href="event_edit?e='.$ID.'">Edit <span class="'.B_ICON.' '.B_ICON.'-pencil"></span></a></td>'.PHP_EOL;
	}
	print '            </tr>'.PHP_EOL;
}
?>
          </tbody>
        </table>
        <script src="/js/jquery.tablesorter.js"></script>
		<script src="/js/jquery.tablesorter.widgets.js"></script>
        <script type="text/javascript">
        $(function() {
            $("#events").tablesorter({
                theme : "bootstrap",
                widthFixed: false,
                headerTemplate : '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!
                widgets : [ "uitheme" ],
                headers: {
					4: { dateFormat: "ddmmyyyy" },
					5: { dateFormat: "ddmmyyyy" },
                    6: { sorter: false }
                }
            })
        })
        </script>
      </div>
    </div>
  </div>
</div>