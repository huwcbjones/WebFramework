<?php
/**
 * Email Templates for User Module
 *
 * @category   Module.User.EmailTemplates
 * @package    user/resource/email_templates.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/
class UserEmail{
	
	static function passwordChange($name, $ip, $location){
		$html = '<p>Dear '.$name.',</p>'.PHP_EOL;
		$html.= '<p>Someone has changed the password for your user.</p>'.PHP_EOL;
		$html.= '<p>The IP address was '.$ip.$location.'.</p>'.PHP_EOL;
		$html.= '<p>If this was you, then please discard this email. However, if this wasn\'t you, please contact us as soon as possible (email <a href="mailto:admin@biggleswadesc.org">admin@biggleswadesc.org</a>).</p>'.PHP_EOL;
		$html.= '<p>Many Thanks,<br />'.PHP_EOL.'Biggleswade Swimming Club</p>';
		
		$text = 'Dear '.$name.','.PHP_EOL;
		$text.= 'Someone has changed the password for your user.'.PHP_EOL;
		$text.= 'The IP address was '.$ip.$location.'.'.PHP_EOL;
		$text.= 'If this was you, then please discard this email. However, if this wasn\'t you, please contact us as soon as possible (email admin@biggleswadesc.org).'.PHP_EOL;
		$text.= 'Many Thanks,'.PHP_EOL;
		$text.= 'Biggleswade Swimming Club';
		
		return array('html'=>$html, 'text'=>$text);
	}
	
	static function adminPasswordChange($name){
		$html = '<p>Dear '.$name.',</p>'.PHP_EOL;
		$html.= '<p>An administrator has changed the password for your user.</p>'.PHP_EOL;
		$html.= '<p>Many Thanks,<br />'.PHP_EOL.'Biggleswade Swimming Club</p>';
		
		$text = 'Dear '.$name.','.PHP_EOL;
		$text.= 'An administrator has changed the password for your user.'.PHP_EOL;
		$text.= 'Many Thanks,'.PHP_EOL;
		$text.= 'Biggleswade Swimming Club';
		
		return array('html'=>$html, 'text'=>$text);
	}
	
	static function accountCreation($name, $username, $email, $code){
		$html = '<p>Dear '.$name.',</p>'.PHP_EOL;
		$html.= '<p>Thank you for applying for an account for the Biggleswade Swimming Club Website, your application has been successful and an account has been created for you to use.</p>'.PHP_EOL;
		$html.= '<h3>Your Details:</h3>'.PHP_EOL;
		$html.= '<p><strong>Name:</strong> '.$name.'<br />'.PHP_EOL;
		$html.= '<strong>Email:</strong> '.$email.'<br />'.PHP_EOL;
		$html.= '<strong>Username:</strong> '.$username.'</p>'.PHP_EOL;
		$html.= '<p>Before you can login to the website, you need to activate your account. To do so, <a href="https://'.$_SERVER['HTTP_HOST'].'/user/activate?code='.$code.'">click here</a><br />'.PHP_EOL;
		$html.= 'Alternatively, visit <a href="https://'.$_SERVER['HTTP_HOST'].'/user/activate">https://'.$_SERVER['HTTP_HOST'].'/user/activate</a> and paste the following code into the activation code box.</p>'.PHP_EOL;
		$html.= '<p><strong>Activation Code:</strong> '.$code.'</p>'.PHP_EOL;
		$html.= '<p>Many Thanks,<br />'.PHP_EOL.'Biggleswade Swimming Club</p>';
		
		$text = 'Dear '.$name.','.PHP_EOL;
		$text.= 'Thank you for applying for an account for the Biggleswade Swimming Club Website, your application has been successful and an account has been created for you to use.'.PHP_EOL;
		$text.= 'Your Details:'.PHP_EOL;
		$text.= 'Name: '.$name.PHP_EOL;
		$text.= 'Email: '.$email.PHP_EOL;
		$text.= 'Username: '.$username.PHP_EOL.PHP_EOL;
		$text.= 'Before you can login to the website, you need to activate your account. To do so visit https://'.$_SERVER['HTTP_HOST'].'/user/activate and paste the following code into the activation code box.'.PHP_EOL;
		$text.= 'Activation Code: '.$code.PHP_EOL.PHP_EOL;
		$html.= 'Many Thanks,'.PHP_EOL.'Biggleswade Swimming Club';
		
		return array('html'=>$html, 'text'=>$text);
	}
	
	static function resendActivation($name, $username, $email, $code){
		$html = '<p>Dear '.$name.',</p>'.PHP_EOL;
		$html.= '<p>You requested a new activation code for your account. In addition to this, we\'d like to remind of of your details.</p>'.PHP_EOL;
		$html.= '<h3>Your Details:</h3>'.PHP_EOL;
		$html.= '<p><strong>Name:</strong> '.$name.'<br />'.PHP_EOL;
		$html.= '<strong>Email:</strong> '.$email.'<br />'.PHP_EOL;
		$html.= '<strong>Username:</strong> '.$username.'</p>'.PHP_EOL;
		$html.= '<p>To activate your account, <a href="https://'.Server::get('HTTP_Host').'/user/activate?code='.$code.'">click here</a><br />'.PHP_EOL;
		$html.= 'Alternatively, visit <a href="https://'.Server::get('HTTP_Host').'/user/activate">https://'.Server::get('HTTP_Host').'/user/activate</a> and paste the following code into the activation code box.</p>'.PHP_EOL;
		$html.= '<p><strong>Activation Code:</strong> '.$code.'</p>'.PHP_EOL;
		$html.= '<p>Many Thanks,<br />'.PHP_EOL.'Biggleswade Swimming Club</p>';
		
		$text = 'Dear '.$name.','.PHP_EOL;
		$text.= 'You requested a new activation code for your account. In addition to this, we\'d like to remind of of your details.'.PHP_EOL;
		$text.= 'Your Details:'.PHP_EOL;
		$text.= 'Name: '.$name.PHP_EOL;
		$text.= 'Email: '.$email.PHP_EOL;
		$text.= 'Username: '.$username.PHP_EOL.PHP_EOL;
		$text.= 'To activate your account, visit https://'.Server::get('HTTP_Host').'/user/activate and paste the following code into the activation code box.'.PHP_EOL;
		$text.= 'Activation Code: '.$code.PHP_EOL.PHP_EOL;
		$html.= 'Many Thanks,'.PHP_EOL.'Biggleswade Swimming Club';
		
		return array('html'=>$html, 'text'=>$text);
	}
	
	static function recoverAccount($name, $username, $email, $code){
		$html = '<p>Dear '.$name.',</p>'.PHP_EOL;
		$html.= '<p>You had trouble logging in and requested to recover your account. To protect your account, we have disabled your account and it will need activating before you can set a new password and log in. In addition to this, we\'d like to remind of of your details.</p>'.PHP_EOL;
		$html.= '<h3>Your Details:</h3>'.PHP_EOL;
		$html.= '<p><strong>Name:</strong> '.$name.'<br />'.PHP_EOL;
		$html.= '<strong>Email:</strong> '.$email.'<br />'.PHP_EOL;
		$html.= '<strong>Username:</strong> '.$username.'</p>'.PHP_EOL;
		$html.= '<p>To recover your account and activate it, <a href="https://'.Server::get('HTTP_Host').'/user/activate?code='.$code.'">click here</a><br />'.PHP_EOL;
		$html.= 'Alternatively, visit <a href="https://'.Server::get('HTTP_Host').'/user/activate">https://'.Server::get('HTTP_Host').'/user/activate</a> and paste the following code into the activation code box.</p>'.PHP_EOL;
		$html.= '<p><strong>Activation Code:</strong> '.$code.'</p>'.PHP_EOL;
		$html.= '<p>Many Thanks,<br />'.PHP_EOL.'Biggleswade Swimming Club</p>';
		
		$text = 'Dear '.$name.','.PHP_EOL;
		$text.= 'You had trouble logging in and requested to recover your account. To protect your account, we have disabled your account and it will need activating before you can set a new password and log in. In addition to this, we\'d like to remind of of your details.'.PHP_EOL;
		$text.= 'Your Details:'.PHP_EOL;
		$text.= 'Name: '.$name.PHP_EOL;
		$text.= 'Email: '.$email.PHP_EOL;
		$text.= 'Username: '.$username.PHP_EOL.PHP_EOL;
		$text.= 'To activate your account, visit https://'.Server::get('HTTP_Host').'/user/activate and paste the following code into the activation code box.'.PHP_EOL;
		$text.= 'Activation Code: '.$code.PHP_EOL.PHP_EOL;
		$html.= 'Many Thanks,'.PHP_EOL.'Biggleswade Swimming Club';
		
		return array('html'=>$html, 'text'=>$text);
	}
	
	/*static function changeEmail($name, $username, $email, $code){
		$html = '<p>Dear '.$name.',</p>'.PHP_EOL;
		$html.= '<p>Your email adddress has been changed. However, to ensure this</p>'.PHP_EOL;
		$html.= '<h3>Your Details:</h3>'.PHP_EOL;
		$html.= '<p><strong>Name:</strong> '.$name.'<br />'.PHP_EOL;
		$html.= '<strong>Email:</strong> '.$email.'<br />'.PHP_EOL;
		$html.= '<strong>Username:</strong> '.$username.'</p>'.PHP_EOL;
		$html.= '<p>To recover your account and activate it, <a href="https://'.Server::get('HTTP_Host').'/user/activate?code='.$code.'">click here</a><br />'.PHP_EOL;
		$html.= 'Alternatively, visit <a href="https://'.Server::get('HTTP_Host').'/user/activate">https://'.Server::get('HTTP_Host').'/user/activate</a> and paste the following code into the activation code box.</p>'.PHP_EOL;
		$html.= '<p><strong>Activation Code:</strong> '.$code.'</p>'.PHP_EOL;
		$html.= '<p>Many Thanks,<br />'.PHP_EOL.'Biggleswade Swimming Club</p>';
		
		$text = 'Dear '.$name.','.PHP_EOL;
		$text.= 'You had trouble logging in and requested to recover your account. To protect your account, we have disabled your account and it will need activating before you can set a new password and log in. In addition to this, we\'d like to remind of of your details.'.PHP_EOL;
		$text.= 'Your Details:'.PHP_EOL;
		$text.= 'Name: '.$name.PHP_EOL;
		$text.= 'Email: '.$email.PHP_EOL;
		$text.= 'Username: '.$username.PHP_EOL.PHP_EOL;
		$text.= 'To activate your account, visit https://'.Server::get('HTTP_Host').'/user/activate and paste the following code into the activation code box.'.PHP_EOL;
		$text.= 'Activation Code: '.$code.PHP_EOL.PHP_EOL;
		$html.= 'Many Thanks,'.PHP_EOL.'Biggleswade Swimming Club';
		
		return array('html'=>$html, 'text'=>$text);
	}*/
}
?>