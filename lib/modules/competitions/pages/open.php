<?php
$page->createTitle();
print $page->getHeader();
print("<div class=\"row pane\">\n");
print("  <div class=\"col-xs-12\">\n");

$MIDs = strgetcsv($page->getData());
if($MIDs!==false){
	include_once("lib/modules/meet.php");
	include_once("lib/plugins/accordion.php");
	$meet = new Meet($mySQL,$page->getPageNumber());
	$accordion = new Accordion();
	foreach($MIDs as $meetID){
		$meet->setID($meetID);
		$meet->createMeet();
		$sessions = $meet->getMeet();
		for($s=1;$s<=$meet->getNumberSessions();$s++){
			$content = "Warm Up: ".$meet->getTimes($s,"warm")."<br />\n";
			$content.= "Sign In Closes: ".$meet->getTimes($s,"sign")."<br />\n";
			$content.= "Start: ".$meet->getTimes($s,"start")."<br />\n";
			if($meet->getDispEvts()===true){
				$content.= "<ul>\n";
				foreach($sessions["S$s"]["E"] as $event){
					if($event['n']!=0){
						$content.= "<li>".$meet->eventTitleGED($event)."</li>\n";
					}else{
						$content.= "<li>".$meet->eventTitle($event)."</li>\n";
					}
				}
				$content.= "</ul>\n";
			}
			$accordion->addPage($meetID.$sessions["S$s"]['SID'],$meet->getTitle(),"");
			$accordion->changeContent($meetID.$sessions["S$s"]['SID'],$content);
		}
		$meet->clear(true);
	}
	$accordion->setOpen(0);
	$accordion->create();

	print $accordion->getAccordion();
}else{
	print ("<h4>The information for the BWSC Open Meet is currently unavailable.</h4>\n");
}
print("  </div>\n");
print("</div>\n");
?>