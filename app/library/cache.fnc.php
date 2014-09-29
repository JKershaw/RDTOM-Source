<?php

include_once __DIR__ . "/classes/storage/FileCache.class.php";

function cache_set($key, $value, $ttl = 86400) {
	$fileCache = new FileCache(time());
	$fileCache->set($key, $value, $ttl);
}

function cache_get($key) {
	$fileCache = new FileCache(time());
	$fileCache->get($key);
}

function cache_delete($key) {
	$fileCache = new FileCache(time());
	$fileCache->forget($key);
}

?>