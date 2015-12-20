<?php

class Doc{

	protected $mySQL;
	private $ID;
	private $docRow;
	private $docRowHeader;
	private $docRowTitle = "Title";
	private $title;
	private $type;
	private $size;
	private $hits;
	private $date;
	private $hitcounter = true;
	private $download;
	private $page;
	
	function __construct($link){
		$this->mySQL = $link;
		$this->createDocRowHeader();
	}
	
	function clear($yes=false){
		if($yes==true){
			$this->setID("");
			$this->setHitCounter(true);
			$this->page=$this->download=$this->date=$this->hits=$this->size=$this->type=$this->title="";
			return true;
		}else{
			return false;
		}
	}
	function setID($ID){
		if(strlen($ID)!=6){
			return false;
		}else{
			$this->ID = $ID;
		}
	}
	
	function setHitCounter($enable){
		if($enable===true){
			$this->hitcounter = true;
		}else{
			$this->hitcounter = false;
		}
	}
	
	function setDocRowTitle($title){
		$this->docRowTitle = $title;
	}
	
	function createDateTitle(){
		$this->title = date("F Y",strtotime($this->date));
	}
	
	function createVariables(){
		global $user;
		$doc_query = $this->mySQL['r']->prepare("SELECT `title`,`fullPath`,`path`,`page`,`uploaded`,`hits` from `core_files` WHERE `ID`=?");
		$doc_query->bind_param('s',$this->ID);
		$doc_query->execute();
		$doc_query->store_result();
		if($doc_query->num_rows!=0){
			$doc_query->bind_result($title,$fullPath,$path,$page,$uploaded,$hits);
			while($doc_query->fetch()){
				$this->title = $title;
				$this->fileExtension($fullPath);
				$this->fileSize($fullPath);
				$this->fileLinkCheck($path);
				$this->page = $user->pageAccess($page);
				$this->date = $uploaded;
				if($this->hitcounter){
					$this->hits = $hits;
				}
			}
			return true;
		}else{return false;}
	}
	function createDocRowHeader(){
		$row = "";
		$row .=("    <div class=\"row\">\n");
		$row .=("      <div class=\"col-xs-3\">\n");
		$row .=("        <p><b>".$this->docRowTitle."</b></p>\n");
		$row .=("      </div>\n");
		$row .=("      <div class=\"col-xs-4\">\n");
		$row .=("        <p><b>Type</b></p>\n");
		$row .=("      </div>\n");
		$row .=("      <div class=\"col-xs-2\">\n");
		$row .=("        <p><b>Size</b></p>\n");
		$row .=("      </div>\n");
		if($this->hitcounter==true){
			$row .=("      <div class=\"col-xs-1\">\n");
			$row .=("        <p><b>Hits</b></p>\n");
			$row .=("      </div>\n");
			$row .=("      <div class=\"col-xs-2\">\n");
		}else{
			$row .=("      <div class=\"col-xs-3\">\n");
		}
		$row .=("      </div>\n");
		$row .=("    </div>\n");
		$this->docRowHeader = $row;
	}
	function createDocRow($checkbox=false){
		$row = "";
		$row .=("    <div class=\"row\">\n");
		$row .=("      <div class=\"col-xs-3\">\n");
		$row .=("        <b>".$this->title."</b>\n");
		$row .=("      </div>\n");
		$row .=("      <div class=\"col-xs-4\">\n");
		$row .=("        ".$this->type."\n");
		$row .=("      </div>\n");
		$row .=("      <div class=\"col-xs-2\">\n");
		$row .=("        ".$this->size."\n");
		$row .=("      </div>\n");
		if($this->hitcounter==true){
			$row .=("      <div class=\"col-xs-1\">\n");
			$row .=("        ".$this->hits."\n");
			$row .=("      </div>\n");
			$row .=("      <div class=\"col-xs-2\">\n");
		}else{
			$row .=("      <div class=\"col-xs-3\">\n");
		}
		if($checkbox==true){
			$row .=('        <input type="checkbox" value="'.$this->ID.'" name="doc[]" />'.PHP_EOL);
		}else{
			$row .=("        ".$this->download."\n");
		}
		$row .=("      </div>\n");
		$row .=("    </div>\n");
		$this->docRow = $row;
	}
	
	function displayOnPage($pageID){
		if(intval($this->page[$pageID])==true){
			return true;
		}else{
			return false;
		}
	}
	private function fileSize($location){
		if(file_exists($location)){
			$this->size = $this->simplifyBytes(filesize($location));
		}else{
			$this->size = "--";
		}
	}
	
	private function fileExtension($location){
		include_once('../lib/fileTypes.php');
		$file = strtolower(pathinfo($location, PATHINFO_EXTENSION));
		if(isset($fileExt[$file])){
			$this->type = $fileExt[$file];
		}else{
			$this->type = '.'.$file;
		}
	}
	
	private function fileLinkCheck($location){
		if(file_exists($location)){
			$this->download = "<a href=\"/download.php?id=".$this->ID."\">Download</a>";
		}else{
			$this->download = "Unavailable";
		}
	}
	
	private function simplifyBytes($bytes){
		if ($bytes < 1024) {
			return number_format($bytes,1) .' B';
		} elseif ($bytes < 1048576) {
			return number_format($bytes / 1024, 1) .' KB';
		} elseif ($bytes < 1073741824) {
			return number_format($bytes / 1048576, 1) . ' MB';
		} elseif ($bytes < 1099511627776) {
			return number_format($bytes / 1073741824, 1) . ' GB';
		} elseif ($bytes < 1125899906842624) {
			return number_format($bytes / 1099511627776, 1) .' TB';
		} elseif ($bytes < 1152921504606846976) {
			return number_format($bytes / 1125899906842624, 1) .' PB';
		} elseif ($bytes < 1180591620717411303424) {
			return number_format($bytes / 1152921504606846976, 1) .' EB';
		} elseif ($bytes < 1208925819614629174706176) {
			return number_format($bytes / 1180591620717411303424, 1) .' ZB';
		} else {
			return number_format($bytes / 1208925819614629174706176, 1) .' YB';
		}
	}
	
	function getSize(){
		if($this->size!=""){
			return $this->size;
		}else{
			return false;
		}
	}
	function getTitle(){
		if($this->title!=""){
			return $this->title;
		}else{
			return false;
		}
	}
	function getType(){
		if($this->type!=""){
			return $this->type;
		}else{
			return false;
		}
	}
	function getDownload(){
		if($this->download!=""){
			return $this->download;
		}else{
			return false;
		}
	}
	function getDocRowHeader(){
		if($this->docRowHeader!=""){
			return $this->docRowHeader;
		}else{
			return false;
		}
	}
	function getDocRow(){
		if($this->docRow!=""){
			return $this->docRow;
		}else{
			return false;
		}
	}
}
?>