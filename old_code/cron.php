#!/usr/bin/env php
<?php

/**
 * Cron Scheduler for BWSC Website
 *
 *
 * @category   WebApp.Cron
 * @package    cron.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
* lib/_init.php
*/

$pass = '27d3df44dc381f5d57010f72cfe1595a1ac5bc42';
require 'lib/_init.php';
$webapp = new WebApp;
$webapp->run();
$webapp->login('system', $pass);
if($webapp->is_loggedIn()){
	$webapp->cron->loadActiveJobs();
	foreach($webapp->cron->getActiveJobs() as $jobID=>$job){
		echo "Running cron job $jobID...\n";
		if($job['user'] != -1){
			$webapp->changeUser('', '', $job['user']);
		}
		$result = $webapp->cron->runJob($jobID);
		if($result->status){
			echo "Job ran successfully!\n";
		}else{
			echo "Job failed to run:!\n";
			echo "  Err: ".$result->msg."\n";
		}
		$webapp->changeUser('system', -1, $pass);
	}
}else{
	echo "Failed to run cron jobs, could not login!\n";
}
?>
