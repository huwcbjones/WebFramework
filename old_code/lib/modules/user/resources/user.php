<?php
/**
 * User Action Class for User
 *
 * @category   Module.User.Action.User
 * @package    user/action.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */

class UserAction extends BaseAction
{
	const name_space = 'Module.User.Action';
	const version = '1.0.0';
	
	function __construct($parent){
		$this->parent = $parent;
		$this->mySQL_r = $parent->mySQL_r;
		$this->mySQL_w = $parent->mySQL_w;
		
		require_once dirname(__FILE__) . '/email_templates.php';
		$this->parent->parent->debug('***** '.$this::name_space.' *****');
		$this->parent->parent->debug($this::name_space.': Version '.$this::version);
		
		$module_query = $this->mySQL_r->prepare("SELECT `module_id` FROM `core_modules` WHERE `namespace`=?");
		$namespace = str_replace(array('Module.','.Action'),'', $this::name_space);
		$module_query->bind_param('s', $namespace);
		$module_query->execute();
		$module_query->store_result();
		if($module_query->num_rows==1){
			$module_query->bind_result($module_id);
			while($module_query->fetch()){
				$this->MOD_ID = $module_id;
			}
			$module_query->free_result();
		}else{
			$this->parent->parent->debug($this::name_space.': Cannot find module... Is the module registered properly?');
			$this->parent->setStatus(500);
		}
	}
	
	function password($requireOldPwd=true, $userID=-1)
	{
		$user = $this->parent->parent->user;
		if($userID==-1) $userID = $user->getUserID();
		$o_pwd = (WebApp::post('o_pwd')===NULL)? '' : WebApp::post('o_pwd');
		$n_pwd = (WebApp::post('n_pwd')===NULL)? '' : WebApp::post('n_pwd');
		$c_pwd = (WebApp::post('c_pwd')===NULL)? '' : WebApp::post('c_pwd');
		
		if(
			($o_pwd == '' && $requireOldPwd)
			||$n_pwd == ''
			||$c_pwd == ''
		){
			return new ActionResult(
				$this,
				'/user/profile/password',
				0,
				'Failed to change password.<br />Error: <code>Fields must not be empty</code>',
				B_T_FAIL
			);
		}
		
		if($requireOldPwd){
			if(!$user->authenticate($o_pwd)){
				$this->parent->parent->logEvent($this::name_space, 'User failed to change password old one was incorrect');
				return new ActionResult(
					$this,
					'/user/profile/password',
					0,
					'Failed to change password.<br />Error: <code>Old password was incorrect</code>',
					B_T_FAIL
				);
			}
		}
		
		if($o_pwd === $n_pwd){
			return new ActionResult(
				$this,
				'/user/profile/password',
				0,
				'Failed to change password.<br />Error: <code>Old password was the same as the new one</code>',
				B_T_FAIL
			);
		}
		
		if($n_pwd !== $c_pwd){
			return new ActionResult(
				$this,
				'/user/profile/password',
				0,
				'Failed to change password.<br />Error: <code>New passwords do not match</code>',
				B_T_FAIL
			);
		}
		$salt = $user->ranHash();
		$password = $user->pwd_hash($n_pwd, $salt).':'.$salt;
		
		$update = $this->mySQL_w->prepare("UPDATE `core_users` SET `pass`=?, `chgPwd`=0, `pwd_reset`=`pwd_reset`+1 WHERE `id`=?");
		if($update===false){
			return new ActionResult(
				$this,
				'/user/profile/password',
				0,
				'Failed to change password.<br />Error:<code>Couldn\'t save new password</code>',
				B_T_FAIL
			);
		}
		$update->bind_param('si', $password, $userID);
		$update->execute();
		$update->store_result();
		if($update->affected_rows==1){
			$this->parent->parent->logEvent($this::name_space, 'User changed password');
			$ip = $_SERVER['REMOTE_ADDR'];
			$details = json_decode(file_get_contents('http://ipinfo.io/'.$ip.'/geo'), true);
			$location = '';
			$this->parent->parent->debug('Location: '.$details['loc']);
			if($details['loc'] != ''){
				$location = ', and in ';
				if($details['country'] != ''){
					$location = $details['country'];
					if($details['region'] != ''){
						$location = $details['region'].', '.$details['country'];
						if($details['city'] != ''){
							$location = $details['city'].', '.$details['region'].', '.$details['country'];
						}
					}
				}
			}
			
			$name = $user->getFirstName();
			$fullName = $user->getFullName();
			$email = $user->getEmail();
			$mail = new Emailer();
			$mail->Subject = 'Password Change';
			
			$mail->msgHTML(UserEmail::passwordChange($name, $ip, $location)['html']);
			$mail->AltBody = (UserEmail::passwordChange($name, $ip, $location)['text']);
			$mail->addAddress($email, $fullName);
			$mail->send();
			Session::del('UserActivation', 'firstPwd');
			return new ActionResult(
				$this,
				'/user/profile',
				1,
				'Successfully changed password!',
				B_T_SUCCESS
			);
		}else{
			return new ActionResult(
				$this,
				'/user/profile/password',
				0,
				'Failed to change password.<br />Error:<code>Unknown error</code>',
				B_T_FAIL
			);
		}
	}
	
	private function genActivation($email, $type, $return){
		$details_query = $this->mySQL_r->prepare("SELECT CONCAT(`f_name`, ' ',`s_name`), `username` FROM `core_users` WHERE `email`=?");
		if($details_query===false){
			return new ActionResult(
				$this,
				$return['f'],
				0,
				'Failed to send activation code.<br />Error: <code>Couldn\'t fetch details</code>',
				B_T_FAIL
			);
		}
		$details_query->bind_param('s', $email);
		$details_query->execute();
		$details_query->bind_result($name, $username);
		$details_query->store_result();
		if($details_query->num_rows!=1)	{
			return new ActionResult(
				$this,
				$return['f'],
				0,
				'No user found to activate',
				B_T_FAIL
			);
		}
		$details_query->fetch();
		
		$this->mySQL_w->autocommit(false);
		$code_query = $this->mySQL_w->prepare("UPDATE `core_users` SET `act_c`=?, `act_t`=FROM_UNIXTIME(?), `act_b`=0 WHERE `email`=?");
		if($code_query===false){
			return new ActionResult(
				$this,
				$return['f'],
				0,
				'Failed to send activation code.<br />Error: <code>Couldn\'t save activation details</code>',
				B_T_FAIL
			);
		}
		$code = $this->parent->parent->user->ranHash();
		$time = time();
		
		$code_query->bind_param('sis', $code, $time, $email);
		$code_query->execute();
		$code_query->store_result();
		if($code_query->affected_rows!=1){
			return new ActionResult(
				$this,
				$return['f'],
				0,
				'Failed to generate activation code.',
				B_T_FAIL
			);
		}

		$mail = new Emailer();
		if($type=='add'){
			$mail->Subject = 'Account Creation';
			$mail->msgHTML(UserEmail::accountCreation($name, $username, $email, $code)['html']);
			$mail->AltBody =UserEmail::accountCreation($name, $username, $email, $code)['text'];
		}elseif($type=='resend'){
			$mail->Subject = 'Resend Activation Email';
			$mail->msgHTML(UserEmail::resendActivation($name, $username, $email, $code)['html']);
			$mail->AltBody =UserEmail::resendActivation($name, $username, $email, $code)['text'];
		}elseif($type=='recover'){
			$mail->Subject = 'Recover Account';
			$mail->msgHTML(UserEmail::recoverAccount($name, $username, $email, $code)['html']);
			$mail->AltBody =UserEmail::recoverAccount($name, $username, $email, $code)['text'];
		}elseif($type=='email'){
			$mail->Subject = 'Change Email';
			$mail->msgHTML(UserEmail::changeEmail($name, $username, $email, $code)['html']);
			$mail->AltBody =UserEmail::changeEmail($name, $username, $email, $code)['text'];
		}
		//$mail->Subject = 'Account Activation';
		$mail->addAddress($email, $name);
	
		if($mail->send()){
			$this->mySQL_w->commit();
			$this->mySQL_w->autocommit(true);
			return new ActionResult(
				$this,
				$return['s'],
				1,
				'Generated activation code and sent email!',
				B_T_SUCCESS
			);
		}else{
			$this->mySQL_w->rollback();
			$this->mySQL_w->autocommit(true);
			return new ActionResult(
				$this,
				$return['f'],
				0,
				'Failed to send activation code.',
				B_T_FAIL
			);
		}
	}
	
	function activate(){
		$code = WebApp::post('code');
		if($code===NULL){
			return new ActionResult(
				$this,
				'/user/activate',
				0,
				'Failed to activate user. No activation code was sent',
				B_T_FAIL
			);
		}
		
		$code_query = $this->mySQL_r->prepare("SELECT `id`,`username`,CONCAT(`f_name`, ' ', `s_name`),`email` FROM `core_users` WHERE `act_c`=? AND NOW()<DATE_ADD(`act_t`,INTERVAL 7 DAY)");
		$update = $this->mySQL_w->prepare("UPDATE `core_users` SET `act_c`='',`act_t`=NULL,`act_b`='1' WHERE `act_c`=?");
		if($code_query === false){
			return new ActionResult(
				$this,
				'/user/activate',
				0,
				'Failed activate user!<br />Error: <code>Failed to check activation code</code>',
				B_T_FAIL
			);
		}
		if($update === false){
			return new ActionResult(
				$this,
				'/user/activate',
				0,
				'Failed activate user!<br />Error: <code>Failed save activation</code>',
				B_T_FAIL
			);
		}
		$code_query->bind_param('s', $code);
		$code_query->bind_result($id, $username, $name, $email);
		
		$code_query->execute();
		$code_query->store_result();
		
		if($code_query->num_rows!=1){
			$this->parent->parent->logEvent($this::name_space, $username.' tried to activate their account using a wrong code');
			return new ActionResult(
				$this,
				'/user/activate',
				0,
				'Failed to activate user. Code is incorrect/has expired',
				B_T_FAIL
			);
		}
		
		$code_query->fetch();
		$update->bind_param('s', $code);
		$update->execute();
		$update->store_result();
		if ($update->affected_rows != 1) {
			return new ActionResult(
				$this,
				'/user/activate',
				0,
				'Failed to activate user. Code is incorrect/has expired',
				B_T_FAIL
			);
		}
		$this->parent->parent->logEvent($this::name_space, $username.' activated their account and has logged in');
		$this->password(false, $id);
		Session::set('WebApp.User', 'loggedIn', true);
		Session::set('WebApp.User', 'username', $username);
		Session::set('WebApp.User', 'userID', $id);
		$this->parent->parent->user->session->create($id);
		return new ActionResult(
			$this,
			'/user',
			1,
			'Account was successfully activated',
			B_T_SUCCESS
		);
	}
	
	function recover(){
		$username	= (WebApp::post('username')===NULL)?	''	:WebApp::post('username');
		$email		= (WebApp::post('email')===NULL)?		''	:WebApp::post('email');
		
		$select_query = $this->mySQL_r->prepare("SELECT `email`, `username` FROM `core_users` WHERE (`username`=? OR `email`=?) AND `act_b`=1");
		if($select_query === false){
			return new ActionResult(
				$this,
				'/user/recover',
				0,
				'Failed send activation email!<br />Error: <code>Failed to check username/email</code>',
				B_T_FAIL
			);
		}
		$select_query->bind_param('ss', $username, $email);
		$select_query->execute();
		$select_query->store_result();
		if($select_query->num_rows!=1){
			return new ActionResult(
				$this,
				'/user/recover',
				0,
				'Failed send activation email!<br />Error: <code>Couldn\'t find user, or user is not activated</code>',
				B_T_FAIL
			);
		}
		$select_query->bind_result($email, $username);
		$select_query->fetch();
		
		return $this->genActivation($email, 'recover', array('f'=>'/user/recover', 's'=>'/user/activate'));
	}
	function add(){
		$f_name		= (WebApp::post('f_name')===NULL)?		''	:WebApp::post('f_name');
		$s_name		= (WebApp::post('s_name')===NULL)?		''	:WebApp::post('s_name');
		$username	= (WebApp::post('username')===NULL)?	''	:WebApp::post('username');
		$email		= (WebApp::post('email')===NULL)?		''	:WebApp::post('email');
		$p_group	= (WebApp::post('p_group')===NULL)?		''	:WebApp::post('p_group');
		
		if($f_name == '')	return new ActionResult($this, '/admin/user/user_add', 0, 'Failed to add user.<br />Error: <code>First Name must not be blank</code>', B_T_FAIL);
		if($s_name == '')	return new ActionResult($this, '/admin/user/user_add', 0, 'Failed to add user.<br />Error: <code>Surname must not be blank</code>', B_T_FAIL);
		if($username == '')	return new ActionResult($this, '/admin/user/user_add', 0, 'Failed to add user.<br />Error: <code>Username must not be blank</code>', B_T_FAIL);
		if($email == '')	return new ActionResult($this, '/admin/user/user_add', 0, 'Failed to add user.<br />Error: <code>Email must not be blank</code>', B_T_FAIL);
		if($p_group == '')	return new ActionResult($this, '/admin/user/user_add', 0, 'Failed to add user.<br />Error: <code>Primary Group must not be blank</code>', B_T_FAIL);
		
		$user_query = $this->mySQL_r->prepare("SELECT `username`,`email` FROM `core_users` WHERE `username`=? OR `email`=?");
		if($user_query === false){
			return new ActionResult(
				$this,
				'/admin/user/user_add',
				0,
				'Failed add user!<br />Error: <code>Add query failed</code>',
				B_T_FAIL
			);
		}
		$user_query->bind_param('ss', $username, $email);
		$user_query->execute();
		$user_query->store_result();
		if ($user_query->num_rows != 0) {
			return new ActionResult(
				$this,
				'/admin/user/user_add',
				0,
				'Failed to add user.<br />Error: <code>User with that username/email already exists</code>',
				B_T_FAIL
			);
		}
		$user_add = $this->mySQL_w->prepare("INSERT INTO `core_users` (`f_name`,`s_name`,`username`,`email`,`p_group`, `pass`) VALUES(?,?,?,?,?,?)");
		if ($user_add == false) {
			return new ActionResult(
				$this,
				'/admin/user/user_add',
				0,
				'Failed to add user.<br />Error: <code>Query failed</code>',
				B_T_FAIL
			);
		}
		$user = $this->parent->parent->user;
		$time = microtime(true);
		$pass = $user->pwd_hash($time, $user->ranHash()).':'.$user->ranHash();
		$user_add->bind_param('ssssis', $f_name, $s_name, $username, $email, $p_group, $pass);
		$user_add->execute();
		$user_add->store_result();
		if ($user_add->affected_rows == 1) {
			$this->parent->parent->logEvent($this::name_space, 'Added new user "'.$username.'"');
			$activationEmail = $this->genActivation($email, 'add', array('f'=>'/admin/user/user_add', 's'=>'/admin/user/user_view'));
			Session::del('status_msg', $activationEmail->id);
			if($activationEmail->status==1){
				return new ActionResult(
					$this,
					'/admin/user/user_view',
					1,
					'Successfully added user!',
					B_T_SUCCESS
				);
			}else{
				return new ActionResult(
					$this,
					'/admin/user/user_view',
					1,
					'Successfully added user, but could not generate activation details!',
					B_T_WARNING
				);
			}
		}else{
			return new ActionResult(
				$this,
				'/admin/user/user_add',
				0,
				'Failed to add user.<br />Error: <code>'.$this->mySQL_w->error.'</code>',
				B_T_FAIL
			);
		}
	}
	
	function edit(){
		$id			= (WebApp::post('id')===NULL)?		''		:intval(WebApp::post('id'));
		$this->parent->parent->debug($id);
		if(!is_int($id)){
			return new ActionResult(
				$this,
				'/admin/user/user_edit',
				0,
				'Failed to edit user.<br />Error: <code>User ID must be an integer</code>',
				B_T_FAIL
			);
		}
		if($id == $this->parent->parent->user->getUserID() && !$this->parent->inGroup(1)){
			$this->parent->parent->logEvent($this::name_space, 'Attempted to edit themself');
			return new ActionResult(
				$this,
				'/admin/user/user_edit',
				0,
				'Failed to edit user.<br />Error: <code>You cannot edit yourself</code>',
				B_T_FAIL
			);
		}
		
		$f_name		= (WebApp::post('f_name')===NULL)?		''		:WebApp::post('f_name');
		$s_name		= (WebApp::post('s_name')===NULL)?		''		:WebApp::post('s_name');
		$username	= (WebApp::post('username')===NULL)?	''		:WebApp::post('username');
		$email		= (WebApp::post('email')===NULL)?		''		:WebApp::post('email');
		$n_pwd		= (WebApp::post('n_pwd')===NULL)?		''		:WebApp::post('n_pwd');
		$n_pwd_c	= (WebApp::post('c_pwd')===NULL)?		''		:WebApp::post('c_pwd');
		$chgPwd		= (WebApp::post('chgPwd')===NULL)?		''		:WebApp::post('chgPwd');
		$enabled	= (WebApp::post('enabled')===NULL)?		false	:WebApp::post('enabled');
		$p_group	= (WebApp::post('p_group')===NULL)?		3		:WebApp::post('p_group');
		$s_groups	= (WebApp::post('s_group')===NULL)?		array()	:strgetcsv(WebApp::post('s_group'));
		
		if($f_name == '')	return new ActionResult($this, '/admin/user/user_edit', 0, 'Failed to add user.<br />Error: <code>First Name must not be blank</code>', B_T_FAIL);
		if($s_name == '')	return new ActionResult($this, '/admin/user/user_edit', 0, 'Failed to add user.<br />Error: <code>Surname must not be blank</code>', B_T_FAIL);
		if($username == '')	return new ActionResult($this, '/admin/user/user_edit', 0, 'Failed to add user.<br />Error: <code>Username must not be blank</code>', B_T_FAIL);
		if($email == '')	return new ActionResult($this, '/admin/user/user_edit', 0, 'Failed to add user.<br />Error: <code>Email must not be blank</code>', B_T_FAIL);
		if($chgPwd == '')	return new ActionResult($this, '/admin/user/user_edit', 0, 'Failed to add user.<br />Error: <code>Change Password must not be blank</code>', B_T_FAIL);
		if($enabled == '')	return new ActionResult($this, '/admin/user/user_edit', 0, 'Failed to add user.<br />Error: <code>Enabled must not be blank</code>', B_T_FAIL);
		if($p_group == '')	return new ActionResult($this, '/admin/user/user_edit', 0, 'Failed to add user.<br />Error: <code>Primary Group must not be blank</code>', B_T_FAIL);
		
		if($this->parent->inGroup(2, false) && $p_group == 1){
			$this->parent->parent->logEvent($this::name_space, 'Tried to make "'.$username.'" a Super Admin');
			return new ActionResult(
				$this,
				'/admin/user/user_edit',
				0,
				'Failed to edit user.<br />Error: <code>You cannot escalate privileges</code>',
				B_T_FAIL
			);
		}
		
		if($this->parent->parent->user->getUserID() == $id && $enabled == false){
			$this->parent->parent->logEvent($this::name_space, 'Tried to disable themself');
			return new ActionResult(
				$this,
				'/admin/user/user_edit',
				0,
				'Failed to edit user.<br />Error: <code>You cannot disable yourself</code>',
				B_T_FAIL
			);
		}
		
		if($n_pwd != $n_pwd_c){
			return new ActionResult(
				$this,
				'/admin/user/user_edit',
				0,
				'Failed to edit user.<br />Error: <code>New passwords must match, or both be empty</code>',
				B_T_FAIL
			);
		}
		
		$clear_sgroup = $this->mySQL_w->prepare("DELETE FROM `core_sgroup` WHERE `user`=?");
		$update_sgroup = $this->mySQL_w->prepare("INSERT INTO `core_sgroup` (`user`, `group`) VALUES (?, ?)");
		if($clear_sgroup === false){
			return new ActionResult(
				$this,
				'/admin/user/user_edit',
				0,
				'Failed edit user!<br />Error: <code>Clear query failed</code>',
				B_T_FAIL
			);
		}
		if($update_sgroup === false){
			return new ActionResult(
				$this,
				'/admin/user/user_edit',
				0,
				'Failed edit user!<br />Error: <code>Update sgroup query failed</code>',
				B_T_FAIL
			);
		}
		if ($n_pwd != '') {
			$userCtrl = $this->parent->parent->user;
			$hash = $userCtrl->ranHash();
			$new_pwd = $userCtrl->pwd_hash($n_pwd, $hash) . ':' . $hash;
			$update = $this->mySQL_w->prepare("UPDATE `core_users` SET `f_name`=?,`s_name`=?,`email`=?,`en`=?,`chgPwd`=?,`p_group`=?,`pass`=?, `pwd_reset`=`pwd_reset`+1 WHERE `id`=? AND `username`=?");
			if($update === false){
				return new ActionResult(
					$this,
					'/admin/user/user_edit',
					0,
					'Failed edit user!<br />Error: <code>Update query failed</code>',
					B_T_FAIL
				);
			}
			$update->bind_param('sssiiisis', $f_name, $s_name, $email, $enabled, $chgPwd, $p_group, $new_pwd, $id, $username);
		} else {
			$update = $this->mySQL_w->prepare("UPDATE `core_users` SET `f_name`=?,`s_name`=?,`email`=?,`en`=?,`chgPwd`=?,`p_group`=? WHERE `id`=? AND `username`=?");
			if($update === false){
				return new ActionResult(
					$this,
					'/admin/user/user_edit',
					0,
					'Failed edit user!<br />Error: <code>Update query failed</code>',
					B_T_FAIL
				);
			}
			$update->bind_param('sssiiiis', $f_name, $s_name, $email, $enabled, $chgPwd, $p_group, $id, $username);
		}
		$clear_sgroup->bind_param('i', $id);
		$update_sgroup->bind_param('ii', $id, $sgroup);
		$clear_sgroup->execute();
		if(count($s_groups)!=0){
			foreach($s_groups as $sgroup){
				$this->parent->parent->debug($sgroup);
				$update_sgroup->bind_param('ii', $id, $sgroup);
				$update_sgroup->execute();
			}
		}
		if($n_pwd != ''){
			$mail = new Emailer();
			
			$mail->Subject = 'Password Changed';
			$mail->msgHTML(UserEmail::adminPasswordChange($f_name)['html']);
			$mail->AltBody =UserEmail::adminPasswordChange($f_name)['text'];
			$mail->addAddress($email, $f_name.' '.$s_name);
			$mail->send();
		}
		$update->execute();
		$update->store_result();
		
		$this->parent->parent->logEvent($this::name_space, 'Edited user "'.$username.'"');
		return new ActionResult(
			$this,
			'/admin/user/user_view',
			1,
			'User was edited.',
			B_T_SUCCESS,
			array(
				'form'=>array(
					'n_pwd'=>'',
					'c_pwd'=>''
				)
			)
		);
	}
	
	function del()
	{
		$users	= (WebApp::post('users')===NULL)?	array()	:strgetcsv(WebApp::post('users'));
		$pass	= (WebApp::post('pwd')===NULL)?		''		:WebApp::post('pwd');
		
		if(count($users)==0){
			return new ActionResult(
				$this,
				'/admin/user/user_view',
				0,
				'No user(s) were selected!',
				B_T_FAIL,
				array('form'=>array('pwd'=>''))
			);
		}
		if (!$this->parent->parent->user->authenticate($pass)) {
			$this->parent->parent->logEvent($this::name_space, 'Failed to delete user(s): authentication failure');
			return new ActionResult(
				$this,
				'/admin/user/group_view',
				0,
				'Failed to delete user(s)!<br />Error: <code>Your password was incorrect</code>',
				B_T_FAIL,
				array('form'=>array('pwd'=>''))
			);
		}
		if(in_array($this->parent->parent->user->getUserID(), $users)){
			$this->parent->parent->logEvent($this::name_space, 'Failed to delete user(s): delete current user');
			return new ActionResult(
				$this,
				'/admin/user/group_view',
				0,
				'Failed to delete user!<br />Error: <code>You cannot delete yourself</code>',
				B_T_FAIL,
				array('form'=>array('pwd'=>''))
			);
		}
		
		$check_query = $this->mySQL_w->prepare("SELECT `p_group` FROM `core_users` WHERE `id`=?");
		
		if($check_query === false){
			return new ActionResult(
				$this,
				'/admin/user/user_view',
				0,
				'Failed to delete users!<br />Error: <code>Check query failed</code>',
				B_T_FAIL,
				array('form'=>array('pwd'=>''))
			);
		}
		foreach($users as $UID){
			// TODO Server Side validation
			$check_query->bind_param('i', $UID);
			$check_query->execute();
			$check_query->bind_result($p_group);
			$check_query->fetch();
			if($p_group==1 && !$this->parent->parent->user->inGroup(1)){
				$this->parent->parent->logEvent($this::name_space, 'Failed to delete user(s): select Super Admin');
				return new ActionResult(
					$this,
					'/admin/user/group_view',
					0,
					'Failed to delete user!<br />Error: <code>You cannot delete a Super Administrator</code>',
					B_T_FAIL,
					array('form'=>array('pwd'=>''))
				);
			}
		}
		$check_query->free_result();

		$update_query = $this->mySQL_w->prepare("DELETE FROM `core_users` WHERE `id`=?");
		
		if($update_query === false){
			return new ActionResult(
				$this,
				'/admin/user/user_view',
				0,
				'Failed delete users!<br />Error: <code>Update query failed</code>',
				B_T_FAIL,
				array('form'=>array('pwd'=>''))
			);
		}
		$affected_rows = 0;
		foreach($users as $UID){
			$update_query->bind_param('i', $UID);
			$update_query->execute();
			$update_query->store_result();
			$affected_rows += $update_query->affected_rows;
		}

		if($affected_rows == count($users)){
			$this->parent->parent->logEvent($this::name_space, 'Deleted '.csvgetstr($users));
			return new ActionResult(
				$this,
				'/admin/user/user_view',
				1,
				'Successfully deleted selected user(s)!',
				B_T_SUCCESS,
				array('form'=>array('pwd'=>''))
			);
		}else{
			$this->parent->parent->logEvent($this::name_space, 'Deleted some of '.csvgetstr($users));
			return new ActionResult(
				$this,
				'/admin/user/user_view',
				1,
				'Successfully deleted '.$affected_rows.'/'.count($users).' selected user(s)!<br /><small>Possible cause: <code>User with that ID may not exist</code></small>',
				B_T_WARNING,
				array('form'=>array('pwd'=>''))
			);
		}
	}
	
	function enable(){
		$users		= (WebApp::post('users')===NULL)?	array()	:strgetcsv(WebApp::post('users'));
		if(count($users)==0){
			$users	= (WebApp::get('u')===NULL)?	array()	:strgetcsv(WebApp::get('u'));
		}
		if(count($users)==0){
			return new ActionResult(
				$this,
				'/admin/user/user_view',
				0,
				'No user(s) were selected!',
				B_T_FAIL
			);
		}
		$update_query = $this->mySQL_w->prepare("UPDATE `core_users` SET `en`=1 WHERE `id`=?");
		if($update_query === false){
			return new ActionResult(
				$this,
				'/admin/user/user_view',
				0,
				'Failed enable users!<br />Error: <code>Update query failed</code>',
				B_T_FAIL
			);
		}
		$affected_rows = 0;
		foreach($users as $UID){
			$update_query->bind_param('i', $UID);
			$update_query->execute();
			$update_query->store_result();
			$affected_rows += $update_query->affected_rows;
		}

		if($affected_rows == count($users)){
			$this->parent->parent->logEvent($this::name_space, 'Enabled '.csvgetstr($users));
			return new ActionResult(
				$this,
				'/admin/user/user_view',
				1,
				'Successfully enabled selected user(s)!',
				B_T_SUCCESS
			);
		}else{
			$this->parent->parent->logEvent($this::name_space, 'Enabled some of '.csvgetstr($users));
			return new ActionResult(
				$this,
				'/admin/user/user_view',
				1,
				'Successfully enabled '.$affected_rows.'/'.count($users).' selected user(s)!<br /><small>Possible cause: <code>User was already enabled</code></small>',
				B_T_WARNING
			);
		}
	}
	
	function disable(){
		$users	= (WebApp::post('users')===NULL)?	array()	:strgetcsv(WebApp::post('users'));
		if(count($users)==0){
			$users	= (WebApp::get('u')===NULL)?	array()	:strgetcsv(WebApp::get('u'));
		}
		if(count($users)==0){
			return new ActionResult(
				$this,
				'/admin/user/group_view',
				0,
				'No user(s) were selected!',
				B_T_FAIL
			);
		}

		$check_query = $this->mySQL_w->prepare("SELECT `p_group` FROM `core_users` WHERE `id`=?");
		
		if($check_query === false){
			return new ActionResult(
				$this,
				'/admin/user/user_view',
				0,
				'Failed to disable users!<br />Error: <code>Check query failed</code>',
				B_T_FAIL
			);
		}
		if(in_array($this->parent->parent->user->getUserID(), $users)){
			$this->parent->parent->logEvent($this::name_space, 'Failed to disable user: disable current user');
			return new ActionResult(
				$this,
				'/admin/user/user_view',
				0,
				'Failed to disable user!<br />Error: <code>Cannot disable yourself</code>',
				B_T_FAIL
			);
		}
		foreach($users as $UID){
			$check_query->bind_param('i', $UID);
			$check_query->execute();
			$check_query->bind_result($p_group);
			$check_query->fetch();
			
			if($p_group==1 && !$this->parent->parent->user->inGroup(1)){
				$this->parent->parent->logEvent($this::name_space, 'Failed to disable user: disable Super Admin');
				return new ActionResult(
					$this,
					'/admin/user/group_view',
					0,
					'Failed to disable user!<br />Error: <code>You cannot disable Super Administrators</code>',
					B_T_FAIL
				);
			}
		}
		$check_query->free_result();

		$update_query = $this->mySQL_w->prepare("UPDATE `core_users` SET `en`=0 WHERE `id`=?");
		
		if($update_query === false){
			return new ActionResult(
				$this,
				'/admin/user/user_view',
				0,
				'Failed disable users!<br />Error: <code>Update query failed</code>',
				B_T_FAIL
			);
		}
		$affected_rows = 0;
		foreach($users as $UID){
			$update_query->bind_param('i', $UID);
			$update_query->execute();
			$update_query->store_result();
			$affected_rows += $update_query->affected_rows;
		}

		if($affected_rows == count($users)){
			$this->parent->parent->logEvent($this::name_space, 'Disabled users '.csvgetstr($users));
			return new ActionResult(
				$this,
				'/admin/user/user_view',
				1,
				'Successfully disabled selected user(s)!',
				B_T_SUCCESS
			);
		}else{
			$this->parent->parent->logEvent($this::name_space, 'Disabled some of users '.csvgetstr($users));
			return new ActionResult(
				$this,
				'/admin/user/user_view',
				1,
				'Successfully disabled '.$affected_rows.'/'.count($users).' selected user(s)!<br /><small>Possible cause: <code>User was already disabled</code></small>',
				B_T_WARNING
			);
		}
	}
	
	function setpassword(){
		$n_pwd		= (WebApp::post('n_pwd')===NULL)?		''		:WebApp::post('n_pwd');
		$n_pwd_c	= (WebApp::post('c_pwd')===NULL)?		''		:WebApp::post('c_pwd');
		$users		= (WebApp::post('users')===NULL)?		array()	:strgetcsv(WebApp::post('users'));
		if(count($users)==0){
			return new ActionResult(
				$this,
				'/admin/user/user_view',
				0,
				'Failed to set passwords.<br />Error: <code>No users were selected</code>',
				B_T_FAIL
			);
		}
		if($n_pwd == ''){
			return new ActionResult(
				$this,
				'/admin/user/user_view',
				0,
				'Failed to set passwords.<br />Error: <code>Password cannot be blank</code>',
				B_T_FAIL
			);
		}
		if($n_pwd != $n_pwd_c){
			return new ActionResult(
				$this,
				'/admin/user/user_view',
				0,
				'Failed to set passwords.<br />Error: <code>New passwords must match</code>',
				B_T_FAIL
			);
		}
		
		$userCtrl = $this->parent->parent->user;
		
		$check_query = $this->mySQL_w->prepare("SELECT `p_group` FROM `core_users` WHERE `id`=?");
		
		if($check_query === false){
			return new ActionResult(
				$this,
				'/admin/user/user_view',
				0,
				'Failed to set passwords!<br />Error: <code>Check query failed</code>',
				B_T_FAIL
			);
		}
		foreach($users as $UID){
			$check_query->bind_param('i', $UID);
			$check_query->execute();
			$check_query->bind_result($p_group);
			$check_query->fetch();
			if($p_group==1 && !$this->parent->parent->user->inGroup(1)){
				$this->parent->parent->logEvent($this::name_space, 'Tried to set password on a Super Admin');
				return new ActionResult(
					$this,
					'/admin/user/group_view',
					0,
					'Failed to set password for user!<br />Error: <code>You cannot set the password for a Super Administrator</code>',
					B_T_FAIL
				);
			}
		}
		$check_query->free_result();
		
		$update_query = $this->mySQL_w->prepare("UPDATE `core_users` SET `pass`=?, `chgPwd`=1, `pwd_reset`=`pwd_reset`+1 WHERE `id`=?");
		if($update_query === false){
			return new ActionResult(
				$this,
				'/admin/user/user_view',
				0,
				'Failed to set passwords!<br />Error: <code>Update query failed</code>',
				B_T_FAIL
			);
		}
		$affected_rows = 0;
		foreach($users as $UID){
			$hash = $userCtrl->ranHash();
			$new_pwd = $userCtrl->pwd_hash($n_pwd, $hash) . ':' . $hash;
			$update_query->bind_param('si', $new_pwd, $UID);
			$update_query->execute();
			$update_query->store_result();
			$affected_rows += $update_query->affected_rows;
		}

		if($affected_rows == count($users)){
			$this->parent->parent->logEvent($this::name_space, 'Set new password for users '.csvgetstr($users));
			return new ActionResult(
				$this,
				'/admin/user/user_view',
				1,
				'Successfully set password for selected user(s)!',
				B_T_SUCCESS
			);
		}else{
			$this->parent->parent->logEvent($this::name_space, 'Set new password for some users '.csvgetstr($users));
			return new ActionResult(
				$this,
				'/admin/user/user_view',
				1,
				'Successfully set password for '.$affected_rows.'/'.count($users).' selected user(s)!<br /><small>Possible cause: <code>Unknown</code></small>',
				B_T_WARNING
			);
		}
	}
	
	function resend(){
		$username	= (WebApp::post('username')===NULL)?	''		:WebApp::post('username');
		$email		= (WebApp::post('email')===NULL)?		''		:WebApp::post('email');
		
		$select_query = $this->mySQL_r->prepare("SELECT `email`, `username` FROM `core_users` WHERE (`username`=? OR `email`=?) AND `act_b`=0");
		if($select_query === false){
			return new ActionResult(
				$this,
				'/user/resend',
				0,
				'Failed resend activation email!<br />Error: <code>Failed to check username/email</code>',
				B_T_FAIL
			);
		}
		$select_query->bind_param('ss', $username, $email);
		$select_query->execute();
		$select_query->store_result();
		if($select_query->num_rows!=1){
			return new ActionResult(
				$this,
				'/user/resend',
				0,
				'Failed resend activation email!<br />Error: <code>Couldn\'t find user, or user is already activated</code>',
				B_T_FAIL
			);
		}
		$select_query->bind_result($email, $username);
		$select_query->fetch();
		
		return $this->genActivation($email, 'resend', array('f'=>'/user/resend', 's'=>'/user/activate'));
	}
	
	function edit_details(){
		$userid		= (WebApp::post('userid')===NULL)?		''		:WebApp::post('userid');
		$f_name		= (WebApp::post('f_name')===NULL)?		''		:WebApp::post('f_name');
		$s_name		= (WebApp::post('s_name')===NULL)?		''		:WebApp::post('s_name');
		$username	= (WebApp::post('username')===NULL)?	''		:WebApp::post('username');
		//$old_email	= (WebApp::post('old_email')===NULL)?	''		:WebApp::post('old_email');
		//$email		= (WebApp::post('email')===NULL)?		''		:WebApp::post('email');
		
		if($userid != $this->parent->parent->user->getUserID()){
			return new ActionResult(
				$this,
				'/user/profile/details',
				0,
				'Failed save details.<br />Error: <code>User IDs don\'t match</code>',
				B_T_FAIL
			);
		}
		
		if($f_name=='' || $s_name==''){
			return new ActionResult(
				$this,
				'/admin/user/user_edit',
				0,
				'Failed to edit user.<br />Error: <code>Name must not be empty</code>',
				B_T_FAIL
			);
		}
		
		$update = $this->mySQL_w->prepare("UPDATE `core_users` SET `f_name`=?,`s_name`=? WHERE `id`=?");
		if($update === false){
			return new ActionResult(
				$this,
				'/user/profile/details',
				0,
				'Failed save details!',
				B_T_FAIL
			);
		}
		$update->bind_param('ssi', $f_name, $s_name, $userid);
		$update->execute();
		$update->store_result();
		if($update->affected_rows==0){
			return new ActionResult(
				$this,
				'/user/profile/details',
				0,
				'Nothing to change',
				B_T_INFO
			);
		}
		/*if($old_email != $email){
			return $this->genActivation($email, 'email', array('f'=>'/user/activate', 's'=>'/user/activate'));
		}else{*/
			return new ActionResult(
				$this,
				'/user/profile',
				1,
				'Saved details!',
				B_T_SUCCESS
			);
		//}
	}
}
?>