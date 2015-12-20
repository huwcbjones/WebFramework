<?php
/**
 * Email Action Class
 *
 * @category   Module.Email.Action
 * @package    email/action.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */

class ActionController extends BaseAction
{
	const	 name_space	 = 'Module.Email';
	const	 version	 = '1.0.0';
	
	
	public function checknames(){
		if(!$this->accessAdminPage(0)){
			return new ActionResult(
				$this,
				'/admin/email',
				0,
				'You are not allowed to do that!',
				B_T_FAIL
			);
		}
		if(count(strgetcsv(WebApp::post('to')))==0){
			return new ActionResult(
				$this,
				'/admin/email',
				0,
				'Nothing sent to check',
				B_T_FAIL
			);
		}
		$to = strgetcsv(WebApp::post('to'));
		
		$user_query = $this->mySQL_r->prepare("SELECT `id` FROM `core_users` WHERE `username`=?");
		$group_query = $this->mySQL_r->prepare("SELECT `GID` FROM `core_groups` WHERE `name`=?");
		
		$failed = array();
		$name_test = '';
		$user_query->bind_param('s', $name_test);
		$group_query->bind_param('s', $name_test);
		foreach($to as $name_test){
			$name_test = trim($name_test);
			$user_query->execute();
			$user_query->store_result();
			if($user_query->num_rows==0){
				$user_query->free_result();
				$group_query->execute();
				$group_query->store_result();
				if($group_query->num_rows==0){
					$group_query->free_result();
					if (!filter_var($name_test, FILTER_VALIDATE_EMAIL)) {
						$failed[] = $name_test;
					}
				}
			}
		}
		
		if(count($failed)==0){
			return new ActionResult(
				$this,
				'/admin/email',
				1,
				'Found all addresses',
				B_T_SUCCESS
			);
		}
		return new ActionResult(
			$this,
			'/admin/email',
			0,
			'Failed to find some addresses<br /><code>'.implode(', ', $failed).'</code>',
			B_T_FAIL
		);
		
	}
	
	public function send(){
		if(!$this->accessAdminPage(0)){
			return new ActionResult(
				$this,
				'/admin/email',
				0,
				'You are not allowed to send emails!',
				B_T_FAIL
			);
		}
		$check = $this->checknames();
		if($check->status==0){
			return $check;
		}else{
			Session::del('status_msg', $check->id);
		}
		
		$to			= WebApp::post('to');
		$subject	= WebApp::post('subject');
		$message	= WebApp::post('message');
		
		$mail = new Emailer();
		
		$mail->setFrom($this->parent->parent->user->getUsername().'@biggleswadesc.org', $this->parent->parent->user->getFullName());
		
		$mail->Subject = $subject;
		$mail->msgHTML($message);
		$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
		
		$to = strgetcsv(WebApp::post('to'));
		
		// Fetches emails from usernames
		$user_query = $this->mySQL_r->prepare("SELECT CONCAT(`f_name`, ' ', `s_name`), `email` FROM `core_users` WHERE `username`=?");
		
		// Fetches names and emails from p_group names
		$p_group_query = $this->mySQL_r->prepare(
"SELECT CONCAT(`f_name`, ' ', `s_name`),`email` FROM `core_users`
INNER JOIN `core_groups` ON `p_group`=`GID` AND `core_groups`.`name`=? AND `type`='p'"
);
		
		// Fetches names and emails from s_group names through link table (core_sgroup)
		$s_group_query = $this->mySQL_r->prepare(
"SELECT CONCAT(`f_name`, ' ', `s_name`),`email` FROM `core_users`
INNER JOIN `core_groups` ON `core_groups`.`name`=? AND `type`='s'
INNER JOIN `core_sgroup` ON `core_sgroup`.`user`=`core_users`.`id` AND `core_groups`.`GID`=`core_sgroup`.`group`"
);
	
		$email_addresses = array();
		foreach($to as $name){
			$name = trim($name);
			if (filter_var($name, FILTER_VALIDATE_EMAIL)) {
				$email_addresses[$name] = $name;
			}else{
				// Check if name is user
				$user_query->bind_param('s', $name);
				$user_query->bind_result($fullName, $email);
				$user_query->execute();
				$user_query->store_result();


				if($user_query->num_rows==1){
					$this->parent->parent->debug($this::name_space.': Address is for user');
					// deal with user
					$user_query->fetch();
					$email_addresses[$email] = $fullName;
					$user_query->free_result();
					$user_query->reset();
				}else{
					
					// Check if name is pgroup
					$user_query->free_result();
					$p_group_query->bind_param('s', $name);
					$p_group_query->bind_result($fullName, $email);
					$p_group_query->execute();
					$p_group_query->store_result();
					if($p_group_query->num_rows!=0){
						while($p_group_query->fetch()){
							$email_addresses[$email] = $fullName;
						}
						$p_group_query->free_result();
						$p_group_query->reset();
					}else{
						$p_group_query->free_result();
						$p_group_query->reset();

						// Check sgroup
						$s_group_query->bind_param('s', $name);
						$s_group_query->bind_result($fullName, $email);
						$s_group_query->execute();
						$s_group_query->store_result();
						if($s_group_query->num_rows!=0){
							// Deal with sgroup
							while($s_group_query->fetch()){
								$email_addresses[$email] = $fullName;
							}
						}
						$s_group_query->free_result();
						$s_group_query->reset();
					}
				}
			}
		}
		
		$failed = array();
		foreach($email_addresses as $email=>$name){
			$mail->addAddress($email, $name);
			if(!$mail->send()){
				$failed[] = $email;
				$this->parent->parent->debug($this::name_space.': Did not send mail to '.$email);
				$this->parent->parent->debug('Reason: '.$mail->ErrorInfo);
			}else{
				$this->parent->parent->debug($this::name_space.': Sent mail to '.$email);
			}
			$mail->clearAddresses();
		}
		if(count($failed)==0){
			return new ActionResult(
				$this,
				'/admin/email',
				1,
				'Email was successfully sent!',
				B_T_SUCCESS
			);
		}else{
			return new ActionResult(
				$this,
				'/admin/email',
				0,
				'Email was sent to except:<code>'.implode(', ', $failed).'</code>',
				B_T_WARNING
			);
		}
	}
}
?>
