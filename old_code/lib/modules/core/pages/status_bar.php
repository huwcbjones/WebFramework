<?php
/**
 * Status Bar Page
 *
 * @category   WebApp.Page.StatusBar
 * @package    /pages/status_bar.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 * /resources/breadcrumb.php
 */

$user = $page->parent->user;
require_once __MODULE__.'/core/resources/breadcrumb.php';
$breadcrumb = new Breadcrumb($page, $page->mySQL_r);
$breadcrumb->build();
?>
<div class="container">
<div class="row" id="status_bar">
  <div class="col-sm-9" style="padding-left:0px;">
<?php print $breadcrumb->getBreadcrumb(); ?>
  </div>
  <div class="col-sm-3">
    <div class="btn-group pull-right">
<?php
if($page->parent->config->config['core']['database']){
	if($user->is_loggedIn()){
		print ('      <a href="#" class="btn btn-default">'.$user->get_fullName().'</a>'.PHP_EOL);
		print ('      <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.PHP_EOL);
		print ('        <span class="caret"></span>'.PHP_EOL);
		print ('        <span class="sr-only">Toggle Dropdown</span>'.PHP_EOL);
		print ('      </a>'.PHP_EOL);
		print ('      <ul class="dropdown-menu" role="menu">'.PHP_EOL);
		print ('        <li><a href="/user/profile">My Profile <span class="pull-right '.B_ICON.' '.B_ICON.'-user"></span></a></li>'.PHP_EOL);
		print ('        <li class="divider"></li>'.PHP_EOL);
		print ('        <li><a href="#" onclick="processData(\'/action/user/lock\')">');
		print ('Lock <span class="pull-right '.B_ICON.' '.B_ICON.'-lock"></span></a></li>'.PHP_EOL);
		print ('        <li class="divider"></li>'.PHP_EOL);
		
		print ('        <li><a href="/action/user/logout?r='.urlencode(Server::get('Request_URI')).'">');
		print ('Logout <span class="pull-right '.B_ICON.' '.B_ICON.'-log-out"></span></a></li>'.PHP_EOL);
		print ('      </ul>'.PHP_EOL);
		
	}else{
		print ('      <a href="/user/login" class="btn btn-default">Log In&nbsp;&nbsp;&nbsp;&nbsp;<span class="pull-right '.B_ICON.' '.B_ICON.'-log-in"></span></a>'.PHP_EOL);
		if(!$page->parent->config->config['core']['maintenance']){
			print ('      <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.PHP_EOL);
			print ('        <span class="caret"></span>'.PHP_EOL);
			print ('        <span class="sr-only">Toggle Dropdown</span>'.PHP_EOL);
			print ('      </a>'.PHP_EOL);
			print ('      <ul class="dropdown-menu" role="menu">'.PHP_EOL);
			print ('        <li><a href="/user/recover" class="btn btn-default">Forgot Details?</a></li>'.PHP_EOL);
			print ('        <li><a href="/user/register">Registration</a></li>'.PHP_EOL);
			print ('      </ul>'.PHP_EOL);
		}
	}
}
?>
    </div>
  </div>
</div>
<div id="alert_working" class="hidden">
<?php
$working = $page->getPlugin('alert');
$working->setAlert('Processing...', B_T_INFO, 'working', false);
print $working->getAlert();
?>
</div>
<?php
foreach(Session::getAll('status_msg') as $id=>$message){
	print $message.PHP_EOL;
	Session::del('status_msg',$id);
}
?>