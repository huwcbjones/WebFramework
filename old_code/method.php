<?php
/**
 * {TITLE}
 *
 * {DESCRIPTION}
 *
 * @site		
 * @package		Default.php
 * @author		
 * @copyright	
 */
 
/** INCLUDES
 */

function glob_recursive($pattern, $flags = 0){
	$files = glob($pattern, $flags);
	foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir){
		$files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
	}
	return $files;
}

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
</head>

<body>
<pre>
<?php
$files = glob_recursive('lib/*');
foreach($files as $file){
	$lineNo = 1;
	if(substr($file, -4)=='.php'){
		print $file.PHP_EOL;
		$file = fopen($file, 'r');
		while(($line = fgets($file)) !== false){
			if(preg_match('/function[\s\n]+(\S+)[\s\n]*\(/', $line) && strpos($line, 'print')===false){
				$line = trim($line);
				$line = str_replace(' {', '', $line);
				$line = str_replace('{', '', $line);
				$line = str_replace('}', '', $line);
				print '  - '.$line.' ('.$lineNo.')'.PHP_EOL;
			}
			$lineNo++;
		}
	}
}
?>
</pre>
</body>
</html>