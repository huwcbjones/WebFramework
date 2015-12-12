<?php
$page->createTitle();
print $page->getHeader();
if(isset($_GET['msg'])){
	$alert = new Alert();
	$alert->printAlerts();
}
include_once("lib/modules/article.php");
$article = new NewsItem($mySQL);
$article->setID($_GET['a']);
$article->setPreview(false);
$article->setPublish(true);
print("<div class=\"row pane\">\n");
print("  <div class=\"col-xs-12\">\n");
if($article->createArticle(1)){
	if($article->getPublishState()){
		print("    <div class=\"row\">\n");
		print("      <div class=\"col-md-3\">\n");
		print("        <small>Article by: ".$article->getAuthor()."</small>\n");
		print("      </div>\n");
		print("      <div class=\"col-md-3\">\n");
		print("        ".$article->getPublish()."\n");
		print("      </div>\n");
		print("      <div class=\"col-md-3 hidden-xs\">\n");
		if($article->getEdited()!==false){
		print("        <small>Edited by: ".$article->getEdited()."</small>\n");
		}
		print("      </div>\n");
		print("      <div class=\"col-md-3 hidden-xs\">\n");
		print("        ".$article->getEdit()."\n");
		print("      </div>\n");
		print("    </div>\n");
	}
	print("    <div class=\"row\">\n");
	print("      <div class=\"col-md-12\">\n");
	print("        <h3>".$article->getTitle()."</h3>\n");
	print("        ".$article->getContent()."\n");
	print("      </div>\n");
	print("    </div>\n");
}else{
	print('      <h2>No article selected to preview!</h2>'.PHP_EOL);
}
print("  </div>\n");
print("</div>\n");
?>
<div class="row pane">
  <div class="col-xs-12">
    <form class="form-horizontal" action="<?php print $_SERVER['REQUEST_URI']?>" method="get">
      <div class="form-group">
        <div class="col-xs-4 text-right">
          <p><label for="a">Article to Preview <?php Tooltip::helpTooltip('Select the article you wish to preview and then click \'View\'. A <> after the article ID indicates the article is not displaying on the website.') ?></label></p>
        </div>
        <div class="col-xs-8">
          <select name="a" class="form-control">
            <option disabled="disabled" <?php if(!isset($_GET['a'])){print (' selected');} ?>>Select an Article</option>
          <?php
		  $article_query = $mySQL['r']->prepare("SELECT `title`,`ID`,`enable` FROM `news_articles` ORDER BY `ID`");
		  $article_query->execute();
		  $article_query->store_result();
		  $article_query->bind_result($title,$ID,$preview);
		  while($article_query->fetch()){
			  print('            <option value="'.$ID.'"');
			  if(isset($_GET['a'])&&$_GET['a']==$ID){print (' selected');}
			  print('>'.$title.' - '.$ID);
			  if($preview=="1"){print ' <>';}
			  print('</option>'.PHP_EOL);
		  }
		  $article_query->free_result();
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