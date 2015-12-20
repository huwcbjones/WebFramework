<?php
/**
 * Modules Action Class
 *
 * @category   Module.Modules
 * @package    modules/action.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */

class ActionController extends BaseAction
{
	const	 name_space	 = 'Module.Modules';
	const	 version	 = '1.0.0';
	
	public function pre_install(){
		if($this->accessAdminPage(1)){
			require_once dirname(__FILE__) . '/resources/install.php';
			
			$installer = new Installer($this->parent);
			return $installer->preInstall();
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function install_1(){
		if($this->accessAdminPage(1)){
			require_once dirname(__FILE__) . '/resources/install.php';
		
			$installer = new Installer($this->parent);
			return $installer->install(1);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function install_2(){
		if($this->accessAdminPage(1)){
			require_once dirname(__FILE__) . '/resources/install.php';
		
			$installer = new Installer($this->parent);
			return $installer->install(2);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function install_3(){
		if($this->accessAdminPage(1)){
			require_once dirname(__FILE__) . '/resources/install.php';
		
			$installer = new Installer($this->parent);
			return $installer->install(3);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function install_4(){
		if($this->accessAdminPage(1)){
			require_once dirname(__FILE__) . '/resources/install.php';
		
			$installer = new Installer($this->parent);
			return $installer->install(4);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function install_5(){
		if($this->accessAdminPage(1)){
			require_once dirname(__FILE__) . '/resources/install.php';
		
			$installer = new Installer($this->parent);
			return $installer->install(5);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function install_6(){
		if($this->accessAdminPage(1)){
			require_once dirname(__FILE__) . '/resources/install.php';
		
			$installer = new Installer($this->parent);
			return $installer->install(6);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function install_7(){
		if($this->accessAdminPage(1)){
			require_once dirname(__FILE__) . '/resources/install.php';
		
			$installer = new Installer($this->parent);
			return $installer->install(7);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function install_8(){
		if($this->accessAdminPage(1)){
			require_once dirname(__FILE__) . '/resources/install.php';
		
			$installer = new Installer($this->parent);
			return $installer->install(8);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function install_9(){
		if($this->accessAdminPage(1)){
			require_once dirname(__FILE__) . '/resources/install.php';
		
			$installer = new Installer($this->parent);
			return $installer->install(9);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function pre_update(){
		if($this->accessAdminPage(5)){
			require_once dirname(__FILE__) . '/resources/update.php';
			
			$updater = new Updater($this->parent);
			return $updater->preUpdate();
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function update_1(){
		if($this->accessAdminPage(5)){
			require_once dirname(__FILE__) . '/resources/update.php';
			
			$updater = new Updater($this->parent);
			return $updater->update(1);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function update_2(){
		if($this->accessAdminPage(5)){
			require_once dirname(__FILE__) . '/resources/update.php';
			
			$updater = new Updater($this->parent);
			return $updater->update(2);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function update_3(){
		if($this->accessAdminPage(5)){
			require_once dirname(__FILE__) . '/resources/update.php';
			
			$updater = new Updater($this->parent);
			return $updater->update(3);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function update_4(){
		if($this->accessAdminPage(5)){
			require_once dirname(__FILE__) . '/resources/update.php';
			
			$updater = new Updater($this->parent);
			return $updater->update(4);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function update_5(){
		if($this->accessAdminPage(5)){
			require_once dirname(__FILE__) . '/resources/update.php';
			
			$updater = new Updater($this->parent);
			return $updater->update(5);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function update_6(){
		if($this->accessAdminPage(5)){
			require_once dirname(__FILE__) . '/resources/update.php';
			
			$updater = new Updater($this->parent);
			return $updater->update(6);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function update_7(){
		if($this->accessAdminPage(5)){
			require_once dirname(__FILE__) . '/resources/update.php';
			
			$updater = new Updater($this->parent);
			return $updater->update(7);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function update_8(){
		if($this->accessAdminPage(5)){
			require_once dirname(__FILE__) . '/resources/update.php';
			
			$updater = new Updater($this->parent);
			return $updater->update(8);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function update_9(){
		if($this->accessAdminPage(5)){
			require_once dirname(__FILE__) . '/resources/update.php';
			
			$updater = new Updater($this->parent);
			return $updater->update(9);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function update_10(){
		if($this->accessAdminPage(5)){
			require_once dirname(__FILE__) . '/resources/update.php';
			
			$updater = new Updater($this->parent);
			return $updater->update(10);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function pre_uninstall(){
		if($this->accessAdminPage(2)){
			require_once dirname(__FILE__) . '/resources/uninstall.php';
			
			$uninstaller = new Uninstaller($this->parent);
			return $uninstaller->preUninstall();
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function uninstall_1(){
		if($this->accessAdminPage(2)){
			require_once dirname(__FILE__) . '/resources/uninstall.php';
		
			$uninstaller = new Uninstaller($this->parent);
			return $uninstaller->uninstall(1);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function uninstall_2(){
		if($this->accessAdminPage(2)){
			require_once dirname(__FILE__) . '/resources/uninstall.php';
		
			$uninstaller = new Uninstaller($this->parent);
			return $uninstaller->uninstall(2);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function uninstall_3(){
		if($this->accessAdminPage(2)){
			require_once dirname(__FILE__) . '/resources/uninstall.php';
		
			$uninstaller = new Uninstaller($this->parent);
			return $uninstaller->uninstall(3);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function uninstall_4(){
		if($this->accessAdminPage(2)){
			require_once dirname(__FILE__) . '/resources/uninstall.php';
		
			$uninstaller = new Uninstaller($this->parent);
			return $uninstaller->uninstall(4);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function uninstall_5(){
		if($this->accessAdminPage(2)){
			require_once dirname(__FILE__) . '/resources/uninstall.php';
		
			$uninstaller = new Uninstaller($this->parent);
			return $uninstaller->uninstall(5);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function uninstall_6(){
		if($this->accessAdminPage(2)){
			require_once dirname(__FILE__) . '/resources/uninstall.php';
		
			$uninstaller = new Uninstaller($this->parent);
			return $uninstaller->uninstall(6);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function uninstall_7(){
		if($this->accessAdminPage(2)){
			require_once dirname(__FILE__) . '/resources/uninstall.php';
		
			$uninstaller = new Uninstaller($this->parent);
			return $uninstaller->uninstall(7);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function uninstall_8(){
		if($this->accessAdminPage(2)){
			require_once dirname(__FILE__) . '/resources/uninstall.php';
		
			$uninstaller = new Uninstaller($this->parent);
			return $uninstaller->uninstall(8);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	public function uninstall_9(){
		if($this->accessAdminPage(2)){
			require_once dirname(__FILE__) . '/resources/uninstall.php';
		
			$uninstaller = new Uninstaller($this->parent);
			return $uninstaller->uninstall(9);
			
		}else{
			$this->parent->parent->addHeader('Location', '/admin/modules/');
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
	}
	
	public function backup(){
		if(!$this->accessAdminPage(3)){
			return new ActionResult($this, '/admin/modules/', 1, 'You are not allowed to do that', B_T_FAIL);
		}
		$backups	= (WebApp::post('backups')===NULL)?	array()	:strgetcsv(WebApp::post('backups'));
		if(count($backups)==0){
			$backups = (WebApp::get('m')===NULL)?	array()	:array(WebApp::get('m'));
		}
		if(count($backups)==0){
			return new ActionResult($this, '/admin/modules/backup', 0, 'No module(s) were selected!', B_T_FAIL);
		}
		foreach($backups as $backup){
			$validated = GUMP::is_valid(array('bk'=>$backup), array('bk'=>'integer'));
			if($validated!==true){
				return new ActionResult($this, '/admin/modules/backup', 0, 'No module(s) were selected!', B_T_FAIL); 
			}
		}
		
		$location = __BACKUP__.DIRECTORY_SEPARATOR.date(DATET_BKUP).DIRECTORY_SEPARATOR;
		require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'backup.php');
		
		$result = array();
		foreach($backups as $module){
			$backup = new Backup($this->parent);
			if(!$backup->setLocation($location)){
				return new CronResult(
					$this,
					false,
					'Failed to create backup dir: '.DIRECTORY_SEPARATOR.'backup'.str_replace(__BACKUP__, '', $location.$module)
				);
			}
			
			if(!$backup->setID($module)){
				return new CronResult(
					$this,
					false,
					'Failed to setID for '.$module
				);
			}
			$results[$module] = $backup->backup();
			unset($backup);
		}
		
		$msg = '';
		$status = true;
		foreach($results as $ns=>$data){
			$msg.= '"'.$ns.'": '.$data['msg'].PHP_EOL;
			if(!$data['s']) $status = false;
		}
		
		if($status){
			$msg = 'Backup was completed for selected module(s)!';
			$type = B_T_SUCCESS;
		}else{
			$msg = 'Backup was completed but failed for some/all module(s). Details as follows:'.PHP_EOL.$msg;
			$type = B_T_WARNING;
		}
		
		
		$this->parent->parent->logEvent($this::name_space, 'Back up modules: '.csvgetstr($backups));
		return new ActionResult(
			$this,
			'/admin/modules/backup',
			1,
			$msg,
			$type
		);
	}
}
?>