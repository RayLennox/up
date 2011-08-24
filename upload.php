<?php

require 'utils.php';
ob_start();

if (!isset($_FILES['upload'])) {
	header('Status: 403 Forbidden');
	print 'No.';
	exit;
}

// TODO: Keep an index of hashes

if ($_FILES['upload']['error'] != UPLOAD_ERR_OK) {
	switch ($_FILES['upload']['error']) {
	case UPLOAD_ERR_INI_SIZE:
		ob_die('Upload error: the uploaded file exceeds the upload_max_filesize directive in php.ini.');

	case UPLOAD_ERR_FORM_SIZE:
		ob_die('Upload error: the uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.');

	case UPLOAD_ERR_PARTIAL:
		ob_die('Upload error: the uploaded file was only partially uploaded.');

	case UPLOAD_ERR_NO_FILE:
		ob_die('Upload error: no file was uploaded.');

	case UPLOAD_ERR_NO_TMP_DIR:
		ob_die('Upload error: missing a temporary folder.');

	case UPLOAD_ERR_CANT_WRITE:
		ob_die('Upload error: Failed to write file to disk.');

	default:
		ob_die('Upload error: error code ' . $_FILES['upload']['error']);
	}
}

$store = sha1_file($_FILES['upload']['tmp_name']);

if (!$store) {
	ob_die('Hashing failed?');
}

$file = $store;
$ext = 'xxx';

// We need proper extension support and white/blacklisting.
if (preg_match('#([^\x5C\x2F]*)\.([a-zA-Z0-9]{1,15})$#', $_FILES['upload']['name'], $x)) {
	$ext = strtolower($x[2]);
	$file = strtolower(substr($x[1], 0, 24));

	if (file_exists($file . '.' . $ext)) {
		$file .= '-' . substr($store, 0, 4);
	}
}

$store = $file . '.' . $ext;

if (in_array($ext, array('php', 'cgi', 'js', '.pl'))) {
	//ob_die('No, thank you!');
	$store .= '.txt';
}

$store_path = $upload_directory . $store;

if (!$store) {
	ob_die('You did something naughty.');
}
else if (file_exists($store_path)) {
	ob_die('Oops, that file already exists.');
}

if (!move_uploaded_file($_FILES[ 'upload' ][ 'tmp_name' ], $store_path)) {
    header( 'Status: 500 Internal Server Error' );
    ob_die('Something went horribly wrong.');
}

chmod($store_path, 0644);
print htmlentities('Stored "' . $_FILES['upload']['name'] . '" as ' . $store) . "\n";
print '<script type="text/javascript">setTimeout(function() { location.href = "./"; }, 1000)</script>';

