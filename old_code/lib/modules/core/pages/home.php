<div class="row carouselPane">
  <div class="col-xs-12 mainCarousel hidden-xs">
<?php
	$carousel = new Carousel();
	$carousel->setID("newCarousel");
	$carousel->addXnewimages($mySQL_r,3);
	print $carousel->create();
?>
  </div>
</div>
<div class="row pane">
<?php
$module_res = $page->getResource('modules');
$module_res->getModuleFromNS('news');
if($module_res->isInstalled()){
?>
  <div class="col-md-4">
    <div class="row">
      <div class="col-xs-12">
        <h2>News <?php
        if($mySQL_r->query("SELECT * from `news_articles` WHERE `publish`=1")->num_rows>2){
			print '<a class="btn btn-small btn-info pull-right" href="/news">Read more &raquo;</a>';
		}
		?></h2>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12">
<?php
//include_once("lib/modules/article.php");
/*$article = new NewsItem($mySQL_r);
$news_res = $this->mySQL_r->query("SELECT `ID` from `news_articles` WHERE `publish`=1 ORDER BY `t_p` DESC, `t_e` DESC LIMIT 2");
if($news_res!==false&&$news_res->num_rows!=0){
	while($item = $news_res->fetch_array()){
		$article->setID($item['ID']);
		$article->setPreview(true);
		$article->setPublish(false);
		if($article->createArticle()){
			print("        <h4>".$article->getTitle()."</h4>\n");
			print("          <small><em>By ".$article->getAuthor()."</em></small>\n");
			print("          <article>\n");
			print("            ".$article->getContent()."\n");
			print("          </article>\n");
			if($article->getLonger()){
				print("        <p><a class=\"btn btn-default btn-small\" href=\"/news/article?a=".$article->getID()."\">Read more &raquo;</a></p>\n");
			}
			print("      <hr />\n");
		}
	}
}else{*/
	print("      <h3>There is no news.</h3>\n");
//}
?>
      </div>
    </div>
  </div>
<?php } ?>
<?php
$module_res->reset();
$module_res->getModuleFromNS('competitions');
if($module_res->isInstalled()){
?>
  <div class="col-md-4">
    <div class="row">
      <div class="col-xs-12">
        <h2>Competitions <?php
        /*if($mySQL_r->query("SELECT * from `comp_meet` WHERE `enable`=1 AND CURDATE() BETWEEN `disp_f` AND `disp_u`")->num_rows>2){
			print '<a class="btn btn-small btn-info pull-right" href="/competitions">More &raquo;</a>';
		}*/
		?></h2>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12">
<?php
/*$comp_res = $this->parent->mySQL_r->prepare("SELECT `ID` from `comp_meet` WHERE `enable`=1 AND CURDATE() BETWEEN `disp_f` AND `disp_u` ORDER BY `date_c` DESC LIMIT 2");
$comp_res->execute();
$comp_res->store_result();
if($comp_res->num_rows!=0){
	$comp_res->bind_result($ID);
	include_once("lib/modules/meet.php");
	$meet = new Meet($mySQL,$page->getPageNumber());
	while($comp_res->fetch()){
		$meet->setID($ID);
		$meet->createMeet();
		print("        <h4>".$meet->getTitle()."</h4>\n");
		print("          <p>Details for <strong>".$meet->getTitle()."&nbsp;(");
		print($meet->getDateWordy());
		print(")</strong> are available.</p>\n");
		print("          <p><a class=\"btn btn-default btn-small\" href=\"/competitions/meet?m=".$meet->getID()."\">View details &raquo;</a></p>\n");
		print("      <hr />\n");
		$meet->clear(true);
	}
}else{*/
	print("      <h4>There are no upcoming competitions.</h4>\n");
//}
?>
      </div>
    </div>
  </div>
<?php } ?>
  <div class="col-md-4">
    <h2>Social Media</h2>
    <div class="text-center">
      <a class="twitter-timeline"  data-chrome="nofooter" href="https://twitter.com/BiggleswadeSC" data-widget-id="420193015282405376">Tweets by @BiggleswadeSC</a>
      <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
      <br />
      Find us on facebook at <a target="_blank" href="http://www.facebook.com/biggleswadesc">www.facebook.com/biggleswadesc</a>.<br />
      <div class="fb-like" data-href="https://www.facebook.com/BiggleswadeSC" data-layout="button" data-width="100" data-action="like" data-show-faces="true" data-share="true" kid-directed-site="true"></div>
    </div>
  </div>
</div>