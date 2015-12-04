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
class Form extends BasePlugin
{
	const name_space = 'Plugins.Bootstrap.Form';
	const version = '1.0.0';

	public $page;

	private $form = '';
	private $script = '';
	private $fields = array();

	private $formID = 'form0';
	private $_formAction;
	private $_formMethod;
	private $_formEnc;
	private $_formAF = 'off';
	private $_formClasses = array('form-horizontal');
	private $_onSubmit = '';
	private $_target = '';

	private $tabIndex = 1;
	private $_indent = '';
	private $_built = false;

	private $_label_col = 3;
	private $_field_col = 5;
	private $_valid_col = 4;

	private $_spryCSS = array();
	private $_spryJS = array();

	/**
	 * Form::__construct()
	 * 
	 * @param mixed $parent
	 * @param mixed $id
	 * @param mixed $action
	 * @param string $method
	 * @param string $enctype
	 * @param string $onSubmit
	 * @param string $target
	 * @return
	 */
	function __construct($parent, $id, $action, $method = 'get', $enctype =
		'application/x-www-form-urlencoded', $onSubmit = '', $target = '')
	{
		$this->parent = $parent;
		$this->parent->parent->debug('***** ' . $this::name_space . ' *****');
		$this->parent->parent->debug($this::name_space . ': Version ' . $this::version);

		$this->formID = $id;
		$this->_formAction = $action;
		$this->_formMethod = $method;
		$this->_formEnc = $enctype;
		$this->_onSubmit = $onSubmit;
		$this->_target = $target;
		$this->_genFormHeader();
	}

	/**
	 * Form::addFormClass()
	 * 
	 * @param mixed $class
	 * @return
	 */
	function addFormClass($class)
	{
		$this->parent->parent->debug($this::name_space . ': Added form class "' . $class .
			'"');
		$this->_formClasses[] = $class;
		$this->_formClasses = array_unique($this->_formClasses);
		$this->_genFormHeader();
		return $this;
	}
	/**
	 * Form::setColumns()
	 * 
	 * @param integer $label
	 * @param integer $field
	 * @param integer $validation
	 * @return
	 */
	function setColumns($label = 3, $field = 5, $validation = -1)
	{
		if ($validation == -1)
			$validation = 12 - $label - $field;
		$this->_label_col = $label;
		$this->_field_col = $field;
		$this->_valid_col = $validation;
		$this->parent->parent->debug($this::name_space . ': Setting column widths: L "' .
			$label . '", F "' . $field . '", V "' . $validation . '", ');
		return $this;
	}
	/**
	 * Form::setIndent()
	 * 
	 * @param mixed $indent
	 * @return
	 */
	function setIndent($indent)
	{
		$this->_setIndent($indent);
		$this->_genFormHeader();
		return $this;
	}
	/**
	 * Form::setAutofill()
	 * 
	 * @param bool $autofill
	 * @return
	 */
	function setAutofill($autofill = false)
	{
		if ($autofill) {
			$this->_formAF = 'on';
		} else {
			$this->_formAF = 'off';
		}
		$this->parent->parent->debug($this::name_space . ': Autofill set to "' . $this->
			_formAF . '"');
		$this->_genFormHeader();
		return $this;
	}
	/**
	 * Form::getForm()
	 * 
	 * @return
	 */
	function getForm()
	{
		if (!$this->_built) {
			trigger_error('Call Form::build() before Form::getForm()', E_USER_WARNING);
		}
		return $this->form;
	}

	function getID(){
		return $this->formID;
	}
	/**
	 * Form::addHTML()
	 * 
	 * @param mixed $html
	 * @param bool $line
	 * @return
	 */
	function addHTML($html, $line = true)
	{
		if ($line) {
			$html = $this->_line($html);
		}
		$this->fields[] = $html;
		return $this;
	}


	/**
	 * Form::_genFormHeader()
	 * 
	 * @return
	 */
	private function _genFormHeader()
	{
		$this->parent->parent->debug($this::name_space . ': Generating form header...');
		$form = $this->_indent . '<form';
		$form .= ' class="';
		foreach($this->_formClasses as $class) {
			$form .= $class . ' ';
		}
		$form .= '"';
		$form .= ' id="' . $this->formID . '"';
		$form .= ' action="' . $this->_registerAction() . '"';
		$form .= ' method="' . $this->_formMethod . '"';
		$form .= ' enctype="' . $this->_formEnc . '"';
		$form .= ' autofill="' . $this->_formAF . '"';
		if ($this->_target != '')
			$form .= ' target="' . $this->_target . '"';
		if ($this->_onSubmit != '')
			$form .= ' onsubmit="' . $this->_onSubmit . '"';

		$form .= '>' . PHP_EOL;
		$this->form = $form;
	}

	/**
	 * Form::_registerAction()
	 * 
	 * @return
	 */
	private function _registerAction()
	{
		$action = $this->_formAction;
		if ($action['ajax']) {
			$this->_onSubmit = 'processForm(\'' . $this->formID . '\'); return false;';
		}
		return '/action/' . $action['controller'] . '/' . $action['action'];
	}
	/**
	 * Form::build()
	 * 
	 * @return
	 */
	function build()
	{
		$this->form = $this->_indent . $this->form;
		foreach($this->fields as $field) {
			$this->form .= $field;
		}
		$this->form .= $this->_indent . '</form>' . PHP_EOL;
		$this->form .= $this->_indent . '<script type="text/javascript">' . PHP_EOL;
		$this->form .= $this->script;
		$this->form .= $this->_indent . '</script>' . PHP_EOL;
		foreach($this->_spryJS as $script) {
			$this->parent->addJS($script);
		}
		foreach($this->_spryCSS as $style) {
			$this->parent->addCSS($style);
		}
		$this->_built = true;
		return $this;
	}

	/**
	 * Form::addReCAPTCHA()
	 * 
	 * @return
	 */
	function addReCAPTCHA()
	{
		$reCaptchaPub = $this->parent->parent->config->config['reCAPTCHA']['pub'];
		$reCaptchaPriv = $this->parent->parent->config->config['reCAPTCHA']['priv'];

		require_once ('lib/modules/recaptchalib.php');
		$field = $this->_indent . '<script type="text/javascript">' . PHP_EOL;
		$field .= $this->_indent .
			'var RecaptchaOptions = {theme : "custom", custom_theme_widget: "recaptcha_widget", tabindex: ' .
			$this->tabIndex . ' };' . PHP_EOL;
		$field .= $this->_indent . '</script>' . PHP_EOL;
		$field .= $this->_indent . '<div id="recaptcha_widget" style="display:none">' .
			PHP_EOL;

		$recaptcha = $this->_label('reCAPTCHA', '', array('t' => ''), array('r' => true));
		$recaptcha .= $this->_indent . '    <div class="col-sm-' . ($this->_field_col +
			$this->_valid_col) . '">' . PHP_EOL;
		$recaptcha .= $this->_indent . '      <a id="recaptcha_image" href="#"></a>' .
			PHP_EOL;
		$recaptcha .= $this->_indent . '    </div>' . PHP_EOL;

		$field .= $this->_line($recaptcha);

		$input = $this->_indent . '    <div class="col-sm-' . $this->_label_col . '">' .
			PHP_EOL;
		$input .= $this->_indent .
			'      <label class="recaptcha_only_if_image control-label">Enter the phrase above:</label>' .
			PHP_EOL;
		$input .= $this->_indent .
			'      <label class="recaptcha_only_if_audio control-label">Enter the numbers you hear:</label>' .
			PHP_EOL;
		$input .= $this->_indent . '    </div>' . PHP_EOL;

		$input .= $this->_indent . '    <div class="col-sm-' . $this->_field_col . '">' .
			PHP_EOL;
		$input .= $this->_indent . '      <div class="input-group">' . PHP_EOL;
		$input .= $this->_indent .
			'        <input type="text" id="recaptcha_response_field" name="recaptcha_response_field" class="input-recaptcha form-control" />' .
			PHP_EOL;
		$input .= $this->_indent . '        <span class="input-group-btn">' . PHP_EOL;
		$input .= $this->_indent .
			'          <a class="btn btn-default" href="javascript:Recaptcha.reload()"><span class="' .
			B_ICON . ' ' . B_ICON . '-refresh"></span></a>' . PHP_EOL;
		$input .= $this->_indent .
			'          <a class="btn btn-default recaptcha_only_if_image" href="javascript:Recaptcha.switch_type(\'audio\')"><span title="Get an audio CAPTCHA" class="' .
			B_ICON . ' ' . B_ICON . '-headphones"></span></a>' . PHP_EOL;
		$input .= $this->_indent .
			'          <a class="btn btn-default recaptcha_only_if_audio" href="javascript:Recaptcha.switch_type(\'image\')"><span title="Get an image CAPTCHA" class="' .
			B_ICON . ' ' . B_ICON . '-picture"></span></a>' . PHP_EOL;
		$input .= $this->_indent .
			'          <a class="btn btn-default" href="javascript:Recaptcha.showhelp()"><span class="' .
			B_ICON . ' ' . B_ICON . '-question-sign"></span></a>' . PHP_EOL;
		$input .= $this->_indent . '        </span>' . PHP_EOL;
		$input .= $this->_indent . '      </div>' . PHP_EOL;
		$input .= $this->_indent . '    </div>' . PHP_EOL;

		$field .= $this->_line($input);

		$field .= $this->_indent . '</div>' . PHP_EOL;
		$field .= $this->_indent .
			'<script type="text/javascript" src="https://www.google.com/recaptcha/api/challenge?k=' .
			$reCaptchaPub . '"></script>' . PHP_EOL;
		$field .= $this->_indent . '<noscript>' . PHP_EOL;
		$field .= $this->_indent .
			'  <iframe src="https://www.google.com/recaptcha/api/noscript?k=' . $reCaptchaPub .
			'" height="300" width="500" frameborder="0"></iframe><br>' . PHP_EOL;
		$field .= $this->_indent .
			'  <textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>' .
			PHP_EOL;
		$field .= $this->_indent .
			'  <input type="hidden" name="recaptcha_response_field" value="manual_challenge">' .
			PHP_EOL;
		$field .= $this->_indent . '</noscript>' . PHP_EOL;

		$this->fields['reCAPTCHA'] = $field;
		return $this;
	}

	/**
	 * Form::addBtnLine()
	 * 
	 * @param mixed $buttons
	 * @return
	 */
	function addBtnLine($buttons = array())
	{
		$width = $this->_label_col + $this->_field_col;
		$btn_width = floor(12 / count($buttons));

		$field = $this->_indent . '    <div class="col-sm-' . $width . '">' . PHP_EOL;
		$field .= $this->_indent . '      <div class="row">' . PHP_EOL;

		foreach($buttons as $id => $var) {
			if (!array_key_exists('w', $var))
				$var['w'] = $btn_width;
			if (!array_key_exists('h', $var))
				$var['h'] = 'large';
			if (!array_key_exists('s', $var))
				$var['s'] = B_T_DEFAULT;
			if (!array_key_exists('a', $var))
				$var['a'] = array(
					't' => 'url',
					'a' => '#',
					'oc' => '');
			if (!array_key_exists('l', $var))
				$var['l'] = ucfirst($id);
			if (!array_key_exists('ic', $var))
				$var['ic'] = '';
			if (!array_key_exists('oc', $var['a']))
				$var['a']['oc'] = '';
			$field .= $this->_addCtrlBtn($var['w'], $var['h'], $var['s'], $var['a'], $var['l'],
				$var['ic']);
		}
		$field .= $this->_indent . '      </div>' . PHP_EOL;
		$field .= $this->_indent . '    </div>' . PHP_EOL;
		$field = $this->_line($field);
		$this->fields[] = $field;
		return $this;
	}

	/**
	 * Form::addHiddenField()
	 * 
	 * @param mixed $name
	 * @param mixed $value
	 * @param string $id
	 * @return
	 */
	function addHiddenField($name, $value, $id = '')
	{
		if ($id == '')
			$id = $this->formID . '::' . $name;

		$field = $this->_indent . '  <input type="hidden" name="' . $name . '" value="' .
			$value . '" id="' . $id . '" />' . PHP_EOL;
		$this->fields[$name] = $field;
		return $this;
	}
	
	/**
	 * Form::addSelect2()
	 * 
	 * @param mixed $title
	 * @param mixed $name
	 * @param mixed $value
	 * @param mixed $help
	 * @param mixed $options
	 * @return
	 */
	function addSelect2($title, $name, $value, $help = array('t' => '', 'p' => ''),
		$options = array())
	{
		if (!array_key_exists('d', $options))
			$options['d'] = false;
		if (!array_key_exists('classes', $options))
			$options['classes'] = array();
		if (!array_key_exists('p', $help))
			$help['p'] = '';
		if (!array_key_exists('r', $options))
			$options['r'] = false;
		if (!array_key_exists('ro', $options))
			$options['ro'] = false;
			
		$id = $this->formID . '::' . $name;

		$field = $this->_label($title, $name, $help, $options);
		$field .= $this->_input('hidden', $name, $id, $value, $options, $help['p']);
		$field = $this->_line($field);
		$this->fields[$name] = $field;
		
		$this->_spryCSS['select2'] = 'core/select2/select2.css';
		$this->_spryCSS['select2bootstrap'] = 'core/select2/select2-bootstrap.css';
		$this->_spryJS['select2'] = 'core/select2/select2.js';
		return $this;
	}
	
	/**
	 * Form::addDateTime()
	 * 
	 * @param mixed $title
	 * @param mixed $name
	 * @param mixed $value
	 * @param mixed $help
	 * @param mixed $options
	 * @return
	 */
	function addDateTime($title, $name, $value, $help = array('t' => '', 'p' => ''),
		$options = array())
	{
		if (!array_key_exists('d', $options))
			$options['d'] = false;
		if (!array_key_exists('classes', $options))
			$options['classes'] = array('form-control');
		if (!array_key_exists('p', $help))
			$help['p'] = '';
		if (!array_key_exists('r', $options))
			$options['r'] = false;
		if (!array_key_exists('ro', $options))
			$options['ro'] = false;
			
		$id = $this->formID . '::' . $name;

		$field = $this->_label($title, $name, $help, $options);
		
		$width = $this->_field_col;
		if (!in_array('form-control', $options['classes'])) $options['classes'][] = 'form-control';
		$input = $this->_indent . '    <div class="col-sm-' . $width . '">' . PHP_EOL;
		$input.= $this->_indent . '      <div class="input-group date" id="datetimepicker_'.$name.'">' . PHP_EOL;
		$input.= $this->_indent . '        <input data-date-format="YYYY-MM-DD HH:mm" type="text"';
		if (count($options['classes'] != 0)){
			$input .= ' class="' . implode(' ', $options['classes']) . '"';
		}
		$input .= ' name="' . $name . '" id="' . $id . '" placeholder="' . $help['p'] .
			'"';
		if ((string) $value != '')
			$input .= 'value="' . $value . '"';
		if ($options['r'])
			$input .= ' required="required"';
		if ($options['d'])
			$input .= ' disabled="disabled"';
		if ($options['ro'])
			$input .= ' readonly="readonly"';
		if (array_key_exists('a', $options)) {
			if ($options['a'] !== '')
				$input .= 'accept="' . $options['a'] . '"';
		}
		$input.= ' tabindex="' . $this->tabIndex . '" />' . PHP_EOL;
		$this->tabIndex++;
		$input.= $this->_indent . '        <span class="input-group-addon"><span class="'.B_ICON.' '.B_ICON.'-calendar"></span></span>'.PHP_EOL;
		$input.= $this->_indent . '      </div>' . PHP_EOL;
		$input.= $this->_indent . '    </div>' . PHP_EOL;
		
		$field.= $input;
		$field = $this->_line($field);
		$this->fields[$name] = $field;
		$this->_spryJS['moment'] = 'core/moment.js';
		$this->_spryJS['datetime'] = 'core/datetimepicker/datetimepicker.min.js';
		$this->_spryCSS['datetime'] = 'core/datetimepicker/datetimepicker.css';
		$this->addScript('$("#datetimepicker_'.$name.'").datetimepicker();');
		return $this;
	}

	/**
	 * Form::addTextField()
	 * 
	 * @param mixed $title
	 * @param mixed $name
	 * @param mixed $value
	 * @param mixed $help
	 * @param mixed $options
	 * @return
	 */
	function addTextField($title, $name, $value, $help = array('t' => '', 'p' => ''),
		$options = array())
	{
		if (!array_key_exists('v', $options))
			$options['v'] = false;
		if (!array_key_exists('r', $options))
			$options['r'] = false;
		if (!array_key_exists('t', $options))
			$options['t'] = 'text';
		if (!array_key_exists('d', $options))
			$options['d'] = false;
		if (!array_key_exists('ro', $options))
			$options['ro'] = false;
		if (!array_key_exists('vt', $options))
			$options['vt'] = $options['t'];
		if (!array_key_exists('classes', $options))
			$options['classes'] = array();
		if (!array_key_exists('p', $help))
			$help['p'] = '';

		$id = $this->formID . '::' . $name;

		$field = $this->_label($title, $name, $help, $options);
		$field .= $this->_input($options['t'], $name, $id, $value, $options, $help['p'], '', $options['v']);
		$field = $this->_line($field);
		$this->fields[$name] = $field;
		return $this;
	}

	/**
	 * Form::addFileUpload()
	 * 
	 * @param mixed $title
	 * @param mixed $name
	 * @param mixed $help
	 * @param mixed $options
	 * @return
	 */
	function addFileUpload($title, $name, $help = array('t' => '', 'p' => ''), $options =
		array())
	{
		if (!array_key_exists('r', $options))
			$options['r'] = false;
		if (!array_key_exists('d', $options))
			$options['d'] = false;
		if (!array_key_exists('classes', $options))
			$options['classes'] = array();
		if (!array_key_exists('ro', $options))
			$options['ro'] = false;
		if (!array_key_exists('a', $options))
			$options['a'] = '';
		if (!array_key_exists('p', $help))
			$help['p'] = '';

		$id = $this->formID . '::' . $name;

		$field = $this->_label($title, $name, $help, $options);
		$field .= $this->_input('file', $name, $id, '', $options, $help['p']);
		$field = $this->_line($field);
		$this->fields[$name] = $field;
		return $this;
	}

	/**
	 * Form::addPasswordField()
	 * 
	 * @param mixed $title
	 * @param mixed $name
	 * @param mixed $value
	 * @param mixed $help
	 * @param mixed $options
	 * @return
	 */
	function addPasswordField($title, $name, $value, $help = array('t' => '', 'p' =>
			''), $options = array())
	{
		if (!array_key_exists('r', $options))
			$options['r'] = false;
		if (!array_key_exists('d', $options))
			$options['d'] = false;
		if (!array_key_exists('v', $options))
			$options['v'] = false;
		if (!array_key_exists('w', $options))
			$options['w'] = false;
		if (!array_key_exists('ro', $options))
			$options['ro'] = false;
		if (!array_key_exists('classes', $options))
			$options['classes'] = array();
		if (!array_key_exists('p', $help))
			$help['p'] = '';

		$id = $this->formID . '::' . $name;
		$pwd_id = substr(crc32(microtime()), 1, 7);

		if ($options['v']) {
			if(!$options['w']){
				$field_width = ceil(($this->_field_col + $this->_valid_col) / 1.6);
				$valid_width = ($this->_field_col + $this->_valid_col) - $field_width;
			}else{
				$field_width = $this->_field_col;
				$valid_width = $this->_valid_col;
			}
			
			$script = 'var options = {};' . PHP_EOL;
			$script .= 'options.common = {' . PHP_EOL;
			$script .= '  minChar: 1,' . PHP_EOL;
			$script .= '};' . PHP_EOL;
			$script .= 'options.ui = {' . PHP_EOL;
			$script .= '  container: "#pwd-container-' . $pwd_id . '",' . PHP_EOL;
			$script .= '  viewports: {' . PHP_EOL;
			$script .= '    progress: "#pwstrength_viewport_progress-' . $pwd_id . '",' . PHP_EOL;
			$script .= '    verdict: "#pwstrength_viewport_verdict-' . $pwd_id . '",' . PHP_EOL;
			$script .= '    errors: "#pwstrength_viewport_errors-' . $pwd_id . '"' . PHP_EOL;
			$script .= '  }' . PHP_EOL;
			$script .= '};' . PHP_EOL;
			$script .= '$("#' . str_replace(':', '\\\\:', $id) . '").pwstrength(options);' . PHP_EOL;
			$this->addScript($script);
			$this->_spryJS['password'] = 'core/pwstrength/plugin-1.0.2.min.js';
		} else {
			$field_width = $this->_field_col;
		}

		$field = $this->_label($title, $name, $help, $options);
		$field .= $this->_input('password', $name, $id, $value, $options, $help['p'], $field_width, $options['v'], $pwd_id);

		$field = $this->_line($field, "pwd-container-" . $pwd_id);
		$this->fields[$name] = $field;
		return $this;
	}

	/**
	 * Form::addTextArea()
	 * 
	 * @param mixed $title
	 * @param mixed $name
	 * @param mixed $value
	 * @param integer $rows
	 * @param mixed $help
	 * @param mixed $options
	 * @return
	 */
	function addTextArea($title, $name, $value, $rows = 3, $help = array('t' => '',
			'p' => ''), $options = array())
	{
		if (!array_key_exists('v', $options))
			$options['v'] = false;
		if (!array_key_exists('ck', $options))
			$options['ck'] = false;
		if (!array_key_exists('ckt', $options))
			$options['ckt'] = 'Full';
		if (!array_key_exists('r', $options))
			$options['r'] = false;
		if (!array_key_exists('t', $options))
			$options['t'] = 'text';
		if (!array_key_exists('d', $options))
			$options['d'] = false;
		if (!array_key_exists('ro', $options))
			$options['ro'] = false;
		if (!array_key_exists('p', $help))
			$help['p'] = '';
		$id = $this->formID . '::' . $name;
		
		$field = $this->_label($title, $name, $help, $options);
		$field .= $this->_textarea($name, $id, $value, $rows, $help['p'], $options['d'],
			$options['ro']);
		if ($options['v']) {
			$field = $this->_spry($id, $field, 'textarea', $name, $options);
		}
		$field = $this->_line($field);
		$this->fields[$name] = $field.'<br />';
		if($options['ck']){
			$this->_spryJS['ckeditor'] = 'core/ckeditor/ckeditor.js';
			$this->addScript('CKEDITOR.replace("'.$name.'",{ toolbar: "'.$options['ckt'].'", height: "'.$rows.'em" });');
		}
		return $this;
	}

	/**
	 * Form::addBtnGroup()
	 * 
	 * @param mixed $title
	 * @param mixed $name
	 * @param mixed $buttons
	 * @param mixed $help
	 * @param mixed $options
	 * @return
	 */
	function addBtnGroup($title, $name, $buttons = array(), $help = array('t' => ''),
		$options = array())
	{
		$this->addButtonGroup($title, $name, $buttons = array(), $help = array('t' => ''),
			$options = array());
		return $this;
	}
	/**
	 * Form::addButtonGroup()
	 * 
	 * @param mixed $title
	 * @param mixed $name
	 * @param mixed $buttons
	 * @param mixed $help
	 * @param mixed $options
	 * @return
	 */
	function addButtonGroup($title, $name, $buttons = array(), $help = array('t' =>
			''), $options = array())
	{
		if (!array_key_exists('d', $options))
			$options['d'] = false;
		$field = $this->_label($title, $name, $help, $options);
		$id = $this->formID . '::' . $name;

		$field .= $this->_openBtnGroup();
		$ids = array();
		foreach($buttons as $num => $btn) {
			if (!array_key_exists('i', $btn)) {
				trigger_error('ID for button ' . $name . '::UNKNOWN was not found',
					E_USER_WARNING);
				$btn['i'] = 'UNKNOWN';
			}
			if (in_array($btn['i'], $ids)) {
				trigger_error('Duplicate ID for button ' . $name . '::' . $btn['i'],
					E_USER_WARNING);
				$buttons[$num]['i'] = $btn['i'] = 'DUPLICATE' . time();
			} else {
				$ids[] = $btn['i'];
			}
			if (!array_key_exists('l', $btn)) {
				trigger_error('Label for button ' . $id . '::' . $btn['i'] . ' was not found',
					E_USER_WARNING);
				$btn['l'] = 'UNKNOWN';
			}
			if (!array_key_exists('v', $btn)) {
				trigger_error('Value for ' . $id . '::' . $btn['i'] . ' was not found',
					E_USER_WARNING);
				$btn['v'] = 'UNKNOWN';
			}
			if (!array_key_exists('c', $btn)) {
				trigger_error('Checked state for ' . $id . '::' . $btn['i'] .
					' was not found - defaulting to <u>UNCHECKED</u>', E_USER_NOTICE);
				$btn['c'] = 0;
			}
			if (!array_key_exists('s', $btn)) {
				trigger_error('Default button style for ' . $id . '::' . $btn['i'] .
					' was not found', E_USER_WARNING);
				$btn['s'] = B_T_DEFAULT;
			}

			$field .= $this->_addBtn($btn['s'], $name, $btn['i'], $btn['l'], $btn['v'], $btn['c'],
				$options['d']);
		}
		$this->_addBtnScript($buttons);
		$field .= $this->_closeBtnGroup();

		$field = $this->_line($field);
		$this->fields[$name] = $field;
		return $this;
	}

	/**
	 * Form::addSelect()
	 * 
	 * @param mixed $title
	 * @param mixed $name
	 * @param mixed $option
	 * @param mixed $help
	 * @param mixed $options
	 * @return
	 */
	function addSelect($title, $name, $option = array(), $help = array('t' => ''), $options =
		array())
	{
		if (!array_key_exists('m', $options))
			$options['m'] = false;
		if (!array_key_exists('v', $options))
			$options['v'] = false;
		if (!array_key_exists('r', $options))
			$options['r'] = false;
		if (!array_key_exists('d', $options))
			$options['d'] = false;
		if (!array_key_exists('ro', $options))
			$options['ro'] = false;
		$id = $this->formID . '::' . $name;

		$field = $this->_label($title, $name, $help, $options);

		$field .= $this->_openSelect($name, $id, $options['m']);

		if (count($option) != 0) {
			foreach($option as $opt) {
				if (!array_key_exists('n', $opt)) {
					trigger_error('Name for select option ' . $id . '::UNKNOWN was not found',
						E_USER_WARNING);
					$opt['n'] = 'UNKNOWN';
				}
				if (!array_key_exists('v', $opt)) {
					trigger_error('Value for select option ' . $id . '::' . $opt['n'] .
						' was not found', E_USER_WARNING);
					$opt['v'] = '';
				}
				if (!array_key_exists('s', $opt)) {
					trigger_error('Selected state for select option ' . $id . '::' . $opt['n'] .
						' was not found - defaulting to <u>UNSELECTED</u>', E_USER_NOTICE);
					$opt['s'] = 0;
				}
				if (!array_key_exists('d', $opt)) {
					trigger_error('Disabled state for select option ' . $id . '::' . $opt['n'] .
						' was not found - defaulting to <u>ENABLED</u>', E_USER_NOTICE);
					$opt['d'] = 0;
				}

				$field .= $this->_addSelectOption($opt);
			}
		} else {
			//trigger_error('No options passed into select ' . $id, E_USER_WARNING);
		}
		$field .= $this->_closeSelect();
		if ($options['v']) {
			$field = $this->_spry($id, $field, 'select', $name, $options);
		}

		$field = $this->_line($field);
		$this->fields[$name] = $field;
		return $this;
	}

	/**
	 * Form::addCollapseOptGrid()
	 * 
	 * @param mixed $mode
	 * @param mixed $title
	 * @param mixed $name
	 * @param mixed $panels
	 * @param mixed $help
	 * @param mixed $options
	 * @return
	 */
	function addCollapseOptGrid($mode, $title, $name, $panels = array(), $help =
		array('t' => ''), $options = array())
	{
		if (!array_key_exists('v', $options))
			$options['v'] = false;
		if (!array_key_exists('r', $options))
			$options['r'] = false;
		if (!array_key_exists('d', $options))
			$options['d'] = false;
		if (!array_key_exists('ro', $options))
			$options['ro'] = false;
		$id = $this->formID . '::' . $name;

		$field = $this->_label($title, $name, $help, $options);
		$accordion = $this->parent->getPlugin('accordion');
		$accordion->setID(str_replace(array('[', ']'), '', $name));

		if (count($panels) != 0) {
			foreach($panels as $title => $option) {
				$content = '';
				if (count($option) != 0) {
					foreach($option as $opt) {
						if (!array_key_exists('i', $opt)) {
							$opt['i'] = 'UNKNOWN' . time();
							trigger_error('ID for option ' . $id . '::' . $opt['i'] . ' was not found',
								E_USER_WARNING);
						}
						if (!array_key_exists('l', $opt)) {
							trigger_error('Label for option ' . $id . '::' . $opt['i'] . ' was not found',
								E_USER_WARNING);
							$opt['l'] = 'UNKOWN OPTION';
						}
						if (!array_key_exists('v', $opt)) {
							trigger_error('Value for option ' . $id . '::' . $opt['i'] . ' was not found',
								E_USER_WARNING);
							$opt['v'] = '';
						}
						if (!array_key_exists('c', $opt)) {
							trigger_error('Checked state for option ' . $id . '::' . $opt['i'] .
								' was not found - defaulting to <u>UNCHECKED</u>', E_USER_WARNING);
							$opt['c'] = 0;
						}
						if (!array_key_exists('d', $opt)) {
							trigger_error('Disabled state for option ' . $id . '::' . $opt['i'] .
								' was not found - defaulting to <u>ENABLED</u>', E_USER_WARNING);
							$opt['d'] = 0;
						}
						if ($mode == 'check') {
							$content .= $this->_addCheckBox($name, $opt);
						} elseif ($mode == 'radio') {
							$content .= $this->_addRadioBox($name, $opt);
						}
					}
				} else {
					trigger_error('No options passed into ' . $id, E_USER_WARNING);
				}

				$accordion->addPage(str_replace(' ', '', $title), ucfirst($title), $content);
			}
		}
		$accordion->create();

		$field .= $this->_openOptionGrid();
		$field .= $accordion->getAccordion();
		$field .= $this->_closeOptionGrid();

		$field = $this->_line($field);
		$this->fields[$name] = $field;
		return $this;
	}
	/**
	 * Form::addConfirmCheck()
	 * 
	 * @param mixed $title
	 * @param mixed $name
	 * @param mixed $text
	 * @param mixed $help
	 * @param mixed $options
	 * @return
	 */
	function addConfirmCheck($title, $name, $text, $help = array('t' => ''), $options =
		array())
	{
		if (!array_key_exists('v', $options))
			$options['v'] = false;
		if (!array_key_exists('r', $options))
			$options['r'] = false;
		if (!array_key_exists('d', $options))
			$options['d'] = false;
		if (!array_key_exists('ro', $options))
			$options['ro'] = false;
		$id = $this->formID . '::' . $name;

		$field = $this->_label($title, $name, $help, $options);

		$field .= $this->_openOptionGrid();
		$field .= $this->_addCheckBox($name, array(
			'c' => false,
			'd' => $options['d'],
			'v' => $options['v'],
			'i' => $id,
			'l' => $text));
		$field .= $this->_closeOptionGrid();

		$field = $this->_line($field);
		$this->fields[$name] = $field;
		return $this;
	}

	/**
	 * Form::addOptionGrid()
	 * 
	 * @param mixed $mode
	 * @param mixed $title
	 * @param mixed $name
	 * @param mixed $option
	 * @param mixed $help
	 * @param mixed $options
	 * @return
	 */
	function addOptionGrid($mode, $title, $name, $option = array(), $help = array('t' =>
			''), $options = array())
	{
		if (!array_key_exists('v', $options))
			$options['v'] = false;
		if (!array_key_exists('r', $options))
			$options['r'] = false;
		if (!array_key_exists('d', $options))
			$options['d'] = false;
		if (!array_key_exists('ro', $options))
			$options['ro'] = false;
		$id = $this->formID . '::' . $name;

		$field = $this->_label($title, $name, $help, $options);

		$field .= $this->_openOptionGrid();

		if (count($option) != 0) {
			foreach($option as $opt) {
				if (!array_key_exists('i', $opt)) {
					$opt['i'] = 'UNKNOWN' . time();
					trigger_error('ID for option ' . $id . '::' . $opt['i'] . ' was not found',
						E_USER_WARNING);
				}
				if (!array_key_exists('l', $opt)) {
					trigger_error('Label for option ' . $id . '::' . $opt['i'] . ' was not found',
						E_USER_WARNING);
					$opt['l'] = 'UNKOWN OPTION';
				}
				if (!array_key_exists('v', $opt)) {
					trigger_error('Value for option ' . $id . '::' . $opt['i'] . ' was not found',
						E_USER_WARNING);
					$opt['v'] = '';
				}
				if (!array_key_exists('c', $opt)) {
					trigger_error('Checked state for option ' . $id . '::' . $opt['i'] .
						' was not found - defaulting to <u>UNCHECKED</u>', E_USER_WARNING);
					$opt['c'] = 0;
				}
				if (!array_key_exists('d', $opt)) {
					trigger_error('Disabled state for option ' . $id . '::' . $opt['i'] .
						' was not found - defaulting to <u>ENABLED</u>', E_USER_WARNING);
					$opt['d'] = 0;
				}
				if ($mode == 'check') {
					$field .= $this->_addCheckBox($name, $opt);
				} elseif ($mode == 'radio') {
					$field .= $this->_addRadioBox($name, $opt);
				}
			}
		} else {
			trigger_error('No options passed into ' . $id, E_USER_WARNING);
		}
		$field .= $this->_closeOptionGrid();

		$field = $this->_line($field);
		$this->fields[$name] = $field;
		return $this;
	}

	// HTML Generation
	/**
	 * Form::_line()
	 * 
	 * @param mixed $line
	 * @param string $id
	 * @return
	 */
	private function _line($line, $id = '')
	{
		$field = $this->_indent . '  <div class="form-group"';
		if ($id != '')
			$field .= ' id="' . $id . '"';
		$field .= '>' . PHP_EOL;
		$field .= $line;
		$field .= $this->_indent . '  </div>' . PHP_EOL;
		return $field;
	}
	/**
	 * Form::_label()
	 * 
	 * @param mixed $title
	 * @param mixed $name
	 * @param mixed $help
	 * @param mixed $options
	 * @return
	 */
	private function _label($title, $name, $help, $options)
	{
		if($this->_label_col == 0){
			return '';
		}
		$label = $this->_indent . '    <div class="col-sm-' . $this->_label_col . '">' .
			PHP_EOL;
		$label .= $this->_indent . '      <label for="' . $name . '">' . $title;
		if(!array_key_exists('t', $help)){
			$help['t'] = '';
		}
		if (array_key_exists('r', $options) && $options['r']) {
			$label .= ' *';
			$help['t'] .= ' (Required)';
		}
		if (array_key_exists('ro', $options) && $options['ro']) {
			$help['t'] .= ' (Read Only)';
		}
		if (array_key_exists('t', $help) && $help['t'] != '') {
			$label .= '&nbsp;&nbsp;&nbsp;<a class="glyph-tooltip" href="#" data-toggle="tooltip" data-trigger="click hover focus" data-placement="right" ';
			$label .= 'title="' . $help['t'] .
				'"><span class="glyphicon glyphicon-question-sign text-info"></span></a>';
		}
		$label .= '</label>' . PHP_EOL;
		$label .= $this->_indent . '    </div>' . PHP_EOL;
		return $label;
	}

	/**
	 * Form::_addCtrlBtn()
	 * 
	 * @param mixed $width
	 * @param mixed $size
	 * @param mixed $style
	 * @param mixed $action
	 * @param mixed $label
	 * @param mixed $icon
	 * @return
	 */
	private function _addCtrlBtn($width, $size, $style, $action = array(), $label, $icon)
	{
		$button = $this->_indent . '        <div class="col-sm-' . $width . '">' .
			PHP_EOL;
		if ($action['t'] == 'url') {
			$button .= $this->_indent . '          <a class="btn btn-' . $size . ' btn-' . $style .
				' btn-block"';
			$button .= ' href="' . $action['a'] . '"';
		} elseif ($action['t'] == 'submit') {
			$button .= $this->_indent . '          <input type="submit" class="btn btn-' . $size . ' btn-' . $style . ' btn-block"';
		} else {
			$button .= $this->_indent . '          <button type="' . $action['a'] .
				'" class="btn btn-' . $size . ' btn-' . $style . ' btn-block"';
		}
		$button .= ' tabindex="' . $this->tabIndex . '"';
		if ($action['oc'] != '')
			$button .= ' onclick="' . $action['oc'] . '"';

		$this->tabIndex++;
		if ($action['t'] != 'submit') {
		$button .= '>' . $label;
		if ($icon != '') {
			$button .= '&nbsp;&nbsp;&nbsp;<span class="' . B_ICON . ' ' . B_ICON . '-' . $icon .
				'"></span>';
		}
		} else {
			$button .= ' value="' . $label . '"';
		}
		if ($action['t'] == 'url') {
			$button .= '</a>' . PHP_EOL;
		} elseif($action['t'] == 'submit'){
			$button .= ' />' . PHP_EOL;
		} else {
			$button .= '</button>' . PHP_EOL;
		}
		$button .= PHP_EOL . $this->_indent . '        </div>' . PHP_EOL;

		return $button;
	}

	/**
	 * Form::_input()
	 * 
	 * @param mixed $type
	 * @param mixed $name
	 * @param mixed $id
	 * @param mixed $value
	 * @param mixed $options
	 * @param string $placeholder
	 * @param string $width
	 * @return
	 */
	private function _input($type, $name, $id, $value, $options, $placeholder = '',
		$width = '', $spry=false, $pwd_id=null)
	{
		if ($width == '')
			$width = $this->_field_col;
		if (!in_array('form-control', $options['classes']))
			$options['classes'][] = 'form-control';
		
		$field = $this->_indent . '      <input type="' . $type . '"';
		if (count($options['classes'] != 0))
			$field .= ' class="' . implode(' ', $options['classes']) . '"';
		$field .= ' name="' . $name . '" id="' . $id . '"';
			'"';
		if ($placeholder != '')
			$field .= ' placeholder="' . $placeholder .'"';
		if ((string) $value != '')
			$field .= ' value="' . $value . '"';
		if ($options['r'])
			$field .= ' required="required"';
		if ($options['d'])
			$field .= ' disabled="disabled"';
		if ($options['ro'])
			$field .= ' readonly="readonly"';
		if (array_key_exists('a', $options)) {
			if ($options['a'] !== '')
				$field .= ' accept="' . $options['a'] . '"';
		}
		$field .= ' tabindex="' . $this->tabIndex . '" />' . PHP_EOL;
		$this->tabIndex++;
		if($spry && $type != 'password' && $pwd_id==null){
			$field = $this->_spry($id, $field, $options['vt'], $name, $options);
		}elseif($type=='password' && $pwd_id !==null){
			$field .= $this->_indent . '        <span class="help-block"><span id="pwstrength_viewport_errors-' . $pwd_id . '"></span><span id="pwstrength_viewport_verdict-' . $pwd_id . '"></span></span>' . PHP_EOL;
			$field .= $this->_indent . '        <span id="pwstrength_viewport_progress-' . $pwd_id . '"></span>' . PHP_EOL;
		}
		$field = $this->_indent . '    <div class="col-sm-' . $width . '">' . PHP_EOL. $field;
		$field .= $this->_indent . '    </div>' . PHP_EOL;
		return $field;
	}

	/**
	 * Form::_textarea()
	 * 
	 * @param mixed $name
	 * @param mixed $id
	 * @param mixed $value
	 * @param mixed $rows
	 * @param string $placeholder
	 * @param bool $disabled
	 * @param bool $readonly
	 * @return
	 */
	private function _textarea($name, $id, $value, $rows, $placeholder = '', $disabled = false,
		$readonly = false)
	{
		$field = $this->_indent . '    <div class="col-sm-' . $this->_field_col . '">' .
			PHP_EOL;
		$field .= $this->_indent . '      <textarea class="form-control" name="' . $name .
			'" id="' . $id . '" placeholder="' . $placeholder . '"';
		$field .= 'rows="' . $rows . '" ';
		if ($disabled)
			$field .= 'disabled="disabled" ';
		if ($readonly)
			$field .= 'readonly="readonly" ';
		$field .= 'tabindex="' . $this->tabIndex . '" >';
		$this->tabIndex++;
		$field .= $value;
		$field .= '</textarea>' . PHP_EOL;
		$field .= $this->_indent . '    </div>' . PHP_EOL;
		return $field;
	}

	/**
	 * Form::_openBtnGroup()
	 * 
	 * @return
	 */
	private function _openBtnGroup()
	{
		$field = $this->_indent . '    <div class="col-sm-' . $this->_field_col . '">' .
			PHP_EOL;
		$field .= $this->_indent . '      <div class="btn-group" data-toggle="buttons">' .
			PHP_EOL;
		return $field;
	}
	/**
	 * Form::_closeBtnGroup()
	 * 
	 * @return
	 */
	private function _closeBtnGroup()
	{
		$field = $this->_indent . '      </div>' . PHP_EOL;
		$field .= $this->_indent . '    </div>' . PHP_EOL;
		return $field;
	}
	/**
	 * Form::_addBtn()
	 * 
	 * @param mixed $style
	 * @param mixed $name
	 * @param mixed $id
	 * @param mixed $label
	 * @param mixed $value
	 * @param mixed $checked
	 * @param mixed $disabled
	 * @return
	 */
	private function _addBtn($style, $name, $id, $label, $value, $checked, $disabled)
	{
		$btn = $this->_indent . '        <label class="btn btn-';
		if ($checked) {
			$btn .= $style . ' active';
		} else {
			$btn .= 'default';
		}
		$btn .= '" id="btn-' . $id . '"';
		if ($disabled == true)
			$btn .= ' disabled="disabled"';
		$btn .= '>' . PHP_EOL;
		$btn .= $this->_indent . '          <input type="radio" name="' . $name .
			'" id="' . $id . '" value="' . $value . '"';
		if ($checked == true)
			$btn .= ' checked="checked"';
		if ($disabled == true)
			$btn .= ' disabled="disabled"';
		$btn .= ' tabindex="' . $this->tabIndex . '" /> ' . $label . PHP_EOL;
		$this->tabIndex++;
		$btn .= $this->_indent . '        </label>' . PHP_EOL;
		return $btn;
	}

	/**
	 * Form::_openSelect()
	 * 
	 * @param mixed $name
	 * @param mixed $id
	 * @return
	 */
	private function _openSelect($name, $id, $multiple)
	{
		if($multiple){
			$multiple = 'multiple ';
		}else{
			$multiple = '';
		}
		$select = $this->_indent . '    <div class="col-sm-' . $this->_field_col . '">' .
			PHP_EOL;
		$select .= $this->_indent . '      <select class="form-control" name="' . $name .
			'" id="' . $id . '" '.$multiple.'tabindex="' . $this->tabIndex . '">' . PHP_EOL;
		$this->tabIndex++;
		return $select;
	}
	/**
	 * Form::_closeSelect()
	 * 
	 * @return
	 */
	private function _closeSelect()
	{
		$select = '';
		$select .= $this->_indent . '      </select>' . PHP_EOL;
		$select .= $this->_indent . '    </div>' . PHP_EOL;
		return $select;
	}
	/**
	 * Form::_addSelectOption()
	 * 
	 * @param mixed $opt
	 * @return
	 */
	private function _addSelectOption($opt)
	{
		$option = $this->_indent . '        <option';
		$option .= ' value="' . $opt['v'] . '"';
		if ($opt['s'] == true)
			$option .= ' selected="selected"';
		if ($opt['d'] == true)
			$option .= ' disabled="disabled"';
		$option .= '>' . $opt['n'] . '</option>' . PHP_EOL;
		return $option;
	}

	/**
	 * Form::_openOptionGrid()
	 * 
	 * @return
	 */
	private function _openOptionGrid()
	{
		$grid = $this->_indent . '    <div class="col-sm-' . $this->_field_col . '">' .
			PHP_EOL;
		return $grid;
	}
	/**
	 * Form::_closeOptionGrid()
	 * 
	 * @return
	 */
	private function _closeOptionGrid()
	{
		$grid = $this->_indent . '    </div>' . PHP_EOL;
		return $grid;
	}
	/**
	 * Form::_addOptionBox()
	 * 
	 * @param mixed $mode
	 * @param mixed $name
	 * @param mixed $box
	 * @return
	 */
	private function _addOptionBox($mode, $name, $box)
	{
		$option = $this->_indent . '      <div class="checkbox">' . PHP_EOL;
		$option .= $this->_indent . '        <label>' . PHP_EOL;
		$option .= $this->_indent . '          <input type="' . $mode . '"';
		$option .= ' name="' . $name . '"';
		$option .= ' value="' . $box['v'] . '"';
		$option .= ' id="' . $box['i'] . '"';
		if ($box['c'] == true)
			$option .= ' checked="checked"';
		if ($box['d'] == true)
			$option .= ' disabled="disabled"';
		$option .= ' tabindex="' . $this->tabIndex . '"';
		$this->tabIndex++;
		$option .= '>' . PHP_EOL;
		$option .= $this->_indent . '          ' . $box['l'] . PHP_EOL;
		$option .= $this->_indent . '        </label>' . PHP_EOL;
		$option .= $this->_indent . '      </div>' . PHP_EOL;
		return $option;
	}
	/**
	 * Form::_addCheckBox()
	 * 
	 * @param mixed $name
	 * @param mixed $box
	 * @return
	 */
	private function _addCheckBox($name, $box)
	{
		return $this->_addOptionBox('checkbox', $name, $box);
	}
	/**
	 * Form::_addRadioBox()
	 * 
	 * @param mixed $name
	 * @param mixed $box
	 * @return
	 */
	private function _addRadioBox($name, $box)
	{
		return $this->_addOptionBox('radio', $name, $box);
	}

	/**
	 * Form::_spry()
	 * 
	 * @param mixed $id
	 * @param mixed $field
	 * @param mixed $type
	 * @param mixed $name
	 * @param mixed $options
	 * @return
	 */
	private function _spry($id, $field, $type, $name, $options)
	{
		if (!array_key_exists('c', $options))
			$options['c'] = false;
		$this->_addSpryScript($id, $type, $name, $options);
		$line = $this->_indent . '    <span id="s_' . $name .
			'" class="validation-container">' . PHP_EOL;
		foreach(preg_split('/\r|\r\n|\n/', $field) as $comp) {
			if (trim($comp) != '')
				$line .= '  ' . $comp . PHP_EOL;
		}
		$line .= $this->_spryValidateMsg($id, $options);
		$line .= $this->_indent . '    </span>' . PHP_EOL;
		return $line;

	}
	/**
	 * Form::_spryValidateMsg()
	 * 
	 * @param mixed $id
	 * @param mixed $options
	 * @return
	 */
	private function _spryValidateMsg($id, $options)
	{
		$line = $this->_indent . '      <span class="help-block"><strong>' . PHP_EOL;
		if ($options['c']) {
			$line .= $this->_indent .
				'        <span class="text-info">Characters Remaining: <span id="count_' . $id .
				'"></span></span><br />' . PHP_EOL;
		}
		if (count($options['vm']) != 0) {
			foreach($options['vm'] as $c => $o) {
				$line .= $this->_indent . '        <span class="' . $c . ' text-' . $o['s'] .
					'">' . $o['m'] . '</span>' . PHP_EOL;
			}
		}
		$line .= $this->_indent . '      </strong></span>' . PHP_EOL;
		return $line;
	}

	// Javascript Generation

	/**
	 * Form::addScript()
	 * 
	 * @param mixed $script
	 * @return
	 */
	function addScript($script)
	{
		$line = '';
		foreach(preg_split('/\r|\r\n|\n/', $script) as $comp) {
			if (trim($comp) != '')
				$line .= $this->_indent . $comp . PHP_EOL;
		}
		$this->script .= $line;
		return $this;
	}

	/**
	 * Form::_addBtnScript()
	 * 
	 * @param mixed $btn
	 * @return
	 */
	private function _addBtnScript($btn)
	{
		$script = '';
		foreach($btn as $button) {
			$script .= $this->_indent . '$("#btn-' . $button['i'] . '").click(function(){' .
				PHP_EOL;
			foreach($btn as $chgBtn) {
				if ($button == $chgBtn) {
					$script .= $this->_indent . '  $("#btn-' . $chgBtn['i'] . '").addClass("btn-' .
						$chgBtn['s'] . '").removeClass("btn-default");' . PHP_EOL;
				} else {
					$script .= $this->_indent . '  $("#btn-' . $chgBtn['i'] .
						'").addClass("btn-default").removeClass("btn-' . $chgBtn['s'] . '");' . PHP_EOL;
				}
			}
			$script .= $this->_indent . '});' . PHP_EOL;
		}
		$this->script .= $script;
	}

	/**
	 * Form::_addSpryScript()
	 * 
	 * @param mixed $id
	 * @param mixed $type
	 * @param mixed $name
	 * @param mixed $options
	 * @return
	 */
	private function _addSpryScript($id, $type, $name, $options)
	{
		if ($options['r'] == false) {
			$options['vo'] = 'isRequired:false,' . $options['vo'];
		}
		if ($options['c'] == true) {
			$options['vo'] = 'counterId: "count_' . $id .
				'", counterType: "chars_remaining",' . $options['vo'];
		}
		switch($type) {
			case 'text':
			case 'email':
			case 'date':
			case 'real':
			case 'integer':
			case 'time':
			case 'url':
			case 'ip':
				$this->_spryCSS['txtfield'] = 'core/spry/SpryValidationTextField.css';
				$this->_spryJS['txtfield'] = 'core/spry/SpryValidationTextField.js';
				$script = $this->_indent . 'var ' . $name .
					' = new Spry.Widget.ValidationTextField(';
				if ($options['vt'] == 'text')
					$options['vt'] = 'none';
				$script .= '"s_' . $name . '", "' . $options['vt'] . '", {' . $options['vo'] .
					'}';
				$script .= ');' . PHP_EOL;
				break;
			case 'select':
				$this->_spryCSS['select'] = 'core/spry/SpryValidationSelect.css';
				$this->_spryJS['select'] = 'core/spry/SpryValidationSelect.js';
				$script = $this->_indent . 'var ' . $name .
					' = new Spry.Widget.ValidationSelect(';
				$script .= '"s_' . $name . '", {' . $options['vo'] . '}';
				$script .= ');' . PHP_EOL;
				break;
			case 'textarea':
				$this->_spryCSS['txtarea'] = 'core/spry/SpryValidationTextarea.css';
				$this->_spryJS['txtarea'] = 'core/spry/SpryValidationTextarea.js';
				$script = $this->_indent . 'var ' . $name .
					' = new Spry.Widget.ValidationTextarea(';
				$script .= '"s_' . $name . '", {' . $options['vo'] . '}';
				$script .= ');' . PHP_EOL;
				break;
			case 'password':
				$this->_spryCSS['pwd'] = 'core/spry/SpryValidationPassword.css';
				$this->_spryJS['pwd'] = 'core/spry/SpryValidationPassword.js';
				$script = $this->_indent . 'var ' . $name .
					' = new Spry.Widget.ValidationPassword(';
				$script .= '"s_' . $name . '", {' . $options['vo'] . '}';
				$script .= ');' . PHP_EOL;
				break;
			case 'confirm':
				$this->_spryCSS['conf'] = 'core/spry/SpryValidationConfirm.css';
				$this->_spryJS['conf'] = 'core/spry/SpryValidationConfirm.js';
				$script = $this->_indent . 'var ' . $name .
					' = new Spry.Widget.ValidationConfirm(';
				$script .= '"s_' . $name . '", "' . $options['vc'] . '", {' . $options['vo'] .
					'}';
				$script .= ');' . PHP_EOL;
				break;
			default:
				$script = '';
		}
		$this->script .= $script;
	}

	/**
	 * Form::toggleLink()
	 * 
	 * @param mixed $parent
	 * @param mixed $value
	 * @param string $indent
	 * @param string $group
	 * @param mixed $options
	 * @return
	 */
	static function toggleLink($parent, $value, $indent = '', $group = '', $options =
		array())
	{

		if (!array_key_exists('s', $options))
			$options['s'] = array();
		if (!array_key_exists('f', $options))
			$options['f'] = array();

		if (!array_key_exists('h', $options['s']))
			$options['s']['h'] = '';
		if (!array_key_exists('h', $options['f']))
			$options['f']['h'] = '';
		if (!array_key_exists('u', $options['s']))
			$options['s']['u'] = '';
		if (!array_key_exists('u', $options['f']))
			$options['f']['u'] = '';
		if (!array_key_exists('c', $options['s']))
			$options['s']['c'] = '';
		if (!array_key_exists('c', $options['f']))
			$options['f']['c'] = '';
		if (!array_key_exists('i', $options['s']))
			$options['s']['i'] = 'ok-sign';
		if (!array_key_exists('i', $options['f']))
			$options['f']['i'] = 'remove-sign';


		$link = '';
		if ($value == true) {
			$link .= '<a class="glyph-tooltip text-success';
			if (($group != '' && $parent->inGroup($group)) && $options['s']['u'] != '') {
				$link .= '"';
				$link .= ' data-toggle="tooltip" data-trigger="click hover focus" data-placement="bottom"';
				$link .= ' title="' . $options['s']['h'] . '"';
				$link .= ' href="' . $options['s']['u'] . '"';
				if ($options['s']['c'] != '')
					$link .= ' onclick="' . $options['s']['c'] . '"';

			} else {
				$link .= ' disabled"';
			}
			$link .= '>';
			$link .= '<span class="' . B_ICON . ' ' . B_ICON . '-' . $options['s']['i'] .
				'"></span>';
			$link .= '</a>';
		} else {
			$link .= '<a class="glyph-tooltip text-danger';
			if (($group != '' && $parent->inGroup($group)) && $options['f']['u'] != '') {
				$link .= '"';
				$link .= ' data-toggle="tooltip" data-trigger="click hover focus" data-placement="bottom"';
				$link .= ' title="' . $options['f']['h'] . '" ';
				$link .= ' href="' . $options['f']['u'] . '"';
				if ($options['f']['c'] != '')
					$link .= ' onclick="' . $options['f']['c'] . '"';

			} else {
				$link .= ' disabled"';
			}
			$link .= '>';
			$link .= '<span class="' . B_ICON . ' ' . B_ICON . '-' . $options['f']['i'] .
				'"></span>';
			$link .= '</a>';
		}
		return $link;
	}
}
?>
