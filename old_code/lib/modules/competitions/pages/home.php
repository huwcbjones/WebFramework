<?php
if(WebApp::get('page')===NULL||!intval(WebApp::get('page'))){
	WebApp::get('page', 1);
}
if(WebApp::get('page')<0){
	WebApp::get('page', 1);
}
$options['disp']['name'] = 'Number to Display';
$options['disp']['def'] = 10;
$options['disp']['opt'] = array(
	2=>2,
	5=>5,
	10=>10,
	20=>20,
	-1=>'All'
);
$options['details']['name'] = 'All Details?';
$options['details']['def'] = 0;
$options['details']['opt'] = array(
	0=>'No',
	1=>'Yes'
);
$options['old']['name'] = 'Old Competitions?';
$options['old']['def'] = 0;
$options['old']['opt'] = array(
	0=>'No',
	1=>'Yes'
);
$options['order']['name'] = 'Order By';
$options['order']['def'] = 'c';
$options['order']['opt'] = array(
	'c'=>'Date Added',
	's'=>'First day of Meet',
	'f'=>'Last day of Meet'
);
$options['sort']['name'] = 'Sort';
$options['sort']['def'] = 'ASC';
$options['sort']['opt'] = array(
	'ASC'=>'Ascending',
	'DESC'=>'Descending'
);


foreach($options as $opt=>$data){
	if(WebApp::get($opt)===NULL){
		WebApp::get($opt, $data['def']);
	}
}
// Don't change the order of the statements to ensure the mySQL executes correctly
$statements = array();
$statements['0_where'] = "WHERE `enable`='1'";
if(!WebApp::get('old')){
	$statements['0_where'].=' AND CURDATE() BETWEEN `disp_f` AND `disp_u`';
}
$statements['1_order'] = 'ORDER BY `date_'.WebApp::get('order').'` '.WebApp::get('sort');

if(WebApp::get('disp')!=-1){
	$statements['2_disp'] = 'LIMIT '.((WebApp::get('page')-1)*WebApp::get('disp')).', '.WebApp::get('disp');
}


$col['x'] = $col['m'] = 0;
print '<div class="row pane">'.PHP_EOL;
print '  <div class="col-xs-12">'.PHP_EOL;
print '    <div class="row">'.PHP_EOL;
print '      <div class="col-xs-12">'.PHP_EOL;
print '        <form id="comp_form" class="form-inline" method="get" action="">'.PHP_EOL;
print '          <div class="row" style="padding-top:10px;">'.PHP_EOL;
foreach($options as $opt=>$data){
	$col['m'] = $col['m']+4;
	$col['x'] = $col['x']+12;
	print '            <div class="col-md-2 col-xs-6 text-right">'.PHP_EOL;
	print '              <label for="disp">'.$data['name'].'</label>'.PHP_EOL;
	print '            </div>'.PHP_EOL;
	print '            <div class="col-md-2 col-xs-6">'.PHP_EOL;
	print '              <select class="form-control" name="'.$opt.'" style="width:100%">'.PHP_EOL;
	
	foreach($data['opt'] as $k=>$v){
		print '                <option value="'.$k.'"';
		if(WebApp::get($opt)==$k) print ' selected="selected"';
		print '>'.$v.'</option>'.PHP_EOL;
	}
	
	print '              </select>'.PHP_EOL;
	print '            </div>'.PHP_EOL;
	if($col['m'] == 12){
		print '            <div class="clearfix"></div>'.PHP_EOL;
		$col['x'] = $col['m'] = 0;
	}elseif($col['x'] == 12){
		print '            <div class="clearfix visible-xs"></div>'.PHP_EOL;
		$col['x'] = 0;
	}
}

print '            <div class="col-md-1 col-md-offset-2 col-sm-3 col-sm-offset-6 col-xs-12">'.PHP_EOL;
print '              <button type="input" onclick="getCompetition()" class="btn btn-block btn-primary">View</button>'.PHP_EOL;
print '            </div>'.PHP_EOL;
print '          </div>'.PHP_EOL;
print '        </form>'.PHP_EOL;
print '      </div>'.PHP_EOL;
print '    </div>'.PHP_EOL;
print '  </div>'.PHP_EOL;
print '</div>'.PHP_EOL;
print '<div id="competitions">'.PHP_EOL;

ksort($statements);
$statement = implode(' ', $statements);
$meet_query = $mySQL_r->prepare("SELECT `ID` FROM `comp_meet` ".$statement);
$meet_query->execute();
$meet_query->store_result();
$meet_query->bind_result($comp_id);
if($meet_query===false){
	$page->parent->debug($this::name_space.': Fetch competitions failed:');
	$page->parent->debug($this::name_space.':   '.$mySQL_r->error_message);
}
if($meet_query!==false&&$meet_query->num_rows!=0){

	if(WebApp::get('details')){
		$numRows_q = $mySQL_r->prepare("SELECT COUNT(*) FROM `comp_meet` ".$statements['0_where']);
		$numRows_q->execute();
		$numRows_q->store_result();
		$numRows_q->bind_result($numRows);
		$numRows_q->fetch();
		if($numRows<WebApp::get('page')){
			WebApp::get('page', $numRows);
		}
		$numRows_q->free_result();
		
		$paginator = $page->getPlugin('paginator');
		$paginator->setItems($numRows);
		$paginator->setItemsPerPage(WebApp::get('disp'));
		$paginator->setPageLink(
			"?disp="	.WebApp::get('disp').
			"&details="	.WebApp::get('details').
			"&old="		.WebApp::get('old').
			"&order="	.WebApp::get('order').
			"&sort="	.WebApp::get('sort')
		);
		$paginator->setCurrentPage(WebApp::get('page'));
		$paginator->createPaginator();
		if($meet = $page->getResource('competitions')){
			while($meet_query->fetch()){
				if($comp = $meet->fetchCompetition($comp_id)){
					print $comp->format();
					
				}
			}
			print $paginator->getPaginator();
		}else{
			print '<div class="row pane">'.PHP_EOL;
			print '  <div class="col-xs-12">'.PHP_EOL;
			print '    <h4>Failed to load competitions.</h4>'.PHP_EOL;
			print '  </div>'.PHP_EOL;
			print '</div>'.PHP_EOL;
		}
	}else{
		$table = $page->getPlugin('table', array('competitions_table'));
		$table
		->setIndent(4)
		->addClass('table-bordered')
		->addClass('table-hover')
		->addClass('table-striped')
		->sort(true)
		->pager(true);
		$table->addHeader(array(
			Table::addTHeadCell('Title', true, '', '', true),
			Table::addTHeadCell('Date(s)'),
			Table::addTHeadCell('Location', true, '', '', true),
			Table::addTHeadCell('Entry Date'),
			Table::addTHeadCell('<abbr title="Number of Sessions"><b><span class="visible-xs visible-sm">S</span><span class="hidden-xs hidden-sm">Sessions</span></b></abbr>', false, '', '', true),
			Table::addTHeadCell('<abbr title="Number of Events"><b><span class="visible-xs visible-sm">E</span><span class="hidden-xs hidden-sm">Events</span></b></abbr>', false, '', '', true),
			Table::addTHeadCell('',false)
		));
		if($meet = $page->getResource('competitions')){
			while($meet_query->fetch()){
				if($comp = $meet->fetchCompetition($comp_id)){
					$table->addRow(array(
						Table::addCell($comp->title),
						Table::addCell('<span class="hidden">'.strtotime($comp->date['start']).'</span>'.$comp->date['long']),
						Table::addCell('<a href="/location/'.$comp->getLocation('ID').'">'.$comp->getLocation('city').'</a>'),
						Table::addCell($comp->date['entry']),
						Table::addCell($comp->data['sessions']),
						Table::addCell($comp->data['events']),
						Table::addCell('<a href="/competitions/meet/'.$comp->ID.'">More...</a>')
					));
				}
			}
			$table->build();
			
			print '<div class="row pane">'.PHP_EOL;
			print '  <div class="col-xs-12">'.PHP_EOL;
			print $table->getTable();
			print '  </div>'.PHP_EOL;
			print '</div>'.PHP_EOL;
		}else{
			print '<div class="row pane">'.PHP_EOL;
			print '  <div class="col-xs-12">'.PHP_EOL;
			print '    <h4>Failed to load competitions.</h4>'.PHP_EOL;
			print '  </div>'.PHP_EOL;
			print '</div>'.PHP_EOL;
		}
	}	
}else{
	print '<div class="row pane">'.PHP_EOL;
	print '  <div class="col-xs-12">'.PHP_EOL;
	print '    <h4>There are no competitions to display.</h4>'.PHP_EOL;
	print '  </div>'.PHP_EOL;
	print '</div>'.PHP_EOL;
}
print '</div>'.PHP_EOL;
?>
<script>
function getCompetition(){
	$.get('/ajax/competition.php?',$("#comp_form").serialize(),function(e){
		$("#results").html(e);
	});
}
</script>