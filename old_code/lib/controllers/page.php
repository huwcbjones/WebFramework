<?php

/**
 * Page Constructer
 *
 * @category   WebApp.Page
 * @package    page.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
*
*/
class Page extends BaseCtrl
{
	const name_space = 'WebApp.Page';
	const version = '1.0.0';

	private $ctrl;

	public $page = '';
	public $pageDetails = array();
	public $_css = array();
	public $_js = array();

	private $content = '';

	private $_regen = false;
	private $_generating = false;

	/**
	 * Page::setPage()
	 * @return
	 */
	public function setPage()
	{

		if (!$this->parent->config->config['core']['database']) {
			$this->_setNoDBPage();
			return;
		}
		
		if(
			$this->parent->config->config['core']['maintenance']
			&&!$this->parent->user->inGroup(2)
			&&WebApp::get('cat1') !== 'action'
			&&WebApp::get('cat1') !== 'user'
			&&WebApp::get('cat2') !== 'login'
		) {
			WebApp::get('cat1', 'core');
			WebApp::get('cat2', 'maintenance');
		}
		for ($i = 1; $i <= 4; $i++) {
			${'cat'.$i} = WebApp::get('cat' . $i);
		}

		$this->parent->debug($this::name_space . ': Getting page info from database...');
		$page = $this->_getPageInfo();
		if ($page === false || !is_array($page) || count($page)===0) {
			$this->parent->debug($this::name_space . ': Page does not exist in the database');
			$this->setStatus(404);
			return;
		}

		$this->pageDetails = $page;
		$this->parent->debug($this::name_space . ': Page ID: '.$page['num']);

		if (!$this->parent->user->can_accessPage($page['num'])) {
			$this->parent->debug($this::name_space . ': User does not have access to this page!');
			$this->setStatus(401);
			return;
		}
		
		$this->parent->debug($this::name_space . ': Current user has access to this page.');
		
		if (!file_exists(__MODULE__ . '/' . $cat1 . '/controller.php')) {
			$this->parent->debug($this::name_space . ': Could not find "controller.php"!');
			$this->setStatus(404);
			return;
		}
		
		if (!@include_once __MODULE__ . '/' . $cat1 . '/controller.php') {
			$this->parent->debug($this::name_space . ': Could not access "controller.php"! Check r/w permissions');
			$this->setStatus(404);
			return;
		}

		if (!class_exists('PageController')) {
			$this->parent->debug($this::name_space . ': Could not find PageController class in "controller.php"!');
			$this->setStatus(404);
			return;
		}

		$this->ctrl = new PageController($this);

		if ($this->getStatus() == 200) {
			$this->parent->debug($this::name_space . ': Page controller loaded');
			if (!$this->ctrl->processPage()) {
				$this->parent->debug($this::name_space . ': Error occurred whilst processing page...');
				$this->setStatus(500);
			}
		}
	}

	private function _setNoDBPage(){
		$page['title'] = 'Connection Error';
		$page['num'] = -1;
		$page['desc'] = '';
		$page['intro'] = '';
		$page['data'] = '';
		$this->pageDetails = $page;

		if (!file_exists(__MODULE__ . '/core/controller.php')) {
			$this->parent->debug($this::name_space . ': Could not find "controller.php"!');
			$this->setStatus(404);
			return;
		}

		if (!@include_once __MODULE__ . '/core/controller.php') {
			$this->parent->debug($this::name_space . ': Could not access "controller.php"! Check r/w permissions');
			$this->setStatus(404);
			return;
		}

		if (!class_exists('PageController')) {
			$this->parent->debug($this::name_space . ': Could not find PageController class in "controller.php"!');
			$this->setStatus(404);
			return;
		}

		$this->ctrl = new PageController($this);

		if ($this->getStatus() == 200) {
			$this->parent->debug($this::name_space . ': Page controller loaded');
			if (!$this->ctrl->processPage('core', 'connection_error')) {
				$this->setStatus(404);
			}
		}
	}
	
	/**
	 * Page::setContent()
	 * 
	 * @param mixed $content
	 * @return
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}

	/**
	 * Page::regenerate()
	 * 
	 * @return
	 */
	public function regenerate()
	{
		$this->_regen = true;
	}
	
	/**
	 * Page::setTitle()
	 * 
	 * @param mixed $title
	 * @return
	 */
	public function setTitle($title)
	{
		$this->parent->debug($this::name_space . ': Title change request...');
		if ($title != $this->pageDetails['title']) {
			$this->parent->debug($this::name_space . ': Title changed to "'.$title.'"!');
			$this->pageDetails['title'] = $title;
			$this->_regen = true;
		}
	}

	/**
	 * Page::_getPageInfo()
	 * 
	 * @return
	 */
	private function _getPageInfo()
	{
		for ($i = 1; $i <= 4; $i++) {
			${'cat' . $i} = WebApp::get('cat' . $i);
		}
		$page = $this->_checkNormalPage($cat1, $cat2, $cat3);
		if($page !== false){
			return $page;
		}
		
		$this->parent->debug($this::name_space.': Page not found, checking extended...');
		
		$page = $this->_checkExtendedPage($cat1, $cat2, $cat3, $cat4);
		if($page !== false){
			return $page;
		}
		
		$this->parent->debug($this::name_space.': Extended page not found, checking hotlinks...');
		
		require_once __MODULE__ . '/link/controller.php';
		$link = new LinkController($this);
		$newUrl = $link->getHotlink(WebApp::get('cat1'));
		if ($newUrl !== false) {
			header("Location: " . $newUrl);
			exit();
		} else {
			$this->parent->debug($this::name_space.': Hotlink not found...');
			return false;
		}

	}

	private function _checkNormalPage($cat1, $cat2, $cat3){
		// Using null safe <=> as $cat1, $cat2, $cat3 return null if they are not present
		$page_query = $this->mySQL_r->prepare("SELECT `ID`,`title`,`https`,`desc`,`introText`,`data`,`css`,`js` from `core_pages` WHERE `cat1`<=>? AND `cat2`<=>? AND `cat3`<=>?");
		$page_query->bind_param('sss', $cat1, $cat2, $cat3);
		$page_query->execute();
		$page_query->store_result();
		if ($page_query->num_rows != 1) {
			$this->parent->debug($this::name_space.': Found '.$page_query->num_rows.' entries for page!');
			$page_query->free_result();
			return false;
		}
		$page_query->bind_result($ID, $title, $https, $desc, $intro, $data, $css, $js);
		$page_query->fetch();
		$this->checkHTTPS($https);
		$page['title'] = $title;
		$page['num'] = $ID;
		$page['desc'] = $desc;
		$page['intro'] = $intro;
		$page['data'] = unserialize($data);
		foreach(strgetcsv($js) as $script) {
			$this->addJS($script);
		}
		foreach(strgetcsv($css) as $sheet) {
			$this->addCSS($sheet);
		}
		$page_query->free_result();
		return $page;
	}
	
	private function _checkExtendedPage($cat1, $cat2, $cat3, $cat4){
		// Using null safe <=> as $cat1, $cat2, $cat3 return null if they are not present
		for ($c = 4; $c >= 1; $c--) {
			if (${'cat' . $c} !== NULL) {
				${'cat' . $c} = '*';
				break;
			}
		}
		$page_query = $this->mySQL_r->prepare("SELECT `ID`,`title`,`https`,`desc`,`introText`,`data`,`css`,`js` from `core_pages` WHERE `cat1`<=>? AND `cat2`<=>? AND `cat3`<=>?");
		$page_query->bind_param('sss', $cat1, $cat2, $cat3);
		$page_query->execute();
		$page_query->store_result();
		if ($page_query->num_rows !== 1) {
			$this->parent->debug($this::name_space.': Found '.$page_query->num_rows.' entries for page!');
			return false;
		}
		$page_query->bind_result($ID, $title, $https, $desc, $intro, $data, $css, $js);
		$page_query->fetch();
		$page['title'] = $title;
		$page['num'] = $ID;
		$page['desc'] = $desc;
		$page['intro'] = $intro;
		$page['data'] = unserialize($data);
		foreach(strgetcsv($js) as $script) {
			$this->addJS($script);
		}
		foreach(strgetcsv($css) as $sheet) {
			$this->addCSS($sheet);
		}
		$page_query->free_result();

		return $page;
	}
	/**
	 * Page::create()
	 * 
	 * @return
	 */
	public function execute()
	{
		$this->_generating = true;
		if ($this->getStatus() != 200) {
			if ($this->parent->user->is_loggedIn() && $this->getStatus() == 401) {
				$this->setStatus(403);
			}
			if (!$this->parent->user->is_loggedIn() && WebApp::get('cat1') === 'admin') {
				$this->setStatus(404);
			}
		}
		require_once __MODULE__ . '/core/controller.php';
		$coreController = new CorePageController($this);

		$this->parent->debug($this::name_space . ': Page title is "'.$this->getTitle().'"');
		$this->parent->debug($this::name_space . ': Getting page header');
		$page = $coreController->getHeader($this);

		$this->parent->debug($this::name_space . ': Getting navbar');
		$page .= $coreController->getNavBar($this);

		$this->parent->debug($this::name_space . ': Getting status bar');
		$page .= $coreController->getStatusBar($this);

		if ($this->getStatus() == 200) {
			$this->parent->debug($this::name_space . ': Getting page content');
			$page .= $this->content;
		} else {
			$this->parent->debug($this::name_space . ': Generating error message');
			$error = new Error($this, $this->getStatus());
			$page .= $error->getError();
		}
		$page .= '</div>' . PHP_EOL;

		$this->parent->debug($this::name_space . ': Getting page footer');
		$page .= $coreController->getFooter($this);

		$this->_generating = false;
		$this->parent->debug($this::name_space . ': Page created!');
		$this->parent->content = $page;
		
		if ($this->_regen) {
			$this->parent->debug($this::name_space .
				': Regenerating page... something changed whilst creating the page');
			$this->_regen = false;
			$this->execute();
		}
		
		

	}

	/**
	 * Page::getPageDescription()
	 * 
	 * @return
	 */
	public function getPageDescription()
	{
		return $this->pageDetails['desc'];
	}
	/**
	 * Page::getTitle()
	 * 
	 * @return
	 */
	public function getTitle()
	{
		if (array_key_exists('title', $this->pageDetails)) {
			return $this->pageDetails['title'];
		} else {
			return '';
		}
	}

	// Add a CSS File to the css to load
	/**
	 * Page::addCSS()
	 * 
	 * @param mixed $file
	 * @param string $media
	 * @return
	 */
	public function addCSS($file, $media = '')
	{
		$this->parent->debug($this::name_space . ': Adding css file: ' . $file);
		if (!array_key_exists($file, $this->_css)) {
			$this->_css[$file] = '';
		}
	}

	// Add a JS File to load
	/**
	 * Page::addJS()
	 * 
	 * @param mixed $file
	 * @return
	 */
	public function addJS($file)
	{
		$this->parent->debug($this::name_space . ': Adding javascript file: ' . $file);
		if (!array_key_exists($file, $this->_js)) {
			$this->_js[$file] = '';
		}
	}
}

?>
