<?php

/**
 * Base Resource
 *
 * @category   WebApp.Base
 * @package    class.baseresource.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
 *
 */

class BaseResource extends Base
{
	const		name_space		= 'WebApp.Base.Resource';
	const		version			= '1.0.0';
	
	function parseTemplate($variables, $template){
		if(file_exists($template)){
			$template = file_get_contents($template);
			$template = $this->_replaceVars($template, $variables);
			return $template;
		}else{
			return false;
		}
	}
	
	private function _replaceVars($text, $variables, $delim='%'){
		// Create regex for replace
		$delim = preg_quote($delim);
		$regex ='/'.$delim.'([A-z0-9]+)'.$delim.'/e';
		foreach($variables as $key=>$value){
			${$key} = $value;
		}

		// Unset unused variables
		unset($variables, $delim, $key, $value);
		
		$hr = '    <div class="row">'.PHP_EOL.'      <div class="col-xs-12">'.PHP_EOL.'        <hr />'.PHP_EOL.'      </div>'.PHP_EOL.'    </div>'.PHP_EOL;

		while(preg_match_all($regex,$text,$matches)!==0){
			$text = preg_replace($regex, "$$1", $text);
		}
		return $text;
	}
}
?>