<?php
/**
 * Paginator Class
 *
 * Creates HTML Pagination
 *
 * @category   Plugins.Bootstrap.Paginator
 * @package    paginator.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */
 
class Paginator extends BasePlugin
{
	const		name_space		= 'Plugins.Boostrap.Paginator';
	const		version			= '1.0.0';
	
	private $paginator;
	private $pages;
	private $items;
	private $items_per_page;
	private $curPage;
	private $pageLink;
	
	function setItemsPerPage($itemsPerPage){
		$this->parent->parent->debug($this::name_space.': Set "'.$itemsPerPage.'" items/page');
		$this->items_per_page = $itemsPerPage;
	}
	function setItems($items){
		$this->parent->parent->debug($this::name_space.': Set "'.$items.'" number of items');
		$this->items= $items;
	}
	function setCurrentPage($page){
		$this->parent->parent->debug($this::name_space.': Set current page to "'.$page.'"');
		$this->curPage= $page;
	}
	function setPageLink($link){
		$this->parent->parent->debug($this::name_space.': Set page link to "'.$link.'"');
		$this->pageLink= $link;
	}
	
	function createPaginator(){
		$this->parent->parent->debug($this::name_space.': Creating Paginator...');
		if($this->items_per_page==-1){
			$this->items_per_page = $this->items;
		}
		$this->pages = ceil($this->items/$this->items_per_page);
		$paginator = "";
		$paginator.= '<div class="row text-center">'.PHP_EOL;
		$paginator.= '  <div class="col-sm-8 col-sm-offset-2">'.PHP_EOL;
		$paginator.= '    <ul class="pagination">'.PHP_EOL;
		$paginator.= '      <li class="'; if($this->curPage==1)				$paginator.= 'disabled';	$paginator.= '"><a href="'.$this->pageLink.'&page=1">&laquo;</a></li>'.PHP_EOL;
		for($p=1;$p<=$this->pages;$p++){
		$paginator.= '      <li class="'; if($this->curPage==$p)			$paginator.= 'active';		$paginator.= '"><a href="'.$this->pageLink.'&page='.$p.'">'.$p.'</a></li>'.PHP_EOL;
		}
		$paginator.= '      <li class="'; if($this->curPage==$this->pages)	$paginator.= 'disabled';	$paginator.= '"><a href="'.$this->pageLink.'&page='.$this->pages.'">&raquo;</a></li>'.PHP_EOL;
		$paginator.= '    </ul>'.PHP_EOL;
		$paginator.= '  </div>'.PHP_EOL;
		$paginator.= '</div>'.PHP_EOL;
		$this->paginator = $paginator;
	}
	
	function getPages(){
		return $this->pages;
	}
	function getPaginator(){
		return $this->paginator;
	}
	
}
?>

