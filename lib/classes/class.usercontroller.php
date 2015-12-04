<?php

/**
 * User Controller Class
 *
 * @category   WebApp.User
 * @package    user.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 * 
 */

class UserController extends Base
{
	const name_space = 'WebApp.User';
	const version = '1.0.0';

	public $session;
	
	// User Data
	private	$userID		= 0;
	private	$f_name		= 'Anonymous';
	private	$s_name		= '';
	private	$username	= 'anon';
	private	$email		= '';
	private	$pages		= array();
	private	$enabled	= true;
	private	$p_group	= 0;
	private	$groups		= array(0);
	private	$s_groups	= array();
	private	$loggedIn	= false;
	private	$changePwd	= false;


	function __construct($parent)
	{

		$this->parent = $parent;
		$this->mySQL_r = $parent->mySQL_r;
		$this->mySQL_w = $parent->mySQL_w;

		$this->parent->debug('***** ' . $this::name_space . ' *****');
		$this->parent->debug($this::name_space . ': Version ' . $this::version);
		
		$this->session = new SessionTokeniser($this);
			
		// Is a user logged in?
		if (Session::get($this::name_space, 'loggedIn') !== true) {
			$this->parent->debug($this::name_space . ': No user logged in, using anoymous');
			$this->_fetchDetails();
			return;
		}

		if($this->session->check()){
			$this->parent->debug($this::name_space . ': User logged in');
			$this->loggedIn = true;
			$this->username = Session::get($this::name_space, 'username');
			$this->userID = Session::get($this::name_space, 'userID');
			$this->session->update();
			}else{
			Session::del($this::name_space, 'loggedIn');
			Session::del($this::name_space, 'username');
			Session::del($this::name_space, 'userID');
		}
		
		// Create user data
		$this->_fetchDetails();
		if ($this->enabled == false) {
			$this->parent->debug($this::name_space . ': User disabled... logging out');
			$this->logout();
			header("Location: /user/login");
			exit();
		} elseif (Server::get('request_uri') != "/user/profile/password" && $this->changePwd == 1) {
			$this->parent->debug($this::name_space . ': User must change password');
			WebApp::forceRedirect('/user/profile/password');
		}
	}

	private function _fetchDetails()
	{
		if(!$this->parent->config->config['core']['database']){
			return;
		}
		$this->parent->debug($this::name_space . ': Fetching user data for UID: ' . $this->userID);
		
		$user_query = $this->mySQL_r->prepare("SELECT `id`, `f_name`, `s_name`, `email`, `p_group`, `en`, `chgPwd` FROM `core_users` WHERE `username`=? OR `id`<=>?");
		$sgroup_query = $this->mySQL_r->prepare("SELECT `group` FROM `core_sgroup` WHERE `user`=?");
		
		if($user_query === false){
			$this->debug($this::name_space.': User query failed');
			break;
		}
		if($sgroup_query === false){
			$this->debug($this::name_space.': 2nd group query failed');
			break;
		}
		
		$user_query->bind_param('si', $this->username, $this->userID);
		$sgroup_query->bind_param('i', $this->userID);
		
		$user_query->bind_result($user_id, $f_name, $s_name, $email, $p_group, $enabled, $chgPwd);
		$sgroup_query->bind_result($sgroup);
		
		$user_query->execute();
		$user_query->store_result();
		

		while ($user_query->fetch()) {
			$this->userID		= $user_id;
			$this->f_name		= $f_name;
			$this->s_name		= $s_name;
			$this->email		= $email;
			$this->p_group		= $p_group;
			$this->changePwd	= intval($chgPwd);
			
			$sgroup_query->execute();
			$sgroup_query->store_result();
			$sgroups = array();
			while($sgroup_query->fetch()){
				$sgroups[] = $sgroup;
			}
			$sgroup_query->free_result();
			$this->s_groups		= $sgroups;
			
			if(intval($enabled)==0){
				$this->enabled = false;
				break;
			}

			$groups = array_merge(array($p_group), $this->s_groups);
			array_values($groups);
			
			$this->groups = $groups;
			
			$group_query = $this->mySQL_r->prepare("SELECT `en` FROM `core_groups` WHERE `GID`=?");
			if($group_query === false){
				$this->debug($this::name_space.': Group query failed');
				break;
			}
			$group_query->bind_param('i', $groupid);
			$group_query->bind_result($en);
			
			// Loop through them all and get the enabled state
			foreach($groups as $groupid) {
				$group_query->execute();
				$group_query->fetch();
				$group_query->store_result();
				
				$enables['g' . $groupid] = intval($en);
				
				// If this group is disabled, we can quit whilst we're
				// ahead because we don't need to check other groups
				if(intval($en)==0){
					$this->enabled = false;
					break 2;
				}
			}
			$group_query->free_result();
			$this->enabled = true;
		}
		$user_query->free_result();
		$this->pages = $this->_calcPageAccess();
	}
	
	function cliLogon($user, $pass){
		if($this->authenticate($pass, -1, $user)){
			$this->username = $user;
			$this->userID = NULL;
			$this->loggedIn = true;
			$this->_fetchDetails();
			if(!$this->inGroup(3)){
				return $this->cliLogout();
			}
			if($this->enabled){
				$this->parent->logEvent($this::name_space, $user.' logged in via CLI!');
				return true;
			}
			return $this->cliLogout();
		}else{
			$this->parent->logEvent($this::name_space, $user.' failed to log in via CLI!');
			return false;
		}
	}
	
	private function _calcpageAccess()
	{
		$groups = $this->groups;
		$pages = array();
		$group_query = $this->mySQL_r->prepare("SELECT `PID` FROM `core_gpage` WHERE `GID`=?");
		if($group_query === false){
			$this->parent->debug($this::name_space.': Failed to fetch pages to calculate page access from db');
			return $pages;
		}
		$group_query->bind_result($page);
		foreach($groups as $group) {
			$group_query->bind_param('i', $group);
			$group_query->execute();
			$group_query->store_result();

			while ($group_query->fetch()) {
				$pages[] = $page;
			}
		}
		$group_query->free_result();

		$pages = array_unique($pages);
		return $pages;
	}
	
	function cliLogout(){
		$this->username = 'anon';
		$this->userID = 0;
		$this->loggedIn = false;
		$this->_fetchDetails();
	}

	function cliChangeUser($user, $pass='', $id = NULL){
		$requirePass = true;
		if($this->inGroup(1)) $requirePass = false;
		if($this->authenticate($pass, $id, $user) || !$requirePass){
			$this->username = $user;
			$this->userID = NULL;
			$this->loggedIn = true;
			$this->_fetchDetails();
			if($this->enabled){
				$this->parent->logEvent($this::name_space, $user.' logged into CLI');
				return true;
			}
			$this->cliLogout();
			return 'Account not enabled, logging out';
		}else{
			return false;
		}
	}
	
	public function authenticate($pass, $id = '', $user = '')
	{
		if ($user == '') {
			$user = $this->username;
		}
		if ($id == '') {
			$id = $this->userID;
		}
		if ($id == -1) {
			$id = null;
		}
		
		$user_query = $this->mySQL_r->prepare("SELECT `pass` FROM `core_users` WHERE `id`<=>? OR `username`=?");
		$user_query->bind_param('is', $id, $user);
		$user_query->execute();
		$user_query->bind_result($pwd);
		$user_query->store_result();
		
		while ($user_query->fetch()) {
			$dbpass = explode(":", $pwd);
			$salt = $dbpass[1];
			$dbpass = $dbpass[0];
		}
		if ($user_query->num_rows == 1) {
			return ($this->pwd_hash($pass, $salt) == $dbpass);
		}
		return false;
	}

	public function logout(){
		$this->session->destroy();
		Session::del($this::name_space, 'loggedIn');
		Session::del($this::name_space, 'username');
		Session::del($this::name_space, 'userID');
		$this->parent->addHeader('location', '/');
		if(WebApp::get('r')!==NULL){
			return new ActionResult($this, urldecode(WebApp::get('r')), 1, '');
		}
		return new ActionResult($this, '/', 1, '');
	}
	
	public function is_loggedIn(){
		return $this->loggedIn;
	}
	public function get_firstName()
	{
		return $this->f_name;
	}
	public function get_surname()
	{
		return $this->s_name;
	}
	public function get_fullName()
	{
		$fullname = '';
		if($this->f_name !== '') $fullname.= $this->f_name;
		if($this->s_name !== '') $fullname.= $this->s_name;
		return $fullname;
	}
	public function get_username()
	{
		return $this->username;
	}
	public function get_email()
	{
		return $this->email;
	}
	public function get_userID()
	{
		return $this->userID;
	}
	public function get_group()
	{
		return $this->p_group;
	}
	public function get_sGroups()
	{
		return $this->s_groups;
	}
	public function is_inGroup($groupID, $superadmin=true)
	{
		if($superadmin && $this->p_group == 1){
			return true;
		}
		if(is_array($groupID)){
			$result = array();
			foreach($groupID as $GID){
				$result[] = in_array($GID, $this->groups);
			}
			return in_array(true, $result);
		}else{
			return in_array($groupID, $this->groups);
		}
	}
	public function can_accessPage($pageID)
	{
		if ($this->p_group == 1 || in_array($pageID, $this->pages)) {
			return true;
		} else {
			return false;
		}
	}
	
	// Deprecated forms
	public function getFirstName()
	{
		$this->parent->debug($this::name_space.': getFirstName() is deprecated, use get_firstName() instead', 1);
		return $this->get_firstName();
	}
	public function getSurname()
	{
		$this->parent->debug($this::name_space.': getSurame() is deprecated, use get_surname() instead', 1);
		return $this->get_surname();
	}
	public function getFullName()
	{
		$this->parent->debug($this::name_space.': getFullName() is deprecated, use get_fullName() instead', 1);
		return $this->get_fullName();
	}
	public function getUserID()
	{
		$this->parent->debug($this::name_space.': getUserID() is deprecated, use get_userID() instead', 1);
		return $this->get_userID();
	}
	public function getUsername()
	{
		$this->parent->debug($this::name_space.': getUsername() is deprecated, use get_Username() instead', 1);
		return $this->get_username();
	}
	public function inGroup($groupID, $superadmin=true)
	{
		$this->parent->debug($this::name_space.': inGroup is deprecated, use is_inGroup instead', 1);
		return $this->is_inGroup($groupID, $superadmin);
	}
	
	public function accessPage($pageID)
	{
		$this->parent->debug($this::name_space.': accessPage is deprecated, use can_accessPage instead', 1);
		return $this->can_accessPage($pageID);
	}

	public static function pwd_hash($password, $salt)
	{
		$hash = hash("sha512", $password . $salt);
		$hash = hash("sha512", hash("sha256", $salt) . $hash);
		return $hash;
	}

	public static function ranHash($length = 32)
	{
		return hash("sha1", mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
	}
}
?>
