#!/usr/bin/env php
<?php

/**
 * Index file for BWSC Website
 *
 *
 * @category   WebApp.Index
 * @package    index.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
* lib/webapp.php
*/

require 'lib/_init.php';
$webapp = new WebApp;
$webapp->run();
$help = file_get_contents(__LIBDIR__.'/cli_help.txt').PHP_EOL;

function loginPrompt() {
	global $webapp;
	echo "\n===== WebApp Login =====\n";
	echo "Enter your username: ";
	$user = trim(fgets(STDIN));
	echo "Enter your Password: ";
	$pass = trim(fgets(STDIN));
	if($webapp->login($user, $pass)){
		echo "Logged in!\n";
	}else{
		echo "Invalid username/password\n";
		loginPrompt();
	}
}

loginPrompt();
echo $webapp->user->getUsername().' > ';
$quit = false;
while(!$quit){
	while(!$webapp->user->is_loggedIn()){
		loginPrompt();
	}
	$next_line = trim(fgets(STDIN));
	switch($next_line){
		case 'exit();':
		case 'exit':
			$quit = true;
			$result = '';
			exit();
			break;
		case 'methods();':
			echo "\$WebApp::\n";
			foreach(get_class_methods($webapp) as $method){
			echo "         $method()\n";
			}
			foreach(get_object_vars($webapp) as $var=>$obj){
				if(is_object($obj)&&strpos($var, 'mySQL')===false){
					echo "\$webapp->$var::\n";
					foreach(get_class_methods($webapp->{$var}) as $cmeth){
						echo "             $cmeth()\n";
					}
				}
			}
			$result = true;
			break;
		case 'help':
		case 'help();':
			$result = $help;
			break;
		case 'login();':
			loginPrompt();
			break;
		case 'logout();':
			$result = $webapp->logout();
			break;
		case 'changeUser();':
			echo "===== Change User =====\n";
			echo "Enter Username: ";
			$user = trim(fgets(STDIN));
			echo "Enter Password: ";
			$pass = trim(fgets(STDIN));
			$result = $webapp->changeUser($user, $pass);
			break;
		default:
			$result = @eval($next_line);
	}
	if($result===true){
		$result = ' TRUE';
	}elseif($result===false){
		$result = ' FALSE';
	}
	echo $result;
	if(substr($result, -1)!="\n") echo "\n";
	echo $webapp->user->getUsername().' > ';
}
?>
