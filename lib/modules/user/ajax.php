<?php
/**
 * User Ajax Class
 *
 * @category   Module.User.Ajax
 * @package    user/ajax.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */

class AjaxController extends BaseAjax
{
	const	 name_space	 = 'Module.User.Ajax';
	const	 version	 = '1.0.0';
	
	public function secondary_groups(){
		$q = WebApp::get('q');
		if($q === NULL){
			return new ActionResult(
				$this,
				Server::get('HTTP_Referer'),
				0,
				'No search term sent',
				B_T_FAIL,
				array(
					'groups'=>array()
				)
			);
		}
		
		$groups = array();
		
		$q = '%'.$q.'%';
		
		$group_query = $this->mySQL_r->prepare("SELECT `GID`,`name` FROM `core_groups` WHERE `name` LIKE ? AND `type`='s'");
		
		$group_query->bind_param('s', $q);
		$group_query->execute();
		$group_query->store_result();
		$group_query->bind_result($id, $value);
		while($group_query->fetch()){
			$group['id'] = $id;
			$group['text'] = $value;
			$groups[] = $group;
		}
		
		return new ActionResult(
			$this,
			'/admin/email',
			0,
			'Success',
			B_T_SUCCESS,
			array(
				'groups'=>$groups
			)
		);
	}
}
?>
