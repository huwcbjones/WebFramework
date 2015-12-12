<?php
/**
 * Event Classes
 *
 * @category   News.Event
 * @package    event.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */

/**
 * Event Manager Class
 *
 * @category   News.Event.Magaer
 */

/*
 */
class Event{
	
	protected $mySQL;
	
	function __construct($link){
		$this->mySQL = $link;
	}
	
	function event_add($title,$starts,$ends,$notes,$location,$allDay){
		global $user;
		if($user->accessPage(52)){
			$query = $this->mySQL['r']->prepare("SELECT `ID` from `news_events` WHERE `title` LIKE CONCAT('%',?,'%')");
			$query->bind_param('s',$title);
			$query->execute();
			$query->store_result();
			if($query->num_rows==0){
				$this->mySQL['r']->autocommit(false);
				$stmt = $this->mySQL['r']->prepare("INSERT INTO `news_events` (`title`,`starts`,`ends`,`notes`,`location`,`enable`,`allDay`) VALUES(?,FROM_UNIXTIME(?),FROM_UNIXTIME(?),?,?,0,?)");
				if($stmt!==false){
					$stmt->bind_param('siissi',$title,$starts,$ends,$notes,$location,$allDay);
					$stmt->execute();
					$stmt->store_result();
					if($stmt->affected_rows==1){
						$this->mySQL['r']->commit();
						$this->mySQL['r']->autocommit(true);return 0;
					}else{
						$this->mySQL['r']->rollback();
						$this->mySQL['r']->autocommit(true);return 1;
					}
				}else{$this->mySQL['r']->autocommit(true);return 2;}
			}else{return 3;}
		}else{return 4;}
	}
	
	function event_del($ID){
		global $user;
		if($user->accessPage(54)){
			$query = $this->mySQL['r']->prepare("SELECT `ID` from `news_events` WHERE `ID`=?");
			$query->bind_param('i',$ID);
			$query->execute();
			$query->store_result();
			if($query->num_rows!=0){
				$this->mySQL['r']->autocommit(false);
				$stmt = $this->mySQL['r']->prepare("DELETE FROM `news_events` WHERE `ID`=?");
				if($stmt!==false){
					$stmt->bind_param('i',$ID);
					$stmt->execute();
					$stmt->store_result();
					if($stmt->affected_rows==1){
						$this->mySQL['r']->commit();
						$this->mySQL['r']->autocommit(true);return 0;
					}else{
						$this->mySQL['r']->rollback();
						$this->mySQL['r']->autocommit(true);return 1;
					}
				}else{$this->mySQL['r']->autocommit(true);return 2;}
			}else{return 3;}
		}else{return 4;}
	}
	
	function event_edit($ID,$title,$enable,$starts,$ends,$notes,$location,$link,$allDay){
		global $user;
		if($user->accessPage(53)){
			$this->mySQL['r']->autocommit(false);
			$stmt = $this->mySQL['r']->prepare("UPDATE `news_events` SET `title`=?,`enable`=?,`starts`=FROM_UNIXTIME(?),`ends`=FROM_UNIXTIME(?),`notes`=?,`location`=?,`link`=?,`allDay`=? WHERE `ID`=?");
			if($stmt!==false){
				$stmt->bind_param('siiisssii',$title,$enable,$starts,$ends,$notes,$location,$link,$allDay,$ID);
				$stmt->execute();
				$stmt->store_result();
				if($stmt->affected_rows==1){
					$this->mySQL['r']->commit();
					$this->mySQL['r']->autocommit(true);return 0;
				}else{
					$this->mySQL['r']->rollback();
					$this->mySQL['r']->autocommit(true);return 1;
				}
			}else{
				$this->mySQL['r']->autocommit(true);return 2;
			}
		}else{return 3;}
	}
	
	function enable($ID,$mode){
		global $user;
		if($user->accessPage(53)){
			$stmt = $this->mySQL['r']->prepare("UPDATE `news_events` SET `enable`=? WHERE `ID`=?");
			if($stmt!==false){
				$stmt->bind_param('ii',$mode,$ID);
				$stmt->execute();
				$stmt->store_result();
				if($stmt->affected_rows==1){
					$this->mySQL['r']->commit();
					$this->mySQL['r']->autocommit(true);return 0;
				}else{
					$this->mySQL['r']->rollback();
					$this->mySQL['r']->autocommit(true);return 1;
				}
			}else{
				$this->mySQL['r']->autocommit(true);return 2;
			}
		}else{return 3;}
	}
}

/**
 * Event Item class
 *
 * @category   News.Event.Item
 */

/*
 * lib/modules/location.php
 */
class EventItem{
	
	protected $mySQL;
	private $ID;
	private $title;
	private $starts;
	private $ends;
	private $notes;
	private $location;
	var $data = false;
	private $event;
	private $closeBtn=false;
	private $allDay;
	
	function __construct($link){
		include_once($_SERVER['DOCUMENT_ROOT']."/lib/modules/location.php");
		$this->mySQL = $link;
	}
	
	function setID($ID){
		$this->ID = $ID;
		$this->data = false;
	}
	function setCloseBtn($bool){
		$this->closeBtn = $bool;
	}
	function getID($ID){
		return $this->ID;
	}
	function createData(){
		$eventdata = $this->mySQL['r']->prepare("SELECT `title`,`starts`,`ends`,`notes`,`link`,`location`,`allDay` from `news_events` WHERE `ID`=?");
		$eventdata->bind_param('i',$this->ID);
		$eventdata->execute();
		$eventdata->store_result();
		if($eventdata->num_rows!=0){
			$eventdata->bind_result($title,$starts,$ends,$notes,$link,$location,$allDay);
			while($eventdata->fetch()){
				$this->title = $title;
				$this->starts = $starts;
				$this->ends = $ends;
				$this->notes = $notes;
				$this->allDay = $allDay;
				$this->link = $link;
				if(substr($location,0,1)=='%'&&substr($location,-1,1)=='%'){
					$loca = new Location($this->mySQL);
					$loca->getLocation(substr($location,1,-1));
					$this->location = '<a href="/location?l='.$loca->ID.'">'.$loca->name.', '.$loca->address['city'].'</a>';
				}else{
					$this->location = $location;
				}
			}
			$this->data = true;
		}else{
			$this->data = false;
		}
	}
	function createEvent(){
		global $page;
		$event ='<div class="row pane">'.PHP_EOL;
		$event.='  <div class="col-xs-12">'.PHP_EOL;
		if($this->closeBtn){
			$event.= $page->closeBtn;
		}
		$event.='    <div class="row">'.PHP_EOL;
		$event.='      <div class="col-xs-2">'.PHP_EOL;
		$event.='        <h2>What?</h2>'.PHP_EOL;
		$event.='      </div>'.PHP_EOL;
		$event.='      <div class="col-xs-10">'.PHP_EOL;
		$event.='        <h2>'.$this->title.'</h2>'.PHP_EOL;
		$event.='      </div>'.PHP_EOL;
		$event.='    </div>'.PHP_EOL;
		$event.='    <div class="row">'.PHP_EOL;
		$event.='      <div class="col-xs-2">'.PHP_EOL;
		$event.='        <h2>When?</h2>'.PHP_EOL;
		$event.='      </div>'.PHP_EOL;
		$event.='      <div class="col-xs-10">'.PHP_EOL;
		if($this->allDay){
			if(date("d/M/Y",strtotime($this->starts))==date("d/M/Y",strtotime($this->ends))){
				$event.=' <h2>'.date("l j<\s\u\p>S</\s\u\p> F Y",strtotime($this->starts));
			}elseif(date("M/Y",strtotime($this->starts))==date("M/Y",strtotime($this->ends))){
				$event.='        <h2>'.date("l j<\s\u\p>S</\s\u\p>",strtotime($this->starts));
				$event.=' to '.date("l j<\s\u\p>S</\s\u\p> F Y",strtotime($this->ends));
			}elseif(date("Y",strtotime($this->starts))==date("Y",strtotime($this->ends))){
				$event.=' <h2>'.date("l j<\s\u\p>S</\s\u\p> F",strtotime($this->starts));
				$event.=' to '.date("l j<\s\u\p>S</\s\u\p> F Y",strtotime($this->ends));
			}else{
				$event.=' <h2>'.date("l j<\s\u\p>S</\s\u\p> F Y",strtotime($this->starts));
				$event.=' to '.date("l j<\s\u\p>S</\s\u\p> F Y",strtotime($this->ends));
			}
		}else{
			if(date("d/M/Y",strtotime($this->starts))==date("d/M/Y",strtotime($this->ends))){
				$event.='        <h2>'.date("l j<\s\u\p>S</\s\u\p> F Y, ",strtotime($this->starts));
				if(date("A",strtotime($this->starts))==date("A",strtotime($this->ends))){
					$event.=date("g:i",strtotime($this->starts)).' to '.date("g:i A",strtotime($this->ends));
				}else{
					$event.=date("g:i A",strtotime($this->starts)).' to '.date("g:i A",strtotime($this->ends));
				}
			}else{
				$event.='        <h2>'.date("l j<\s\u\p>S</\s\u\p> F Y, g:i A",strtotime($this->starts)).' to '.date("l j<\s\u\p>S</\s\u\p> F Y, g:i A",strtotime($this->starts));
			}
		}
		$event.='</h2>'.PHP_EOL;
		$event.='      </div>'.PHP_EOL;
		$event.='      <div class="col-xs-2">'.PHP_EOL;
		$event.='        <h2>Where?</h2>'.PHP_EOL;
		$event.='      </div>'.PHP_EOL;
		$event.='      <div class="col-xs-10">'.PHP_EOL;
		$event.='        <h2>'.$this->location.'</h2>'.PHP_EOL;
		$event.='      </div>'.PHP_EOL;
		$event.='      <div class="col-xs-2">'.PHP_EOL;
		$event.='        <h3>Details</h3>'.PHP_EOL;
		$event.='      </div>'.PHP_EOL;
		$event.='      <div class="col-xs-10">'.PHP_EOL;
		$event.='        <h3>'.$this->notes.'</h3>'.PHP_EOL;
		$event.='      </div>'.PHP_EOL;
		$event.='    </div>'.PHP_EOL;
		$event.='  </div>'.PHP_EOL;
		$event.='</div>'.PHP_EOL;
		$this->event = $event;
	}
	function getData(){
		if(!$this->data){
			$this->createData();
		}
		$event['title'] = $this->title;
		$event['starts'] = $this->starts;
		$event['ends'] = $this->ends;
		$event['notes'] = $this->notes;
		$event['link'] = $this->link;
		return $event;
	}
	function getTimestamp($time){
		if(!$this->data){
			$this->createData();
		}
		return strtotime($this->{$time});
	}
	function getTitle(){
		if(!$this->data){
			$this->createData();
		}
		return $this->title;
	}
	function getStart(){
		if(!$this->data){
			$this->createData();
		}
		return $this->starts;
	}
	function getEnd(){
		if(!$this->data){
			$this->createData();
		}
		return $this->ends;
	}
	function getNotes(){
		if(!$this->data){
			$this->createData();
		}
		return $this->notes;
	}
	function getUrl(){
		if(!$this->data){
			$this->createData();
		}
		if($this->link==""){
			return false;
		}else{
			return $this->link;
		}
	}
	
	function getEvent(){
		return $this->event;
	}
}