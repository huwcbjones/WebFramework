<?php
/**
 * Article Action Class for News
 *
 * @category   Module.News.Action.Article
 * @package    news/resources/article.action.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/
 
class ArticleAction extends BaseAction
{
	const name_space = 'Module.Article.Action';
	const version = '1.0.0';
	
	function add(){
		$title	= (WebApp::post('title')===NULL)?	''	:WebApp::post('title');
		$p_from	= (WebApp::post('p_from')==='')?	NULL:getSQLDate(WebApp::post('p_from'));
		$p_to	= (WebApp::post('p_to')==='')?		NULL:getSQLDate(WebApp::post('p_to'));
		$article= (WebApp::post('article')===NULL)?	''	:WebApp::post('article');
		$user	= $this->parent->parent->user->getUserID();
		$group	= $this->parent->parent->user->getGroup();
		
		$aid	 = removeSpecialChars($title);
		
		$article_add = $this->mySQL_w->prepare("INSERT INTO `news_articles` (`title`,`aid`,`user`,`group`,`article`,`date_p`,`publish_f`,`publish_u`) VALUES(?,?,?,?,?,NOW(),?,?)");
		if ($article_add == false) {
			return new ActionResult(
				$this,
				'/admin/news/article_add',
				0,
				'Failed to save article.<br />Error: <code>Query failed</code>',
				B_T_FAIL
			);
		}
		$article_add->bind_param('ssiisss', $title, $aid, $user, $group, $article, $p_from, $p_to);
		$article_add->execute();
		$article_add->store_result();
		if ($article_add->affected_rows == 1) {
			$this->parent->parent->logEvent($this::name_space, 'Added article '.$title);
			return new ActionResult(
				$this,
				'/admin/news/article_view',
				1,
				'Successfully saved article!',
				B_T_SUCCESS
			);
		} else {
			$this->parent->parent->logEvent($this::name_space, 'Failed to add article '.$title);
			return new ActionResult(
				$this,
				'/admin/news/article_add',
				0,
				'Failed to add article.<br />Error: <code>'.$this->mySQL_w->error.'</code>',
				B_T_FAIL
			);
		}
	}
	
	/*function edit(){
		$GID	= (WebApp::post('id')===NULL)?		''		:WebApp::post('id');
		$name	= (WebApp::post('name')===NULL)?	''		:WebApp::post('name');
		$desc	= (WebApp::post('desc')===NULL)?	''		:WebApp::post('desc');
		$en		= (WebApp::post('enabled')===NULL)?	1		:WebApp::post('enabled');
		$type	= (WebApp::post('type')===NULL)?	's'		:WebApp::post('type');
		$pages	= (WebApp::post('pages')===NULL)?	array()	:WebApp::post('pages');
		if(!$this->parent->inGroup(1)){
			if($this->parent->inGroup($GID, false)){
				$this->parent->parent->logEvent($this::name_space, 'Tried to edit their own group.');
				return new ActionResult(
					$this,
					'/admin/user/group_view',
					0,
					'Failed to edit group.<br />Error: <code>You cannot edit a group that you are a member of</code>',
					B_T_FAIL
				);
			}
		}
		$group_query = $this->mySQL_r->prepare("SELECT `name` FROM `core_groups` WHERE `GID`=?");
		$group_query->bind_param('i', $GID);
		$group_query->execute();
		$group_query->store_result();
		if ($group_query->num_rows == 0) {
			return new ActionResult(
				$this,
				'/admin/user/group_edit',
				0,
				'Failed to edit group.<br />Error: <code>Group with that ID not exist</code>',
				B_T_FAIL
			);
		}
		$group_edit = $this->mySQL_w->prepare("UPDATE `core_groups` SET `name`=?, `en`=?, `desc`=?, `type`=? WHERE `GID`=?");
		$gpage_del = $this->mySQL_w->prepare("DELETE FROM `core_gpage` WHERE `GID`=?");
		$gpage_edit = $this->mySQL_w->prepare("INSERT INTO `core_gpage` (`GID`,`PID`) VALUES(?,?)");
		if (in_array(false, array($group_edit, $gpage_del, $gpage_edit))) {
			return new ActionResult(
				$this,
				'/admin/user/group_edit',
				0,
				'Failed to add group.<br />Error: <code>Query failed</code>',
				B_T_FAIL
			);
		}
		$group_edit->bind_param('sissi', $name, $en, $desc, $type, $GID);
		$group_edit->execute();
		$group_edit->store_result();
		
		$gpage_del->bind_param('i',$GID);
		$gpage_del->execute();
		$gpage_del->free_result();
		
		foreach($pages as $PID){
			$gpage_edit->bind_param('ii', $GID, $PID);
			$gpage_edit->execute();
		}
		$this->parent->parent->logEvent($this::name_space, 'Edited group '.$GID);
		return new ActionResult(
			$this,
			'/admin/user/group_view',
			1,
			'Successfully edited group!',
			B_T_SUCCESS
		);
	}

	function del()
	{
		$groups	= (WebApp::post('groups')===NULL)?	array()	:strgetcsv(WebApp::post('groups'));
		$pass	= (WebApp::post('pwd')===NULL)?		''		:WebApp::post('pwd');
		if (!$this->parent->parent->user->authenticate($pass)) {
			$this->parent->parent->logEvent($this::name_space, 'Failed to delete group: authentication failure');
			return new ActionResult(
				$this,
				'/admin/user/group_view',
				0,
				'Failed to delete group!<br />Error: <code>User\'s password was incorrect</code>',
				B_T_FAIL,
				array('form'=>array('pwd'=>''))
			);
		}
		if($this->inGroup($groups, false, false)){
			$this->parent->parent->logEvent($this::name_space, 'Tried to delete own group');
			return new ActionResult(
				$this,
				'/admin/user/group_view',
				0,
				'Failed to delete group!<br />Error: <code>Cannot delete a group that you are a member of</code>',
				B_T_FAIL,
				array('form'=>array('pwd'=>''))
			);
		}
		$result = array();
		foreach($groups as $GID){
			$result[] = $GID<1000;
		}
		if(in_array(true, $result) && !$this->inGroup(1)){
			$this->parent->parent->logEvent($this::name_space, 'Tried to delete core group');
			return new ActionResult(
				$this,
				'/admin/user/group_view',
				0,
				'Failed to delete group!<br />Error: <code>Cannot delete core groups</code>',
				B_T_FAIL,
				array('form'=>array('pwd'=>''))
			);
		}
		$group_query = $this->mySQL_w->prepare("DELETE FROM `core_groups` WHERE `GID`=?");
		foreach($groups as $GID){
			$group_query->bind_param('i', $GID);
			$group_query->execute();
		}
		$group_query->store_result();
		if ($group_query->affected_rows == count($GID)) {
			$this->parent->parent->logEvent($this::name_space, 'Deleted groups '.csvgetstr($groups));
			return new ActionResult(
				$this,
				'/admin/user/group_view',
				1,
				'Successfully deleted selected group(s)!',
				B_T_SUCCESS,
				array('form'=>array('pwd'=>''))
			);
		} else {
			$this->parent->parent->logEvent($this::name_space, 'Deleted some of groups '.csvgetstr($groups));
			return new ActionResult(
				$this,
				'/admin/user/group_view',
				0,
				'Failed to delete selected group(s)!<br />Error: <code>Unknown reason</code>',
				B_T_FAIL,
				array('form'=>array('pwd'=>''))
			);
		}
	}

	function enable(){
		$groups	= (WebApp::post('groups')===NULL)?	array()	:strgetcsv(WebApp::post('groups'));
		if(count($groups)==0){
			$groups	= (WebApp::get('g')===NULL)?	array()	:strgetcsv(WebApp::get('g'));
		}
		if(count($groups)==0){
			return new ActionResult(
				$this,
				'/admin/user/group_view',
				0,
				'No group(s) were selected!',
				B_T_FAIL
			);
		}
		$update_query = $this->mySQL_w->prepare("UPDATE `core_groups` SET `en`=1 WHERE `GID`=?");

		$affected_rows = 0;
		foreach($groups as $GID){
			$update_query->bind_param('i', $GID);
			$update_query->execute();
			$update_query->store_result();
			$affected_rows += $update_query->affected_rows;
		}

		if($affected_rows == count($groups)){
			$this->parent->parent->logEvent($this::name_space, 'Enabled groups '.csvgetstr($groups));
			return new ActionResult(
				$this,
				'/admin/user/group_view',
				1,
				'Successfully enabled selected group(s)!',
				B_T_SUCCESS
			);
		}else{
			$this->parent->parent->logEvent($this::name_space, 'Enabled some of groups '.csvgetstr($groups));
			return new ActionResult(
				$this,
				'/admin/user/group_view',
				1,
				'Successfully enabled '.$affected_rows.'/'.count($groups).' selected group(s)!<br /><small>Possible cause: <code>Group was already enabled</code></small>',
				B_T_WARNING
			);
		}
	}
	function disable(){
		$groups	= (WebApp::post('groups')===NULL)?	array()	:strgetcsv(WebApp::post('groups'));
		if(count($groups)==0){
			$groups	= (WebApp::get('g')===NULL)?	array()	:strgetcsv(WebApp::get('g'));
		}
		if(count($groups)==0){
			return new ActionResult(
				$this,
				'/admin/user/group_view',
				0,
				'No group(s) were selected!',
				B_T_FAIL
			);
		}
		$update_query = $this->mySQL_w->prepare("UPDATE `core_groups` SET `en`=0 WHERE `GID`=?");

		foreach($groups as $GID){
			if($this->inGroup($GID, false, false)){
				$this->parent->parent->logEvent($this::name_space, 'Tried to disable own group');
				return new ActionResult(
					$this,
					'/admin/user/group_view',
					0,
					'Failed to disable group!<br />Error: <code>Cannot disable a group that you are a member of</code>',
					B_T_FAIL
				);
			}
			if($GID<1000 && !$this->inGroup(1)){
				$this->parent->parent->logEvent($this::name_space, 'Tried to disable core group');
				return new ActionResult(
					$this,
					'/admin/user/group_view',
					0,
					'Failed to disable group!<br />Error: <code>Cannot disable a core group</code>',
					B_T_FAIL
				);
			}
		}
		
		$affected_rows = 0;
		foreach($groups as $GID){
			$update_query->bind_param('i', $GID);
			$update_query->execute();
			$update_query->store_result();
			$affected_rows += $update_query->affected_rows;
		}

		if($affected_rows == count($groups)){
			$this->parent->parent->logEvent($this::name_space, 'Disabled groups '.csvgetstr($groups));
			return new ActionResult(
				$this,
				'/admin/user/group_view',
				1,
				'Successfully disabled selected group(s)!',
				B_T_SUCCESS
			);
		}else{
			$this->parent->parent->logEvent($this::name_space, 'Disabled some of groups '.csvgetstr($groups));
			return new ActionResult(
				$this,
				'/admin/user/group_view',
				1,
				'Successfully disabled '.$affected_rows.'/'.count($groups).' selected group(s)!<br /><small>Possible cause: <code>Group was already disabled</code></small>',
				B_T_WARNING
			);
		}
	}*/
}
?>