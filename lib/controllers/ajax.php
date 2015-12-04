<?php

/**
 * Ajax Constructer
 *
 * @category   WebApp.Ajax
 * @package    ajax.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
*
*/

class Ajax extends BaseCtrl
{
	const name_space = 'WebApp.Ajax';
	const version = '1.0.0';

	private $ctrl;

	private $content = array();

	private $result;

	public function setAjax()
	{
		//$this->parent->addHeader('content-type', 'application/json');
		// Check to see if we are in maintenance mode
		if (!$this->parent->config->config['core']['database']) {
			$this->setStatus(500);
			return;
		}

		for ($i = 1; $i != 3; $i++) {
			${'cat' . $i} = WebApp::get('cat'.$i);
		}

		if (!file_exists(__MODULE__ . '/' . $cat2 . '/ajax.php')) {
			$this->parent->debug($this::name_space . ':  Could not find "ajax.php"!');
			$this->result = new ActionResult($this, $_SERVER['REQUEST_URI'], 0,
				'Whoops, something went wrong with that action and we\'re trying to fix it. <br />Error: <code>Ajax Ctrl not found</code>');
			return;
		}

		if (!@include_once __MODULE__ . '/' . $cat2 . '/ajax.php') {
			$this->parent->debug($this::name_space . ':  Could not access "ajax.php"! Check r/w permissions');
			$this->result = new ActionResult($this, $_SERVER['REQUEST_URI'], 0,
				'Whoops, something went wrong with that action and we\'re trying to fix it. <br />Error: <code>Failed to open Ajax</code>');
			return;
		}

		if (class_exists('AjaxController')) {
			$this->ctrl = new AjaxController($this);
			$this->parent->debug($this::name_space . ': AjaxController loaded');
		} else {
			$this->parent->debug($this::name_space . ': Could not find AjaxController class in "ajax.php"!');
			$this->result = new ActionResult($this, $_SERVER['REQUEST_URI'], 0,
				'Whoops, something went wrong with that action and we\'re trying to fix it. <br />Error: <code>Ajax Ctrl Ob not found</code>');
		}
	}

	public function execute()
	{
		if (!is_object($this->result)) {
			if ($this->ctrl->processAjax() !== false) {
				$this->parent->debug($this::name_space . ': Getting ajax content');
			} else {
				$this->parent->debug($this::name_space .
					': Error occurred whilst processing ajax...');
			}
			$this->result = $this->ctrl->result;
		}
		$this->parent->debug($this::name_space . ': Ajax executed!');
		$this->parent->content = json_encode($this->result->getResult(),
			JSON_HEX_QUOT | JSON_HEX_TAG);
		$this->setStatus(200);
	}
}

?>