<?php
/**
 * Modules Resource Class
 *
 * @category   Module.Location.Resource
 * @package    modules/resource.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
 */

class ModulesResource extends BaseResource
{
	const	name_space	= 'Module.Modules';
	const	version		= '1.0.0';
	
	public	$ID				= '';
	public	$namespace		= '';
	public	$version		= '';
	public	$installed		= false;
	
		
	public	$installDate	= '';
	public	$description	= '';
	public	$copyright		= '';
	public	$backup			= false;
	public	$uninstall		= false;
	public	$backup_tables	= array();

	public function reset(){
		foreach(array('ID', 'namespace', 'installDate', 'name', 'version', 'description', 'copyright', 'backup', 'backup_tables', 'uninstall') as $var){
			unset($this->$var);
		}
		$this->installed = false;
	}

	function getModuleFromNS($ns){
		$this->parent->parent->debug($this::name_space.': Fetching module "'.$ns.'"');
		$mod_q = $this->parent->mySQL_r->prepare(
"SELECT
`namespace`, `install_date`, `name`, `version`, `description`, `author`, `authorUrl`, `copyright`, `backup`, `uninstall`
FROM `core_modules` WHERE `namespace`=?"
);
		if(!$mod_q){
			$this->parent->parent->debug($this::name_space.': MySQL Statement error!');
			return false;
		}
		$mod_q->bind_param('s',$ns);
		$mod_q->execute();
		$mod_q->store_result();
		if($mod_q->num_rows==1){
			$mod_q->bind_result(
				$module_id, $install, $name, $version, $desc, $author, $authorUrl, $copyright, $backup, $uninstall
			);
			while($loc_q->fetch()){
				$this->ID				= $module_id;
				$this->namespace		= $ns;
				$this->installDate		= date(DATET_SHORT, strtotime($install));
				$this->name				= $name;
				$this->version			= $version;
				$this->description		= $desc;
				$this->copyright		= $copyright;
				$this->backup			= ($backup==1) ? true: false;
				$this->uninstall		= ($uninstall==1) ? true: false;
			}
			$this->parent->parent->debug($this::name_space.': Fetched module "'.$name.'"!');
			$this->installed = true;
			return $this;
		}else{
			$this->installed = false;
			return false;
		}
	}

	function getModuleFromID($id){
		$this->parent->parent->debug($this::name_space.': Fetching module "'.$id.'"');
		$mod_q = $this->parent->mySQL_r->prepare(
"SELECT
`namespace`, `install_date`, `name`, `version`, `description`, `author`, `authorUrl`, `copyright`, `backup`, `uninstall`
FROM `core_modules` WHERE `module_id`=?"
);
		if(!$mod_q){
			$this->parent->parent->debug($this::name_space.': MySQL Statement error!');
			return false;
		}
		$mod_q->bind_param('i',$id);
		$mod_q->execute();
		$mod_q->store_result();
		if($mod_q->num_rows==1){
			$mod_q->bind_result(
				$namespace, $install, $name, $version, $desc, $author, $authorUrl, $copyright, $backup, $uninstall
			);
			while($mod_q->fetch()){
				$this->ID = $id;
				$this->namespace		= $namespace;
				$this->installDate		= date(DATET_SHORT, strtotime($install));
				$this->name				= $name;
				$this->version			= $version;
				$this->description		= $desc;
				$this->copyright		= $copyright;
				$this->backup			= ($backup==1) ? true: false;
				$this->uninstall		= ($uninstall==1) ? true: false;
			}
			$this->parent->parent->debug($this::name_space.': Fetched module "'.$name.'"!');
			$this->installed = true;
			return $this;
		}else{
			$this->installed = false;
			return false;
		}
	}
	public function getBackupTables(){
		if(!$this->installed){
			return false;
		}
		$backup_query = $this->mySQL_r->prepare("SELECT `table`, `structure`, `data` FROM `core_backup` WHERE `module_id`=?");
		if(!$backup_query){
			$this->parent->parent->debug($this::name_space.': Query error whilst retrieving backup tables!');
			return false;
		}
		$backup_query->bind_param('i', $this->ID);
		$backup_query->execute();
		$backup_query->bind_result($table, $structure, $data);
		$backup_query->store_result();
		while($backup_query->fetch()){
			$this->backup_tables[$table]['s'] = $structure;
			$this->backup_tables[$table]['d'] = $data;
		}
		return true;
	}
	public function is_installed(){
		return $this->installed;
	}
	public function isInstalled(){
		$this->parent->parent->debug($this::name_space.': isInstalled is deprecated, use is_installed instead', 1);
		return $this->is_installed();
	}
}
?>