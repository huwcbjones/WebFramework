<div class="row pane">
  <div class="col-xs-12">
    <div class="row">
      <div class="col-xs-12">
        <h2><?php print $page->getTitle() ?></h2>
      </div>
    </div>
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="row">
          <?php
if(!isset($pages_query)){
	$pages_query = $mySQL_r->prepare("SELECT `ID`,`title`,`cat3` FROM `core_pages` WHERE `cat1`=? AND `cat2`=? AND `cat3`!='' AND `cat3` NOT LIKE '%edit%'");
}
$options_query = $mySQL_r->prepare("SELECT `name`,`value` FROM `core_options` WHERE `name`=?");
$pages_query->bind_param('ss',$page->cat_1,$page->cat_2);

$pages_query->execute();
$pages_query->store_result();
$pages_query->bind_result($PID,$ptitle,$pcat);
while($pages_query->fetch()){
	if($user->accessPage($PID)){
		print'      <div class="col-md-3 col-sm-4 col-xs-6"><p><a href="'.$page->cat_2.'/'.$page->cat_1.'">'.PHP_EOL;
		$opt_name = 'icon-'.$page->cat_1;
		$options_query->bind_param('s',$opt_name);
		$options_query->execute();
		$options_query->bind_result($optname,$optvalue);
		$options_query->fetch();
		print'        <img src="'.$optvalue.'" class="image-rounded ctrlPanel-icon"/><br />'.PHP_EOL;
		print'        '.$ptitle.PHP_EOL;
		print'      </a></p></div>'.PHP_EOL;
	}
	$options_query->free_result();
}
print'      <div class="col-md-3 col-sm-4 col-xs-6"><p><a href="/admin">'.PHP_EOL;
print'        <img src="/images/icons/admin_back.png" class="image-rounded ctrlPanel-icon"/><br />'.PHP_EOL;
print'        Back to Admin Panel'.PHP_EOL;
print'      </a></p></div>'.PHP_EOL;

?>
        </div>
      </div>
    </div>
  </div>
</div>
