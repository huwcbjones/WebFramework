<?php

/**
 * Image Constructer
 *
 * @category   WebApp.Image
 * @package    image.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones 
 */

/*
 *
 */

class Image extends File
{
	const		name_space		= 'WebApp.Image';
	const		version			= '1.0.0';
	
	public function setImage(){
		$this->setFile();
	}
	public function getMIME($filename){
		$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		if(array_key_exists($ext, $this->MIMEs)){
			return $this->MIMEs[$ext];
		}else{
			$info = finfo_open(FILEINFO_MIME_TYPE);
			$mimetype = finfo_file($info, $filename);
			finfo_close($info);
			return $mimetype;
		}
	}	
}
?>