<?php

/**
 * Event Logger
 *
 *
 * @category   Core.EventLog
 * @package    event.log.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 * 
 */

class Logger extends Base
{

	const name_space = 'WebApp.Logger';
	const version = '1.0.0';

	function logEvent($ns, $event)
	{
		$this->parent->debug($this::name_space . ': Logging event to event log...');
		$userID = $this->parent->user->getUserID();
		$user_ip = Server::get('Remote_Addr');
		if($user_ip === NULL) $user_ip = '127.0.0.1';
		$uri = Server::get('Request_URI');
		if($uri === NULL) $uri = '&lt;&lt;CLI&gt;&gt;';
		$event_log = $this->mySQL_w->prepare("INSERT INTO `core_log` (`user_id`,`user_ip`,`uri`,`namespace`,`event`) VALUES(?,INET_ATON(?),?,?,?)");
		$event_log->bind_param('issss', $userID, $user_ip, $uri, $ns, $event);
		$event_log->execute();
		$event_log->store_result();
		if ($event_log->affected_rows == 1) {
			return true;
		} else {
			$this->parent->debug($this::name_space.': '.$this->mySQL_w->error);
			return false;

		}
		$event_log->free_result();
	}
}

?>