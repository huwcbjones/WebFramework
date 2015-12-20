<?php
/**
 * Form Extras
 *
 * Adds extra components
 *
 * @category   Plugins.Bootstrap.Form.Extras
 * @package    form.extras.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */
class FormExtras {
	
	private $_extras = '';
	private $script = '';
	private $_scripts = array();
	private $comps = array();

	private $_indent = '';
	private $_built = false;

	private $_CSS	= array();
	private $_JS	= array();
	
	function build(){
		global $page;
		$this->_extras = $this->_indent.$this->_extras;
		foreach($this->comps as $field){
			$this->_extras.= $field;
		}
		$this->_extras.= $this->_indent.'<script type="text/javascript">'.PHP_EOL;
		$this->_extras.= implode(PHP_EOL, $this->_scripts);
		$this->_extras.= $this->_indent.'</script>'.PHP_EOL;
		foreach($this->_JS as $script){
			if(!in_array($script, $page->js)) $page->js[] = $script;
		}
		foreach($this->_CSS as $style){
			if(!in_array($style, $page->css)) $page->css[] = $style;
		}
		$this->_built = true;
	}
	
	function getExtra(){
		if(!$this->_built){
			trigger_error('Call FormExtras::build() before FormExtras::getExtra()', E_USER_WARNING);
		}
		return $this->_extras;
	}
	
	function setIndent($indent){
		$this->_indent = $indent;
	}

	function addHTML($html){
		$this->comps[] = $html;
	}
	
	function addDeleteConfModal($type, $action, $auth=false){
		$this->_ConfModal('delete', 'trash', $type, $action, $auth);
	}
	
	function addEnableConfModal($type, $action, $auth=false){
		$this->_ConfModal('enable', 'ok-sign', $type, $action, $auth);
		//$this->_addEnableConfModalScript($type);
	}
	
	function addDisableConfModal($type, $action, $auth=false){
		$this->_ConfModal('disable', 'ok-sign', $type, $action, $auth);
		//$this->_addDisableConfModalScript($type);
	}
	
	function addCustomConfModal($prefix, $confIcon, $type, $action, $prompt = '', $auth=false, $conf=false){
		$this->_ConfModal($prefix, $confIcon, $type, $action, $auth, $conf, $prompt, true);
	}
	
	private function _ConfModal($mode, $icon, $type, $action, $auth=false, $conf=false, $prompt = '', $cust=false){
		$mod_id = substr(crc32(microtime()), 1, 6);
		if($prompt==''){
			$prompt = '<p>Are you sure you want to '.$mode.' the following '.$type.':</p>';
		}
		$mod_content = $prompt.PHP_EOL;
		$mod_content.= '<ul id="'.$mode.'_'.$type.'"></ul>'.PHP_EOL;
		$mod_form = new Form($mode.'_f', $action, 'post', 'application/x-www-form-urlencoded', $mode.'_'.$type.'(); return false;');
		$mod_form->setColumns(4,8);
		$mod_form->addHiddenField($type, '', $type.'_to_'.$mode);
		if($auth){
			$mod_form->addPasswordField(
				'Your Password',
				'pwd',
				'',
				array('t'=>'Your password to confirm the operation','p'=>'Your Password'),
				array(
					't'=>'password',
					'r'=>true
				)
			);
		}
		if($conf){
			$mod_form->addButtonGroup(
				'Are you sure?',																		// Group Label
				'conf',																					// Name
				array(
					array(
						'i'=>'confY'.$mod_id,																	// ID
						's'=>B_T_SUCCESS,																// Active Style
						'v'=>1,																			// Value
						'l'=>'Yes',																		// Label
						'c'=>false																		// Checked
					),
					array(
						'i'=>'confN'.$mod_id,																	// ID
						's'=>B_T_FAIL,																	// Active Style
						'v'=>0,																			// Value
						'l'=>'No',																		// Label
						'c'=>true																		// Checked
					)
				),
				array('t'=>'Are you sure you wish to perform this action?')					// Help Text
			);
		}
		$mod_form->build();
		$mod_content.= $mod_form->getForm();
		$mod = new Modal();
		$mod->setID($mode);
		$mod->setTitle(ucfirst($mode).' '.ucfirst($type).'?');
		$mod->setBody($mod_content);
		$mod->setLeft('default','Cancel','remove-sign','button','$(\'#'.$mode.'\').modal(\'hide\')');
		$mod->setRight('danger','OK',$icon,'button',$mode.'_'.$type.'(this)');
		$mod->create();
		$this->comps[] = $mod->getModal();
		$this->_addConfModalScript($mode, $type, $cust);
	}
	
	// Javascript Generation
	
	function addScript($script){
		$line = '';
		foreach(preg_split('/\r|\r\n|\n/', $script) as $comp){
			if(trim($comp)!='') $line.= $this->_indent.$comp.PHP_EOL;
		}
		$this->_scripts[] = $line;
	}
	
	private function _addConfModalScript($mode, $type, $custom=false){
		$script = 'function '.$mode.'_mod(){'.PHP_EOL;
		if(!$custom){
			$script.= '	var items = $(".'.$type.'_check").filter(":checked");'.PHP_EOL;
			$script.= '	if(items.length!=0){'.PHP_EOL;
			$script.= '		$("#'.$mode.'_'.$type.'").html("");'.PHP_EOL;
			$script.= '		$("#'.$type.'_to_'.$mode.'").val("");'.PHP_EOL;
			$script.= '		items.each(function(){'.PHP_EOL;
			$script.= '			$("#'.$mode.'_'.$type.'").append("<li>"+$("#i_"+$(this).val()).html()+"</li>");'.PHP_EOL;
			$script.= '			$("#'.$type.'_to_'.$mode.'").val($(this).val()+","+$("#'.$type.'_to_'.$mode.'").val());'.PHP_EOL;
			$script.= '		});'.PHP_EOL;
			$script.= '		$("#'.$mode.'").modal("show");'.PHP_EOL;
			$script.= '	}'.PHP_EOL;
			$script.= '	return false;'.PHP_EOL;
		}else{
			$script.= '		$("#'.$mode.'").modal("show");'.PHP_EOL;
		}
		$script.= '}'.PHP_EOL;
		$script.= 'function '.$mode.'_'.$type.'(btn){'.PHP_EOL;
		$script.= '	$(btn).attr("data-loading-text", "Working...").button("loading");'.PHP_EOL;
		$script.= '	var frm		= document.getElementById("'.$mode.'_f");'.PHP_EOL;
		$script.= '	var jfrm	= $(frm);'.PHP_EOL;
		$script.= '	$.post(jfrm.attr("action"), jfrm.serialize(), function(data){'.PHP_EOL;
		$script.= '		if(data.status==1){'.PHP_EOL;
		$script.= '			window.location.href = data.url;'.PHP_EOL;
		$script.= '		}else{'.PHP_EOL;
		$script.= '			$(".alert").alert("close");'.PHP_EOL;
		$script.= '			$($.parseHTML(data.msg)).insertAfter("#page-title");'.PHP_EOL;
		$script.= '			$("#'.$mode.'").modal("hide");'.PHP_EOL;
		$script.= '		}'.PHP_EOL;
		$script.= '		$(btn).button("reset");'.PHP_EOL;
		$script.= '	},"json");'.PHP_EOL;
		$script.= '};'.PHP_EOL;
		$this->_scripts[$mode.'ModConf'] = $script;
	}
}
?>