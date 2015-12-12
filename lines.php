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

function glob_recursive($pattern, $flags = 0)
{
	$files = glob($pattern, $flags);
	foreach(glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
		$files = array_merge($files, glob_recursive($dir . '/' . basename($pattern), $flags));
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

$totalLines = 0;
$totalFiles = 0;
$files = glob_recursive('*');

foreach($files as $file) {
	if (!is_dir($file) && substr($file, -4) == '.php') {
		$handle = fopen($file, 'r');
		$lineCount = 0;
		while (!feof($handle)) {
			$line = fgets($handle);
			$lineCount++;
		}
		echo "File: $file\n";
		echo "	Lines:	$lineCount\n";
		$totalFiles++;
		$totalLines += $lineCount;
	}
}
echo "Total Files:	$totalFiles\n";
echo "Total Lines:	$totalLines\n";

?>
</pre>
</body>
</html>