<?php
/**
 * Competiton Classes
 *
 * @category   Comp
 * @package    meet.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */

/**
 * Competition
 *
 * @category   Comp.Item
 */

/*
 * lib/modules/doc.php
 * lib/modules/location.php
 */
class Meet{
	
	protected $mySQL;
	protected $notesArray = array("e"=>"Entries","s"=>"Swimmers","p"=>"Parents","c"=>"Coaches");
	protected $doc;
	private $id;
	private $dispEvts = true;
	public $meet;
	private $numberSessions;
	private $numberEvents;
	private $date;
	private $date_s;
	private $date_f;
	private $date_wordy;
	private $title;
	public $resServ = array();
	private $resServLink;
	public $licence;
	private $notes = array("e"=>"","c"=>"","s"=>"","p"=>"");
	private $docs;
	private $link;
	private $shortMeet;
	private $longMeet;
	private $events = array();
	private $closeBtn = false;
	private $enable;
	private $location;
	public $wizard = 0;
	public $options = array();
	public $strokes = array();
	
	function __construct($link){
		$this->mySQL = $link;
		include_once($_SERVER['DOCUMENT_ROOT'] . "/lib/modules/docs.php");
		include_once($_SERVER['DOCUMENT_ROOT'] . "/lib/modules/location.php");
		$this->doc = new Doc($this->mySQL);
		$query = $this->mySQL['r']->prepare("SELECT `value` FROM `core_options` WHERE `name`=?");
		$opts = array('distances','strokes','rounds','gender','res-series','res-services');
		foreach($opts as $option){
			$opt = 'comp_'.$option;
			$query->bind_param('s',$opt);
			$query->execute();
			$query->bind_result($option_res);
			$query->fetch();
			$this->options[$option] = unserialize($option_res);
		}
		foreach($this->options['strokes'] as $type=>$value){
			$this->strokes = $this->strokes + $value;
		}
	}
	
	function setID($ID){
		$this->id = $ID;
	}
	function setCloseBtn($bool){
		$this->closeBtn = $bool;
	}
	function setDispEvts($enable = true){
		if($enable===true){
			$this->dispEvts = true;
		}else{
			$this->dispEvts = false;
		}
	}
	function clear($yes=false){
		if($yes===true){
			$this->dispEvts = true;
			$this->id="";
			$this->meet="";
			$this->numberSessions="";
			$this->numberEvents="";
			$this->date="";
			$this->title="";
			$this->resServ="";
			$this->resServLink="";
			$this->licence="";
			$this->docs="";
			$this->link="";
			$this->notes = array("e"=>"","c"=>"","s"=>"","p"=>"");
			$this->enable = "";
		}
	}
	function createMeet(){
		// Get data from Main Comp DB
		$meet_query = $this->mySQL['r']->prepare("SELECT
			`title`,
			`date_f`,
			`date_s`,
			`sessions`,
			`events`,
			`notes_e`,
			`notes_c`,
			`notes_s`,
			`notes_p`,
			`docs`,
			`licence`,
			`enable`,
			`location`,
			`wizStat`
		FROM `comp_meet` WHERE `ID`=?");
		$meet_query->bind_param('s',$this->id);
		$meet_query->execute();
		$meet_query->store_result();
		$res_query = $this->mySQL['r']->prepare("SELECT
			`enable`,
			`text`,
			`download`,
			`meet`,
			`series`,
			`services`,
			`nextSession`,
			`indSession`
		FROM `comp_res` WHERE `MID`=?");
		$res_query->bind_param('s',$this->id);
		$res_query->execute();
		$res_query->store_result();
		if($meet_query->num_rows!=0){
			$res_query->bind_result(
				$res['enable'],
				$res['text'],
				$res['download'],
				$res['meet'],
				$res['series'],
				$res['services'],
				$res['nextSession'],
				$res['indivSession']
			);
			$meet_query->bind_result(
				$title,
				$date_f,
				$date_s,
				$sessions,
				$events,
				$notes['e'],
				$notes['c'],
				$notes['s'],
				$notes['p'],
				$docs,
				$licence,
				$enable,
				$location,
				$wizStat
			);
			while($meet_query->fetch()){
				$this->title = $title;
				$this->date_s = $date_s;$this->date_f = $date_f;
				$this->enable = $enable;
				while($res_query->fetch()){
					$this->resServ = $res;
				}
				$this->licence = $licence;
				$this->resServ['services'] = unserialize($this->resServ['services']);
				if($date_f!=""&&$date_f!=$date_s){
					$this->date = date("l jS F Y",strtotime($date_s))." - ".date("l jS F Y",strtotime($date_f));
					if($date_s<=date('Y-m-d', strtotime('yesterday'))&&$date_f==date('Y-m-d', strtotime('today'))){
						$this->date_wordy = 'Today';
					}elseif($date_s<=date('Y-m-d', strtotime('yesterday'))&&$date_f==date('Y-m-d', strtotime('tomorrow'))){
						$this->date_wordy = 'Today until Tomorrow';
					}elseif($date_s==date('Y-m-d', strtotime('yesterday'))&&$date_f==date('Y-m-d', strtotime('tomorrow'))){
						$this->date_wordy = 'Today until Tomorrow';
					}elseif($date_s==date('Y-m-d', strtotime('today'))&&$date_f==date('Y-m-d', strtotime('tomorrow'))){
						$this->date_wordy = 'Today until Tomorrow';
					}elseif($date_s== date('Y-m-d', strtotime('today'))&&$date_f>date('Y-m-d', strtotime('tomorrow'))){
						$this->date_wordy = 'Today until '. date("l jS F Y",strtotime($date_f));
					}elseif($date_s== date('Y-m-d', strtotime('tomorrow'))&&$date_f>date('Y-m-d', strtotime('tomorrow'))){
						$this->date_wordy = 'Tomorrow until '. date("l jS F Y",strtotime($date_f));
					}elseif($date_s== date('Y-m-d', strtotime('tommorrow'))){
						$this->date_wordy.= 'Tomorrow';
					}else{
						$this->date_wordy= date("l jS F Y",strtotime($date_s)).' to '.date("l jS F Y",strtotime($date_f));
					}

				}else{
					$this->date = date("l jS F Y",strtotime($date_s));
					if($date_s== date('Y-m-d', strtotime('tomorrow'))){
						$this->date_wordy = 'Tomorrow';
					}elseif($date_s== date('Y-m-d', strtotime('today'))){
						$this->date_wordy = 'Today';
					}else{
						$this->date_wordy = date("l jS F Y",strtotime($date_s));
					}
				}
				$this->numberEvents = $events;
				$this->numberSessions = $sessions;
				//$this->wizard = $wizard;
				/*if($resServ['text']==''&&$resServ['enable']==1){
					$this->resServ['text'] = "<p>Find the results <a href=\"http://".RESULTS_SERVER."/?m=".$resServ['meet']."&series=".$resServ['series']."\">here</a></p>\n";
				}*/
				$this->notes['e'] = $notes['e'];
				$this->notes['c'] = $notes['c'];
				$this->notes['p'] = $notes['p'];
				$this->notes['s'] = $notes['s'];
				
				if(substr($location,0,1)=='%'&&substr($location,-1)=='%'){
					$loc = new Location($this->mySQL);
					$loc->getLocation(substr($location,1,-1));
					$this->location = $loc->getData();
				}else{
					$this->location['name'] = $location;
				}
				$this->link = "/competitions/meet?m=".$this->id;
				$this->docs= strgetcsv($docs);
				if($sessions>0){
					$session_query = $this->mySQL['r']->prepare("
					SELECT
						`SID`,
						`num`,
						`number`,
						`date`,
						`t_warm`,
						`t_sign`,
						`t_start`,
						`events`					
					FROM `comp_session`
					WHERE
						`MID`=?
					ORDER BY
						`num` ASC
					");
					$session_query->bind_param('s',$this->id);
					$session_query->execute();
					$session_query->store_result();
					if($session_query->num_rows!=0){
						$session_query->bind_result(
							$SID,
							$num,
							$number,
							$date,
							$t['warm'],
							$t['sign'],
							$t['start'],
							$events
						);
						while($session_query->fetch()){
							$this->meet['S'][$number]['SID'] = $SID;
							$this->meet['S'][$number]['num'] = $num;
							$this->meet['S'][$number]['date'] = date("d/m/Y",strtotime($date));
							$this->meet['S'][$number]['t']['warm'] = date("H:i",strtotime($t['warm']));
							$this->meet['S'][$number]['t']['sign'] = date("H:i",strtotime($t['sign']));
							$this->meet['S'][$number]['t']['start'] = date("H:i",strtotime($t['start']));
							if($events>0){
								$event_query = $this->mySQL['r']->prepare("
								SELECT
									`num`,
									`number`,
									`prefix`,
									`e_g`,
									`e_d`,
									`e_s`,
									`e_r`,
									`e_al`,
									`e_au`
								FROM `comp_event`
								WHERE
									`MID`=? AND
									`SID`=?
								ORDER BY
									`number` ASC
								");
								$event_query->bind_param('ss',$this->id,$SID);
								$event_query->execute();
								$event_query->store_result();
								if($event_query->num_rows!=0){
									$event_query->bind_result(
										$e_num,
										$e_number,
										$prefix,
										$e['g'],
										$e['d'],
										$e['s'],
										$e['r'],
										$e['a']['l'],
										$e['a']['u']
									);
									while($event_query->fetch()){
										if($prefix==true){
											$this->meet['S'][$number]['E'][$e_number]['num'] = $number.str_pad($e_num,2,'0',STR_PAD_LEFT);
										}else{
											$this->meet['S'][$number]['E'][$e_number]['num'] = $e_num;
										}
										$this->meet['S'][$number]['E'][$e_number]['g'] = $e['g'];
										$this->meet['S'][$number]['E'][$e_number]['d'] = $e['d'];
										$this->meet['S'][$number]['E'][$e_number]['s'] = $e['s'];
										$this->meet['S'][$number]['E'][$e_number]['r'] = $e['r'];
										$this->meet['S'][$number]['E'][$e_number]['al'] = $e['a']['l'];
										$this->meet['S'][$number]['E'][$e_number]['au'] = $e['a']['u'];
									}
									
								}
							}
						}
					}
			}else{
					$this->dispEvts = false;
				}
				return true;
			}
		}else{return false;}
	}
	function eventTitle($value,$round=true){
		//include_once($_SERVER['DOCUMENT_ROOT']."/lib/comp_events.php");
		$title = $this->options['gender'][$value['g']]." ";
		if($value['al']==0&&$value['au']!=0){
			$title .= $value['au'].'/U ';
		}elseif($value['al']!=0&&$value['au']==0){
			$title .= $value['al'].'/O ';
		}elseif($value['al']!=0&&$value['au']!=0){
			$title .= $value['al'].' to '.$value['au'].' ';
		}else{
			$title .= 'Open ';
		}
		$title .= $value['d'].LENGTH." ".$this->strokes[$value['s']].' ';
		if($round==true) $this->options['rounds'][$value['r']];
		return $title;
	}
	function getDispEvts(){
		return $this->dispEvts;
	}
	function getID(){
		return $this->id;
	}
	function getTitle(){
		return $this->title;
	}
	function isEnabled(){
		return $this->enable;
	}
	function getEnabled(){
		return $this->enable;
	}
	function getNumberSessions(){
		return $this->numberSessions;
	}
	function getNumberEvents(){
		return $this->numberEvents;
	}
	function getDate(){
		return $this->date;
	}
	function getDates($option=''){
		$dates['s']=$this->date_s;
		$dates['f']=$this->date_f;
		if($option==''){
			return $dates;
		}elseif(array_key_exists($option,$dates)){
			return $dates[$option];
		}else{
			return false;
		}
	}
	function getDateWordy(){
		return $this->date_wordy;
	}
	function getNotes($note=''){
		if($note==''){
			return $this->notes;	
		}elseif(isset($this->notes[$note])&&$this->notes[$note]!=""){
			return $this->notes[$note];
		}else{
			return false;
		}
	}
	function getTimes($session,$time){
		return $this->meet['S'][$session]['t'][$time];
	}
	function getDocs(){
		if(count($this->docs)!=0){
			return $this->docs;
		}else{
			return false;
		}
	}
	function getEvent($session,$event){
		if(isset($this->meet['S'][$session]['E'][$event])){
			return $this->meet['S'][$session]['E'][$event];
		}else{
			return false;
		}
	}
	function getSession($session){
		if(isset($this->meet['S'][$session])){
			return $this->meet['S'][$session];
		}else{
			return false;
		}
	}
	function getShortMeet(){
		return $this->shortMeet;
	}
	function getLongMeet(){
		return $this->longMeet;
	}
	function shortTitle(){
		$title = "";
		$title.= "    <div class=\"row\">\n";
		$title.= "      <div class=\"col-xs-3\">\n";
		$title.= "        <p><b>Title</b></p>\n";
		$title.= "      </div>\n";
		$title.= "      <div class=\"col-xs-4\">\n";
		$title.= "        <p><b>Date(s)</b></p>\n";
		$title.= "      </div>\n";
		$title.= "      <div class=\"col-xs-1\">\n";
		$title.= "        <p><abbr title=\"Number of Sessions\"><b><span class=\"visible-xs visible-sm\">S</span><span class=\"hidden-xs hidden-sm\">Sessions</span></b></abbr></p>\n";
		$title.= "      </div>\n";
		$title.= "      <div class=\"col-xs-1\">\n";
		$title.= "        <p><abbr title=\"Number of Events\"><b><span class=\"visible-xs visible-sm\">E</span><span class=\"hidden-xs hidden-sm\">Events</span></b></abbr></p>\n";
		$title.= "      </div>\n";
		$title.= "      <div class=\"col-xs-2\">\n";
		$title.= "        <p><b>Where</b></p>\n";
		$title.= "      </div>\n";
		$title.= "      <div class=\"col-xs-1\">\n";
		$title.= "      </div>\n";
		$title.= "    </div>\n";
		return $title;
	}
	function createShortMeet(){
		if($this->numberSessions==0){$numberSessions='--';
		}else{$numberSessions=$this->numberSessions;}
		if($this->numberEvents==0){$numberEvents='--';
		}else{$numberEvents=$this->numberEvents;}
		$meet = "";
		$meet.= "    <div class=\"row\">\n";
		$meet.= "      <div class=\"col-xs-3\">\n";
		$meet.= "        <p>".$this->title."</p>\n";
		$meet.= "      </div>\n";
		$meet.= "      <div class=\"col-xs-4\">\n";
		$meet.= "        <p>".$this->date."</p>\n";
		$meet.= "      </div>\n";
		$meet.= "      <div class=\"col-xs-1\">\n";
		$meet.= "        <p>".$numberSessions."</p>\n";
		$meet.= "      </div>\n";
		$meet.= "      <div class=\"col-xs-1\">\n";
		$meet.= "        <p>".$numberEvents."</p>\n";
		$meet.= "      </div>\n";
		$meet.= "      <div class=\"col-xs-2\">\n";
		if(isset($this->location['id'])){
			$meet.= "        <p><a href=\"/location?l=".$this->location['id']."\">".$this->location['address']['city']."</a></p>\n";
		}else{
			$meet.= "        <p>".$this->location['name']."</p>\n";
		}
		$meet.= "      </div>\n";
		$meet.= "      <div class=\"col-xs-1\">\n";
		$meet.= "        <p><a href=\"".$this->link."\">More</a></p>\n";
		$meet.= "      </div>\n";
		$meet.= "    </div>\n";
		$this->shortMeet = $meet;
	}
	function createLongMeet(){
		global $page;
		$meet = "";
		$meet.="<div class=\"row pane\">\n";
		$meet.="  <div class=\"col-xs-12\">\n";
		if($this->closeBtn){
			$meet.=$page->closeBtn;
		}
		$meet.="    <div class=\"row\">\n";
		$meet.="      <div class=\"col-xs-12\">\n";
		  $meet.="        <h3>".$this->title."</h3>\n";
		$meet.="      </div>\n";
		$meet.="    </div>\n";
		
		$meet.="    <div class=\"row\">\n";
		$meet.="      <div class=\"col-xs-3\">\n";
		$meet.="      	<h4><b>Date:</b></h4>\n";
		$meet.="      </div>\n";
		$meet.="      <div class=\"col-xs-9\">\n";
		$meet.="        <h4>".$this->date."</h4>\n";
		$meet.="      </div>\n";
		$meet.="    </div>\n";
		
		$meet.="    <div class=\"row\">\n";
		$meet.="      <div class=\"col-xs-3\">\n";
		$meet.="      	<h4><b>Location:</b></h4>\n";
		$meet.="      </div>\n";
		$meet.="      <div class=\"col-xs-9\">\n";
		$meet.="        <h4><a href=\"/location?l=".$this->location['id'].'">'.$this->location['name'].', '.$this->location['address']['city']."</a></h4>\n";
		$meet.="      </div>\n";
		$meet.="    </div>\n";
		$meet.="    <div class=\"row\">\n      <div class=\"col-xs-12\">\n        <hr />\n      </div>\n    </div>\n";
		
		$notes = $this->notesArray;
		foreach($notes as $k=>$v){
			$notes=0;
			if(strlen($this->getNotes($k))!=0){
				$meet.="    <div class=\"row\">\n";
				$meet.="      <div class=\"col-xs-3\">\n";
				$meet.="      	<p><b>Notes for $v:</b></p>\n";
				$meet.="      </div>\n";
				$meet.="      <div class=\"col-xs-9\">\n";
				$meet.="        ".$this->getNotes($k)."\n";
				$meet.="      </div>\n";
				$meet.="    </div>\n";
				$notes++;
			}
			if($notes!=0) $meet.="    <div class=\"row\">\n      <div class=\"col-xs-12\">\n        <hr />\n      </div>\n    </div>\n";
		}
		if($this->resServ['enable']!=false){
			$meet.="    <div class=\"row\">\n";
			$meet.="      <div class=\"col-xs-3\">\n";
			$meet.="      	<p><b>Results Service:</b></p>\n";
			$meet.="      </div>\n";
			$meet.="      <div class=\"col-xs-9\">\n";
			$meet.="        ".$this->resServ['text']."\n";
			$meet.="      </div>\n";
			$meet.="    </div>\n";
			$meet.="    <div class=\"row\">\n      <div class=\"col-xs-12\">\n        <hr />\n      </div>\n    </div>\n";
		}
		
		
		if($this->dispEvts===true){
			$meet.="    <div class=\"row\">\n";
			$meet.="      <div class=\"col-xs-12\">\n";
			$meet.="        <div class=\"row\">\n";
			$meet.="          <div class=\"col-xs-12\">\n";
			$meet.="            <h4><b>Schedule</b></h4>\n";
			$meet.="          </div>\n";
			$meet.="        </div>\n";
			$times = array("warm"=>"Warm Up","sign"=>"Sign In/Withdraw","start"=>"Start");
			foreach($this->meet['S'] as $s=>$session){
				$meet.="        <div class=\"row\">\n";
				$meet.="          <div class=\"col-xs-12\">\n";	
				if($this->numberSessions>1){	
					$meet.="            <div class=\"row\">\n";
					$meet.="              <div class=\"col-xs-12\">\n";
					$meet.="                <p><b>Session ".$s." - ".date("l jS F Y",strtotime(str_replace('/','-',$session['date'])))."</b></p>\n";
					$meet.="              </div>\n";
					$meet.="            </div>\n";
				}
				$meet.="            <div class=\"row\">\n";
				foreach($times as $k=>$v){
					$meet.="              <div class=\"col-xs-6 col-sm-2 times\">\n";
					$meet.="                <p><b>$v:</b></p>\n";
					$meet.="              </div>\n";
					$meet.="              <div class=\"col-xs-6 col-sm-2\">\n";
					$meet.="                <p>".$this->getTimes($s,$k)."</p>\n";
					$meet.="              </div>\n";
				}
				$meet.="            </div>\n";
				$meet.="            <div class=\"row\">\n";
				$meet.="              <div class=\"col-xs-10 col-md-offset-1\">\n";
				$meet.="                <div class=\"row\">\n";
				$meet.="                  <div class=\"col-md-2 col-xs-3\">\n";
				$meet.="                    <p><b>Event #</b></p>\n";
				$meet.="                  </div>\n";
				$meet.="                  <div class=\"col-md-3 col-xs-9\" align=\"center\">\n";
				$meet.="                    <p><b>Event</b></p>\n";
				$meet.="                  </div>\n";
				$meet.="                  <div class=\"col-md-2 col-xs-3 visible-md visible-lg col-md-offset-1\">\n";
				$meet.="                    <p><b>Event #</b></p>\n";
				$meet.="                  </div>\n";
				$meet.="                  <div class=\"col-md-3 col-xs-9 visible-md visible-lg\" align=\"center\">\n";
				$meet.="                    <p><b>Event</b></p>\n";
				$meet.="                  </div>\n";
				$meet.="               </div>\n";
				$meet.="             </div>\n";
				$meet.="           </div>\n";
				$meet.="           <div class=\"row\">\n";
				foreach($session['E'] as $e=>$event){
					
					$meet.="             <div class=\"col-md-1 hidden-xs hidden-sm\"></div>\n";
					if($this->getEvent($s,$e)!==false){
							$event = $this->getEvent($s,$e);
							$meet.="             <div class=\"col-md-1 col-xs-3 times\">\n";
							$meet.="               <p>".$event['num']."</p>\n";
							$meet.="             </div>\n";
							$meet.="             <div class=\"col-md-4 col-xs-9 times\">\n";
							$meet.="               <p>".$this->eventTitle($event)."</p>\n";
							$meet.="             </div>\n";
					}
					
				}
				$meet.="           </div>\n";
				/*for($e=1;$e<($this->numberEvents);$e=$e+2){
					
					for($n=0;$n<=1;$n++){
						if($this->getEvent($s,($e+$n))!==false){
							$event = $this->getEvent($s,($e+$n));
							$meet.="             <div class=\"col-md-1 col-xs-3 times\">\n";
							$meet.="               <p>".$event['num']."</p>\n";
							$meet.="             </div>\n";
							$meet.="             <div class=\"col-md-4 col-xs-9 times\">\n";
							$meet.="               <p>".$this->eventTitle($event)."</p>\n";
							$meet.="             </div>\n";
						}
					}
					$meet.="           </div>\n";
				}*/
				$meet.="         </div>\n";
				$meet.="       </div>\n";
				if($s!=$this->numberSessions){$meet.="        <div class=\"row\">\n          <div class=\"col-xs-12 col-md-10 col-md-offset-1\">\n            <hr />\n           </div>\n        </div>\n";
				}else{$meet.="    <div class=\"row\">\n      <div class=\"col-xs-12\">\n        <hr />\n      </div>\n    </div>\n";$meet.="     </div>\n";$meet.="   </div>\n";}
			}
		}
		if($this->docs!==false){
			$doc = $this->doc;
			$docs=array();
			$numberDocs=0;
			foreach($this->docs as $docID){
				$doc->setID($docID);
				$doc->createVariables();
				if($doc->displayOnPage($this->pageNumber)){
					$numberDocs++;
					$doc->createDocRow();
					$docs[$numberDocs]= $doc->getDocRow();
				}
				$doc->clear(true);
			}
			if($numberDocs!=0){
				$meet.="    <div class=\"row\">\n";
				$meet.="      <div class=\"col-xs-12\">\n";
				$meet.="        <div class=\"row\">\n";
				$meet.="          <div class=\"col-xs-12\">\n";
				$meet.="            <h4><b>Forms And Documents</b></h4>\n";
				$meet.="          </div>\n";
				$meet.="        </div>\n";
				$meet.= $doc->getDocRowHeader();
				foreach($docs as $row){
					$meet.= $row;
				}
				$meet.="    <div class=\"row\">\n      <div class=\"col-xs-12\">\n        <hr />\n      </div>\n    </div>\n";
				$meet.="      </div>\n";
				$meet.="    </div>\n";
			}
				
		}
		$meet.="  </div>\n";
		$meet.="</div>\n";
		$this->longMeet = $meet;
	}
}

/**
 * Competition Manager Class
 *
 * @category   Comp.Manager
 */

/*
 */
class Competition{
	protected $mySQL;
	
	function __construct($link){
		$this->mySQL = $link;
	}
	
	function add_Comp($title,$date_s,$date_f,$location,$disp_f,$disp_u,$licence){
		global $user;
		if($user->accessPage(58)){
			$query = $this->mySQL['r']->prepare("SELECT `ID` from `comp_meet` WHERE `title` LIKE CONCAT('%',?,'%')");
			$query->bind_param('s',$title);
			$query->execute();
			$query->store_result();
			if($query->num_rows==0){
				$ID = ranString(6);
				$ids = $this->mySQL['r']->prepare("SELECT `ID` FROM `comp_meet` WHERE `ID`=?");
				$ids->bind_param($ID);
				$ids->execute();
				$ids->store_result();
				while($ids->num_rows!=0){
					$ID = ranString(6);
					$ids->bind_param($ID);
					$ids->execute();
					$ids->store_result();
				}
				$this->mySQL['w']->autocommit(false);
				$stmt = $this->mySQL['w']->prepare("INSERT INTO `comp_meet` (`ID`,`title`,`date_c`,`location`,`disp_f`,`disp_u`,`date_s`,`date_f`,`licence`,`wizStat`) VALUES(?,?,NOW(),?,FROM_UNIXTIME(?),FROM_UNIXTIME(?),FROM_UNIXTIME(?),FROM_UNIXTIME(?),?,1)");
				if($stmt!==false){
					$disp_f = strtotime($disp_f);
					$disp_u = strtotime($disp_u);
					$date_s = strtotime($date_s);
					$date_f = strtotime($date_f);
					
					$stmt->bind_param('sssiiiis',$ID,$title,$location,$disp_f,$disp_u,$date_s,$date_f,$licence);
					$stmt->execute();
					$stmt->store_result();
					if($stmt->affected_rows==1){
						$this->mySQL['w']->commit();
						$this->mySQL['w']->autocommit(true);return array('res'=>0,'id'=>$ID);
					}else{
						$this->mySQL['w']->rollback();
						$this->mySQL['w']->autocommit(true);return 1;
					}
				}else{$this->mySQL['w']->autocommit(true);return 2;}
			}else{return 3;}
		}else{return 4;}
	}
	function add_Comp_notes($id,$entry,$coaches,$swimmers,$parents){
		global $user;
		if($user->accessPage(59)){
			$query = $this->mySQL['r']->prepare("SELECT `ID` from `comp_meet` WHERE `ID`=?");
			$query->bind_param('s',$id);
			$query->execute();
			$query->store_result();
			if($query->num_rows==1){
				$this->mySQL['r']->autocommit(false);
				$stmt = $this->mySQL['w']->prepare("UPDATE `comp_meet` SET `notes_e`=?,`notes_c`=?,`notes_s`=?,`notes_p`=?,`wizStat`=2 WHERE `ID`=?");
				if($stmt!==false){
					
					$stmt->bind_param('sssss',$entry,$coaches,$swimmers,$parents,$id);
					$stmt->execute();
					$stmt->store_result();
					if($stmt->affected_rows==1){
						$this->mySQL['w']->commit();
						$this->mySQL['w']->autocommit(true);return array('res'=>0,'id'=>$ID);
					}else{
						$this->mySQL['w']->rollback();
						$this->mySQL['w']->autocommit(true);return 1;
					}
				}else{$this->mySQL['w']->autocommit(true);return 2;}
			}else{return 3;}
		}else{return 4;}
	}
	function add_Comp_docs($id,$docs_array){
		global $user;
		if($user->accessPage(60)){
			$query = $this->mySQL['r']->prepare("SELECT `ID` from `comp_meet` WHERE `ID`=?");
			$query->bind_param('s',$id);
			$query->execute();
			$query->store_result();
			if($query->num_rows==1){
				$docs=csvgetstr($docs_array);
				$this->mySQL['r']->autocommit(false);
				$stmt = $this->mySQL['w']->prepare("UPDATE `comp_meet` SET `docs`=?,`wizStat`=3 WHERE `ID`=?");
				if($stmt!==false){
					$stmt->bind_param('ss',$docs,$id);
					$stmt->execute();
					$stmt->store_result();
					if($stmt->affected_rows==1){
						$this->mySQL['w']->commit();
						$this->mySQL['w']->autocommit(true);return array('res'=>0,'id'=>$ID);
					}else{
						$this->mySQL['w']->rollback();
						$this->mySQL['w']->autocommit(true);return 1;
					}
				}else{$this->mySQL['w']->autocommit(true);return 2;}
			}else{return 3;}
		}else{return 4;}
	}
	function add_Comp_sessions($MID,$sessions){
		global $user;
		if($user->accessPage(61)){
			$query = $this->mySQL['r']->prepare("SELECT `ID` from `comp_meet` WHERE `ID`=?");
			$query->bind_param('s',$MID);
			$query->execute();
			$query->store_result();
			if($query->num_rows==1){
				$this->mySQL['w']->autocommit(false);
				$update = $this->mySQL['w']->prepare("UPDATE `comp_meet` SET `sessions`=`sessions`+1,`wizStat`=4 WHERE `ID`=?");
				$insert = $this->mySQL['w']->prepare("INSERT INTO `comp_session` (`MID`,`SID`,`number`,`num`,`date`,`t_warm`,`t_sign`,`t_start`) VALUES(?,?,?,?,FROM_UNIXTIME(?),?,?,?)");
				$ids = $this->mySQL['r']->prepare("SELECT `SID` FROM `comp_session` WHERE `SID`=?");
				if($insert!==false&&$ids!==false&&$update!==false){
					$numRows = 0;
					foreach($sessions as $num=>$session){
						$SID = ranString(6);
						$ids->bind_param('s',$SID);
						$ids->execute();
						$ids->store_result();
						while($ids->num_rows!=0){
							$SID = ranString(6);
							$ids->bind_param('s',$SID);
							$ids->execute();
							$ids->store_result();
						}
						$update->bind_param('s',$MID);
						$update->execute();
						$session['d'] = strtotime($session['d']);
						$insert->bind_param('ssiiisss',$MID,$SID,$num,$session['n'],$session['d'],$session['t']['w'],$session['t']['i'],$session['t']['s']);
						$insert->execute();
						$numRows = $numRows + $insert->affected_rows;
					}
					$insert->store_result();
					$select = $this->mySQL['r']->prepare("SELECT `sessions` FROM `comp_meet` WHERE `ID`=?");
					$select->bind_param('s',$MID);
					$select->execute();
					$select->bind_result($comp_sess);
					$select->store_result();
					$select->fetch();
					if(($numRows==$comp_sess)&&($comp_sess==count($sessions))){
						$this->mySQL['w']->commit();
						$this->mySQL['w']->autocommit(true);return array('res'=>0,'id'=>$MID);
					}else{
						$this->mySQL['w']->rollback();
						$this->mySQL['w']->autocommit(true);return 1;
					}
				}else{$this->mySQL['w']->autocommit(true);return 2;}
			}else{return 3;}
		}else{return 4;}
	}
	function add_Comp_events($MID,$events){
		global $user;
		if($user->accessPage(62)){
			$query = $this->mySQL['r']->prepare("SELECT `ID` from `comp_meet` WHERE `ID`=?");
			$query->bind_param('s',$MID);
			$query->execute();
			$query->store_result();
			if($query->num_rows==1){
				$this->mySQL['w']->autocommit(false);
				$update_m = $this->mySQL['w']->prepare("UPDATE `comp_meet` SET `events`=`events`+1,`wizStat`=5 WHERE `ID`=?");
				$update_s = $this->mySQL['w']->prepare("UPDATE `comp_session` SET `events`=`events`+1 WHERE `MID`=? AND `SID`=?");
				$insert = $this->mySQL['w']->prepare("INSERT INTO `comp_event` (`MID`,`SID`,`EID`,`number`,`num`,`prefix`,`e_g`,`e_d`,`e_s`,`e_r`,`e_al`,`e_au`) VALUES(?,?,?,?,?,0,?,?,?,?,?,?)");
				$ids = $this->mySQL['r']->prepare("SELECT `EID` FROM `comp_event` WHERE `EID`=?");
				if($insert!==false&&$ids!==false&&$update_m!==false&&$update_s!==false){
					$numRows = 0;
					foreach($events as $num=>$event){
						$EID = ranString(6);
						$ids->bind_param('s',$EID);
						$ids->execute();
						$ids->store_result();
						while($ids->num_rows!=0){
							$EID = ranString(6);
							$ids->bind_param('s',$EID);
							$ids->execute();
							$ids->store_result();
						}
						$update_m->bind_param('s',$MID);
						$update_m->execute();
						$update_s->bind_param('ss',$MID,$event['n']['s']);
						$update_s->execute();
						$insert->bind_param('sssiissssii',$MID,$event['n']['s'],$EID,$num,$event['n']['e'],$event['g'],$event['d'],$event['s'],$event['r'],$event['a']['l'],$event['a']['u']);
						$insert->execute();
						$numRows = $numRows + $insert->affected_rows;
					}

					$select = $this->mySQL['r']->prepare("SELECT `events` FROM `comp_meet` WHERE `ID`=?");
					$select->bind_param('s',$MID);
					$select->execute();
					$select->bind_result($comp_evts);
					$select->store_result();
					$select->fetch();
					if(($numRows==$comp_evts)&&($comp_evts==count($events))){
						$this->mySQL['w']->commit();
						$this->mySQL['w']->autocommit(true);return array('res'=>0,'id'=>$MID);
					}else{
						$this->mySQL['w']->rollback();
						$this->mySQL['w']->autocommit(true);return 1;
					}
				}else{$this->mySQL['w']->autocommit(true);return 2;}
			}else{return 3;}
		}else{return 4;}
	}
	function add_Comp_res($MID,$enable,$meet,$series,$text,$download,$services,$nextSession='',$indSession=0){
		global $user;
		if($user->accessPage(63)){
			$query = $this->mySQL['r']->prepare("SELECT `ID` from `comp_meet` WHERE `ID`=?");
			$query->bind_param('s',$MID);
			$query->execute();
			$query->store_result();
			if($query->num_rows==1){
				$this->mySQL['w']->autocommit(false);
				$stmt = $this->mySQL['w']->prepare("
				INSERT INTO `comp_res` (
					`MID`,
					`enable`,
					`text`,
					`download`,
					`meet`,
					`series`,
					`services`,
					`nextSession`,
					`indSession`
				)
				VALUES (
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?
				)
				");
				if($stmt!==false){
					$services = serialize($services);
					$stmt->bind_param('sssssss', $MID, $enable, $text, $download, $meet, $series, $services, $nextSession, $indSession);
					$stmt->execute();
					$stmt->store_result();
					if($stmt->affected_rows==1){
						$resServ = $this->createResServ($MID,true);
						if($resServ===true){
							$this->mySQL['w']->commit();
							$this->mySQL['w']->autocommit(true);return array('res'=>0,'id'=>$MID);
						}else{
							$this->removeResServ($MID,true);
							$this->mySQL['w']->rollback();
							$this->mySQL['w']->autocommit(true);return 1;
						}
					}else{
						$this->mySQL['w']->rollback();
						$this->mySQL['w']->autocommit(true);return 2;
					}
				}else{$this->mySQL['w']->autocommit(true);return 3;}
			}else{return 4;}
		}else{return 5;}
	}
	
	function createResServ($MID,$authenticated=false){
		chdir("/var/www/vhosts/biggleswadesc.org/subdomains/results.biggleswadesc.org/httpdocs/res");
		global $user;
		if((($user->accessPage(63)||$user->accessPage(64))&&!$authenticated)||$authenticated){
			$query = $this->mySQL['r']->prepare("SELECT `MID`,`meet`,`series`, from `comp_res` WHERE `MID`=? AND `enable`='1'");
			$query->bind_param('s',$MID);
			$query->execute();
			$query->store_result();
			if($query->num_rows==1){
				$comp = new Meet($this->mySQL);
				$comp->setID($MID);
				$comp->createMeet();
				$query->bind_result($MID,$meet,$series);
				$query->fetch();
				
				if(!file_exists($meet)){
					mkdir($meet);
				}elseif(file_exists($meet)&&!is_dir($meet)){
					unlink($meet);
					mkdir($meet);
				}
				chdir($meet);
				
				if(!file_exists($series)){
					mkdir($series);
				}elseif(file_exists($series)&&!is_dir($series)){
					unlink($series);
					mkdir($series);
				}
				chdir($series);
				if($comp_meet->res['indivSession']){
					foreach($comp->meet['S'] as $i=>$session){
						if(!file_exists('session'.$i)){
							mkdir('session'.$i);
						}elseif(file_exists('session'.$i)&&!is_dir('session'.$i)){
							unlink('session'.$i);
							mkdir('session'.$i);
						}
					}
				}
				$meetFile = fopen('meet.php','w');
				$file ="<?php\n";
				$file.="/*\n*\n* Meet Definition PHP File\n* Author: BWSC Website (Huw Jones)\n* (C) Biggleswade Swimming Club\n* File Created: ".date("H:i:s d/m/Y")."\n*/\n";
				$file.="\n/*\n* Version\n*/\n";
				$file.="// 3 = Event titles are hardcoded into the meet.php file. These files have been created automatically\n";
				$file.='$meet["version"]'."\t\t\t\t".'= 3;'.PHP_EOL;
				$file.="\n/*\n* Core\n*/\n";
				$file.="// Meet title \n";
				$file.='$meet["core"]["title"]'."\t\t\t".'= "'.$comp->getTitle().'";'.PHP_EOL;
				$file.="// Meet title \n";
				$file.='$meet["core"]["licence"]'."\t\t".'= "'.$comp->licence.'";'.PHP_EOL;
				$file.="// Number of Sessions (For Continuity Checking)\n";
				$file.='$meet["core"]["sessions"]'."\t\t".'= '.$comp->getNumberSessions().';'.PHP_EOL;
				$file.="// Number of Events (For Continuity Checking)\n";
				$file.='$meet["core"]["events"]'."\t\t\t".'= '.$comp->getNumberEvents().';'.PHP_EOL;
				$file.="\n// Date\n";
				$file.="// Set the date prefix for the event files. dd/mm/yyYY should be set as YYmmdd\n";
				$file.='$meet["core"]["date"]["prefix"]'."\t".'= "'.date("ymd",strtotime($comp->getDates('s'))).'";'.PHP_EOL;
				$file.="// Start Date\n";
				$file.='$meet["core"]["date"]["start"]'."\t".'= "'.$comp->getDates('s').'";'.PHP_EOL;
				$file.="// Finish Date\n";
				$file.='$meet["core"]["date"]["finish"]'."\t".'= "'.$comp->getDates('f').'";'.PHP_EOL;
				$file.="// Individual Sessions\n";
				$file.='$meet["core"]["indivSess"]'."\t\t".'= "'.$comp->resServ['indivSess'].'";'.PHP_EOL;
				$file.="// Next Session\n";
				$file.='$meet["core"]["nextSession"]'."\t".'= "'.$comp->resServ['nextSession'].'";'.PHP_EOL;
				$file.="\n// Download Results\n";
				$file.="// If you wish user to be able to download the results as PDFs, set to true.\n";
				$file.="// If you are going to upload PDFs set to 'PDF', otherwise set to 'compile' and the service will compile the results files into PDFs on the fly.\n";
				$file.='$meet["core"]["download"]'."\t\t".'= "'.strtoupper($comp->resServ['download']).'";'.PHP_EOL;
				$file.="\n/*\n*\n* Services\n*\n*/\n";
				$file.='// Use the $meet["services"] array with the value as the title (as it will appear to the user)'.PHP_EOL;
				$file.="// and the key as the suffix of the file. I.e: yymmdd[SUFFIX].htm\n";
				$file.='// Eg: $meet["services"]["lastheat"] = "Last Heat";'.PHP_EOL;
				foreach($comp->resServ['services'] as $k=>$service){
					if($service==1){
						if(strlen($k)<7){ $tab = "\t\t"; }else{ $tab = "\t";}
						$file.='$meet["services"]["'.$k.'"]'.$tab.'= "'.$comp->options['res-services'][$k]['title'].'";'.PHP_EOL;
					}
				}
				
				$file.="\n/*\n*\n* Meet Sessions and Events\n*\n*/\n";
				$file.='// For each of the sessions, add events to the $meet["session"]["X"]["events"] array, where X is the session number,'.PHP_EOL;
				$file.="// the key is the initial for the event code and the value is the event title.\n";
				$file.='// Eg: $meet["session"]["1"]["events"]["F001"]["title" = "Girls 9/O 50m Freestyle";'.PHP_EOL;
				foreach($comp->meet['S'] as $s=>$session){
					$file.="\n// Session ".$s.PHP_EOL;
					$file.="// Date\n";
					$file.='$meet["session"]['.$s.']["date"]'."\t\t\t\t\t\t".'= '.strtotime(str_replace('/','-',$session['date'])).';'.PHP_EOL;
					$file.="// Start Time\n";
					$file.='$meet["session"]['.$s.']["times"]["start"]'."\t\t\t".'= '.strtotime($session['t']['start']).';'.PHP_EOL;
					$file.="// Events\n";
					foreach($session['E'] as $e=>$event){
						if($comp->getEvent($s,$e)!==false){
							$event = $comp->getEvent($s,$e);
							$title = $comp->eventTitle($event,false);
							if($event['r']=='t'){$event['round']='f';$event['r']='h';}else{$event['round']=$event['r'];}
							$file.='$meet["session"]['.$s.']["events"]["'.strtoupper($event['round']).str_pad($event['num'],3,'0',STR_PAD_LEFT).'"]["title"]'."\t".'= "'.$title.'";'.PHP_EOL;
							$file.='$meet["session"]['.$s.']["events"]["'.strtoupper($event['round']).str_pad($event['num'],3,'0',STR_PAD_LEFT).'"]["round"]'."\t".'= "'.$comp->options['rounds'][$event['r']].'";'.PHP_EOL;
							$file.='$meet["session"]['.$s.']["events"]["'.strtoupper($event['round']).str_pad($event['num'],3,'0',STR_PAD_LEFT).'"]["r"]'."\t\t".'= "'.$event['r'].'";'.PHP_EOL;
							$file.='$meet["session"]['.$s.']["events"]["'.strtoupper($event['round']).str_pad($event['num'],3,'0',STR_PAD_LEFT).'"]["s"]'."\t\t".'= "'.$event['s'].'";'.PHP_EOL;
							$file.='$meet["session"]['.$s.']["events"]["'.strtoupper($event['round']).str_pad($event['num'],3,'0',STR_PAD_LEFT).'"]["d"]'."\t\t".'= "'.$event['d'].'";'.PHP_EOL;
							$file.='$meet["session"]['.$s.']["events"]["'.strtoupper($event['round']).str_pad($event['num'],3,'0',STR_PAD_LEFT).'"]["g"]'."\t\t".'= "'.$event['g'].'";'.PHP_EOL;
						}
					}
				}
				$file.="?>\n";
				fwrite($meetFile,$file);
				fclose($meetFile);
				return true;
			}else{return false;}
		}else{return false;}
	}
	function removeResServ($MID,$authenicated=false){
		chdir("/var/www/vhosts/biggleswadesc.org/subdomains/results.biggleswadesc.org/httpdocs/res");
		global $user;
		if(($user->accessPage(67)&&!$authenicated)||$authenicated){
			$query = $this->mySQL['r']->prepare("SELECT `ID`,`meet`,`series` from `comp_meet` WHERE `ID`=?");
			$query->bind_param('s',$MID);
			$query->execute();
			$query->store_result();
			if($query->num_rows==1){
				$query->bind_result($MID,$meet,$series);
				$query->fetch();
				$this->mySQL['r']->autocommit(false);
				$stmt = $this->mySQL['r']->prepare("UPDATE `comp_meet` SET `meet`='',`series`='',`resText`='',`resServ`='0' WHERE `ID`=?");
				if($stmt!==false){
					$stmt->bind_param('s',$MID);
					$stmt->execute();
					$stmt->store_result();
					if($stmt->affected_rows==1){
						if(file_exists($meet.'/'.$series.'/meet.php')) unlink($meet.'/'.$series.'/meet.php');
						$this->mySQL['r']->commit();
						$this->mySQL['r']->autocommit(true);return array('res'=>0,'id'=>$ID);
					}else{
						$this->mySQL['r']->rollback();
						$this->mySQL['r']->autocommit(true);return 2;
					}
				}else{$this->mySQL['r']->autocommit(true);return 3;}
			}else{return 4;}
		}else{return 5;}
	}
	function edit_Comp(){
	}
	function del_Comp($ID){
		global $user;
		if($user->accessPage(66)){
			$query = $this->mySQL['r']->prepare("SELECT `events`,`sessions` from `comp_meet` WHERE `ID`=?");
			$query->bind_param('s',$ID);
			$query->execute();
			$query->bind_result($events,$sessions);
			$query->store_result();
			$query->fetch();
			if($query->num_rows==1){
				$this->mySQL['r']->autocommit(false);
				$del['comp'] = $this->mySQL['r']->prepare("DELETE FROM `comp_meet` WHERE `ID`=?");
				$del['sess']  = $this->mySQL['r']->prepare("DELETE FROM `comp_session` WHERE `MID`=?");
				$del['evts']  = $this->mySQL['r']->prepare("DELETE FROM `comp_event` WHERE `MID`=?");
				if(!in_array(false,$del)){
					
					$del['comp']->bind_param('s',$ID);
					$del['comp']->execute();
					$del['comp']->store_result();
					$del['sess']->bind_param('s',$ID);
					$del['sess']->execute();
					$del['sess']->store_result();
					$del['evts']->bind_param('s',$ID);
					$del['evts']->execute();
					$del['evts']->store_result();
					
					if($del['comp']->affected_rows!=0){
						if($del['sess']->affected_rows==$sessions){
							if($del['evts']->affected_rows==$events){
								$this->mySQL['r']->commit();
								$this->mySQL['r']->autocommit(true);return 0;
							}else{$this->mySQL['r']->rollback();$this->mySQL['r']->autocommit(true);return 1;}
						}else{$this->mySQL['r']->rollback();$this->mySQL['r']->autocommit(true);return 2;}
					}else{$this->mySQL['r']->rollback();$this->mySQL['r']->autocommit(true);return 3;}
				}else{$this->mySQL['r']->autocommit(true);return 4;}
			}else{return 5;}
		}else{return 6;}
	}
	function enable($ID,$mode){
		global $user;
		if($user->accessPage(63)){
			$stmt = $this->mySQL['r']->prepare("UPDATE `comp_meet` SET `enable`=? WHERE `ID`=?");
			if($stmt!==false){
				$stmt->bind_param('is',$mode,$ID);
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
	
	function add_Sess(){
	}
	function edit_Sess(){
	}
	function del_Sess(){
	}
	
	function add_Evt(){
	}
	function edit_Evt(){
	}
	function del_Evt(){
	}
}
?>