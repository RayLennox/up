<?php

$upload_link = '/res/';
$upload_directory = '/srv/www/nx3d.org' . $upload_link;


$ips = array(
	gethostbyname('vixen.nx3d.org'),
	'77.169.158.169',
);

if (!in_array($_SERVER['REMOTE_ADDR'], $ips)
 && substr($_SERVER['REMOTE_ADDR'], 0, 3) != '10.') {
	$auth_fail = true;

	if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
		if ($_SERVER['PHP_AUTH_PW'] == 'thisisnotit') {
			$auth_fail = false;
		}
	}

	if ($auth_fail) {
		header('HTTP/1.1 401 Unauthorized');
		header('WWW-Authenticate: Basic realm="File uploads"');
		print $_SERVER['REMOTE_ADDR'];
		exit;
	}
}

ini_set('display_errors', 'on');
error_reporting(E_ALL|E_NOTICE|E_STRICT);


function file_size($size)
{
	static $sizes = array(
		array(1000000,  1048576,    'M'),
		array(1000,     1024,       'k'),
		array(0,        1,          ''),
	);

	if ($size < 0) {
		return '2G+';
	}

	foreach ($sizes as $sz) {
		if ($size > $sz[0]) {
			return sprintf('%.1f %sB', $size/$sz[1], $sz[2]);
		}
	}

	return '?';
}


function ob_die($message='')
{
	header('Content-Type: text/plain');
	print $message;

	if (ob_get_level()) {
		ob_end_flush();
	}

	exit;
}

