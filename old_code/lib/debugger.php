<?php

/**
 * Debugger
 *
 * @category   Debugger
 * @package    debugger.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones	
 */

/**
 */

class Debugger
{

	public $debugLog = array();

	/**
	 * Debugger::debug()
	 * 
	 * @param mixed $text
	 * @return
	 */
	public function debug($text, $shift = 0, $cli_print = false)
	{
		$space = '';
		$spaces = 80 - strlen($text);
		for ($s = 1; $s <= $spaces; $s++) {
			$space .= ' ';
		}
		if (version_compare(PHP_VERSION, '5.3.6', '>=')) {
			$bt = debug_backtrace(~DEBUG_BACKTRACE_PROVIDE_OBJECT & DEBUG_BACKTRACE_IGNORE_ARGS);
		}else{
			$bt = debug_backtrace(false);

		}
		
		$caller = array_shift($bt);
		if (strpos($caller['file'], 'autoload.php') === false) {
			$caller = array_shift($bt);
		}

		if($shift != 0 && count($bt) > 1){
			for($i = 0; $i < abs($shift); $i++){
				if(count($bt) <= abs($shift)){
					break;
				}
				$caller = array_shift($bt);
			}
		}
		$file = '';
		if(array_key_exists('file', $caller)){
			$file = str_replace(__EXECDIR__, '', $caller['file']);
		}else{
			$caller['file'] = 'Unknown';
			$caller['line'] = '-';
		}
		$file = str_replace(DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR .
			'modules', '_MODULE_', $file);
		$file = str_replace(DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR .
			'plugins', '_PLUGIN_', $file);
		$file = str_replace(DIRECTORY_SEPARATOR . 'lib', '_LIB_', $file);
		$file = str_replace(DIRECTORY_SEPARATOR . 'class.', DIRECTORY_SEPARATOR, $file);
		
		if ($spaces < 16 || strlen($file) > 30) {
			$file = explode(DIRECTORY_SEPARATOR, $caller['file']);
			$file = $file[count($file) - 1];
		}
		$msg =  $text . $space . '(' . $file . ', ' . $caller['line'] . ')';
		$this->debugLog[ranString(2, 2) . microtime(false)] = $msg;
		if(is_CLI()&&$cli_print){
			echo ' # '.$msg.PHP_EOL;
		}
	}

	/**
	 * Debugger::compile()
	 * 
	 * @return
	 */
	public function compile()
	{
		$debug = array();
		foreach($this->debugLog as $time => $event) {
			$date_array = explode(" ", $time);
			$date = date("Y-m-d H:i:s", $date_array[1]);
			$debug[] = "  " . $date . '' . substr(number_format(substr($date_array[0], 2), 5),
				1) . " - " . $event . PHP_EOL;
		}
		end($this->debugLog);
		$after = key($this->debugLog);
		reset($this->debugLog);
		$before = key($this->debugLog);
		$execTime = (substr($after, 13) - substr($before, 13)) + (substr($after, 2, 9) -
			substr($before, 2, 9));
		$debug[] = PHP_EOL . '  Execution took: ' . $execTime . PHP_EOL;
		$this->debugLog = $debug;
	}
}
