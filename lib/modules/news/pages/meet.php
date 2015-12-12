<?php
$comp_id = WebApp::get('cat_3');
if($comp_id!=''){
$meet_query = $mySQL_r->prepare("SELECT `ID` FROM `comp_meet` WHERE `ID`=?");
$meet_query->bind_param('s',$comp_id);
$meet_query->execute();
$meet_query->store_result();
	if($meet_query->num_rows!=0){
		if($meet = $page->getResource('competitions')){
			if($comp = $meet->fetchCompetition($comp_id)){
				$page->setTitle($comp->title);
				$page->cat_2 = $comp->title;
				$page->cat_3 = '';
				print $comp->format();
			}
		}
	}else{
		$page->setStatus(404);
	}
}else{
	$page->setStatus(404);
}
?>