<?php

include_once __DIR__ . "/classes/storage/FileCache.class.php";

function cache_set($key, $string, $timeout = 86400) {

	$key = preg_replace("/[^a-zA-Z0-9]/", "", $key);
	
	$data['timestamp'] = time();
	$data['timeout'] = $timeout;
	$data['data'] = serialize($string);
	
	$filename = "../filecache/" . $key;

	if (!is_writable($filename)) {
		return false;
	}

	$fh = fopen($filename, 'w');

	$serializedData = serialize($data);
	fwrite($fh, $serializedData);
	
	fclose($fh);

	
	return true;
	
}

function cache_get($key) {
		
	$key = preg_replace("/[^a-zA-Z0-9]/", "", $key);
	
	@$fh = fopen("../filecache/" . $key, 'r');
	if (!$fh) {
		return false;
	}

	@$theData = fread($fh, filesize("../filecache/" . $key));
	
	fclose($fh);
	
	if (!$theData) {
		return false;
	}
	
	$theData = unserialize($theData);

	if ($_GET['nocache'] || (($theData['timestamp'] + $theData['timeout']) < time()))
	{
		cache_delete($key);
		return false;
	}
	else 
	{
		return unserialize($theData['data']);
	}
}

function cache_delete($key) {
	$key = preg_replace("/[^a-zA-Z0-9]/", "", $key);
	@unlink("../filecache/" . $key);
}

?>