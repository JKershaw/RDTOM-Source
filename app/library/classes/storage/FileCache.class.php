<?php
class FileCache
{
	
	private $cacheFolder;
	private $now;
	
	function __construct($currentTime = false) {
		if (!$currentTime) {
			$currentTime = time();
		}
		$this->now = $currentTime;
		$this->cacheFolder = __DIR__ . "/filecache/";
		
		if (!is_dir($this->cacheFolder)) {
			mkdir($this->cacheFolder);
		}
	}
	
	public function set($key, $value, $ttl = 86400) {
		
		$filename = $this->generateFileName($key);
		
		$data['timestamp'] = $this->now;
		$data['ttl'] = $ttl;
		$data['data'] = serialize($value);
		
		$fh = fopen($filename, 'w');
		
		$serializedData = serialize($data);
		fwrite($fh, $serializedData);
		
		fclose($fh);
	}
	
	public function get($key) {
		
		$filename = $this->generateFileName($key);
		
		try {
			
			$fh = fopen($filename, 'r');
			$theData = fread($fh, filesize($filename));
			fclose($fh);
			
			$theData = unserialize($theData);
			
			$value = unserialize($theData['data']);
			
			if (($theData['timestamp'] + $theData['ttl']) < $this->now) {
				$this->forget($key);
				$value = false;
			}
		}
		catch(exception $e) {
			$value = false;
		}
		
		return $value;
	}
	
	public function forget($key) {
		$filename = $this->generateFileName($key);
		try {
			unlink($filename);
		}
		catch(exception $e) {
		}
	}
	
	private function generateFileName($key) {
		$key = preg_replace("/[^a-zA-Z0-9]/", "", $key);
		$filename = $this->cacheFolder . $key . ".cache";
		return $filename;
	}
}