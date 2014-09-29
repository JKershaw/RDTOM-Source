<?php
class FileCacheTest extends \PHPUnit_Framework_TestCase
{
	
	protected function setUp() {
		$this->fileCache = new FileCache(time());
	}
	
	public function testSaveAndRetreiveValue() {
		
		$expectedValue = "Testing a value";
		$key = "testKey";
		
		$this->fileCache->set($key, $expectedValue);
		$returnedValue = $this->fileCache->get($key);
		
		$this->assertEquals($returnedValue, $expectedValue);
	}
	
	public function testSaveAndRetreiveMultipleValues() {
	
		$expectedValue1 = "Testing a value1";
		$key1 = "testKey1";
		$expectedValue2 = "Testing a value2";
		$key2 = "testKey2";
	
		$this->fileCache->set($key1, $expectedValue1);
		$this->fileCache->set($key2, $expectedValue2);
		$returnedValue1 = $this->fileCache->get($key1);
		$returnedValue2 = $this->fileCache->get($key2);
	
		$this->assertEquals($returnedValue1, $expectedValue1);
		$this->assertEquals($returnedValue2, $expectedValue2);
	}
	
	public function testSaveAndRetreiveWithOddKey() {
		
		$expectedValue = "Testing a value";
		$key = "test^&*(789fh8974&^%(+__)(()'Key";
		
		$this->fileCache->set($key, $expectedValue);
		$returnedValue = $this->fileCache->get($key);
		
		$this->assertEquals($returnedValue, $expectedValue);
	}
	

	// Test fetch no key exists
	// Fetch expired
	// Delete cache
	
}

class FileCache
{
	
	private $cacheFolder;
	private $now;
	
	function __construct($currentTime) {
		$this->now = $currentTime;
		$this->cacheFolder = __DIR__ . "/filecache/";
		
		// check if folder exists, create it if it doesn't
		if (!is_dir($this->cacheFolder)) {
			mkdir($this->cacheFolder);
		}
	}
	
	public function set($key, $value) {
		
		$filename = $this->generateFileName($key);

		$data['data'] = serialize($value);
		
		$fh = fopen($filename, 'w');
		
		$serializedData = serialize($data);
		fwrite($fh, $serializedData);
		
		fclose($fh);
	}
	
	public function get($key) {

		$filename = $this->generateFileName($key);
		
		@$fh = fopen($filename, 'r');
		
		@$theData = fread($fh, filesize($filename));
		
		fclose($fh);
		
		$theData = unserialize($theData);
		return unserialize($theData['data']);
	}

	private function generateFileName($key) {
		$key = preg_replace("/[^a-zA-Z0-9]/", "", $key);
		$filename = $this->cacheFolder . $key . ".cache";
		return $filename;
	}
}
