<?php
$page->createTitle();
print $page->getHeader();
if(isset($_GET['msg'])){
	$alert = new Alert();
	$alert->printAlerts();
}
?>
<div class="row pane">
  <div class="col-xs-12">
    <div class="row">
<?php
print('        <div class="col-md-2 col-xs-3">'.PHP_EOL);
print('          <a class="btn btn-xs btn-default btn-block" href="/admin/competitions">Close&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-remove-sign"></span></a>'.PHP_EOL);
print('        </div>'.PHP_EOL);

print('        <div class="col-md-2 col-xs-3">'.PHP_EOL);
if($user->accessPage(58)){
	 print('          <a href="/admin/competitions/comp_add" class="btn btn-xs btn-block btn-success">Add Comp.&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-plus-sign"></span></a>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

print('        <div class="col-md-2 col-xs-3">'.PHP_EOL);
if($user->accessPage(65)){
	 print('          <a href="/admin/competitions/comp_del" class="btn btn-xs btn-block btn-danger">Delete Comp.&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-trash"></span></a>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);


//print('        <div class="col-md-2 col-xs-3 pull-right">'.PHP_EOL);
//if($user->accessPage()){
//	 print('          <a href="/admin/competitions/entry_view" class="btn btn-xs btn-block btn-info">View Entries&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-info-sign"></span></a>'.PHP_EOL);
//}
//print('        </div>'.PHP_EOL);
?>
      </div><br />
    <div class="row">
      <div class="col-xs-4">
        <p><b>Title</b></p>
      </div>
      <div class="col-xs-1">
        <p><abbr title="Number of Sessions"><b><span class="visible-xs visible-sm">S</span></b></abbr><b><span class="hidden-xs hidden-sm">Sessions</span></b></p>
      </div>
      <div class="col-xs-1">
        <p><abbr title="Number of Events"><b><span class="visible-xs visible-sm">E</span><span class="hidden-xs hidden-sm">Events</span></b></abbr></p>
      </div>
      <div class="col-xs-2">
        <p><b>Starts</b></p>
      </div>
      <div class="col-xs-2">
        <p><b>Ends</b></p>
      </div>
      <div class="col-xs-1">
        <p><b><abbr title="Meet Enabled?">En</abbr></b></p>
      </div>
    </div>
    <?php
$meet_query = $mySQL['r']->prepare("SELECT `ID` FROM `comp_meet` ORDER BY `date_f` DESC");
$meet_query->execute();
$meet_query->store_result();
if($meet_query->num_rows!=0){
	include_once("lib/modules/meet.php");
	$meet_query->bind_result($ID);
	$meet = new Meet($mySQL,$page->getPageNumber());
	while($meet_query->fetch()){
		$meet->setID($ID);
		$meet->createMeet();
		$dates=$meet->getDates();
		print('    <div class="row">'.PHP_EOL);
		print('      <div class="col-xs-4">'.PHP_EOL);
		print('        <p>'.$meet->getTitle().'</p>'.PHP_EOL);
		print('      </div>'.PHP_EOL);
		print('      <div class="col-xs-1">'.PHP_EOL);
		print('        <p>'.$meet->getNumberSessions().'</p>'.PHP_EOL);
		print('      </div>'.PHP_EOL);
		print('      <div class="col-xs-1">'.PHP_EOL);
		print('        <p>'.$meet->getNumberEvents().'</p>'.PHP_EOL);
		print('      </div>'.PHP_EOL);
		print('      <div class="col-xs-2">'.PHP_EOL);
		print('        <p>'.date("d/m/Y",strtotime($dates['s'])).'</p>'.PHP_EOL);
		print('      </div>'.PHP_EOL);
		print('      <div class="col-xs-2">'.PHP_EOL);
		print('        <p>'.date("d/m/Y",strtotime($dates['f'])).'</p>'.PHP_EOL);
		print('      </div>'.PHP_EOL);
		print('      <div class="col-xs-1">'.PHP_EOL);
		if($meet->getEnabled()=="1"){
			print('            <p class="text-success">');
			if($user->accessPage(63)){
				print "<a class=\"glyph-tooltip text-success\" data-toggle=\"tooltip\" data-trigger=\"click hover focus\" data-placement=\"bottom\" title=\"Click to disable meet. The meet won't appear on the website\" href=\"/act/enable?cat=".$page->getSubCat()."&mode=comp&i=$ID&m=0\">";
			}
			print("<span class=\"".B_ICON." ".B_ICON."-eye-open\"></span>");
			if($user->accessPage(63)){
				print('</a>');
			}
			print("</p>\n");
		}else{
			print('            <p class="text-danger">');
			if($user->accessPage(63)){
				print('<a class="glyph-tooltip text-danger" data-toggle="tooltip" data-trigger="click hover focus" data-placement="bottom" title="Click to enable meet. The meet will appear on the website." href="/act/enable?cat='.$page->getSubCat().'&mode=comp&i='.$ID.'&m=1">');
			}
			print("<span class=\"".B_ICON." ".B_ICON."-eye-close\"></span>");
			if($user->accessPage(63)){
				print('</a>');
			}
		}
		print('      </div>'.PHP_EOL);
		print('      <div class="col-xs-1">'.PHP_EOL);
		if($user->accessPage(63)&&$meet->wizard==0){
			print('        <div class="dropdown">');
			print('          <a data-toggle="dropdown" href="#">Edit&nbsp;&nbsp;&nbsp;<span class="pull-right '.B_ICON.' '.B_ICON.'-chevron-down"></span></a>'.PHP_EOL);
			print('          <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">'.PHP_EOL);
			foreach(array('','notes','docs','sessions','events','res') as $value){
				print('            <li><a href="/admin/competitions/comp_edit');
				if($value!=''){print('-');}
				print($value);
				print('?m='.$ID.'">');
				print 'Edit';
				if($value!=''){print(' '.ucfirst($value));}else{print(' Meet');}
				print('</a></li>'.PHP_EOL);
			}
			
			print('          </ul>'.PHP_EOL);
			print('        </div>'.PHP_EOL);
		}elseif($user->accessPage(58)&&$meet->wizard!=0){
				print('        <p><a href="/admin/competitions/comp_add-');
				switch($meet->wizard){
				case '1':
					print('notes');break;
				case '2':
					print('docs');break;
				case '3':
					print('sessions');break;
				case '4':
					print('events');break;
				case '5':
					print('res');break;
				}
				print('?m='.$ID.'">');
				print('<abbr title="Continue Wizard">Cont.&nbsp;&nbsp;&nbsp;<span class="pull-right '.B_ICON.' '.B_ICON.'-chevron-right"></span></abbr></a></p>'.PHP_EOL);
		}
		print('      </div>'.PHP_EOL);
		print('    </div>'.PHP_EOL);
		$meet->clear(true);
	}
}
?>
  </div>
</div>