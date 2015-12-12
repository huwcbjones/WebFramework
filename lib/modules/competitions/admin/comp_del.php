<?php
$page->createTitle();
print $page->getHeader();
if(isset($_GET['msg'])){
	$alert = new Alert();
	$alert->printAlerts();
}
$tabindex = 1;
?>

<div class="row pane">
  <div class="col-sm-12 col-md-11 col-lg-11 col-lg-offset-1 col-md-offset-1">
    <form class="remove form-horizontal" id="remove" action="/act/comp_del" method="post">
      <div class="form-group">
        <span id="s_comp">
          <div class="col-sm-2">
            <label for="article">Competition * <?php Tooltip::helpTooltip('Competition to delete. (Required)') ?></label>
          </div>
          <div class="col-sm-6">
            <select class="form-control" name="comp" tabindex="<?php $tabindex++;print $tabindex;?>">
              <option disabled="disabled">Select a Competition</option>
<?php
		  $article_query = $mySQL['r']->prepare("SELECT `title`,`ID`,`enable` FROM `comp_meet` ORDER BY `date_c` ASC");
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
          <div class="col-sm-4"><strong>
            <span class="selectRequiredMsg text-danger">Please select a competition.</span>
          </strong></div>
        </span>
      </div>
      <div class="form-group">
        <div class="col-sm-8">
          <div class="row">
            <div class="col-xs-6">
              <a tabindex="<?php $tabindex++;print $tabindex;?>" class="btn btn-large btn-default btn-block" href="<?php print $page->prevPage?>">Close&nbsp;&nbsp;&nbsp;<span class="<?php print B_ICON.' '.B_ICON ?>-remove-sign"></span></a>
            </div>
            <div class="col-xs-6">
              <button onClick="return openModal()" id="openConf" tabindex="<?php $tabindex++;print $tabindex;?>" class="btn btn-large btn-danger btn-block" >Delete Compeition&nbsp;&nbsp;&nbsp;<span class="<?php print B_ICON.' '.B_ICON ?>-trash"></span></button>
            </div>
          </div>
        </div>
      </div>
<?php
	  $deleteConf = new Modal();
	  $deleteConf->setID('delConf');
	  $deleteConf->setBody('Are you sure you wish to delete this competition?<br />All data associated with this competition will be lost.');
	  $deleteConf->setTitle('Delete Competition?');
	  $deleteConf->setLeft('default','Cancel','remove-sign');
	  $deleteConf->setRight('danger','Delete','trash','submit');
	  print $deleteConf->getModal();
?>
    </form>
  </div>
</div>

<script type="text/javascript">
$("#recaptcha_response_field").attr("tabindex","<?php print $recaptchaTab?>");
function openModal(){
	if(Spry.Widget.Form.validate(document.getElementById("remove"))){
		$("#delConf").modal('show');
	}
	return false;
}
var comp = new Spry.Widget.ValidationSelect("s_comp", {validateOn:["blur", "change"]});
</script>