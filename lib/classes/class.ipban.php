<?php

/**
 * IP Blocker
 *
 * @category   WebApp.IpBlock
 * @package    class.ipban.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 * 
 */

class IpBan extends Base
{

	const name_space = 'WebApp.IpBan';
	const version = '1.0.0';

	function check()
	{
		if ($this->parent->config->config['core']['database'] === false) {
			return true;
		}
		$check_query = $this->mySQL_r->prepare("SELECT `id` FROM `core_ip` WHERE `ip`=INET_ATON(?) AND NOW()<DATE_ADD(`time`, INTERVAL `length` DAY)");
		
		$ip = Server::get('Remote_Addr');
		$check_query->bind_param('s', $ip);
		$check_query->execute();
		$check_query->store_result();
		
		return ($check_query->num_rows == 0);
	}
	
	function ban($reason, $length=-1, $ip=''){
		
		if($ip == '') $ip = Server::get('Remote_Addr');
		if($length == -1) $length = 36526;
		if($this->parent->user->is_loggedIn()){
			$user_id = $this->parent->user->getUserID();
		}else{
			$user_id = -1;
		}
		
		$ban_query = $this->mySQL_w->prepare(
"INSERT INTO `core_ip` (`time`, `user_id`, `ip`, `length`, `reason`) VALUES (NOW(), ?, INET_ATON(?), ?, ?)
ON DUPLICATE KEY UPDATE
	`length`=(`length`+VALUES(`length`)),
	`reason`=CONCAT(`reason`, '. Ban extended by ', VALUES(`length`), ' days for reason ', VALUES(`reason`))
");
		$ban_query->bind_param('isis', $user_id, $ip, $length, $reason);
		$ban_query->execute();
		$ban_query->store_result();
		if($ban_query->affected_rows == 1){
			$this->parent->logEvent($this::name_space, 'Blocked '.$ip.' for '.$length.' days because "'.$reason.'"');
			return true;
		}else{
			$this->parent->logEvent($this::name_space, 'Failed to block '.$ip.' for "'.$reason.'"');
			return false;
		}
	}
}

?>