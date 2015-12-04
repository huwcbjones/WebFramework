<?php

/**
 * HTML Form Builder Class
 *
 * Creates forms with a variety of components
 *
 * @category   Plugins.Bootstrap.Form.Core
 * @package    form.php
 * @site       www.biggleswadesc.org
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
*/
class Modalconf extends BasePlugin
{
	const name_space = 'Plugins.Bootstrap.ModalConf';
	const version = '1.0.0';

	public	$page;
	
	private	$_modal;
	public	$form;
	private	$_content;
	private $_script;
	private $_html;
	
	private	$action		= '';
	private $type		= '';
	private $webAction	= '';
	private $method		= '';
	
	function __construct($parent, $action, $type, $webAction, $method='')
	{
		$this->parent = $parent;
		$this->parent->parent->debug('***** ' . $this::name_space . ' *****');
		$this->parent->parent->debug($this::name_space . ': Version ' . $this::version);
		
		$this->action		= $action;
		$this->type			= $type;
		$this->webAction	= $webAction;
		$this->method		= $method;
		$this->_modal = $this->parent->getPlugin('modal', array());
	}
	
	function addDefaultConfig($action='', $type='', $webAction='', $method=''){
		if($action		=='') $action	= $this->action;
		if($type		=='') $type		= $this->type;
		if($webAction	=='') $webAction= $this->webAction;
		if($method		=='') $method	= $this->method;
		
		$this->addDefaultScript();
		$this->addForm();
		$this->form
			->setColumns(4, 8)
			->addHiddenField(str_replace(' ', '_', strtolower($type)).'s', '', str_replace(' ', '_', strtolower($type)).'_to_'.str_replace(' ', '_', strtolower($action)));
		return $this;
	}
	
	function addDefaultScript(){
		$type = str_replace(' ', '_', strtolower($this->type));
		$action = str_replace(' ', '_', strtolower($this->action));
		$script = '<script type="text/javascript">'.PHP_EOL;
		$script.= 'function '.$action.'_mod(){'.PHP_EOL;
		$script.= '	var '.$type.'s = $(".'.$type.'s_check").filter(":checked")'.PHP_EOL;
		$script.= '	if('.$type.'s.length!=0){'.PHP_EOL;
		$script.= '		$("#'.$type.'_'.$action.'s").html("");'.PHP_EOL;
		$script.= '		$("#'.$type.'_to_'.$action.'").val("");'.PHP_EOL;
		$script.= '		'.$type.'s.each(function(){'.PHP_EOL;
		$script.= '			$("#'.$type.'_'.$action.'s").append("<li>"+$("#i_"+$(this).val()).text()+"</li>");'.PHP_EOL;
		$script.= '			$("#'.$type.'_to_'.$action.'").val($(this).val()+","+$("#'.$type.'_to_'.$action.'").val());'.PHP_EOL;
		$script.= '		});		'.PHP_EOL;
		$script.= '		$("#'.$action.'_'.$type.'s").modal("show");'.PHP_EOL;
		$script.= '	}'.PHP_EOL;
		$script.= '	return false;'.PHP_EOL;
		$script.= '}'.PHP_EOL;
		$script.= '</script>'.PHP_EOL;
		$this->_script = $script;
		return $this;
	}
	
	function addForm($action='', $type='', $webAction='', $method=''){
		if($action		=='') $action	= $this->action;
		if($type		=='') $type		= $this->type;
		if($webAction	=='') $webAction= $this->webAction;
		if($method		=='') $method	= $this->method;
		$this->form = $this->parent->getPlugin('form', array(str_replace(' ', '_', strtolower($type)).'_'.str_replace(' ', '_', strtolower($action)).'_form', $webAction, $method));
		return $this;
	}
	
	function setDefaultContent($action='', $type=''){
		if($action		=='') $action	= $this->action;
		if($type		=='') $type		= $this->type;
		
		$content = '<p>Are you sure you want to '.$action.' the following '.$type.'(s)?</p>'.PHP_EOL;
		$content.= '<ul id="'.str_replace(' ', '_', strtolower($type)).'_'.str_replace(' ', '_', strtolower($action)).'s"></ul>'.PHP_EOL;
		$this->form->build();
		$content.= $this->form->getForm();
		$this->_content = $content;
		return $this;
	}
	function setContent($content){
		$this->_content = $content;
		return $this;
	}
	
	function setDefaultModal($action='', $type=''){
		if($action		=='') $action	= $this->action;
		if($type		=='') $type		= $this->type;
		$this->_modal
			->setTitle(ucfirst($action).' '.ucfirst($type).'(s)?')
			->setBody($this->_content);
		$type = str_replace(' ','_',strtolower($type));
		$action = str_replace(' ','_',strtolower($action));
		$this->_modal
			->setID($action.'_'.$type.'s')
			->setLeft('default','Cancel','remove-sign','button','$(\'#'.$action.'_'.$type.'s\').modal(\'hide\')')
			->setRight('primary','OK','ok-sign','button','processModal(\''.$action.'_'.$type.'\',\''.$type.'_'.$action.'_form\', this, \'ok\')');
		return $this;
	}
	function setTitle($title){
		$this->_modal->setTitle($title);
		return $this;
	}
	function setLeftBtn($mode,$text,$glyph="",$type='close',$onclick='',$data=array()){
		$this->_modal->setLeft($mode,$text,$glyph,$type,$onclick,$data);
		return $this;
	}
	function setRightBtn($mode,$text,$glyph="",$type='button',$onclick='',$data=array()){
		if($onclick==''){
			$onclick = 'processModal(\''.str_replace(' ', '_', strtolower($this->action)).'_'.str_replace(' ', '_', strtolower($this->type)).'s\',\''.$this->form->getID().'\', this, \''.strtolower($text).'\')';
		}
		$this->_modal->setRight($mode,$text,$glyph,$type,$onclick,$data);
		return $this;
	}
	function setCentreBtn($mode,$text,$glyph="",$type='button',$onclick='',$data=array()){
		$this->_modal->setCentre($mode,$text,$glyph,$type,$onclick,$data);
		return $this;
	}
	
	function addScript($script){
		$this->_script = $script;
		return $this;
	}
	
	function addShowScript(){
		$type = str_replace(' ', '_', strtolower($this->type));
		$action = str_replace(' ', '_', strtolower($this->action));
		$script = '<script type="text/javascript">'.PHP_EOL;
		$script.= 'function '.$action.'_mod(){ $("#'.$action.'_'.$type.'s").modal("show"); return false; }'.PHP_EOL;
		$script.= '</script>'.PHP_EOL;
		$this->_script = $script;
		return $this;
	}
	
	function build(){
		$this->_modal->create();
		$this->_html = $this->_modal->getModal();
		$this->_html.= $this->_script;
		return $this;
	}
	
	function getModal(){
		return $this->_html;
	}
}
?>