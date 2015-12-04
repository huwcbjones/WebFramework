<?php
/**
 * Modal HTML Generator Class...
 *
 * @category   Plugins.Bootstrap.Modal
 * @package    modal.php
 * @site       www.biggleswadesc.org
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */
class Modal extends BasePlugin {
	
	const name_space = 'Plugins.Bootstrap.Modal';
	const version = '1.0.0';
	
	protected $modal;
	protected $btn_left = array('id'=>'btn_left','type'=>'','mode'=>'','text'=>'','onclick'=>'','data'=>'');
	protected $btn_right = array('id'=>'btn_right','type'=>'','mode'=>'','text'=>'','onclick'=>'','data'=>'');
	protected $btn_centre = array('id'=>'btn_centre','type'=>'','mode'=>'','text'=>'','onclick'=>'','data'=>'');
	protected $title;
	protected $body;
	protected $id='Modal0';
	
	function __construct($parent){
		$this->parent = $parent;
		$this->parent->parent->debug('***** ' . $this::name_space . ' *****');
		$this->parent->parent->debug($this::name_space . ': Version ' . $this::version);
		
		$this->setLeft(B_T_DEFAULT,'Close','remove-sign');
		$this->setRight(B_T_PRIMARY,'OK','ok-sign');
	}

	function setID($ID){
		$this->parent->parent->debug($this::name_space.': Setting ID to "'.$ID.'"');
		$this->id = $ID;
		return $this;
	}
	function setLeft($mode,$text,$glyph="",$type='close',$onclick='',$data=array()){
		$this->parent->parent->debug($this::name_space.': Setting left button');
		$this->btn_left['id'] = strtolower(str_replace(' ','_',$text));
		$this->btn_left['mode'] = $mode;
		$this->btn_left['text'] = $text.'&nbsp;&nbsp;&nbsp;';
		if($glyph!=""){
			$this->btn_left['text'].='<span class="'.B_ICON.' '.B_ICON.'-'.$glyph.'"></span>';
		}
		if($onclick==''&&$type=='close'){
			$this->btn_left['type'] = 'button';
			$this->btn_left['onclick'] = ' data-dismiss="modal"';
		}else{
			$this->btn_left['type'] = $type;
			$this->btn_left['onclick'] = 'onclick="'.$onclick.'"';
		}
		foreach($data as $attrib=>$value){
			$this->btn_left['data'].= ' data-'.$attrib.'="'.$value.'"';
		}
		return $this;
	}
	function setCentre($mode,$text,$glyph="",$type='button',$onclick='',$data=array()){
		$this->parent->parent->debug($this::name_space.': Setting centre button');
		$this->btn_centre['id'] = strtolower(str_replace(' ','_',$text));
		$this->btn_centre['mode'] = $mode;
		$this->btn_centre['text'] = $text.'&nbsp;&nbsp;&nbsp;';
		if($glyph!=""){
			$this->btn_centre['text'].='<span class="'.B_ICON.' '.B_ICON.'-'.$glyph.'"></span>';
		}
		$this->btn_centre['type'] = $type;
		$this->btn_centre['onclick'] = 'onclick="'.$onclick.'"';
		foreach($data as $attrib=>$value){
			$this->btn_left['data'].= ' data-'.$attrib.'="'.$value.'"';
		}
		return $this;
	}
	function setRight($mode,$text,$glyph="",$type='button',$onclick='',$data=array()){
		$this->parent->parent->debug($this::name_space.': Setting right button');
		$this->btn_right['id'] = strtolower(str_replace(' ','_',$text));
		$this->btn_right['mode'] = $mode;
		$this->btn_right['text'] = $text.'&nbsp;&nbsp;&nbsp;';
		if($glyph!=""){
			$this->btn_right['text'].='<span class="'.B_ICON.' '.B_ICON.'-'.$glyph.'"></span>';
		}
		$this->btn_right['type'] = $type;
		$this->btn_right['onclick'] = ' onclick="'.$onclick.'"';
		foreach($data as $attrib=>$value){
			$this->btn_right['data'].= ' data-'.$attrib.'="'.$value.'"';
		}
		return $this;
	}
	
	function setBody($content){
		$this->parent->parent->debug($this::name_space.': Setting modal body');
		$this->body = $content;
		return $this;
	}
	
	function setTitle($title){
		$this->parent->parent->debug($this::name_space.': Modal title set as "'.$title.'"');
		$this->title = $title;
		return $this;
	}
	
	function create(){
		$this->parent->parent->debug($this::name_space.': Creating modal');
		$modal = '<div class="modal fade" id="'.$this->id.'" tabindex="-1" role="dialog" aria-hidden="true">'.PHP_EOL;
		$modal.= '    <div class="modal-dialog">'.PHP_EOL;
		$modal.= '      <div class="modal-content">'.PHP_EOL;
		$modal.= '        <div class="modal-header">'.PHP_EOL;
		$modal.= '          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'.PHP_EOL;
		$modal.= '          <h4 class="modal-title">'.$this->title.'</h4>'.PHP_EOL;
		$modal.= '        </div>'.PHP_EOL;
		$modal.= '        <div class="modal-body">'.PHP_EOL;
		$modal.= '          '.$this->body.PHP_EOL;
		$modal.= '        </div>'.PHP_EOL;
		$modal.= '        <div class="modal-footer">'.PHP_EOL;
		$modal.= '          <button type="'.$this->btn_left['type'].'" class="btn btn-'.$this->btn_left['mode'].'" id="'.$this->btn_left['id'].'"'.$this->btn_left['onclick'].'>'.$this->btn_left['text'].'</button>'.PHP_EOL;
		if($this->btn_centre['type']!=''){
			$modal.= '          <button type="'.$this->btn_centre['type'].'" class="btn btn-'.$this->btn_centre['mode'].'" id="'.$this->btn_centre['id'].'"'.$this->btn_centre['onclick'].'>'.$this->btn_centre['text'].'</button>'.PHP_EOL;
		}
		$modal.= '          <button type="'.$this->btn_right['type'].'" class="btn btn-'.$this->btn_right['mode'].'" id="'.$this->btn_right['id'].'"'.$this->btn_right['onclick'].$this->btn_right['data'].'>'.$this->btn_right['text'].'</button>'.PHP_EOL;
		$modal.= '        </div>'.PHP_EOL;
		$modal.= '      </div>'.PHP_EOL;
		$modal.= '    </div>'.PHP_EOL;
		$modal.= '  </div>'.PHP_EOL;
		$this->modal = $modal;
		return $this;
	}
	
	function getModal(){
		$this->create();
		return $this->modal;
	}
}
?>