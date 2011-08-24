<?php

require 'utils.php';

function file_cmp($a, $b)
{
	$a = explode('.', $a);
	$b = explode('.', $b);

	while (count($a) && count($b)) {
		$ea = array_pop($a);
		$eb = array_pop($b);
		if ($ea < $eb) return -1;
		if ($ea > $eb) return 1;
	}

	if (count($a)) return -1;
	if (count($b)) return 1;
	return 0;
}

$files = array();

foreach (scandir($upload_directory) as $file) {
    if (preg_match( '/(^\.|\.php$)/', $file)) {
        continue;
    }

    $files[] = $file;
}

usort($files, 'file_cmp');

?><!DOCTYPE html>
<html>
<head>
<title>File upload</title>
<style>
html {
	background-color: #F9F9F9;
	color: #000;
	font-family: sans-serif;
}

body {
	max-width: 35em;
	margin: 2em auto;
}

#upwrap, #ftwrap {
	background-color: #FFF;
	margin: 1em 0;
}

#upwrap {
	border: 1px solid #CCC;
	border-radius: .4em;
	padding: .5em 1em;
}

#ftwrap {
	padding: 0 0;
}

#ftable { border-collapse: collapse; width: 100%; font-size: 0.9em; }
#ftable td, th { border: 1px solid #CCC; padding: 0.3em 0.5em; }
#ftable th { background-color: #DFA; text-align: center; }
#ftable td a { display: block; color: #099; }
#ftable td a:hover { color: #022; }
#ftable tr:hover { background-color: #EEE; }
#ftable td.file-size { text-align: right; }
</style>
</head>
<body>
<div id="upwrap">
<form enctype="multipart/form-data" action="upload.php" method="POST">
<p><input name="upload" type="file" /></p>
<p><input type="submit" value="Send File" /></p>
</form>
</div>
<div id="ftwrap">
<table id="ftable">
<tr><th>File</th><th>Size</th><th>Date</th></tr>
<?php


foreach ($files as $file)
{
	$file_path = $upload_directory . $file;
	$file = htmlentities($file, ENT_QUOTES);

	if (is_array($stat = @stat($file_path))) {
		$size = file_size($stat[7]);
		$modified = gmdate('F jS, Y', $stat[9]);
	}
	else {
		$size = $modified = 'unknown';
	}

	print '<tr><td><a href="' . $upload_link . $file . '">';
	print $file;
	print '</a></td><td class="file-size">';
	print $size;
	print '</td><td class="file-date">';
	print $modified;
	print "</td></tr>\n";
}

?>
</table>
</div>
</body>
</html>
