<?php

/**
 * Backup Creation Class
 *
 * @category   WebApp.Base.Backup
 * @package    backup.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
*
*/

class Backup extends Base
{
	const name_space = 'Module.Modules';
	const version = '1.0.0';
	
	private $ns				= '';
	private $id				= '';
	private $location			= __BACKUP__;
	private $location_prefix	= true;
	private $module;
	
	function __construct($parent){
		$this->__init($parent);
		$this->_checkBackupDir();
	}
	
	public function setNamespace($ns){
		$this->ns = $ID;
		return $this->_checkModule();
	}
	public function setID($ID){
		$this->id = $ID;
		return $this->_checkModule();
	}
	
	public function setLocation($location, $create=true, $prefix=true){
		$this->location_prefix = $prefix;
		$this->location = $location;
		if($create){
			return $this->_checkBackupDir();
		}
	}
	public function backup($sql=true){
		if(!$this->module->getBackupTables()){
			return array('s'=>false, 'm'=>'Failed to fetch tables to back up!');
		}
		if($this->location_prefix){
			$this->location.= 'mod_'.strtolower($this->module->namespace);
			if(!file_exists($this->location)){
				mkdir($this->location, 0775, true);
			}
		}
		mkdir($this->location.DIRECTORY_SEPARATOR.'sql');
		foreach($this->module->backup_tables as $table=>$options){
			$t = array();
			
			$structure = '';
			if($options['s']){
				$structure_query = $this->mySQL_r->query('SHOW CREATE TABLE `'.$this->mySQL_r->real_escape_string($table).'`');
				if(!$structure_query){
					return array('s'=>false, 'msg'=>'Failed to backup tables');
				}
				$structure = $structure_query->fetch_row();
				$structure = 'DROP TABLE IF EXISTS `'.$this->mySQL_r->real_escape_string($table).'`'.PHP_EOL.$structure[1];
			}
			$data = array();
			if($options['d']){
				$data_query = $this->mySQL_r->query('SELECT * FROM `'.$this->mySQL_r->real_escape_string($table).'`');
				while($d = $data_query->fetch_assoc()){
					$d = $this->_replaceNULL($d);
					$d = $this->_toValueString($d);
					$data[] = $this->mySQL_r->real_escape_string($d);
				}
			}
			$insert = '';
			if(count($data)!=0){
				$insert = 'INSERT INTO `'.$this->mySQL_r->real_escape_string($table).'` VALUES'.PHP_EOL.'  ';
				$insert.= implode(','.PHP_EOL.'  ', $data);
			}
			
			$query = $structure.PHP_EOL.$insert;
			
			$file = fopen($this->location.DIRECTORY_SEPARATOR.'sql'.DIRECTORY_SEPARATOR.$table.'.sql', 'w');
			fwrite($file, $query);
			fclose($file);
		}
		rcopy(__MODULE__.DIRECTORY_SEPARATOR.strtolower($this->module->namespace), $this->location.DIRECTORY_SEPARATOR.'payload');
		rename($this->location.DIRECTORY_SEPARATOR.'payload'.DIRECTORY_SEPARATOR.'module.xml', $this->location.DIRECTORY_SEPARATOR.'module.xml');
		if(file_exists($this->location.DIRECTORY_SEPARATOR.'payload'.DIRECTORY_SEPARATOR.'install.php')){
			rename($this->location.DIRECTORY_SEPARATOR.'payload'.DIRECTORY_SEPARATOR.'install.php', $this->location.DIRECTORY_SEPARATOR.'install.php');
		}
		$this->_zipModule();
		rrmdir($this->location);
		return array('s'=>true, 'msg'=>'Succesfully backed up modules');
	}
	
	private function _checkBackupDir(){
		if(!file_exists($this->location)){
			return mkdir($this->location, 0775, true);
		}
		if(!is_dir($this->location)){
			return mkdir($this->location.'_'.ranString(4,2), 0775, true);
		}
		return true;
	}
	
	private function _checkModule(){
		if($this->ns === '' && $this->id === ''){
			return false;
		}
		
		$checker = $this->parent->getResource('Modules');
		if(!$checker){
			return false;
		}
		if($this->ns !== ''){
			$checker->getModuleFromNS($this->ns);
		}
		if($this->id !== ''){
			$checker->getModuleFromID($this->id);
		}
		$this->module = $checker;
		return $checker->is_installed();
	}
	
	private function _replaceNULL($data){
		foreach($data as $k=>$v){
			if($v === NULL){
				$data[$k] = 'NULL';
			}
		}
		return $data;
	}
	
	private function _toValueString($data){
		return implodewrap(',', $data, '(', ')');
	}
	
	private function _zipModule(){
		$zip = new ZipArchive();
		$zip->open($this->location.'.zip',ZipArchive::CREATE);
		
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($this->location),
			RecursiveIteratorIterator::LEAVES_ONLY
		);
		foreach($files as $name=>$file){
			if(is_dir($file->getRealPath())){
				$zip->addEmptyDir(str_replace($this->location, '', $file->getRealPath()));
			}else{
				$zip->addFile($file->getRealPath(), str_replace($this->location, '', $file->getRealPath()));
			}
		}
		$zip->close();
	}

}
?>