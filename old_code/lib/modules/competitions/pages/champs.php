<?php
$page->createTitle();
print $page->getHeader();
print("<div class=\"row pane\">\n");
print("  <div class=\"col-xs-12\">\n");

$MIDs = strgetcsv($page->getData());
if($MIDs!==false){
	include_once("lib/modules/meet.php");
	$meet = new Meet($mySQL);
	$accordion = new Accordion();
	foreach($MIDs as $meetID){
		$meet->setID($meetID);
		$meet->createMeet();
		if($meet->isEnabled()){
			for($s=1;$s<=$meet->getNumberSessions();$s++){
				$session = $meet->getSession($s);
				if($session!==false){
					if($meet->getNotes('e')!=""){$content = "Entries: ".$meet->getNotes("e")."<br />\n";}
					$content.= "Warm Up: ".$meet->getTimes($s,"warm")."<br />\n";
					$content.= "Sign In Closes: ".$meet->getTimes($s,"sign")."<br />\n";
					$content.= "Start: ".$meet->getTimes($s,"start")."<br />\n";
					if($meet->getDispEvts()===true){
						$content.= "<ul>\n";
						foreach($session['E'] as $evtNum=>$event){
							$content.= "<li>".$meet->eventTitle($event)."</li>\n";
						}
						$content.= "</ul>\n";
					}
					$accordion->addPage($meetID.$session["$s"]['SID'],$meet->getTitle()." - ".$meet->getDate(),"");
					$accordion->changeContent($meetID.$session["$s"]['SID'],$content);
				}
			}
		}
		$meet->clear(true);
	}
	$accordion->setOpen(0);
	$accordion->create();
	print $accordion->getAccordion();
}else{
	print ("<h4>The information for BWSC Club Championships are currently unavailable.</h4>\n");
}

print("  </div>\n");
print("</div>\n");
?>