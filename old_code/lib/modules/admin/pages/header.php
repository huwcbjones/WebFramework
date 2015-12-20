<?php
/**
 * Header Page
 *
 * @category   WebApp.Page.Header
 * @package    modules/core/pages/header.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 */
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="author" content="Huw Jones" />
<meta name="description" content="<?php $page->getPageDescription() ?>" />
<title><?php print $page->getTitle() ?> | BWSC</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<link rel="icon" type="image/ico" href="<?php print $page->getCDN()?>favicon.ico" />
<link rel="shortcut icon" type="image/ico" href="<?php print $page->getCDN() ?>favicon.ico" />
<link rel="stylesheet" media="screen" href="<?php print $page->getCDN()?>css/core/bootstrap.css" />
<link rel="stylesheet" media="screen" href="<?php print $page->getCDN()?>css/core/bootstrap.dashboard.css" />
<link rel="stylesheet" media="screen" href="<?php print $page->getCDN()?>css/core/bootstrap.custom.css" />
<link rel="stylesheet" media="screen" href="<?php print $page->getCDN()?>css/core/web.css" />
<?php
if(count($page->_css)!=0){foreach($page->_css as $sheet=>$media){
	print '<link type="text/css" href="'.$page->getCDN().'css/'.$sheet.'" rel="stylesheet" />'.PHP_EOL;
}}
?>
<!--[if lt IE 9]>
    <script src="<?php print $page->getCDN()?>js/jquery-1.10.2.min.js"></script>
    <script src="<?php print $page->getCDN()?>js/html5shiv.js"></script>
    <script src="<?php print $page->getCDN()?>js/respond.min.js"></script>
<![endif]-->
<!--[if gte IE 9]><!-->
    <script type="text/javascript" src="<?php print $page->getCDN()?>js/core/jquery.min.js"></script>
<!--<![endif]-->
<script type="text/javascript" src="<?php print $page->getCDN()?>js/core/modernizr.js"></script>
<script type="text/javascript" src="<?php print $page->getCDN()?>js/core/bootstrap.js"></script>
<?php
if(count($page->_js)!=0){
	foreach($page->_js as $script=>$blank){
		print ('<script type="text/javascript" src="');
		if(substr($script,0,4)=='http'){
			print ($script);
		}else{
			print ($page->getCDN().'js/'.$script);
		}
		print('"></script>'.PHP_EOL);
	}
}
?>
</head>

<body>
<div class="container-fluid">
  <div class="row">