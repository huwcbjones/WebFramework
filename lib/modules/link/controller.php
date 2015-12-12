<?php
/**
 * Link Controller Class
 *
 * @category   Module.Link
 * @package    link/controller.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
 */
class linkController extends BasePageController
{
	const name_space	= 'Module.Link';
	const version		= '1.0.0';
	
	public function getHotlink($link){
		$link_query = $this->mySQL_r->prepare("SELECT `URI` FROM `core_links` WHERE `hotlink`=?");
		$link_query->bind_param('s', $link);
		$link_query->execute();
		$link_query->store_result();
		$link_query->bind_result($URI);
		if($link_query->num_rows==1){
			while($link_query->fetch()){
				return $URI;
			}
		}
		return false;
	}
	
	function getIDs(){
		return $this->ids;
	}
	function getAllLinks(){
		$link_query = $this->mySQL['r']->prepare("SELECT `ID`,`desc`,`title`,`URI` FROM `core_links` WHERE `short`=''");
		$link_query->execute();
		$link_query->store_result();
		if($link_query->num_rows!=0){
			$link_query->bind_result($ID,$desc,$title,$URI);
			while($link = $link_query->fetch()){
				$this->links[$ID] = array('ID'=>$ID,'desc'=>$desc,'title'=>$title,'URI'=>$URI);
				$this->ids[] = $ID;
			}
		}
	}
	function getLink($ID){
		return $this->links[$ID];
	}
}

?>