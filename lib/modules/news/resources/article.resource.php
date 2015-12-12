<?php
/**
 * Article Resource Class
 *
 * @category   Module.News.Article.Resource
 * @package    competitions/resource.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
 */

class ArticleResource extends NewsResource
{
	const	name_space	= 'Module.New.Article';
	const	version		= '1.0.0';
	
	public		$ID		= '';
	public		$title		= '';
	protected	$options	= false;
	
	function __construct($parent){
		$this->parent = $parent;
		$this->mySQL_r = $parent->mySQL_r;
		$this->hr = '    <div class="row">'.PHP_EOL.'      <div class="col-xs-12">'.PHP_EOL.'        <hr />'.PHP_EOL.'      </div>'.PHP_EOL.'    </div>'.PHP_EOL;
		$this->parent->parent->debug('***** '.$this::name_space.' *****');
		$this->parent->parent->debug($this::name_space.': Version '.$this::version);
	}
	function loadOptions(){
		if($this->options===false){
			$options['strokes'] = array();
		
			$strokes = unserialize($this->parent->parent->config->getOption('comp_strokes'));
			foreach($strokes as $group=>$strokes){
				$options['strokes'] = array_merge($options['strokes'], $strokes);
			}
		
			$options['times']	= unserialize($this->parent->parent->config->getOption('comp_times'));
			$options['gender']	= unserialize($this->parent->parent->config->getOption('comp_gender'));
			$options['rounds']	= unserialize($this->parent->parent->config->getOption('comp_rounds'));
			$this->options = $options;
		}
	}
	
	function parseEvent($evt, $returnArray=false){
		$this->loadOptions();
		
		$event['gender']	= $this->options['gender'][$evt['g']];
		
		if($evt['al']==0&&$evt['au']!=0){
			$event['age']	= $evt['au'].'/U';
		}elseif($evt['al']!=0&&$evt['au']==0){
			$event['age']	= $evt['al'].'/O';
		}elseif($evt['al']!=0&&$evt['au']!=0){
			$event['age']	= $evt['al'].' to '.$evt['au'];
		}else{
			$event['age']	= 'Open';
		}
		
		$event['distance']	= $evt['d'].'m';
		$event['stroke']	= $this->options['strokes'][$evt['s']];
		$event['round']	 	= $this->options['rounds'][$evt['r']];
		
		if($returnArray===false){
			$event = implode(' ', $event);			
		}
		return $event;
	}
	
	function getLocation($component){
		if($this->location!==false){
			return $this->location->getComponent($component);
		}else{
			return '';
		}
	}
	function fetchCompetition($id){
		$this->ID = $id;
		$this->loadOptions();
		$meet_q = $this->parent->mySQL_r->prepare("SELECT
									`title`,
									`location`,
									`course`,
									`display_notes`,	`display_schedule`,
									`disp_f`,			`disp_u`,
									`date_c`,			`date_s`,		`date_f`,		`date_e`,		`date_a`,
									`licence`,
									`notes_e`,			`notes_c`,		`notes_s`,		`notes_p`,
									`docs`,
									`enable`
							FROM `comp_meet`
							WHERE `ID`=?");
		if(!$meet_q){
			$this->parent->parent->debug($this::name_space.': MySQL Statement error in competition fetch!');
			return false;
		}
		$meet_q->bind_param('s', $id);
		$meet_q->execute();
		$meet_q->store_result();
		
		$res_q = $this->parent->mySQL_r->prepare("SELECT
									`enable`,
									`text`,
									`download`,
									`meet`,				`series`,
									`services`,
									`nextSession`,
									`indSession`
							FROM `comp_res`
							WHERE `MID`=?");
		if(!$res_q){
			$this->parent->parent->debug($this::name_space.': MySQL Statement error in results fetch!');
			return false;
		}
		$res_q->bind_param('s', $id);
		$res_q->execute();
		$res_q->store_result();
		
		$session_q = $this->parent->mySQL_r->prepare("SELECT
									`SID`,
									`number`,
									`num`,
									`date`,
									`t_warm`,			`t_sign`,		`t_start`
							FROM `comp_session`
							WHERE `MID`=?
							ORDER BY `number` ASC");
		if(!$session_q){
			$this->parent->parent->debug($this::name_space.': MySQL Statement error in session fetch!');
			return false;
		}	
		
		$event_q = $this->parent->mySQL_r->prepare("SELECT
									`EID`,
									`number`,
									`num`,
									`prefix`,
									`cost`,
									`e_g`,				`e_d`,			`e_s`,			`e_r`,
									`e_al`,				`e_au`
							FROM `comp_event`
							WHERE `MID`=? AND `SID`=?
							ORDER BY `number` ASC");
		if(!$event_q){
			$this->parent->parent->debug($this::name_space.': MySQL Statement error in event fetch!');
			return false;
		}
		
		if($meet_q->num_rows==1){
			$this->parent->parent->debug($this::name_space.': Fetching competition "'.$id.'"');
			$meet_q->bind_result(
									$title,
									$location,
									$course,
									$show['notes'],			$show['schedule'],
									$disp['from'],			$disp['until'],
									$date['created'],		$date['start'],			$date['finish'],		$date['entry'],		$date['ageat'],
									$licence,
									$notes['entries'],		$notes['coaches'],		$notes['swimmers'],		$notes['parents'],
									$docs,
									$enable
								);
			$this->parent->parent->debug($this::name_space.': Fetching results service config for "'.$id.'"');		
			$res_q->bind_result(
									$res['enable'],
									$res['text'],
									$res['download'],
									$res['meet'],
									$res['series'],
									$res['services'],
									$res['nextSession'],
									$res['indSession']
								);
			$session_q->bind_param('s', $id);
			$session_q->bind_result(
									$s_SID,
									$s_number,
									$s_num,
									$s_date,
									$s_t_warm,
									$s_t_sign,
									$s_t_start
								);
			$event_q->bind_result(
									$e_EID,
									$e_number,
									$e_num,
									$e_prefix,
									$e_cost,
									$e_g,		$e_d,		$e_s,		$e_r,
									$e_al,
									$e_au
								);

			$this->parent->parent->debug($this::name_space.': Fetching sessions for "'.$id.'"');

			while($meet_q->fetch()){
				$this->parent->parent->debug($this::name_space.': Fetched meet "'.$id.'"!');	
				$locRes = $this->parent->getResource('location');
				if($locRes!==false){
					$this->location = $locRes->parseLocation($location);
				}else{
					$this->location = false;
				}
				if($date['start']==$date['finish']){
					$date['long']				= date("l jS F Y",strtotime($date['start']));
				}else{
					$date['long']				= date("l jS F Y",strtotime($date['start'])).' to '.date(DATE_LONG,strtotime($date['finish']));
				}
				if($date['entry']=='0000-00-00'){
					$date['entry'] = 'TBA';
				}else{
					$date['entry'] = date(DATE_SHORT, strtotime($date['entry']));
				}
				$this->title				= $title;
				$this->course				= $course;
				$this->display				= $disp;
				$this->date					= $date;
				$this->licence				= $licence;
				$this->notes				= $notes;
				$this->docs					= strgetcsv($docs);
				$this->enable				= $enable;
				$this->show					= $show;
				
				while($res_q->fetch()){
					$this->parent->parent->debug($this::name_space.': Fetched results config for meet "'.$id.'"!');
					$res['services'] = unserialize($res['services']);
					$this->res = $res;
				}
				$res_q->free_result();
				$res_q->close();

				$session_q->execute();
				$session_q->store_result();
				$data = array();
				$data['sessions']	= 0;
				$data['events']		= 0;
				while($session_q->fetch()){
					$data['sessions']++;
					$s['SID']			= $s_SID;
					$s['number']		= $s_number;
					$s['num']			= $s_num;
					$s['date']			= date("l jS F Y", strtotime($s_date));
					$s['t']['warm']		= ($s_t_warm=='00:00:00')? 'TBC' :date('H:i',strtotime($s_t_warm));
					$s['t']['sign']		= ($s_t_sign=='00:00:00')? 'TBC' :date('H:i',strtotime($s_t_sign));
					$s['t']['start']	= ($s_t_start=='00:00:00')? 'TBC' :date('H:i',strtotime($s_t_start));
					$this->parent->parent->debug($this::name_space.': Fetched session "'.$s['SID'].'" ('.$s['number'].') for meet "'.$id.'"!');
					
					$event_q->bind_param('ss', $id, $s['SID']);
					$event_q->execute();
					$event_q->store_result();
					while($event_q->fetch()){
						$data['events']++;
						$e['EID']		= $e_EID;
						$e['number']	= $e_number;
						$e['num']		= $e_num;
						$e['prefix']	= $e_prefix;
						$e['cost']		= $e_cost;
						$e['g']			= $e_g;
						$e['d']			= $e_d;
						$e['s']			= $e_s;
						$e['r']			= $e_r;
						$e['al']		= $e_al;
						$e['au']		= $e_au;
						$s['E'][$e['number']] = $e;
						$this->parent->parent->debug($this::name_space.': Fetched event "'.$e['EID'].'" ('.$e['number'].') for session "'.$s['SID'].'" for meet "'.$id.'"!');
					}
					
					$data['S'][$s['number']] = $s;
					unset($s);
				}
				$event_q->free_result();
				$session_q->free_result();
				$this->data = $data;
			}
			return $this;
		}else{
			return false;
		}
	}
	
	function format($type='all'){
		switch($type){
			case 'all':
				$variables['meetID']		= $this->ID;
				$variables['meetTitle']		= $this->title;
				$variables['meetDate']		= $this->date['long'];
				$variables['meetEntryDate']	= $this->date['entry'];
				
				// Parse Info template
				$info = '';
				$this->parent->parent->debug($this::name_space.': Parsing info template...');
				if($this->location!==false){
					$variables['meetLocationID']	= $this->location->ID;
					$variables['meetLocationName']	= $this->location->name;
					$variables['meetLocationCity']	= $this->location->address['city'];
					if($text = $this->parseTemplate($variables, dirname(__FILE__).'/templates/full_comp-info.htm')){
						$info = $text;
					}
				}else{
					if($text = $this->parseTemplate($variables, dirname(__FILE__).'/templates/full_comp-info_noLoc.htm')){
						$info = $text;
					}
				}
				
				
				// Parse Notes template
				$notes = '';
				if($this->show['notes']==true){
					$this->parent->parent->debug($this::name_space.': Parsing notes template...');
					foreach($this->notes as $type=>$content){
						if($content!=''){
							$variables['noteType']		= ucfirst($type);
							$variables['noteContent']	= $content;
							if($text = $this->parseTemplate($variables, dirname(__FILE__).'/templates/full_comp-note.htm')){
								$notes.= $text;
							}
						}
					}
					unset($variables['noteType'],$variables['noteContent']);
				}
				
				$docs = '';
				// Parse results service template
				$res = '';
				if($this->res['enable']==true && $this->res['text']!=''){
					$this->parent->parent->debug($this::name_space.': Parsing results service template...');
					$variables['resServText']	= $this->res['text'];
					$variables['resServMeet']	= $this->res['meet'];
					$variables['resServSeries']	= $this->res['series'];
					$variables['resServer']		= $this->parent->parent->config->getOption('comp_resServer');
					if($text = $this->parseTemplate($variables, dirname(__FILE__).'/templates/full_comp-resServ.htm')){
						$res = $text;
					}
				}
				
				// Parse schedule template
				$schedule = $sessions = '';
				if($this->show['schedule']==true){
					$defSessionTable = new Table($this->parent, 'schedule');
					$defSessionTable->setIndent(16);
					$defSessionTable->addClass('table-striped');
					$defSessionTable->addClass('table-bordered');
					$defSessionTable->addClass('table-condensed');
					$defSessionTable->addClass('table-hover');
					$defSessionTable->sort(true);
					$defSessionTable->pager(false);
					$defSessionTable->addHeader(array(
						Table::addTHeadCell('Event #'),
						Table::addTHeadCell('Gender'),
						Table::addTHeadCell('Age Group'),
						Table::addTHeadCell('Distance'),
						Table::addTHeadCell('Stroke'),
						Table::addTHeadCell('Round')
					));
					$this->parent->parent->debug($this::name_space.': Parsing event schedule...');
					foreach($this->data['S'] as $sNum=>$session){
						$sessionTable = clone $defSessionTable;
						$sessionTable->setID('m_'.$this->ID.'_sch_s_'.$sNum);

						// Parse session times template
						$sessionTimes = $this->parent->getPlugin('table', array('m_'.$this->ID.'_time_s_'.$sNum));
						$sessionTimes->setIndent(16);
						$sessionTimes->addClass('table-condensed');
						$this->parent->parent->debug($this::name_space.': Parsing session times...');
						$cols = array();
						foreach($session['t'] as $type=>$time){
							$type	= $this->options['times'][$type];
							$cols[] = Table::addCell('<b>'.$type.'</b>');
							$cols[] = Table::addCell($time);
						}
						$sessionTimes->addRow($cols);
						$sessionTimes->build();
						$sessionTimes = $sessionTimes->getTable();
						unset($variables['type'],$variables['time'], $text);
						
						// Parse event template
						$this->parent->parent->debug($this::name_space.': Parsing events template...');
						foreach($session['E'] as $event){
							$eventNumber	= ($event['prefix']==1)?$sNum.$event['num']:$event['num'];
							$event			= $this->parseEvent($event, true);
							$sessionTable->addRow(array(
								Table::addCell($eventNumber),
								Table::addCell($event['gender']),
								Table::addCell($event['age']),
								Table::addCell($event['distance']),
								Table::addCell($event['stroke']),
								Table::addCell($event['round'])
							));
						}
						
						$sessionTable->build();
						// Parse session template
						$variables['sessionNumber'] = $session['number'];
						$variables['sessionDate'] = $session['date'];
						$variables['sessionTimes'] = $sessionTimes;
						$variables['events'] = $sessionTable->getTable();
						if($text = $this->parseTemplate($variables, dirname(__FILE__).'/templates/full_comp-session.htm')){
							$sessions.= $text;
						}
						
					} // End Session foreach
					unset($variables['sessionNumber'],$variables['sessionDate'],$variables['sessionTimes'],$sessionTable);
					
					$this->parent->parent->debug($this::name_space.': Parsing schedule template...');
					$variables['sessions']	= $sessions;
					if($text = $this->parseTemplate($variables, dirname(__FILE__).'/templates/full_comp-schedule.htm')){
						$schedule = $text;
					}
				}
				
				$accordion = $this->parent->getPlugin('accordion');
				$accordion->setID('acc_'.$this->ID);
				$accordion->addPage('_info', 'Information', $info);
				if($res!='')		$accordion->addPage('_results',		'Results Service',	$res);
				if($notes!='')		$accordion->addPage('_notes',		'Notes',			$notes);
				if($docs!='')		$accordion->addPage('_documents',	'Documents',		$docs);
				if($schedule!='')	$accordion->addPage('_schedule',	'Schedule',			$schedule);
				$accordion->setOpen(0);
				$accordion->create();
				
				$variables['meet']		= $accordion->getAccordion();
				if($text = $this->parseTemplate($variables, dirname(__FILE__).'/templates/full_comp.htm')){
					return $text;
				}
				
				break;
		}
	}
	
}
?>