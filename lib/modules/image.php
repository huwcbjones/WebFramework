<?php
/**
 * Image Classes
 *
 * @category   Core.Image
 * @package    image.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */

/**
 * Image Item Class
 *
 * @category   Core.Image.Item
 */

/*
 */
class Image{
	
	protected $location;
	protected $title;
	protected $ID;
	protected $caption;
	protected $browse;
	protected $carousel;
	protected $gallery;
	protected $mySQL;
	
	function __construct($link){
		$this->mySQL = $link;
	}
	function create($ID){
		$image_query = $this->mySQL['r']->prepare("SELECT `title`,`location`,`caption`,`browse`,`carousel`,`gallery` FROM `images` WHERE `ID`=?");
		$image_query->bind_param('i',$ID);
		$image_query->execute();
		$image_query->store_result();
		if($image_query->num_rows!=0){
			$image_query->bind_result($this->title,$this->location,$this->caption,$this->browse,$this->carousel,$this->gallery);
			$image_query->fetch();
			return true;
		}else{return false;}
	}
	
	function getTitle(){
		return $this->title;
	}
	function getLocation(){
		return $this->location;
	}
	function getCaption(){
		return $this->caption;
	}
	function getBrowse(){
		return $this->browse;
	}
	function getCarousel(){
		return $this->carousel;
	}
	function getGallery(){
		return $this->gallery;
	}
}
?>