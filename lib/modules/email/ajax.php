<?php
/**
 * Email Ajax Class
 *
 * @category   Module.Email.Ajax
 * @package    email/ajax.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */

class AjaxController extends BaseAjax
{
	const	 name_space	 = 'Module.Email.Ajax';
	const	 version	 = '1.0.0';
	
	public function contacts(){
		$q = WebApp::get('q');
		if($q === NULL){
			return new ActionResult(
				$this,
				'/admin/email',
				0,
				'No search term sent',
				B_T_FAIL,
				array(
					'contacts'=>array()
				)
			);
		}
		
		$contacts = array();
		
		if(filter_var($q, FILTER_VALIDATE_EMAIL)){
			$contact['id'] = $q;
			$contact['text'] = $q;
			$contacts[] = $contact;
		}
		$q = '%'.$q.'%';
		
		$user_query = $this->mySQL_r->prepare("SELECT `username`, CONCAT(`f_name`, ' ', `s_name`) FROM `core_users` WHERE CONCAT(`username`, ' ', `email`) LIKE ?");
		$group_query = $this->mySQL_r->prepare("SELECT `name` FROM `core_groups` WHERE `name` LIKE ?");
		
		$user_query->bind_param('s', $q);
		$user_query->execute();
		$user_query->store_result();
		$user_query->bind_result($id, $value);
		while($user_query->fetch()){
			$contact['id'] = $id;
			$contact['text'] = $value;
			$contacts[] = $contact;
		}
		
		$user_query->free_result();
		
		$group_query->bind_param('s', $q);
		$group_query->execute();
		$group_query->store_result();
		$group_query->bind_result($value);
		while($group_query->fetch()){
			$contact['id'] = $value;
			$contact['text'] = '* '.$value;
			$contacts[] = $contact;
		}
		
		return new ActionResult(
			$this,
			'/admin/email',
			0,
			'Success',
			B_T_SUCCESS,
			array(
				'contacts'=>$contacts
			)
		);
	}
}
?>
