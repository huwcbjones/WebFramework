<?php
/**
 * Accordion HTML Class
 *
 * Creates HTML accordions
 *
 * @category   Plugins.Bootstrap.Accordion
 * @package    accordion.php
 * @site       www.biggleswadesc.org
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 */
class Accordion extends BasePlugin
{
	const		name_space		= 'Plugins.Boostrap.Accordion';
	const		version			= '1.0.0';
	
	private $_pages = array();
	private $_id = "accordion0";
	private $_accordion;
	private $_open = 0;
	
	function addPage($id,$header = "",$content=""){
		$this->parent->parent->debug($this::name_space.': Adding page "'.$id.'"');
		$this->_pages[$id]['header'] = $header;
		$this->_pages[$id]['content'] = $content;
	}
	function changeHeader($id,$header=""){
		$this->parent->parent->debug($this::name_space.': Changing header of "'.$id.'"');
		$this->_pages[$id]['header'] = $header;
	}
	function changeContent($id,$content=""){
		$this->parent->parent->debug($this::name_space.': Changing content of "'.$id.'"');
		$this->_pages[$id]['content'] = $content;
	}
	function setID($id){
		$this->parent->parent->debug($this::name_space.': Set ID to "'.$id.'"');
		$this->_id = $id;
	}
	function setOpen($index){
		$this->parent->parent->debug($this::name_space.': Set open panel to "'.$index.'"');
		$this->_open = $index;
	}
	function getID(){
		return $this->id;
	}
	function create(){
		$this->parent->parent->debug($this::name_space.': Generating accordion...');
		$accordion = '<div class="panel-group" id="'.$this->_id.'">'.PHP_EOL;
		$index = 0;
		foreach($this->_pages as $id=>$page){
			$this->parent->parent->debug($this::name_space.': Generating panel '.$index.' ('.$id.')');
			$accordion.='  <div class="panel panel-primary">'.PHP_EOL;
			$accordion.='    <div class="panel-heading">'.PHP_EOL;
			$accordion.='      <h3 class="panel-title">'.PHP_EOL;
			$accordion.='        <a class="accordion-toggle" data-toggle="collapse" data-parent="#'.$this->_id.'" href="#'.$this->_id.'_'.$id.'">'.PHP_EOL;
			$accordion.='          '.$page['header'].''.PHP_EOL;
			$accordion.='        </a>'.PHP_EOL;
			$accordion.='      </h3>'.PHP_EOL;
			$accordion.='    </div>'.PHP_EOL;
			$accordion.='    <div id="'.$this->_id.'_'.$id.'" class="panel-collapse collapse';
			if($index==$this->_open){$accordion.=' in';}
			$accordion.='">'.PHP_EOL;
			$accordion.='      <div class="panel-body">'.PHP_EOL;
			$accordion.='        '.$page['content'].''.PHP_EOL;
			$accordion.='      </div>'.PHP_EOL;
			$accordion.='    </div>'.PHP_EOL;
			$accordion.='  </div>'.PHP_EOL;
			$index++;
		}
		$accordion.='</div>'.PHP_EOL;
		$this->accordion = $accordion;
	}
	
	function getAccordion(){
		return $this->accordion;
	}
}
?>