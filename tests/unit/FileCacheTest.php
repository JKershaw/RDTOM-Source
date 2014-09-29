<?php

include_once __DIR__ . "/../../app/library/classes/storage/FileCache.class.php";

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
		$expectedValue2 = array(
			1,
			2,
			3,
			4
		);
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
	
	public function testSaveAndRetreiveWithInvalidKey() {
		
		$key = "This key doesn't exist";
		$returnedValue = $this->fileCache->get($key);
		
		$this->assertEquals(false, $returnedValue);
	}
	
	public function testSaveAndRetreiveAndDeleteValue() {
		
		$expectedValue = "This is the value";
		$key = "woahKey";
		
		$this->fileCache->set($key, $expectedValue);
		$returnedValue = $this->fileCache->get($key);
		
		$this->assertEquals($returnedValue, $expectedValue);
		
		$this->fileCache->forget($key);
		$returnedValue = $this->fileCache->get($key);
		
		$this->assertEquals($returnedValue, false);
	}
	
	public function testForgetNonexistantKey() {
		
		$key = "anotherKey, yo";
		
		$this->fileCache->forget($key);
		$returnedValue = $this->fileCache->get($key);
		
		$this->assertEquals($returnedValue, false);
	}
	
	public function testExpiredData() {
	
		$key = "this is the key";
		$value = "here is some data";
		$lifeSpan = 20;
	
		$age1FileCache = new FileCache(1);
		$age1FileCache->set($key, $value, $lifeSpan);
		$returnedValue = $age1FileCache->get($key);
		$this->assertEquals($returnedValue, $value);
	
		$age15FileCache = new FileCache(15);
		$returnedValue = $age15FileCache->get($key);
		$this->assertEquals($returnedValue, $value);
	
		$age21FileCache = new FileCache(21);
		$returnedValue = $age21FileCache->get($key);
		$this->assertEquals($returnedValue, $value);
	
		$age22FileCache = new FileCache(22);
		$returnedValue = $age22FileCache->get($key);
		$this->assertEquals($returnedValue, false);
	}
	
	public function testExpiredDataNoTTLGiven() {
	
		$key = "this is the key";
		$value = "here is some data";
	
		$age1FileCache = new FileCache(1);
		$age1FileCache->set($key, $value);
		$returnedValue = $age1FileCache->get($key);
		$this->assertEquals($returnedValue, $value);
	
		$age100000FileCache = new FileCache(100000);
		$returnedValue = $age100000FileCache->get($key);
		$this->assertEquals($returnedValue, false);
	
	}
}