<?php

/**
 * Login Tokeniser
 *
 * Draft of the cookie token system
 *
 * @category   WebApp.Core.Token
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 * 
 */

class SessionTokeniser extends Base
{
	
	const name_space = 'WebApp.User.Tokeniser';
	const version = '1.0.0';
	
	public function create($userID){
		$ip = Server::get('remote_addr');
		$sessID = Session::getID();
		$token = $this->parent->ranHash();
		
		$this->parent->parent->debug($this::name_space . ': Creating session for "'.$userID.'@'.$ip.'"...');

		$token_query = $this->mySQL_w->prepare("INSERT INTO `core_sessions` (`user`, `session`, `IP`, `token`, `created`) VALUES (?, ?, INET_ATON(?), ?, NOW())");
		$token_query->bind_param('isss', $userID, $sessID, $ip, $token);
		$token_query->execute();
		$token_query->store_result();
		if($token_query->affected_rows == 1){
			Cookie::set('ltkn', $token, 3600*24*30);
			return true;
		}else{
			return false;
		}
	}
	
	public function check(){
		
		$this->parent->parent->debug($this::name_space . ': Checking for login token...');
		
		if(!$this->parent->parent->config->config['core']['database']){
			return false;
		}
		
		// Check for token cookie
		if(Cookie::get('ltkn') === NULL){
			
			$this->parent->parent->debug($this::name_space . ': Login token not found!');
			return false;
		}
		
		$this->parent->parent->debug($this::name_space . ': Found token');
		
		$token = Cookie::get('ltkn');
		$sessID = Session::getID();
		$userID = Session::get('WebApp.User', 'userID');

		// It does exist so...
		// Find token in database where userID = the userID in the token
		$this->parent->parent->debug($this::name_space . ': Checking sessions table for:');
		$this->parent->parent->debug('T: '.$token.'/ S: '.$sessID.'/ U: '.$userID);
		$token_query = $this->mySQL_r->prepare("SELECT INET_NTOA(`IP`), `auth` FROM `core_sessions` WHERE `token`=? AND `session`=? AND `user`=?");
		$token_query->bind_param('ssi', $token, $sessID, $userID);

		$token_query->execute();
		$token_query->store_result();
		
		if($token_query->num_rows!=1){
			$this->parent->parent->debug($this::name_space . ': Failed to find session.');
			return false;
		}
		
		$token_query->bind_result($ip, $auth);
		$token_query->fetch();
		if(Server::get('remote_addr') != $ip || $auth){
			$update_query = $this->mySQL_w->prepare("UPDATE `core_sessions` SET `auth`=1 WHERE `token`=?");
			$update_query->bind_param('s',$token);
			$update_query->execute();
			WebApp::forceRedirect('/user/auth?r='.urlencode(Server::get('request_uri')));
		}
		$this->parent->parent->debug($this::name_space . ': Found session. Token Check successful!');
		return true;
	}
	
	public function update(){
		$this->parent->parent->debug($this::name_space . ': Updating session status...');
		$token = Cookie::get('ltkn');
		$ip = Server::get('remote_addr');
		$update_query = $this->mySQL_w->prepare("UPDATE `core_sessions` SET `IP`=INET_ATON(?), `lpr`=NOW() WHERE `token`=?");
		$update_query->bind_param('ss', $ip, $token);
		$update_query->execute();
	}
	
	public function unlock(){
		$this->parent->parent->debug($this::name_space.': Unlocking session...');
		$token = Cookie::get('ltkn');
		$session = Session::getID();
		$user = $this->parent->parent->user->getUserID();
		$update_query = $this->mySQL_w->prepare("UPDATE `core_sessions` SET `auth`=0, `lpr`=NOW() WHERE `user`=? AND `session`=? AND `token`=?");
		$update_query->bind_param('iss', $user, $session, $token);
		$update_query->execute();
		$check_query = $this->mySQL_w->prepare("SELECT `ID` FROM `core_sessions` WHERE `auth`=0 AND `user`=? AND `session`=? AND `token`=?");
		$check_query->bind_param('iss', $user, $session, $token);
		$check_query->execute();
		$check_query->store_result();
		return ($check_query->num_rows == 1);
	}
	
	public function destroy(){
		$this->parent->parent->debug($this::name_space . ': Destroying session...');
		$token = Cookie::get('ltkn');
		$update_query = $this->mySQL_w->prepare("DELETE FROM `core_sessions` WHERE `token`=?");
		$update_query->bind_param('s', $token);
		$update_query->execute();
		Cookie::del('ltkn');
	}

}
?>
