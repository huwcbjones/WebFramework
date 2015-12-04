<?php

/**
 * Table Checkbox Script Gen
 *
 *
 * @category   Plugin.TableCheck
 * @package    class.tablecheck.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 * 
 */

class TableCheck
{

	const name_space = 'Plugin.TableCheck';
	const version = '1.0.0';

	private	$script				= '';
	private	$type				= 'item';
	private	$requireCheck		= array();
	private	$requireOneCheck	= array();
	private $scripts			= array();
	
	function setType($type)
	{
		$this->type = $type;
		return $this;
	}
	
	function addRequire($selector)
	{
		$this->requireCheck[] = $selector;
		return $this;
	}
	function addRequireOne($selector)
	{
		$this->requireOneCheck[] = $selector;
		return $this;
	}
	
	function getScript()
	{
		return $this->script;
	}
	
	function addRequireOneBtn($selector, $href){
		$script ='	$("'.$selector.'").click(function(e){'.PHP_EOL;
		$script.='		var '.$this->type.'s = $(".'.$this->type.'s_check").filter(":checked")'.PHP_EOL;
		$script.='		if('.$this->type.'s.length==1){'.PHP_EOL;
		$script.='			var block_id = '.$this->type.'s.first().val();'.PHP_EOL;
		$script.='			document.location.href = "'.$href.'/"+'.$this->type.'_id;'.PHP_EOL;
		$script.='		}else if('.$this->type.'s.length>1){'.PHP_EOL;
		$script.='			alert("Please select one '.$this->type.' only to edit");'.PHP_EOL;
		$script.='		}'.PHP_EOL;
		$script.='		return false;'.PHP_EOL;
		$script.='	});'.PHP_EOL;
		
		$this->scripts[] = $script;
		return $this;
	}
	
	function create()
	{
		$script = '$(function() {'.PHP_EOL;
		$script.= '	$("#selectAll").click(function(){'.PHP_EOL;
		$script.= '		$(".'.$this->type.'s_check").prop("checked", this.checked);'.PHP_EOL;
		$script.= '		if(this.checked){'.PHP_EOL;
		$script.= '			$(".'.$this->type.'_need_check").removeClass("disabled");'.PHP_EOL;
		foreach($this->requireCheck as $selector){
			$script.= '			$("'.$selector.'").removeClass("disabled");'.PHP_EOL;
		}
		$script.= '		}else{'.PHP_EOL;
		$script.= '			$(".'.$this->type.'_need_check").addClass("disabled");'.PHP_EOL;
		foreach($this->requireCheck as $selector){
			$script.= '			$("'.$selector.'").addClass("disabled");'.PHP_EOL;
		}
		foreach($this->requireOneCheck as $selector){
			$script.= '			$("'.$selector.'").addClass("disabled");'.PHP_EOL;
		}
		$script.= '		}'.PHP_EOL;
		$script.= '	});'.PHP_EOL;
		
		$script.= '	$(".'.$this->type.'s_check").change(function(){'.PHP_EOL;
		$script.= '		var check = ($(".'.$this->type.'s_check").filter(":checked").length == $(".'.$this->type.'s_check").length);'.PHP_EOL;
		$script.= '		$("#selectAll").prop("checked", check);'.PHP_EOL;
		$script.= '		if($(".'.$this->type.'s_check").filter(":checked").length>0){'.PHP_EOL;
		$script.= '			if($(".'.$this->type.'s_check").filter(":checked").length==1){'.PHP_EOL;
		foreach($this->requireOneCheck as $selector){
			$script.= '				$("'.$selector.'").removeClass("disabled");'.PHP_EOL;
		}
		$script.= '			}else{'.PHP_EOL;
		foreach($this->requireOneCheck as $selector){
			$script.= '				$("'.$selector.'").addClass("disabled");'.PHP_EOL;
		}
		$script.= '			}'.PHP_EOL;
		$script.= '			$(".'.$this->type.'_need_check").removeClass("disabled");'.PHP_EOL;
		foreach($this->requireCheck as $selector){
			$script.= '			$("'.$selector.'").removeClass("disabled");'.PHP_EOL;
		}
		$script.= '		}else{'.PHP_EOL;
		foreach($this->requireOneCheck as $selector){
			$script.= '			$("'.$selector.'").addClass("disabled");'.PHP_EOL;
		}
		$script.= '			$(".'.$this->type.'_need_check").addClass("disabled");'.PHP_EOL;
		$script.= '		}'.PHP_EOL;
		$script.= '	});'.PHP_EOL;
		$script.= implode($this->scripts, PHP_EOL);
		$script.= '})'.PHP_EOL;
		
		$this->script = $script;
		return $this;
	}

}

?>