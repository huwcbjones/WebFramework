<?php
/**
 * Carousel HTML Class
 *
 * Generates HTML Carousels
 *
 * @category   Plugins.Bootstrap.Carousel
 * @package    carousel.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */

class Carousel{
	
	private $images = array();
	private $carouselid = "carousel0";
	private $height = "320px";
	private $align = "center";
	private $shuffle = true;
	private $_indent = '';
	
	function setIndent($indent){
		$this->_indent = $indent;
	}
	
	function addImage($image){
		$this->images[intval($image["ID"])] = $image;
		$this->images = array_values($this->images);
	}
	
	function addXnewimages($mySQL,$noOfImages){
		$result = $mySQL->query("SELECT * FROM `images` WHERE `carousel`=1 ORDER BY `ID` DESC LIMIT ".$noOfImages);
		if($result->num_rows==0){
			$this->images[0] = array("location"=>"","caption"=>"No Image");
		}else{
			while($row = $result->fetch_assoc()){
				$this->images[$row["ID"]] = $row;
			}
		}
		$this->images = array_values($this->images);
		
	}
	
	function setHeight($height){
		$this->height = $height;
	}
	function setCaptionAlign($align){
		$this->align = $align;
	}
	function removeImage($ID){
		unset($this->images[$ID]);
	}
	
	function setID($id){
		$this->carouselid = $id;
	}
	
	function setShuffle($shuffle){
		$this->shuffle = $shuffle;
	}
	
	function getID(){
		return $this->carouselid;
	}
	
	function getHeight(){
		return $this->height;
	}
	function create(){
		if($this->shuffle===true){
			shuffle($this->images);
		}
		$carousel = $this->_indent.'<div id="'.$this->carouselid.'" class="carousel slide">'.PHP_EOL;
        $carousel.= $this->_indent.'  <ol class="carousel-indicators">'.PHP_EOL;
		for($i=0;$i<count($this->images);$i++){
			$carousel.= $this->_indent.'    <li data-target="#'.$this->carouselid.'" data-slide-to="'.$i.'"';
			if ($i==0) $carousel.= ' class="active"';
			$carousel.= '></li>'.PHP_EOL;
		}
        $carousel.= $this->_indent.'  </ol>'.PHP_EOL;
		$carousel.= $this->_indent.'  <div class="carousel-inner">'.PHP_EOL;
		for($i=0;$i<count($this->images);$i++){
			$carousel.= $this->_indent.'    <div class="item';
			if($i==0){$carousel.=' active';}
			$carousel.= '" style="max-height:'.$this->height.'">'.PHP_EOL;
			if(!isset($this->images[$i]['alt'])){$this->images[$i]['alt']=$this->images[$i]['caption'];}
            $carousel.= $this->_indent.'      <img src="/image?i='.$this->images[$i]['location'].'" alt="'.$this->images[$i]['alt'].'">'.PHP_EOL;
            $carousel.= $this->_indent.'      <div class="carousel-caption" style="text-align:'.$this->align.'">'.PHP_EOL;
            $carousel.= $this->_indent.'          <h4>'.$this->images[$i]['caption'].'</h4>'.PHP_EOL;
			if($this->images[$i]['browse']){$carousel .='          <p><a class="btn btn-large btn-primary" href="/gallery?g='.$this->images[$i]['gallery'].'">Browse gallery</a></p>'.PHP_EOL;}
            $carousel.= $this->_indent.'      </div>'.PHP_EOL.'    </div>'.PHP_EOL;
		}
		$carousel.= $this->_indent.'  </div>'.PHP_EOL;
        $carousel.= $this->_indent.'  <a class="left carousel-control" href="#'.$this->carouselid.'" data-slide="prev"><span class="'.B_ICON.' '.B_ICON.'-chevron-left"></span></a>'.PHP_EOL;
        $carousel.= $this->_indent.'  <a class="right carousel-control" href="#'.$this->carouselid.'" data-slide="next"><span class="'.B_ICON.' '.B_ICON.'-chevron-right"></span></a>'.PHP_EOL;
      	$carousel.= $this->_indent.'</div>'.PHP_EOL;
		return $carousel;
	}
}
?>