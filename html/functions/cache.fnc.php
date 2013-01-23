<?php
function cache_set($key, $string, $timeout = 7200)
{
	
	// generate the data to save
	$key = preg_replace("/[^a-zA-Z0-9]/", "", $key);
	
	$data['timestamp'] = time();
	$data['timeout'] = $timeout;
	$data['data'] = serialize($string);
	
	save_log("cache", "ADD " . $key . ": Timestamp = " . $data['timestamp'] . ", Timeout = " . $data['timeout'] . ", Current timestamp = " . time());
	
	$fh = fopen("filecache/" . $key, 'w') or die("can't open file: " . "/filecache/" . $key);

	// save it
	$stringData = serialize($data);
	fwrite($fh, $stringData);
	
	fclose($fh);
	
	return true;
	
}

function cache_get($key)
{
		
	$key = preg_replace("/[^a-zA-Z0-9]/", "", $key);
	
	@$fh = fopen("filecache/" . $key, 'r');
	if (!$fh)
	{
		return false;
	}
	$theData = fread($fh, filesize("filecache/" . $key));
	fclose($fh);
	
	$theData = unserialize($theData);
	
	save_log("cache", "GET " . $key . ": Timestamp = " . $theData['timestamp'] . ", Timeout = " . $theData['timeout'] . ", Current timestamp = " . time());
		
	if ($_GET['nocache'] || (($theData['timestamp'] + $theData['timeout']) < time()))
	{
		save_log("cache", "GET " . $key . " Out of date");
		
		cache_delete($key);
		return false;
	}
	else 
	{
		save_log("cache", "GET " . $key . " Valid");
		return unserialize($theData['data']);
	}
}

function cache_delete($key)
{
	$key = preg_replace("/[^a-zA-Z0-9]/", "", $key);
	unlink("filecache/" . $key);

}

?>