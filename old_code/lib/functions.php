<?php

/**
 * Functions
 *
 * Contains useful functions
 *
 * @category   Functions
 * @package    functions.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Biggleswade Swimming Club 
 */

/**
 */

function is_CLI()
{
	return (php_sapi_name() === 'cli');
}

function implodewrap($glue, $pieces, $before = '', $after = '', $roundElement = false){
	if($before	=== '') $before	= $glue;
	if($after	=== '') $after	= $before;
	if($roundElement){
		return implode($before.$glue.$after, $pieces);
	}
	return $before.implode($glue, $pieces).$after;
}

function urlimplode($pieces, $filter = true){
	if($filter){
		$pieces = array_filter($pieces);
	}
	return implodewrap('/', $pieces);
}
/**
 * strgetcsv()
 * 
 * @param mixed $string
 * @param string $delim
 * @param bool $filter
 * @return
 */
function strgetcsv($string, $delim = ",", $filter = true)
{
	$csv = array();
	if ($string != "") {
		$csvprelim = str_getcsv($string, $delim);
		if ($filter) {
			foreach($csvprelim as $k => $v) {
				if ($v != "") {
					$csv[$k] = $v;
				}
			}
		}
		$csv = array_values($csv);
	} else {
		$csv = array();
	}
	return $csv;
}
/**
 * csvgetstr()
 * 
 * @param mixed $array
 * @param string $delim
 * @return
 */
function csvgetstr($array, $delim = ",")
{
	$string = '';
	if (count($array) != 0) {
		foreach($array as $k => $v) {
			$string .= $v . ',';
		}
		$string = rtrim($string, ',');
	}
	return $string;
}

/**
 * array_clean()
 * 
 * @param mixed $haystack
 * @return
 */
function array_clean(array $haystack)
{
	foreach($haystack as $key => $value) {
		if (is_array($value)) {
			$haystack[$key] = array_clean($value);
		} elseif (is_string($value)) {
			$value = trim($value);
		}
		if (!$value) {
			unset($haystack[$key]);
		} elseif (strlen($value) == 0) {
			unset($haystack[$key]);
		}
	}
	return $haystack;
}

function array_trim(array $array){
	foreach($array as $key=>$value){
		if(is_array($value)){
			$array[$key] = array_trim($value);
		}elseif(is_object($value)){
			$array[$key] = $value;
		}else{
			$array[$key] = trim($value);
		}
	}
	return $array;
}
/**
 * array_mode()
 * 
 * @param mixed $set
 * @return
 */
function array_mode(array $set)
{
	$counts = array_count_values($set);
	arsort($counts);
	$modes = array_keys($counts, current($counts), true);

	if (count($set) === count($counts)) {
		return false;
	}

	if (count($modes) === 1) {
		return $modes[0];
	}

	return $modes;
}

/**
 * ranString()
 * 
 * @param mixed $length
 * @param integer $strength
 * @return
 */
function ranString($length, $strength = 4)
{
	$listA = 'aeiouy';
	$listB = 'bcdfghjklmnpqrstvwxz';
	if ($strength >= 1) {
		$listA .= 'BCDFGHJKLMPQRSTVWXZ';
	}
	if ($strength >= 2) {
		$listB .= "AEIOUY";
	}
	if ($strength >= 4) {
		$listA .= '0123456789';
	}
	if ($strength >= 8) {
		$listB .= '!£$%^&*()_-+=';
	}

	$string = '';
	$alt = time() % 2;
	for ($i = 0; $i < $length; $i++) {
		if ($alt == 1) {
			$string .= $listA[(rand() % strlen($listA))];
			$alt = 0;
		} else {
			$string .= $listB[(rand() % strlen($listB))];
			$alt = 1;
		}
	}
	return $string;
}

/**
 * rrmdir()
 * 
 * @param mixed $dirPath
 * @return
 */
function rrmdir($dirPath)
{
	foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath,
		FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
		$path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
	}
	return rmdir($dirPath);
}

/**
 * rcopy()
 * 
 * @param mixed $src
 * @param mixed $dst
 * @return
 */
function rcopy($src, $dst)
{
	$dir = opendir($src);
	@mkdir($dst);
	while (false !== ($file = readdir($dir))) {
		if (($file != '.') && ($file != '..')) {
			if (is_dir($src . '/' . $file)) {
				rcopy($src . '/' . $file, $dst . '/' . $file);
			} else {
				copy($src . '/' . $file, $dst . '/' . $file);
			}
		}
	}
	closedir($dir);
	return true;
}

/**
 * not()
 * 
 * @param mixed $boolean
 * @return
 */
function not($boolean)
{
	return !$boolean;
}

/**
 * comment()
 * 
 * @param mixed $text
 * @param string $type
 * @param bool $print
 * @return
 */
function comment($text, $type = 'html', $print = true)
{
	switch($type) {
		case 'html':
			$comment = '<!-- ' . $text . ' -->' . PHP_EOL;
			break;
		case 'js':
		case 'css':
		case 'php':
			$comment = '/* ' . $text . ' */' . PHP_EOL;
			break;
	}
	if ($print) {
		print $comment;
	} else {
		return $comment;
	}
}

/**
 * getSQLDate()
 * 
 * @param mixed $date
 * @return
 */
function getSQLDate($date)
{
	return date(DATET_SQL, strtotime($date));
}

/**
 * removeSpecialChars()
 * 
 * @param mixed $str
 * @return
 */
function removeSpecialChars($str)
{
	$str = strtolower($str);
	$str = preg_replace('/[^a-z0-9 -]+/', '', $str);
	$str = str_replace(' ', '-', $str);
	$str = preg_replace('/-+/', '-', $str);
	return trim($str, '-');
}

// http_response_code() drop-in for <=5.4.0
    if (!function_exists('http_response_code')) {
        function http_response_code($code = NULL) {

            if ($code !== NULL) {

                switch ($code) {
                    case 100: $text = 'Continue'; break;
                    case 101: $text = 'Switching Protocols'; break;
                    case 200: $text = 'OK'; break;
                    case 201: $text = 'Created'; break;
                    case 202: $text = 'Accepted'; break;
                    case 203: $text = 'Non-Authoritative Information'; break;
                    case 204: $text = 'No Content'; break;
                    case 205: $text = 'Reset Content'; break;
                    case 206: $text = 'Partial Content'; break;
                    case 300: $text = 'Multiple Choices'; break;
                    case 301: $text = 'Moved Permanently'; break;
                    case 302: $text = 'Moved Temporarily'; break;
                    case 303: $text = 'See Other'; break;
                    case 304: $text = 'Not Modified'; break;
                    case 305: $text = 'Use Proxy'; break;
                    case 400: $text = 'Bad Request'; break;
                    case 401: $text = 'Unauthorized'; break;
                    case 402: $text = 'Payment Required'; break;
                    case 403: $text = 'Forbidden'; break;
                    case 404: $text = 'Not Found'; break;
                    case 405: $text = 'Method Not Allowed'; break;
                    case 406: $text = 'Not Acceptable'; break;
                    case 407: $text = 'Proxy Authentication Required'; break;
                    case 408: $text = 'Request Time-out'; break;
                    case 409: $text = 'Conflict'; break;
                    case 410: $text = 'Gone'; break;
                    case 411: $text = 'Length Required'; break;
                    case 412: $text = 'Precondition Failed'; break;
                    case 413: $text = 'Request Entity Too Large'; break;
                    case 414: $text = 'Request-URI Too Large'; break;
                    case 415: $text = 'Unsupported Media Type'; break;
                    case 500: $text = 'Internal Server Error'; break;
                    case 501: $text = 'Not Implemented'; break;
                    case 502: $text = 'Bad Gateway'; break;
                    case 503: $text = 'Service Unavailable'; break;
                    case 504: $text = 'Gateway Time-out'; break;
                    case 505: $text = 'HTTP Version not supported'; break;
                    default:
                        exit('Unknown http status code "' . htmlentities($code) . '"');
                    break;
                }

                $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

                header($protocol . ' ' . $code . ' ' . $text);

                $GLOBALS['http_response_code'] = $code;

            } else {

                $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

            }

            return $code;

        }
    }

?>