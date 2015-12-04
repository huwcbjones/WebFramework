<?php

/**
 * File Server
 *
 * Serves static files
 *
 * @category   WebApp.File
 * @package    file.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones	
 */

/** INCLUDES
 */

class File extends BaseCtrl
{
	const name_space = 'WebApp.File';
	const version = '1.0.0';

	private $filename = '';

	public function setFile()
	{
		include __LIBDIR__ . '/fileMIMEs.php';
		$this->MIMEs = $MIME_type;
		if (
			WebApp::get('cat1') == 'css'
			|| WebApp::get('cat1') == 'js'
			|| WebApp::get('cat1') == 'images'
		) {
			$filename = strtolower(WebApp::get('cat2')) . '/' . WebApp::get('cat1') . '/' .
				WebApp::get('cat3');
			$i = 4;
			while(WebApp::get('cat'.$i)!==NULL){
				$filename .= '/' . WebApp::get('cat'.$i);
				$i++;
			}
			$this->parent->addHeader('file', $filename);
			$file = __MODULE__ . '/' . $filename;

		} elseif (WebApp::get('cat1') == 'fonts') {
			$file = __EXECDIR__ . '/' . Server::get('REQUEST_URI');
		}
		if (file_exists($file)) {
			$this->file = $file;
		} else {
			$this->file = false;
		}
	}
	
	public function execute(){
		if ($this->file !== false) {
			if (substr($this->file, 0, 1) == '%') {
			} else {
				$mimetype = $this->getMIME($this->file);
				header('Content-type: ' . $mimetype);
				$this->parent->content = file_get_contents($this->file);
			}
		} else {
			$this->setStatus(404);
		}
	}
	
	public function getMIME($filename)
	{
		$ext = explode('?', $filename);
		$ext = $ext[0];
		$ext = strtolower(pathinfo($ext, PATHINFO_EXTENSION));
		if (array_key_exists($ext, $this->MIMEs)) {
			return $this->MIMEs[$ext];
		} else {
			$info = finfo_open(FILEINFO_MIME_TYPE);
			$mimetype = finfo_file($info, $filename);
			finfo_close($info);
			return $mimetype;
		}
	}

}

?>