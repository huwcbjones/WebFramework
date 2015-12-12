<?php
/**
 * News Action Class
 *
 * @category   Module.News.Action
 * @package    news/action.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

class ActionController extends BaseAction
{
	const	 name_space	 = 'Module.News';
	const	 version	 = '1.0.0';
	
	public function article_add(){
		if($this->accessAdminPage(2)){
			require_once dirname(__FILE__).'/resources/article.action.php';
			$article = new ArticleAction($this->parent);
			return $article->add();
		}else{
			$this->parent->parent->addHeader('Location', '/admin/news');
			return new ActionResult($this, '/admin/news', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function article_edit(){
		if($this->accessAdminPage(3)){
			require_once dirname(__FILE__).'/resources/article.action.php';
			$article = new ArticleAction($this->parent);
			return $article->edit();
		}else{
			$this->parent->parent->addHeader('Location', '/admin/news');
			return new ActionResult($this, '/admin/news', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	/*public function user_enable(){
		if($this->accessAdminPage(2)){
			require_once dirname(__FILE__).'/resources/user.php';
			$user = new UserAction($this->parent);
			return $user->enable();
		}else{
			$this->parent->parent->addHeader('Location', '/admin/user/');
			return new ActionResult($this, '/admin/user/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function user_disable(){
		if($this->accessAdminPage(2)){
			require_once dirname(__FILE__).'/resources/user.php';
			$user = new UserAction($this->parent);
			return $user->disable();
		}else{
			$this->parent->parent->addHeader('Location', '/admin/user/');
			return new ActionResult($this, '/admin/user/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function user_delete(){
		if($this->inGroup(3, true)){
			require_once dirname(__FILE__).'/resources/user.php';
			$user = new UserAction($this->parent);
			return $user->del();
		}else{
			$this->parent->parent->addHeader('Location', '/admin/user/');
			return new ActionResult($this, '/admin/user/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function setpassword(){
		if($this->accessAdminPage(2)){
			require_once dirname(__FILE__).'/resources/user.php';
			$user = new UserAction($this->parent);
			return $user->setpassword();
		}else{
			$this->parent->parent->addHeader('Location', '/admin/user/');
			return new ActionResult($this, '/admin/user/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function session_destroy(){
		if(!$this->inGroup(20, true)){
			return new ActionResult($this, '/admin/user/user_view/', 0, 'You are not allowed to do that', B_T_FAIL);
		}
		
		$sessID = WebApp::get('cat4');
		
		$destroy_query = $this->mySQL_w->prepare("DELETE FROM `core_sessions` WHERE `id`=?");
		$destroy_query->bind_param('i', $sessID);
		$destroy_query->execute();
		$destroy_query->store_result();
		if($destroy_query->affected_rows == 1){
			$this->parent->parent->logEvent($this::name_space, 'Destroyed session');
			return new ActionResult($this, Server::get('HTTP_Referer'), 1, 'Session was destroyed!', B_T_SUCCESS);
		}else{
			$this->parent->parent->logEvent($this::name_space, 'Failed to destroy session');
			return new ActionResult($this, '/admin/user/user_view', 0, 'Failed to destroy session!', B_T_FAIL);
		}
	}
	
	public function session_destroym(){
		if(!$this->inGroup(20, true)){
			return new ActionResult($this, '/admin/', 0, 'You are not allowed to do that', B_T_FAIL);
		}
		
		$userID = WebApp::get('cat4');
		
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
		if($this->accessAdminPage(5)){
			require_once dirname(__FILE__).'/resources/group.php';
			$group = new GroupAction($this->parent);
			return $group->add();
		}else{
			$this->parent->parent->addHeader('Location', '/admin/user/');
			return new ActionResult($this, '/admin/user/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function group_edit(){
		if($this->accessAdminPage(12)){
			require_once dirname(__FILE__).'/resources/group.php';
			$group = new GroupAction($this->parent);
			return $group->edit();
		}else{
			$this->parent->parent->addHeader('Location', '/admin/user/');
			return new ActionResult($this, '/admin/user/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function group_enable(){
		if($this->accessAdminPage(12)){
			require_once dirname(__FILE__).'/resources/group.php';
			$group = new GroupAction($this->parent);
			return $group->enable();
		}else{
			$this->parent->parent->addHeader('Location', '/admin/user/');
			return new ActionResult($this, '/admin/user/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function group_disable(){
		if($this->accessAdminPage(12)){
			require_once dirname(__FILE__).'/resources/group.php';
			$group = new GroupAction($this->parent);
			return $group->disable();
		}else{
			$this->parent->parent->addHeader('Location', '/admin/user/');
			return new ActionResult($this, '/admin/user/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}

	public function group_del(){
		if($this->inGroup(13, true)){
			require_once dirname(__FILE__).'/resources/group.php';
			$group = new GroupAction($this->parent);
			return $group->del();
		}else{
			$this->parent->parent->addHeader('Location', '/admin/user/group_view');
			return new ActionResult($this, '/admin/user/group_view', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}*/
}
?>
