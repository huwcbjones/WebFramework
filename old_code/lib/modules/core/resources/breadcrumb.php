<?php
/**
 * Breadcrumb Generator
 *
 * @category   WebApp.Bootstrap.Breadcrumb
 * @package    breadcrumb.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones	
 */
 
/** INCLUDES
 */
 
class Breadcrumb
{
	private $parent;
	private $mySQL;
	private $breadcrumb = '';
	
	function __construct($parent, $mySQL){
		$this->parent = $parent;
		$this->mySQL = $mySQL;
	}
	function build(){
		$page = $this->parent;
		$breadcrumb = '    <ul class="breadcrumb">'.PHP_EOL;
		if($page->parent->config->config['core']['maintenance']){
			$breadcrumb.= '      <li class="active">Maintenance</li>'.PHP_EOL;
		}elseif(!$page->parent->config->config['core']['database']){
			$breadcrumb.= '      <li class="active">Site Error</li>'.PHP_EOL;
		}else{
			if($page->getStatus()!=200){
				$breadcrumb.= '      <li><a href="/">Home</a></li>'.PHP_EOL;
				$breadcrumb.= '      <li class="active">'.$page->getTitle().'</li>'.PHP_EOL;
			}elseif(WebApp::get('cat3') !== NULL){
				$breadcrumb.= '      <li><a href="/">Home</a></li>'.PHP_EOL;
				$breadcrumb.= '      <li><a href="/'.WebApp::get('cat1').'">'.ucfirst(WebApp::get('cat1')).'</a></li>'.PHP_EOL;
				$breadcrumb.= '      <li><a href="/'.WebApp::get('cat1').'/'.WebApp::get('cat2').'">'.ucfirst(WebApp::get('cat2')).'</a></li>'.PHP_EOL;
				$breadcrumb.= '      <li class="active">'.$page->getTitle().'</li>'.PHP_EOL;
			}elseif(WebApp::get('cat2') !== NULL){
				$breadcrumb.= '      <li><a href="/">Home</a></li>'.PHP_EOL;
				$breadcrumb.= '      <li><a href="/'.WebApp::get('cat1').'">'.ucfirst(WebApp::get('cat1')).'</a></li>'.PHP_EOL;
				$breadcrumb.= '      <li class="active">'.$page->getTitle().'</li>'.PHP_EOL;
			}elseif(WebApp::get('cat1') !== 'core'){
				$breadcrumb.= '      <li><a href="/">Home</a></li>'.PHP_EOL;
				$breadcrumb.= '      <li class="active">'.$page->getTitle().'</li>'.PHP_EOL;
			}else{
				$breadcrumb.= '      <li class="active">Home</li>'.PHP_EOL;
			}
		}
		$breadcrumb.= '    </ul>'.PHP_EOL;
		$this->breadcrumb = $breadcrumb;
	}
	
	function getBreadcrumb(){
		return $this->breadcrumb;
	}
}
?>