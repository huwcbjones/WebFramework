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
$options['order']['name'] = 'Order';
$options['order']['def'] = 'p';
$options['order']['opt'] = array(
	'p'=>'Date Published',
	'e'=>'Date Edited'
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
$statements['0_where'] = "WHERE `publish`='1'";
if(!WebApp::get('old')){
	$statements['0_where'].=' AND CURDATE() BETWEEN `publish_f` AND `publish_u`';
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
	$col['m'] = $col['m']+3;
	$col['x'] = $col['x']+12;
	if(strlen($data['name'])<=7){
		print '            <div class="col-md-1 col-xs-6 text-right">'.PHP_EOL;
	}else{
		print '            <div class="col-md-2 col-xs-6 text-right">'.PHP_EOL;
		$col['m']++;
	}
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
		//print '            <div class="clearfix"></div>'.PHP_EOL;
		$col['x'] = $col['m'] = 0;
	}elseif($col['x'] == 12){
		print '            <div class="clearfix visible-xs"></div>'.PHP_EOL;
		$col['x'] = 0;
	}
}

print '            <div class="col-md-2 col-md-offset-0 col-sm-3 col-sm-offset-6 col-xs-12">'.PHP_EOL;
print '              <button type="input" onclick="getArticles()" class="btn btn-block btn-primary">View</button>'.PHP_EOL;
print '            </div>'.PHP_EOL;
print '          </div>'.PHP_EOL;
print '        </form>'.PHP_EOL;
print '      </div>'.PHP_EOL;
print '    </div>'.PHP_EOL;
print '  </div>'.PHP_EOL;
print '</div>'.PHP_EOL;
print '<div id="articles">'.PHP_EOL;

ksort($statements);
$statement = implode(' ', $statements);
$article_query = $mySQL_r->prepare("SELECT `ID` FROM `news_articles` ".$statement);
if($article_query===false){
	$page->parent->debug($this::name_space.': Fetch articles failed:');
	$page->parent->debug($this::name_space.':   '.$mySQL_r->error);
	$page->setStatus(500);
}else{
	$article_query->execute();
	$article_query->store_result();
	$article_query->bind_result($article_id);
	
	if($article_query!==false&&$article_query->num_rows!=0){
	
			$numRows_q = $mySQL_r->prepare("SELECT COUNT(*) FROM `news_articles` ".$statements['0_where']);
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
			if($articleResource = $page->getResource('news', array('article'))){
				while($article_query->fetch()){
					if($article = $articleResource->fetchCompetition($article_id)){
						print $article->format('short');
						
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
		print '<div class="row pane">'.PHP_EOL;
		print '  <div class="col-xs-12">'.PHP_EOL;
		print '    <h4>There are no articles to display.</h4>'.PHP_EOL;
		print '  </div>'.PHP_EOL;
		print '</div>'.PHP_EOL;
	}
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