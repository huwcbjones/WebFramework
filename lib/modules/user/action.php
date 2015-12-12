<?php
/**
 * User Action Class
 *
 * @category   Module.User.Action
 * @package    user/action.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */

class ActionController extends BaseAction
{
	const	 name_space	 = 'Module.User';
	const	 version	 = '1.0.0';
	
	public function login(){
		if(Session::get($this::name_space, 'login_attempts')>=10){
			$ipBan = new IpBan($this->parent->parent);
			if($ipBan->ban('Too many authentication failures', 15)){
				Session::del($this::name_space, 'login_attempts');
				return new ActionResult($this, '/', 1, '', B_T_FAIL);
			}
		}
		$user = WebApp::post('user');
		$pass = WebApp::post('pwd');
		$this->parent->parent->debug($this::name_space.': Logging in user...');
		$user_query = $this->mySQL_r->prepare("SELECT `id`, `username`, `act_b`, `chgPwd`, `en` FROM `core_users` WHERE `username`=? OR `email`=?");
		$user_query->bind_param('ss',$user, $user);
		$user_query->execute();
		$user_query->bind_result($id, $username, $activated, $chgPwd, $enabled);
		$user_query->store_result();
		
		// Check we have a user to log into
		if($user_query->num_rows!=1){
			$login_attempts = (Session::get($this::name_space, 'login_attempts')===NULL)? 0 : Session::get($this::name_space, 'login_attempts');
			
			$this->parent->parent->logEvent($this::name_space, 'Someone tried to login to user "'.$user.'" except they don\'t exist');
			$this->parent->parent->debug($this::name_space.': Someone tried to login to user "'.$user.'" except they don\'t exist!');
			$this->parent->parent->debug($this::name_space.': Number of attempts '.$login_attempts);
			
			Session::set($this::name_space, 'login_attempts', $login_attempts + 1);
			return new ActionResult($this, '/user/login', 0, 'Invalid username or password!<br />'.PHP_EOL.'Usernames and passwords are case sensitive.', B_T_FAIL, array('form'=>array('pwd'=>'')));
		}
		
		while($user_query->fetch()){
			$active = intval($activated);
			$changePassword = intval($chgPwd);
			$enabled = intval($enabled);
			$id = $id;
		}

		// Have they activated their account?
		if(!$active){
			$this->parent->parent->logEvent($this::name_space, 'Unactivated user "'.$username.'" tried to log in');
			return new ActionResult($this, '/user/activate', 1, '');
		}
		
		// Has the user been disabled?
		if(!$enabled){
			$this->parent->parent->logEvent($this::name_space, 'Disabled user "'.$username.'" tried to log in');
			return new ActionResult($this, '/user/login', 0, 'Your account has been disabled. Contact the webmaster for further information.', B_T_FAIL, array('form'=>array('user'=>'','pwd'=>'')));
		}
		
		// Now we can see if they got the password correct
		if(!$this->parent->parent->user->authenticate($pass, $id, $username)){
			$login_attempts = (Session::get($this::name_space, 'login_attempts')===NULL)? 0 : Session::get($this::name_space, 'login_attempts');
			
			$this->parent->parent->logEvent($this::name_space, $username.' failed to log in');
			$this->parent->parent->debug($this::name_space.': '.$username.' failed to log in');
			$this->parent->parent->debug($this::name_space.': Number of attempts '.$login_attempts);
			
			Session::set($this::name_space, 'login_attempts', $login_attempts + 1);
			return new ActionResult($this, '/user/login', 0, 'Invalid username or password!<br />'.PHP_EOL.'Usernames and passwords are case sensitive.', B_T_FAIL, array('form'=>array('pwd'=>'')));
		}
		
		// Now we can log them in
		Session::del($this::name_space, 'login_attempts');
		$this->parent->parent->logEvent($this::name_space, $username.' logged in');
		//Session::regen();
		if(!$this->parent->parent->user->session->create($id)){
			$this->parent->parent->logEvent($this::name_space, 'Failed to create token!');
			return new ActionResult(
				$this,
				'/user/login',
				0,
				'Login failed, please speak to webmaster',
				B_T_FAIL
			);
		}
		Session::set('WebApp.User', 'loggedIn', true);
		Session::set('WebApp.User', 'username', $username);
		Session::set('WebApp.User', 'userID', $id);
		if($changePassword==1){
			return new ActionResult($this, '/user/profile/password', 1, '');
		}
		if(WebApp::post('r')!==NULL&&WebApp::post('r')!==''){
			$url = urldecode(WebApp::post('r'));
		}else{
			$url = '/user';
		}
		return new ActionResult($this, $url, 1, '');
	}
	
	public function auth(){
		$pass = WebApp::post('pwd');
		$url = WebApp::post('url');
		if($url == '' || $url == NULL){
			$url = '/';
		}
		if(!$this->parent->parent->user->authenticate($pass)){
			return new ActionResult(
				$this,
				'/user/auth',
				0,
				'Invalid password!<br />'.PHP_EOL.'Passwords are case sensitive.',
				B_T_FAIL,
				array(
					'form'=>array(
						'pwd'=>''
					)
				)
			);
		}
		if($this->parent->parent->user->session->unlock()){
			return new ActionResult(
				$this,
				$url,
				1,
				'Welcome back!',
				B_T_SUCCESS
			);
		}else{
			return new ActionResult(
				$this,
				'/user/auth',
				0,
				'Failed to unlock session!',
				B_T_FAIL,
				array(
					'form'=>array(
						'pwd'=>''
					)
				)
			);
		}
	}
	
	public function lock(){
		$lock_query = $this->mySQL_w->prepare("UPDATE `core_sessions` SET `auth`=1 WHERE `token`=?");
		$id = Cookie::get('ltkn');
		$lock_query->bind_param('i', $id);
		$lock_query->execute();
		return new ActionResult($this, Server::get('HTTP_Referer'), 1, 'Locked your session!', B_T_SUCCESS);
	}
	public function logout(){
		$this->parent->parent->logEvent($this::name_space, 'Logged out user');
		return $this->parent->parent->user->logout();
	}
	
	public function activate(){
		require_once dirname(__FILE__).'/resources/user.php';
		$user = new UserAction($this->parent);
		return $user->activate();
	}
	
	public function resend(){
		require_once dirname(__FILE__).'/resources/user.php';
		$user = new UserAction($this->parent);
		return $user->resend();
	}
	
	public function recover(){
		require_once dirname(__FILE__).'/resources/user.php';
		$user = new UserAction($this->parent);
		return $user->recover();
	}
	
	public function change_details(){
		require_once dirname(__FILE__).'/resources/user.php';
		$user = new UserAction($this->parent);
		return $user->edit_details();
	}
	
	public function password(){
		require_once dirname(__FILE__).'/resources/user.php';
		$user = new UserAction($this->parent);
		return $user->password();
	}
	
	public function user_add(){
		if($this->accessAdminPage(2)){
			require_once dirname(__FILE__).'/resources/user.php';
			$user = new UserAction($this->parent);
			return $user->add();
		}else{
			return new ActionResult($this, '/admin/user/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function user_edit(){
		if($this->accessAdminPage(3)){
			require_once dirname(__FILE__).'/resources/user.php';
			$user = new UserAction($this->parent);
			return $user->edit();
		}else{
			return new ActionResult($this, '/admin/user/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function user_enable(){
		if($this->accessAdminPage(3)){
			require_once dirname(__FILE__).'/resources/user.php';
			$user = new UserAction($this->parent);
			return $user->enable();
		}else{
			return new ActionResult($this, '/admin/user/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function user_disable(){
		if($this->accessAdminPage(3)){
			require_once dirname(__FILE__).'/resources/user.php';
			$user = new UserAction($this->parent);
			return $user->disable();
		}else{
			return new ActionResult($this, '/admin/user/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function user_delete(){
		if($this->inGroup(3, true)){
			require_once dirname(__FILE__).'/resources/user.php';
			$user = new UserAction($this->parent);
			return $user->del();
		}else{
			return new ActionResult($this, '/admin/user/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function setpassword(){
		if($this->accessAdminPage(3)){
			require_once dirname(__FILE__).'/resources/user.php';
			$user = new UserAction($this->parent);
			return $user->setpassword();
		}else{
			return new ActionResult($this, '/admin/user/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function session_destroy(){
		if(!$this->accessAdminPage(20)){
			return new ActionResult($this, '/admin/user/user_view', 0, 'You are not allowed to do that', B_T_FAIL);
		}
		if(WebApp::get('m') === 'm'){
			$sessID = (WebApp::post('sessions')===NULL)?	array() :strgetcsv(WebApp::post('sessions'));
			if(count($sessID) === 0){
				return new ActionResult($this, '/admin/user/user_view', 0, 'Session IDs cannot be blank!', B_T_FAIL);
			}
		}else{
			$sessID = WebApp::get('cat4');
			if($sessID === NULL || $sessID == ''){
				return new ActionResult($this, '/admin/user/user_view', 0, 'Session\'s ID cannot be blank!', B_T_FAIL);
			}

			$sessID = array($sessID);
		}
		$destroy_query = $this->mySQL_w->prepare("DELETE FROM `core_sessions` WHERE `id`=?");

		$affected_rows = 0;
		foreach($sessID as $ID){
			$destroy_query->bind_param('i', $ID);
			$destroy_query->execute();
			$destroy_query->store_result();
			$affected_rows =+ $destroy_query->affected_rows;
		}
		if($affected_rows == count($sessID)){
			$this->parent->parent->logEvent($this::name_space, 'Destroyed session(s)');
			return new ActionResult($this, Server::get('HTTP_Referer'), 1, 'Session(s) were destroyed!', B_T_SUCCESS);
		}elseif($affected_rows == 0){
			$this->parent->parent->logEvent($this::name_space, 'Failed to destroy session(s)');
			return new ActionResult($this, '/admin/user/user_view', 0, 'Failed to destroy any sessions!', B_T_FAIL);
		}else{
			$this->parent->parent->logEvent($this::name_space, 'Destroyed some sessions, but failed to destroy the rest!');
			return new ActionResult($this, Server::get('HTTP_Referer'), 1, 'Some sessions were destroyed!', B_T_WARNING);
		}
	}
	
	public function session_lock(){
		if(!$this->accessAdminPage(20)){
			return new ActionResult($this, '/admin/user/user_view', 0, 'You are not allowed to do that', B_T_FAIL);
		}
		if(WebApp::get('m') === 'm'){
			$sessID = (WebApp::post('sessions')===NULL)?	array() :strgetcsv(WebApp::post('sessions'));
			if(count($sessID) === 0){
				return new ActionResult($this, '/admin/user/user_view', 0, 'Session IDs cannot be blank!', B_T_FAIL);
			}
		}else{
			$sessID = WebApp::get('cat4');
			if($sessID === NULL || $sessID == ''){
				return new ActionResult($this, '/admin/user/user_view', 0, 'Session\'s ID cannot be blank!', B_T_FAIL);
			}

			$sessID = array($sessID);
		}
		$destroy_query = $this->mySQL_w->prepare("UPDATE `core_sessions` SET `auth`=1 WHERE `id`=?");

		$affected_rows = 0;
		foreach($sessID as $ID){
			$destroy_query->bind_param('i', $ID);
			$destroy_query->execute();
			$destroy_query->store_result();
			$affected_rows =+ $destroy_query->affected_rows;
		}
		if($affected_rows == count($sessID)){
			$this->parent->parent->logEvent($this::name_space, 'Locked session(s)');
			return new ActionResult($this, Server::get('HTTP_Referer'), 1, 'Session(s) were locked!', B_T_SUCCESS);
		}elseif($affected_rows == 0){
			$this->parent->parent->logEvent($this::name_space, 'Failed to lock session(s)');
			return new ActionResult($this, '/admin/user/user_view', 0, 'Failed to lock any sessions!', B_T_FAIL);
		}else{
			$this->parent->parent->logEvent($this::name_space, 'Locked some sessions, but failed to lock the rest!');
			return new ActionResult($this, Server::get('HTTP_Referer'), 1, 'Some sessions were locked!', B_T_WARNING);
		}
	}
	
	public function session_destroym(){
		if(!$this->accessAdminPage(20)){
			return new ActionResult($this, '/admin/', 0, 'You are not allowed to do that', B_T_FAIL);
		}
		
		$userID = WebApp::get('cat4');
		if($userID === NULL || $userID == ''){
			return new ActionResult($this, '/admin/user/user_view', 0, 'User ID cannot be blank!', B_T_FAIL);
		}
		
		$destroy_query = $this->mySQL_w->prepare("DELETE FROM `core_sessions` WHERE `user`=?");
		$destroy_query->bind_param('i', $userID);
		$destroy_query->execute();
		$destroy_query->store_result();
		if($destroy_query->affected_rows != 0){
			$this->parent->parent->logEvent($this::name_space, 'Logged out user '.$userID);
			return new ActionResult($this, Server::get('HTTP_Referer'), 1, 'User was logged!', B_T_SUCCESS);
		}else{
			$this->parent->parent->logEvent($this::name_space, 'Failed to add '.$userID);
			return new ActionResult($this, '/admin/user/user_view', 0, 'Failed to logout user!', B_T_FAIL);
		}
	}
	
	public function group_add(){
		if($this->accessAdminPage(12)){
			require_once dirname(__FILE__).'/resources/group.php';
			$group = new GroupAction($this->parent);
			return $group->add();
		}else{
			return new ActionResult($this, '/admin/user/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function group_edit(){
		if($this->accessAdminPage(13)){
			require_once dirname(__FILE__).'/resources/group.php';
			$group = new GroupAction($this->parent);
			return $group->edit();
		}else{
			return new ActionResult($this, '/admin/user/group_view', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function group_enable(){
		if($this->accessAdminPage(13)){
			require_once dirname(__FILE__).'/resources/group.php';
			$group = new GroupAction($this->parent);
			return $group->enable();
		}else{
			return new ActionResult($this, '/admin/user/group_view', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function group_disable(){
		if($this->accessAdminPage(13)){
			require_once dirname(__FILE__).'/resources/group.php';
			$group = new GroupAction($this->parent);
			return $group->disable();
		}else{
			return new ActionResult($this, '/admin/user/group_view', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}

	public function group_del(){
		if($this->inGroup(13, true)){
			require_once dirname(__FILE__).'/resources/group.php';
			$group = new GroupAction($this->parent);
			return $group->del();
		}else{
			return new ActionResult($this, '/admin/user/group_view', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}

}
?>
