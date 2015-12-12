<?php
$page->createTitle();
if(isset($_SESSION['act_user'])){$act_user = $_SESSION['act_user'];
}else{$act_user = "";}
if(isset($_SESSION['act_email'])){$act_email = $_SESSION['act_email'];
}else{$act_email= "";}
?>
<div class="jumbotron">
  <div class="row">
    <div class="col-xs-12">
      <h2><?php print $page->getTitle(); ?></h2>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12">
<?php
if(isset($_GET['msg'])){
	$alert = new Alert();
	$alert->printAlert();
}
print $page->getIntroText(); ?>
    </div>
  </div>
  <form class="form-horizontal" method="post" action="/act/resendActivation">
  <div class="form-group">
    <div class="col-xs-10">
      <div class="form-group">
        <div class="col-xs-12">
          <input class="form-control" type="text" name="user" placeholder="Username" value="<?php print $act_user?>" />
        </div>
      </div>
      <div class="form-group">
        <div class="col-xs-12">
          <input class="form-control" type="email" name="email" placeholder="Email Address" value="<?php print $act_email?>" />
        </div>
      </div>
    </div>
    <div class="col-xs-2">
      <input class="btn btn-primary btn-block" type="submit" value="Resend" />
    </div>
  </div>
  </form>
</div>