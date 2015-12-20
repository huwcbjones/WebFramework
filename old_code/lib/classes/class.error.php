<?php

/**
 * HTTP Status Error Generator
 *
 * @category   WebApp.Error
 * @package    class.error.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 * 
 */

class Error
{

	private $errorMsg;
	private $parent;

	function __construct($parent, $errorCode, $type = 'page', $errorDetails = '')
	{
		$this->parent = $parent;
		$this->errorCode = $errorCode;
		switch($type) {
			case 'alert':
				$errorMsg = $this->_createErrorMsg($errorDetails, false);
				$this->_createAlert($errorMsg);
				break;
			case 'page':
			default:
				$errorMsg = $this->_createErrorMsg($errorDetails);
				$this->_createError($errorMsg);
				break;
		}
	}

	private function _createError($errorMsg)
	{
		$this->errorMsg = '  <div class="jumbotron">' . PHP_EOL;
		$this->errorMsg .= '    <h1>Error ' . $this->errorCode . '</h1>' . PHP_EOL;
		$this->errorMsg .= $errorMsg;
		$this->errorMsg .= '  </div>' . PHP_EOL;
	}
	private function _createAlert($errorMsg)
	{
		$alert = $this->parent->getPlugin('alert');
		$this->errorMsg = $alert->setAlert($errorMsg)->getAlert(true);

	}
	private function _createErrorMsg($errorDetails, $buttons = true)
	{
		switch($this->errorCode) {
			case 401:
				$errorMsg = '    <p>Sorry, you aren\'t allowed access that page, please log in.</p>' .
					PHP_EOL;
				if ($buttons)
					$errorMsg .= '    <p><a class="btn btn-primary btn-lg" href="/user/login?r=' .
						urlencode($_SERVER['REQUEST_URI']) . '">Log In</a></p>' . PHP_EOL;
				break;
			case 403:
				$errorMsg =
					'    <p>Sorry, you do not have the required permissions to access that page.</p>' .
					PHP_EOL;
				break;
			case 404:
				$errorMsg = '    <p>Sorry, that page could not be found.</p>' . PHP_EOL;
				if ($buttons)
					$errorMsg .=
						'    <p><a class="btn btn-lg btn-info" href="javascript:window.history.back()">&laquo; Back</a></p>' .
						PHP_EOL;
				break;
			case 500:
				$errorMsg = '    <p>The server encountered an error with that request.<br />';
				$errorMsg .= ($errorDetails == '') ? '' : 'Error: <code>' . $errorDetails .
					'</code></p>' . PHP_EOL;
				break;
			default:
				$errorMsg =
					'    <p>Woops, something unexpected happened there.<br />Whilst we don\'t quite know what went wrong, we\'ll try and fix it soon.</p>' .
					PHP_EOL;
		}
		return $errorMsg;
	}

	function getError()
	{
		return $this->errorMsg;
	}
}

?>