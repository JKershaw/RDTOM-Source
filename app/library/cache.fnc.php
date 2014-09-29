<?php

include_once("FileCache");

// function cache_set($key, $value, $ttl = 86400) {
// 	$fileCache = new FileCache();
// 	$fileCache->set($key, $value, $ttl);
// }

function cache_get($key) {
	$fileCache = new FileCache();
	return $fileCache->get($key);
}

function cache_delete($key) {
	$fileCache = new FileCache();
	$fileCache->forget($key);
}

?>