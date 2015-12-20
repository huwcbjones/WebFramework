<?php

/**
 * Action Constructer
 *
 * @category   WebApp.Action
 * @package    action.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
*
*/

class Action extends BaseCtrl
{
	const name_space = 'WebApp.Action';
	const version = '1.0.0';

	private $ctrl;

	private $content = array();

	private $result;

	public function setAction()
	{
		//$this->parent->addHeader('content-type', 'application/json');
		// Check to see if we are in maintenance mode
		if (!$this->parent->config->config['core']['database']) {
			$this->setStatus(500);
			$this->result = new ActionResult($this, $_SERVER['REQUEST_URI'], 0,
				'Whoops, something went wrong with that action and we\'re trying to fix it. <br />Error: <code>Action Ctrl not found</code>');
			return;
		}

		for ($i = 1; $i != 3; $i++) {
			${'cat' . $i} = WebApp::get('cat'. ($i + 1));
		}

		if (!file_exists(__MODULE__ . '/' . $cat1 . '/action.php')) {
			$this->parent->debug($this::name_space . ':  Could not find "'.$cat1.'/action.php"!');
			$this->result = new ActionResult($this, $_SERVER['REQUEST_URI'], 0,
				'Whoops, something went wrong with that action and we\'re trying to fix it. <br />Error: <code>Action Ctrl not found</code>');
			return;
		}
		$this->parent->debug($this::name_space . ':   Found "'.$cat1.'/action.php"!');
		if (!@include_once __MODULE__ . '/' . $cat1 . '/action.php') {
			$this->parent->debug($this::name_space . ':  Could not access "action.php"! Check r/w permissions');
			$this->result = new ActionResult($this, $_SERVER['REQUEST_URI'], 0,
				'Whoops, something went wrong with that action and we\'re trying to fix it. <br />Error: <code>Failed to open Action</code>');
			return;
		}

		if (!class_exists('ActionController')) {
			$this->parent->debug($this::name_space . ': Could not find ActionController class in "action.php"!');
			$this->result = new ActionResult($this, $_SERVER['REQUEST_URI'], 0,
				'Whoops, something went wrong with that action and we\'re trying to fix it. <br />Error: <code>Action Ctrl Ob not found</code>');
		}
		
		$this->ctrl = new ActionController($this);
		
		if(!is_object($this->ctrl)){
			$this->parent->debug($this::name_space . ': Failed to create ActionController!');
			$this->result = new ActionResult($this, $_SERVER['REQUEST_URI'], 0,
				'Whoops, something went wrong with that action and we\'re trying to fix it. <br />Error: <code>Action Ctrl Ob not found</code>');
			return;
		}
		
		$this->parent->debug($this::name_space . ': Action controller loaded');
	}

	public function execute()
	{
		if (!is_object($this->result)) {
			
			if ($this->ctrl->processAction() !== false) {
				$this->parent->debug($this::name_space . ': Getting action content');
			} else {
				$this->parent->debug($this::name_space .
					': Error occurred whilst processing action...');
			}
			$this->result = $this->ctrl->result;
		}
		$this->parent->debug($this::name_space . ': Action executed!');
		$this->parent->content = json_encode($this->result->getResult(),
			JSON_HEX_QUOT | JSON_HEX_TAG);
		$this->setStatus(200);
	}
}

?>