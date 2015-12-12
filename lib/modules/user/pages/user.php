<?php
$page->createTitle();
print $page->getHeader();
?>
<div class="row pane">
  <div class="col-xs-12">
    <div class="row">
      <div class="col-xs-8">
        <h4>Welcome <?php print $user->getName() ?></h4>
      </div>
      <div class="col-xs-4">
        <h5><?php print date("l, jS F Y");?></h5>
      </div>
    </div>
  </div>
  <div class="col-md-10 col-md-offset-1 text-center">
    <div class="row">
      <div class="col-md-3 col-sm-4 col-xs-6"><p><a href="/user/swimmers">
        <img src="images/icons/swimmer.png" class="image-rounded ctrlPanel-icon"/><br />
        Manage My Swimmers
      </a></p></div>
      <div class="col-md-3 col-sm-4 col-xs-6"><p><a href="/user/entry">
        <img src="images/icons/entry.png" class="image-rounded ctrlPanel-icon"/><br />
        Online Entry
      </a></p></div>
      <div class="col-md-3 col-sm-4 col-xs-6"><p><a href="/user/profile">
        <img src="images/icons/user.png" class="image-rounded ctrlPanel-icon"/><br />
        My Profile
      </a></p></div>
      <?php
  if($user->inGroup(1)||$user->inGroup(1)||$user->accessPage(1000)){
      print("        <div class=\"col-md-3 col-sm-4 col-xs-6\"><p><a href=\"/admin\">\n");
      print("          <img src=\"images/icons/admin.png\" class=\"image-rounded ctrlPanel-icon\"/><br />\n");
      print("          Admin Panel\n");
      print("        </a></p></div>\n");
      print("      </div>\n");
  }?>
  </div>
</div>